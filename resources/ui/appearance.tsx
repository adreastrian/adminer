import { useEffect, useState } from 'react';
import { Select } from './arc/select/select';
import SegmentedControl from './arc/segmented-control/segmented-control';

export type AppearanceMode = 'light' | 'dark' | 'system';
export type PaletteSlot = 'light' | 'dark';
export type Palette = {
  id: string;
  label: string;
  kind: PaletteSlot;
  tokens: Record<string, string>;
};
export type PaletteProvider = {
  id: string;
  label: string;
  defaultLight: string;
  defaultDark: string;
  palettes: Palette[];
};
export type AppearanceState = {
  mode: AppearanceMode;
  scheme: string;
  lightPalette: string;
  darkPalette: string;
  resolvedMode: PaletteSlot;
};
declare global {
  interface Window {
    adminerPalettes?: PaletteProvider[];
    adminerAppearance?: {
      getState(): AppearanceState;
      setMode(mode: AppearanceMode): void;
      setScheme(providerId: string): void;
      setPalette(mode: PaletteSlot, paletteId: string): void;
    };
  }
}

const modes = [
  { value: 'light', label: 'Light' },
  { value: 'dark', label: 'Dark' },
  { value: 'system', label: 'System' },
];

export function Appearance() {
  const [state, setState] = useState(() => window.adminerAppearance!.getState());
  useEffect(() => {
    const update = () => setState(window.adminerAppearance!.getState());
    document.addEventListener('adminer:appearance', update);
    update();
    return () => document.removeEventListener('adminer:appearance', update);
  }, []);
  const catalog = window.adminerPalettes ?? [];
  const family = catalog.find(provider => provider.id === state.scheme);
  if (!family) return null;
  const paletteOptions = family.palettes.map(palette => ({ value: palette.id, label: palette.label }));
  const light = family.palettes.find(palette => palette.id === state.lightPalette);
  const dark = family.palettes.find(palette => palette.id === state.darkPalette);

  return <div className="arc-appearance-controls">
    <SegmentedControl label="Appearance" options={modes} value={state.mode}
      onValueChange={mode => window.adminerAppearance!.setMode(mode as AppearanceMode)} />
    <Select label="Theme family" options={catalog.map(provider => ({ value: provider.id, label: provider.label }))}
      value={state.scheme} onValueChange={scheme => window.adminerAppearance!.setScheme(scheme)} />
    <Select label="Light palette" options={paletteOptions} value={state.lightPalette}
      description={light?.kind === 'dark' ? `${light.label} uses dark colors, including in light mode.` : undefined}
      onValueChange={palette => window.adminerAppearance!.setPalette('light', palette)} />
    <Select label="Dark palette" options={paletteOptions} value={state.darkPalette}
      description={dark?.kind === 'light' ? `${dark.label} uses light colors, including in dark mode.` : undefined}
      onValueChange={palette => window.adminerAppearance!.setPalette('dark', palette)} />
  </div>;
}
