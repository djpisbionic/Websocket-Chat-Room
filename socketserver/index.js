const app = require('express')();

const fs = require('fs');
var privateKey = fs.readFileSync('/home/darryl/conf/web/chatroom.darrylpolo.tech/ssl/chatroom.darrylpolo.tech.key', 'utf8');
var certificate = fs.readFileSync('/home/darryl/conf/web/chatroom.darrylpolo.tech/ssl/chatroom.darrylpolo.tech.crt', 'utf8');
var credentials = { key: privateKey, cert: certificate };
const https = require('https').createServer(credentials, app);
const async = require('async');
const io = require('socket.io')(https, {
    cors: {
        origin: "*",
        methods: ["GET", "POST"]
    }
});
var mysql = require('mysql');

var con = mysql.createConnection({
    host: "localhost",
    user: "chatdemo",
    password: "####",
    database: "chatdemo"
});
app.get('/', function (req, res) {
    res.send("Hello World!");
})
con.connect(function (err) {
    if (err) {
        console.log(err); return false;
    }
    con.timeout = 0;
    io.on("connection", (socket) => {
        var connectedUser;
        var chatRoom;
        socket.on("init join", async (userData, roomId, admin = false) => {
            try {
                await con.query(`SELECT * FROM chatroom_user WHERE chatroom='${roomId}'`, async function (err, room) {
                    if (admin) {

                        if (room.length > 0) {
                        } else {
                            await con.query(`SELECT * FROM chatroom_user WHERE chatroom='${roomId}' AND user='${userData.username}'`, async function (err, result) {
                                console.log(result);
                                if (result.length == 0) {
                                    await con.query(`INSERT INTO chatroom_user(type,user,chatroom)VALUES('mods','${userData.username}','${roomId}')`);
                                }
                            })
                        }
                    } else {
                        await con.query(`SELECT * FROM chatroom_user WHERE chatroom='${roomId}' AND user='${userData.username}'`, async function (err, result) {
                            if (result.length == 0) {
                                await con.query(`INSERT INTO chatroom_user(type,user,chatroom)VALUES('user','${userData.username}','${roomId}')`);
                            }
                        })

                    }
                })
                connectedUser = userData.username;
                chatRoom = roomId;
                await con.query(`UPDATE chatroom_user SET is_active=1 WHERE user='${connectedUser}' AND chatroom='${chatRoom}'`, function (err, active) {
                    console.log(active);
                })
                con.query(`SELECT * FROM chatroom_user WHERE chatroom='${roomId}' AND is_active=1`, async function (err, room) {

                    socket.join(roomId);


                    io.to(roomId).emit("user joined", room);
                    io.to(roomId).emit("join success", roomId);
                })
            } catch (e) {
                console.log(e);
            }
        })

        socket.on("chat message", (msgObj, roomId) => {
            io.to(roomId).emit("chat message received", msgObj);
        });

        socket.on("kick user", async (userId, roomId) => {
            //  await con.query(`DELETE FROM chatroom_user WHERE chatroom='${roomId}' AND user='${userId}'`, function (err, result) {
            //      if (result.affectedRows > 0) {
            io.to(roomId).emit("user kicked", userId);
            //      }
            //  });
            con.query(`SELECT * FROM chatroom_user WHERE chatroom='${roomId}'`, async function (err, room) {
                io.to(roomId).emit("user joined", room);
            })
        })
        socket.on("ban user", async (userId, roomId) => {
            await con.query(`UPDATE chatroom_user set is_banned=1 WHERE chatroom='${roomId}' AND user='${userId}'`, function (err, result) {
                if (result.affectedRows > 0) {
                    io.to(roomId).emit("user banned", userId);
                }
            });
            con.query(`SELECT * FROM chatroom_user WHERE chatroom='${roomId}'`, async function (err, room) {
                io.to(roomId).emit("user joined", room);
            })
        })
        socket.on("make moderator", async (userId, roomId) => {
            await con.query(`UPDATE chatroom_user set type='mods' WHERE chatroom='${roomId}' AND user='${userId}'`, function (err, result) {
                if (result.affectedRows > 0) {
                    io.to(roomId).emit("moderator made", userId);
                }
            });
            con.query(`SELECT * FROM chatroom_user WHERE chatroom='${roomId}'`, async function (err, room) {
                io.to(roomId).emit("user joined", room);
            })
        })
        socket.on("remove moderator", async (userId, roomId) => {
            await con.query(`UPDATE chatroom_user set type='user' WHERE chatroom='${roomId}' AND user='${userId}'`, function (err, result) {
                if (result.affectedRows > 0) {
                    io.to(roomId).emit("moderator removed", userId);
                }
            });
            con.query(`SELECT * FROM chatroom_user WHERE chatroom='${roomId}'`, async function (err, room) {
                io.to(roomId).emit("user joined", room);
            })
        })
        socket.on("start instant chat", async (roomId, to, from) => {
            if (to != from) {
                con.query(`SELECT * FROM instant_chats WHERE (user_to='${to}' AND user_from='${from}') OR (user_to='${from}' AND user_from='${to}')`, async function (err, check) {
                    console.log(err);
                    if (check.length > 0) {
                        console.log("check", check);
                        io.to(roomId).emit("instant chat started", roomId, to, from);
                        return;
                    }
                    await con.query(`INSERT INTO instant_chats(user_to,user_from)VALUES('${to}','${from}')`, function (error, chat) {
                        console.log(error);
                        if (chat) {
                            console.log(chat);
                            io.to(roomId).emit("instant chat started", roomId, to, from);
                            return;
                        }

                    })

                })
            }
        })
        socket.on("instant message", (msg, roomId, to, from) => {
            con.query(`INSERT INTO instant_messages(text,user_to,user_from)VALUES('${msg}','${to}','${from}')`, function (error, chat) {
                console.log(error);
                if (chat) {
                    console.log(chat);
                    io.to(roomId).emit("instant message received", msg, to, from);
                    return;
                }

            })
        });
        socket.on("instant message seen", async (roomId, from, to) => {
            con.query(`SELECT count(*) as seen_count FROM instant_messages WHERE is_seen=0 AND ((user_to='${to}' AND user_from='${from}') OR (user_to='${from}' AND user_from='${to}'))`, async function (error, chat) {
                await con.query(`UPDATE instant_messages SET is_seen=1 WHERE (user_to='${to}' AND user_from='${from}') OR (user_to='${from}' AND user_from='${to}')`, function (err, update) {


                })
                if (chat) {
                    io.to(roomId).emit("instant message seen", chat[0].seen_count);
                    return;
                }

            })
        });
        socket.on("instant message fetch", (roomId, to, from) => {

            console.log(to, from);
            con.query(`SELECT * FROM instant_messages WHERE (user_to='${to}' AND user_from='${from}') OR (user_to='${from}' AND user_from='${to}')`, function (error, chat) {
                console.log(error);
                if (chat) {
                    console.log(chat);
                    io.to(roomId).emit("instant message fetch", chat);
                    return;
                }

            })
        });
        socket.on("instant chats fetch", (roomId, user) => {
            con.query(`SELECT * FROM instant_chats WHERE user_to='${user}' OR user_from='${user}'`, function (error, chat) {
                console.log(error);
                if (chat) {
                    console.log(chat);
                    io.to(roomId).emit("instant chats fetch", chat);
                    return;
                }

            })
        });
        socket.on("disconnect", () => {
            con.query(`UPDATE chatroom_user SET is_active=0 WHERE user='${connectedUser}' AND chatroom='${chatRoom}'`, function (err, active) {
                console.log(active);
            })
        })
    })

});
con.on("error", function (err) {
    console.log("Error", err);
})
https.listen(process.env.PORT || 3005, () => {
    console.log('listening on *:3005');
});
