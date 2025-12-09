self.addEventListener("push", function (event) {
    let data = {};

    try {
        data = event.data.json();
    } catch (e) {
        console.error("Push JSON parse error:", e);
        return;
    }

    event.waitUntil(
        self.registration.showNotification(data.title || "Notification", {
            body: data.body,
            icon: data.icon,
            badge: data.badge,
            data: data.data
        })
    );

    if (data.data) {
        self.clients.matchAll({ includeUncontrolled: true }).then(clients => {
            clients.forEach(client => {
                client.postMessage({
                    type: "PUSH_MESSAGE",
                    payload: data.data
                });
            });
        });
    }
});
