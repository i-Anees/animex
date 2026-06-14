/* ANIMEX WEAR — App shell: state, routing, cart + wishlist logic */
const { useState, useEffect, useRef } = React;

function AuthModal({ open, onClose }) {
  const [tab, setTab] = useState('login');
  if (!open) return null;
  return (
    <>
      <div className="scrim is-open" onClick={onClose}></div>
      <div style={{ position:'fixed', top:'50%', left:'50%', transform:'translate(-50%,-50%)',
        width:'min(420px,92vw)', background:'#fff', zIndex:110, padding:'40px 36px', boxShadow:'var(--sh-4)' }}>
        <div style={{ display:'flex', justifyContent:'space-between', alignItems:'center', marginBottom:24 }}>
          <Logo size={22} />
          <button className="iconbtn" onClick={onClose}><i className="bi bi-x-lg"></i></button>
        </div>
        <div className="auth-tabs">
          <button className={tab==='login'?'is-on':''} onClick={() => setTab('login')}>Sign In</button>
          <button className={tab==='reg'?'is-on':''} onClick={() => setTab('reg')}>Register</button>
        </div>
        {tab === 'reg' && <Field label="Name" placeholder="Your name" />}
        <Field label="Email" type="email" placeholder="you@email.com" />
        <Field label="Password" type="password" placeholder="••••••••" />
        {tab === 'login' && <div className="mono" style={{ fontSize:11, color:'var(--fg-4)', textAlign:'right', marginBottom:16, cursor:'pointer', textTransform:'uppercase', letterSpacing:'.06em' }}>Forgot password?</div>}
        <button className="btn btn--block" onClick={onClose}>{tab === 'login' ? 'Sign In' : 'Create Account'}</button>
      </div>
    </>
  );
}

function Confirmation({ go, order }) {
  useReveal();
  return (
    <div className="wrap reveal" style={{ paddingBlock:'clamp(80px,12vw,180px)', textAlign:'center', maxWidth:560 }}>
      <div style={{ width:64, height:64, borderRadius:'50%', background:'#000', color:'#fff', display:'flex', alignItems:'center', justifyContent:'center', margin:'0 auto 28px', fontSize:28 }}>
        <i className="bi bi-check-lg"></i>
      </div>
      <Overline>Order Confirmed</Overline>
      <h1 style={{ fontFamily:'var(--font-display)', fontWeight:800, fontSize:'clamp(32px,5vw,52px)', textTransform:'uppercase', letterSpacing:'-.03em', margin:'14px 0 16px' }}>Thank You</h1>
      <p className="ax-body" style={{ margin:'0 auto 8px', maxWidth:'42ch' }}>
        Your order <span className="mono" style={{ color:'#000' }}>#{order || '—'}</span> is confirmed. A receipt is on its way to your inbox, and tracking follows within 48 hours.
      </p>
      <div style={{ display:'flex', gap:12, justifyContent:'center', marginTop:32 }}>
        <button className="btn" onClick={() => go('home')}>Continue Shopping</button>
        <button className="btn btn--ghost" onClick={() => go('shop')}>Track Order</button>
      </div>
    </div>
  );
}

function App() {
  const [view, setView] = useState('home');
  const [current, setCurrent] = useState(null);   // current product for PDP
  const [cart, setCart] = useState(() => { try { return JSON.parse(localStorage.getItem('ax_cart')) || []; } catch (e) { return []; } });
  const [wish, setWish] = useState(() => { try { return JSON.parse(localStorage.getItem('ax_wish')) || []; } catch (e) { return []; } });
  useEffect(() => { try { localStorage.setItem('ax_cart', JSON.stringify(cart)); } catch (e) {} }, [cart]);
  useEffect(() => { try { localStorage.setItem('ax_wish', JSON.stringify(wish)); } catch (e) {} }, [wish]);
  const [drawer, setDrawer] = useState(false);
  const [search, setSearch] = useState(false);
  const [auth, setAuth] = useState(false);
  const [toast, setToast] = useState(null);
  const [orderNo, setOrderNo] = useState(null);
  const [shopPreset, setShopPreset] = useState(null);   // {collection|category|flag} for filtered Shop links

  const go = (v, preset = null) => { setShopPreset(preset); setView(v); window.scrollTo(0, 0); };
  const openProduct = (p) => { setCurrent(p); setView('product'); window.scrollTo(0, 0); };

  const flash = (msg) => { setToast(msg); clearTimeout(window.__t); window.__t = setTimeout(() => setToast(null), 2200); };

  const addToCart = (p, opts) => {
    const o = opts || { size: p.sizes.find(s => !p.soldOutSizes.includes(s)) || 'M', qty: 1, colorName: 'Black', color: '#000' };
    const key = p.id + '|' + o.size + '|' + o.colorName;
    setCart(c => {
      const ex = c.find(i => i.key === key);
      if (ex) return c.map(i => i.key === key ? { ...i, qty: i.qty + o.qty } : i);
      return [...c, { key, product: p, size: o.size, qty: o.qty, colorName: o.colorName, color: o.color }];
    });
    flash('Added to bag — ' + p.title);
    setDrawer(true);
  };

  const setQty = (item, d) => setCart(c => c.map(i => i.key === item.key ? { ...i, qty: Math.max(1, i.qty + d) } : i));
  const removeItem = (item) => setCart(c => c.filter(i => i.key !== item.key));

  const toggleWish = (p) => {
    setWish(w => {
      const has = w.find(x => x.id === p.id);
      if (has) { flash('Removed from wishlist'); return w.filter(x => x.id !== p.id); }
      flash('Saved to wishlist'); return [...w, p];
    });
  };
  const wished = (p) => !!wish.find(x => x.id === p.id);

  const handlers = {
    onOpen: openProduct,
    onAdd: (p) => addToCart(p),
    onWish: toggleWish,
    isWished: wished,
  };

  const cartCount = cart.reduce((s, i) => s + i.qty, 0);

  return (
    <div>
      <Announce />
      <Navbar view={view} go={go} cartCount={cartCount} wishCount={wish.length}
        onCart={() => setDrawer(true)} onSearch={() => setSearch(true)} onAccount={() => setAuth(true)} />

      <main>
        {view === 'home' && <Home go={go} handlers={handlers} />}
        {view === 'shop' && <Shop go={go} handlers={handlers} preset={shopPreset} />}
        {view === 'product' && current && <Product product={current} go={go}
          onAdd={(p, o) => addToCart(p, o)} onBuy={() => { setDrawer(false); go('checkout'); }}
          handlers={handlers} />}
        {view === 'wishlist' && <Wishlist wishItems={wish} go={go} handlers={handlers} />}
        {view === 'checkout' && <Checkout items={cart} go={go} onPlaced={(number) => { setCart([]); setOrderNo(number); go('confirmation'); }} />}
        {view === 'confirmation' && <Confirmation go={go} order={orderNo} />}
      </main>

      {view !== 'checkout' && <Footer go={go} />}

      <CartDrawer open={drawer} items={cart} onClose={() => setDrawer(false)}
        onQty={setQty} onRemove={removeItem} onCheckout={() => { setDrawer(false); go('checkout'); }} />
      <SearchOverlay open={search} onClose={() => setSearch(false)} onOpenProduct={openProduct} />
      <AuthModal open={auth} onClose={() => setAuth(false)} />
      <Toast msg={toast} on={!!toast} />
    </div>
  );
}

ReactDOM.createRoot(document.getElementById('root')).render(<App />);
