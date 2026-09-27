/* Photo-inspired Ar-Rahmah campus. Standalone browser IIFE, Three.js r149+.
 * Units are metres, ground y=0, facade faces +z. No external assets or loaders.
 * Parent owns lighting/camera/render loop. Animate each record's object.rotation.z
 * with Math.sin(time + phase) * amplitude; all pivots start at zero rotation.
 */
(function (scope) {
  'use strict';
  scope.createArrahmahCampus = function (THREE, options = {}) {
    const group = new THREE.Group();
    group.name = 'arrahmah-campus';
    const animated = [], ownedGeometry = new Set(), ownedMaterials = new Set(), textures = new Set();
    const batches = new Map(), geometries = new Map();
    let seed = 1907;
    const random = () => { seed = (1664525 * seed + 1013904223) >>> 0; return seed / 4294967296; };
    const mat = (color, extra) => {
      const m = new THREE.MeshStandardMaterial(Object.assign({ color, roughness: 0.84, metalness: 0 }, extra));
      ownedMaterials.add(m); return m;
    };
    const materials = {
      ivory: mat('#eee7d6'), white: mat('#faf3e4'), plaster: mat('#d4cfc0'), slate: mat('#283b48'),
      slateLight: mat('#354a54'), recess: mat('#526866'), foundation: mat('#6e7977'), stair: mat('#6c4430'),
      tread: mat('#a07959'), rail: mat('#a5b0ab', { metalness: 0.48, roughness: 0.46 }),
      orange: mat('#c66b43'), red: mat('#a04b36'), glass: mat('#294f53', { roughness: 0.34, metalness: 0.16, emissive: '#d99a4f', emissiveIntensity: 0.12 }),
      warmGlass: mat('#967555', { emissive: '#ffb968', emissiveIntensity: 0.3, roughness: 0.48 }),
      grass: mat('#657e43'), verge: mat('#8c9a59'), path: mat('#b6b29f'), soil: mat('#5c4634'),
      trunk: mat('#897456'), leaf: mat('#315b38'), leafLight: mat('#507b40'), palm: mat('#577547', { side: THREE.DoubleSide }),
      pot: mat('#262d2b'), flower: mat('#c32e76'), flowerLight: mat('#df548d'), lamp: mat('#303e3c'),
      light: mat('#ffe3aa', { emissive: '#ffcc7b', emissiveIntensity: 0.8 }), flag: mat('#d84239', { side: THREE.DoubleSide })
    };
    const geo = (key, make) => {
      if (!geometries.has(key)) { const g = make(); geometries.set(key, g); ownedGeometry.add(g); }
      return geometries.get(key);
    };
    const cube = geo('cube', () => new THREE.BoxGeometry(1, 1, 1));
    const ball = geo('ball', () => new THREE.IcosahedronGeometry(1, 1));
    const bud = geo('bud', () => new THREE.IcosahedronGeometry(1, 0));
    const cylinder = geo('cylinder', () => new THREE.CylinderGeometry(1, 1, 1, 8));
    const potGeo = geo('pot', () => new THREE.CylinderGeometry(0.5, 0.34, 1, 8));
    const matrix = new THREE.Matrix4(), position = new THREE.Vector3(), scale = new THREE.Vector3();
    const quaternion = new THREE.Quaternion(), euler = new THREE.Euler();
    const up = new THREE.Vector3(0, 1, 0);
    function part(name, parent = group, x = 0, y = 0, z = 0) {
      const p = new THREE.Group(); p.name = name; p.position.set(x, y, z); parent.add(p); return p;
    }
    function instance(parent, geometry, material, x, y, z, sx, sy, sz, rotation) {
      const key = `${parent.id}/${geometry.id}/${material.id}`;
      if (!batches.has(key)) batches.set(key, { parent, geometry, material, matrices: [] });
      position.set(x, y, z); scale.set(sx, sy, sz);
      if (rotation?.isQuaternion) quaternion.copy(rotation);
      else quaternion.setFromEuler(euler.set(rotation?.[0] || 0, rotation?.[1] || 0, rotation?.[2] || 0));
      matrix.compose(position, quaternion, scale);
      batches.get(key).matrices.push(matrix.clone());
    }
    const box = (p, m, x, y, z, w, h, d, r) => instance(p, cube, m, x, y, z, w, h, d, r);
    function beam(p, m, a, b, width = 0.08, depth = width) {
      const start = new THREE.Vector3(...a), end = new THREE.Vector3(...b), direction = end.clone().sub(start);
      const center = start.add(end).multiplyScalar(0.5);
      const q = new THREE.Quaternion().setFromUnitVectors(up, direction.clone().normalize());
      instance(p, cube, m, center.x, center.y, center.z, width, direction.length(), depth, q);
    }
    function mesh(p, g, m, name) {
      ownedGeometry.add(g); const o = new THREE.Mesh(g, m); o.name = name; p.add(o); return o;
    }
    // Chamfered octagonal roof rings preserve the broad angular Indonesian roof.
    function ring(w, d, y) {
      const c = Math.min(w, d) * 0.19;
      return [[-w/2+c,y,d/2],[w/2-c,y,d/2],[w/2,y,d/2-c],[w/2,y,-d/2+c],
        [w/2-c,y,-d/2],[-w/2+c,y,-d/2],[-w/2,y,-d/2+c],[-w/2,y,d/2-c]];
    }
    function roof(p, name, w, d, baseY, topW, topD, topY, material) {
      const low = ring(w,d,baseY), high = ring(topW,topD,topY), vertices = [];
      for (let i = 0; i < 8; i++) {
        const j = (i+1)%8;
        vertices.push(...low[i],...high[i],...low[j], ...low[j],...high[i],...high[j]);
      }
      const g = new THREE.BufferGeometry(); g.setAttribute('position', new THREE.Float32BufferAttribute(vertices,3)); g.computeVertexNormals();
      mesh(p,g,material,name);
      return { low, high };
    }
    const mosque = part('mosque', group, -7, 0, -3);
    box(mosque,materials.foundation,0,0.24,0,18.5,0.48,15.5);
    box(mosque,materials.white,0,1.22,0,18,1.96,15);
    box(mosque,materials.plaster,0,2.17,0,18.6,0.24,15.6);
    box(mosque,materials.ivory,0,4.65,-1.1,17.5,4.7,12.5);
    // Recessed porch, columns, glazed doors and two abstract geometric screens.
    box(mosque,materials.recess,0,4.25,5.22,7,4,0.16);
    for (const x of [-3.75,3.75]) {
      box(mosque,materials.white,x,4.6,6.5,0.72,4.9,1.5);
      box(mosque,materials.plaster,x,2.55,6.5,1,0.5,1.7);
      box(mosque,materials.ivory,x,6.9,6.5,1.05,0.35,1.9);
    }
    for (let i=0;i<5;i++) {
      const x=-2.65+i*1.32;
      box(mosque,materials.glass,x,4.05,5.37,1.14,3.5,0.14);
      box(mosque,materials.ivory,x-0.61,4.05,5.48,0.09,3.6,0.12);
      box(mosque,materials.rail,x+0.37,3.92,5.57,0.035,0.46,0.035);
    }
    for (const x of [-6.35,6.35]) {
      box(mosque,materials.recess,x,4.8,6.3,4.2,3.85,0.18);
      for (const side of [-1,1]) box(mosque,materials.white,x+side*2.17,4.8,6.48,0.14,4.05,0.2);
      for (const y of [2.85,6.75]) box(mosque,materials.white,x,y,6.48,4.48,0.14,0.2);
      // Interlocked diamonds and right-angle lattice: decoration, not writing.
      for (let row=0;row<3;row++) for(let col=0;col<3;col++) {
        const cx=x-1.35+col*1.35, cy=3.48+row*1.28;
        const pts=[[cx,cy+0.57,6.49],[cx+0.57,cy,6.49],[cx,cy-0.57,6.49],[cx-0.57,cy,6.49]];
        for(let k=0;k<4;k++) beam(mosque,materials.white,pts[k],pts[(k+1)%4],0.13,0.17);
        box(mosque,materials.ivory,cx,cy,6.51,0.28,0.28,0.18);
      }
    }
    box(mosque,materials.recess,0,6.35,6.95,7,1.1,0.1);
    for(let i=0;i<22;i++) for(let row=0;row<3;row++) {
      box(mosque,materials.ivory,-3.35+i*0.32,6.05+row*0.31,7.06,0.16,0.16,0.12,[0,0,Math.PI/4]);
    }
    for(const side of [-1,1]) for(let k=0;k<6;k++) {
      box(mosque,materials.recess,side*8.79,4.4,-5.4+k*1.8,0.12,2.6,0.65);
      box(mosque,materials.ivory,side*8.88,4.4,-5.4+k*1.8,0.16,2.65,0.09);
    }
    roof(mosque,'lower-cream-fascia',20.4,17,7.05,20.4,17,7.38,materials.ivory);
    roof(mosque,'lower-hip-roof',20.7,17.3,7.38,12,10,9.7,materials.slate);
    box(mosque,materials.recess,0,9.88,0,11.6,1.25,9.3);
    for(let x=-5.4;x<=5.4;x+=0.9) box(mosque,materials.ivory,x,9.98,4.71,0.08,0.9,0.12);
    roof(mosque,'upper-cream-fascia',14.4,12.3,10.35,14.4,12.3,10.7,materials.ivory);
    const upper=roof(mosque,'upper-polygonal-roof',14.7,12.6,10.7,0.04,0.04,15.1,materials.slate);
    for(let i=0;i<8;i++) beam(mosque,materials.slateLight,upper.low[i],upper.high[i],0.055);
    for(let k=1;k<5;k++) {
      const f=k/5, rim=ring(14.7*(1-f),12.6*(1-f),10.7+4.4*f+0.012);
      for(let i=0;i<8;i++) beam(mosque,materials.slateLight,rim[i],rim[(i+1)%8],0.022);
    }
    // Twin brown flights, central stepped planters, fine silver rails.
    for(let step=0;step<10;step++) {
      const z=14.55-step*0.68, h=(step+1)*0.22;
      for(const side of [-1,1]) {
        box(mosque,materials.stair,side*3.27,h/2,z,5.85,h,0.72);
        box(mosque,materials.tread,side*3.27,h+0.018,z+0.32,5.86,0.035,0.07);
      }
      if(step%2===0) {
        box(mosque,materials.ivory,0,h/2+0.18,z,0.62,h+0.36,0.68);
        instance(mosque,ball,materials.leaf,0,h+0.55,z,0.4,0.35,0.38);
      }
    }
    for(const x of [-6.22,-0.39,0.39,6.22]) {
      for(let i=0;i<6;i++) {const z=14.85-i*1.3,y=0.25+i*0.42;box(mosque,materials.rail,x,y+0.47,z,0.06,0.94,0.06);}
      beam(mosque,materials.rail,[x,1.18,14.85],[x,3.3,8.35],0.075);
    }
    const minaret=part('minaret',group,-22,0,-3);
    box(minaret,materials.foundation,0,0.6,0,3.9,1.2,3.9);
    box(minaret,materials.ivory,0,11.1,0,2.65,20.4,2.65);
    for(let face=0;face<4;face++) {
      const panel=part('tower-panel-'+face,minaret); panel.rotation.y=face*Math.PI/2;
      box(panel,materials.recess,0,11.2,1.337,1.82,18.8,0.035);
      for(const x of [-1.2,1.2]) box(panel,materials.plaster,x,11.15,1.4,0.18,20.3,0.14);
      for(let row=0;row<13;row++) {
        const y=2.2+row*1.43;
        box(panel,materials.ivory,0,y-0.57,1.42,1.87,0.09,0.08);
        const pts=[[0,y+0.53,1.42],[0.68,y,1.42],[0,y-0.53,1.42],[-0.68,y,1.42]];
        for(let k=0;k<4;k++) beam(panel,materials.ivory,pts[k],pts[(k+1)%4],0.09);
        box(panel,materials.plaster,0,y,1.44,0.27,0.27,0.1,[0,0,Math.PI/4]);
      }
    }
    box(minaret,materials.plaster,0,21.5,0,3.25,0.45,3.25);
    box(minaret,materials.white,0,22.3,0,3.4,1.2,3.4);
    for(const x of [-0.95,0,0.95]) box(minaret,materials.recess,x,22.35,1.72,0.25,0.67,0.06);
    const cap=geo('cap',()=>new THREE.ConeGeometry(2.65,2.2,4));
    instance(minaret,cap,materials.ivory,0,24,0,1,1,1,[0,Math.PI/4,0]);
    const school=part('school',group,18,0,-11);
    box(school,materials.foundation,0,0.28,0,25.5,0.56,10.5);
    box(school,materials.orange,0,8.2,0,25,15.8,10);
    // Four real balcony levels with recessed glazing, paired mullions and rails.
    for(let floor=0;floor<4;floor++) {
      const y=0.65+floor*3.75;
      box(school,materials.white,0,y,5.55,25.8,0.24,2.1);
      box(school,materials.white,0,y+0.22,-0.02,25.4,0.16,10.4);
      for(let bay=0;bay<8;bay++) {
        const x=-10.95+bay*3.13;
        box(school,materials.ivory,x,y+1.82,5.08,2.5,2.48,0.18);
        box(school,bay%5===floor?materials.warmGlass:materials.glass,x,y+1.82,5.2,2.28,2.24,0.13);
        for(const dx of [-0.39,0.39]) box(school,materials.ivory,x+dx,y+1.82,5.31,0.065,2.3,0.09);
        box(school,materials.ivory,x,y+1.65,5.31,2.3,0.06,0.09);
        box(school,materials.red,x-1.53,y+1.85,5.4,0.24,3.46,0.7);
        box(school,materials.white,x,y+3.12,5.45,2.66,0.15,0.65);
        // Ground floor still has a low open veranda railing.
        for(let rail=0;rail<7;rail++) box(school,materials.ivory,x-1.2+rail*0.4,y+0.7,6.56,0.045,1,0.045);
      }
      for(const h of [0.4,1.2]) box(school,materials.white,0,y+h,6.56,25.2,0.07,0.07);
      for(const side of [-1,1]) for(let bay=0;bay<3;bay++) {
        box(school,materials.ivory,side*12.55,y+1.83,-3.2+bay*3.15,0.16,2.48,2.32);
        box(school,materials.glass,side*12.66,y+1.83,-3.2+bay*3.15,0.1,2.2,2.05);
        box(school,materials.ivory,side*12.73,y+1.83,-3.2+bay*3.15,0.1,2.25,0.09);
      }
    }
    for(const x of [-12.25,12.25]) box(school,materials.red,x,8.1,5.6,0.6,16.1,1.35);
    box(school,materials.red,0,15.72,5.35,25.5,1.55,0.65);
    box(school,materials.white,0,16.62,0,25.7,0.24,10.7);
    box(school,materials.orange,0,17.02,-4.9,25,0.64,0.3);
    for(const x of [-12.35,12.35]) box(school,materials.orange,x,17.02,0,0.3,0.64,10);
    // Plain-text sign only; wraps long names without remote fonts or invented script.
    const signCanvas=document.createElement('canvas'); signCanvas.width=2048; signCanvas.height=256;
    const ctx=signCanvas.getContext('2d');
    if(ctx) {
      ctx.fillStyle='#a04b36'; ctx.fillRect(0,0,2048,256);
      const text=String(options.signText == null ? 'MODERN TAHFIDZ AR-RAHMAH BOARDING SCHOOL' : options.signText).trim();
      let fontSize=76, lines=[];
      function wrap(size) {
        ctx.font=`600 ${size}px sans-serif`; const result=[];
        for(const paragraph of text.split(/\n/)) {
          let line='';
          for(const word of paragraph.split(/\s+/)) {
            if(ctx.measureText(word).width>1900) {
              if(line) { result.push(line); line=''; }
              for(const letter of word) {
                if(ctx.measureText(line+letter).width>1900 && line) { result.push(line); line=''; }
                line+=letter;
              }
            } else if(line && ctx.measureText(line+' '+word).width>1900) { result.push(line); line=word; }
            else line+=(line?' ':'')+word;
          }
          if(line) result.push(line);
        }
        return result;
      }
      do {lines=wrap(fontSize); if(lines.length*fontSize*1.16<=220) break; fontSize*=0.88;} while(fontSize>1);
      ctx.fillStyle='#f5e6c8'; ctx.textAlign='center'; ctx.textBaseline='middle';
      lines.forEach((line,i)=>ctx.fillText(line,1024,128+(i-(lines.length-1)/2)*fontSize*1.16,1900));
      const texture=new THREE.CanvasTexture(signCanvas); texture.encoding=THREE.sRGBEncoding; textures.add(texture);
      const signMat=mat('#ffffff',{map:texture,roughness:0.9});
      const sign=mesh(school,new THREE.PlaneGeometry(23.7,1.28),signMat,'school-name-sign'); sign.position.set(0,15.72,5.69);
    }
    const landscape=part('landscape');
    box(landscape,materials.grass,0,-0.21,0,130,0.4,110);
    box(landscape,materials.path,0,0.012,14,79,0.045,3);
    box(landscape,materials.path,18,0.018,6,5.8,0.05,15);
    box(landscape,materials.path,-27,0.01,-1,4.2,0.04,33);
    for(const z of [12.45,15.58]) box(landscape,materials.ivory,0,0.08,z,79,0.16,0.13);
    for(let i=0;i<40;i++) box(landscape,materials.plaster,-38+i*1.95,0.041,14,0.035,0.012,2.96);
    // Low planted beds and a continuous photographed row of black flower pots.
    for(const x of [-18,8,28]) {
      box(landscape,materials.red,x,0.19,10.6,11,0.38,2.4);
      box(landscape,materials.soil,x,0.4,10.6,10.7,0.08,2.12);
      for(let i=0;i<16;i++) instance(landscape,ball,i%2?materials.leaf:materials.leafLight,x-5+i*0.67,0.73,10.6,0.49,0.5,0.6);
    }
    for(let i=0;i<53;i++) {
      const x=-35+i*1.34,z=16.25;
      instance(landscape,potGeo,materials.pot,x,0.3,z,0.7,0.6,0.7);
      instance(landscape,bud,materials.leaf,x,0.85,z,0.55,0.54,0.43);
      for(let j=0;j<10;j++) {
        const a=random()*Math.PI*2,r=0.4*Math.sqrt(random());
        instance(landscape,bud,j%3?materials.flower:materials.flowerLight,x+Math.cos(a)*r,1.06+random()*0.32,z+Math.sin(a)*r,0.13,0.115,0.13);
      }
    }
    // Sculptural multi-cloud topiary; crown pivots independently of the trunk.
    function tree(x,z,size,index) {
      const treeGroup=part('topiary-'+index,landscape,x,0,z);
      instance(treeGroup,cylinder,materials.trunk,0,size*0.27,0,size*0.07,size*0.54,size*0.065);
      const crown=part('topiary-crown-'+index,treeGroup,0,size*0.46,0);
      animated.push({object:crown,phase:index*1.7,amplitude:0.012});
      const lobes=[[0,0.38,0,0.28],[-0.22,0.17,0.02,0.18],[0.22,0.21,-0.01,0.19],[-0.07,0.02,0.1,0.14],[0.04,0.25,-0.19,0.19]];
      for(const [lx,ly,lz,r] of lobes) {
        beam(crown,materials.trunk,[0,-size*0.12,0],[lx*size,ly*size,lz*size],size*0.055);
        instance(crown,ball,materials.leaf,lx*size,ly*size,lz*size,r*size,r*size*0.56,r*size*0.86);
        for(let i=0;i<13;i++) {
          const a=random()*Math.PI*2, spread=r*size*0.68;
          instance(crown,bud,i%3?materials.leaf:materials.leafLight,lx*size+Math.cos(a)*spread,ly*size+random()*r*size*0.35,lz*size+Math.sin(a)*spread*0.8,r*size*0.38,r*size*0.3,r*size*0.38);
        }
      }
    }
    tree(9,6,14,0); tree(34,1,8,1); tree(-33,-10,9,2);
    // Feather palms: curved rachis plus tapered diamond leaflets, not cone trees.
    const leafGeo=geo('palm-leaflet',()=>{
      const g=new THREE.BufferGeometry();
      g.setAttribute('position',new THREE.Float32BufferAttribute([0,0,0, -0.13,0.44,0.045, 0,1,0, 0,0,0, 0,1,0, 0.13,0.44,0.045],3));g.computeVertexNormals();return g;
    });
    function palm(x,z,height,index) {
      instance(landscape,cylinder,materials.trunk,x,height/2,z,0.19,height,0.19);
      for(let i=1;i<height*3;i++) instance(landscape,cylinder,materials.stair,x,i/3,z,0.205,0.065,0.205);
      const crown=part('palm-crown-'+index,landscape,x,height,z);
      animated.push({object:crown,phase:0.6+index*1.43,amplitude:0.025});
      for(let frond=0;frond<9;frond++) {
        const a=frond*Math.PI*2/9, length=height*0.5;
        const point=t=>[Math.cos(a)*length*t, Math.sin(t*Math.PI)*0.78-t*t*0.55, Math.sin(a)*length*t];
        for(let s=0;s<6;s++) beam(crown,materials.leafLight,point(s/6),point((s+1)/6),0.035);
        for(let s=1;s<=9;s++) {
          const t=s/10,start=point(t),len=(1-t)*0.8+0.18;
          for(const side of [-1,1]) {
            const direction=new THREE.Vector3(Math.cos(a)*0.24+Math.cos(a+Math.PI/2)*side*len,-0.2,Math.sin(a)*0.24+Math.sin(a+Math.PI/2)*side*len);
            const q=new THREE.Quaternion().setFromUnitVectors(up,direction.clone().normalize());
            instance(crown,leafGeo,materials.palm,...start,1,direction.length(),1,q);
          }
        }
      }
    }
    palm(-17,5,8.2,0);palm(-25,3,6.8,1);palm(0,8,4.2,2);palm(28,5,5.4,3);
    for(let i=0;i<13;i++) {
      const x=34+i*1.3,z=-6+(i%3)*2.5;
      instance(landscape,ball,i%2?materials.leaf:materials.leafLight,x,1.3,z,1.25,1.5,1.1);
    }
    for(const x of [-28,-12,20,35]) {
      instance(landscape,cylinder,materials.lamp,x,2.25,12,0.065,4.5,0.065);
      box(landscape,materials.lamp,x+0.25,4.48,12,0.65,0.09,0.12);
      box(landscape,materials.light,x+0.5,4.41,12,0.32,0.06,0.24);
      box(landscape,materials.foundation,x,0.18,12,0.42,0.36,0.42);
    }
    // Indonesian red-over-white flag; cloth pivots gently without simulation.
    instance(landscape,cylinder,materials.rail,27,5,1,0.065,10,0.065);
    instance(landscape,bud,materials.light,27,10.05,1,0.11,0.11,0.11);
    const flag=part('flag-cloth',landscape,27,9.3,1);
    box(flag,materials.flag,1,0.35,0,2,0.7,0.045);box(flag,materials.white,1,-0.35,0,2,0.7,0.045);
    animated.push({object:flag,phase:2.2,amplitude:0.038});
    // Per-parent/material/geometry instancing preserves animated pivots and names.
    for(const b of batches.values()) {
      const object=new THREE.InstancedMesh(b.geometry,b.material,b.matrices.length);
      object.name=`${b.parent.name}-details-${b.material.color.getHexString()}`;
      b.matrices.forEach((m,i)=>object.setMatrixAt(i,m));object.instanceMatrix.needsUpdate=true;
      object.castShadow=options.shadows===true;object.receiveShadow=options.shadows===true;
      // r149 Box3 expands using the shared geometry, not instance transforms.
      // Per-object bounding spheres are computed by r149's renderer when needed.
      object.frustumCulled=false;
      b.parent.add(object);
    }
    group.updateMatrixWorld(true);
    let disposed=false;
    return {group,animated,materials,dispose() {
      if(disposed) return;disposed=true;
      ownedGeometry.forEach(g=>g.dispose());ownedMaterials.forEach(m=>m.dispose());textures.forEach(t=>t.dispose());
      group.traverse(o=>{if(o.isInstancedMesh && typeof o.dispose==='function') o.dispose();});
    }};
  };
})(typeof window !== 'undefined' ? window : globalThis);
