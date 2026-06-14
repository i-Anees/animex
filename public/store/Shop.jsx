/* ANIMEX WEAR — Shop / PLP with filters + sort */
const { useState, useEffect, useRef } = React;

function FilterGroup({ title, children }) {
  return (
    <div className="filters__group">
      <h4>{title}</h4>
      {children}
    </div>
  );
}

function Shop({ go, handlers }) {
  useReveal();
  const [cats, setCats] = useState([]);
  const [colls, setColls] = useState([]);
  const [sizes, setSizes] = useState([]);
  const [maxPrice, setMaxPrice] = useState(240);
  const [sort, setSort] = useState('featured');
  const [density, setDensity] = useState(4);

  const toggle = (arr, set, v) => set(arr.includes(v) ? arr.filter(x => x !== v) : [...arr, v]);

  let list = PRODUCTS.filter(p =>
    (cats.length === 0 || cats.includes(p.category)) &&
    (colls.length === 0 || colls.includes(p.collection)) &&
    (sizes.length === 0 || sizes.some(s => p.sizes.includes(s) && !p.soldOutSizes.includes(s))) &&
    (p.sale || p.price) <= maxPrice
  );
  if (sort === 'price-asc') list = [...list].sort((a, b) => (a.sale||a.price) - (b.sale||b.price));
  if (sort === 'price-desc') list = [...list].sort((a, b) => (b.sale||b.price) - (a.sale||a.price));
  if (sort === 'new') list = [...list].sort((a, b) => (b.isNew?1:0) - (a.isNew?1:0));

  const SIZES = ['S','M','L','XL','XXL'];

  return (
    <div>
      <div className="pagehead">
        <div className="crumbs">Home / Shop / All Drops</div>
        <h1>All Drops</h1>
        <p>The full archive — every series, every category. Numbered, heavyweight, produced once.</p>
      </div>
      <div className="plp">
        <aside className="filters">
          <FilterGroup title="Series">
            {COLLECTIONS.map(c => (
              <div key={c.id} className={'fopt' + (colls.includes(c.id) ? ' is-on' : '')} onClick={() => toggle(colls, setColls, c.id)}>
                <span className="fbox">{colls.includes(c.id) && <i className="bi bi-check"></i>}</span>
                {c.name}
                <span className="ct">{productsByCollection(c.id).length}</span>
              </div>
            ))}
          </FilterGroup>
          <FilterGroup title="Category">
            {CATEGORIES.map(c => (
              <div key={c} className={'fopt' + (cats.includes(c) ? ' is-on' : '')} onClick={() => toggle(cats, setCats, c)}>
                <span className="fbox">{cats.includes(c) && <i className="bi bi-check"></i>}</span>
                {c}
                <span className="ct">{PRODUCTS.filter(p => p.category === c).length}</span>
              </div>
            ))}
          </FilterGroup>
          <FilterGroup title="Size">
            <div className="fsize">
              {SIZES.map(s => (
                <div key={s} className={'s' + (sizes.includes(s) ? ' is-on' : '')} onClick={() => toggle(sizes, setSizes, s)}>{s}</div>
              ))}
            </div>
          </FilterGroup>
          <FilterGroup title={'Max Price — ' + money(maxPrice)}>
            <input type="range" min="78" max="240" step="2" value={maxPrice}
              onChange={(e) => setMaxPrice(+e.target.value)} style={{ width:'100%', accentColor:'#000' }} />
            <div className="mono" style={{ display:'flex', justifyContent:'space-between', fontSize:11, color:'var(--fg-4)', marginTop:8 }}>
              <span>$78</span><span>$240</span>
            </div>
          </FilterGroup>
        </aside>

        <div>
          <div className="plp__toolbar">
            <span className="plp__count">{list.length} Products</span>
            <div className="sortbar">
              <div className="density">
                <button className={density===3?'is-on':''} onClick={() => setDensity(3)}><i className="bi bi-grid"></i></button>
                <button className={density===4?'is-on':''} onClick={() => setDensity(4)}><i className="bi bi-grid-3x3-gap"></i></button>
              </div>
              <select value={sort} onChange={(e) => setSort(e.target.value)}>
                <option value="featured">Sort — Featured</option>
                <option value="new">Newest</option>
                <option value="price-asc">Price — Low to High</option>
                <option value="price-desc">Price — High to Low</option>
              </select>
            </div>
          </div>
          {list.length === 0 ? (
            <div style={{ padding:'80px 0', textAlign:'center' }}>
              <p className="mono" style={{ color:'var(--fg-4)', letterSpacing:'.1em', textTransform:'uppercase' }}>No products match these filters.</p>
            </div>
          ) : (
            <div className={'pgrid' + (density === 3 ? ' pgrid--3' : '')}>
              {list.map(p => <ProductCard key={p.id} p={p} {...handlers} />)}
            </div>
          )}
        </div>
      </div>
    </div>
  );
}

Object.assign(window, { Shop });
