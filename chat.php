<?php 
include("dbconn.php");
require_once("includes/functions.php"); 
include("header.php");
$username = mysql_real_escape_string($_POST['user']);
$username = strip_tags($username);
$username = htmlentities($username);
?>
<div class="container">
  <div class="row justify-content-center chathead">
    <h1>Demo Chatroom</h1>
  </div>
</div>

<section class="vh-100 gradient-custom">
<div class="chatContainer right" style="margin-top:50px; margin: 0px auto;background-color: #BCBCBC; width: 70%; border: 1px #a7a7a7 solid"> 
	<?php if(isBanned($username)==false){ ?>
                        <div class="container-fluid" style="background:white;">
                            <div class="row" style="padding-top:10px;">
                                <div class="col-md-6">
                                    <button class="btn btn-block btn-tab-control btn-primary btn-sm" data-tab=".tab-live-chat"> Live Chat </button>
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-block btn-tab-control btn-success btn-sm" data-tab=".tab-inbox" style="background-color: #1c4ca0;"> Private Message <span id="alertMessage">0</span> </button>
                                </div>
                            </div>
                            <div class="tabs-control">
                                <div class="tab active tab-live-chat">
                                    <div class="row">
                                        <div class="col-md-8 stream-messages-container"  style="padding-left: 15px; padding-right: 0px; width: 77%; height: 100%" >
                                            <div class="messages-header">
                                            </div>
                                            <div class="messages" id="chatjoin">
                                            </div>
                                        </div>
                                        <div class="col-md-4 users-container" style="padding: 0px; width: 20%; height: 100%">
                                            <div class="users-header">
                                            </div>
                                            <div class="users">
                                                <div class="host-users">
                                                    <div class="host-users2">HOST</div>
                                                    <div id="host-users" onmouseover="this.style.background='#ebebeb';" onmouseout="this.style.background='white';"></div>
                                                </div>
                                                <div class="mod-users">
                                                    <div class="host-users2">MODS</div>
                                                    <div id="mod-users"></div>
                                                </div>
                                                <div class="normal-users">
                                                    <div class="host-users2">ROSTER</div>
                                                    <div id="normal-users" onmouseover="this.style.background='#ebebeb';" onmouseout="this.style.background='white';"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12" style="padding-left: 30px;padding-top:30px;">
                                            <form id="msgForm" class="row">
                                                <div class="col-md-9" style="padding:0;">
                                                    <input type="text" id="messageValue" class="form-control" autocomplete="off"/>
                                                </div>
                                                <div class="col-md-3">
                                                    <button class="btn btn-sm send-message btn-primary btn-block" type="submit" style="background-color: #d21f3d;">Send</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab tab-inbox">
                                    <div class="row">
                                        <div class="col-md-8 stream-messages-container" style="padding-left: 15px; padding-right: 0px; width: 70%; height: 100%">
                                            <div class="messages-header">
                                            </div>
                                            <div class="personal-chat-messages">
                                            </div>
                                        </div>
                                        <div class="col-md-4 users-container" style="padding: 0px; width: 28%; height: 100%" >
                                            <div class="users-header">
                                            </div>
                                            <div class="users">
                                                <div class="mychats-users">
                                                    <div class="host-users2">PRIVATE MSG</div>
                                                    <div style="padding-left: 5px" id="mychats-users"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12" style="padding-left: 30px;padding-top:30px;">
                                            <form id="instantmsgForm" class="row">
                                                <div class="col-md-9" style="padding:0;">
                                                    <input type="text" id="messageValuePersonal" class="form-control" autocomplete="off" />
                                                </div>
                                                <div class="col-md-3">
                                                    <button class="btn btn-sm send-message btn-primary btn-block" style="background-color: #d21f3d;" type="submit">Send</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script>
                        $(document).ready(function() {
                            var usersContainer = CtxMenu(".ctxMenu");
                            var modContainer= CtxMenu(".ctxMenuMod");
                            var hostContainer= CtxMenu(".ctxHost");
                            var instantUser=0;
                            var localStorage=window.localStorage;
                            function initLocal()
                            {
                                var check=localStorage.getItem("instantChats");
                                if(!check){
                                    localStorage.setItem("instantChats",JSON.stringify([]));
                                }
                            }
                            initLocal();
                            function pushChat(to,from)
                            {
                                 var check=JSON.parse(localStorage.getItem("instantChats"));
                                 check.push(JSON.stringify({
                                     to:to,
                                     from:from,
                                     messages:[]
                                 }));
                                 localStorage.setItem("instantChats",JSON.stringify(check));
                            }
                            $(".btn-tab-control").on("click", function() {
                                var cLass = $(this).data("tab");
                                $(".tab").removeClass("active");
                                $(cLass).addClass("active");
                                // $(cLass).animate({
                                //     display: "block"
                                // }, 500);
                            })
                            // $("#messageValue").emojioneArea();
                            // $("#messageValuePersonal").emojioneArea();
                            const socket = io("https://chatroom.darrylpolo.tech:3005");
                            var roomId = "DemoChat";
                            var id = "<?php echo $username; ?>";

                            <?php if(isModerator($username)){ ?>
                            var user = {
                                "username": id,
                                "is_admin": true,
                                "joined_time": new Date().getTime(),
                            };
                            socket.emit("init join", user, roomId, admin = true);
                            <?php
                        }else{
                            ?>
                            var user = {
                                "username": id,
                                "is_admin": false,
                                "joined_time": new Date().getTime(),
                            };
                            socket.emit("init join", user, roomId, admin = false);
                            <?php
                        }
                        ?>
                            socket.on("join success", (roomId) => {
                                console.log("Joined to: " + roomId);
                            })
                            socket.on("user joined", (users) => {
                                console.log(users);
                                $("#host-users").html("");
                                $("#normal-users").html("");
                                $("#mod-users").html("");
                                users.forEach(function(v, i) {
                                    if (v.is_banned == 0) {
                                        if (v.type == "mods") {
                                            if (v.user==roomId) {
                                                $("#host-users").append(`<div class="ctxHost" data-id="${v.user}" style="padding-left: 5px; padding-bottom:2px;font-size:12px;"><a style="color:#1c4ca0;" href="#" class="username">${v.user}</a></div>`);
                                            } else {
                                                if (v.user != id && v.user != v.chatroom) {
                                                    $("#mod-users").append(`<div class="ctxMenuMod" data-id="${v.user}" style="padding-left: 5px; padding-bottom:2px; font-size:12px;"  onmouseover="this.style.background='#ebebeb';" onmouseout="this.style.background='white';" ><a style="color:#1c4ca0;" href="#" class="username">${v.user}</a></div>`);
                                                } else {
                                                    $("#mod-users").append(`<div class="usernamesel" style="padding-left: 5px; padding-bottom:2px; font-size:12px;" onmouseover="this.style.background='#ebebeb';" onmouseout="this.style.background='white';"><a style="color:#1c4ca0;" href="#" class="username">${v.user}</a></div>`);
                                                }
                                            }
                                        } else {
                                            $("#normal-users").append(`<div class="ctxMenu" data-id="${v.user}" style="padding-left: 5px; padding-bottom:2px; font-size:12px;" onmouseover="this.style.background='#ebebeb';" onmouseout="this.style.background='white';"><a style="color:#1c4ca0;" href="#" class="username">${v.user}</a></div>`);
                                        }
                                    }
                                });
                                socket.emit("instant chats fetch",roomId,id);
                            })
                            <?php if(isModerator($username)) { ?>
                            usersContainer.addItem("Kick", function(el) {
                                socket.emit("kick user", $(el).data("id"), roomId);
                            });
//                            usersContainer.addItem("Ban", function(el) {
//                                socket.emit("ban user", $(el).data("id"), roomId);
//                            });
//                            usersContainer.addItem("Make Moderator", function(el) {
//                                socket.emit("make moderator", $(el).data("id"), roomId);
//                            });
//                            
//                            modContainer.addItem("Remove as Moderator", function(el) {
//                                socket.emit("remove moderator", $(el).data("id"), roomId);
//                            });
                            <?php } ?>
							<?php if ($username == $caster) { ?>
							modContainer.addItem("Kick", function(el) {
                                socket.emit("kick user", $(el).data("id"), roomId);
                            });
                            modContainer.addItem("Ban", function(el) {
                                socket.emit("ban user", $(el).data("id"), roomId);
                            });
							modContainer.addItem("Remove as Moderator", function(el) {
                                socket.emit("remove moderator", $(el).data("id"), roomId);
                            });
							                            usersContainer.addItem("Ban", function(el) {
                                socket.emit("ban user", $(el).data("id"), roomId);
                            });
                            usersContainer.addItem("Make Moderator", function(el) {
                                socket.emit("make moderator", $(el).data("id"), roomId);
                            });
							<?php } ?>
                            usersContainer.addItem("Private Message", function(el) {
                                var userId=$(el).data("id");
                                $(".btn-tab-control").each(function(i, v) {
                                    if ($(v).data("tab") == ".tab-inbox") {
                                        $(v).trigger("click");
                                        socket.emit("start instant chat",roomId,userId,id);
                                    }
                                })
                            });
                            usersContainer.addItem("View Profile", function(el) {
                                window.location.href = "/" + $(el).data("id");
                            });
                            modContainer.addItem("Private Message", function(el) {
                                var userId=$(el).data("id");
                                $(".btn-tab-control").each(function(i, v) {
                                    if ($(v).data("tab") == ".tab-inbox") {
                                        $(v).trigger("click");
                                        socket.emit("start instant chat",roomId,userId,id);
                                    }
                                })
                            });
                            modContainer.addItem("View Profile", function(el) {
                                window.open("/" + $(el).data("id"),"_blank");
                            });					
                            hostContainer.addItem("Private Message", function(el) {
                                var userId=$(el).data("id");
                                $(".btn-tab-control").each(function(i, v) {
                                    if ($(v).data("tab") == ".tab-inbox") {
                                        $(v).trigger("click");
                                        socket.emit("start instant chat",roomId,userId,id);
                                    }
                                })
                            });
                            hostContainer.addItem("View Profile", function(el) {
                                window.open("/" + $(el).data("id"),"_blank");
                            });
                            socket.on("user kicked", function(userId) {
                                if (id == userId) {
									alert("You have been kicked from the livestream.");
                                    window.location.href = "index.php";
                                }
                            })
                            $(document).on("click",".instantUser",function(){
                                console.log("HERE");
                                $(".personal-chat-messages").html("");
                                instantUser=$(this).data("room");
                                socket.emit("instant message seen",roomId,id,instantUser);
                                socket.emit("instant message fetch",roomId,id,instantUser);
                                $("#activeChat").html(instantUser);
                            })
                            socket.on("instant message seen",function(rows){
                                console.log(rows);
                                if(parseInt($("#alertMessage").html())>0)
                                {
                                 $("#alertMessage").html(parseInt($("#alertMessage").html())-rows);
                                }
                                
                            })
                            socket.on("instant message fetch",function(messages){
                                console.log(messages);
                                $(".personal-chat-messages").html("");
                               messages.forEach(function(v,i){
                                   $(".personal-chat-messages").append(`<div class="message">
                                        <p><span class="username"><strong>${v.user_from}</strong></span>: ${v.text}</p>
                                    </div>`);
                               })
                               
                                    scrollSmoothToBottom("personal-chat-messages");
                                
                            })
                            socket.on("instant chats fetch",function(chats){
                                $("#mychats-users").html("");
                               chats.forEach(function(v,i){
                                   if(v.user_from==id){
                                        $("#mychats-users").append(`<div data-id="${v.user_to}" class="instantUser" onmouseover="this.style.background='#ebebeb';" onmouseout="this.style.background='white';" data-room=${v.user_to} ><a href="#" class="username">${v.user_to}</a></div>`);
                                   }
                                   if(v.user_to==id){
                                       $("#mychats-users").append(`<div data-id="${v.user_from}" class="instantUser" onmouseover="this.style.background='#ebebeb';" onmouseout="this.style.background='white';" data-room=${v.user_from}><a href="#" class="username">${v.user_from}</a></div>`);
                                   }
                               })
                                
                            })
                            socket.on("instant chat started",function(roomId,to,from){
                                socket.emit("instant chats fetch",roomId,id);return;
                                if(to==id || from==id)
                                {
                                    if(from==id)
                                    {
                                        instantUser=to;
                                        $("#mychats-users").append(`<li data-id="${to}" class="instantUser" data-room=${to}><a href="#" class="username">${to}</a></li>`);
                                    }
                                    if(to==id)
                                    {
                                        instantUser=from;
                                        $("#mychats-users").append(`<li data-id="${from}" class="instantUser" data-room=${from}><a href="#" class="username">${from}</a></li>`);
                                    }
                                }
                            })
                            socket.on("moderator made", function(userId) {
                                if (id == userId) {
                                    window.location.reload();
                                }
                            })
                            socket.on("moderator removed", function(userId) {
                                if (id == userId) {
                                    window.location.reload();
                                }
                            })
                            

                            function scrollSmoothToBottom(id) {
                                var div = $("." + id)[0];
                                $('.' + id).animate({
                                    scrollTop: div.scrollHeight - div.clientHeight
                                }, 500);
                            }
                            $("#instantmsgForm").on("submit", function(e) {
                                e.preventDefault();
                                if ($("#messageValuePersonal").val().length == 0) {
                                    return;
                                }
                                socket.emit("instant message", $("#messageValuePersonal").val(), roomId,instantUser,id);
                                 $(".personal-chat-messages").append(`<div class="message">
                                        <p><span class="username"><strong>${id}</strong></span>: ${ $("#messageValuePersonal").val()}</p>
                                    </div>`);
                                    scrollSmoothToBottom("personal-chat-messages");
                                $("#messageValuePersonal").val('');
                                // $("#messageValue")[0].emojioneArea.setText("");
                            })
                            socket.on("instant message received", (msg,to,from) => {
                                console.log(msg,to,from);
                                if(instantUser==from){
                                    socket.on("instant message seen",roomId,to,from);
                                    if(to==id){
                                     
                                     $(".personal-chat-messages").append(`<div class="message">
                                        <p><span class="username"><strong>${from}</strong></span>: ${msg}</p>
                                    </div>`);
                                    scrollSmoothToBottom("personal-chat-messages");
                                 }
                                }else{
                                    if(to!=id)
                                    {
                                        $("#alertMessage").html(parseInt($("#alertMessage").html())+1)
                                    }
                                }
                                 
                            });
                            $("#msgForm").on("submit", function(e) {
                                e.preventDefault();
                                if ($("#messageValue").val().length == 0) {
                                    return;
                                }
                                var msg = {
                                    "username": id,
                                    "time": new Date().getTime(),
                                    "text": $("#messageValue").val(),
                                    "user": user
                                };
                                socket.emit("chat message", msg, roomId);
                                $("#messageValue").val('');
                                // $("#messageValue")[0].emojioneArea.setText("");
                            })
							
                            socket.on("chat message received", (msgObj) => {
                                if (msgObj.username == roomId) {
                                    $(".messages").append(`<div class="message">
                        <p><span class="username"><span class="modMsg" style="background-color: #d21f3d;">HOST</span><strong>${msgObj.username}</strong></span>: ${msgObj.text}</p>
                    </div>`);
                                } else {
                                    if (msgObj.user.is_admin) {
                                        $(".messages").append(`<div class="message">
                        <p><span class="username"><span class="modMsg">MOD</span><strong>${msgObj.username}</strong></span>: ${msgObj.text}</p>
                    </div>`);
                                    } else {
                                        $(".messages").append(`<div class="message">
                        <p><span class="username"><strong>${msgObj.username}</strong></span>: ${msgObj.text}</p>
                    </div>`);
                                    }
                                }
                                scrollSmoothToBottom("messages");
                            });
                        });
                        </script> 
                         <?php } ?>
                    </div>
                </div>
</div>