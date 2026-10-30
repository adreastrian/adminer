import './arc/foundation.css';
import './bridge.css';
import { Component, useLayoutEffect, useRef, useState, type ReactNode } from 'react';
import { createRoot, type Root } from 'react-dom/client';
import { Select } from './arc/select/select';
import { Combobox } from './arc/combobox/combobox';
import { Appearance } from './appearance';

class NativeFallback extends Component<{ children: ReactNode; onError(): void }, { failed: boolean }> {
  state = { failed: false };
  static getDerivedStateFromError() { return { failed: true }; }
  componentDidCatch() { this.props.onError(); }
  render() { return this.state.failed ? null : this.props.children; }
}

type NativeState = {
  selected: string;
  options: { value: string; label: string; disabled: boolean }[];
  disabled: boolean;
  required: boolean;
};

type NativeControl = HTMLSelectElement | HTMLInputElement;

function fieldLabel(native: NativeControl): string {
  if (native.closest('#dbs')) return 'Database';
  if (/^columns\[.*\]\[fun\]$/.test(native.name)) return 'Column function';
  if (/^columns\[.*\]\[col\]$/.test(native.name)) return 'Column';
  if (/^where\[.*\]\[col\]$/.test(native.name)) return 'Search column';
  if (/^where\[.*\]\[op\]$/.test(native.name)) return 'Search operator';
  if (/^order\[/.test(native.name)) return 'Sort column';
  const labelledBy = native.getAttribute('aria-labelledby');
  if (labelledBy) {
    const label = labelledBy.split(/\s+/).map(id => document.getElementById(id)?.textContent ?? '').join(' ').trim();
    if (label) return label;
  }
  if (native.getAttribute('aria-label')) return native.getAttribute('aria-label')!;
  for (const label of native.labels ?? []) {
    const copy = label.cloneNode(true) as HTMLElement;
    copy.querySelectorAll('select, input, button, .arc-island').forEach(node => node.remove());
    const text = copy.textContent?.trim().replace(/\s+/g, ' ');
    if (text) return text;
  }
  const header = native.closest('tr')?.querySelector('th')?.textContent?.trim();
  if (header) return header;
  const names: Record<string, string> = { lang: 'Language', db: 'Database', ns: 'Schema', 'auth[driver]': 'System' };
  return native.title || names[native.name] || native.name.replace(/\[|\]|_/g, ' ').trim() || 'Selection';
}

function readNative(native: HTMLSelectElement): NativeState {
  const database = !!native.closest('#dbs');
  return {
    selected: native.selectedIndex < 0 || database && native.value === '' ? '' : `option-${native.selectedIndex}`,
    options: Array.from(native.options, (option, index) => {
      const group = option.parentElement instanceof HTMLOptGroupElement ? option.parentElement : null;
      const emptyLabel = database ? 'Choose database'
        : /^columns\[.*\]\[fun\]$/.test(native.name) ? 'No function'
        : /^columns\[.*\]\[col\]$/.test(native.name) ? 'All columns'
        : /^order\[/.test(native.name) ? 'No sorting' : 'None';
      const text = option.label.trim() || emptyLabel;
      return {
        // Index keys preserve duplicate and empty native values without Radix's empty-value restriction.
        value: `option-${index}`,
        label: group?.label ? `${group.label}: ${text}` : text,
        disabled: option.disabled || !!group?.disabled,
      };
    }),
    disabled: native.matches(':disabled'),
    required: native.required,
  };
}

function NativeSelect({ native, host, ready }: { native: HTMLSelectElement; host: HTMLElement; ready(): void }) {
  const [state, setState] = useState(() => readNative(native));
  const trigger = useRef<HTMLButtonElement & HTMLInputElement>(null);
  const [invalid, setInvalid] = useState(false);
  const id = host.id + '-control';
  const label = fieldLabel(native);
  const nativeDescription = (native.getAttribute('aria-describedby') || '').split(/\s+/)
    .map(descriptionId => document.getElementById(descriptionId)?.textContent?.trim()).filter(Boolean).join(' ');
  const searchable = !!native.closest('#dbs') && state.options.length > 12;

  useLayoutEffect(() => {
    const restores = new Map<object, Map<string, () => void>>();
    let active = true;
    const sync = () => {
      if (!active) return;
      const next = readNative(native);
      host.hidden = native.hidden || native.classList.contains('hidden') || native.style.display === 'none';
      host.dataset.arcField = native.name;
      setState(previous => JSON.stringify(previous) === JSON.stringify(next) ? previous : next);
      setInvalid(!native.validity.valid && native.matches(':user-invalid'));
      installOptionObservers();
    };
    // Adminer also changes values directly, without emitting a DOM event.
    const observeProperty = (target: object, prototype: object, key: string) => {
      if (restores.get(target)?.has(key) || Object.prototype.hasOwnProperty.call(target, key)) return;
      const descriptor = Object.getOwnPropertyDescriptor(prototype, key);
      if (!descriptor?.get || !descriptor.set) return;
      Object.defineProperty(target, key, {
        configurable: true,
        get() { return descriptor.get!.call(this); },
        set(value) { descriptor.set!.call(this, value); sync(); },
      });
      if (!restores.has(target)) restores.set(target, new Map());
      restores.get(target)!.set(key, () => Reflect.deleteProperty(target, key));
    };
    function installOptionObservers() {
      Array.from(native.options).forEach(option => observeProperty(option, HTMLOptionElement.prototype, 'selected'));
    }
    observeProperty(native, HTMLSelectElement.prototype, 'value');
    observeProperty(native, HTMLSelectElement.prototype, 'selectedIndex');
    installOptionObservers();
    const observer = new MutationObserver(sync);
    observer.observe(native, { childList: true, subtree: true, characterData: true, attributes: true,
      attributeFilter: ['selected', 'disabled', 'label', 'value', 'required', 'name', 'class', 'style', 'hidden', 'aria-describedby'] });
    // A disabled fieldset disables the actual select even when its own attribute is absent.
    const fieldsets = Array.from(native.closest('form')?.querySelectorAll('fieldset') ?? []).filter(fieldset => fieldset.contains(native));
    fieldsets.forEach(fieldset => observer.observe(fieldset, { attributes: true, attributeFilter: ['disabled'] }));
    const onReset = () => queueMicrotask(sync);
    const focus = () => trigger.current?.focus();
    const forwardFocus = () => native.dispatchEvent(new FocusEvent('focus'));
    trigger.current?.addEventListener('focus', forwardFocus);
    const onInvalid = (event: Event) => {
      event.preventDefault();
      setInvalid(true);
      trigger.current?.focus();
    };
    native.addEventListener('change', sync);
    native.addEventListener('input', sync);
    native.addEventListener('focus', focus);
    native.addEventListener('invalid', onInvalid);
    native.form?.addEventListener('reset', onReset);
    ready();
    sync();
    return () => {
      active = false;
      observer.disconnect();
      native.removeEventListener('change', sync);
      native.removeEventListener('input', sync);
      native.removeEventListener('focus', focus);
      native.removeEventListener('invalid', onInvalid);
      native.form?.removeEventListener('reset', onReset);
      trigger.current?.removeEventListener('focus', forwardFocus);
      restores.forEach(properties => properties.forEach(restore => restore()));
    };
  }, [native, ready]);

  const choose = (value: string) => {
    // The Combobox clear action maps back to the native empty option when it exists.
    const index = value ? Number(value.slice('option-'.length)) : Array.from(native.options).findIndex(option => option.value === '');
    if (!Number.isInteger(index) || index < 0 || !native.options[index] || state.options[index]?.disabled) return;
    if (native.selectedIndex === index) return;
    native.selectedIndex = index;
    native.dispatchEvent(new Event('input', { bubbles: true }));
    native.dispatchEvent(new Event('change', { bubbles: true }));
    setState(readNative(native));
    setInvalid(!native.validity.valid);
  };

  useLayoutEffect(() => {
    trigger.current?.setAttribute('aria-invalid', String(invalid));
    if (state.required) trigger.current?.setAttribute('aria-required', 'true');
    else trigger.current?.removeAttribute('aria-required');
    const describedBy = native.getAttribute('aria-describedby');
    if (describedBy) trigger.current?.setAttribute('aria-describedby', host.shadowRoot ? `${id}-native-description` : describedBy);
    if (native.title) trigger.current?.setAttribute('title', native.title);
  }, [state, invalid, native, host, id]);

  const props = { id, label, options: state.options, value: state.selected, disabled: state.disabled, onValueChange: choose };
  return <>
    {searchable
      ? <Combobox {...props} ref={trigger} placeholder="Choose database" />
      : <Select {...props} ref={trigger} placeholder={`Choose ${label.toLocaleLowerCase()}`} />}
    {host.shadowRoot && nativeDescription && <span id={`${id}-native-description`} className="arc-native-description">{nativeDescription}</span>}
    {invalid && <span className="arc-field-error" role="status">{native.validationMessage}</span>}
  </>;
}

function readDatalist(native: HTMLInputElement) {
  return {
    value: native.value,
    options: Array.from(native.list?.options ?? []).filter(option => option.value && !option.disabled)
      .map(option => ({ value: option.value, label: option.label || option.value, keywords: [option.value] }))
      .filter((option, index, options) => options.findIndex(item => item.value === option.value) === index),
    disabled: native.matches(':disabled'),
    readOnly: native.readOnly,
    required: native.required,
    placeholder: native.placeholder,
  };
}

function NativeDatalist({ native, host, ready }: { native: HTMLInputElement; host: HTMLElement; ready(): void }) {
  const [state, setState] = useState(() => readDatalist(native));
  const [invalid, setInvalid] = useState(false);
  const trigger = useRef<HTMLInputElement>(null);
  const committed = useRef(native.value);
  const id = host.id + '-control';
  const description = (native.getAttribute('aria-describedby') || '').split(/\s+/)
    .map(descriptionId => document.getElementById(descriptionId)?.textContent?.trim()).filter(Boolean).join(' ');

  useLayoutEffect(() => {
    const sync = () => {
      const next = readDatalist(native);
      host.hidden = native.hidden || native.classList.contains('hidden') || native.style.display === 'none';
      host.dataset.arcField = native.name;
      setState(previous => JSON.stringify(previous) === JSON.stringify(next) ? previous : next);
      setInvalid(!native.validity.valid && native.matches(':user-invalid'));
    };
    // Keep direct Adminer assignments visible without replacing the native form control.
    const descriptor = Object.getOwnPropertyDescriptor(HTMLInputElement.prototype, 'value')!;
    const observeValue = !Object.prototype.hasOwnProperty.call(native, 'value');
    if (observeValue) Object.defineProperty(native, 'value', {
      configurable: true,
      get() { return descriptor.get!.call(this); },
      set(value) { descriptor.set!.call(this, value); sync(); },
    });
    const observer = new MutationObserver(sync);
    observer.observe(native, { attributes: true, attributeFilter: ['value', 'disabled', 'readonly', 'required',
      'placeholder', 'name', 'class', 'style', 'hidden', 'aria-describedby', 'list', 'maxlength', 'pattern'] });
    if (native.list) observer.observe(native.list, { childList: true, subtree: true, characterData: true, attributes: true });
    const fieldsets = Array.from(native.form?.querySelectorAll('fieldset') ?? []).filter(fieldset => fieldset.contains(native));
    fieldsets.forEach(fieldset => observer.observe(fieldset, { attributes: true, attributeFilter: ['disabled'] }));
    const onReset = () => queueMicrotask(() => { committed.current = native.value; sync(); });
    const focus = () => trigger.current?.focus();
    const onInvalid = (event: Event) => {
      event.preventDefault();
      setInvalid(true);
      trigger.current?.focus();
    };
    const form = native.form;
    native.addEventListener('input', sync);
    native.addEventListener('change', sync);
    native.addEventListener('focus', focus);
    native.addEventListener('invalid', onInvalid);
    form?.addEventListener('reset', onReset);
    ready();
    sync();
    return () => {
      observer.disconnect();
      native.removeEventListener('input', sync);
      native.removeEventListener('change', sync);
      native.removeEventListener('focus', focus);
      native.removeEventListener('invalid', onInvalid);
      form?.removeEventListener('reset', onReset);
      if (observeValue) Reflect.deleteProperty(native, 'value');
    };
  }, [native, host, ready]);

  return <>
    <Combobox ref={trigger} id={id} label={fieldLabel(native)} {...state} allowCustomValue
      description={description || undefined} title={native.title || undefined}
      aria-invalid={invalid} aria-required={state.required || undefined}
      maxLength={native.maxLength < 0 ? undefined : native.maxLength}
      onFocus={() => {
        committed.current = native.value;
        native.dispatchEvent(new FocusEvent('focus'));
      }}
      onValueChange={value => {
        // Exiting menu items can retain old React handlers during their animation.
        if (native.readOnly || native.matches(':disabled') || native.value === value) return;
        native.value = value;
        native.dispatchEvent(new Event('input', { bubbles: true }));
        setState(readDatalist(native));
      }}
      onBlur={() => {
        if (committed.current !== native.value) {
          committed.current = native.value;
          native.dispatchEvent(new Event('change', { bubbles: true }));
        }
        native.dispatchEvent(new FocusEvent('blur'));
        setState(readDatalist(native));
        setInvalid(!native.validity.valid);
      }} />
    {invalid && <span className="arc-field-error" role="status">{native.validationMessage}</span>}
  </>;
}

const mounts = new Map<NativeControl, { root: Root; host: HTMLElement; restore(): void }>();
const failed = new WeakSet<NativeControl>();
let nextId = 0;
let appearanceMounted = false;

function restoreAttributes(native: NativeControl) {
  if (!native.dataset.arcOriginal) return;
  const original = JSON.parse(native.dataset.arcOriginal) as { tabindex: string | null; ariaHidden: string | null };
  for (const [key, value] of [['tabindex', original.tabindex], ['aria-hidden', original.ariaHidden]] as const) {
    if (value === null) native.removeAttribute(key);
    else native.setAttribute(key, value);
  }
  delete native.dataset.arcOriginal;
  delete native.dataset.arcEnhanced;
}

function hasClippingAncestor(native: HTMLInputElement) {
  // Arc Combobox renders its menu inline. Keep the browser datalist when an
  // editor table or scroller would clip it. Select uses a portal and is unaffected.
  for (let ancestor = native.parentElement; ancestor; ancestor = ancestor.parentElement) {
    const style = getComputedStyle(ancestor);
    if ([style.overflowX, style.overflowY].some(overflow => /^(auto|scroll|hidden|clip)$/.test(overflow))) return true;
  }
  return false;
}

function enhanceControl(native: NativeControl) {
  if (!native.isConnected || mounts.has(native) || failed.has(native)) return;
  // Adminer clones form rows. Restore copied attributes even if the clone needs native fallback.
  if (native.dataset.arcEnhanced) {
    if (native.nextElementSibling?.classList.contains('arc-select-island')) native.nextElementSibling.remove();
    restoreAttributes(native);
  }
  if ((native instanceof HTMLSelectElement ? native.multiple || native.size > 1 : native.type !== 'text' || !native.list || hasClippingAncestor(native)) ||
    native.closest('.arc-island, .jush-autocomplete, .sqlarea, [data-arc-skip], [data-arc-native]') ||
    native.classList.contains('jush-autocomplete') || native.getAttribute('aria-hidden') === 'true') return;
  const host = document.createElement('span');
  host.className = 'arc-island arc-select-island';
  host.id = `arc-select-${++nextId}`;
  host.dataset.arcField = native.name;
  if (native.closest('#dbs')) host.dataset.arcDatabase = 'true';
  if (native.closest('#fieldset-select, #fieldset-search, #fieldset-sort')) host.dataset.arcQuery = 'true';
  native.after(host);

  // Adminer queries and clones its form's light DOM. Keep Arc's internal controls out of
  // that DOM so qsl('select, input', row) still returns the actual native field.
  const isolated = !!native.form && !native.closest('#dbs');
  let container: HTMLElement = host;
  if (isolated) {
    host.dataset.arcIsolated = 'true';
    const shadow = host.attachShadow({ mode: 'open' });
    const sheet = document.querySelector<HTMLLinkElement>('link[href*="arc-ui.css"]');
    if (sheet) {
      const link = document.createElement('link');
      link.rel = 'stylesheet';
      link.href = sheet.href;
      shadow.appendChild(link);
    }
    const styles = document.createElement('style');
    styles.textContent = '.arc-shadow-content > div > label { position:absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; }';
    shadow.appendChild(styles);
    container = document.createElement('div');
    container.className = 'arc-shadow-content';
    shadow.appendChild(container);
  }
  const root = createRoot(container);
  const restore = () => {
    restoreAttributes(native);
    host.hidden = true;
  };
  const ready = () => {
    native.dataset.arcOriginal = JSON.stringify({ tabindex: native.getAttribute('tabindex'), ariaHidden: native.getAttribute('aria-hidden') });
    native.dataset.arcEnhanced = 'true';
    native.tabIndex = -1;
    native.setAttribute('aria-hidden', 'true');
  };
  mounts.set(native, { root, host, restore });
  root.render(<NativeFallback onError={() => {
    failed.add(native);
    restore();
  }}>{native instanceof HTMLSelectElement
    ? <NativeSelect native={native} host={host} ready={ready} />
    : <NativeDatalist native={native} host={host} ready={ready} />}</NativeFallback>);
}

function scan() {
  mounts.forEach(({ root, host, restore }, native) => {
    if (native.isConnected && host.isConnected) return;
    root.unmount();
    restore();
    host.remove();
    mounts.delete(native);
  });
  document.querySelectorAll<NativeControl>('select, input[list]').forEach(enhanceControl);
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
  const belongsToSidebarSelect = (event: Event) => {
    for (const target of event.composedPath()) {
      if (!(target instanceof Element)) continue;
      if (target.matches('[role="combobox"]')) {
        const tree = target.getRootNode();
        if (target.closest('#foot .arc-island') || tree instanceof ShadowRoot && tree.host.closest('#foot')) return true;
      }
      const listbox = target.closest('[role="listbox"]');
      if (!listbox?.id) continue;
      const owner = `[aria-controls="${CSS.escape(listbox.id)}"]`;
      if (document.querySelector(`#foot ${owner}`)) return true;
      if (Array.from(mounts.values()).some(({ host }) => host.closest('#foot') && host.shadowRoot?.querySelector(owner))) return true;
    }
    return false;
  };
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
