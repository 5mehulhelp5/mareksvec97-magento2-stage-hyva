import { Search, Menu, Shield, Headphones, CheckCircle2, Users, PlayCircle, ChevronDown, Table2, Armchair, Grid3x3, BedDouble, Image as ImageIcon, Sparkles, Factory } from "lucide-react";
import { Button } from "./ui/button";
import { useState, useEffect, useRef } from "react";
import { Link } from "react-router-dom";
import { Minicart } from "./Minicart";
import logoImg from "figma:asset/5ded09a0be0e822261ee714fc9bfbe851ae499b9.png";
import flagSK from "figma:asset/6bb0ad4cade0d9b52949068ac551cfdca9dc14f1.png";
import legTrapez from "figma:asset/ed8f36ae2fe0dd7c1ceb951d5664bd8fc7c2f1ea.png";
import legStraight from "figma:asset/bf73ec76fd77d096947bb87e2fd1dec2a4aa2588.png";
import legX from "figma:asset/0cccdb2ea6d2fa9080d2621a8e86e64e0867fc22.png";
import legSpider from "figma:asset/9ab8cdf79d0a48396c1521467bc8e17374a8233d.png";
import legHairpin from "figma:asset/eff5a502f8be9012ff3a694d0b59c3dbc398424a.png";
import legV from "figma:asset/47a5db653d2f6acae5585aea60f8a35ceef2750e.png";
import svgPaths from "../../imports/svg-rrewuqmewt";
import { CartIcon } from "./icons/CartIcon";

// Hyva-style icons
function IconOutlineSearch({ className = "w-5 h-5" }: { className?: string }) {
  return (
    <div className={className}>
      <svg className="block size-full" fill="none" preserveAspectRatio="none" viewBox="0 0 24 24">
        <path d={svgPaths.p1e319980} stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" />
      </svg>
    </div>
  );
}

function IconOutlineUserCircle({ className = "w-5 h-5" }: { className?: string }) {
  return (
    <div className={className}>
      <svg className="block size-full" fill="none" preserveAspectRatio="none" viewBox="0 0 24 24">
        <path d={svgPaths.p2d8c3c20} stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" />
      </svg>
    </div>
  );
}

