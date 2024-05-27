require([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'domReady!'
], function ($, modal) {
    window.notificationTimeout = undefined;
    window.bopisNotifications = [];
    function checkNotifications() {

        window.notificationTimeout = undefined;
        $.get(WS_NOTIFICATION_URL, {}, function (data) {
            window.bopisNotifications = data.notifications;
            sendNotifications();
        });
    }
    function sendNotifications() {
        if (Notification.permission !== "granted" && Notification.permission !== "denied") {
            Notification.requestPermission().then((permission) => {
                // If the user accepts, let's create a notification
                if (permission === "granted") {
                    sendNotifications();
                }
            });
        } else if(Notification.permission === "granted") {
            console.log(window.bopisNotifications);
            $.each(window.bopisNotifications, function (index, data) {
                console.log(data);
                var notification = new Notification(data.title, {
                    icon: data.icon,
                    body: data.body,
                });

                notification.onclick = function () {
                    window.open(data.url);
                };
                $.get(data.notification_url, {}, function (data) {});
            });

            window.notificationTimeout = setTimeout(checkNotifications, 10000);

        }
    }
    if(ADMIN_IS_LOGGED_IN > 0) {
        checkNotifications();
    }
});
