import { useState, useEffect, useRef } from "react";
import { motion, AnimatePresence } from "motion/react";
import {
  Star,
  Heart,
  CheckCircle,
  Truck,
  Minus,
  Plus,
  Award,
  ChevronDown,
  ChevronUp,
  ChevronLeft,
  Ruler,
  Package,
  Layers,
  Shield,
  Hammer,
  Sparkles,
  MessageCircle,
  ThumbsUp,
  Send,
  Image as ImageIcon,
  Filter,
  ChevronRight,
  X,
  ShoppingCart,
  Weight,
  FileText,
} from "lucide-react";
import { toast } from "sonner";
import { Button } from "../components/ui/button";
import { TrustBanner } from "../components/TrustBanner";
import { CustomerInspirationGallery } from "../components/CustomerInspirationGallery";
import { CartIcon } from "../components/icons/CartIcon";
import Slider from "react-slick";
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";

// Import produktových obrázkov z Figma
import productImage1 from "figma:asset/04f0a15636d95bda95f42982c378c883dd93fb53.png";
import productImage2 from "figma:asset/55ae23da63723ad380aec2b614591a750318afeb.png";
import productImage3 from "figma:asset/c41554294d3abbf2ad0ce6c41bc47edd1f2694d7.png";
import supportImage from "figma:asset/8b7aa4d10e501809b6737f54d5a8ddd74b4c032e.png";

// Payment logos
import visaLogo from "figma:asset/c893affd596684781b2e2bd6b2355148610e56f8.png";
import paypalLogo from "figma:asset/47192e2bb28234430cc97ce7997c18704cc37dab.png";
import mastercardLogo from "figma:asset/8b2059243e9ccd26c470f049baedeae8dabdd868.png";
import applePayLogo from "figma:asset/f2b67aa23d3b541c40dbf8e795d4f7cb132f281f.png";
import googlePayLogo from "figma:asset/8cccc5e8da96de580896d4ef3124ca192058ad75.png";

interface Review {
  id: number;
  name: string;
  rating: number;
  date: string;
  verified: boolean;
  comment: string;
  images?: string[];
}

const mockReviews: Review[] = [
  {
    id: 1,
    name: "Martin K.",
    rating: 5,
    date: "15.12.2024",
    verified: true,
    comment:
      "Výborná kvalita, presne podľa popisu. Nohy sú pevné a stabilné, stôl z nich je perfektný. Odporúčam!",
    images: [
      "https://images.unsplash.com/photo-1759190068354-f59d0ba26b23?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=400",
      "https://images.unsplash.com/photo-1734126565187-e53d8debf6ab?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=400",
    ],
  },
  {
    id: 2,
    name: "Jana S.",
    rating: 5,
    date: "08.12.2024",
    verified: true,
    comment:
      "Super dizajn, moderný vzhľad. Montáž bola jednoduchá. Veľmi spokojná s nákupom.",
    images: [
      "https://images.unsplash.com/photo-1758977403341-0104135995af?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=400",
    ],
  },
  {
    id: 3,
    name: "Peter H.",
    rating: 4,
    date: "02.12.2024",
    verified: true,
    comment:
      "Kvalitné spracovanie, rýchle dodanie. Odporúčam pre všetkých, čo chcú moderný stôl.",
  },
  {
    id: 4,
    name: "Lucia M.",
    rating: 5,
    date: "28.11.2024",
    verified: true,
    comment:
      "Úžasný produkt! Presne to, co som hľadala. Stabilné, krásne a kvalitné.",
    images: [
      "https://images.unsplash.com/photo-1752061905248-e93a855489fe?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=400",
    ],
  },
  {
    id: 5,
    name: "Tomáš B.",
    rating: 5,
    date: "20.11.2024",
    verified: true,
    comment:
      "Perfektná stabilita, moderný look. Jednoznačne odporúčam!",
  },
  {
    id: 6,
    name: "Eva K.",
    rating: 4,
    date: "15.11.2024",
    verified: false,
    comment:
      "Pekné nohy, len montáž trvala dlhšie ako som čakala. Inak super!",
  },
];

// Custom arrow components pre slider
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

