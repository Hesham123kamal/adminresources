 var config = {
    apiKey: "AIzaSyD9WKo1BWN9paLWtOB8qQFDY4Tu2SAjyNs",
    authDomain: "academy-2ec85.firebaseapp.com",
    databaseURL: "https://academy-2ec85.firebaseio.com",
   
    storageBucket: "academy-2ec85.appspot.com",
    messagingSenderId: "991133136964"
  };
/*var config = {
    apiKey: "AIzaSyBvZKV3CO2J5MiHwii-RLEUwRAA3fxWto8",
    authDomain: "newproject-157016.firebaseapp.com",
    databaseURL: "https://newproject-157016.firebaseio.com",
    projectId: "newproject-157016",
    storageBucket: "newproject-157016.appspot.com",
    messagingSenderId: "984881592366"
};*/
firebase.initializeApp(config);
function SendToken(token){
    var xmlHttp = new XMLHttpRequest();
    var data=new FormData();
    data.append('token',token)
    xmlHttp.open( "POST", "saveTokenNumber.php", false ); // false for synchronous request
    xmlHttp.send(data);
}
const messaging =firebase.messaging();
messaging.requestPermission()
.then(function(){
    console.log('Have Permission');
    return messaging.getToken();
}).then(function(token){
    console.log('get token')
    console.log(token)
    SendToken(token);
}).catch(function(err){
        console.log('Error Occured'+err);
});
messaging.onMessage(function(payload){
    //console.log(payload.notification);
    //console.log(payload.notification)
    var notification = new Notification(payload.notification.title, {
        icon: payload.notification.icon,
        body: payload.notification.body,
    });
    notification.onclick = function () {
        window.open(payload.notification.click_action);
    };
});
