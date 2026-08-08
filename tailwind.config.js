/**
 * Tailwind config — Way More BD (isdb-custom)
 *
 * Design tokens mirror the target system:
 *   primary   #f48721  (orange)   secondary #041f1e (dark green)
 *   title     #222831             paragraph #252a34
 *   border    #cccccc             page bg   #FBF9F5
 *   font      Open Sans
 *
 * NOTE: the `amber` scale is intentionally REMAPPED onto the brand orange so
 * every existing `bg-amber-500` / `text-amber-600` in the templates becomes the
 * brand colour with no per-file edits. Use `brand-*` for new work.
 */
module.exports = {
  content: [
    './**/*.php',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['"Open Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
      },
      colors: {
        // Brand tokens (explicit)
        brand: {
          DEFAULT: '#f48721',
          primary: '#f48721',
          hover:   '#e0761a',
          dark:    '#041f1e', // secondary — nav bar, dark panels
          title:   '#222831',
          body:    '#252a34',
          line:    '#cccccc',
          bg:      '#FBF9F5', // page background
          soft:    '#FFF2EA', // tinted button bg
        },
        // Remap amber -> brand orange (keeps all existing utility classes working)
        amber: {
          50:  '#fef6ee',
          100: '#fde8d5',
          200: '#fbd0aa',
          300: '#f8b174',
          400: '#f69a45',
          500: '#f48721',
          600: '#e0761a',
          700: '#b95c15',
          800: '#934a18',
          900: '#763e17',
        },
      },
      borderRadius: {
        card: '4px', // their card radius
      },
    },
  },
  // Classes emitted by WordPress/WooCommerce (never present in our PHP source)
  // would otherwise be tree-shaken. Keep them.
  safelist: [
    'hidden',
    { pattern: /^(bg|text|ring|border)-(amber|emerald|rose|slate|stone|brand)-(50|100|200|400|500|600|700|800|900)$/ },
  ],
  plugins: [
    require('@tailwindcss/typography'),
  ],
};
