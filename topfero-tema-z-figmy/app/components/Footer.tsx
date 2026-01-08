import { Facebook, Instagram, Mail, Phone, MapPin, MessageCircle, Youtube } from "lucide-react";
import logoWhite from "figma:asset/1e4f7f97b6d6b2f3da62a85b0ee4821044524af0.png";

export function Footer() {
  return (
    <footer className="bg-black text-white" role="contentinfo">
      {/* Main footer */}
      <div className="max-w-7xl mx-auto px-4 py-10">
        <div className="grid grid-cols-1 gap-8 lg:grid-cols-4 lg:gap-10">
          {/* Col 1: Logo + About + Social */}
          <div>
            <a href="https://topfero.sk/" className="inline-block">
              <img 
                src={logoWhite} 
                alt="TOPFERO" 
                className="h-10 w-auto" 
                width="180" 
                height="40" 
                loading="lazy"
              />
            </a>
            <p className="mt-5 max-w-[60ch] leading-relaxed text-white/80">
              TOPFERO – Prémiové stolové nohy a domové čísla na mieru. Laserové gravírovanie a práškové lakovanie. Vyrobené s precíznosťou a vášňou na Slovensku.
            </p>
            
            {/* Social icons */}
            <nav className="mt-6" aria-label="Sociálne siete">
              <ul className="flex items-center gap-3">
                <li>
                  <a 
                    href="#" 
                    className="group inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/10 ring-1 ring-white/20 transition hover:bg-white/20 hover:ring-white/30 focus:outline-none focus-visible:ring-2 focus-visible:ring-white" 
                    target="_blank" 
                    rel="noopener" 
                    aria-label="Facebook"
                    style={{ ['--hover-bg' as string]: '#7e9b84' }}
                    onMouseEnter={(e) => e.currentTarget.style.backgroundColor = '#7e9b84'}
                    onMouseLeave={(e) => e.currentTarget.style.backgroundColor = ''}
                  >
                    <Facebook className="h-5 w-5 text-white transition group-hover:scale-110" />
                  </a>
                </li>
                <li>
                  <a 
                    href="#" 
                    className="group inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/10 ring-1 ring-white/20 transition hover:bg-white/20 hover:ring-white/30 focus:outline-none focus-visible:ring-2 focus-visible:ring-white" 
                    target="_blank" 
                    rel="noopener" 
                    aria-label="Instagram"
                    onMouseEnter={(e) => e.currentTarget.style.backgroundColor = '#7e9b84'}
                    onMouseLeave={(e) => e.currentTarget.style.backgroundColor = ''}
                  >
                    <Instagram className="h-5 w-5 text-white transition group-hover:scale-110" />
                  </a>
                </li>
                <li>
                  <a 
                    href="#" 
                    className="group inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/10 ring-1 ring-white/20 transition hover:bg-white/20 hover:ring-white/30 focus:outline-none focus-visible:ring-2 focus-visible:ring-white" 
                    target="_blank" 
                    rel="noopener" 
                    aria-label="YouTube"
                    onMouseEnter={(e) => e.currentTarget.style.backgroundColor = '#7e9b84'}
                    onMouseLeave={(e) => e.currentTarget.style.backgroundColor = ''}
                  >
                    <Youtube className="h-5 w-5 text-white transition group-hover:scale-110" />
                  </a>
                </li>
              </ul>
            </nav>
          </div>

          {/* Col 2: Informácie */}
          <div>
            <h3 className="m-0 font-bold leading-tight text-lg lg:text-xl mb-5">Informácie</h3>
            <ul className="space-y-2.5">
              <li>
                <a 
                  href="/obchodne-podmienky" 
                  className="text-white/80 hover:text-white transition-colors"
                  onMouseEnter={(e) => e.currentTarget.style.color = '#7e9b84'}
                  onMouseLeave={(e) => e.currentTarget.style.color = ''}
                >
                  Obchodné podmienky
                </a>
              </li>
              <li>
                <a 
                  href="/ochrana-osobnych-udajov" 
                  className="text-white/80 hover:text-white transition-colors"
                  onMouseEnter={(e) => e.currentTarget.style.color = '#7e9b84'}
                  onMouseLeave={(e) => e.currentTarget.style.color = ''}
                >
                  Ochrana osobných údajov
                </a>
              </li>
              <li>
                <a 
                  href="/kontakt" 
                  className="text-white/80 hover:text-white transition-colors"
                  onMouseEnter={(e) => e.currentTarget.style.color = '#7e9b84'}
                  onMouseLeave={(e) => e.currentTarget.style.color = ''}
                >
                  Kontakt
                </a>
              </li>
              <li>
                <a 
                  href="/blog" 
                  className="text-white/80 hover:text-white transition-colors"
                  onMouseEnter={(e) => e.currentTarget.style.color = '#7e9b84'}
                  onMouseLeave={(e) => e.currentTarget.style.color = ''}
                >
                  Blog
                </a>
              </li>
              <li>
                <a 
                  href="/o-nas" 
                  className="text-white/80 hover:text-white transition-colors"
                  onMouseEnter={(e) => e.currentTarget.style.color = '#7e9b84'}
                  onMouseLeave={(e) => e.currentTarget.style.color = ''}
                >
                  O nás
                </a>
              </li>
            </ul>
          </div>

          {/* Col 3: Služby */}
          <div>
            <h3 className="m-0 font-bold leading-tight text-lg lg:text-xl mb-5">Služby</h3>
            <ul className="space-y-2.5">
              <li>
                <a 
                  href="/vyroba-na-mieru" 
                  className="text-white/80 hover:text-white transition-colors"
                  onMouseEnter={(e) => e.currentTarget.style.color = '#7e9b84'}
                  onMouseLeave={(e) => e.currentTarget.style.color = ''}
                >
                  Výroba na mieru
                </a>
              </li>
              <li>
                <a 
                  href="/faq" 
                  className="text-white/80 hover:text-white transition-colors"
                  onMouseEnter={(e) => e.currentTarget.style.color = '#7e9b84'}
                  onMouseLeave={(e) => e.currentTarget.style.color = ''}
                >
                  Často kladené otázky
                </a>
              </li>
              <li>
                <a 
                  href="/doprava-a-platba" 
                  className="text-white/80 hover:text-white transition-colors"
                  onMouseEnter={(e) => e.currentTarget.style.color = '#7e9b84'}
                  onMouseLeave={(e) => e.currentTarget.style.color = ''}
                >
                  Doprava a platba
                </a>
              </li>
              <li>
                <a 
                  href="/materialy-a-farby" 
                  className="text-white/80 hover:text-white transition-colors"
                  onMouseEnter={(e) => e.currentTarget.style.color = '#7e9b84'}
                  onMouseLeave={(e) => e.currentTarget.style.color = ''}
                >
                  Materiály a farby
                </a>
              </li>
            </ul>
          </div>

          {/* Col 4: Kontakt */}
          <div>
            <h3 className="m-0 font-bold leading-tight text-lg lg:text-xl mb-5">Kontakt</h3>
            <address className="not-italic text-white/80">
              <div className="mb-4 text-sm leading-relaxed">
                <div className="font-semibold text-white">BigConnect s.r.o.</div>
                <div>Duklianska 21</div>
                <div>08501 Bardejov, Slovensko</div>
              </div>
              
              {/* E-mail */}
              <div className="mt-4">
                <a 
                  href="mailto:info@topfero.com" 
                  className="group inline-flex items-center gap-2.5 hover:underline underline-offset-4 decoration-white/40 text-white/90 hover:text-white transition-colors"
                  onMouseEnter={(e) => e.currentTarget.style.color = '#7e9b84'}
                  onMouseLeave={(e) => e.currentTarget.style.color = ''}
                >
                  <span className="inline-grid size-9 place-items-center rounded-lg bg-white/10 ring-1 ring-white/20 group-hover:bg-white/15 transition-colors">
                    <Mail className="w-4 h-4" />
                  </span>
                  <span className="text-sm">info@topfero.com</span>
                </a>
              </div>
              
              {/* Telefón */}
              <div className="mt-3">
                <a 
                  href="tel:+421948950119" 
                  className="group inline-flex items-center gap-2.5 hover:underline underline-offset-4 decoration-white/40 text-white/90 hover:text-white transition-colors"
                  onMouseEnter={(e) => e.currentTarget.style.color = '#7e9b84'}
                  onMouseLeave={(e) => e.currentTarget.style.color = ''}
                >
                  <span className="inline-grid size-9 place-items-center rounded-lg bg-white/10 ring-1 ring-white/20 group-hover:bg-white/15 transition-colors">
                    <Phone className="w-4 h-4" />
                  </span>
                  <span className="text-sm">+421 948 950 119</span>
                </a>
              </div>
              
              {/* CTA */}
              <div className="mt-5">
                <a 
                  href="/kontakt" 
                  className="inline-flex items-center gap-2 rounded-lg px-4 py-2.5 ring-1 ring-white/25 transition font-medium text-sm"
                  style={{ backgroundColor: '#7e9b84', color: 'white' }}
                  onMouseEnter={(e) => e.currentTarget.style.backgroundColor = '#6d8a73'}
                  onMouseLeave={(e) => e.currentTarget.style.backgroundColor = '#7e9b84'}
                >
                  <MessageCircle className="w-4 h-4" />
                  <span>Napíšte nám</span>
                </a>
              </div>
            </address>
          </div>
        </div>

        {/* Language selector - Full width centered section */}
        <div className="mt-12 pt-8 border-t border-white/10">
          <nav aria-label="Jazyk / krajina">
            <div className="text-center">
              <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 max-w-4xl mx-auto">
                <a 
                  href="https://topfero.sk" 
                  hrefLang="sk" 
                  lang="sk" 
                  className="inline-flex items-center justify-center gap-2 rounded-lg px-3 py-2.5 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                  onMouseEnter={(e) => {
                    const span = e.currentTarget.querySelector('span');
                    if (span) span.style.color = '#7e9b84';
                  }}
                  onMouseLeave={(e) => {
                    const span = e.currentTarget.querySelector('span');
                    if (span) span.style.color = '';
                  }}
                >
                  <img 
                    src="https://dev.topfero.sk/static/frontend/BigConnect/Hyva-Starter/sk_SK/images/flags/sk1.svg" 
                    alt="Slovensko" 
                    className="h-5 w-5 rounded-full shadow-sm object-cover" 
                    loading="lazy"
                  />
                  <span className="text-sm text-white/90 font-semibold">topfero.sk</span>
                </a>
                <a 
                  href="https://topfero.cz" 
                  hrefLang="cs" 
                  lang="cs" 
                  className="inline-flex items-center justify-center gap-2 rounded-lg px-3 py-2.5 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                  onMouseEnter={(e) => {
                    const span = e.currentTarget.querySelector('span');
                    if (span) span.style.color = '#7e9b84';
                  }}
                  onMouseLeave={(e) => {
                    const span = e.currentTarget.querySelector('span');
                    if (span) span.style.color = '';
                  }}
                >
                  <img 
                    src="https://dev.topfero.sk/static/frontend/BigConnect/Hyva-Starter/sk_SK/images/flags/cz1.svg" 
                    alt="Česko" 
                    className="h-5 w-5 rounded-full shadow-sm object-cover" 
                    loading="lazy"
                  />
                  <span className="text-sm text-white/90 font-semibold">topfero.cz</span>
                </a>
                <a 
                  href="https://topfero.de" 
                  hrefLang="de" 
                  lang="de" 
                  className="inline-flex items-center justify-center gap-2 rounded-lg px-3 py-2.5 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                  onMouseEnter={(e) => {
                    const span = e.currentTarget.querySelector('span');
                    if (span) span.style.color = '#7e9b84';
                  }}
                  onMouseLeave={(e) => {
                    const span = e.currentTarget.querySelector('span');
                    if (span) span.style.color = '';
                  }}
                >
                  <img 
                    src="https://flagcdn.com/w40/de.png" 
                    alt="Deutschland" 
                    className="h-5 w-5 rounded-full shadow-sm object-cover" 
                    loading="lazy"
                  />
                  <span className="text-sm text-white/90 font-semibold">topfero.de</span>
                </a>
                <a 
                  href="https://topfero.at" 
                  hrefLang="de" 
                  lang="de" 
                  className="inline-flex items-center justify-center gap-2 rounded-lg px-3 py-2.5 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                  onMouseEnter={(e) => {
                    const span = e.currentTarget.querySelector('span');
                    if (span) span.style.color = '#7e9b84';
                  }}
                  onMouseLeave={(e) => {
                    const span = e.currentTarget.querySelector('span');
                    if (span) span.style.color = '';
                  }}
                >
                  <img 
                    src="https://flagcdn.com/w40/at.png" 
                    alt="Österreich" 
                    className="h-5 w-5 rounded-full shadow-sm object-cover" 
                    loading="lazy"
                  />
                  <span className="text-sm text-white/90 font-semibold">topfero.at</span>
                </a>
                <a 
                  href="https://topfero.hu" 
                  hrefLang="hu" 
                  lang="hu" 
                  className="inline-flex items-center justify-center gap-2 rounded-lg px-3 py-2.5 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                  onMouseEnter={(e) => {
                    const span = e.currentTarget.querySelector('span');
                    if (span) span.style.color = '#7e9b84';
                  }}
                  onMouseLeave={(e) => {
                    const span = e.currentTarget.querySelector('span');
                    if (span) span.style.color = '';
                  }}
                >
                  <img 
                    src="https://flagcdn.com/w40/hu.png" 
                    alt="Magyarország" 
                    className="h-5 w-5 rounded-full shadow-sm object-cover" 
                    loading="lazy"
                  />
                  <span className="text-sm text-white/90 font-semibold">topfero.hu</span>
                </a>
                <a 
                  href="https://topfero.pl" 
                  hrefLang="pl" 
                  lang="pl" 
                  className="inline-flex items-center justify-center gap-2 rounded-lg px-3 py-2.5 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                  onMouseEnter={(e) => {
                    const span = e.currentTarget.querySelector('span');
                    if (span) span.style.color = '#7e9b84';
                  }}
                  onMouseLeave={(e) => {
                    const span = e.currentTarget.querySelector('span');
                    if (span) span.style.color = '';
                  }}
                >
                  <img 
                    src="https://flagcdn.com/w40/pl.png" 
                    alt="Polska" 
                    className="h-5 w-5 rounded-full shadow-sm object-cover" 
                    loading="lazy"
                  />
                  <span className="text-sm text-white/90 font-semibold">topfero.pl</span>
                </a>
              </div>
            </div>
          </nav>
        </div>
      </div>

      {/* Bottom bar: copy + platobné ikony */}
      <div className="border-t border-white/15 mt-4">
        <div className="max-w-7xl mx-auto flex flex-col items-center justify-between gap-3 py-5 px-4 text-sm sm:flex-row">
          <div className="opacity-90 flex items-center gap-2">
            © 2025 Metalio — súčasť skupiny MetaloPro
            <img 
              src="https://dev.topfero.sk/static/frontend/BigConnect/Hyva-Starter/sk_SK/images/metalopro-badge.png" 
              alt="MetaloPro" 
              className="h-5 opacity-60 hover:opacity-90 transition" 
              loading="lazy"
            />
          </div>
          <div className="flex gap-3 flex-wrap items-center justify-center sm:justify-end" aria-label="Spôsoby platby">
            {/* Visa */}
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="32" fill="none" viewBox="0 0 48 32" role="img">
              <g clipPath="url(#a)">
                <path fill="#D9DFF7" d="M0 4a4 4 0 0 1 4-4h40a4 4 0 0 1 4 4v24a4 4 0 0 1-4 4H4a4 4 0 0 1-4-4z"></path>
                <path fill="#1434CB" d="M19.108 10.212 14.243 21.82H11.07l-2.393-9.263c-.146-.57-.272-.78-.714-1.02-.722-.391-1.913-.759-2.962-.987l.071-.337h5.109c.651 0 1.237.434 1.385 1.184l1.264 6.715 3.124-7.9zm12.435 7.817c.013-3.063-4.236-3.232-4.206-4.6.009-.417.405-.86 1.273-.973.43-.056 1.616-.099 2.96.52l.527-2.461A8.1 8.1 0 0 0 29.29 10c-2.968 0-5.056 1.578-5.074 3.837-.019 1.67 1.491 2.603 2.629 3.158 1.17.569 1.563.934 1.558 1.443-.008.778-.933 1.122-1.798 1.135-1.509.024-2.385-.408-3.083-.733l-.544 2.543c.702.322 1.997.603 3.339.617 3.154 0 5.218-1.558 5.227-3.97m7.837 3.79h2.777l-2.424-11.607H37.17c-.577 0-1.063.336-1.278.852L31.387 21.82h3.152l.626-1.734h3.852zm-3.35-4.113 1.58-4.358.91 4.358zm-12.632-7.494L20.915 21.82h-3.002l2.483-11.607z"></path>
              </g>
              <defs><clipPath id="a"><path fill="#fff" d="M0 0h48v32H0z"></path></clipPath></defs>
              <title>Visa</title>
            </svg>
            
            {/* Mastercard */}
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="32" fill="none" viewBox="0 0 48 32" role="img">
              <g clipPath="url(#a_2)">
                <path fill="#F1EFEB" d="M0 4a4 4 0 0 1 4-4h40a4 4 0 0 1 4 4v24a4 4 0 0 1-4 4H4a4 4 0 0 1-4-4z"></path>
                <path fill="#FF5F00" d="M28.376 8.139h-8.75v15.725h8.75z"></path>
                <path fill="#EB001B" d="M20.181 16a9.98 9.98 0 0 1 3.82-7.861A9.96 9.96 0 0 0 17.82 6c-5.522 0-10 4.478-10 10s4.478 10 10 10A9.96 9.96 0 0 0 24 23.861 9.98 9.98 0 0 1 20.182 16"></path>
                <path fill="#F79E1B" d="M40.178 16c0 5.522-4.477 10-10 10-2.333 0-4.477-.8-6.18-2.139A9.98 9.98 0 0 0 27.818 16a9.98 9.98 0 0 0-3.82-7.861A9.96 9.96 0 0 1 30.178 6c5.522 0 10 4.478 10 10"></path>
              </g>
              <defs><clipPath id="a_2"><path fill="#fff" d="M0 0h48v32H0z"></path></clipPath></defs>
              <title>Mastercard</title>
            </svg>
            
            {/* PayPal */}
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="32" fill="none" viewBox="0 0 48 32" role="img">
              <g clipPath="url(#a_3)">
                <path fill="#D6EDFF" d="M0 4a4 4 0 0 1 4-4h40a4 4 0 0 1 4 4v24a4 4 0 0 1-4 4H4a4 4 0 0 1-4-4z"></path>
                <path fill="#000" d="M43 10.5v9.523h-2.167V10.5zm-2.965 3.214v6.327H38.11v-.546a2.6 2.6 0 0 1-.83.546q-.465.201-1.006.203-.679 0-1.259-.253a3.2 3.2 0 0 1-1.006-.72 3.5 3.5 0 0 1-.682-1.06 3.7 3.7 0 0 1-.238-1.33q-.001-.708.238-1.313.252-.619.682-1.076a3 3 0 0 1 1.006-.708 3 3 0 0 1 1.26-.267q.539 0 1.006.203.466.19.83.545v-.545h1.925zm-3.414 4.604q.592.001.97-.407.39-.404.39-1.035c0-.42-.13-.769-.39-1.036a1.27 1.27 0 0 0-.97-.406c-.391 0-.722.134-.983.406q-.377.405-.377 1.036c0 .42.127.769.377 1.035q.391.406.983.407M29.996 10.5q.857 0 1.462.24.604.24 1.02.67.428.444.668 1.013.239.57.238 1.227.001.657-.238 1.227c-.157.377-.383.72-.667 1.013q-.415.43-1.021.67-.604.24-1.462.24h-1.044v3.238h-2.205V10.5zm-.316 4.34c.296 0 .519-.03.681-.09q.254-.103.415-.253.34-.316.34-.847t-.34-.847a1.2 1.2 0 0 0-.415-.24q-.239-.101-.681-.102h-.731v2.378zm-11.055-1.126h2.393l1.624 3.034h.027l1.447-3.034h2.216l-4.748 9.538h-2.205l2.167-4.363zm-.427 0v6.327h-1.926v-.546a2.6 2.6 0 0 1-.83.546q-.466.201-1.006.203-.679 0-1.259-.253a3.2 3.2 0 0 1-1.006-.72 3.6 3.6 0 0 1-.682-1.06 3.7 3.7 0 0 1-.238-1.33q0-.708.238-1.313.251-.619.682-1.076a3 3 0 0 1 1.006-.708 3 3 0 0 1 1.26-.267q.539 0 1.006.203.467.19.83.545v-.545h1.925zm-3.414 4.604q.591.001.972-.407.392-.404.392-1.035c0-.42-.13-.769-.392-1.036a1.27 1.27 0 0 0-.972-.406q-.591-.001-.983.406-.378.405-.377 1.036c0 .42.128.769.377 1.035q.392.406.983.407M8.25 10.5q.857 0 1.462.24.604.24 1.02.67.428.444.668 1.013.239.57.238 1.227.001.657-.238 1.227c-.156.377-.383.72-.667 1.013q-.415.43-1.021.67-.604.24-1.462.24H7.205v3.238H5V10.5zm-.313 4.34c.295 0 .519-.03.681-.09a1.3 1.3 0 0 0 .415-.253q.34-.316.34-.847t-.34-.847a1.2 1.2 0 0 0-.415-.24q-.24-.101-.681-.102h-.731v2.378z"></path>
              </g>
              <defs><clipPath id="a_3"><path fill="#fff" d="M0 0h48v32H0z"></path></clipPath></defs>
              <title>PayPal</title>
            </svg>
            
            {/* Klarna */}
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="32" fill="none" viewBox="0 0 48 32" role="img">
              <g clipPath="url(#a_4)">
                <path fill="#FFF1F7" d="M0 4a4 4 0 0 1 4-4h40a4 4 0 0 1 4 4v24a4 4 0 0 1-4 4H4a4 4 0 0 1-4-4z"></path>
                <path fill="#000" d="M39.478 18.825c-.884 0-1.572-.724-1.572-1.603 0-.88.688-1.603 1.572-1.603s1.573.724 1.573 1.603c0 .88-.69 1.603-1.573 1.603m-.442 1.694c.754 0 1.716-.284 2.249-1.396l.052.025c-.234.608-.234.97-.234 1.06v.143H43v-6.258h-1.897v.142c0 .09 0 .453.233 1.06l-.051.026c-.533-1.112-1.495-1.396-2.249-1.396-1.806 0-3.08 1.422-3.08 3.297s1.274 3.297 3.08 3.297m-6.38-6.594c-.858 0-1.534.297-2.08 1.396l-.052-.026c.234-.607.234-.97.234-1.06v-.142H28.86v6.258h1.95v-3.297c0-.866.507-1.41 1.325-1.41.82 0 1.222.466 1.222 1.397v3.31h1.95v-3.982c0-1.423-1.118-2.444-2.652-2.444M26.04 15.32l-.052-.026c.234-.607.234-.97.234-1.06v-.142h-1.897v6.258h1.95l.012-3.013c0-.879.468-1.41 1.235-1.41.208 0 .377.027.572.078v-1.913c-.858-.181-1.625.142-2.054 1.228m-6.199 3.504c-.883 0-1.572-.724-1.572-1.603 0-.88.689-1.603 1.572-1.603s1.573.724 1.573 1.603c0 .88-.689 1.603-1.573 1.603M19.4 20.52c.754 0 1.716-.284 2.249-1.396l.052.025c-.234.608-.234.97-.234 1.06v.143h1.897v-6.258h-1.897v.142c0 .09 0 .453.234 1.06l-.052.026c-.533-1.112-1.495-1.396-2.248-1.396-1.807 0-3.08 1.422-3.08 3.297s1.273 3.297 3.08 3.297m-5.796-.168h1.95V11.3h-1.95zm-1.43-9.051h-1.988c0 1.616-1 3.064-2.52 4.099l-.599.414V11.3H5v9.05h2.066v-4.486l3.418 4.487h2.521l-3.288-4.293c1.495-1.073 2.47-2.74 2.457-4.758"></path>
              </g>
              <defs><clipPath id="a_4"><path fill="#fff" d="M0 0h48v32H0z"></path></clipPath></defs>
              <title>Klarna</title>
            </svg>
            
            {/* Apple Pay */}
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="32" fill="none" viewBox="0 0 48 32" role="img">
              <g clipPath="url(#a_5)">
                <path fill="#E6E6E6" d="M0 4a4 4 0 0 1 4-4h40a4 4 0 0 1 4 4v24a4 4 0 0 1-4 4H4a4 4 0 0 1-4-4z"></path>
                <path fill="#000" d="M13.422 11.356a2.62 2.62 0 0 0 .604-1.856 2.6 2.6 0 0 0-1.718.88 2.47 2.47 0 0 0-.615 1.793 2.12 2.12 0 0 0 1.729-.817m.605.944c-.955-.063-1.761.541-2.217.541s-1.157-.509-1.91-.498a2.81 2.81 0 0 0-2.397 1.453c-1.06 1.76-.265 4.38.732 5.823.488.71 1.06 1.496 1.835 1.464s1.008-.467 1.888-.467 1.146.467 1.91.456c.763-.01 1.293-.71 1.781-1.421a6.4 6.4 0 0 0 .796-1.644 2.57 2.57 0 0 1-1.549-2.344 2.61 2.61 0 0 1 1.252-2.207 2.73 2.73 0 0 0-2.121-1.156m8.305-1.983a3.34 3.34 0 0 1 3.51 3.51 3.393 3.393 0 0 1-3.563 3.48h-2.291v3.648h-1.666V10.317zm-2.344 5.643h1.92a2.005 2.005 0 0 0 2.259-2.122 1.983 1.983 0 0 0-2.249-2.121h-1.91zm6.29 2.821c0-1.358 1.06-2.196 2.895-2.302l2.122-.127v-.594c0-.87-.584-1.39-1.56-1.39a1.485 1.485 0 0 0-1.644 1.146h-1.506c.096-1.39 1.294-2.45 3.214-2.45s3.097 1.008 3.097 2.567v5.367H31.37v-1.283a2.76 2.76 0 0 1-2.46 1.41 2.364 2.364 0 0 1-2.631-2.344m5.027-.7v-.615l-1.92.116c-.954.064-1.495.488-1.495 1.157 0 .668.562 1.124 1.421 1.124a1.87 1.87 0 0 0 1.994-1.782m3.033 5.781V22.61q.26.024.52 0a1.305 1.305 0 0 0 1.39-1.06l.137-.478-2.81-7.86h1.728l1.973 6.365 1.962-6.365H41l-2.917 8.189c-.657 1.888-1.432 2.493-3.044 2.493-.18 0-.573-.011-.7-.032"></path>
              </g>
              <defs><clipPath id="a_5"><path fill="#fff" d="M0 0h48v32H0z"></path></clipPath></defs>
              <title>Apple Pay</title>
            </svg>
            
            {/* Google Pay */}
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="32" fill="none" viewBox="0 0 48 32" role="img">
              <g clipPath="url(#a_6)">
                <path fill="#E4E5E7" d="M0 4a4 4 0 0 1 4-4h40a4 4 0 0 1 4 4v24a4 4 0 0 1-4 4H4a4 4 0 0 1-4-4z"></path>
                <path fill="#3C4043" d="M22.869 16.488v3.933H21.62v-9.712h3.309q1.259 0 2.139.838.897.84.897 2.048c0 .826-.3 1.508-.898 2.06q-.869.828-2.138.827h-2.061zm0-4.583v3.387h2.087c.494 0 .91-.17 1.235-.5.331-.332.5-.735.5-1.19a1.62 1.62 0 0 0-.5-1.177q-.486-.516-1.235-.514h-2.087zm8.361 1.651q1.383 0 2.183.741.8.74.8 2.029v4.095h-1.19v-.923h-.052q-.771 1.139-2.06 1.138-1.1.001-1.84-.65-.741-.652-.741-1.626 0-1.033.78-1.638.781-.613 2.08-.611 1.113 0 1.827.41v-.287c0-.435-.17-.8-.514-1.105a1.76 1.76 0 0 0-1.209-.455c-.695 0-1.248.293-1.651.884l-1.099-.689q.909-1.314 2.685-1.313m-1.613 4.824c0 .325.136.598.416.812q.411.324.968.325.79 0 1.405-.585.616-.583.617-1.371-.584-.46-1.625-.462-.76.002-1.268.364-.515.381-.513.917M41 13.77l-4.16 9.57h-1.288l1.547-3.348-2.743-6.221h1.359l1.976 4.771h.026l1.924-4.771z"></path>
                <path fill="#4285F4" d="M17.904 15.695q-.001-.61-.104-1.171h-5.232v2.145l3.013.001a2.58 2.58 0 0 1-1.118 1.728v1.392h1.794c1.047-.97 1.647-2.402 1.647-4.095"></path>
                <path fill="#34A853" d="M14.464 18.398c-.5.337-1.142.534-1.895.534-1.453 0-2.685-.98-3.127-2.3h-1.85v1.436a5.57 5.57 0 0 0 4.977 3.067c1.505 0 2.768-.494 3.688-1.346z"></path>
                <path fill="#FABB05" d="M9.268 15.568c0-.37.062-.729.174-1.065v-1.436h-1.85A5.54 5.54 0 0 0 7 15.568c0 .9.214 1.749.592 2.501l1.85-1.435a3.4 3.4 0 0 1-.174-1.066"></path>
                <path fill="#E94235" d="M12.57 12.204c.82 0 1.556.283 2.136.835l1.59-1.588c-.966-.9-2.224-1.451-3.727-1.451a5.57 5.57 0 0 0-4.977 3.067l1.85 1.436c.442-1.32 1.674-2.3 3.127-2.3"></path>
              </g>
              <defs><clipPath id="a_6"><path fill="#fff" d="M0 0h48v32H0z"></path></clipPath></defs>
              <title>Google Pay</title>
            </svg>
          </div>
        </div>
      </div>
    </footer>
  );
}