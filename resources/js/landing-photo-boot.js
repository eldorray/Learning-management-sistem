// Runs inside the authored document's closure, before any WebGL jobs.
function startSchoolPhoto() {
  const measurePhoto = () => {
    document.documentElement.style.setProperty('--vw', vpW() + 'px');
    measure();
  };
  $('.word-fb').textContent = $('#school-background').dataset.wordmark;
  wireReveals();
  measurePhoto();
  wireNav();
  wireHeroExit();
  addEventListener('resize', measurePhoto, { passive: true });
  new ResizeObserver(measurePhoto).observe(document.querySelector('.page'));
  if (document.fonts) document.fonts.ready.then(measurePhoto);
  document.body.classList.remove('is-locked');
  preEl.classList.add('done');
  prePct.textContent = '100';
  $('#hero').querySelectorAll('[data-rv], .mask-line').forEach(el => el.classList.add('rv-in'));
  window.__kage = window.__secret = { mode: 'photo', anchors: () => anchors };
}
