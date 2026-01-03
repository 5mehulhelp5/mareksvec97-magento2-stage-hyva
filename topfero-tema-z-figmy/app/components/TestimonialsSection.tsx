import { Star, CheckCircle, Camera, ChevronLeft, ChevronRight } from "lucide-react";
import { Badge } from "./ui/badge";
import Slider from "react-slick";
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";
import image1 from 'figma:asset/cd0412c49d42047ff40e8a25b233b9237de15f83.png';
import image2 from 'figma:asset/1a8ac6948991523bf9857ed90b175162d2fb914d.png';
import image3 from 'figma:asset/e982e1c9cc00362c451562f6ea45d4f55bc6c901.png';
import flagDE from 'figma:asset/8676fd138f2efb82bdecb27da3d7aed62b5f7d0f.png';
import flagCZ from 'figma:asset/1df31320bc5907822f461d842e83e047a53bfc35.png';
import flagAT from 'figma:asset/447a7ada77772647fb975cb64730ce40767d6f54.png';
import flagFR from 'figma:asset/197b6e84db5868f10ae04fdff59a299c2e21d4fa.png';
import flagHR from 'figma:asset/2a5657fb823ce1ede65557928468d78d0263fc88.png';

// Custom arrow components
function NextArrow(props: any) {
  const { onClick } = props;
  return (
    <button
      onClick={onClick}
      className="absolute -right-4 top-1/2 -translate-y-1/2 z-10 w-12 h-12 rounded-full bg-white shadow-lg flex items-center justify-center hover:bg-gray-50 transition-colors"
      style={{ border: '2px solid #7e9b84' }}
    >
      <ChevronRight className="w-6 h-6" style={{ color: '#7e9b84' }} />
    </button>
  );
}

function PrevArrow(props: any) {
  const { onClick } = props;
  return (
    <button
      onClick={onClick}
      className="absolute -left-4 top-1/2 -translate-y-1/2 z-10 w-12 h-12 rounded-full bg-white shadow-lg flex items-center justify-center hover:bg-gray-50 transition-colors"
      style={{ border: '2px solid #7e9b84' }}
    >
      <ChevronLeft className="w-6 h-6" style={{ color: '#7e9b84' }} />
    </button>
  );
}

