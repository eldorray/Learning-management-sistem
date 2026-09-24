import { readFileSync } from 'node:fs';
import { createHash } from 'node:crypto';
import assert from 'node:assert/strict';

const manifest = JSON.parse(readFileSync(new URL('../resources/threeui/manifest.json', import.meta.url)));
const root = new URL('../', import.meta.url);
for (const entry of manifest.files) {
    const bytes = readFileSync(new URL(`resources/threeui/${entry.path}`, root));
    assert.equal(createHash('sha256').update(bytes).digest('hex'), entry.sha256, entry.path);
    if (entry.path.startsWith('public/') && !entry.path.endsWith('kage.html')) {
        assert.equal(createHash('sha256').update(readFileSync(new URL(entry.path, root))).digest('hex'), entry.sha256);
    }
}
for (const entry of manifest.assets) {
    const bytes = readFileSync(new URL(entry.path, root));
    assert.equal(bytes.length, entry.bytes, entry.path);
    assert.equal(createHash('sha256').update(bytes).digest('hex'), entry.sha256, entry.path);
}
const source = readFileSync(new URL('resources/threeui/public/landing-pages/kage.html', root));
const schema = JSON.parse(readFileSync(new URL('resources/threeui/content-schema.json', root)));
const spans = Object.entries(schema).filter(([, field]) => field.offset !== undefined).sort((a,b) => a[1].offset-b[1].offset);
let end = 0;
for (const [key, field] of spans) {
    assert.ok(field.offset >= end, `Overlapping slot ${key}`);
    assert.equal(source.subarray(field.offset, field.offset+field.length).toString(), field.source, key);
    end = field.offset+field.length;
}
console.log(`Verified ${manifest.files.length} original sources, ${manifest.assets.length} binary assets, ${spans.length} non-overlapping content anchors.`);
