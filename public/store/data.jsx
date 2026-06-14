/* ANIMEX WEAR — catalog, now sourced live from the Laravel/MySQL backend.
   Helpers are identical to the prototype; only COLLECTIONS / CATEGORIES /
   PRODUCTS are injected from the database via window.__ANIMEX_DATA__. */

// tonal pairs for product media fallback: [resting, hover] — kept inside the mono range
const TONES = [
  ['#F5F5F5', '#2D2D2D'], ['#EDEDED', '#000000'], ['#F0F0F0', '#1f1f1f'],
  ['#2D2D2D', '#F5F5F5'], ['#000000', '#2D2D2D'], ['#E8E8E8', '#111111'],
];

// Curated color fashion/streetwear photography (used for hero / collection banners).
const PHOTOS = [
  '1521572163474-6864f9cf17ab','1503341504253-dff4815485f1','1490481651871-ab68de25d43d',
  '1492288991661-058aa541ff43','1483985988355-763728e1935b','1551232864-3f0890e580d9',
  '1620799140408-edc6dcb6d633','1576566588028-4147f3842f27','1556821840-3a63f95609a7',
  '1618354691373-d851c5c3a990','1512436991641-6745cdb1723f','1469334031218-e382a71b716b',
  '1485231183945-fffde7cc051e','1441986300917-64674bd600d8','1487222477894-8943e31ef7b2',
  '1499714608240-22fc6ad53fb2','1490114538077-0a7f8cb49891','1564584217132-2271feaeb3c5',
  '1517841905240-472988babdf9','1438761681033-6461ffad8d80',
];
function U(id, w){ return 'https://images.unsplash.com/photo-' + id + '?w=' + (w||800) + '&q=75&auto=format&fit=crop'; }
function photo(i, w){ return U(PHOTOS[((i % PHOTOS.length) + PHOTOS.length) % PHOTOS.length], w); }

function money(n){ return '$' + n; }

const __D = window.__ANIMEX_DATA__ || { collections: [], categories: [], products: [] };
const COLLECTIONS = __D.collections;
const CATEGORIES  = __D.categories;
const PRODUCTS    = __D.products;

function productsByCollection(id){ return PRODUCTS.filter(p => p.collection === id); }
function getProduct(id){ return PRODUCTS.find(p => p.id === id); }

Object.assign(window, { COLLECTIONS, CATEGORIES, PRODUCTS, TONES, money, productsByCollection, getProduct, U, photo, PHOTOS });
