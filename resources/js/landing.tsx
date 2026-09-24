import { createRoot } from 'react-dom/client';
import { KageLandingPage } from '@designcodeio/threeui';
import '@designcodeio/threeui/style.css';
import '../css/landing.css';
import './app.js';

export function Scene() {
    return (
        <div className="shader-frame">
            <KageLandingPage
                headingFont="onest"
                bodyFont="onest"
                headingWeight="400"
                bodyWeight="300"
                primaryColor="#e0231c"
                headingSize={46}
                bodySize={17}
                headingLetterSpacing={-0.012}
            />
        </div>
    );
}

const root = document.getElementById('kage-root');
if (root) createRoot(root).render(<Scene />);

window.addEventListener('message', (event) => {
    const frame = document.querySelector('#kage-root iframe');
    if (event.origin !== location.origin || event.source !== frame?.contentWindow) return;
    if (event.data?.type === 'arrahmah-navigate' && ['/login', '/register'].includes(event.data.path)) {
        location.assign(event.data.path);
    }
});

document.getElementById('pwa-install')?.addEventListener('click', () => window.triggerPwaInstall());
document.getElementById('pwa-later')?.addEventListener('click', () => document.getElementById('pwa-install-banner')?.remove());
