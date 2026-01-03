import {
  Heart,
  ChevronLeft,
  ChevronRight,
  Star,
} from "lucide-react";
import { Button } from "./ui/button";
import { CartIcon } from "./icons/CartIcon";
import Slider from "react-slick";
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";
import { ImageWithFallback } from "./figma/ImageWithFallback";
import imgSpider from "figma:asset/c41554294d3abbf2ad0ce6c41bc47edd1f2694d7.png";
import imgXShape from "figma:asset/9ca27e24a4cf405b7beb8eb9ab4a281b7ffbe045.png";
import imgVShape from "figma:asset/47a5db653d2f6acae5585aea60f8a35ceef2750e.png";

const bestSellers = [
  {
    id: 1,
    name: "Stolové nohy Spider",
    price: "149,90 €",
    originalPrice: "199,90 €",
    image: imgSpider,
    badge: "Bestseller",
    discount: "-25%",
  },
  {
    id: 2,
    name: "Stolové nohy X-tvar",
    price: "179,90 €",
    originalPrice: "229,90 €",
    image: imgXShape,
    badge: "Top predaj",
    discount: "-22%",
  },
  {
    id: 3,
    name: "Stolové nohy V-tvar",
    price: "159,90 €",
    originalPrice: "209,90 €",
    image: imgVShape,
    badge: "Bestseller",
    discount: "-24%",
  },
  {
    id: 4,
    name: "Stolové nohy Spider Black",
    price: "149,90 €",
    originalPrice: "199,90 €",
    image: imgSpider,
    badge: "Nové",
    discount: "-25%",
  },
  {
    id: 5,
    name: "Stolové nohy X-tvar Industrial",
    price: "189,90 €",
    originalPrice: "249,90 €",
    image: imgXShape,
    badge: "Bestseller",
    discount: "-24%",
  },
  {
    id: 6,
    name: "Stolové nohy V-tvar Premium",
    price: "169,90 €",
    originalPrice: "219,90 €",
    image: imgVShape,
    badge: "Top predaj",
    discount: "-23%",
  },
  {
    id: 7,
    name: "Stolové nohy Spider Double",
    price: "199,90 €",
    originalPrice: "269,90 €",
    image: imgSpider,
    badge: "Bestseller",
    discount: "-26%",
  },
  {
    id: 8,
    name: "Stolové nohy X-tvar Classic",
    price: "159,90 €",
    originalPrice: "209,90 €",
    image: imgXShape,
    badge: "Nové",
    discount: "-24%",
  },
];

// Custom arrow components
function PrevArrow(props: any) {
  const { onClick } = props;
  return (
    <button
      onClick={onClick}
      className="absolute -left-4 top-1/2 -translate-y-1/2 z-10 w-12 h-12 rounded-full bg-white shadow-lg flex items-center justify-center hover:bg-gray-50 transition-colors"
      style={{ border: "2px solid #7e9b84" }}
      aria-label="Predchádzajúci"
    >
      <ChevronLeft
        className="w-6 h-6"
        style={{ color: "#7e9b84" }}
      />
    </button>
  );
}

function NextArrow(props: any) {
  const { onClick } = props;
  return (
    <button
      onClick={onClick}
      className="absolute -right-4 top-1/2 -translate-y-1/2 z-10 w-12 h-12 rounded-full bg-white shadow-lg flex items-center justify-center hover:bg-gray-50 transition-colors"
      style={{ border: "2px solid #7e9b84" }}
      aria-label="Ďalší"
    >
      <ChevronRight
        className="w-6 h-6"
        style={{ color: "#7e9b84" }}
      />
    </button>
  );
}

