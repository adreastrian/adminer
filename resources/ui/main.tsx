import './arc/foundation.css';
import './bridge.css';
import { useLayoutEffect } from 'react';
import { createRoot } from 'react-dom/client';
import { Appearance } from './appearance';
import { NativeFallback } from './native-fallback';
import { belongsToSidebarSelect, syncNativeControls } from './native-controls';

let appearanceMounted = false;

function scan() {
  syncNativeControls();
  const appearance = document.getElementById('arc-appearance');
  if (appearance && !appearanceMounted && window.adminerAppearance) {
    appearanceMounted = true;
    const host = document.createElement('div');
    host.className = 'arc-island';
    appearance.appendChild(host);
    createRoot(host).render(<NativeFallback onError={() => { host.remove(); appearance.classList.remove('arc-appearance-ready'); }}>
      <AppearanceReady onReady={() => appearance.classList.add('arc-appearance-ready')} />
    </NativeFallback>);
  }
}

function AppearanceReady({ onReady }: { onReady(): void }) {
  useLayoutEffect(onReady, [onReady]);
  return <Appearance />;
}

function start() {
  // Radix opens on pointerdown and disables outside pointer events. Its opening click can then
  // land on <html>, which Adminer mistakes for an outside-sidebar click. Remember that gesture.
  let sidebarSelectGesture = false;
  document.documentElement.addEventListener('pointerdown', event => {
    sidebarSelectGesture = belongsToSidebarSelect(event);
  }, true);
  // Record keyboard-generated item clicks before React closes the portal and clears aria-controls.
  document.documentElement.addEventListener('click', event => {
    sidebarSelectGesture ||= belongsToSidebarSelect(event);
  }, true);
  // This boundary is after React's portal handlers on body, but before Adminer's document handler.
  document.documentElement.addEventListener('click', event => {
    if (sidebarSelectGesture) event.stopPropagation();
    sidebarSelectGesture = false;
  });
  scan();
  let queued = false;
  const observer = new MutationObserver(records => {
    if (queued || !records.some(record => [...record.addedNodes, ...record.removedNodes].some(node =>
      node instanceof Element && (node.matches('select, input[list], datalist, .arc-select-island, #arc-appearance') || node.querySelector('select, input[list], datalist'))))) return;
    queued = true;
    queueMicrotask(() => { queued = false; scan(); });
  });
  observer.observe(document.body, { childList: true, subtree: true });
  document.addEventListener('adminer:appearance', scan);
}

if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start, { once: true });
else start();
