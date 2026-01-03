import { ArrowRight } from "lucide-react";
import imageSpider from 'figma:asset/c671633ecab4c7163ad22494a5a820df792f7be2.png';
import imageU from 'figma:asset/bf73ec76fd77d096947bb87e2fd1dec2a4aa2588.png';
import imageX from 'figma:asset/0cccdb2ea6d2fa9080d2621a8e86e64e0867fc22.png';
import imageV from 'figma:asset/ed8f36ae2fe0dd7c1ceb951d5664bd8fc7c2f1ea.png';

export function CategoriesSection() {
  const categories = [
    {
      title: "Stolové nohy Spider",
      description: "Nohy v tvare pavúka s moderným dizajnom ktorý sa určite hodí k vašej doske.",
      image: "https://images.unsplash.com/photo-1759410124528-1866c265bf48?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxzcGlkZXIlMjB0YWJsZSUyMGxlZ3MlMjBtZXRhbHxlbnwxfHx8fDE3NjczNDE3NjN8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
    },
    {
      title: "Stolové nohy so stredovou tyčou",
      description: "Stolové nohy spojené stredovou tyčou pre väčšiu stabilitu.",
      image: "https://images.unsplash.com/photo-1646090479934-6c837249e482?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtZXRhbCUyMHRhYmxlJTIwbGVncyUyMGNyb3NzYmFyfGVufDF8fHx8MTc2NzM0MTc2M3ww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
    },
    {
      title: "Stolové nohy tvar X",
      description: "Stolové nohy s charakteristickým tvarom X kombinujú moderný dizajn a robustnosťou.",
      image: imageX,
    },
    {
      title: "Stolové nohy tvar U",
      description: "Stolové nohy s elegantným tvaru U poskytujú jednoducho, no výrazný dizajn.",
      image: imageU,
    },
    {
      title: "Stolové nohy tvar V",
      description: "Dynamický dizajn v tvare V prináva stolu zaujímavý vizuál a poskytuje pevný základ.",
      image: imageV,
    },
    {
      title: "Stolové nohy Hairpin",
      description: "Stolové nohy Hairpin predstavujú klasiku v modernom dizajne a štýlové detaily.",
      image: "https://images.unsplash.com/photo-1586998474523-8a52e8ad3a0a?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxoYWlycGluJTIwdGFibGUlMjBsZWdzfGVufDF8fHx8MTc2NzM0MTc2M3ww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
    },
  ];

  return (
    <section className="py-20 bg-gray-50">
      <div className="max-w-7xl mx-auto px-4">
        <div className="text-center mb-16">
          <h2 className="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            Vybrané kategórie
          </h2>
          <p className="text-xl text-gray-600 max-w-2xl mx-auto">
            Objavte našu ponuku stolových nôh v rôznych dizajnoch a štýloch
          </p>
        </div>

        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          {categories.map((category, index) => (
            <div 
              key={index}
              className="group bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-all duration-300"
            >
              <div className="bg-white h-56 overflow-hidden">
                <img 
                  src={category.image} 
                  alt={category.title}
                  className="w-full h-full object-contain p-8 group-hover:scale-105 transition-transform duration-300"
                />
              </div>
              <div className="p-6">
                <h3 className="font-bold text-gray-900 mb-3">
                  {category.title}
                </h3>
                <p className="text-sm text-gray-600 leading-relaxed mb-6">
                  {category.description}
                </p>
                <button 
                  className="w-full py-3 text-white rounded-lg font-medium transition-all inline-flex items-center justify-center gap-2"
                  style={{ backgroundColor: '#7e9b84' }}
                  onMouseEnter={(e) => e.currentTarget.style.backgroundColor = '#6d8a73'}
                  onMouseLeave={(e) => e.currentTarget.style.backgroundColor = '#7e9b84'}
                >
                  Do kategórie
                  <ArrowRight className="w-4 h-4" />
                </button>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}