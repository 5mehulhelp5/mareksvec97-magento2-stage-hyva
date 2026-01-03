import { ArrowRight, Star, CheckCircle, Award } from "lucide-react";
import { Button } from "./ui/button";
import heroImage from "figma:asset/c41554294d3abbf2ad0ce6c41bc47edd1f2694d7.png";

export function HeroSection() {
  return (
    <section className="relative overflow-hidden bg-white">
      <div className="max-w-7xl mx-auto px-4 py-12 md:py-20">
        <div className="flex flex-col lg:grid lg:grid-cols-2 gap-12 items-center">
          {/* Left content */}
          <div className="space-y-8 order-2 lg:order-1">
            {/* Main heading */}
            <div className="space-y-4">
              <div className="inline-block">
                <div className="flex items-center gap-2 text-sm font-semibold px-3 py-1 rounded-full" style={{ backgroundColor: '#f0f4f1', color: '#7e9b84' }}>
                  <Star className="w-4 h-4 fill-current" />
                  <span>Premium kvalita</span>
                </div>
              </div>
              
              <h1 className="text-5xl md:text-6xl lg:text-7xl font-bold text-gray-900 leading-tight">
                Stolové nohy
                <span className="block" style={{ color: '#7e9b84' }}>na mieru</span>
              </h1>
              
              <p className="text-xl text-gray-600 leading-relaxed max-w-xl">
                Objavte naše stolové nohy v tvare pavúka! Tieto nohy pridajú vášmu stolu jedinečný vzhľad, kombinujúc moderný dizajn s robustnou konštrukciou.
              </p>
            </div>

            {/* CTA buttons */}
            <div className="flex flex-col sm:flex-row gap-4">
              <Button 
                size="lg" 
                className="text-white text-lg px-8 py-6 transition-all"
                style={{ backgroundColor: '#7e9b84' }}
                onMouseEnter={(e) => e.currentTarget.style.backgroundColor = '#6d8a73'}
                onMouseLeave={(e) => e.currentTarget.style.backgroundColor = '#7e9b84'}
              >
                Vybrať rozmery
                <ArrowRight className="ml-2 w-5 h-5" />
              </Button>
              
              <Button 
                size="lg" 
                variant="outline" 
                className="text-lg px-8 py-6 border-2"
                style={{ borderColor: '#7e9b84', color: '#7e9b84' }}
                onMouseEnter={(e) => {
                  e.currentTarget.style.backgroundColor = '#7e9b84';
                  e.currentTarget.style.color = '#ffffff';
                }}
                onMouseLeave={(e) => {
                  e.currentTarget.style.backgroundColor = 'transparent';
                  e.currentTarget.style.color = '#7e9b84';
                }}
              >
                Katalóg produktov
              </Button>
            </div>

            {/* Trust indicators */}
            <div className="grid grid-cols-2 gap-6 pt-6">
              <div className="flex items-center gap-3">
                <div className="w-12 h-12 rounded-full flex items-center justify-center" style={{ backgroundColor: '#f0f4f1' }}>
                  <CheckCircle className="w-6 h-6" style={{ color: '#7e9b84' }} />
                </div>
                <div>
                  <div className="font-semibold text-gray-900">Vlastná výroba</div>
                  <div className="text-sm text-gray-600">Vyrobené na SK</div>
                </div>
              </div>
              
              <div className="flex items-center gap-3">
                <div className="w-12 h-12 rounded-full flex items-center justify-center" style={{ backgroundColor: '#f0f4f1' }}>
                  <Award className="w-6 h-6" style={{ color: '#7e9b84' }} />
                </div>
                <div>
                  <div className="font-semibold text-gray-900">Rôzne rozmery</div>
                  <div className="text-sm text-gray-600">Podľa vášho stola</div>
                </div>
              </div>
            </div>
          </div>

          {/* Right image */}
          <div className="relative lg:pl-8 order-1 lg:order-2">
            <div className="relative">
              <img 
                src={heroImage}
                alt="Stolové nohy - pavúk dizajn"
                className="w-full h-auto"
              />
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}