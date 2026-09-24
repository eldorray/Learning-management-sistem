<style id="school-photo-style">
    .school-photo #gl, .school-photo #fg-sky, .school-photo .fg,
    .school-photo #cursor, .school-photo #grain { display: none !important; }
    #school-background { position: fixed; inset: 0; z-index: 0; pointer-events: none; }
    #school-background img { width: 100%; height: 100%; object-fit: cover; object-position: {{ $position }}; }
    #school-background::after { content: ''; position: absolute; inset: 0; background: linear-gradient(90deg, rgba(3,6,9,.72), rgba(3,6,9,.18) 70%), linear-gradient(0deg, rgba(3,6,9,.70), transparent 65%); }
    .school-photo .word-fb { display: block; position: absolute; left: var(--pad); right: var(--pad); bottom: 22%; pointer-events: none; font-family: 'Wordmark','Onest',sans-serif; font-weight: 600; font-size: clamp(28px, 13vw, 190px); line-height: 1; text-align: center; overflow-wrap: anywhere; color: rgba(223,231,224,.85); }
    .school-photo .sec::before, .school-photo #pathways::before, .school-photo #eternity::before { background: none; }
    .school-photo .sec, .school-photo .foot { background: rgba(3,6,9,.42); }
    @media (max-width: 820px) { .school-photo .word-fb { bottom: 34%; } }
</style>
<div id="school-background" aria-hidden="true" data-wordmark="{{ $wordmark }}">
    <img src="{{ $image }}" alt="" fetchpriority="high">
</div>
