/* ANIMEX WEAR — chrome: Navbar, CartDrawer, SearchOverlay, Footer, Announce */
const { useState, useEffect, useRef } = React;

function Announce() {
  return (
    <div className="announce">
      Free express shipping over $200 &nbsp;·&nbsp; New drop every Friday 18:00 GMT
    </div>
  );
}

function Navbar({ view, go, cartCount, wishCount, onCart, onSearch, onAccount }) {
  const [scrolled, setScrolled] = useState(false);
  const [mega, setMega] = useState(false);
  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 24);
    window.addEventListener('scroll', onScroll);
    return () => window.removeEventListener('scroll', onScroll);
  }, []);
  // close the mega menu on any navigation so its panel never overlays the page
  useEffect(() => { setMega(false); }, [view]);
  const nav = (v) => { setMega(false); go(v); };
  return (
    <nav className={'nav' + (scrolled ? ' is-scrolled' : '')}
      onMouseLeave={() => setMega(false)}>
      <div className="nav__inner">
        <div className="nav__group">
          <div className="nav__links">
            <span className={'nav__link' + (view==='shop'?' is-active':'')}
              onMouseEnter={() => setMega(true)} onClick={() => nav('shop')}>Shop</span>
            <span className={'nav__link' + (view==='shop'?' is-active':'')} onClick={() => nav('shop')}>Collections</span>
            <span className={'nav__link' + (view==='shop'?' is-active':'')} onClick={() => nav('shop')}>New In</span>
            <span className={'nav__link' + (view==='shop'?' is-active':'')} onClick={() => nav('shop')}>Sale</span>
          </div>
        </div>
        <Logo onClick={() => nav('home')} />
        <div className="nav__group nav__group--right">
          <div className="nav__icons">
            <button className="iconbtn" onClick={onSearch} aria-label="Search"><i className="bi bi-search"></i></button>
            <button className="iconbtn" onClick={onAccount} aria-label="Account"><i className="bi bi-person"></i></button>
            <button className="iconbtn" onClick={() => nav('wishlist')} aria-label="Wishlist">
              <i className="bi bi-heart"></i>
              {wishCount > 0 && <span className="count">{wishCount}</span>}
            </button>
            <button className="iconbtn" onClick={onCart} aria-label="Cart">
              <i className="bi bi-bag"></i>
              {cartCount > 0 && <span className="count">{cartCount}</span>}
            </button>
            <button className="iconbtn nav__burger" aria-label="Menu"><i className="bi bi-list"></i></button>
          </div>
        </div>
      </div>
      <div className={'mega' + (mega ? ' is-open' : '')} onMouseEnter={() => setMega(true)}>
        <div className="mega__inner">
          <div className="mega__col">
            <h4>Series</h4>
            {COLLECTIONS.slice(0,4).map(c => <a key={c.id} onClick={() => nav('shop')}>{c.name}</a>)}
          </div>
          <div className="mega__col">
            <h4>&nbsp;</h4>
            {COLLECTIONS.slice(4).map(c => <a key={c.id} onClick={() => nav('shop')}>{c.name}</a>)}
          </div>
          <div className="mega__col">
            <h4>Category</h4>
            {CATEGORIES.slice(0,4).map(c => <a key={c} onClick={() => nav('shop')}>{c}</a>)}
          </div>
          <div className="mega__col">
            <h4>Featured</h4>
            <a onClick={() => nav('shop')}>New Arrivals</a>
            <a onClick={() => nav('shop')}>Best Sellers</a>
            <a onClick={() => nav('shop')}>Limited Drops</a>
            <a onClick={() => nav('shop')} style={{ color:'var(--fn-sale)' }}>Final Sale</a>
          </div>
          <div className="mega__feat" onClick={() => nav('shop')}>
            <span className="w">07</span>
            <Overline>Drop 014 — Live Now</Overline>
            <h3>Ninja Legacy<br/>Chapter Three</h3>
            <button className="btn-link" style={{ color:'#fff' }}>Shop the drop <i className="bi bi-arrow-right"></i></button>
          </div>
        </div>
      </div>
    </nav>
  );
}

function CartLine({ item, onQty, onRemove }) {
  const p = item.product;
  return (
    <div className="cart-line">
      <div className="cart-line__img">
        <img src={p.img} alt="" onError={(e) => { e.target.style.display='none'; e.target.nextSibling.style.display='block'; }}
          style={{ width:'100%', height:'100%', objectFit:'cover', filter:'saturate(1.08) contrast(1.03)' }} />
        <span className="w" style={{ display:'none' }}>AX</span>
      </div>
      <div className="cart-line__main">
        <div className="cart-line__over">{p.collectionName}</div>
        <div className="cart-line__title">{p.title}</div>
        <div className="cart-line__meta">{item.size} / {item.colorName} · {p.drop}</div>
        <div className="cart-line__row">
          <div className="stepper">
            <button onClick={() => onQty(item, -1)}><i className="bi bi-dash"></i></button>
            <span className="q">{String(item.qty).padStart(2,'0')}</span>
            <button onClick={() => onQty(item, 1)}><i className="bi bi-plus"></i></button>
          </div>
          <span className="cart-line__price">{money((p.sale || p.price) * item.qty)}</span>
        </div>
        <div style={{ marginTop:10 }}>
          <button className="cart-line__rm" onClick={() => onRemove(item)}>Remove</button>
        </div>
      </div>
    </div>
  );
}

