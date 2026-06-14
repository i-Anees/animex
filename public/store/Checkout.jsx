/* ANIMEX WEAR — Checkout + Wishlist
   Persists a real order; single AED-20 standard shipping; voucher validated
   against the backend; inline validation; AED currency. */
const { useState, useEffect, useRef } = React;

// Single shipping method.
const SHIP_OPTS = [
  { id: 'std', name: 'Standard', sub: '2–3 business days', cost: 20 },
];

function Field({ label, ...props }) {
  return (
    <div className="field">
      <label>{label}</label>
      <input {...props} />
    </div>
  );
}

function CardLogos() {
  return (
    <div style={{ display: 'flex', gap: 8, marginLeft: 'auto' }}>
      <svg width="36" height="24" viewBox="0 0 36 24" aria-label="Visa"><rect width="36" height="24" rx="3" fill="#fff" stroke="#e2e2e2"/><text x="18" y="16" textAnchor="middle" fontFamily="Arial" fontWeight="700" fontSize="10" fill="#1A1F71">VISA</text></svg>
      <svg width="36" height="24" viewBox="0 0 36 24" aria-label="Mastercard"><rect width="36" height="24" rx="3" fill="#fff" stroke="#e2e2e2"/><circle cx="15" cy="12" r="6.5" fill="#EB001B"/><circle cx="21" cy="12" r="6.5" fill="#F79E1B" fillOpacity="0.9"/></svg>
      <svg width="36" height="24" viewBox="0 0 36 24" aria-label="American Express"><rect width="36" height="24" rx="3" fill="#2E77BC"/><text x="18" y="15" textAnchor="middle" fontFamily="Arial" fontWeight="700" fontSize="7" fill="#fff">AMEX</text></svg>
      <svg width="36" height="24" viewBox="0 0 36 24" aria-label="Apple Pay"><rect width="36" height="24" rx="3" fill="#000"/><text x="18" y="15" textAnchor="middle" fontFamily="Arial" fontWeight="600" fontSize="7" fill="#fff">Pay</text></svg>
    </div>
  );
}

function Checkout({ items, go, onPlaced }) {
  const [coupon, setCoupon] = useState('');
  const [voucher, setVoucher] = useState(null);     // { code, discount, label }
  const [couponMsg, setCouponMsg] = useState('');
  const [placing, setPlacing] = useState(false);
  const [errMsg, setErrMsg] = useState('');

  const sub = items.reduce((s, i) => s + (i.product.sale || i.product.price) * i.qty, 0);
  const shipCost = SHIP_OPTS[0].cost; // flat AED 20 standard
  const discount = voucher ? voucher.discount : 0;
  const tax = Math.round((sub - discount) * 0.08);
  const total = sub - discount + shipCost + tax;

  const applyVoucher = async () => {
    setCouponMsg('');
    const code = coupon.trim();
    if (!code) return;
    try {
      const res = await fetch(window.__VOUCHER_URL__, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.__CSRF__, 'Accept': 'application/json' },
        body: JSON.stringify({ code, subtotal: sub }),
      });
      const data = await res.json();
      if (!res.ok || !data.valid) { setVoucher(null); setCouponMsg(data.message || 'Invalid voucher code.'); return; }
      setVoucher({ code: data.code, discount: data.discount, label: data.label });
      setCouponMsg('Applied — ' + data.label);
    } catch (e) {
      setVoucher(null);
      setCouponMsg('Could not validate that voucher.');
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (placing || items.length === 0) return;
    setErrMsg('');
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
      coupon: voucher ? voucher.code : null,
      items: items.map(i => ({ id: i.product._id, size: i.size, color: i.colorName, qty: i.qty })),
    };
    try {
      const res = await fetch(window.__ORDER_URL__, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.__CSRF__, 'Accept': 'application/json' },
        body: JSON.stringify(payload),
      });
      const data = await res.json();
      if (!res.ok) {
        // surface the first validation error
        let msg = data.message || 'Please check your details and try again.';
        if (data.errors) { const first = Object.values(data.errors)[0]; if (first && first[0]) msg = first[0]; }
        throw new Error(msg);
      }
      onPlaced(data.number);
    } catch (err) {
      setErrMsg(err.message || 'Something went wrong placing your order.');
      setPlacing(false);
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  };

  return (
    <div className="checkout">
      <form className="checkout__form" onSubmit={handleSubmit} noValidate>
        <h1>Checkout</h1>
        <div className="mono" style={{ color:'var(--fg-4)', fontSize:12, letterSpacing:'.06em' }}>
          <span onClick={() => go('home')} style={{ cursor:'pointer' }}>Bag</span> / Information / Payment
        </div>

        {errMsg && (
          <div style={{ margin:'18px 0 0', padding:'12px 16px', background:'#fdecec', border:'1px solid var(--fn-sale,#B11919)', color:'var(--fn-sale,#B11919)', fontSize:14 }}>
            {errMsg}
          </div>
        )}

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
          <Field label="Address" name="address" placeholder="221 Sheikh Zayed Road" required />
          <div className="field-row">
            <Field label="City" name="city" placeholder="Dubai" required />
            <Field label="Postal code (optional)" name="postcode" placeholder="00000" />
          </div>
          <Field label="Country" name="country" placeholder="United Arab Emirates" defaultValue="United Arab Emirates" required />
        </div>

        <div className="co-step">
          <div className="co-step__head"><span className="co-step__n">3</span><h3>Shipping Method</h3></div>
          <div className="ship-opt is-on">
            <span className="ship-radio"></span>
            <div>
              <div className="name">{SHIP_OPTS[0].name}</div>
              <div className="sub">{SHIP_OPTS[0].sub}</div>
            </div>
            <span className="cost">{money(SHIP_OPTS[0].cost)}</span>
          </div>
        </div>

        <div className="co-step">
          <div className="co-step__head"><span className="co-step__n">4</span><h3>Payment</h3><CardLogos /></div>
          <Field label="Card number" name="card" placeholder="0000 0000 0000 0000" inputMode="numeric" />
          <div className="field-row">
            <Field label="Expiry" name="exp" placeholder="MM / YY" />
            <Field label="CVC" name="cvc" placeholder="000" inputMode="numeric" />
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
          <input value={coupon} onChange={(e) => setCoupon(e.target.value)} placeholder="Voucher code" />
          <button type="button" onClick={applyVoucher}>Apply</button>
        </div>
        {couponMsg && <div className="mono" style={{ fontSize:11, letterSpacing:'.04em', color: voucher ? 'var(--fn-success)' : 'var(--fn-sale)', margin:'4px 0 8px' }}>{couponMsg}</div>}
        {voucher && <div className="cart-totals" style={{ color:'var(--fn-success)' }}><span>Discount ({voucher.label})</span><span>−{money(discount)}</span></div>}
        <div className="cart-totals"><span>Subtotal</span><span>{money(sub)}</span></div>
        <div className="cart-totals"><span>Shipping</span><span>{money(shipCost)}</span></div>
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
