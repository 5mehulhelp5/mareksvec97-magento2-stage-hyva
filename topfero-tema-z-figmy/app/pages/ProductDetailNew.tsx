import { useState } from "react";
import { motion, AnimatePresence } from "motion/react";
import { 
  Star, 
  Heart, 
  CheckCircle, 
  ShoppingCart, 
  Truck, 
  Minus, 
  Plus,
  Award,
  ChevronDown,
  ChevronUp,
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
  Filter
} from "lucide-react";
import { Button } from "../components/ui/button";
import { TrustBanner } from "../components/TrustBanner";
import { CustomerInspirationGallery } from "../components/CustomerInspirationGallery";

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
    comment: "Výborná kvalita, presne podľa popisu. Nohy sú pevné a stabilné, stôl z nich je perfektný. Odporúčam!",
    images: [
      "https://images.unsplash.com/photo-1759190068354-f59d0ba26b23?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=400",
      "https://images.unsplash.com/photo-1734126565187-e53d8debf6ab?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=400"
    ]
  },
  {
    id: 2,
    name: "Jana S.",
    rating: 5,
    date: "08.12.2024",
    verified: true,
    comment: "Super dizajn, moderný vzhľad. Montáž bola jednoduchá. Veľmi spokojná s nákupom.",
    images: [
      "https://images.unsplash.com/photo-1758977403341-0104135995af?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=400"
    ]
  },
  {
    id: 3,
    name: "Peter H.",
    rating: 4,
    date: "02.12.2024",
    verified: true,
    comment: "Kvalitné spracovanie, rýchle dodanie. Odporúčam pre všetkých, čo chcú moderný stôl.",
  },
  {
    id: 4,
    name: "Lucia M.",
    rating: 5,
    date: "28.11.2024",
    verified: true,
    comment: "Úžasný produkt! Presne to, co som hľadala. Stabilné, krásne a kvalitné.",
    images: [
      "https://images.unsplash.com/photo-1752061905248-e93a855489fe?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=400"
    ]
  },
  {
    id: 5,
    name: "Tomáš B.",
    rating: 5,
    date: "20.11.2024",
    verified: true,
    comment: "Perfektná stabilita, moderný look. Jednoznačne odporúčam!",
  },
  {
    id: 6,
    name: "Eva K.",
    rating: 4,
    date: "15.11.2024",
    verified: false,
    comment: "Pekné nohy, len montáž trvala dlhšie ako som čakala. Inak super!",
  },
];

