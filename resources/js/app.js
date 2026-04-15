// ── Service Worker Registration ────────────────────────────
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    });
}

// ── PWA Install Prompt ─────────────────────────────────────
let deferredPrompt = null;

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;

    // Show the install banner if it exists on the page
    const banner = document.getElementById('pwa-install-banner');
    if (banner) {
        banner.classList.remove('hidden');
        banner.classList.add('flex');
    }
});

window.addEventListener('appinstalled', () => {
    deferredPrompt = null;
    const banner = document.getElementById('pwa-install-banner');
    if (banner) banner.remove();
});

// Called from the install button
window.triggerPwaInstall = function () {
    if (!deferredPrompt) return;
    deferredPrompt.prompt();
    deferredPrompt.userChoice.then(() => {
        deferredPrompt = null;
        const banner = document.getElementById('pwa-install-banner');
        if (banner) banner.remove();
    });
};

// ── Push Notification Permission ───────────────────────────
window.requestNotificationPermission = async function () {
    if (!('Notification' in window)) return;
    const permission = await Notification.requestPermission();
    return permission;
};
