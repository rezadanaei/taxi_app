/* -------------------------------
   Push Receiver
-------------------------------- */
self.addEventListener("push", function(event) {
    let data = {};

    try {
        data = event.data ? event.data.json() : {};
    } catch {
        data = {};
    }

    const options = {
        body: data.body || "پیام جدید دارید.",
        icon: data.icon || "/icon.png",
        badge: data.badge || "/badge.png",
        data: {
            url: data.data?.url || "/",
            type: data.data?.type || null,
            trip: data.data?.trip || null
        }
    };

    event.waitUntil(
        self.registration.showNotification(data.title || "اعلان جدید", options)
    );

   /* -----------------------------------------------
    If the /profile page is open → send the data immediately
    Only if type = "trip"
    ----------------------------------------------- */

    if (options.data.type === "trip") {

        event.waitUntil(
            self.clients.matchAll({ type: "window", includeUncontrolled: true })
                .then(clients => {

                    clients.forEach(client => {
                        const url = new URL(client.url);

                        if (url.pathname === "/profile") {
                            client.postMessage({
                                type: "trip",
                                trip: options.data.trip
                            });
                        }
                    });
                })
        );
    }
});