export function ProductDetail() {
  const [selectedImage, setSelectedImage] = useState(0);
  const [selectedHeight, setSelectedHeight] = useState("72cm");
  const [selectedColor, setSelectedColor] = useState("black");
  const [quantity, setQuantity] = useState(1);
  const [isFavorite, setIsFavorite] = useState(false);
  const [openAccordion, setOpenAccordion] = useState<string | null>("description");
  const [openFaqIndex, setOpenFaqIndex] = useState<number | null>(null);
  const [reviewFilter, setReviewFilter] = useState<"all" | 5 | 4 | 3 | "photos">("all");

  const images = [productImage1, productImage2, productImage3, productImage1];
  
  const heights = [
    { value: "70cm", label: "70cm", inStock: true },
    { value: "72cm", label: "72cm", inStock: true },
    { value: "75cm", label: "75cm", inStock: true },
    { value: "80cm", label: "80cm", inStock: false },
  ];

  const colors = [
    { value: "black", label: "Čierna matná", hex: "#000000", inStock: true },
    { value: "white", label: "Biela", hex: "#FFFFFF", inStock: true },
    { value: "gray", label: "Antracit", hex: "#4A4A4A", inStock: true },
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
      answer: "V balení sú 2 kusy (1 pár) stolových nôh. Pre klasický 4-nohý stôl potrebujete objednať 2 balenia (spolu 4 nohy).",
    },
    {
      question: "Sú nohy vhodné aj na vonkajšie použitie?",
      answer: "Nohy sú určené primárne na vnútorné použitie. Pre vonkajšie použitie odporúčame galvanizovanú verziu s dodatočnou povrchovou úpravou.",
    },
    {
      question: "Aká je maximálna nosnosť?",
      answer: "Jedna noha unesie až 150 kg. Pri správnej montáži a rozložení váhy môže stôl uniesť značnú záťaž.",
    },
    {
      question: "Ako sa nohy montujú k doske?",
      answer: "Nohy sa montujú pomocou priložených skrutiek. Odporúčame vyvŕtať predvŕtané otvory v doske stola pre najlepšiu stabilitu.",
    },
  ];

  const relatedProducts = [
    {
      id: 1,
      name: "Stolová noha Leg - Hairpin 72cm",
      image: productImage1,
      price: "24,90 €",
      rating: 4.8,
    },
    {
      id: 2,
      name: "Stolová Noha - Acamar Spider 75cm",
      image: productImage3,
      price: "32,50 €",
      rating: 4.9,
    },
    {
      id: 3,
      name: "Centrálne nohy Y-tvar 72cm",
      image: productImage2,
      price: "36,90 €",
      rating: 4.7,
    },
  ];

  const incrementQuantity = () => setQuantity(prev => Math.min(prev + 1, 10));
  const decrementQuantity = () => setQuantity(prev => Math.max(prev - 1, 1));

  const averageRating = mockReviews.reduce((acc, review) => acc + review.rating, 0) / mockReviews.length;

  const scrollToDescription = () => {
    const descriptionElement = document.getElementById("product-description");
    if (descriptionElement) {
      descriptionElement.scrollIntoView({ behavior: "smooth", block: "start" });
    }
  };

  const filteredReviews = mockReviews.filter(review => {
    if (reviewFilter === "all") return true;
    if (reviewFilter === "photos") return review.images && review.images.length > 0;
    return review.rating === reviewFilter;
  });

  return (
    <div className="min-h-screen bg-white">
      {/* Breadcrumb */}
      <div className="bg-gray-50 border-b border-gray-200">
        <div className="max-w-7xl mx-auto px-4 py-3">
          <nav className="flex items-center gap-2 text-sm text-gray-600">
            <a href="/" className="hover:text-[#7e9b84] transition-colors">Domov</a>
            <span>/</span>
            <a href="/stolove-nohy" className="hover:text-[#7e9b84] transition-colors">Stolové nohy</a>
            <span>/</span>
            <span className="text-gray-900 font-medium">Stolová Noha Acamar Frame Extreme X-tvar</span>
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
                {/* Badges */}
                <div className="absolute top-4 left-4 flex flex-col gap-2">
                  <span className="bg-blue-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                    NOVINKA
                  </span>
                  <span className="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1">
                    <CheckCircle className="w-3 h-3" />
                    SKLADOM
                  </span>
                </div>
                {/* Favorite */}
                <button
                  onClick={() => setIsFavorite(!isFavorite)}
                  className="absolute top-4 right-4 w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform"
                >
                  <Heart className={`w-5 h-5 ${isFavorite ? 'fill-red-500 text-red-500' : 'text-gray-600'}`} />
                </button>
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
                    {averageRating.toFixed(1)} ({mockReviews.length} recenzií)
                  </span>
                  <span className="text-green-600 font-medium">156 ks predaných</span>
                </div>
                <p className="text-gray-700 text-base leading-relaxed">
                  Štýlová stolová noha Acamar Frame Extreme s elegantným X-tvarom je perfektným riešením pre moderné interiéry. 
                  Vyrobená z kvalitnej ocele s práškovým povrchom zaručuje maximálnu stabilitu a dlhú životnosť.{" "}
                  <button 
                    onClick={scrollToDescription}
                    className="text-[#7e9b84] font-medium hover:underline"
                  >
                    Čítať viac
                  </button>
                </p>
              </div>

              {/* Height Selection */}
              <div>
                <label className="block mb-3">
                  <span className="font-medium">Výška:</span>
                  <span className="text-gray-600 ml-2">{selectedHeight}</span>
                </label>
                <div className="grid grid-cols-4 gap-3">
                  {heights.map((height) => (
                    <button
                      key={height.value}
                      onClick={() => height.inStock && setSelectedHeight(height.value)}
                      disabled={!height.inStock}
                      className={`py-3 px-4 rounded-lg border-2 font-medium transition-all ${
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
                    {colors.find(c => c.value === selectedColor)?.label}
                  </span>
                </label>
                <div className="flex items-center gap-3">
                  {colors.map((color) => (
                    <button
                      key={color.value}
                      onClick={() => color.inStock && setSelectedColor(color.value)}
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
                          border: color.value === 'white' ? '1px solid #e5e7eb' : 'none'
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
                    <span className="font-semibold">Dostupné</span>
                  </div>
                </div>

                {/* Delivery Time */}
                <div className="flex items-center gap-2 mb-4 text-gray-700">
                  <Truck className="w-5 h-5 text-gray-600" />
                  <span className="text-sm">
                    Čas doručenia: <strong>Skladom - U vás do 8 - 12 dní</strong>
                  </span>
                </div>

                {/* Price */}
                <div className="flex items-baseline gap-3 mb-6 pb-6 border-b border-gray-300">
                  <span className="text-4xl font-bold text-[#7e9b84]">38,90 €</span>
                  <span className="text-xl text-gray-400 line-through">45,90 €</span>
                  <span className="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                    -15%
                  </span>
                </div>

                {/* Quantity & Add to Cart */}
                <div className="flex gap-2 sm:gap-3 items-stretch">
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
                    style={{ backgroundColor: '#7e9b84' }}
                    onMouseEnter={(e) => e.currentTarget.style.backgroundColor = '#6d8a73'}
                    onMouseLeave={(e) => e.currentTarget.style.backgroundColor = '#7e9b84'}
                  >
                    <ShoppingCart className="w-4 h-4 sm:w-5 sm:h-5" />
                    <span className="hidden xs:inline sm:inline">Pridať do košíka</span>
                    <span className="xs:hidden sm:hidden">Košík</span>
                  </Button>
                </div>

                <p className="text-xs text-gray-600 mt-4">Cena za 1 pár (2 kusy)</p>

                {/* Payment Methods */}
                <div className="pt-4 border-t border-gray-200">
                  <p className="text-sm text-gray-600 mb-3 text-center">Bezpečné platobné metódy</p>
                  <div className="flex items-center justify-center gap-3 flex-wrap">
                    <div className="h-10 px-4 bg-white border-2 border-gray-200 rounded-lg flex items-center justify-center">
                      <img src={visaLogo} alt="Visa" className="h-6" />
                    </div>
                    <div className="h-10 px-4 bg-white border-2 border-gray-200 rounded-lg flex items-center justify-center">
                      <img src={mastercardLogo} alt="Mastercard" className="h-6" />
                    </div>
                    <div className="h-10 px-4 bg-white border-2 border-gray-200 rounded-lg flex items-center justify-center">
                      <img src={applePayLogo} alt="Apple Pay" className="h-6" />
                    </div>
                    <div className="h-10 px-4 bg-white border-2 border-gray-200 rounded-lg flex items-center justify-center">
                      <img src={googlePayLogo} alt="Google Pay" className="h-6" />
                    </div>
                    <div className="h-10 px-4 bg-white border-2 border-gray-200 rounded-lg flex items-center justify-center">
                      <img src={paypalLogo} alt="PayPal" className="h-6" />
                    </div>
                  </div>
                </div>
              </div>

              {/* Help/Contact Box */}
              <div className="relative bg-gradient-to-br from-[#7e9b84]/10 via-white to-[#7e9b84]/5 border-2 border-[#7e9b84]/30 rounded-xl p-4 overflow-hidden">
                <div className="absolute top-0 right-0 w-20 h-20 bg-[#7e9b84]/10 rounded-full blur-2xl -z-10"></div>
                
                <div className="flex items-center gap-3">
                  <div className="relative flex-shrink-0">
                    <img 
                      src={supportImage} 
                      alt="Podpora" 
                      className="w-12 h-12 rounded-full object-cover border-2 border-white shadow"
                    />
                    <div className="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                  </div>
                  
                  <div className="flex-1 min-w-0">
                    <h3 className="font-semibold text-base text-gray-900 mb-1">
                      Máte otázky ohľadom produktu?
                    </h3>
                    <p className="text-sm text-gray-700 mb-3 leading-relaxed">
                      Ak máte záujem o úpravu produktu na mieru alebo potrebujete poradiť, neváhajte nás kontaktovať.
                    </p>
                    
                    <Button
                      variant="outline"
                      className="w-full text-[#7e9b84] border-2 border-[#7e9b84] hover:bg-[#7e9b84]/5 text-sm font-medium h-9"
                    >
                      Kontaktujte nás
                    </Button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Customer Inspiration Gallery */}
      <CustomerInspirationGallery />

      {/* Product Details Accordions - Modern Shopify Style */}
      <section id="product-description" className="py-12 bg-white scroll-mt-20">
        <div className="max-w-7xl mx-auto px-4">
          <div className="space-y-4">
            
            {/* Popis a Výhody - Accordion */}
            <div className="border border-gray-200 rounded-xl overflow-hidden bg-white shadow-sm">
              <button
                onClick={() => setOpenAccordion(openAccordion === "description" ? null : "description")}
                className="w-full flex items-center justify-between px-6 py-5 text-left hover:bg-gray-50 transition-colors"
              >
                <div className="flex items-center gap-3">
                  <Sparkles className="w-6 h-6 text-[#7e9b84]" />
                  <h3 className="text-xl font-semibold text-gray-900">Popis a Výhody</h3>
                </div>
                {openAccordion === "description" ? (
                  <ChevronUp className="w-6 h-6 text-gray-600" />
                ) : (
                  <ChevronDown className="w-6 h-6 text-gray-600" />
                )}
              </button>
              
              <AnimatePresence>
                {openAccordion === "description" && (
                  <motion.div
                    initial={{ height: 0, opacity: 0 }}
                    animate={{ height: "auto", opacity: 1 }}
                    exit={{ height: 0, opacity: 0 }}
                    transition={{ duration: 0.3 }}
                    className="overflow-hidden"
                  >
                    <div className="px-6 pb-6 space-y-6">
                      {/* Description */}
                      <div className="prose max-w-none">
                        <p className="text-gray-700 leading-relaxed mb-4">
                          <strong>Stolová noha Acamar Frame Extreme X-tvar</strong> je ideálnym riešením pre každého, kto hľadá kombináciu 
                          moderného dizajnu a extrémnej stability. Tieto nohy sú navrhnuté s dôrazom na detail a remeselné spracovanie.
                        </p>
                        <p className="text-gray-700 leading-relaxed mb-6">
                          Každá noha je vyrobená z <strong>kvalitnej ocele</strong> s hrúbkou materiálu 3 mm, čo zaručuje masívnu silu 
                          a dlhú životnosť. Povrchová úprava prášková farba poskytuje nielen elegantný vzhľad, ale aj ochranu proti korózii 
                          a mechanickému poškodeniu.
                        </p>
                      </div>

                      {/* Benefits Grid */}
                      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div className="bg-gradient-to-br from-[#7e9b84]/5 to-[#7e9b84]/10 p-5 rounded-xl border border-[#7e9b84]/20">
                          <div className="flex items-start gap-3">
                            <div className="p-2 bg-[#7e9b84] rounded-lg">
                              <Shield className="w-6 h-6 text-white" />
                            </div>
                            <div className="flex-1">
                              <h4 className="font-semibold text-gray-900 mb-1">Extrémna stabilita</h4>
                              <p className="text-sm text-gray-700">Nosnosť až 150 kg na jednu nohu</p>
                            </div>
                          </div>
                        </div>

                        <div className="bg-gradient-to-br from-[#7e9b84]/5 to-[#7e9b84]/10 p-5 rounded-xl border border-[#7e9b84]/20">
                          <div className="flex items-start gap-3">
                            <div className="p-2 bg-[#7e9b84] rounded-lg">
                              <Sparkles className="w-6 h-6 text-white" />
                            </div>
                            <div className="flex-1">
                              <h4 className="font-semibold text-gray-900 mb-1">Moderný dizajn</h4>
                              <p className="text-sm text-gray-700">Elegantný X-tvar pasuje do každého interiéru</p>
                            </div>
                          </div>
                        </div>

                        <div className="bg-gradient-to-br from-[#7e9b84]/5 to-[#7e9b84]/10 p-5 rounded-xl border border-[#7e9b84]/20">
                          <div className="flex items-start gap-3">
                            <div className="p-2 bg-[#7e9b84] rounded-lg">
                              <Layers className="w-6 h-6 text-white" />
                            </div>
                            <div className="flex-1">
                              <h4 className="font-semibold text-gray-900 mb-1">Kvalitné materiály</h4>
                              <p className="text-sm text-gray-700">Oceľ s práškovým povrchom</p>
                            </div>
                          </div>
                        </div>

                        <div className="bg-gradient-to-br from-[#7e9b84]/5 to-[#7e9b84]/10 p-5 rounded-xl border border-[#7e9b84]/20">
                          <div className="flex items-start gap-3">
                            <div className="p-2 bg-[#7e9b84] rounded-lg">
                              <Hammer className="w-6 h-6 text-white" />
                            </div>
                            <div className="flex-1">
                              <h4 className="font-semibold text-gray-900 mb-1">Jednoduchá montáž</h4>
                              <p className="text-sm text-gray-700">Vrátane montážnych skrutiek</p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </motion.div>
                )}
              </AnimatePresence>
            </div>

            {/* Špecifikácie - Accordion */}
            <div className="border border-gray-200 rounded-xl overflow-hidden bg-white shadow-sm">
              <button
                onClick={() => setOpenAccordion(openAccordion === "specs" ? null : "specs")}
                className="w-full flex items-center justify-between px-6 py-5 text-left hover:bg-gray-50 transition-colors"
              >
                <div className="flex items-center gap-3">
                  <Package className="w-6 h-6 text-[#7e9b84]" />
                  <h3 className="text-xl font-semibold text-gray-900">Technické špecifikácie</h3>
                </div>
                {openAccordion === "specs" ? (
                  <ChevronUp className="w-6 h-6 text-gray-600" />
                ) : (
                  <ChevronDown className="w-6 h-6 text-gray-600" />
                )}
              </button>
              
              <AnimatePresence>
                {openAccordion === "specs" && (
                  <motion.div
                    initial={{ height: 0, opacity: 0 }}
                    animate={{ height: "auto", opacity: 1 }}
                    exit={{ height: 0, opacity: 0 }}
                    transition={{ duration: 0.3 }}
                    className="overflow-hidden"
                  >
                    <div className="px-6 pb-6">
                      <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {specs.map((spec, index) => (
                          <div key={index} className="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
                            <div className="p-2 bg-white rounded-lg shadow-sm">
                              {spec.label === "Materiál" && <Layers className="w-5 h-5 text-[#7e9b84]" />}
                              {spec.label === "Nosnosť" && <Shield className="w-5 h-5 text-[#7e9b84]" />}
                              {spec.label === "Rozmer profilu" && <Ruler className="w-5 h-5 text-[#7e9b84]" />}
                              {spec.label === "Montáž" && <Hammer className="w-5 h-5 text-[#7e9b84]" />}
                              {spec.label === "Hmotnosť (pár)" && <Package className="w-5 h-5 text-[#7e9b84]" />}
                              {!["Materiál", "Nosnosť", "Rozmer profilu", "Montáž", "Hmotnosť (pár)"].includes(spec.label) && (
                                <CheckCircle className="w-5 h-5 text-[#7e9b84]" />
                              )}
                            </div>
                            <div className="flex-1">
                              <p className="text-sm text-gray-600 mb-1">{spec.label}</p>
                              <p className="font-semibold text-gray-900">{spec.value}</p>
                            </div>
                          </div>
                        ))}
                      </div>
                    </div>
                  </motion.div>
                )}
              </AnimatePresence>
            </div>

            {/* FAQ - Accordion */}
            <div className="border border-gray-200 rounded-xl overflow-hidden bg-white shadow-sm">
              <button
                onClick={() => setOpenAccordion(openAccordion === "faq" ? null : "faq")}
                className="w-full flex items-center justify-between px-6 py-5 text-left hover:bg-gray-50 transition-colors"
              >
                <div className="flex items-center gap-3">
                  <MessageCircle className="w-6 h-6 text-[#7e9b84]" />
                  <h3 className="text-xl font-semibold text-gray-900">Často kladené otázky</h3>
                </div>
                {openAccordion === "faq" ? (
                  <ChevronUp className="w-6 h-6 text-gray-600" />
                ) : (
                  <ChevronDown className="w-6 h-6 text-gray-600" />
                )}
              </button>
              
              <AnimatePresence>
                {openAccordion === "faq" && (
                  <motion.div
                    initial={{ height: 0, opacity: 0 }}
                    animate={{ height: "auto", opacity: 1 }}
                    exit={{ height: 0, opacity: 0 }}
                    transition={{ duration: 0.3 }}
                    className="overflow-hidden"
                  >
                    <div className="px-6 pb-6 space-y-4">
                      {faqs.map((faq, index) => (
                        <div key={index} className="border border-gray-200 rounded-lg overflow-hidden">
                          <button
                            onClick={() => setOpenFaqIndex(openFaqIndex === index ? null : index)}
                            className="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-gray-50 transition-colors"
                          >
                            <h4 className="font-medium text-gray-900 pr-4">{faq.question}</h4>
                            {openFaqIndex === index ? (
                              <ChevronUp className="w-5 h-5 text-gray-600 flex-shrink-0" />
                            ) : (
                              <ChevronDown className="w-5 h-5 text-gray-600 flex-shrink-0" />
                            )}
                          </button>
                          
                          <AnimatePresence>
                            {openFaqIndex === index && (
                              <motion.div
                                initial={{ height: 0, opacity: 0 }}
                                animate={{ height: "auto", opacity: 1 }}
                                exit={{ height: 0, opacity: 0 }}
                                transition={{ duration: 0.2 }}
                                className="overflow-hidden"
                              >
                                <div className="px-5 pb-4 pt-2 text-gray-700 leading-relaxed bg-gray-50">
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

            {/* Recenzie - Accordion */}
            <div className="border border-gray-200 rounded-xl overflow-hidden bg-white shadow-sm">
              <button
                onClick={() => setOpenAccordion(openAccordion === "reviews" ? null : "reviews")}
                className="w-full flex items-center justify-between px-6 py-5 text-left hover:bg-gray-50 transition-colors"
              >
                <div className="flex items-center gap-3">
                  <Star className="w-6 h-6 text-yellow-400 fill-yellow-400" />
                  <div className="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-3">
                    <h3 className="text-xl font-semibold text-gray-900">Recenzie zákazníkov</h3>
                    <span className="text-sm font-normal text-gray-600">
                      ({mockReviews.length} hodnotení • {averageRating.toFixed(1)} ⭐)
                    </span>
                  </div>
                </div>
                {openAccordion === "reviews" ? (
                  <ChevronUp className="w-6 h-6 text-gray-600 flex-shrink-0" />
                ) : (
                  <ChevronDown className="w-6 h-6 text-gray-600 flex-shrink-0" />
                )}
              </button>
              
              <AnimatePresence>
                {openAccordion === "reviews" && (
                  <motion.div
                    initial={{ height: 0, opacity: 0 }}
                    animate={{ height: "auto", opacity: 1 }}
                    exit={{ height: 0, opacity: 0 }}
                    transition={{ duration: 0.3 }}
                    className="overflow-hidden"
                  >
                    <div className="px-6 pb-6 space-y-6">
                      {/* Rating Summary */}
                      <div className="bg-gradient-to-br from-yellow-50 to-orange-50 p-6 rounded-xl border border-yellow-200">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                          {/* Average Rating */}
                          <div className="flex flex-col items-center justify-center text-center">
                            <div className="text-6xl font-bold text-gray-900 mb-2">{averageRating.toFixed(1)}</div>
                            <div className="flex items-center gap-1 mb-2">
                              {[...Array(5)].map((_, i) => (
                                <Star
                                  key={i}
                                  className={`w-6 h-6 ${
                                    i < Math.floor(averageRating)
                                      ? "fill-yellow-400 text-yellow-400"
                                      : "text-gray-300"
                                  }`}
                                />
                              ))}
                            </div>
                            <p className="text-gray-700">Na základe {mockReviews.length} recenzií</p>
                          </div>

                          {/* Rating Bars */}
                          <div className="space-y-2">
                            {[5, 4, 3, 2, 1].map((rating) => {
                              const count = mockReviews.filter(r => r.rating === rating).length;
                              const percentage = (count / mockReviews.length) * 100;
                              return (
                                <div key={rating} className="flex items-center gap-3">
                                  <div className="flex items-center gap-1 w-16">
                                    <span className="text-sm font-medium text-gray-700">{rating}</span>
                                    <Star className="w-4 h-4 fill-yellow-400 text-yellow-400" />
                                  </div>
                                  <div className="flex-1 h-3 bg-gray-200 rounded-full overflow-hidden">
                                    <div
                                      className="h-full bg-yellow-400 transition-all duration-500"
                                      style={{ width: `${percentage}%` }}
                                    />
                                  </div>
                                  <span className="text-sm text-gray-600 w-12 text-right">{count}</span>
                                </div>
                              );
                            })}
                          </div>
                        </div>
                      </div>

                      {/* Filter Buttons */}
                      <div className="flex flex-wrap items-center gap-3">
                        <button
                          onClick={() => setReviewFilter("all")}
                          className={`px-4 py-2 rounded-full font-medium transition-all ${
                            reviewFilter === "all"
                              ? "bg-[#7e9b84] text-white"
                              : "bg-gray-100 text-gray-700 hover:bg-gray-200"
                          }`}
                        >
                          Všetky ({mockReviews.length})
                        </button>
                        <button
                          onClick={() => setReviewFilter(5)}
                          className={`px-4 py-2 rounded-full font-medium transition-all ${
                            reviewFilter === 5
                              ? "bg-[#7e9b84] text-white"
                              : "bg-gray-100 text-gray-700 hover:bg-gray-200"
                          }`}
                        >
                          5 ⭐ ({mockReviews.filter(r => r.rating === 5).length})
                        </button>
                        <button
                          onClick={() => setReviewFilter(4)}
                          className={`px-4 py-2 rounded-full font-medium transition-all ${
                            reviewFilter === 4
                              ? "bg-[#7e9b84] text-white"
                              : "bg-gray-100 text-gray-700 hover:bg-gray-200"
                          }`}
                        >
                          4 ⭐ ({mockReviews.filter(r => r.rating === 4).length})
                        </button>
                        <button
                          onClick={() => setReviewFilter("photos")}
                          className={`px-4 py-2 rounded-full font-medium transition-all flex items-center gap-2 ${
                            reviewFilter === "photos"
                              ? "bg-[#7e9b84] text-white"
                              : "bg-gray-100 text-gray-700 hover:bg-gray-200"
                          }`}
                        >
                          <ImageIcon className="w-4 h-4" />
                          S fotkami ({mockReviews.filter(r => r.images && r.images.length > 0).length})
                        </button>
                      </div>

                      {/* Reviews List */}
                      <div className="space-y-6">
                        {filteredReviews.map((review) => (
                          <div key={review.id} className="border-b border-gray-200 last:border-0 pb-6 last:pb-0">
                            <div className="flex items-start justify-between mb-3">
                              <div>
                                <div className="flex items-center gap-2 mb-1">
                                  <span className="font-medium text-lg">{review.name}</span>
                                  {review.verified && (
                                    <span className="flex items-center gap-1 text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">
                                      <Award className="w-3 h-3" />
                                      Overený nákup
                                    </span>
                                  )}
                                </div>
                                <div className="flex items-center gap-1">
                                  {[...Array(5)].map((_, i) => (
                                    <Star
                                      key={i}
                                      className={`w-4 h-4 ${
                                        i < review.rating
                                          ? "fill-yellow-400 text-yellow-400"
                                          : "text-gray-300"
                                      }`}
                                    />
                                  ))}
                                </div>
                              </div>
                              <span className="text-sm text-gray-500">{review.date}</span>
                            </div>
                            <p className="text-gray-700 leading-relaxed mb-4">{review.comment}</p>
                            {review.images && review.images.length > 0 && (
                              <div className="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                {review.images.map((image, index) => (
                                  <div key={index} className="aspect-square rounded-lg overflow-hidden border border-gray-200">
                                    <img
                                      src={image}
                                      alt={`Fotografia zákazníka ${index + 1}`}
                                      className="w-full h-full object-cover hover:scale-110 transition-transform duration-300 cursor-pointer"
                                    />
                                  </div>
                                ))}
                              </div>
                            )}
                          </div>
                        ))}
                      </div>

                      {/* Write Review CTA */}
                      <div className="pt-6 border-t border-gray-200">
                        <Button
                          className="w-full sm:w-auto text-white flex items-center justify-center gap-2"
                          style={{ backgroundColor: '#7e9b84' }}
                          onMouseEnter={(e) => e.currentTarget.style.backgroundColor = '#6d8a73'}
                          onMouseLeave={(e) => e.currentTarget.style.backgroundColor = '#7e9b84'}
                        >
                          <Send className="w-5 h-5" />
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
      </section>

      {/* Related Products */}
      <section className="py-12 bg-white">
        <div className="max-w-7xl mx-auto px-4">
          <h2 className="mb-8 text-2xl md:text-3xl">Podobné produkty</h2>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {relatedProducts.map((product) => (
              <motion.div
                key={product.id}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                className="group bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-xl transition-all"
              >
                <div className="relative aspect-square bg-gray-100">
                  <img
                    src={product.image}
                    alt={product.name}
                    className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                  />
                </div>
                <div className="p-4">
                  <h3 className="mb-2 line-clamp-2 text-base">{product.name}</h3>
                  <div className="flex items-center gap-1 mb-3">
                    <Star className="w-4 h-4 fill-yellow-400 text-yellow-400" />
                    <span className="text-sm text-gray-600">{product.rating}</span>
                  </div>
                  <div className="flex items-center justify-between">
                    <span className="font-bold text-[#7e9b84] text-xl">{product.price}</span>
                    <Button
                      className="text-white"
                      style={{ backgroundColor: '#7e9b84' }}
                      onMouseEnter={(e) => e.currentTarget.style.backgroundColor = '#6d8a73'}
                      onMouseLeave={(e) => e.currentTarget.style.backgroundColor = '#7e9b84'}
                    >
                      Detail
                    </Button>
                  </div>
                </div>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* Trust Banner */}
      <TrustBanner />

      {/* Sticky Add to Cart - Mobile */}
      <div className="fixed bottom-0 left-0 right-0 bg-white border-t-2 border-gray-200 p-4 lg:hidden shadow-lg z-50">
        <div className="flex items-center gap-4">
          <div>
            <p className="text-xs text-gray-600">Cena</p>
            <p className="text-2xl font-bold text-[#7e9b84]">38,90 €</p>
          </div>
          <Button
            className="flex-1 text-white flex items-center justify-center gap-2 h-12"
            style={{ backgroundColor: '#7e9b84' }}
            onMouseEnter={(e) => e.currentTarget.style.backgroundColor = '#6d8a73'}
            onMouseLeave={(e) => e.currentTarget.style.backgroundColor = '#7e9b84'}
          >
            <ShoppingCart className="w-5 h-5" />
            <span>Pridať do košíka</span>
          </Button>
        </div>
      </div>
    </div>
  );
}
