// tailwind.config.js  (CELÝ SÚBOR – upravený na globálne farby + gradienty bez inline hexov)

const { spacing } = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors');
const hyvaModules = require('@hyva-themes/hyva-modules');

const TOKENS = {
  primary: {
    lighter: '#8faf96',
    DEFAULT: '#7e9b84',
    darker: '#6d8a73',
    darkest: '#5d7a63', // ✅ doplnené (aby sme nemali nikde “cudzí” hex)
  },
  secondary: {
    lighter: '#f9fafb',
    DEFAULT: '#e5e7eb',
    darker: '#d1d5db',
  },
  background: {
    lighter: '#ffffff',
    DEFAULT: '#f0f4f1',
    darker: '#e5e7eb',
  },
  container: {
    lighter: '#ffffff',
    DEFAULT: '#ffffff',
    darker: '#f9fafb',
  },
  border: {
    lighter: '#f3f4f6',
    DEFAULT: '#e5e7eb',
    darker: '#d1d5db',
  },
  text: {
    lighter: '#334155',
    DEFAULT: '#111827',
    darker: '#0f172a',
  },
  mutedText: {
    DEFAULT: '#6b7280',
  },
};

module.exports = hyvaModules.mergeTailwindConfig({
  theme: {
    screens: {
      sm: '640px',
      md: '768px',
      lg: '1024px',
      xl: '1280px',
      '2xl': '1536px',
    },

    container: {
      center: true,
      padding: spacing['6'],
      screens: {
        '2xl': '1280px',
      },
    },

    extend: {
      fontFamily: {
        sans: ['Jost', 'Helvetica Neue', 'Arial', 'sans-serif'],
      },

      // ✅ globálne gradienty (použijeme v šablónach ako bg-inspiration)
      backgroundImage: {
        inspiration: `linear-gradient(135deg, ${TOKENS.primary.DEFAULT} 0%, ${TOKENS.primary.darker} 100%)`,
        'inspiration-hover': `linear-gradient(135deg, ${TOKENS.primary.darker} 0%, ${TOKENS.primary.darkest} 100%)`,
      },

      colors: {
        primary: TOKENS.primary,
        secondary: TOKENS.secondary,
        background: TOKENS.background,
        container: TOKENS.container,
        border: TOKENS.border,

        text: TOKENS.text,
        muted: TOKENS.mutedText,

        green: colors.emerald,
        yellow: colors.amber,
        purple: colors.violet,
        orange: colors.orange,
        red: { ...colors.red, DEFAULT: colors.red['500'] },
      },

      textColor: {
        primary: TOKENS.primary,
        secondary: TOKENS.secondary,
        text: TOKENS.text,
        muted: TOKENS.mutedText,
      },

      backgroundColor: {
        primary: TOKENS.primary,
        secondary: TOKENS.secondary,
        background: TOKENS.background,
        container: TOKENS.container,
      },

      borderColor: {
        primary: TOKENS.primary,
        secondary: TOKENS.secondary,
        border: TOKENS.border,
        container: {
          lighter: '#f3f4f6',
          DEFAULT: '#e7e7e7',
          darker: '#b6b6b6',
        },
      },

      minHeight: {
        a11y: spacing['11'],
        'screen-25': '25vh',
        'screen-50': '50vh',
        'screen-75': '75vh',
      },
      maxHeight: {
        'screen-25': '25vh',
        'screen-50': '50vh',
        'screen-75': '75vh',
      },
    },
  },

  safelist: [
    'grid','flex','items-center','justify-between','flex-wrap','justify-center',
    'grid-cols-1','sm:grid-cols-2','lg:grid-cols-4','sm:grid-cols-3','lg:grid-cols-6',
    'grid-cols-[44px,1fr]','sm:grid-cols-[44px,1fr,auto]',
    'col-span-2','sm:col-span-1','sm:justify-self-end','min-w-0','shrink-0',

    'w-full','w-[44px]','w-[55%]','max-w-6xl','mx-auto',
    'h-8','h-11','h-28','h-56','h-80','h-[1px]',
    'px-3','px-4','px-5','px-6','py-2','py-3','py-4','py-10',
    'mt-1','mt-2','mt-3','mt-4','mt-6','mt-10','mt-12','my-10',
    'gap-2','gap-3','gap-6','gap-8',

    'text-xs','text-sm','text-lg','text-xl','text-2xl','text-3xl',
    'font-medium','font-semibold','leading-relaxed','tracking-tight',
    'whitespace-nowrap',

    'rounded-xl','rounded-2xl','ring-1','ring-black/5','shadow-sm',
    'overflow-hidden','object-cover','relative','absolute','inset-x-0','bottom-0',
    'backdrop-blur','group','group-hover:scale-105','transition-transform','duration-300',

    'bg-white','bg-white/90','bg-primary/5','bg-primary/10',
    'text-primary','bg-gradient-to-r','from-primary/60','to-transparent',
    'border','border-[#ECECEC]','border-primary/20',
    'text-[#2C2C2C]','text-[#1C1C1C]','text-[#2C2C2C]/70','text-[#2C2C2C]/80','text-[#2C2C2C]/30',
    'bg-green-100','text-green-700',

    'hidden','md:inline-flex','sm:hidden',

    // ✅ pre gradient “Inšpirácie”
    'bg-inspiration',
    'hover:bg-inspiration-hover',
  ],

  plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],

  content: [
    '../../**/*.phtml',
    '../../*/layout/*.xml',
    '../../*/page_layout/override/base/*.xml',
    '../../../../../../../vendor/hyva-themes/magento2-default-theme/**/*.phtml',
    '../../../../../../../app/code/**/view/frontend/**/*.phtml',
    '../../../../../../../app/code/**/view/frontend/**/*.xml',
  ],
});
