import { build } from 'esbuild';
import { copyFile } from 'node:fs/promises';

await build({
  entryPoints: ['resources/ui/main.tsx'],
  outfile: 'public/assets/build/arc-ui.js',
  bundle: true,
  minify: true,
  format: 'iife',
  target: ['es2020'],
  jsx: 'automatic',
  define: { 'process.env.NODE_ENV': '"production"' },
  legalComments: 'eof',
  banner: {
    js: '/*! UI Arc components: MIT, Copyright (c) 2026 Elia Kuratli. Full license: arc-ui.LICENSE.txt. */',
    css: '/*! UI Arc components: MIT, Copyright (c) 2026 Elia Kuratli. Full license: arc-ui.LICENSE.txt. */',
  },
});
await copyFile('resources/ui/arc/LICENSE', 'public/assets/build/arc-ui.LICENSE.txt');
