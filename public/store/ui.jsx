/* ANIMEX WEAR — shared UI primitives */
const { useState, useEffect, useRef } = React;

function Logo({ size, onClick }) {
  return (
    <div className="logo" style={size ? { fontSize: size } : null} onClick={onClick}>
      ANIMEX<span className="lite"> WEAR</span>
    </div>
  );
}

function Overline({ children, style }) {
  return <div className="over" style={style}>{children}</div>;
}

function Badge({ kind, children }) {
  return <span className={'badge' + (kind ? ' badge--' + kind : '')}>{children}</span>;
}

function Stars({ value, size }) {
  const full = Math.round(value);
  return (
    <span className="stars" style={size ? { fontSize: size } : null}>
      {'★★★★★'.slice(0, full)}<span style={{ color: 'var(--ink-200)' }}>{'★★★★★'.slice(full)}</span>
    </span>
  );
}

// Media — real color photo with graceful fallback to AX panel.
function Media({ tone, src, label, fontSize }) {
  const [failed, setFailed] = useState(false);
  const isDark = tone === '#000000' || tone === '#2D2D2D' || tone === '#1f1f1f' || tone === '#111111';
  const wm = isDark ? 'rgba(255,255,255,0.07)' : 'rgba(0,0,0,0.06)';
  return (
    <div style={{ position:'absolute', inset:0, background:tone, display:'flex',
      alignItems:'center', justifyContent:'center', overflow:'hidden' }}>
      {src && !failed && (
        <img src={src} loading="lazy" alt="" onError={() => setFailed(true)}
          style={{ position:'absolute', inset:0, width:'100%', height:'100%',
            objectFit:'cover', filter:'saturate(1.08) contrast(1.03)' }} />
      )}
      {(!src || failed) && (
        <span style={{ fontFamily:'var(--font-display)', fontWeight:800,
          fontSize: fontSize || 'clamp(40px,7vw,72px)', letterSpacing:'-.04em',
          color: wm, lineHeight:1, userSelect:'none' }}>{label || 'AX'}</span>
      )}
    </div>
  );
}

function Price({ p, size }) {
  if (p.sale) {
    return (
      <span className="pcard__price" style={size ? { fontSize:size } : null}>
        <span className="was">{money(p.price)}</span>
        <span className="now">{money(p.sale)}</span>
      </span>
    );
  }
  return <span className="pcard__price" style={size ? { fontSize:size } : null}>{money(p.price)}</span>;
}

function ProductCard({ p, onOpen, onAdd, onWish, wished, isWished }) {
  const on = isWished ? isWished(p) : wished;
  return (
    <div className="pcard" onClick={() => onOpen && onOpen(p)}>
      <div className="pcard__media">
        <div className="pcard__badges">
          {p.isNew && <Badge>New</Badge>}
          {p.sale && <Badge kind="sale">−30%</Badge>}
          {p.isLimited && <Badge kind="line">Limited</Badge>}
        </div>
        <button className={'pcard__wish' + (on ? ' is-on' : '')}
          onClick={(e) => { e.stopPropagation(); onWish && onWish(p); }}>
          <i className={'bi ' + (on ? 'bi-heart-fill' : 'bi-heart')}></i>
        </button>
        <div className="ph ph--a"><Media tone={p.tones[0]} src={p.img} /></div>
        <div className="ph ph--b"><Media tone={p.tones[1]} src={p.imgHover} /></div>
        {p.stock === 0
          ? <button className="pcard__add" disabled style={{ background:'var(--ink-300)' }}>Sold Out</button>
          : <button className="pcard__add" onClick={(e) => { e.stopPropagation(); onAdd && onAdd(p); }}>+ Quick Add</button>}
      </div>
      <div className="pcard__info">
        <div className="pcard__over">{p.collectionName}</div>
        <div className="pcard__title">{p.title}</div>
        <Price p={p} />
      </div>
    </div>
  );
}

// Toast notification
function Toast({ msg, on }) {
  return (
    <div className={'toast' + (on ? ' is-on' : '')}>
      <i className="bi bi-check-lg"></i>{msg}
    </div>
  );
}

// scroll reveal hook
function useReveal() {
  useEffect(() => {
    const els = document.querySelectorAll('.reveal:not(.is-in)');
    const io = new IntersectionObserver((entries) => {
      entries.forEach((e) => { if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); } });
    }, { threshold: 0.12 });
    els.forEach((el) => io.observe(el));
    return () => io.disconnect();
  });
}

Object.assign(window, { Logo, Overline, Badge, Stars, Media, Price, ProductCard, Toast, useReveal });