export function TestimonialsSection() {
  const testimonials = [
    {
      rating: 5,
      text: "Les pieds en forme de U s'adaptent parfaitement à notre plateau en chêne avec résine époxy bleue. Excellente qualité de soudure et peinture en poudre. La table est stable et luxueuse. Merci!",
      author: "Pierre D.",
      location: "France",
      flagImg: flagFR,
      verified: true,
      image: image1,
    },
    {
      rating: 5,
      text: "Die Spider-Beine haben unseren Esstisch komplett verwandelt. Schnelle Lieferung nach Deutschland, perfekt verpackt. Montage war super einfach. Absolute Kaufempfehlung!",
      author: "Klaus M.",
      location: "Deutschland",
      flagImg: flagDE,
      verified: true,
      image: image2,
    },
    {
      rating: 5,
      text: "Stolne noge u obliku X su savršene za naš hrastov stol. Odlična kvaliteta, brza dostava u Hrvatsku. Stol je stabilan i izgleda luksuzno. Preporučam!",
      author: "Ivan P.",
      location: "Hrvatska",
      flagImg: flagHR,
      verified: true,
      image: image3,
    },
    {
      rating: 5,
      text: "Objednal jsem si U-nohy pro jídelní stůl a jsem nadšený. Přesné vyhotovení, rychlé dodání do ČR a kvalita materiálu je opravdu na nejvyšší úrovni. Doporučuji!",
      author: "Jan V.",
      location: "Česká republika",
      flagImg: flagCZ,
      verified: true,
      image: image1,
    },
    {
      rating: 5,
      text: "Die Hairpin-Beine sind ein Traum! Montage in 20 Minuten erledigt, Qualität ist hervorragend. Unser Schreibtisch steht jetzt bombenfest. Preis-Leistung unschlagbar!",
      author: "Maria S.",
      location: "Österreich",
      flagImg: flagAT,
      verified: true,
      image: image2,
    },
    {
      rating: 5,
      text: "Les pieds Spider ont transformé notre table à manger. Livraison rapide en France, emballage parfait. Le design industriel est exactement ce que nous cherchions. Excellent travail!",
      author: "Sophie L.",
      location: "France",
      flagImg: flagFR,
      verified: true,
      image: image3,
    },
  ];

  const sliderSettings = {
    dots: true,
    infinite: true,
    speed: 500,
    slidesToShow: 3,
    slidesToScroll: 1,
    nextArrow: <NextArrow />,
    prevArrow: <PrevArrow />,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1,
        }
      },
      {
        breakpoint: 640,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
        }
      }
    ]
  };

  return (
    <section className="py-20 bg-white">
      <div className="max-w-7xl mx-auto px-4">
        {/* Header */}
        <div className="text-center mb-16">
          <Badge className="mb-4" style={{ backgroundColor: '#f0f4f1', color: '#7e9b84', borderColor: '#7e9b84' }}>
            <Camera className="w-3 h-3 mr-1" />
            Fotky od zákazníkov
          </Badge>
          <h2 className="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            Čo hovoria naši zákazníci?
          </h2>
          <p className="text-xl text-gray-600 max-w-2xl mx-auto">
            Zákazníci z celého sveta nám dôverujú - Francúzsko • Nemecko • Chorvátsko • Česko • Rakúsko
          </p>
        </div>

        {/* Testimonials Slider */}
        <div className="relative px-8">
          <Slider {...sliderSettings}>
            {testimonials.map((testimonial, index) => (
              <div key={index} className="px-3">
                <div className="group bg-white rounded-xl overflow-hidden border border-gray-200 hover:shadow-xl transition-all duration-300 flex flex-col">
                  {/* Customer Photo */}
                  <div className="relative h-80 overflow-hidden bg-gray-100 order-1">
                    <img 
                      src={testimonial.image} 
                      alt={`Stôl od ${testimonial.author}`}
                      className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                    />
                    {/* Verified badge overlay */}
                    <div className="absolute top-4 right-4 bg-white rounded-full px-3 py-1.5 shadow-lg flex items-center gap-1.5">
                      <CheckCircle className="w-4 h-4 text-green-600" />
                      <span className="text-xs font-semibold text-gray-700">Overené</span>
                    </div>
                  </div>

                  {/* Review content */}
                  <div className="p-6 order-2">
                    {/* Rating */}
                    <div className="flex gap-1 mb-4">
                      {[...Array(testimonial.rating)].map((_, i) => (
                        <Star key={i} className="w-5 h-5 fill-yellow-400 text-yellow-400" />
                      ))}
                    </div>

                    {/* Review text */}
                    <p className="text-gray-700 leading-relaxed mb-6 min-h-[120px]">
                      "{testimonial.text}"
                    </p>

                    {/* Author info */}
                    <div className="flex items-center justify-between pt-4 border-t border-gray-100">
                      <div>
                        <div className="font-semibold text-gray-900 flex items-center gap-2">
                          <img 
                            src={testimonial.flagImg} 
                            alt={testimonial.location}
                            className="w-6 h-6 rounded-full object-cover"
                          />
                          {testimonial.author}
                        </div>
                        <div className="text-sm text-gray-500">{testimonial.location}</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            ))}
          </Slider>
        </div>

        {/* Trust message */}
        <div className="mt-16 text-center">
          <div className="inline-flex items-center gap-3 sm:gap-6 md:gap-8 px-4 py-4 sm:px-6 sm:py-5 md:px-8 md:py-6 rounded-xl mx-auto" style={{ backgroundColor: '#f0f4f1' }}>
            <div>
              <div className="text-xl sm:text-2xl md:text-3xl font-bold mb-1" style={{ color: '#7e9b84' }}>5000+</div>
              <div className="text-[10px] sm:text-xs md:text-sm text-gray-600 whitespace-nowrap">Spokojných zákazníkov</div>
            </div>
            <div className="w-px h-8 sm:h-10 md:h-12 bg-gray-300"></div>
            <div>
              <div className="text-xl sm:text-2xl md:text-3xl font-bold mb-1" style={{ color: '#7e9b84' }}>4.9/5</div>
              <div className="text-[10px] sm:text-xs md:text-sm text-gray-600 whitespace-nowrap">Priemerné hodnotenie</div>
            </div>
            <div className="w-px h-8 sm:h-10 md:h-12 bg-gray-300"></div>
            <div>
              <div className="text-xl sm:text-2xl md:text-3xl font-bold mb-1" style={{ color: '#7e9b84' }}>98%</div>
              <div className="text-[10px] sm:text-xs md:text-sm text-gray-600 whitespace-nowrap">Odporúčajú ďalej</div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}