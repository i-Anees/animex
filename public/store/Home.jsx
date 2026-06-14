/* ANIMEX WEAR — Homepage */
const { useState, useEffect, useRef } = React;

const HERO_DEFAULT = [
  { over: 'Drop 014 — Live Now', title: 'Ninja Legacy', sub: 'Chapter Three. Heavyweight cotton, numbered to 300. The hidden village, reissued for the street.', tone: '#06121c', img: '1492288991661-058aa541ff43', accent: '#2BE2FF', accent2: '#1E6BFF' },
  { over: 'New Series', title: 'Saiyan Energy', sub: 'Beyond limits. Garment-dyed heavyweights and technical outerwear engineered to outlast the hype.', tone: '#1c1405', img: '1490114538077-0a7f8cb49891', accent: '#FFC633', accent2: '#FF7A1A' },
  { over: 'Final Sale', title: 'Dark Curse', sub: 'The cursed archive — last pieces, up to 30% off. When it’s gone, it does not return.', tone: '#14041a', img: '1503341504253-dff4815485f1', accent: '#C95BFF', accent2: '#FF2E88' },
];
// Hero content is managed from the admin (hero_slides) and injected; fall back to defaults.
const HERO_SLIDES = (window.__ANIMEX_DATA__ && window.__ANIMEX_DATA__.hero && window.__ANIMEX_DATA__.hero.length)
  ? window.__ANIMEX_DATA__.hero : HERO_DEFAULT;

function hexToRgb(h){ const n = parseInt(h.slice(1),16); return [(n>>16)&255,(n>>8)&255,n&255]; }

function Hero({ go }) {
  const s = HERO_SLIDES[0];
  return (
    <section className="hero hero--cinematic" style={{ '--accent': s.accent, '--accent2': s.accent2 }}>
      <div className="hero__slide is-active">
        <div className="hero__media" style={{ background: s.tone }}>
          <img src={s.imgUrl || U(s.img, 1600)} alt="" onError={(e) => { e.target.style.display='none'; }}
            style={{ position:'absolute', inset:0, width:'100%', height:'100%', objectFit:'cover', filter:'saturate(1.15) contrast(1.06)', opacity:.9 }} />
        </div>
        <div className="hero__duotone" style={{ background:'linear-gradient(125deg, '+s.accent2+'2e, transparent 45%, '+s.accent+'2e)' }}></div>
        <div className="hero__scrim"></div>
      </div>
      <div className="hero__aura"></div>
      <div className="hero__aura hero__aura--2"></div>
      <div className="hero__grid"></div>
      <div className="hero__sweep"></div>
      <div className="hero__vignette"></div>
      <div className="hero__content is-active">
        <div className="hero__kicker"><span className="hero__pulse"></span><Overline>{s.over}</Overline></div>
        <h1 className="hero__title">{s.title}</h1>
        <p className="hero__sub">{s.sub}</p>
        <div className="hero__cta">
          <button className="btn btn--energy btn--lg" onClick={() => go('shop', { flag: 'new' })}>New Arrivals</button>
          <button className="btn btn--ghost btn--lg" style={{ color:'#fff', borderColor:'rgba(255,255,255,.4)' }} onClick={() => go('shop', { flag: 'best' })}>Best Sellers</button>
        </div>
      </div>
    </section>
  );
}

function Marquee() {
  const items = ['Standard Shipping AED 20','New Drop Every Friday','Numbered Limited Editions','320GSM Heavyweight','UAE-Wide Delivery','30-Day Returns'];
  const loop = [...items, ...items];
  return (
    <div className="marquee">
      <div className="marquee__track">
        {loop.map((t, i) => <span key={i}>{t}<i className="bi bi-asterisk"></i></span>)}
      </div>
    </div>
  );
}

function Collections({ go }) {
  return (
    <section className="section wrap reveal">
      <div className="sec-head">
        <div className="sec-head__intro">
          <Overline>Trending Series</Overline>
          <h2>Shop by Universe</h2>
        </div>
        <button className="btn-link" onClick={() => go('shop')}>All collections <i className="bi bi-arrow-right"></i></button>
      </div>
      <div className="coll-grid">
        {COLLECTIONS.map(c => (
          <div key={c.id} className="coll" style={{ background: c.tone, '--accent': c.accent }} onClick={() => go('shop', { collection: c.id })}>
            <img className="coll__img" src={U(c.img, 700)} alt="" onError={(e) => { e.target.style.display='none'; }}
              style={{ position:'absolute', inset:0, width:'100%', height:'100%', objectFit:'cover', filter:'saturate(1.1) contrast(1.05)', opacity:.86 }} />
            <div className="coll__glow"></div>
            <div className="coll__scrim"></div>
            <div className="coll__label">
              <Overline>{c.tag}</Overline>
              <h3>{c.name}</h3>
              <div className="arr"><i className="bi bi-arrow-right"></i></div>
            </div>
          </div>
        ))}
      </div>
    </section>
  );
}

function ProductRow({ title, over, products, go, cta, preset, ...handlers }) {
  return (
    <section className="section--tight wrap reveal">
      <div className="sec-head">
        <div className="sec-head__intro">
          <Overline>{over}</Overline>
          <h2>{title}</h2>
        </div>
        <button className="btn-link" onClick={() => go('shop', preset || null)}>{cta || 'View all'} <i className="bi bi-arrow-right"></i></button>
      </div>
      <div className="pgrid">
        {products.map(p => <ProductCard key={p.id} p={p} {...handlers} />)}
      </div>
    </section>
  );
}

