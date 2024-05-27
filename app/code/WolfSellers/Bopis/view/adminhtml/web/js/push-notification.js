define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';
    return function(config)
    {
        window.PUSH_ODER_ID = config.orderId;
        var showNotification =function() {
            if (!Notification) {
                return;
            }
            if (Notification.permission !== "granted") {
                return;
            }
            $.ajax({
                url:config.url,
                global: false,
                dataType: 'json',
                type: 'POST',
                data: {orderId:window.PUSH_ODER_ID,form_key:window.FORM_KEY},
                async: true
            }).done(
                function (response) {

                    if(response.result == 'success')
                    {
                        var snd = new Audio("/media/pushnotification/"+config.sound);
                        snd.play();

                        var notification = new Notification("Nueva orden #"+response.order, {
                            icon: "/media/pushnotification/"+config.icon,
                            vibrate: [100,2000,100],
                            //sound: "/media/notification/notification.mp3",
                            body: config.notificationtext,
                            requireInteraction: true
                        });
                        notification.onclick = function () {
                            window.open(response.url);
                            notification.close();
                        };
                        window.PUSH_ODER_ID = response.orderId;
                    }

                }
            ).fail(

            );

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
        setInterval(function(){ showNotification(); }, (config.seconds * 1000));
    }

});
