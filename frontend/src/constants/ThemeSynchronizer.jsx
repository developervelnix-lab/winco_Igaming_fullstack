import { useEffect } from 'react';
import { COLORS, lightTheme, FONTS } from './theme';
import { useTheme } from '../context/ThemeContext';

const ThemeSynchronizer = () => {
  const { theme } = useTheme();

  useEffect(() => {
    const root = document.documentElement;
    const activeColors = theme === 'light' ? lightTheme : COLORS;
    
    // Sync Colors
    Object.entries(activeColors).forEach(([key, value]) => {
      // Convert camelCase to kebab-case
      const kebabKey = key.replace(/([a-z0-9]|(?=[A-Z]))([A-Z])/g, '$1-$2').toLowerCase();
      root.style.setProperty(`--${kebabKey}`, value);
      
      // Legacy support for index.css specific names
      if (key === 'brand') root.style.setProperty('--brand-color', value);
      if (key === 'brandLight') root.style.setProperty('--brand-color-light', value);
      if (key === 'brandDark') root.style.setProperty('--brand-color-dark', value);
      if (key === 'secondaryBg') root.style.setProperty('--secondary-bg', value);
    });

    // border colors derived from brand (keep it dark/light consistent or vary if needed)
    // using text color logic for border instead of brand in light mode might be cleaner, but let's stick to brand for now
    const brandRGB = activeColors.brand.startsWith('#') 
      ? hexToRgb(activeColors.brand) 
      : { r: 230, g: 160, b: 0 };
    
    // Use slightly darker border for light theme if needed, or stick to same opacity
    root.style.setProperty('--border', `rgba(${brandRGB.r}, ${brandRGB.g}, ${brandRGB.b}, 0.12)`);
    // Additional generic border for consistency across themes:
    root.style.setProperty('--border2', theme === 'light' ? 'rgba(0,0,0,0.07)' : 'rgba(255,255,255,0.07)');

    // Sync Fonts
    Object.entries(FONTS).forEach(([key, value]) => {
      root.style.setProperty(`--font-${key}`, value);
    });

  }, [theme]);

  const hexToRgb = (hex) => {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
      r: parseInt(result[1], 16),
      g: parseInt(result[2], 16),
      b: parseInt(result[3], 16)
    } : null;
  };

  return null; // This component doesn't render anything
};

export default ThemeSynchronizer;