function CartDrawer({ open, items, onClose, onQty, onRemove, onCheckout }) {
  const sub = items.reduce((s, i) => s + (i.product.sale || i.product.price) * i.qty, 0);
  const ship = sub > 200 || sub === 0 ? 0 : 12;
  const count = items.reduce((s, i) => s + i.qty, 0);
  return (
    <>
      <div className={'scrim' + (open ? ' is-open' : '')} onClick={onClose}></div>
      <aside className={'drawer' + (open ? ' is-open' : '')}>
        <div className="drawer__head">
          <h3>Bag — {String(count).padStart(2,'0')}</h3>
          <button className="iconbtn" onClick={onClose}><i className="bi bi-x-lg"></i></button>
        </div>
        <div className="drawer__body">
          {items.length === 0 ? (
            <div className="cart-empty">
              <i className="bi bi-bag"></i>
              <p>Your bag is empty</p>
              <button className="btn btn--sm" onClick={onClose}>Explore Drops</button>
            </div>
          ) : items.map((i) => <CartLine key={i.key} item={i} onQty={onQty} onRemove={onRemove} />)}
        </div>
        {items.length > 0 && (
          <div className="drawer__foot">
            <div className="cart-totals"><span>Subtotal</span><span>{money(sub)}</span></div>
            <div className="cart-totals"><span>Shipping</span><span>{ship === 0 ? 'Free' : money(ship)}</span></div>
            <div className="cart-totals grand"><span>Total</span><span>{money(sub + ship)}</span></div>
            <button className="btn btn--block" style={{ marginTop:18 }} onClick={onCheckout}>Checkout</button>
            <div className="cart-ship">Secure checkout · 30-day returns</div>
          </div>
        )}
      </aside>
    </>
  );
}

function SearchOverlay({ open, onClose, onOpenProduct }) {
  const [q, setQ] = useState('');
  const ref = useRef(null);
  useEffect(() => { if (open && ref.current) ref.current.focus(); }, [open]);
  const results = q.length > 0
    ? PRODUCTS.filter(p => (p.title + ' ' + p.collectionName + ' ' + p.category).toLowerCase().includes(q.toLowerCase())).slice(0, 6)
    : PRODUCTS.filter(p => p.isBest).slice(0, 5);
  return (
    <div className={'search-overlay' + (open ? ' is-open' : '')}>
      <button className="iconbtn" style={{ position:'absolute', top:24, right:'var(--gutter)' }} onClick={onClose}><i className="bi bi-x-lg"></i></button>
      <div className="search-overlay__inner">
        <input ref={ref} value={q} onChange={(e) => setQ(e.target.value)} placeholder="Search drops…" />
        <div className="search-sugg">
          <h4>{q ? results.length + ' results' : 'Trending now'}</h4>
          {results.map(p => (
            <div key={p.id} className="search-result" onClick={() => { onClose(); onOpenProduct(p); }}>
              <span className="w" style={{ overflow:'hidden', padding:0 }}>
                <img src={p.img} alt="" onError={(e) => { e.target.style.display='none'; }}
                  style={{ width:'100%', height:'100%', objectFit:'cover', filter:'saturate(1.08) contrast(1.03)' }} />
              </span>
              <span className="t">{p.title}</span>
              <span className="p mono">{money(p.sale || p.price)}</span>
            </div>
          ))}
          {q && results.length === 0 && <div className="mono" style={{ color:'var(--fg-4)', padding:'14px 0' }}>No matches. Try a series name.</div>}
        </div>
      </div>
    </div>
  );
}

function Footer({ go }) {
  return (
    <footer className="footer">
      <div className="footer__top">
        <div className="footer__brand">
          <Logo size={34} />
          <p>Anime, made into fashion. Numbered limited drops engineered in heavyweight cotton and cut for the street.</p>
          <div className="footer__social">
            <a><i className="bi bi-instagram"></i></a>
            <a><i className="bi bi-tiktok"></i></a>
            <a><i className="bi bi-twitter-x"></i></a>
            <a><i className="bi bi-discord"></i></a>
          </div>
        </div>
        <div className="footer__col">
          <h4>Shop</h4>
          <a onClick={() => go('shop')}>New Arrivals</a>
          <a onClick={() => go('shop')}>Best Sellers</a>
          <a onClick={() => go('shop')}>Collections</a>
          <a onClick={() => go('shop')}>Sale</a>
        </div>
        <div className="footer__col">
          <h4>Help</h4>
          <a>Shipping</a><a>Returns</a><a>Size Guide</a><a>Track Order</a><a>FAQ</a>
        </div>
        <div className="footer__col">
          <h4>Brand</h4>
          <a>About</a><a>Drops Calendar</a><a>Stockists</a><a>Contact</a>
        </div>
      </div>
      <div className="footer__bottom">
        <span>© 2026 ANIMEX WEAR</span>
        <span>Privacy · Terms · Cookies</span>
      </div>
    </footer>
  );
}

Object.assign(window, { Announce, Navbar, CartDrawer, SearchOverlay, Footer, CartLine });
