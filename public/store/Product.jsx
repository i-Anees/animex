/* ANIMEX WEAR — Product detail (PDP) */
const { useState, useEffect, useRef } = React;

function Accordion({ items }) {
  const [open, setOpen] = useState(0);
  return (
    <div className="acc">
      {items.map((it, i) => (
        <div key={i} className={'acc__item' + (open === i ? ' is-open' : '')}>
          <div className="acc__head" onClick={() => setOpen(open === i ? -1 : i)}>
            {it.t}<i className="bi bi-plus"></i>
          </div>
          <div className="acc__body"><div className="acc__body-inner">{it.b}</div></div>
        </div>
      ))}
    </div>
  );
}

function Product({ product, go, onAdd, onBuy, handlers }) {
  useReveal();
  const p = product;
  const [img, setImg] = useState(0);
  const [size, setSize] = useState(null);
  const [color, setColor] = useState(0);
  const [qty, setQty] = useState(1);
  const gallery = p.gallery || [];
  const colorNames = ['Black','Bone','Charcoal'];
  const related = PRODUCTS.filter(x => x.collection === p.collection && x.id !== p.id).slice(0, 4);
  const fallback = PRODUCTS.filter(x => x.id !== p.id).slice(0, 4);
  const rel = related.length >= 4 ? related : [...related, ...fallback].slice(0, 4);

  const doAdd = () => {
    if (!size) { setSize(p.sizes.find(s => !p.soldOutSizes.includes(s))); }
    onAdd(p, { size: size || p.sizes[0], qty, colorName: colorNames[color], color: p.colors[color] });
  };

  return (
    <div>
      <div className="pdp">
        <div className="pdp__gallery">
          <div className="pdp__thumbs">
            {gallery.map((src, i) => (
              <div key={i} className={'pdp__thumb' + (img === i ? ' is-on' : '')} onClick={() => setImg(i)}>
                <img src={src} alt="" onError={(e) => { e.target.style.display='none'; }}
                  style={{ width:'100%', height:'100%', objectFit:'cover', filter:'saturate(1.08) contrast(1.03)' }} />
              </div>
            ))}
          </div>
          <div className="pdp__main-img">
            {p.sale && <Badge kind="sale">−30%</Badge>}
            <img src={gallery[img]} alt="" onError={(e) => { e.target.style.display='none'; }}
              style={{ position:'absolute', inset:0, width:'100%', height:'100%', objectFit:'cover', filter:'saturate(1.08) contrast(1.03)' }} />
          </div>
        </div>

        <div className="pdp__info">
          <Overline>{p.collectionName} · {p.drop}</Overline>
          <h1>{p.title}</h1>
          <div className="pdp__rating">
            <Stars value={p.rating} />
            <span className="ct">{p.rating} · {p.reviews} reviews</span>
          </div>
          <div className="pdp__price">
            {p.sale
              ? <><span className="now">{money(p.sale)}</span><span className="was">{money(p.price)}</span></>
              : <span>{money(p.price)}</span>}
          </div>
          <p className="pdp__desc">{p.desc}</p>

          <div className="pdp__block">
            <div className="pdp__block-head"><h4>Color — {colorNames[color]}</h4></div>
            <div className="swatches">
              {p.colors.map((c, i) => (
                <span key={i} className={'swatch' + (color === i ? ' is-on' : '')} style={{ background:c }} onClick={() => setColor(i)}></span>
              ))}
            </div>
          </div>

          <div className="pdp__block">
            <div className="pdp__block-head"><h4>Size {size ? '— ' + size : ''}</h4><a>Size guide</a></div>
            <div className="fsize">
              {p.sizes.map(s => {
                const out = p.soldOutSizes.includes(s);
                return <div key={s} className={'s' + (size === s ? ' is-on' : '')}
                  style={out ? { color:'var(--ink-300)', textDecoration:'line-through', cursor:'not-allowed' } : null}
                  onClick={() => !out && setSize(s)}>{s}</div>;
              })}
            </div>
          </div>

          <div className="pdp__buy">
            <div className="stepper">
              <button onClick={() => setQty(q => Math.max(1, q - 1))}><i className="bi bi-dash"></i></button>
              <span className="q">{String(qty).padStart(2,'0')}</span>
              <button onClick={() => setQty(q => q + 1)}><i className="bi bi-plus"></i></button>
            </div>
            {p.stock === 0
              ? <button className="btn btn--block" disabled>Sold Out — Notify Me</button>
              : <button className="btn btn--block" onClick={doAdd}>Add to Bag — {money((p.sale||p.price)*qty)}</button>}
          </div>
          {p.stock !== 0 && (
            <button className="btn btn--secondary btn--block" onClick={() => { doAdd(); onBuy(); }}>Buy Now</button>
          )}

          <div className="pdp__perks">
            <div className="pdp__perk"><i className="bi bi-truck"></i> Standard shipping AED 20 · 2–3 business days</div>
            <div className="pdp__perk"><i className="bi bi-arrow-repeat"></i> 30-day returns on unworn items</div>
            <div className="pdp__perk"><i className="bi bi-patch-check"></i> Numbered edition of {p.edition} · {p.stock === 0 ? 'Sold out' : (p.stock <= 4 ? 'Low stock — ' + p.stock + ' left' : 'In stock')}</div>
          </div>

          <div style={{ marginTop:30 }}>
            <Accordion items={[
              { t: 'Details & Fabric', b: '320GSM brushed-back heavyweight cotton. Boxy, dropped-shoulder fit. Screen-printed graphics with tonal embroidery and a numbered woven label.' },
              { t: 'Shipping & Returns', b: 'Standard shipping is AED 20 (2–3 business days), dispatched within 48 hours. 30-day returns on unworn items with tags attached.' },
              { t: 'Sizing', b: 'Garment runs true to size with an oversized cut. Model is 185cm wearing size L. For a fitted look, size down.' },
            ]} />
          </div>
        </div>
      </div>

      <section className="section--tight wrap reveal">
        <div className="sec-head">
          <div className="sec-head__intro">
            <Overline>You May Also Like</Overline>
            <h2>From the {p.collectionName} Line</h2>
          </div>
          <button className="btn-link" onClick={() => go('shop')}>View all <i className="bi bi-arrow-right"></i></button>
        </div>
        <div className="pgrid">
          {rel.map(x => <ProductCard key={x.id} p={x} {...handlers} />)}
        </div>
      </section>
    </div>
  );
}

Object.assign(window, { Product, Accordion });