function FlashSale({ go }) {
  const [t, setT] = useState({ h: 11, m: 42, s: 8 });
  useEffect(() => {
    const iv = setInterval(() => setT(({ h, m, s }) => {
      let ts = h * 3600 + m * 60 + s - 1; if (ts < 0) ts = 11 * 3600;
      return { h: Math.floor(ts / 3600), m: Math.floor((ts % 3600) / 60), s: ts % 60 };
    }), 1000);
    return () => clearInterval(iv);
  }, []);
  const pad = n => String(n).padStart(2, '0');
  return (
    <section className="flash reveal">
      <div className="flash__inner">
        <div className="flash__left">
          <div>
            <Overline style={{ color:'var(--fn-sale)' }}>Flash Drop · 24H Only</Overline>
            <h3 style={{ marginTop:8 }}>Up to 30% Off Final Sale</h3>
          </div>
          <div className="timer">
            {[['Hrs',pad(t.h)],['Min',pad(t.m)],['Sec',pad(t.s)]].map(([l,v]) => (
              <div key={l} className="timer__u"><div className="n">{v}</div><div className="l">{l}</div></div>
            ))}
          </div>
        </div>
        <button className="btn" onClick={() => go('shop', { flag: 'sale' })}>Shop Sale</button>
      </div>
    </section>
  );
}

function FeaturedBanner({ go }) {
  return (
    <section className="banner reveal">
      <img src={U('1441986300917-64674bd600d8', 1600)} alt="" onError={(e) => { e.target.style.display='none'; }}
        style={{ position:'absolute', inset:0, width:'100%', height:'100%', objectFit:'cover', filter:'saturate(1.1) contrast(1.04)', opacity:.62 }} />
      <div className="banner__inner">
        <Overline>Featured Collection</Overline>
        <h2>The Survey Corps Outerwear Capsule</h2>
        <p>Technical bombers and trenches engineered for the wall. Water-repellent shell, tonal wings-of-freedom embroidery, numbered to 200.</p>
        <button className="btn btn--inverse btn--lg" onClick={() => go('shop')}>Explore the Capsule</button>
      </div>
    </section>
  );
}

const REVIEWS = [
  { stars: 5, quote: 'The heavyweight hoodie is the best-constructed piece in my rotation. Fit is exactly as described.', who: 'Kenji R. — Verified Buyer' },
  { stars: 5, quote: 'Packaging alone feels like a luxury house. The numbered label is a beautiful touch.', who: 'Amara L. — Verified Buyer' },
  { stars: 4, quote: 'Sold out fast but the restock notify worked. Quality justifies the price point.', who: 'Diego M. — Verified Buyer' },
];

function Testimonials() {
  return (
    <section className="section wrap reveal">
      <div className="sec-head">
        <div className="sec-head__intro">
          <Overline>Reviewed</Overline>
          <h2>Worn & Rated</h2>
        </div>
        <div className="mono" style={{ color:'var(--fg-3)', fontSize:13 }}>4.8 / 5 · 6,400+ reviews</div>
      </div>
      <div className="testi">
        {REVIEWS.map((r, i) => (
          <div key={i} className="testi__c">
            <div className="testi__stars"><Stars value={r.stars} /></div>
            <p>“{r.quote}”</p>
            <div className="testi__who">{r.who}</div>
          </div>
        ))}
      </div>
    </section>
  );
}

function Newsletter() {
  const [done, setDone] = useState(false);
  return (
    <section className="news reveal">
      <div className="news__inner">
        <Overline>The List</Overline>
        <h2>Early Access to Every Drop</h2>
        <p>Join the list for first access, restock alerts, and members-only releases. No spam — just drops.</p>
        {done ? (
          <div className="mono" style={{ color:'#fff', letterSpacing:'.1em', textTransform:'uppercase' }}>✓ You’re on the list</div>
        ) : (
          <form className="news__form" onSubmit={(e) => { e.preventDefault(); setDone(true); }}>
            <input type="email" placeholder="your@email.com" required />
            <button type="submit">Join</button>
          </form>
        )}
      </div>
    </section>
  );
}

function Home({ go, handlers }) {
  useReveal();
  const featured = PRODUCTS.slice(0, 8);
  const arrivals = PRODUCTS.filter(p => p.isNew).slice(0, 4);
  const best = PRODUCTS.filter(p => p.isBest).slice(0, 4);
  const limited = PRODUCTS.filter(p => p.isLimited || p.sale).slice(0, 4);
  return (
    <div>
      <Hero go={go} />
      <Marquee />
      <Collections go={go} />
      <ProductRow over="Curated" title="Featured Products" products={featured} go={go} {...handlers} />
      <FlashSale go={go} />
      <ProductRow over="Just Landed" title="New Arrivals" products={arrivals.length ? arrivals : featured.slice(0,4)} go={go} cta="Shop new" preset={{ flag: 'new' }} {...handlers} />
      <FeaturedBanner go={go} />
      <ProductRow over="Most Wanted" title="Best Sellers" products={best.length ? best : featured.slice(4,8)} go={go} cta="Shop best" preset={{ flag: 'best' }} {...handlers} />
      <ProductRow over="Last Chance" title="Limited Edition Drops" products={limited} go={go} cta="Shop sale" preset={{ flag: 'sale' }} {...handlers} />
      <Testimonials />
      <Newsletter />
    </div>
  );
}

Object.assign(window, { Home, Hero, Marquee, Collections, ProductRow, FlashSale, FeaturedBanner, Testimonials, Newsletter });