export function Header() {
  const [isMinicartOpen, setIsMinicartOpen] = useState(false);
  const [isDropdownOpen, setIsDropdownOpen] = useState(false);
  const [isLanguageDropdownOpen, setIsLanguageDropdownOpen] = useState(false);

  const languages = [
    { code: 'sk', name: 'Slovensko', domain: 'topfero.sk', flag: 'https://dev.topfero.sk/static/frontend/BigConnect/Hyva-Starter/sk_SK/images/flags/sk1.svg' },
    { code: 'cz', name: 'Česko', domain: 'topfero.cz', flag: 'https://dev.topfero.sk/static/frontend/BigConnect/Hyva-Starter/sk_SK/images/flags/cz1.svg' },
    { code: 'de', name: 'Deutschland', domain: 'topfero.de', flag: 'https://flagcdn.com/w40/de.png' },
    { code: 'at', name: 'Österreich', domain: 'topfero.at', flag: 'https://flagcdn.com/w40/at.png' },
    { code: 'hu', name: 'Magyarország', domain: 'topfero.hu', flag: 'https://flagcdn.com/w40/hu.png' },
    { code: 'pl', name: 'Polska', domain: 'topfero.pl', flag: 'https://flagcdn.com/w40/pl.png' },
  ];

  const dropdownRef = useRef(null);

  useEffect(() => {
    const currentRef = dropdownRef.current;
    const handleClickOutside = (event) => {
      if (currentRef && !currentRef.contains(event.target)) {
        setIsDropdownOpen(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => {
      document.removeEventListener('mousedown', handleClickOutside);
    };
  }, []);

  return (
    <header className="sticky top-0 bg-white border-b border-gray-200 z-50 shadow-sm">
      {/* Top bar with trust indicators - Desktop (1280px+) */}
      <div className="py-2 hidden xl:block" style={{ backgroundColor: '#7e9b84', maxHeight: '48px' }}>
        <div className="max-w-7xl mx-auto px-4 flex items-center justify-between text-sm text-white h-full">
          <div className="flex items-center gap-6">
            <div className="flex items-center gap-2">
              <Shield className="w-4 h-4 fill-white" />
              <span>Vyrobené na Slovensku</span>
            </div>
            <div className="flex items-center gap-2">
              <Headphones className="w-4 h-4" />
              <span>Kompletné poradenstvo</span>
            </div>
            <div className="flex items-center gap-2">
              <CheckCircle2 className="w-4 h-4" />
              <span>Kvalitné spracovanie</span>
            </div>
            <div className="flex items-center gap-2">
              <Users className="w-4 h-4" />
              <span>Viac ako 2000 spokojných zákazníkov</span>
            </div>
          </div>
          <a href="#" className="flex items-center gap-2 px-3 py-1.5 bg-white/20 hover:bg-white/30 rounded-lg transition-colors whitespace-nowrap">
            <PlayCircle className="w-4 h-4" />
            <span>Pozrite si našu výrobu</span>
          </a>
        </div>
      </div>

      {/* Top bar with trust indicators - Tablet (768px - 1279px) - Static compact layout */}
      <div className="py-2 hidden md:flex xl:hidden justify-center" style={{ backgroundColor: '#7e9b84' }}>
        <div className="flex items-center gap-3 text-xs text-white px-4">
          <div className="flex items-center gap-1.5">
            <Shield className="w-3.5 h-3.5 fill-white" />
            <span>Vyrobené na Slovensku</span>
          </div>
          <div className="flex items-center gap-1.5">
            <Headphones className="w-3.5 h-3.5" />
            <span>Kompletné poradenstvo</span>
          </div>
          <div className="flex items-center gap-1.5">
            <CheckCircle2 className="w-3.5 h-3.5" />
            <span>Kvalitné spracovanie</span>
          </div>
          <div className="flex items-center gap-1.5">
            <Users className="w-3.5 h-3.5" />
            <span>Viac ako 2000 spokojných zákazníkov</span>
          </div>
        </div>
      </div>

      {/* Top bar with trust indicators - Mobile (below 768px) with scrolling animation */}
      <div className="py-2 md:hidden overflow-hidden relative" style={{ backgroundColor: '#7e9b84' }}>
        <div className="flex animate-scroll">
          <div className="flex items-center gap-6 px-4 text-xs text-white whitespace-nowrap">
            <div className="flex items-center gap-2">
              <Shield className="w-3.5 h-3.5 fill-white" />
              <span>Vyrobené na Slovensku</span>
            </div>
            <div className="flex items-center gap-2">
              <Headphones className="w-3.5 h-3.5" />
              <span>Kompletné poradenstvo</span>
            </div>
            <div className="flex items-center gap-2">
              <CheckCircle2 className="w-3.5 h-3.5" />
              <span>Kvalitné spracovanie</span>
            </div>
            <div className="flex items-center gap-2">
              <Users className="w-3.5 h-3.5" />
              <span>Viac ako 2000 spokojných zákazníkov</span>
            </div>
            <div className="flex items-center gap-2">
              <PlayCircle className="w-3.5 h-3.5" />
              <span>Pozrite si našu výrobu</span>
            </div>
          </div>
          {/* Duplicate for seamless loop */}
          <div className="flex items-center gap-6 px-4 text-xs text-white whitespace-nowrap">
            <div className="flex items-center gap-2">
              <Shield className="w-3.5 h-3.5 fill-white" />
              <span>Vyrobené na Slovensku</span>
            </div>
            <div className="flex items-center gap-2">
              <Headphones className="w-3.5 h-3.5" />
              <span>Kompletné poradenstvo</span>
            </div>
            <div className="flex items-center gap-2">
              <CheckCircle2 className="w-3.5 h-3.5" />
              <span>Kvalitné spracovanie</span>
            </div>
            <div className="flex items-center gap-2">
              <Users className="w-3.5 h-3.5" />
              <span>Viac ako 2000 spokojných zákazníkov</span>
            </div>
            <div className="flex items-center gap-2">
              <PlayCircle className="w-3.5 h-3.5" />
              <span>Pozrite si našu výrobu</span>
            </div>
          </div>
        </div>
      </div>

      {/* Main header */}
      <div className="max-w-7xl mx-auto px-4 py-3 md:py-4">
        <div className="flex items-center justify-between gap-3 md:gap-6">
          {/* Left side: Mobile Hamburger + Logo */}
          <div className="flex items-center gap-3">
            <Button variant="ghost" size="icon" className="md:hidden p-2 -ml-2">
              <Menu className="w-6 h-6" />
            </Button>

            {/* Logo - left aligned on mobile and desktop */}
            <a href="/" className="flex-shrink-0">
              <img src={logoImg} alt="TOPFEO" className="h-7 md:h-10 w-auto" />
            </a>
          </div>
          
          {/* Desktop: Search bar */}
          <div className="hidden md:flex items-center gap-2 bg-gray-50 px-4 py-2.5 rounded-lg flex-1 max-w-md border border-gray-200">
            <input 
              type="text" 
              placeholder="Hľadať celý obchod tu..."
              className="bg-transparent border-none outline-none text-sm w-full placeholder:text-gray-400"
            />
            <IconOutlineSearch className="w-5 h-5 text-gray-400" />
          </div>

          {/* Right side actions */}
          <div className="flex items-center gap-2 md:gap-3">
            {/* Mobile: Language selector - icon only */}
            <div className="relative md:hidden">
              <button 
                className="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors" 
                onClick={() => setIsLanguageDropdownOpen(!isLanguageDropdownOpen)}
              >
                <img src={flagSK} alt="SK" className="w-5 h-5 rounded-full object-cover" />
              </button>

              {/* Language Dropdown for mobile */}
              {isLanguageDropdownOpen && (
                <>
                  <div 
                    className="fixed inset-0 z-40" 
                    onClick={() => setIsLanguageDropdownOpen(false)}
                  />
                  <div className="absolute top-full right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-200 py-2 z-50 overflow-hidden">
                    <div className="px-3 py-2 border-b border-gray-100">
                      <p className="text-xs font-semibold text-gray-500 uppercase tracking-wide">Vyberte krajinu</p>
                    </div>
                    <div className="py-1">
                      {languages.map((language) => (
                        <a 
                          href={`https://${language.domain}`} 
                          key={language.code} 
                          className="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition-colors group"
                          onMouseEnter={(e) => {
                            e.currentTarget.style.backgroundColor = '#f0f4f1';
                          }}
                          onMouseLeave={(e) => {
                            e.currentTarget.style.backgroundColor = '';
                          }}
                        >
                          <img 
                            src={language.flag} 
                            alt={language.name} 
                            className="w-6 h-6 rounded-full object-cover shadow-sm ring-1 ring-gray-200" 
                          />
                          <div className="flex-1">
                            <div className="font-medium text-gray-900 text-sm">{language.name}</div>
                            <div className="text-xs text-gray-500">{language.domain}</div>
                          </div>
                          <ChevronDown 
                            className="w-4 h-4 -rotate-90 text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity" 
                            onMouseEnter={(e) => e.currentTarget.style.color = '#7e9b84'}
                            onMouseLeave={(e) => e.currentTarget.style.color = ''}
                          />
                        </a>
                      ))}
                    </div>
                  </div>
                </>
              )}
            </div>

            {/* Desktop: Language selector - full */}
            <div className="relative hidden md:block lg:block">
              <button 
                className="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors border border-gray-200" 
                onClick={() => setIsLanguageDropdownOpen(!isLanguageDropdownOpen)}
              >
                <img src={flagSK} alt="SK" className="w-5 h-5 rounded-full object-cover" />
                <span className="text-gray-600 text-sm font-medium">topfero.sk</span>
                <ChevronDown className={`w-3.5 h-3.5 text-gray-500 transition-transform ${isLanguageDropdownOpen ? 'rotate-180' : ''}`} />
              </button>

              {/* Language Dropdown */}
              {isLanguageDropdownOpen && (
                <>
                  {/* Backdrop to close on outside click */}
                  <div 
                    className="fixed inset-0 z-40" 
                    onClick={() => setIsLanguageDropdownOpen(false)}
                  />
                  
                  {/* Dropdown menu */}
                  <div className="absolute top-full right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-200 py-2 z-50 overflow-hidden">
                    <div className="px-3 py-2 border-b border-gray-100">
                      <p className="text-xs font-semibold text-gray-500 uppercase tracking-wide">Vyberte krajinu</p>
                    </div>
                    <div className="py-1">
                      {languages.map((language) => (
                        <a 
                          href={`https://${language.domain}`} 
                          key={language.code} 
                          className="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition-colors group"
                          onMouseEnter={(e) => {
                            e.currentTarget.style.backgroundColor = '#f0f4f1';
                          }}
                          onMouseLeave={(e) => {
                            e.currentTarget.style.backgroundColor = '';
                          }}
                        >
                          <img 
                            src={language.flag} 
                            alt={language.name} 
                            className="w-6 h-6 rounded-full object-cover shadow-sm ring-1 ring-gray-200" 
                          />
                          <div className="flex-1">
                            <div className="font-medium text-gray-900 text-sm">{language.name}</div>
                            <div className="text-xs text-gray-500">{language.domain}</div>
                          </div>
                          <ChevronDown 
                            className="w-4 h-4 -rotate-90 text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity" 
                            style={{ ['--hover-color' as string]: '#7e9b84' }}
                            onMouseEnter={(e) => e.currentTarget.style.color = '#7e9b84'}
                            onMouseLeave={(e) => e.currentTarget.style.color = ''}
                          />
                        </a>
                      ))}
                    </div>
                  </div>
                </>
              )}
            </div>
            
            {/* Mobile: Phone icon only */}
            <a href="tel:+421910123456" className="md:hidden p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor" width="20" height="20" aria-hidden="true" style={{ color: '#7e9b84' }}>
                <path strokeLinecap="round" strokeLinejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
              </svg>
            </a>

            {/* Desktop: Phone with text */}
            <a href="tel:+421910123456" className="hidden md:flex lg:flex items-center gap-2.5 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors border border-gray-200">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor" width="18" height="18" aria-hidden="true" style={{ color: '#7e9b84' }}>
                <path strokeLinecap="round" strokeLinejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
              </svg>
              <span className="text-gray-800 font-semibold text-sm">+421 910 123 456</span>
            </a>
            
            {/* User icon - visible on mobile */}
            <Button variant="ghost" size="icon" className="text-gray-700 bg-gray-100 hover:bg-gray-200 p-2">
              <IconOutlineUserCircle className="w-5 h-5 text-gray-700" />
            </Button>
            
            {/* Cart button */}
            <div className="relative">
              <Button 
                className="relative text-white p-2 md:px-4 md:py-2 md:gap-2.5 shadow-md hover:shadow-lg transition-all" 
                style={{ backgroundColor: '#7e9b84' }} 
                onMouseEnter={(e) => e.currentTarget.style.backgroundColor = '#6d8a73'} 
                onMouseLeave={(e) => e.currentTarget.style.backgroundColor = '#7e9b84'} 
                onClick={() => setIsMinicartOpen(!isMinicartOpen)}
              >
                <CartIcon className="w-5 h-5 text-white" />
                <span className="hidden md:inline font-semibold text-sm">438,00 €</span>
                <span className="absolute -top-1 -right-1 md:-top-2 md:-right-2 bg-red-600 text-white text-xs rounded-full w-5 h-5 md:w-6 md:h-6 flex items-center justify-center font-semibold border-2" style={{ animation: 'pulse-border 2s ease-in-out infinite' }}>
                  2
                </span>
              </Button>
            </div>
          </div>
        </div>
      </div>

      {/* Mobile: Search bar below header */}
      <div className="md:hidden px-4 py-3 border-t border-gray-100">
        <div className="flex items-center gap-2 bg-gray-50 px-4 py-2.5 rounded-lg border border-gray-200">
          <input 
            type="text" 
            placeholder="Hľadať celý obchod tu..."
            className="bg-transparent border-none outline-none text-sm w-full placeholder:text-gray-400"
          />
          <IconOutlineSearch className="w-5 h-5 text-gray-400" />
        </div>
      </div>

      {/* Desktop Navigation Menu - 3rd layer (hidden on mobile) */}
      <nav className="hidden md:block border-t border-gray-200 bg-gray-50">
        <div className="max-w-7xl mx-auto px-4">
          <div className="flex items-center justify-between py-3">
            <nav className="hidden md:flex items-center gap-6 lg:gap-8">
              <div 
                className="relative"
                onMouseEnter={() => setIsDropdownOpen(true)}
                onMouseLeave={() => setIsDropdownOpen(false)}
                ref={dropdownRef}
              >
                <Link to="/stolove-nohy" className="flex items-center gap-2 text-gray-700 hover:text-black transition-colors group">
                  <Table2 className="w-4 h-4 lg:w-5 lg:h-5" style={{ color: '#7e9b84' }} />
                  <span className="font-medium text-sm lg:text-base">Stolové nohy</span>
                  <ChevronDown className={`w-3.5 h-3.5 lg:w-4 lg:h-4 transition-transform ${isDropdownOpen ? 'rotate-180' : ''}`} />
                </Link>
                
                {/* Dropdown menu */}
                {isDropdownOpen && (
                  <div className="absolute top-full left-0 mt-2 w-[480px] bg-white rounded-lg shadow-xl border border-gray-200 p-4 z-50">
                    <div className="grid grid-cols-2 gap-2">
                      <a href="#" className="flex items-center gap-3 px-3 py-2.5 hover:bg-gray-50 rounded-lg transition-colors">
                        <div className="w-12 h-12 bg-gray-100 rounded-md flex items-center justify-center p-1.5 flex-shrink-0">
                          <img src={legTrapez} alt="Trapézové nohy" className="w-full h-full object-contain" />
                        </div>
                        <span className="font-medium text-gray-900 text-sm">Trapézové nohy</span>
                      </a>
                      <a href="#" className="flex items-center gap-3 px-3 py-2.5 hover:bg-gray-50 rounded-lg transition-colors">
                        <div className="w-12 h-12 bg-gray-100 rounded-md flex items-center justify-center p-1.5 flex-shrink-0">
                          <img src={legX} alt="X-tvar nohy" className="w-full h-full object-contain" />
                        </div>
                        <span className="font-medium text-gray-900 text-sm">X-tvar nohy</span>
                      </a>
                      <a href="#" className="flex items-center gap-3 px-3 py-2.5 hover:bg-gray-50 rounded-lg transition-colors">
                        <div className="w-12 h-12 bg-gray-100 rounded-md flex items-center justify-center p-1.5 flex-shrink-0">
                          <img src={legStraight} alt="Rovné nohy" className="w-full h-full object-contain" />
                        </div>
                        <span className="font-medium text-gray-900 text-sm">Rovné nohy</span>
                      </a>
                      <a href="#" className="flex items-center gap-3 px-3 py-2.5 hover:bg-gray-50 rounded-lg transition-colors">
                        <div className="w-12 h-12 bg-gray-100 rounded-md flex items-center justify-center p-1.5 flex-shrink-0">
                          <img src={legSpider} alt="Pavúkové nohy" className="w-full h-full object-contain" />
                        </div>
                        <span className="font-medium text-gray-900 text-sm">Pavúkové nohy</span>
                      </a>
                      <a href="#" className="flex items-center gap-3 px-3 py-2.5 hover:bg-gray-50 rounded-lg transition-colors">
                        <div className="w-12 h-12 bg-gray-100 rounded-md flex items-center justify-center p-1.5 flex-shrink-0">
                          <img src={legHairpin} alt="Hairpin nohy" className="w-full h-full object-contain" />
                        </div>
                        <span className="font-medium text-gray-900 text-sm">Hairpin nohy</span>
                      </a>
                      <a href="#" className="flex items-center gap-3 px-3 py-2.5 hover:bg-gray-50 rounded-lg transition-colors">
                        <div className="w-12 h-12 bg-gray-100 rounded-md flex items-center justify-center p-1.5 flex-shrink-0">
                          <img src={legV} alt="V-tvar nohy" className="w-full h-full object-contain" />
                        </div>
                        <span className="font-medium text-gray-900 text-sm">V-tvar nohy</span>
                      </a>
                    </div>
                    <div className="border-t border-gray-100 mt-3 pt-3">
                      <a href="#" className="flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium hover:bg-gray-50 rounded-lg transition-colors" style={{ color: '#7e9b84' }}>
                        <span>Zobraziť všetky stolové nohy</span>
                        <ChevronDown className="w-4 h-4 -rotate-90" />
                      </a>
                    </div>
                  </div>
                )}
              </div>
              <a href="#" className="flex items-center gap-2 text-gray-700 hover:text-black transition-colors">
                <Armchair className="w-4 h-4 lg:w-5 lg:h-5" style={{ color: '#7e9b84' }} />
                <span className="font-medium text-sm lg:text-base">Nohy na lavice</span>
              </a>
              <a href="#" className="flex items-center gap-2 text-gray-700 hover:text-black transition-colors group">
                <Grid3x3 className="w-4 h-4 lg:w-5 lg:h-5" style={{ color: '#7e9b84' }} />
                <span className="font-medium text-sm lg:text-base">Kovové regály</span>
                <ChevronDown className="w-3.5 h-3.5 lg:w-4 lg:h-4 group-hover:translate-y-0.5 transition-transform" />
              </a>
              <a href="#" className="flex items-center gap-2 text-gray-700 hover:text-black transition-colors">
                <BedDouble className="w-4 h-4 lg:w-5 lg:h-5" style={{ color: '#7e9b84' }} />
                <span className="font-medium text-sm lg:text-base">Kovové postele</span>
              </a>
              <Link 
                to="/galeria" 
                className="flex items-center gap-2 px-4 py-2 rounded-lg transition-all group relative overflow-hidden"
                style={{ 
                  background: 'linear-gradient(135deg, #7e9b84 0%, #6d8a73 100%)',
                }}
                onMouseEnter={(e) => {
                  e.currentTarget.style.background = 'linear-gradient(135deg, #6d8a73 0%, #5d7a63 100%)';
                }}
                onMouseLeave={(e) => {
                  e.currentTarget.style.background = 'linear-gradient(135deg, #7e9b84 0%, #6d8a73 100%)';
                }}
              >
                <Sparkles className="w-4 h-4 lg:w-5 lg:h-5 text-white animate-pulse" />
                <span className="font-semibold text-sm lg:text-base text-white">Inšpirácie</span>
                <div className="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
              </Link>
              <Link to="/o-nas" className="flex items-center gap-2 text-gray-700 hover:text-black transition-colors">
                <Factory className="w-4 h-4 lg:w-5 lg:h-5" style={{ color: '#7e9b84' }} />
                <span className="font-medium text-sm lg:text-base">O nás</span>
              </Link>
            </nav>

            {/* Removed button here */}
            <div></div>
          </div>
        </div>
      </nav>

      {/* Minicart */}
      <Minicart isOpen={isMinicartOpen} onClose={() => setIsMinicartOpen(false)} />
    </header>
  );
}