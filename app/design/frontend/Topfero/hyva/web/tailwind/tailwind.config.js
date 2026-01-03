const {
  spacing
} = require('tailwindcss/defaultTheme');

const colors = require('tailwindcss/colors');

const hyvaModules = require('@hyva-themes/hyva-modules');

module.exports = hyvaModules.mergeTailwindConfig({
  theme: {
    extend: {
      screens: {
        'sm': '640px',
        // => @media (min-width: 640px) { ... }
        'md': '768px',
        // => @media (min-width: 768px) { ... }
        'lg': '1024px',
        // => @media (min-width: 1024px) { ... }
        'xl': '1280px',
        // => @media (min-width: 1280px) { ... }
        '2xl': '1536px' // => @media (min-width: 1536px) { ... }
      },
      fontFamily: {
        sans: ["Segoe UI", "Helvetica Neue", "Arial", "sans-serif"]
      },
      colors: {
        primary: {
          lighter: '#6B77A8',   // svetlejší odtieň
          "DEFAULT": '#586cac',  // hlavná farba
          darker: '#4d5c95'    // tmavší odtieň
        },
        secondary: {
          lighter: colors.blue['100'],
          "DEFAULT": colors.blue['200'],
          darker: colors.blue['300']
        },
        background: {
          lighter: colors.blue['100'],
          "DEFAULT": colors.blue['200'],
          darker: colors.blue['300']
        },
        green: colors.emerald,
        yellow: colors.amber,
        purple: colors.violet
      },
      textColor: {
        orange: colors.orange,
        red: { ...colors.red,
          "DEFAULT": colors.red['500']
        },
        primary: {
          lighter: colors.gray['700'],
          "DEFAULT": colors.gray['800'],
          darker: colors.gray['900']
        },
        secondary: {
          lighter: colors.gray['400'],
          "DEFAULT": colors.gray['600'],
          darker: colors.gray['800']
        }
      },
      backgroundColor: {
        primary: {
          lighter: '#6B77A8',
          DEFAULT: '#586cac', // ← hlavná
          darker:  '#4d5c95'
        },
        secondary: {
          lighter: colors.blue['100'],
          "DEFAULT": colors.blue['200'],
          darker: colors.blue['300']
        },
        container: {
          lighter: colors.white,
          "DEFAULT": colors.neutral['50'],
          darker: colors.neutral['100']
        }
      },
      borderColor: {
        primary: {
          lighter: '#6B77A8',
          DEFAULT: '#586cac', // ← hlavná
          darker:  '#4d5c95'
        },
        secondary: {
          lighter: colors.blue['100'],
          "DEFAULT": colors.blue['200'],
          darker: colors.blue['300']
        },
        container: {
          lighter: colors.neutral['100'],
          "DEFAULT": '#e7e7e7',
          darker: '#b6b6b6'
        }
      },
      minHeight: {
        a11y: spacing["11"],
        'screen-25': '25vh',
        'screen-50': '50vh',
        'screen-75': '75vh'
      },
      maxHeight: {
        'screen-25': '25vh',
        'screen-50': '50vh',
        'screen-75': '75vh'
      },
      container: {
        center: true,
        padding: spacing["6"]
      }
    }
  },
  safelist: [
      // ----- Layout / grid / flex (aj s breakpointmi)
      'grid','flex','items-center','justify-between','flex-wrap','justify-center',
      'grid-cols-1','sm:grid-cols-2','lg:grid-cols-4','sm:grid-cols-3','lg:grid-cols-6',
      'grid-cols-[44px,1fr]','sm:grid-cols-[44px,1fr,auto]',
      'col-span-2','sm:col-span-1','sm:justify-self-end','min-w-0','shrink-0',

      // ----- Veľkosti / spacing
      'w-full','w-[44px]','w-[55%]','max-w-6xl','mx-auto',
      'h-8','h-11','h-28','h-56','h-80','h-[1px]',
      'px-3','px-4','px-5','px-6','py-2','py-3','py-4','py-10','mt-1','mt-2','mt-3','mt-4','mt-6','mt-10','mt-12','my-10','gap-2','gap-3','gap-6','gap-8',

      // ----- Typografia
      'text-xs','text-sm','text-lg','text-xl','text-2xl','text-3xl',
      'font-medium','font-semibold','leading-relaxed','tracking-tight',
      'whitespace-nowrap',

      // ----- Cards / vizuál
      'rounded-xl','rounded-2xl','ring-1','ring-black/5','shadow-sm',
      'overflow-hidden','object-cover','relative','absolute','inset-x-0','bottom-0',
      'backdrop-blur','group','group-hover:scale-105','transition-transform','duration-300',

      // ----- Farby (aj arbitrary + opacita)
      'bg-white','bg-white/90','bg-primary/5','bg-primary/10',
      'text-primary','bg-gradient-to-r','from-primary/60','to-transparent',
      'border','border-[#ECECEC]','border-primary/20',
      'text-[#2C2C2C]','text-[#1C1C1C]','text-[#2C2C2C]/70','text-[#2C2C2C]/80','text-[#2C2C2C]/30',
      'bg-green-100','text-green-700',
      

      // ----- Utility, ktoré sa často strácajú v CMS
      'hidden','md:inline-flex','sm:hidden'
    ],
  plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
  // Examples for excluding patterns from purge
  content: [
    // this theme's phtml and layout XML files
    '../../**/*.phtml',
    '../../*/layout/*.xml',
    '../../*/page_layout/override/base/*.xml',
    // parent theme in Vendor (if this is a child-theme)
    '../../../../../../../vendor/hyva-themes/magento2-default-theme/**/*.phtml',
    //'../../../../../../../vendor/hyva-themes/magento2-default-theme/*/layout/*.xml',
    //'../../../../../../../vendor/hyva-themes/magento2-default-theme/*/page_layout/override/base/*.xml',
    // app/code phtml files (if need tailwind classes from app/code modules)
    //'../../../../../../../app/code/**/*.phtml',
  ]
});
