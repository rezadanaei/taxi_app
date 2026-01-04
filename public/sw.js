self.addEventListener("push", function (event) {
    if (!event.data) return;

    let payload;
    try {
        payload = event.data.json();
    } catch {
        return;
    }

    const title = payload.title || "نوتیفیکیشن جدید";
    const body  = payload.body || "";
    const icon  = payload.icon || "/icons/icon-192.png";
    const badge = payload.badge || "/icons/badge-72.png";

    const customData = payload.data || {};

    event.waitUntil(
        (async () => {
            await self.registration.showNotification(title, {
                body,
                icon,
                badge,
                data: customData,
                vibrate: [100, 50, 100],
            });

            const clients = await self.clients.matchAll({
                includeUncontrolled: true,
            });

            for (const client of clients) {
                client.postMessage({
                    type: customData.type,
                    payload: customData,
                });
            }
        })()
    );
});
