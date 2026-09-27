#!/usr/bin/env node
// Geometry/unit checks only: canvas is mocked; no WebGL rendering is performed.
import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import vm from 'node:vm';
import { fileURLToPath } from 'node:url';
const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const textCalls = [];
const canvas = () => ({ width: 1, height: 1, getContext: () => ({
  fillStyle: '', font: '', textAlign: '', textBaseline: '',
  fillRect() {}, clearRect() {}, measureText: text => ({ width: text.length * 24 }),
  fillText: (text, x, y, maxWidth) => textCalls.push({ text, x, y, maxWidth }),
}) });
const context = vm.createContext({ console, document: { createElement: tag => {
  assert.equal(tag, 'canvas'); return canvas();
} } });
context.window = context;
vm.runInContext(fs.readFileSync(path.join(root, 'public/landing-pages/secret-pathways-assets/three.min.js'), 'utf8'), context);
assert.equal(context.THREE.REVISION, '149', 'Check against the actual vendored Three r149 API');
const modelPath = path.join(root, 'resources/js/campus-model.js');
assert.ok(fs.existsSync(modelPath), 'The procedural campus implementation must exist');
vm.runInContext(fs.readFileSync(modelPath, 'utf8'), context);
assert.equal(typeof context.createArrahmahCampus, 'function');
const THREE = context.THREE;
const campus = context.createArrahmahCampus(THREE, { signText: 'MODERN TAHFIDZ AR-RAHMAH BOARDING SCHOOL — SEKOLAH ISLAM TERPADU' });
assert.ok(campus.group.isGroup);
for (const name of ['mosque', 'minaret', 'school', 'landscape']) {
  assert.ok(campus.group.getObjectByName(name)?.isGroup, `Missing named ${name} group`);
}
campus.group.updateMatrixWorld(true);
const bounds = {};
for (const name of ['mosque', 'minaret', 'school', 'landscape']) {
  const box = new THREE.Box3().setFromObject(campus.group.getObjectByName(name));
  bounds[name] = { min: box.min.toArray(), max: box.max.toArray() };
  assert.ok(box.min.toArray().concat(box.max.toArray()).every(Number.isFinite));
}
assert.ok(bounds.mosque.max[1] >= 14 && bounds.mosque.max[1] <= 16.5);
assert.ok(bounds.minaret.max[1] >= 24 && bounds.minaret.max[1] <= 26);
assert.ok(bounds.school.max[1] >= 16 && bounds.school.max[1] <= 20);
assert.ok(bounds.landscape.min[0] <= -65 && bounds.landscape.max[0] >= 65);
assert.ok(bounds.landscape.min[2] <= -55 && bounds.landscape.max[2] >= 55);
let drawCalls = 0, triangles = 0, instances = 0, meshes = 0;
const geometries = new Set(), materials = new Set(), textures = new Set();
campus.group.traverse(object => {
  if (!object.isMesh) return;
  meshes++;
  const g = object.geometry;
  const n = object.isInstancedMesh ? object.count : 1;
  instances += n;
  drawCalls += Array.isArray(object.material) ? g.groups.length : 1;
  triangles += (g.index ? g.index.count : g.attributes.position.count) / 3 * n;
  geometries.add(g);
  for (const m of Array.isArray(object.material) ? object.material : [object.material]) {
    materials.add(m); if (m.map) textures.add(m.map);
  }
  for (const value of g.attributes.position.array) assert.ok(Number.isFinite(value), 'Finite vertices');
  if (object.isInstancedMesh) for (const value of object.instanceMatrix.array) assert.ok(Number.isFinite(value), 'Finite transforms');
});
assert.ok(drawCalls <= 180, `Draw-call budget: ${drawCalls}`);
assert.ok(triangles <= 150000, `Triangle budget: ${triangles}`);
assert.ok(instances > 1000, 'Detailed repeated architectural and vegetation elements');
assert.ok(campus.animated.length >= 4);
for (const item of campus.animated) {
  assert.ok(item.object.isObject3D);
  assert.ok(Number.isFinite(item.phase) && item.amplitude > 0 && item.amplitude < 0.15);
}
assert.ok(textCalls.length >= 2, 'Long sign text wraps onto several lines');
assert.ok([...textures].some(t => t.isCanvasTexture));
assert.ok([...materials].some(m => m.emissiveIntensity > 0 && m.emissive?.getHex() > 0), 'Warm window emission');
const signature = instance => {
  const data = [];
  instance.group.traverse(o => { if (o.isMesh) data.push(o.name, Array.from(o.matrix.elements), o.isInstancedMesh ? Array.from(o.instanceMatrix.array) : []); });
  return JSON.stringify(data);
};
const second = context.createArrahmahCampus(THREE, { signText: 'SECOND CAMPUS' });
assert.equal(signature(campus), signature(second), 'Deterministic geometry across factory invocations');
let disposedGeometries = 0, disposedMaterials = 0, disposedTextures = 0;
for (const g of geometries) g.addEventListener('dispose', () => disposedGeometries++);
for (const m of materials) m.addEventListener('dispose', () => disposedMaterials++);
for (const t of textures) t.addEventListener('dispose', () => disposedTextures++);
assert.equal(typeof campus.dispose, 'function');
campus.dispose();
campus.dispose();
assert.equal(disposedGeometries, geometries.size, 'Dispose each geometry exactly once');
assert.equal(disposedMaterials, materials.size, 'Dispose each material exactly once');
assert.equal(disposedTextures, textures.size, 'Dispose each texture exactly once');
second.dispose();
console.log(JSON.stringify({ status: 'PASS', testType: 'geometry/unit only; canvas mock; NOT rendered/WebGL tests', threeRevision: THREE.REVISION, drawCalls, triangles, meshes, instances, geometries: geometries.size, materials: materials.size, animationGroups: campus.animated.length, bounds, signLines: textCalls.length }, null, 2));
