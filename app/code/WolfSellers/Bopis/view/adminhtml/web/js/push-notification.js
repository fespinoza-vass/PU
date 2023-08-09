require([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';
        
    var showNotification =function() {
        if (!Notification) {
            return;
        }
        if (Notification.permission !== "granted") {
            Notification.requestPermission();
        } 
        else {
            var notification = new Notification("Nueva orden #000001", {
                //icon: data_notif[i]['icon'],
                vibrate: [100,2000,100],
                sound: "/media/notification/notification.wav",
                body: "Prueba de contenido"
            });

        }
    }
    $("#allow_notification").hide();
    if (Notification.permission !== "granted"){
        $("#allow_notification").show();
    }

    $("#allow_notification").click(function(){
        if (Notification.permission !== "granted"){
            Notification.requestPermission();
            $("#allow_notification").hide();
        }
    });
    setInterval(function(){ showNotification(); }, 20000);
  
});