export function BestSellersSection() {
  const settings = {
    dots: false,
    infinite: true,
    speed: 500,
    slidesToShow: 4,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 3000,
    pauseOnHover: true,
    prevArrow: <PrevArrow />,
    nextArrow: <NextArrow />,
    responsive: [
      {
        breakpoint: 1280,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 1,
        },
      },
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1,
        },
      },
      {
        breakpoint: 640,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1,
        },
      },
    ],
  };

  return (
    <section className="py-20 bg-white">
      <div className="max-w-7xl mx-auto px-4">
        {/* Header */}
        <div className="text-center mb-12">
          <div className="inline-block px-4 py-2 bg-[#7e9b84] text-white rounded-full mb-4">
            Najpredávanejšie produkty
          </div>
          <h2 className="text-3xl md:text-4xl font-bold mb-4">
            Obľúbené u našich zákazníkov
          </h2>
          <p
            className="text-lg max-w-2xl mx-auto"
            style={{ color: "#666" }}
          >
            Produkty s najvyšším hodnotením a tisíckami
            spokojných zákazníkov
          </p>
        </div>

        {/* Carousel */}
        <div className="bestsellers-carousel">
          <Slider {...settings}>
            {bestSellers.map((product) => (
              <div key={product.id} className="px-1.5 sm:px-3">
                <div className="bg-white rounded-xl sm:rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 group border border-gray-100 flex flex-col">
                  {/* Image */}
                  <div className="relative overflow-hidden aspect-square bg-white order-1">
                    <img
                      src={product.image}
                      alt={product.name}
                      className="w-full h-full object-contain p-4 sm:p-8 group-hover:scale-110 transition-transform duration-500"
                    />
                    {/* Badge */}
                    <div className="absolute top-2 left-2 sm:top-4 sm:left-4">
                      <span className="bg-[#7e9b84] text-white px-2 py-0.5 sm:px-3 sm:py-1 rounded-full text-xs sm:text-sm">
                        {product.badge}
                      </span>
                    </div>
                    {/* Discount */}
                    <div className="absolute top-2 right-2 sm:top-4 sm:right-4">
                      <span className="bg-red-500 text-white px-2 py-0.5 sm:px-3 sm:py-1 rounded-full font-bold text-xs sm:text-sm">
                        {product.discount}
                      </span>
                    </div>
                    {/* Wishlist */}
                    <button
                      className="absolute bottom-2 right-2 sm:bottom-4 sm:right-4 bg-white/90 backdrop-blur-sm p-2 sm:p-3 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-opacity hover:bg-[#7e9b84] hover:text-white"
                      aria-label="Pridať do obľúbených"
                    >
                      <Heart className="w-4 h-4 sm:w-5 sm:h-5" />
                    </button>
                  </div>

                  {/* Content */}
                  <div className="p-3 sm:p-5 order-2">
                    {/* Title */}
                    <h3 className="font-semibold text-sm sm:text-base mb-2 sm:mb-4 min-h-[2.5rem] line-clamp-2">
                      {product.name}
                    </h3>

                    {/* Price */}
                    <div className="flex items-center gap-1.5 sm:gap-2 mb-3 sm:mb-4">
                      <span
                        className="font-bold text-base sm:text-lg"
                        style={{ color: "#7e9b84" }}
                      >
                        {product.price}
                      </span>
                      <span className="text-gray-400 line-through text-xs sm:text-sm">
                        {product.originalPrice}
                      </span>
                    </div>

                    {/* CTA Button */}
                    <Button className="w-full bg-[#7e9b84] hover:bg-[#6a8470] text-white font-bold py-3 sm:py-5 text-xs sm:text-sm">
                      <CartIcon className="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" />
                      Pridať do košíka
                    </Button>
                  </div>
                </div>
              </div>
            ))}
          </Slider>
        </div>

        {/* View All Button */}
        <div className="text-center mt-12">
          <Button
            size="lg"
            variant="outline"
            className="border-2 border-[#7e9b84] text-[#7e9b84] hover:bg-[#7e9b84] hover:text-white px-8 py-6"
          >
            Zobraziť všetky produkty
          </Button>
        </div>
      </div>

      <style>{`
        .bestsellers-carousel .slick-dots {
          bottom: -50px;
        }
        
        .bestsellers-carousel .slick-dots li button:before {
          font-size: 12px;
          color: #7e9b84;
        }
        
        .bestsellers-carousel .slick-dots li.slick-active button:before {
          color: #7e9b84;
        }
      `}</style>
    </section>
  );
}