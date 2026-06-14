/* ANIMEX WEAR — Checkout + Wishlist
   Visually identical to the prototype; "Place Order" now persists a real
   order to the Laravel/MySQL backend and forwards the real order number. */
const { useState, useEffect, useRef } = React;

const SHIP_OPTS = [
  { id: 'std', name: 'Standard', sub: '5–7 business days', cost: 0 },
  { id: 'exp', name: 'Express', sub: '2–3 business days', cost: 12 },
  { id: 'next', name: 'Next Day', sub: 'Order before 14:00', cost: 22 },
];

function Field({ label, ...props }) {
  return (
    <div className="field">
      <label>{label}</label>
      <input {...props} />
    </div>
  );
}

function Checkout({ items, go, onPlaced }) {
  const [ship, setShip] = useState('exp');
  const [coupon, setCoupon] = useState('');
  const [applied, setApplied] = useState(false);
  const [placing, setPlacing] = useState(false);
  const sub = items.reduce((s, i) => s + (i.product.sale || i.product.price) * i.qty, 0);
  const shipCost = sub > 200 ? 0 : SHIP_OPTS.find(o => o.id === ship).cost;
  const discount = applied ? Math.round(sub * 0.1) : 0;
  const tax = Math.round((sub - discount) * 0.08);
  const total = sub - discount + shipCost + tax;

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (placing || items.length === 0) return;
    setPlacing(true);
    const fd = new FormData(e.target);
    const payload = {
      email: fd.get('email'),
      first_name: fd.get('first_name'),
      last_name: fd.get('last_name'),
      address: fd.get('address'),
      city: fd.get('city'),
      postcode: fd.get('postcode'),
      country: fd.get('country'),
      ship,
      coupon: applied ? (coupon || 'PROMO') : null,
      items: items.map(i => ({ id: i.product._id, size: i.size, color: i.colorName, qty: i.qty })),
    };
    try {
      const res = await fetch(window.__ORDER_URL__, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.__CSRF__, 'Accept': 'application/json' },
        body: JSON.stringify(payload),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'We could not place your order.');
      onPlaced(data.number);
    } catch (err) {
      alert(err.message || 'Something went wrong placing your order.');
      setPlacing(false);
    }
  };

  return (
    <div className="checkout">
      <form className="checkout__form" onSubmit={handleSubmit}>
        <h1>Checkout</h1>
        <div className="mono" style={{ color:'var(--fg-4)', fontSize:12, letterSpacing:'.06em' }}>
          <span onClick={() => go('home')} style={{ cursor:'pointer' }}>Bag</span> / Information / Payment
        </div>

        <div className="co-step">
          <div className="co-step__head"><span className="co-step__n">1</span><h3>Contact</h3></div>
          <Field label="Email" type="email" name="email" placeholder="you@email.com" required />
        </div>

        <div className="co-step">
          <div className="co-step__head"><span className="co-step__n">2</span><h3>Shipping Address</h3></div>
          <div className="field-row">
            <Field label="First name" name="first_name" placeholder="Kenji" required />
            <Field label="Last name" name="last_name" placeholder="Tanaka" required />
          </div>
          <Field label="Address" name="address" placeholder="221 Shibuya Crossing" required />
          <div className="field-row">
            <Field label="City" name="city" placeholder="Tokyo" required />
            <Field label="Postal code" name="postcode" placeholder="150-0002" required />
          </div>
          <Field label="Country" name="country" placeholder="Japan" required />
        </div>

        <div className="co-step">
          <div className="co-step__head"><span className="co-step__n">3</span><h3>Shipping Method</h3></div>
          {SHIP_OPTS.map(o => (
            <div key={o.id} className={'ship-opt' + (ship === o.id ? ' is-on' : '')} onClick={() => setShip(o.id)}>
              <span className="ship-radio"></span>
              <div>
                <div className="name">{o.name}</div>
                <div className="sub">{o.sub}</div>
              </div>
              <span className="cost">{(sub > 200 || o.cost === 0) ? 'Free' : money(o.cost)}</span>
            </div>
          ))}
        </div>

        <div className="co-step">
          <div className="co-step__head"><span className="co-step__n">4</span><h3>Payment</h3></div>
          <Field label="Card number" name="card" placeholder="0000 0000 0000 0000" />
          <div className="field-row">
            <Field label="Expiry" name="exp" placeholder="MM / YY" />
            <Field label="CVC" name="cvc" placeholder="000" />
          </div>
        </div>

        <button className="btn btn--block btn--lg" style={{ marginTop:24 }} type="submit" disabled={placing}>
          {placing ? 'Placing…' : 'Place Order — ' + money(total)}
        </button>
        <div className="cart-ship" style={{ textAlign:'left', marginTop:14 }}>
          <i className="bi bi-lock"></i> Secured by 256-bit SSL encryption
        </div>
      </form>

      <aside className="checkout__summary">
        <h3 style={{ fontFamily:'var(--font-display)', fontWeight:800, fontSize:16, textTransform:'uppercase', letterSpacing:'.02em', margin:'0 0 8px' }}>Order Summary</h3>
        {items.map(i => (
          <div key={i.key} className="osum-line">
            <div className="osum-line__img">
              <img src={i.product.img} alt="" onError={(e) => { e.target.style.display='none'; }}
                style={{ width:'100%', height:'100%', objectFit:'cover', filter:'saturate(1.08) contrast(1.03)' }} />
              <span className="q">{i.qty}</span>
            </div>
            <div className="osum-line__main">
              <div className="t">{i.product.title}</div>
              <div className="m">{i.size} / {i.colorName} · {i.product.collectionName}</div>
            </div>
            <span className="osum-line__price">{money((i.product.sale || i.product.price) * i.qty)}</span>
          </div>
        ))}
        <div className="coupon">
          <input value={coupon} onChange={(e) => setCoupon(e.target.value)} placeholder="Promo code" />
          <button onClick={() => setApplied(coupon.trim().length > 0)}>Apply</button>
        </div>
        {applied && <div className="cart-totals" style={{ color:'var(--fn-success)' }}><span>Promo applied — 10%</span><span>−{money(discount)}</span></div>}
        <div className="cart-totals"><span>Subtotal</span><span>{money(sub)}</span></div>
        <div className="cart-totals"><span>Shipping</span><span>{shipCost === 0 ? 'Free' : money(shipCost)}</span></div>
        <div className="cart-totals"><span>Tax (est.)</span><span>{money(tax)}</span></div>
        <div className="cart-totals grand"><span>Total</span><span>{money(total)}</span></div>
      </aside>
    </div>
  );
}

function Wishlist({ wishItems, go, handlers }) {
  useReveal();
  return (
    <div>
      <div className="pagehead">
        <div className="crumbs">Home / Account / Wishlist</div>
        <h1>Wishlist</h1>
        <p>Saved pieces. Add to bag before the drop sells out — nothing here is guaranteed to restock.</p>
      </div>
      <div className="wrap" style={{ paddingBlock:'40px 120px' }}>
        {wishItems.length === 0 ? (
          <div style={{ textAlign:'center', padding:'80px 0' }}>
            <i className="bi bi-heart" style={{ fontSize:40, color:'var(--ink-200)' }}></i>
            <p className="mono" style={{ color:'var(--fg-4)', letterSpacing:'.1em', textTransform:'uppercase', margin:'18px 0 24px' }}>Your wishlist is empty</p>
            <button className="btn" onClick={() => go('shop')}>Explore Drops</button>
          </div>
        ) : (
          <div className="pgrid">
            {wishItems.map(p => <ProductCard key={p.id} p={p} {...handlers} />)}
          </div>
        )}
      </div>
    </div>
  );
}

Object.assign(window, { Checkout, Wishlist, Field, SHIP_OPTS });
