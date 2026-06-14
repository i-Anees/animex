// Pre-compiles the ANIMEX storefront JSX (the original prototype components) into
// minified, IIFE-isolated JS so the browser needs NO runtime Babel. Design unchanged.
import { build } from 'esbuild';
import { mkdirSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const src = resolve(root, 'public/store');
const out = resolve(src, 'dist');
mkdirSync(out, { recursive: true });

// Load order matches the original index.html dependency order.
const files = ['data', 'ui', 'chrome', 'Home', 'Shop', 'Product', 'Checkout', 'App'];

await Promise.all(files.map((name) =>
  build({
    entryPoints: [resolve(src, `${name}.jsx`)],
    outfile: resolve(out, `${name}.js`),
    bundle: false,          // each file shares window globals, like the prototype
    minify: true,
    format: 'iife',         // isolate each file's top-level `const {useState}=React`
    loader: { '.jsx': 'jsx' },
    jsx: 'transform',
    jsxFactory: 'React.createElement',
    jsxFragment: 'React.Fragment',
    target: ['es2019'],
    legalComments: 'none',
  })
));

console.log('Storefront compiled →', out);
