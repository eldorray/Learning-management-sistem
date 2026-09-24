# Kage landing integration

The registered ThreeUI source revision is c8e06b90397ac246baf0ab6f32f5f6b570acc6fe03c7009f711b579fb72d9f49.

Original source: https://threeui.com/source-code/kage-landing-page.json
Canonical document: https://threeui.com/landing-pages/kage.html
Binary assets were copied byte-for-byte from @designcodeio/threeui 1.2.0 and checked against the supplied hashes. License notices are retained here.

All eight registered source files are preserved under this directory with their original paths. The Kage-only entry in resources/js/threeui-kage.tsx copies the registered Kage function verbatim while avoiding unrelated component imports whose sources are not in this bundle. Vite aliases @designcodeio/threeui and its stylesheet to these local exact-source files. This is a vendored source integration, not an installed ThreeUI package dependency. React is used only for the authored frame and typography controls.

The original HTML remains immutable here. GET /landing-pages/kage.html serves a derived response, replacing only allowlisted byte-offset content anchors before the original scripts boot. The original styles, shaders, scene scripts and asset-relative paths remain in place. Do not put a static public/landing-pages/kage.html file ahead of that route: it would bypass saved settings.

Admin: /admin/landing-page (admin only).
Preview: /landing-preview (also works when signed in).
Storage: existing settings table, landing_page_content JSON key; no migration required.
Uploads: public disk, landing/ directory. Ensure php artisan storage:link resolves to this project's storage/app/public on deployment.

The CMS covers all authored editorial text nodes except the generated loader progress counter, metadata/accessibility labels, canvas wordmark, six rail labels, all 21 raster image occurrences, nine inline artwork overrides and favicon. Shader code, technical diagnostic strings, geometry, lighting and procedural scenery are not editable content. Foreground layers can be replaced per occurrence rather than only per shared filename. Plain text is HTML-escaped, JS literals are JSON-escaped, images are managed uploads, and scene navigation uses an anchor allowlist.

An LMS access strip occupies its own space below the authored viewport, retaining login/register/PWA access without covering the scene. Authenticated home redirects remain unchanged.

Checks:

    node scripts/verify-kage.mjs
    php artisan test --compact
    npm run build

Browser QA used a separate scratch SQLite database: desktop and mobile scene readiness, all six scroll sections, mobile menu, all foreground image loads, typography, text/canvas edits, upload/readback/reset and login navigation. No working database content was overwritten.

Known upstream build warning: threeui.css references ./fonts/fragment-mono.woff2 for unrelated catalog components, not Kage. Kage's Onest/Japanese/wordmark fonts are self-contained in secret-pathways-assets/fonts.css. npm audit also reports vulnerabilities in existing build-tool dependencies; no broad dependency upgrades were applied in this feature.