export function ProductDetail() {
  const [selectedImage, setSelectedImage] = useState(0);
  const [selectedHeight, setSelectedHeight] =
    useState("72x70x120cm");
  const [selectedColor, setSelectedColor] = useState("black");
  const [quantity, setQuantity] = useState(1);
  const [isFavorite, setIsFavorite] = useState(false);
  const [openAccordion, setOpenAccordion] = useState<
    string | null
  >("description");
  const [openFaqIndex, setOpenFaqIndex] = useState<
    number | null
  >(null);
  const [reviewFilter, setReviewFilter] = useState<
    "all" | 5 | 4 | 3 | "photos"
  >("all");
  const [showStickyBar, setShowStickyBar] = useState(false);
  const addToCartRef = useRef<HTMLDivElement>(null);

  const images = [
    productImage1,
    productImage2,
    productImage3,
    productImage1,
  ];

  const heights = [
    {
      value: "72x70x120cm",
      label: "72x70x120 cm",
      inStock: true,
    },
    {
      value: "72x70x130cm",
      label: "72x70x130 cm",
      inStock: true,
    },
    {
      value: "72x70x140cm",
      label: "72x70x140 cm",
      inStock: true,
    },
    {
      value: "72x70x150cm",
      label: "72x70x150 cm",
      inStock: true,
    },
    {
      value: "72x70x160cm",
      label: "72x70x160 cm",
      inStock: true,
    },
    {
      value: "72x70x170cm",
      label: "72x70x170 cm",
      inStock: true,
    },
    {
      value: "72x70x180cm",
      label: "72x70x180 cm",
      inStock: true,
    },
  ];

  const colors = [
    {
      value: "black",
      label: "Čierna matná",
      hex: "#000000",
      inStock: true,
    },
    {
      value: "white",
      label: "Biela",
      hex: "#FFFFFF",
      inStock: true,
    },
    {
      value: "gray",
      label: "Antracit",
      hex: "#4A4A4A",
      inStock: true,
    },
  ];

  const specs = [
    { label: "Materiál", value: "Oceľ" },
    { label: "Typ", value: "X-tvar" },
    { label: "Nosnosť", value: "150 kg" },
    { label: "Rozmer profilu", value: "10x10 cm" },
    { label: "Hmotnosť (pár)", value: "8,5 kg" },
    { label: "Povrchová úprava", value: "Prášková farba" },
    { label: "Hrúbka materiálu", value: "3 mm" },
    { label: "Montáž", value: "Vrátane skrutiek" },
  ];

  const faqs = [
    {
      question: "Koľko nôh je v balení?",
      answer:
        "V balení sú 2 kusy (1 pár) stolových nôh. Pre klasický 4-nohý stôl potrebujete objednať 2 balenia (spolu 4 nohy).",
    },
    {
      question: "Sú nohy vhodné aj na vonkajšie použitie?",
      answer:
        "Nohy sú určené primárne na vnútorné použitie. Pre vonkajšie použitie odporúčame galvanizovanú verziu s dodatočnou povrchovou úpravou.",
    },
    {
      question: "Aká je maximálna nosnosť?",
      answer:
        "Jedna noha unesie až 150 kg. Pri správnej montáži a rozložení váhy môže stôl uniesť značnú záťaž.",
    },
    {
      question: "Ako sa nohy montujú k doske?",
      answer:
        "Nohy sa montujú pomocou priložených skrutiek. Odporúčame vyvŕtať predvŕtané otvory v doske stola pre najlepšiu stabilitu.",
    },
  ];

  const relatedProducts = [
    {
      id: 1,
      name: "Stolová noha Leg - Hairpin 72cm",
      image: productImage1,
      price: "24,90 €",
      originalPrice: "32,90 €",
      rating: 4.8,
      badge: "Bestseller",
      discount: "-24%",
    },
    {
      id: 2,
      name: "Stolová Noha - Acamar Spider 75cm",
      image: productImage3,
      price: "32,50 €",
      originalPrice: "42,90 €",
      rating: 4.9,
      badge: "Top predaj",
      discount: "-24%",
    },
    {
      id: 3,
      name: "Centrálne nohy Y-tvar 72cm",
      image: productImage2,
      price: "36,90 €",
      originalPrice: "48,90 €",
      rating: 4.7,
      badge: "Novinka",
      discount: "-25%",
    },
    {
      id: 4,
      name: "Stolová noha X-tvar Industrial 75cm",
      image: productImage1,
      price: "29,90 €",
      originalPrice: "39,90 €",
      rating: 4.8,
      badge: "Bestseller",
      discount: "-25%",
    },
    {
      id: 5,
      name: "Stolová noha V-shape Modern 72cm",
      image: productImage2,
      price: "34,90 €",
      originalPrice: "45,90 €",
      rating: 4.6,
      badge: "Top predaj",
      discount: "-24%",
    },
  ];

  const incrementQuantity = () =>
    setQuantity((prev) => Math.min(prev + 1, 10));
  const decrementQuantity = () =>
    setQuantity((prev) => Math.max(prev - 1, 1));

  const averageRating =
    mockReviews.reduce(
      (acc, review) => acc + review.rating,
      0,
    ) / mockReviews.length;

  const scrollToDescription = () => {
    const descriptionElement = document.getElementById(
      "product-description",
    );
    if (descriptionElement) {
      descriptionElement.scrollIntoView({
        behavior: "smooth",
        block: "start",
      });
    }
  };

  // Sledovanie scrollu pre desktop sticky bar
  useEffect(() => {
    const handleScroll = () => {
      if (addToCartRef.current) {
        const rect = addToCartRef.current.getBoundingClientRect();
        // Zobraz sticky bar keď je add to cart sekcia mimo obrazovky (scrollnuté hore)
        setShowStickyBar(rect.bottom < 0);
      }
    };

    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  const filteredReviews = mockReviews.filter((review) => {
    if (reviewFilter === "all") return true;
    if (reviewFilter === "photos")
      return review.images && review.images.length > 0;
    return review.rating === reviewFilter;
  });

  const handleAddToCart = () => {
    toast.custom((t) => (
      <motion.div
        initial={{ opacity: 0, y: -20 }}
        animate={{ opacity: 1, y: 0 }}
        exit={{ opacity: 0, y: -20 }}
        transition={{ duration: 0.3 }}
        className="bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden max-w-4xl w-full"
      >
        <div className="flex items-center gap-4 p-4">
          {/* Success Icon */}
          <div className="flex-shrink-0 w-10 h-10 bg-emerald-500 rounded-full flex items-center justify-center">
            <CheckCircle className="w-5 h-5 text-white" />
          </div>
          
          {/* Message */}
          <div className="flex-1 min-w-0">
            <p className="text-gray-900">
              <span className="font-semibold">Stolová Noha - Acamar</span> bola pridaná do košíka (× {quantity})
            </p>
          </div>
          
          {/* Actions */}
          <div className="flex items-center gap-3 flex-shrink-0">
            <button
              onClick={() => toast.dismiss(t)}
              className="text-gray-600 hover:text-gray-900 transition-colors"
            >
              Pokračovať
            </button>
            <button
              onClick={() => {
                toast.dismiss(t);
                // Navigate to cart
              }}
              className="bg-[#7e9b84] hover:bg-[#6d8a73] text-white px-5 py-2.5 rounded-lg transition-colors flex items-center gap-2"
            >
              <ShoppingCart className="w-4 h-4" />
              <span>Zobraziť košík</span>
            </button>
            <button
              onClick={() => toast.dismiss(t)}
              className="text-gray-400 hover:text-gray-600 transition-colors p-1"
            >
              <X className="w-5 h-5" />
            </button>
          </div>
        </div>
      </motion.div>
    ), {
      duration: 5000,
      position: 'top-center',
    });
  };

  return (
    <div className="min-h-screen bg-white">
      {/* Breadcrumb */}
      <div className="bg-gray-50 border-b border-gray-200">
        <div className="max-w-7xl mx-auto px-4 py-3">
          <nav className="flex items-center gap-2 text-sm text-gray-600">
            {/* Desktop breadcrumb - zobrazí sa na md+ zariadeniach */}
            <div className="hidden md:flex items-center gap-2">
              <a
                href="/"
                className="hover:text-[#7e9b84] transition-colors"
              >
                Domov
              </a>
              <span>/</span>
              <a
                href="/stolove-nohy"
                className="hover:text-[#7e9b84] transition-colors"
              >
                Stolové nohy
              </a>
              <span>/</span>
              <span className="text-gray-900 font-medium">
                Stolová Noha Acamar Frame Extreme X-tvar
              </span>
            </div>
            
            {/* Mobile breadcrumb - zobrazí sa len na mobile, len posledná kategória */}
            <div className="flex md:hidden items-center gap-2">
              <a href="/stolove-nohy" className="hover:text-[#7e9b84] transition-colors flex items-center gap-1">
                <ChevronLeft className="w-4 h-4" />
              </a>
              <span className="text-gray-900 font-medium">
                Stolová Noha Acamar Frame Extreme X-tvar
              </span>
            </div>
          </nav>
        </div>
      </div>

      {/* Main Product Section */}
      <section className="py-8 md:py-12">
        <div className="max-w-7xl mx-auto px-4">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
            {/* Image Gallery */}
            <div className="space-y-4">
              {/* Main Image */}
              <motion.div
                className="relative aspect-square bg-white rounded-2xl overflow-hidden border border-gray-100"
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
              >
                <img
                  src={images[selectedImage]}
                  alt="Stolová noha"
                  className="w-full h-full object-contain p-8"
                />
                {/* Badges - Left Side */}
                <div className="absolute top-4 left-4 flex flex-col gap-2">
                  <span className="bg-blue-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                    NOVINKA
                  </span>
                  <span className="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1">
                    <CheckCircle className="w-3 h-3" />
                    SKLADOM
                  </span>
                </div>
              </motion.div>

              {/* Thumbnails */}
              <div className="grid grid-cols-4 gap-3">
                {images.map((image, index) => (
                  <button
                    key={index}
                    onClick={() => setSelectedImage(index)}
                    className={`aspect-square bg-white rounded-lg overflow-hidden border-2 transition-all ${
                      selectedImage === index
                        ? "border-[#7e9b84] shadow-md"
                        : "border-gray-200 hover:border-gray-300"
                    }`}
                  >
                    <img
                      src={image}
                      alt={`Náhľad ${index + 1}`}
                      className="w-full h-full object-contain p-2"
                    />
                  </button>
                ))}
              </div>
            </div>

            {/* Product Info */}
            <div className="space-y-6">
              {/* Title & Rating */}
              <div>
                <h1 className="mb-3 text-2xl md:text-3xl lg:text-4xl">
                  Stolová Noha Acamar Frame Extreme X-tvar
                </h1>
                <div className="flex items-center gap-3 mb-4">
                  <div className="flex items-center gap-1">
                    {[...Array(5)].map((_, i) => (
                      <Star
                        key={i}
                        className={`w-5 h-5 ${
                          i < Math.floor(averageRating)
                            ? "fill-yellow-400 text-yellow-400"
                            : "text-gray-300"
                        }`}
                      />
                    ))}
                  </div>
                  <span className="text-gray-600">
                    {averageRating.toFixed(1)} (
                    {mockReviews.length} recenzií)
                  </span>
                </div>
                
                {/* Technical Specs Badges */}
                <div className="flex flex-wrap items-center gap-3 mb-4">
                  <div className="flex items-center gap-2 px-4 py-2.5 bg-[#7e9b84]/10 border border-[#7e9b84]/30 rounded-lg">
                    <Weight className="w-5 h-5 text-[#7e9b84]" />
                    <div className="flex flex-col">
                      <span className="text-xs text-gray-500">Nosnosť</span>
                      <span className="font-semibold text-gray-900">150 kg</span>
                    </div>
                  </div>
                  <div className="flex items-center gap-2 px-4 py-2.5 bg-[#7e9b84]/10 border border-[#7e9b84]/30 rounded-lg">
                    <Package className="w-5 h-5 text-[#7e9b84]" />
                    <div className="flex flex-col">
                      <span className="text-xs text-gray-500">Váha</span>
                      <span className="font-semibold text-gray-900">15 kg</span>
                    </div>
                  </div>
                  <div className="flex items-center gap-2 px-4 py-2.5 bg-[#7e9b84]/10 border border-[#7e9b84]/30 rounded-lg">
                    <Layers className="w-5 h-5 text-[#7e9b84]" />
                    <div className="flex flex-col">
                      <span className="text-xs text-gray-500">Balenie</span>
                      <span className="font-semibold text-gray-900">1 pár (2 ks)</span>
                    </div>
                  </div>
                </div>
                
                <div className="relative">
                  <p className="text-gray-700 text-base leading-relaxed relative">
                    Štýlová stolová noha Acamar Frame Extreme s elegantným X-tvarom je perfektným riešením pre moderné interiéry. Vyrobená z kvalitnej ocele s práškovým povrchom zaručuje maximálnu stabilitu a dlhú životnosť. Precízne spracovanie a odolný materiál zabezpečujú, že táto stolová noha vydrží aj náročné...
                    <span className="absolute left-0 right-0 bottom-0 h-9 pointer-events-none bg-gradient-to-b from-transparent to-white"></span>
                  </p>
                  <button
                    onClick={scrollToDescription}
                    className="text-[#7e9b84] font-medium underline hover:no-underline mt-2 inline-block transition-all"
                  >
                    Čítať viac
                  </button>
                </div>
              </div>

              {/* Height Selection */}
              <div>
                <label className="block mb-3">
                  <span className="font-medium">Rozmery:</span>
                  <span className="text-gray-600 ml-2">
                    {
                      heights.find(
                        (h) => h.value === selectedHeight,
                      )?.label
                    }
                  </span>
                </label>
                <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                  {heights.map((height) => (
                    <button
                      key={height.value}
                      onClick={() =>
                        height.inStock &&
                        setSelectedHeight(height.value)
                      }
                      disabled={!height.inStock}
                      className={`py-3 px-2 rounded-lg border-2 font-medium transition-all text-sm ${
                        selectedHeight === height.value
                          ? "border-[#7e9b84] bg-[#7e9b84]/5 text-[#7e9b84]"
                          : height.inStock
                            ? "border-gray-200 hover:border-gray-300"
                            : "border-gray-200 bg-gray-100 text-gray-400 cursor-not-allowed"
                      }`}
                    >
                      {height.label}
                    </button>
                  ))}
                </div>
              </div>

              {/* Color Selection */}
              <div>
                <label className="block mb-3">
                  <span className="font-medium">Farba:</span>
                  <span className="text-gray-600 ml-2">
                    {
                      colors.find(
                        (c) => c.value === selectedColor,
                      )?.label
                    }
                  </span>
                </label>
                <div className="flex items-center gap-3">
                  {colors.map((color) => (
                    <button
                      key={color.value}
                      onClick={() =>
                        color.inStock &&
                        setSelectedColor(color.value)
                      }
                      disabled={!color.inStock}
                      className={`relative w-12 h-12 rounded-full border-2 transition-all ${
                        selectedColor === color.value
                          ? "border-[#7e9b84] scale-110"
                          : "border-gray-300 hover:scale-105"
                      } ${!color.inStock ? "opacity-50 cursor-not-allowed" : ""}`}
                      title={color.label}
                    >
                      <div
                        className="absolute inset-1 rounded-full"
                        style={{
                          backgroundColor: color.hex,
                          border:
                            color.value === "white"
                              ? "1px solid #e5e7eb"
                              : "none",
                        }}
                      />
                      {selectedColor === color.value && (
                        <CheckCircle className="absolute -top-1 -right-1 w-5 h-5 text-[#7e9b84] bg-white rounded-full" />
                      )}
                    </button>
                  ))}
                </div>
              </div>

              {/* Price & Add to Cart Box */}
              <div className="bg-gray-50 p-6 rounded-xl">
                {/* Availability */}
                <div className="flex items-center gap-2 mb-4">
                  <div className="flex items-center gap-2 text-green-600">
                    <div className="w-3 h-3 bg-green-600 rounded-full"></div>
                    <span className="font-semibold">
                      Dostupné
                    </span>
                  </div>
                </div>

                {/* Delivery Time */}
                <div className="flex items-center gap-2 mb-4 text-gray-700">
                  <Truck className="w-5 h-5 text-gray-600" />
                  <span className="text-sm">
                    Čas doručenia:{" "}
                    <strong>
                      Skladom - U vás do 8 - 12 dní
                    </strong>
                  </span>
                </div>

                {/* Price */}
                <div className="flex items-baseline gap-3 mb-6 pb-6 border-b border-gray-300">
                  <span className="text-4xl font-bold text-[#7e9b84]">
                    38,90 €
                  </span>
                  <span className="text-xl text-gray-400 line-through">
                    45,90 €
                  </span>
                  <span className="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                    -15%
                  </span>
                </div>

                {/* Quantity & Add to Cart */}
                <div ref={addToCartRef} className="flex gap-2 sm:gap-3 items-stretch">
                  {/* Quantity */}
                  <div className="flex items-center border-2 border-gray-200 rounded-lg overflow-hidden bg-white">
                    <button
                      onClick={decrementQuantity}
                      className="p-2 sm:p-3 hover:bg-gray-100 transition-colors"
                    >
                      <Minus className="w-4 h-4 sm:w-5 sm:h-5" />
                    </button>
                    <input
                      type="number"
                      value={quantity}
                      readOnly
                      className="w-10 sm:w-16 h-full text-center font-medium bg-white text-sm sm:text-base flex items-center justify-center"
                    />
                    <button
                      onClick={incrementQuantity}
                      className="p-2 sm:p-3 hover:bg-gray-100 transition-colors"
                    >
                      <Plus className="w-4 h-4 sm:w-5 sm:h-5" />
                    </button>
                  </div>

                  {/* Add to Cart */}
                  <Button
                    className="flex-1 text-white flex items-center justify-center gap-1 sm:gap-2 h-[44px] sm:h-[52px] text-sm sm:text-lg font-semibold"
                    style={{ backgroundColor: "#7e9b84" }}
                    onMouseEnter={(e) =>
                      (e.currentTarget.style.backgroundColor =
                        "#6d8a73")
                    }
                    onMouseLeave={(e) =>
                      (e.currentTarget.style.backgroundColor =
                        "#7e9b84")
                    }
                    onClick={handleAddToCart}
                  >
                    <CartIcon className="w-4 h-4 sm:w-5 sm:h-5" />
                    <span className="hidden xs:inline sm:inline">
                      Pridať do košíka
                    </span>
                    <span className="xs:hidden sm:hidden">
                      Košík
                    </span>
                  </Button>
                </div>

                {/* Payment Methods */}
                <div className="pt-4">
                  <p className="text-xs text-gray-500 mb-2.5 text-center">
                    Bezpečné platobné metódy
                  </p>
                  <div className="flex items-center justify-center gap-2 flex-wrap">
                    <div className="h-8 px-3 bg-white border border-gray-200 rounded-md flex items-center justify-center">
                      <img
                        src={visaLogo}
                        alt="Visa"
                        className="h-4"
                      />
                    </div>
                    <div className="h-8 px-3 bg-white border border-gray-200 rounded-md flex items-center justify-center">
                      <img
                        src={mastercardLogo}
                        alt="Mastercard"
                        className="h-4"
                      />
                    </div>
                    <div className="h-8 px-3 bg-white border border-gray-200 rounded-md flex items-center justify-center">
                      <img
                        src={applePayLogo}
                        alt="Apple Pay"
                        className="h-4"
                      />
                    </div>
                    <div className="h-8 px-3 bg-white border border-gray-200 rounded-md flex items-center justify-center">
                      <img
                        src={googlePayLogo}
                        alt="Google Pay"
                        className="h-4"
                      />
                    </div>
                    <div className="h-8 px-3 bg-white border border-gray-200 rounded-md flex items-center justify-center">
                      <img
                        src={paypalLogo}
                        alt="PayPal"
                        className="h-4"
                      />
                    </div>
                  </div>
                </div>
              </div>

              {/* Help/Contact Box */}
              <div className="relative bg-gradient-to-br from-[#7e9b84]/10 via-white to-[#7e9b84]/5 border-2 border-[#7e9b84]/30 rounded-xl p-4 overflow-hidden">
                <div className="absolute top-0 right-0 w-20 h-20 bg-[#7e9b84]/10 rounded-full blur-2xl -z-10"></div>

                <div className="flex items-center justify-between gap-4">
                  <div className="flex items-center gap-3 flex-1 min-w-0">
                    <div className="relative flex-shrink-0">
                      <img
                        src={supportImage}
                        alt="Podpora"
                        className="w-12 h-12 rounded-full object-cover border-2 border-white shadow"
                      />
                      <div className="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                    </div>

                    <div className="flex-1 min-w-0">
                      <h3 className="font-semibold text-base text-gray-900 mb-0.5">
                        Máte otázky ohľadom produktu?
                      </h3>
                      <p className="text-sm text-gray-700 leading-relaxed">
                        Ak máte záujem o úpravu produktu na mieru alebo potrebujete poradiť, neváhajte nás kontaktovať.
                      </p>
                    </div>
                  </div>

                  <Button
                    variant="outline"
                    className="text-[#7e9b84] border-2 border-[#7e9b84] hover:bg-[#7e9b84]/5 text-sm font-medium h-10 px-6 flex-shrink-0"
                  >
                    Kontaktujte nás
                  </Button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Customer Inspiration Gallery */}
      <CustomerInspirationGallery />

      {/* Product Details Accordions - Modern Shopify Style */}
      <section
        id="product-description"
        className="bg-gray-50 scroll-mt-20"
      >
        {/* Horizontal divider before first section */}
        <div className="border-t border-gray-200"></div>
        
        <div className="space-y-0 divide-y divide-gray-200">
          {/* Popis a Technické špecifikácie - Combined Accordion */}
          <div className="bg-white">
            <div className="max-w-7xl mx-auto px-4">
              <button
                onClick={() =>
                  setOpenAccordion(
                    openAccordion === "description"
                      ? null
                      : "description",
                  )
                }
                className="w-full flex items-center justify-between py-4 md:py-6 text-left group transition-colors"
              >
                <div className="flex items-center gap-3">
                  <FileText className="w-5 h-5 md:w-6 md:h-6 text-[#7e9b84] flex-shrink-0" />
                  <h3 className="text-lg md:text-xl font-semibold text-gray-900 group-hover:text-[#7e9b84] transition-colors">
                    Popis a Technické špecifikácie
                  </h3>
                </div>
                {openAccordion === "description" ? (
                  <Minus className="w-5 h-5 md:w-6 md:h-6 text-gray-900 group-hover:text-[#7e9b84] flex-shrink-0 transition-colors" />
                ) : (
                  <Plus className="w-5 h-5 md:w-6 md:h-6 text-gray-900 group-hover:text-[#7e9b84] flex-shrink-0 transition-colors" />
                )}
              </button>

              <AnimatePresence>
                {openAccordion === "description" && (
                  <motion.div
                    initial={{ height: 0, opacity: 0 }}
                    animate={{ height: "auto", opacity: 1 }}
                    exit={{ height: 0, opacity: 0 }}
                    transition={{ duration: 0.25, ease: "easeInOut" }}
                    className="overflow-hidden"
                  >
                    <div className="pb-6 md:pb-8 bg-white">
                      {/* Two Column Layout: Description Left, Specs Table Right */}
                      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-12">
                        {/* Left: Description */}
                        <div className="space-y-4">
                          <p className="text-gray-700 leading-relaxed">
                            <strong>
                              Stolová noha Acamar Frame Extreme
                              X-tvar
                            </strong>{" "}
                            je ideálnym riešením pre každého,
                            kto hľadá kombináciu moderného
                            dizajnu a extrémnej stability.
                          </p>
                          
                          <div className="space-y-3">
                            <div className="flex items-start gap-3">
                              <div className="w-1.5 h-1.5 rounded-full bg-[#7e9b84] mt-2 flex-shrink-0"></div>
                              <p className="text-gray-700 leading-relaxed">
                                Vyrobené z <strong>kvalitnej ocele</strong> s hrúbkou materiálu 3 mm pre maximálnu stabilitu a dlhú životnosť
                              </p>
                            </div>
                            <div className="flex items-start gap-3">
                              <div className="w-1.5 h-1.5 rounded-full bg-[#7e9b84] mt-2 flex-shrink-0"></div>
                              <p className="text-gray-700 leading-relaxed">
                                Povrchová úprava prášková farba poskytuje elegantný vzhľad a ochranu proti korózii
                              </p>
                            </div>
                            <div className="flex items-start gap-3">
                              <div className="w-1.5 h-1.5 rounded-full bg-[#7e9b84] mt-2 flex-shrink-0"></div>
                              <p className="text-gray-700 leading-relaxed">
                                Vhodné pre stoly s hrúbkou dosky 20-40 mm
                              </p>
                            </div>
                            <div className="flex items-start gap-3">
                              <div className="w-1.5 h-1.5 rounded-full bg-[#7e9b84] mt-2 flex-shrink-0"></div>
                              <p className="text-gray-700 leading-relaxed">
                                Kompletné montážne príslušenstvo pre jednoduché a bezpečné pripevnenie
                              </p>
                            </div>
                          </div>
                        </div>

                        {/* Right: Specifications Table */}
                        <div>
                          <h4 className="font-semibold text-gray-900 mb-4 md:mb-5 text-base md:text-lg">
                            Technické parametre
                          </h4>
                          <div className="space-y-0 bg-gray-50 rounded-xl overflow-hidden border border-gray-200">
                            {specs.map((spec, index) => (
                              <div
                                key={index}
                                className="flex flex-col sm:grid sm:grid-cols-2 gap-2 sm:gap-4 p-3 md:py-3.5 border-b border-gray-200 last:border-0 hover:bg-white transition-colors"
                              >
                                <div className="text-xs sm:text-sm font-medium text-gray-600 sm:pl-4">
                                  {spec.label}
                                </div>
                                <div className="text-sm sm:text-sm text-gray-900 font-semibold sm:pr-4">
                                  {spec.value}
                                </div>
                              </div>
                            ))}
                          </div>
                        </div>
                      </div>
                    </div>
                  </motion.div>
                )}
              </AnimatePresence>
            </div>
          </div>

          {/* FAQ - Accordion */}
          <div className="bg-white">
            <div className="max-w-7xl mx-auto px-4">
              <button
                onClick={() =>
                  setOpenAccordion(
                    openAccordion === "faq" ? null : "faq",
                  )
                }
                className="w-full flex items-center justify-between py-4 md:py-6 text-left group transition-colors"
              >
                <div className="flex items-center gap-3">
                  <MessageCircle className="w-5 h-5 md:w-6 md:h-6 text-[#7e9b84] flex-shrink-0" />
                  <h3 className="text-lg md:text-xl font-semibold text-gray-900 group-hover:text-[#7e9b84] transition-colors">
                    Často kladené otázky
                  </h3>
                </div>
                {openAccordion === "faq" ? (
                  <Minus className="w-5 h-5 md:w-6 md:h-6 text-gray-900 group-hover:text-[#7e9b84] flex-shrink-0 transition-colors" />
                ) : (
                  <Plus className="w-5 h-5 md:w-6 md:h-6 text-gray-900 group-hover:text-[#7e9b84] flex-shrink-0 transition-colors" />
                )}
              </button>

              <AnimatePresence>
                {openAccordion === "faq" && (
                  <motion.div
                    initial={{ height: 0, opacity: 0 }}
                    animate={{ height: "auto", opacity: 1 }}
                    exit={{ height: 0, opacity: 0 }}
                    transition={{ duration: 0.25, ease: "easeInOut" }}
                    className="overflow-hidden"
                  >
                    <div className="pb-6 md:pb-8 space-y-3 md:space-y-4 bg-white">
                      {faqs.map((faq, index) => (
                        <div
                          key={index}
                          className="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-md transition-shadow"
                        >
                          <button
                            onClick={() =>
                              setOpenFaqIndex(
                                openFaqIndex === index
                                  ? null
                                  : index,
                              )
                            }
                            className="w-full flex items-center justify-between px-4 md:px-5 py-3 md:py-4 text-left hover:bg-gray-50 transition-colors group"
                          >
                            <h4 className="font-medium text-gray-900 pr-4 text-sm md:text-base group-hover:text-[#7e9b84] transition-colors">
                              {faq.question}
                            </h4>
                            {openFaqIndex === index ? (
                              <ChevronUp className="w-4 h-4 md:w-5 md:h-5 text-[#7e9b84] flex-shrink-0" />
                            ) : (
                              <ChevronDown className="w-4 h-4 md:w-5 md:h-5 text-gray-600 flex-shrink-0" />
                            )}
                          </button>

                          <AnimatePresence>
                            {openFaqIndex === index && (
                              <motion.div
                                initial={{
                                  height: 0,
                                  opacity: 0,
                                }}
                                animate={{
                                  height: "auto",
                                  opacity: 1,
                                }}
                                exit={{ height: 0, opacity: 0 }}
                                transition={{ duration: 0.2, ease: "easeInOut" }}
                                className="overflow-hidden"
                              >
                                <div className="px-4 md:px-5 pb-3 md:pb-4 pt-0 text-gray-700 leading-relaxed bg-gray-50 text-sm md:text-base">
                                  {faq.answer}
                                </div>
                              </motion.div>
                            )}
                          </AnimatePresence>
                        </div>
                      ))}
                    </div>
                  </motion.div>
                )}
              </AnimatePresence>
            </div>
          </div>

          {/* Recenzie - Accordion */}
          <div className="bg-white">
            <div className="max-w-7xl mx-auto px-4">
              <button
                onClick={() =>
                  setOpenAccordion(
                    openAccordion === "reviews"
                      ? null
                      : "reviews",
                  )
                }
                className="w-full flex items-center justify-between py-4 md:py-6 text-left group transition-colors"
              >
                <div className="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-3">
                  <div className="flex items-center gap-3">
                    <Star className="w-5 h-5 md:w-6 md:h-6 text-[#7e9b84] flex-shrink-0 fill-[#7e9b84]" />
                    <h3 className="text-lg md:text-xl font-semibold text-gray-900 group-hover:text-[#7e9b84] transition-colors">
                      Recenzie zákazníkov
                    </h3>
                  </div>
                  <span className="text-xs md:text-sm font-normal text-gray-600 ml-8 sm:ml-0">
                    ({mockReviews.length} hodnotení •{" "}
                    {averageRating.toFixed(1)} ⭐)
                  </span>
                </div>
                {openAccordion === "reviews" ? (
                  <Minus className="w-5 h-5 md:w-6 md:h-6 text-gray-900 group-hover:text-[#7e9b84] flex-shrink-0 transition-colors" />
                ) : (
                  <Plus className="w-5 h-5 md:w-6 md:h-6 text-gray-900 group-hover:text-[#7e9b84] flex-shrink-0 transition-colors" />
                )}
              </button>

              <AnimatePresence>
                {openAccordion === "reviews" && (
                  <motion.div
                    initial={{ height: 0, opacity: 0 }}
                    animate={{ height: "auto", opacity: 1 }}
                    exit={{ height: 0, opacity: 0 }}
                    transition={{ duration: 0.25, ease: "easeInOut" }}
                    className="overflow-hidden"
                  >
                    <div className="pb-6 md:pb-8 space-y-5 md:space-y-6 bg-white">
                      {/* Rating Summary */}
                      <div className="bg-gradient-to-br from-yellow-50 to-orange-50 p-5 md:p-6 rounded-xl border border-yellow-100">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                          {/* Average Rating */}
                          <div className="flex flex-col items-center justify-center text-center">
                            <div className="text-5xl md:text-6xl font-bold text-gray-900 mb-2">
                              {averageRating.toFixed(1)}
                            </div>
                            <div className="flex items-center gap-1 mb-2">
                              {[...Array(5)].map((_, i) => (
                                <Star
                                  key={i}
                                  className={`w-5 h-5 md:w-6 md:h-6 ${
                                    i <
                                    Math.floor(averageRating)
                                      ? "fill-yellow-400 text-yellow-400"
                                      : "text-gray-300"
                                  }`}
                                />
                              ))}
                            </div>
                            <p className="text-sm md:text-base text-gray-700">
                              Na základe {mockReviews.length}{" "}
                              recenzií
                            </p>
                          </div>

                          {/* Rating Bars */}
                          <div className="space-y-2">
                            {[5, 4, 3, 2, 1].map((rating) => {
                              const count = mockReviews.filter(
                                (r) => r.rating === rating,
                              ).length;
                              const percentage =
                                (count / mockReviews.length) *
                                100;
                              return (
                                <div
                                  key={rating}
                                  className="flex items-center gap-2 md:gap-3"
                                >
                                  <div className="flex items-center gap-1 w-12 md:w-16">
                                    <span className="text-xs md:text-sm font-medium text-gray-700">
                                      {rating}
                                    </span>
                                    <Star className="w-3 h-3 md:w-4 md:h-4 fill-yellow-400 text-yellow-400" />
                                  </div>
                                  <div className="flex-1 h-2.5 md:h-3 bg-gray-200 rounded-full overflow-hidden">
                                    <div
                                      className="h-full bg-yellow-400 transition-all duration-500"
                                      style={{
                                        width: `${percentage}%`,
                                      }}
                                    />
                                  </div>
                                  <span className="text-xs md:text-sm text-gray-600 w-8 md:w-12 text-right">
                                    {count}
                                  </span>
                                </div>
                              );
                            })}
                          </div>
                        </div>
                      </div>

                      {/* Filter Buttons */}
                      <div className="overflow-x-auto -mx-4 px-4 md:mx-0 md:px-0">
                        <div className="flex items-center gap-2 md:gap-3 min-w-max md:min-w-0 md:flex-wrap">
                          <button
                            onClick={() => setReviewFilter("all")}
                            className={`px-3 md:px-4 py-2 rounded-full font-medium transition-all text-xs md:text-sm whitespace-nowrap ${
                              reviewFilter === "all"
                                ? "bg-[#7e9b84] text-white shadow-md"
                                : "bg-gray-100 text-gray-700 hover:bg-gray-200"
                            }`}
                          >
                            Všetky ({mockReviews.length})
                          </button>
                          <button
                            onClick={() => setReviewFilter(5)}
                            className={`px-3 md:px-4 py-2 rounded-full font-medium transition-all text-xs md:text-sm whitespace-nowrap ${
                              reviewFilter === 5
                                ? "bg-[#7e9b84] text-white shadow-md"
                                : "bg-gray-100 text-gray-700 hover:bg-gray-200"
                            }`}
                          >
                            5 ⭐ (
                            {
                              mockReviews.filter(
                                (r) => r.rating === 5,
                              ).length
                            }
                            )
                          </button>
                          <button
                            onClick={() => setReviewFilter(4)}
                            className={`px-3 md:px-4 py-2 rounded-full font-medium transition-all text-xs md:text-sm whitespace-nowrap ${
                              reviewFilter === 4
                                ? "bg-[#7e9b84] text-white shadow-md"
                                : "bg-gray-100 text-gray-700 hover:bg-gray-200"
                            }`}
                          >
                            4 ⭐ (
                            {
                              mockReviews.filter(
                                (r) => r.rating === 4,
                              ).length
                            }
                            )
                          </button>
                          <button
                            onClick={() =>
                              setReviewFilter("photos")
                            }
                            className={`px-3 md:px-4 py-2 rounded-full font-medium transition-all flex items-center gap-1.5 md:gap-2 text-xs md:text-sm whitespace-nowrap ${
                              reviewFilter === "photos"
                                ? "bg-[#7e9b84] text-white shadow-md"
                                : "bg-gray-100 text-gray-700 hover:bg-gray-200"
                            }`}
                          >
                            <ImageIcon className="w-3.5 h-3.5 md:w-4 md:h-4" />
                            S fotkami (
                            {
                              mockReviews.filter(
                                (r) =>
                                  r.images && r.images.length > 0,
                              ).length
                            }
                            )
                          </button>
                        </div>
                      </div>

                      {/* Reviews List */}
                      {filteredReviews.length > 0 ? (
                        <div className="space-y-5 md:space-y-6">
                          {filteredReviews.map((review) => (
                            <div
                              key={review.id}
                              className="border-b border-gray-200 last:border-0 pb-5 md:pb-6 last:pb-0"
                            >
                              <div className="flex items-start gap-3 md:gap-4 mb-3">
                                {/* Avatar with initials */}
                                <div className="w-10 h-10 md:w-12 md:h-12 rounded-full bg-[#7e9b84]/10 flex items-center justify-center flex-shrink-0 border-2 border-[#7e9b84]/20">
                                  <span className="text-[#7e9b84] font-semibold text-sm md:text-base">
                                    {review.name.charAt(0)}
                                  </span>
                                </div>
                                
                                <div className="flex-1">
                                  <div className="flex items-start justify-between mb-2">
                                    <div>
                                      <div className="flex items-center gap-2 mb-1">
                                        <span className="font-medium text-base md:text-lg">
                                          {review.name}
                                        </span>
                                        {review.verified && (
                                          <span className="flex items-center gap-1 text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                                            <Award className="w-3 h-3" />
                                            Overený
                                          </span>
                                        )}
                                      </div>
                                      <div className="flex items-center gap-1">
                                        {[...Array(5)].map((_, i) => (
                                          <Star
                                            key={i}
                                            className={`w-3.5 h-3.5 md:w-4 md:h-4 ${
                                              i < review.rating
                                                ? "fill-yellow-400 text-yellow-400"
                                                : "text-gray-300"
                                            }`}
                                          />
                                        ))}
                                      </div>
                                    </div>
                                    <span className="text-xs md:text-sm text-gray-500">
                                      {review.date}
                                    </span>
                                  </div>
                                  <p className="text-gray-700 leading-relaxed mb-3 md:mb-4 text-sm md:text-base">
                                    {review.comment}
                                  </p>
                                  {review.images &&
                                    review.images.length > 0 && (
                                      <div className="grid grid-cols-2 sm:grid-cols-3 gap-2 md:gap-3">
                                        {review.images.map(
                                          (image, index) => (
                                            <div
                                              key={index}
                                              className="aspect-square rounded-lg overflow-hidden border border-gray-200"
                                            >
                                              <img
                                                src={image}
                                                alt={`Fotografia zákazníka ${index + 1}`}
                                                className="w-full h-full object-cover hover:scale-110 transition-transform duration-300 cursor-pointer"
                                              />
                                            </div>
                                          ),
                                        )}
                                      </div>
                                    )}
                                </div>
                              </div>
                            </div>
                          ))}
                        </div>
                      ) : (
                        <div className="text-center py-8 md:py-12">
                          <div className="w-16 h-16 md:w-20 md:h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <Star className="w-8 h-8 md:w-10 md:h-10 text-gray-400" />
                          </div>
                          <h4 className="font-semibold text-gray-900 mb-2 text-base md:text-lg">
                            Žiadne recenzie nenájdené
                          </h4>
                          <p className="text-sm md:text-base text-gray-600">
                            Skúste zmeniť filter alebo pridajte prvú recenziu!
                          </p>
                        </div>
                      )}

                      {/* Write Review CTA */}
                      <div className="pt-4 md:pt-6 border-t border-gray-200">
                        <Button
                          className="w-full sm:w-auto text-white flex items-center justify-center gap-2"
                          style={{ backgroundColor: "#7e9b84" }}
                          onMouseEnter={(e) =>
                            (e.currentTarget.style.backgroundColor =
                              "#6d8a73")
                          }
                          onMouseLeave={(e) =>
                            (e.currentTarget.style.backgroundColor =
                              "#7e9b84")
                          }
                        >
                          <Send className="w-4 h-4 md:w-5 md:h-5" />
                          <span>Napísať recenziu</span>
                        </Button>
                      </div>
                    </div>
                  </motion.div>
                )}
              </AnimatePresence>
            </div>
          </div>
        </div>
        
        {/* Horizontal divider after last section */}
        <div className="border-t border-gray-200"></div>
      </section>

      {/* Related Products */}
      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4">
          {/* Header */}
          <div className="text-center mb-12">
            <div className="inline-block px-4 py-2 bg-[#7e9b84] text-white rounded-full mb-4">
              Podobné produkty
            </div>
            <h2 className="text-3xl md:text-4xl font-bold mb-4">
              Mohlo by vás zaujímať
            </h2>
            <p
              className="text-lg max-w-2xl mx-auto"
              style={{ color: "#666" }}
            >
              Ďalšie kvalitné stolové nohy z našej ponuky
            </p>
          </div>

          {/* Carousel */}
          <div className="related-products-carousel">
            <Slider
              dots={false}
              infinite={true}
              speed={500}
              slidesToShow={4}
              slidesToScroll={1}
              autoplay={true}
              autoplaySpeed={3000}
              pauseOnHover={true}
              prevArrow={<PrevArrow />}
              nextArrow={<NextArrow />}
              responsive={[
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
              ]}
            >
              {relatedProducts.map((product) => (
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
        </div>

        <style>{`
          .related-products-carousel .slick-dots {
            bottom: -50px;
          }
          
          .related-products-carousel .slick-dots li button:before {
            font-size: 12px;
            color: #7e9b84;
          }
          
          .related-products-carousel .slick-dots li.slick-active button:before {
            color: #7e9b84;
          }
        `}</style>
      </section>

      {/* Trust Banner */}
      <TrustBanner />

      {/* Sticky Add to Cart - Mobile & Desktop */}
      <AnimatePresence>
        {showStickyBar && (
          <motion.div
            initial={{ y: 100, opacity: 0 }}
            animate={{ y: 0, opacity: 1 }}
            exit={{ y: 100, opacity: 0 }}
            transition={{ duration: 0.3 }}
            className="fixed bottom-0 left-0 right-0 bg-white border-t-2 border-gray-200 shadow-2xl z-50"
          >
            {/* Mobile Version */}
            <div className="lg:hidden p-3">
              <div className="flex items-center gap-3">
                {/* Product Image & Info */}
                <img
                  src={images[selectedImage]}
                  alt="Stolová Noha Acamar"
                  className="w-12 h-12 object-contain bg-gray-50 rounded-lg border border-gray-200 flex-shrink-0"
                />
                <div className="flex-1 min-w-0">
                  <p className="text-xs text-gray-600 truncate">
                    Stolová Noha Acamar
                  </p>
                  <div className="flex items-center gap-2">
                    <span className="text-xl font-bold text-[#7e9b84]">
                      38,90 €
                    </span>
                    <span className="text-xs text-gray-400 line-through">
                      45,90 €
                    </span>
                  </div>
                </div>
                {/* Add to Cart Button */}
                <Button
                  className="text-white flex items-center justify-center gap-1 h-12 px-4 flex-shrink-0"
                  style={{ backgroundColor: "#7e9b84" }}
                  onMouseEnter={(e) =>
                    (e.currentTarget.style.backgroundColor = "#6d8a73")
                  }
                  onMouseLeave={(e) =>
                    (e.currentTarget.style.backgroundColor = "#7e9b84")
                  }
                  onClick={handleAddToCart}
                >
                  <CartIcon className="w-5 h-5" />
                  <span className="hidden xs:inline">Košík</span>
                </Button>
              </div>
            </div>

            {/* Desktop Version */}
            <div className="max-w-7xl mx-auto px-4 py-4 hidden lg:block">
              <div className="flex items-center justify-between gap-6">
                {/* Product Info */}
                <div className="flex items-center gap-4">
                  <img
                    src={images[selectedImage]}
                    alt="Stolová Noha Acamar"
                    className="w-16 h-16 object-contain bg-gray-50 rounded-lg border border-gray-200"
                  />
                  <div>
                    <h3 className="font-semibold text-gray-900 mb-1">
                      Stolová Noha - Acamar Frame Extreme X-tvar
                    </h3>
                    <div className="flex items-center gap-2">
                      <span className="text-2xl font-bold text-[#7e9b84]">
                        38,90 €
                      </span>
                      <span className="text-sm text-gray-400 line-through">
                        45,90 €
                      </span>
                      <span className="bg-red-500 text-white px-2 py-0.5 rounded-full text-xs font-bold">
                        -15%
                      </span>
                    </div>
                  </div>
                </div>

                {/* Quantity & Add to Cart */}
                <div className="flex items-center gap-4">
                  {/* Quantity Selector */}
                  <div className="flex items-center border-2 border-gray-200 rounded-lg overflow-hidden bg-white">
                    <button
                      onClick={decrementQuantity}
                      className="p-3 hover:bg-gray-100 transition-colors"
                    >
                      <Minus className="w-5 h-5" />
                    </button>
                    <span className="w-16 text-center font-medium">
                      {quantity}
                    </span>
                    <button
                      onClick={incrementQuantity}
                      className="p-3 hover:bg-gray-100 transition-colors"
                    >
                      <Plus className="w-5 h-5" />
                    </button>
                  </div>

                  {/* Add to Cart Button */}
                  <Button
                    className="text-white flex items-center justify-center gap-2 h-[52px] px-8 text-lg font-semibold"
                    style={{ backgroundColor: "#7e9b84" }}
                    onMouseEnter={(e) =>
                      (e.currentTarget.style.backgroundColor = "#6d8a73")
                    }
                    onMouseLeave={(e) =>
                      (e.currentTarget.style.backgroundColor = "#7e9b84")
                    }
                    onClick={handleAddToCart}
                  >
                    <CartIcon className="w-5 h-5" />
                    <span>Pridať do košíka</span>
                  </Button>
                </div>
              </div>
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
}