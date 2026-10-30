import { build } from 'esbuild';
import { copyFile } from 'node:fs/promises';

await build({
  entryPoints: ['ui/main.tsx'],
  outfile: 'assets/arc-ui.js',
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
await copyFile('ui/arc/LICENSE', 'assets/arc-ui.LICENSE.txt');
