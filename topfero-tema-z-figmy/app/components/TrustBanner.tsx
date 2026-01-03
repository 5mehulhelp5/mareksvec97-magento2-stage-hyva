import { MapPin, Headphones, Award, ShieldCheck } from "lucide-react";

export function TrustBanner() {
  const features = [
    {
      icon: MapPin,
      title: "Vyrábame na Slovensku",
      description: "Všetky naše produkty sú zhotovené priamo na Slovensku."
    },
    {
      icon: Headphones,
      title: "Kompletné poradenstvo",
      description: "Ovládame svoj remeslo. Máte otázky? Spoločne nájdeme riešenie na každý problém."
    },
    {
      icon: Award,
      title: "Vysoká kvalita",
      description: "Každý produkt prejde kontrolou kvality. Obdržíte od nás len overený produkt."
    },
    {
      icon: ShieldCheck,
      title: "100% zabezpečená platba",
      description: "Vaše platby sú chránené najmodernejšími bezpečnostnými systémami."
    }
  ];

  return (
    <div className="py-12 border-y" style={{ backgroundColor: '#7e9b84', borderColor: '#6d8a73' }}>
      <div className="max-w-7xl mx-auto px-4">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          {features.map((feature, index) => (
            <div key={index} className="flex flex-col items-center text-center gap-3">
              <div className="w-16 h-16 rounded-full flex items-center justify-center bg-white">
                <feature.icon className="w-8 h-8" style={{ color: '#7e9b84' }} />
              </div>
              <h3 className="font-bold text-white">
                {feature.title}
              </h3>
              <p className="text-sm text-white leading-relaxed opacity-90">
                {feature.description}
              </p>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}