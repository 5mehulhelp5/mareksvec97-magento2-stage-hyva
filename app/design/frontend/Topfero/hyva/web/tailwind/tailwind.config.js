const { spacing } = require('tailwindcss/defaultTheme');

const hyvaModules = require('@hyva-themes/hyva-modules');

module.exports = hyvaModules.mergeTailwindConfig({
  theme: {
    extend: {
      fontFamily: {
        sans: ['"Setup Grotesk"', 'ui-sans-serif', 'system-ui', 'sans-serif']
      },
      colors: {
        brand: {
          DEFAULT: '#7e9b84',
          hover: '#6a8470',
          light: '#f0f4f1'
        },
        primary: {
          DEFAULT: '#7e9b84',
          hover: '#6a8470',
          light: '#f0f4f1'
        },
        text: {
          secondary: '#666666'
        },
        surface: {
          light: '#f9fafb'
        },
        border: {
          DEFAULT: '#e5e7eb'
        }
      },
      maxWidth: {
        '7xl': '80rem'
      },
      container: {
        center: true,
        padding: {
          DEFAULT: spacing['4'],
          sm: spacing['4'],
          lg: spacing['4']
        }
      }
    }
  },
  safelist: [
    // Layout / grid / flex (with breakpoints)
    'grid',
    'flex',
    'items-center',
    'justify-between',
    'flex-wrap',
    'justify-center',
    'grid-cols-1',
    'sm:grid-cols-2',
    'lg:grid-cols-4',
    'sm:grid-cols-3',
    'lg:grid-cols-6',
    'col-span-2',
    'sm:col-span-1',
    'sm:justify-self-end',
    'min-w-0',
    'shrink-0',

    // Sizes / spacing
    'w-full',
    'max-w-7xl',
    'mx-auto',
    'px-4',
    'py-12',
    'py-20',
    'gap-4',
    'gap-8',

    // Typography
    'text-sm',
    'text-base',
    'text-xl',
    'text-2xl',
    'text-3xl',
    'md:text-4xl',
    'font-bold',
    'font-medium',
    'leading-relaxed',

    // Cards / visuals
    'rounded-lg',
    'rounded-2xl',
    'shadow-lg',
    'hover:shadow-xl',
    'hover:shadow-2xl',
    'transition-all',
    'duration-300',
    'overflow-hidden',

    // Colors
    'bg-brand',
    'hover:bg-brand-hover',
    'bg-brand-light',
    'text-white',
    'text-text-secondary',
    'border-border',
    'bg-surface-light',

    // Utilities used in CMS
    'hidden',
    'md:inline-flex',
    'sm:hidden'
  ],
  plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
  content: [
    // this theme's phtml and layout XML files
    '../../**/*.phtml',
    '../../*/layout/*.xml',
    '../../*/page_layout/override/base/*.xml',
    // parent theme in Vendor (if this is a child-theme)
    '../../../../../../../vendor/hyva-themes/magento2-default-theme/**/*.phtml'
    //'../../../../../../../vendor/hyva-themes/magento2-default-theme/*/layout/*.xml',
    //'../../../../../../../vendor/hyva-themes/magento2-default-theme/*/page_layout/override/base/*.xml',
    // app/code phtml files (if need tailwind classes from app/code modules)
    //'../../../../../../../app/code/**/*.phtml',
  ]
});
