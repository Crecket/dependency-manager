//$(function () {
//
//    var globalSocket;
//    if (document.location.hostname == "localhost" ||
//        document.location.hostname == "nodejs-game" ||
//        document.location.hostname == "crecket.dev"
//    ) {
//        globalSocket = io.connect('localhost:8000/global');
//    } else {
//        globalSocket = io.connect('crecket.me:8000/global', {secure: true});
//    }
//    var connected = true;
//
//    globalSocket.on('connect', function (data) {
//        // connected
//        $('#online-count').text('1');
//    }).on('connect_error', function (data) {
//        connected = false;
//        $('#online-count').text('?');
//    }).on('disconnect', function (data) {
//        connected = false;
//        $('#online-count').text('?');
//        Messenger().post({
//            message: 'Lost contact with server',
//            type: 'error',
//            showCloseButton: true
//        });
//    }).on('server notification', function (data) {
//        var type = data.type;
//        var message = data.message;
//
//        Messenger().post({
//            message: message,
//            type: type,
//            position: 'top left',
//            showCloseButton: true
//        });
//    }).on('server heartbeat', function (data) {
//        $('#online-count').text(data.online);
//    });
//
//    setInterval(function(){
//        if(!connected){
//            console.log('globalSocket server is offline');
//            Messenger().post({
//                message: 'No contact with server',
//                type: 'error',
//                showCloseButton: true
//            });
//        }
//    }, 5000);
//
//});