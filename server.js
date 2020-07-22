var express = require('express');
var app = express();
var http = require('http').Server(app);
var io = require('socket.io')(http);


var port = "3000";
var socketid = [];
var numJoined = [];


// 메시지를 전송한 클라이언트를 제외한 모든 클라이언트에게 메시지를 전송한다
// socket.broadcast.emit('chat', msg);

// 메시지를 전송한 클라이언트에게만 메시지를 전송한다
// socket.emit('s2c chat', msg);

// 접속된 모든 클라이언트에게 메시지를 전송한다
// io.emit('s2c chat', msg);

// 특정 클라이언트에게만 메시지를 전송한다
// io.to(id).emit('s2c chat', data);
// force client disconnect from server


io.on('connection', function(socket) {

    console.log("socket io connected  / socket id : "+socket.id);


    //USER SOCKET ID 저장
    socket.on('onconnect', function(data){

        socket.name = data.userid;
        socketid[socket.name]= socket.id;
        console.log('----------[USER CONNECT]----------');
        console.log('[on connect] userid : ' + data.userid);
        console.log('[on connect] socketid[socket.name] : ' + socketid[socket.name]);
        console.log('[on connect] socketid[data.userid] : ' + socketid[data.userid]);
        socket.emit('checked',"checked");

    });

    //USER JOIN TO CHATROOMLIST
    socket.on('joinchatroomlist', function(data){

        var chatroomid = data.storyindex;
        var userid = data.userid;

        socket.join(chatroomid);
        console.log('----------[JOIN CHATROOM LIST]----------');
        console.log('roomid :::  '+data.storyindex);
        console.log('userid  :::  '+data.userid);
        console.log('socket.name  :::  '+socket.name);
        console.log('socketid[socket.name]  :::  '+socketid[socket.name]);

    });
    //USER JOIN TO CHATROOM
    socket.on('joinchatroom', function(data){

        var chatroomid = data.storyindex;
        var userid = data.userid;
        socket.join(chatroomid);
        var msg = {
            "storyindex" : chatroomid,
            "userindex" : userid
        }

        if (numJoined[chatroomid] == undefined || numJoined[chatroomid]<=0){
            numJoined[chatroomid]=1;
        }else{
            numJoined[chatroomid]++;
        }

        io.to(chatroomid).emit("joinchatroom",msg)

        console.log('----------[USER JOIN CHATROOM]----------');
        console.log('roomid :::  '+chatroomid);
        console.log('userid  :::  '+userid);
        console.log('socket.name  :::  '+socket.name);
        console.log('socketid[socket.name]  :::  '+socketid[socket.name]);
        console.log('numJoined[chatroomid]  :::  '+numJoined[chatroomid]);
    });
    //USER JOIN TO CHATROOM
    socket.on('leavechatroom', function(data){

        var chatroomid = data.chatroomid;

        if (numJoined[chatroomid] != undefined){
            numJoined[chatroomid]--;
        }

        console.log('----------[USER leave CHATROOM]----------');
        console.log('roomid :::  '+chatroomid);
        console.log('socket.name  :::  '+socket.name);
        console.log('socketid[socket.name]  :::  '+socketid[socket.name]);
        console.log('numJoined[chatroomid]  :::  '+numJoined[chatroomid]);
    });

    socket.on('newjointochatroom',function (data) {
        var storyindex = data.storyindex;
        var data = {

            storyindex : storyindex
        }

        console.log('----------[new join chatroom]----------');
        console.log('roomid :::  '+data.storyindex);
        io.to(data.storyindex).emit('newcomer',data)
    })



//CLIETN 로부터 CHATROOMID 받아서 해당 ROOM으로 메세지 전송.
    socket.on('newmessage_fromclient', function(data){
        var numJoinedcount = numJoined[data.chatroomid];

        var message = {
            message : data.message,
            userid : data.userid,
            chatroomid : data.chatroomid,
            userprofile : data.userprofile,
            currentusername : data.currentusername,
            sendtime : data.sendtime,
            read : numJoinedcount
        };


        console.log('----------[NEW MESSAGE]----------');
        console.log('message  :::  '+data.message);
        console.log('roomid :::  '+data.chatroomid);
        console.log('sendinguser  :::  '+data.currentusername);
        console.log('userid  :::  '+data.userid);
        console.log('userprofile  :::  '+data.userprofile);
        console.log('sendtime  :::  '+data.sendtime);
        console.log('numJoinedcount  :::  '+numJoinedcount);

        io.to(data.chatroomid).emit('newmessagefrom_server', message);
    });
    socket.on('clientRejectCall', function(data){
        console.log('data'+data);

        var id =socketid[data.callerindex];
        var message = {
            reject : data.reject
        };
        console.log('[client reject call] socket id :  '+id+'   message data.reject : '+data.reject);
        io.to(id).emit('rejectFromCallee', message);
    });
    socket.on('faceInfo', function(data){

        var id =socketid[data.oppositeUserIndex];
        var message = {
            faceX : data.faceX,
            faceY : data.faceY,
            facewidth : data.facewidth,
            faceheight : data.faceheight,
            bitmapWidth : data.bitmapWidth,
            bitmapHeight : data.bitmapHeight
        };
        console.log('id: ' + data.oppositeUserIndex);
        console.log('socketid: ' + socketid[data.oppositeUserIndex]);
        console.log('data.faceY: ' +data.faceY);
        io.to(id).emit('getFaceInfo', message);
    });

    socket.on('disconnect', function() {

        console.log('----------[USER DISCONNECT]----------');
        console.log('user disconnected: ' + socket.name);
        console.log('user disconnected: ' + socketid[socket.name]);
        delete socketid[socket.name];
        console.log('user disconnected check: ' + socketid[socket.name]);
        console.log('-------------------------------------');

    });


});

//안씀 / 작동은 함
function NumClientsInRoom(room) {
    var clients = io.nsps["/"].adapter.rooms[room];
    return Object.keys(clients).length;
}

http.listen(port, function(){
    console.log('listening on *:'+port);
});