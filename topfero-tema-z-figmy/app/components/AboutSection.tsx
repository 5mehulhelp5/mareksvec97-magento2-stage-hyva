import { ArrowRight, CheckCircle2 } from "lucide-react";
import workshopImage from 'figma:asset/40765a6a3991e9d124b478f67d4a3c0086362adc.png';

export function AboutSection() {
  const stats = [
    { number: "15+", label: "rokov skúseností" },
    { number: "5000+", label: "spokojných zákazníkov" },
    { number: "100%", label: "slovenská výroba" },
  ];

  const features = [
    "Ručná práca slovenských remeselníkov",
    "Precízne CNC spracovanie kovov",
    "Individuálny prístup ku každej zákazke",
  ];

  return (
    <section className="py-20 bg-gray-50">
      <div className="max-w-7xl mx-auto px-4">
        <div className="grid lg:grid-cols-2 gap-12 items-center">
          {/* Text Content */}
          <div>
            <h2 className="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
              O nás
            </h2>
            <p className="text-lg text-gray-600 leading-relaxed mb-6">
              Naša výroba je spojením tradície a inovácií, kde skúsení inžinieri 
              a remeselníci pracujú ruka v ruke. Neustále sa posúvame vpred a s 
              precíznosťou pretransformujeme kov do dokonalých výrobkov pod našou strechou.
            </p>

            {/* Features */}
            <div className="space-y-3 mb-8">
              {features.map((feature, index) => (
                <div key={index} className="flex items-start gap-3">
                  <CheckCircle2 className="w-5 h-5 flex-shrink-0 mt-0.5" style={{ color: '#7e9b84' }} />
                  <span className="text-gray-700">{feature}</span>
                </div>
              ))}
            </div>

            {/* Stats */}
            <div className="grid grid-cols-3 gap-6 mb-8">
              {stats.map((stat, index) => (
                <div key={index}>
                  <div className="font-bold text-2xl md:text-3xl mb-1" style={{ color: '#7e9b84' }}>
                    {stat.number}
                  </div>
                  <div className="text-sm text-gray-600">
                    {stat.label}
                  </div>
                </div>
              ))}
            </div>

            {/* CTA */}
            <button 
              className="inline-flex items-center gap-2 px-6 py-3 text-white rounded-lg font-medium transition-all"
              style={{ backgroundColor: '#7e9b84' }}
              onMouseEnter={(e) => e.currentTarget.style.backgroundColor = '#6d8a73'}
              onMouseLeave={(e) => e.currentTarget.style.backgroundColor = '#7e9b84'}
            >
              Čítať viac
              <ArrowRight className="w-4 h-4" />
            </button>
          </div>

          {/* Image */}
          <div className="relative">
            <div className="rounded-2xl overflow-hidden shadow-xl">
              <img 
                src={workshopImage} 
                alt="TOPFERO výroba - naši remeselníci pri práci"
                className="w-full h-full object-cover"
              />
            </div>
            
            {/* Floating Badge */}
            <div 
              className="absolute -bottom-6 -left-6 bg-white rounded-xl shadow-lg p-6 max-w-xs"
            >
              <div className="flex items-center gap-4">
                <div className="w-12 h-12 rounded-full flex items-center justify-center" style={{ backgroundColor: '#7e9b84' }}>
                  <CheckCircle2 className="w-6 h-6 text-white" />
                </div>
                <div>
                  <div className="font-bold text-gray-900">Kvalita garantovaná</div>
                  <div className="text-sm text-gray-600">Každý výrobok kontrolovaný</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}