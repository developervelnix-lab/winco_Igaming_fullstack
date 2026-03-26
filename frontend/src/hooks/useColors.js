import { useTheme } from '../context/ThemeContext';
import { COLORS, lightTheme, FONTS } from '../constants/theme';

/**
 * useColors hook - returns the active theme's colors reactively.
 * Use this instead of `COLORS` directly in components that need
 * background/text colors to respond to theme changes.
 */
export const useColors = () => {
  const { theme } = useTheme();
  const colors = theme === 'light' ? lightTheme : COLORS;
  return colors;
};

export { FONTS };
