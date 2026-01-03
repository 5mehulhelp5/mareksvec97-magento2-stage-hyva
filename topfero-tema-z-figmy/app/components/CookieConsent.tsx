import { useState, useEffect } from "react";
import { Cookie, Shield, Settings, CheckCircle } from "lucide-react";
import { Button } from "./ui/button";

export function CookieConsent() {
  const [isVisible, setIsVisible] = useState(false);
  const [showDetails, setShowDetails] = useState(false);

  useEffect(() => {
    // Check if user has already accepted cookies
    const hasAccepted = localStorage.getItem('cookieConsent');
    if (!hasAccepted) {
      // Show banner after a short delay
      setTimeout(() => setIsVisible(true), 1000);
    }
  }, []);

  const acceptAll = () => {
    localStorage.setItem('cookieConsent', 'all');
    setIsVisible(false);
  };

  const acceptEssential = () => {
    localStorage.setItem('cookieConsent', 'essential');
    setIsVisible(false);
  };

  if (!isVisible) return null;

  return (
    <div className="fixed bottom-0 left-0 right-0 z-50 p-4 md:p-6 animate-in slide-in-from-bottom duration-500">
      <div className="max-w-7xl mx-auto">
        <div 
          className="bg-white rounded-2xl shadow-2xl border-2 overflow-hidden"
          style={{ borderColor: '#7e9b84' }}
        >
          {/* Main Content */}
          <div className="p-6 md:p-8">
            <div className="flex flex-col md:flex-row md:items-start gap-6">
              {/* Icon */}
              <div 
                className="w-16 h-16 rounded-full flex items-center justify-center flex-shrink-0"
                style={{ backgroundColor: '#f0f4f1' }}
              >
                <Cookie className="w-8 h-8" style={{ color: '#7e9b84' }} />
              </div>

              {/* Text Content */}
              <div className="flex-1 space-y-4">
                <div>
                  <h3 className="text-xl md:text-2xl font-bold mb-2">
                    Používame cookies 🍪
                  </h3>
                  <p className="leading-relaxed" style={{ color: '#666' }}>
                    Aby sme vám poskytli najlepší zážitok z nakupovania, používame súbory cookies. 
                    Pomáhajú nám analyzovať návštevnosť, personalizovať obsah a zlepšovať naše služby.
                  </p>
                </div>

                {/* Details Section */}
                {showDetails && (
                  <div className="space-y-4 pt-4 border-t border-gray-200">
                    <div className="flex items-start gap-3">
                      <div className="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style={{ backgroundColor: '#f0f4f1' }}>
                        <Shield className="w-5 h-5" style={{ color: '#7e9b84' }} />
                      </div>
                      <div className="flex-1">
                        <h4 className="font-bold mb-1">Nevyhnutné cookies</h4>
                        <p className="text-sm" style={{ color: '#666' }}>
                          Zabezpečujú základné funkcie webu ako navigácia, prístup k chráneným sekciám a košík.
                        </p>
                      </div>
                      <div className="flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold" style={{ backgroundColor: '#f0f4f1', color: '#7e9b84' }}>
                        <CheckCircle className="w-4 h-4" />
                        Vždy aktívne
                      </div>
                    </div>
                    
                    <div className="flex items-start gap-3">
                      <div className="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style={{ backgroundColor: '#f0f4f1' }}>
                        <Settings className="w-5 h-5" style={{ color: '#7e9b84' }} />
                      </div>
                      <div className="flex-1">
                        <h4 className="font-bold mb-1">Analytické a marketingové cookies</h4>
                        <p className="text-sm" style={{ color: '#666' }}>
                          Pomáhajú nám pochopiť, ako návštevníci používajú web, merať výkonnosť a zobrazovať relevantné reklamy.
                        </p>
                      </div>
                    </div>
                  </div>
                )}

                {/* Buttons */}
                <div className="flex flex-col sm:flex-row gap-3 pt-2">
                  <Button
                    onClick={acceptAll}
                    className="bg-[#7e9b84] hover:bg-[#6a8470] text-white font-bold px-8 py-5 text-base transition-all shadow-lg hover:shadow-xl"
                  >
                    Súhlasím so všetkým
                  </Button>
                  
                  <Button
                    onClick={acceptEssential}
                    variant="outline"
                    className="font-bold px-8 py-5 text-base border-2 border-[#7e9b84] text-[#7e9b84] hover:bg-[#7e9b84] hover:text-white transition-all"
                  >
                    Len nevyhnutné
                  </Button>

                  <button
                    onClick={() => setShowDetails(!showDetails)}
                    className="font-bold px-6 py-3 text-base transition-colors underline hover:no-underline"
                    style={{ color: '#7e9b84' }}
                  >
                    {showDetails ? 'Skryť detaily ↑' : 'Nastavenia cookies ↓'}
                  </button>
                </div>

                {/* Privacy Link */}
                <p className="text-sm" style={{ color: '#666' }}>
                  Viac informácií nájdete v našich{' '}
                  <a 
                    href="/ochrana-osobnych-udajov" 
                    className="underline hover:no-underline font-bold"
                    style={{ color: '#7e9b84' }}
                  >
                    zásadách ochrany osobných údajov
                  </a>
                  .
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}