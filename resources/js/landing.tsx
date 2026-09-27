import { createRoot } from 'react-dom/client';
import { MengToSketchbookLandingPage } from '@designcodeio/threeui';
import '@designcodeio/threeui/style.css';
import '../css/landing.css';
import './app.js';

export function Scene() {
    return (
        <div className="shader-frame">
            <MengToSketchbookLandingPage
                headingFont="instrument-serif"
                bodyFont="newsreader"
                headingWeight="400"
                bodyWeight="400"
                primaryColor="#2b2721"
                headingSize={30}
                bodySize={20}
                headingLetterSpacing={0.010}
            />
        </div>
    );
}

const root = document.getElementById('landing-root');
if (root) createRoot(root).render(<Scene />);

window.addEventListener('message', (event) => {
    const frame = document.querySelector('#landing-root iframe');
    if (event.origin !== location.origin || event.source !== frame?.contentWindow) return;
    if (event.data?.type === 'arrahmah-navigate' && ['/login', '/register'].includes(event.data.path)) {
        location.assign(event.data.path);
    }
});

document.getElementById('pwa-install')?.addEventListener('click', () => window.triggerPwaInstall());
document.getElementById('pwa-later')?.addEventListener('click', () => document.getElementById('pwa-install-banner')?.remove());
