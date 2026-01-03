import { useState } from "react";
import Masonry, { ResponsiveMasonry } from "react-responsive-masonry";
import { motion, AnimatePresence } from "motion/react";
import { MapPin, ShoppingBag, X, Star, Quote, ChevronLeft, ChevronRight, ArrowRight, Upload, Camera } from "lucide-react";

// Import reálnych fotiek z Figma
import customerPhoto1 from "figma:asset/cd0412c49d42047ff40e8a25b233b9237de15f83.png";
import customerPhoto2 from "figma:asset/e982e1c9cc00362c451562f6ea45d4f55bc6c901.png";
import customerPhoto3 from "figma:asset/1a8ac6948991523bf9857ed90b175162d2fb914d.png";
import customerPhoto4 from "figma:asset/b318ae15b35fee5c6a7a1be34b76219701ffd878.png";
import customerPhoto6 from "figma:asset/a375adb5f3f6c7e72f6d38d46f2ad069da946358.png";

interface GalleryItem {
  id: number;
  image: string;
  category: "table-legs" | "house-numbers";
  customerName: string;
  location: string;
  country: string;
  countryFlag: string;
  productName: string;
  productImage: string;
  productPrice: string;
  productUrl: string;
  rating: number;
  review: string;
  date: string;
}

const galleryItems: GalleryItem[] = [
  {
    id: 1,
    image: customerPhoto1,
    category: "table-legs",
    customerName: "Martina K.",
    location: "Bratislava",
    country: "Slovensko",
    countryFlag: "https://dev.topfero.sk/static/frontend/TopFero/hyva/sk_SK/images/flags/sk1.svg",
    productName: "Stolové nohy U-tvar 70cm s epoxy živicou",
    productImage: customerPhoto1,
    productPrice: "35,90 €",
    productUrl: "#",
    rating: 5,
    review: "Úžasný stôl s modrou epoxy živicou! Nohy drží perfektne, stabilné a elegantné. Som nadšená výsledkom!",
    date: "15.12.2024",
  },
  {
    id: 2,
    image: customerPhoto2,
    category: "table-legs",
    customerName: "Peter B.",
    location: "Košice",
    country: "Slovensko",
    countryFlag: "https://dev.topfero.sk/static/frontend/TopFero/hyva/sk_SK/images/flags/sk1.svg",
    productName: "Kovové nohy U-tvar 75cm - Čierna matná",
    productImage: customerPhoto2,
    productPrice: "28,90 €",
    productUrl: "#",
    rating: 5,
    review: "Výborná kvalita, jednoduché montovanie. Stôl je stabilný a vyzerá veľmi moderne. Určite odporúčam!",
    date: "08.12.2024",
  },
  {
    id: 3,
    image: customerPhoto3,
    category: "table-legs",
    customerName: "Jana S.",
    location: "Nitra",
    country: "Slovensko",
    countryFlag: "https://dev.topfero.sk/static/frontend/TopFero/hyva/sk_SK/images/flags/sk1.svg",
    productName: "Pavúkové nohy Spider 72cm - Čierna",
    productImage: customerPhoto3,
    productPrice: "32,50 €",
    productUrl: "#",
    rating: 5,
    review: "Krásny industriálny dizajn! Pavúkové nohy vyzerajú úžasne a stôl je veľmi pevný. Presne ako na obrázku.",
    date: "22.11.2024",
  },
  {
    id: 4,
    image: customerPhoto4,
    category: "table-legs",
    customerName: "Katarína V.",
    location: "Trenčín",
    country: "Slovensko",
    countryFlag: "https://dev.topfero.sk/static/frontend/TopFero/hyva/sk_SK/images/flags/sk1.svg",
    productName: "Trapézové nohy U-tvar 73cm - Čierna matná",
    productImage: customerPhoto4,
    productPrice: "29,90 €",
    productUrl: "#",
    rating: 5,
    review: "Perfektný stolový set! Nohy sú pevné, moderné a ladíme dokonale s drevom. Celá rodina je nadšená!",
    date: "18.12.2024",
  },
  {
    id: 6,
    image: customerPhoto6,
    category: "table-legs",
    customerName: "Andrea P.",
    location: "Poprad",
    country: "Slovensko",
    countryFlag: "https://dev.topfero.sk/static/frontend/TopFero/hyva/sk_SK/images/flags/sk1.svg",
    productName: "Trapézové nohy 72cm - Antracit",
    productImage: customerPhoto6,
    productPrice: "31,50 €",
    productUrl: "#",
    rating: 5,
    review: "Krásna kombinácia s dubovým drevom! Nohy sú stabilné a elegantné. Jedáleň vyzerá úžasne modernou!",
    date: "12.12.2024",
  },
  {
    id: 7,
    image: "https://images.unsplash.com/photo-1557603150-b612a4c68e6a?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxob3VzZSUyMG51bWJlcnMlMjBtb2Rlcm58ZW58MXx8fHwxNzY3MzU0NjE0fDA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
    category: "house-numbers",
    customerName: "Michal T.",
    location: "Žilina",
    country: "Slovensko",
    countryFlag: "https://dev.topfero.sk/static/frontend/TopFero/hyva/sk_SK/images/flags/sk1.svg",
    productName: "Moderné číslo domu - Čierna matná",
    productImage: "https://images.unsplash.com/photo-1557603150-b612a4c68e6a?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=400",
    productPrice: "24,90 €",
    productUrl: "#",
    rating: 5,
    review: "Moderný minimalistický dizajn, čísla vyzerajú presne ako na webe. Rýchle dodanie a perfektné balenie.",
    date: "30.11.2024",
  },
  {
    id: 8,
    image: "https://images.unsplash.com/photo-1643877094694-b6573e2ea6d6?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800",
    category: "house-numbers",
    customerName: "Lucia M.",
    location: "Prešov",
    country: "Slovensko",
    countryFlag: "https://dev.topfero.sk/static/frontend/TopFero/hyva/sk_SK/images/flags/sk1.svg",
    productName: "Minimalistické číslo - Nerez",
    productImage: "https://images.unsplash.com/photo-1643877094694-b6573e2ea6d6?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=400",
    productPrice: "22,90 €",
    productUrl: "#",
    rating: 5,
    review: "Minimalistický dizajn, nerez vyzerá skvelo pri našom modernom dome. Veľmi spokojná!",
    date: "15.11.2024",
  },
  {
    id: 9,
    image: "https://images.unsplash.com/photo-1759643161985-5fcb6dfc9a13?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800",
    category: "table-legs",
    customerName: "Tomáš V.",
    location: "Trnava",
    country: "Slovensko",
    countryFlag: "https://dev.topfero.sk/static/frontend/TopFero/hyva/sk_SK/images/flags/sk1.svg",
    productName: "Hairpin nohy 75cm - Zelená šalviová",
    productImage: "https://images.unsplash.com/photo-1759643161985-5fcb6dfc9a13?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=400",
    productPrice: "25,50 €",
    productUrl: "#",
    rating: 5,
    review: "Úžasná šalviová zelená, sedí perfektne do našej obývačky. Retro štýl v modernom prevedení!",
    date: "05.12.2024",
  },
  {
    id: 10,
    image: "https://images.unsplash.com/photo-1762295539419-b7fae80cffc4?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxob3VzZSUyMG51bWJlciUyMHBsYXF1ZXxlbnwxfHx8fDE3NjczNTQ2MTR8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
    category: "house-numbers",
    customerName: "Zuzana P.",
    location: "Banská Bystrica",
    country: "Slovensko",
    countryFlag: "https://dev.topfero.sk/static/frontend/TopFero/hyva/sk_SK/images/flags/sk1.svg",
    productName: "LED číslo s podsvietením - Biele",
    productImage: "https://images.unsplash.com/photo-1762295539419-b7fae80cffc4?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=400",
    productPrice: "32,00 €",
    productUrl: "#",
    rating: 5,
    review: "Večer to vyzerá fantasticky! LED podsvietenie je jemné a elegantné. Návštevy sa vždy pýtajú, kde sme to kúpili.",
    date: "20.12.2024",
  },
  {
    id: 11,
    image: "https://images.unsplash.com/photo-1661537377602-405eb50cef79?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtb2Rlcm4lMjBhZGRyZXNzJTIwc2lnbnxlbnwxfHx8fDE3NjczNDM2NDJ8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
    category: "house-numbers",
    customerName: "Daniel K.",
    location: "Martin",
    country: "Slovensko",
    countryFlag: "https://dev.topfero.sk/static/frontend/TopFero/hyva/sk_SK/images/flags/sk1.svg",
    productName: "Gravírovaná tabuľka s číslom domu",
    productImage: "https://images.unsplash.com/photo-1661537377602-405eb50cef79?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=400",
    productPrice: "28,50 €",
    productUrl: "#",
    rating: 5,
    review: "Precízna gravírovaná tabuľka, vyzerá veľmi luxusne. Montáž bola jednoduchá, určite odporúčam!",
    date: "10.12.2024",
  },
];

export function CustomerGalleryPage() {
  const [lightboxItem, setLightboxItem] = useState<GalleryItem | null>(null);
  const [lightboxIndex, setLightboxIndex] = useState<number>(0);
  const [showUploadModal, setShowUploadModal] = useState(false);
  const [uploadedImage, setUploadedImage] = useState<string | null>(null);

  const stats = {
    totalProjects: 150,
    happyCustomers: 140,
    yearsExperience: 8,
  };

  const openLightbox = (item: GalleryItem) => {
    const index = galleryItems.findIndex((i) => i.id === item.id);
    setLightboxIndex(index);
    setLightboxItem(item);
  };

  const closeLightbox = () => {
    setLightboxItem(null);
  };

  const goToPrevious = () => {
    const newIndex = lightboxIndex > 0 ? lightboxIndex - 1 : galleryItems.length - 1;
    setLightboxIndex(newIndex);
    setLightboxItem(galleryItems[newIndex]);
  };

  const goToNext = () => {
    const newIndex = lightboxIndex < galleryItems.length - 1 ? lightboxIndex + 1 : 0;
    setLightboxIndex(newIndex);
    setLightboxItem(galleryItems[newIndex]);
  };

  const handleImageUpload = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      const reader = new FileReader();
      reader.onloadend = () => {
        setUploadedImage(reader.result as string);
      };
      reader.readAsDataURL(file);
    }
  };

  const handleSubmitPhoto = (e: React.FormEvent) => {
    e.preventDefault();
    // Tu bude logika na odoslanie formulára
    alert("Ďakujeme! Vaša fotografia bola odoslaná.");
    setShowUploadModal(false);
    setUploadedImage(null);
  };

  return (
    <div className="min-h-screen bg-white">
      {/* Hero Header */}
      <section className="bg-[#7e9b84] text-white py-12 md:py-16 lg:py-24">
        <div className="container mx-auto px-4 max-w-6xl">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            className="text-center"
          >
            <h1 className="mb-3 md:mb-4 text-3xl md:text-4xl lg:text-5xl leading-tight">Galéria Inšpirácií</h1>
            <p className="text-base md:text-xl lg:text-2xl max-w-3xl mx-auto opacity-90 leading-relaxed px-2">
              Pozrite si realizácie našich spokojných zákazníkov. Nechajte sa inšpirovať skutočnými fotografiami produktov v reálnom prostredí.
            </p>
          </motion.div>

          {/* Stats */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8 mt-10 md:mt-12 max-w-3xl mx-auto">
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6 }}
              className="text-center py-4 md:py-0"
            >
              <div className="text-4xl md:text-4xl lg:text-5xl mb-2 font-bold">{stats.totalProjects}+</div>
              <div className="text-sm md:text-base opacity-90">Projektov</div>
            </motion.div>
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6, delay: 0.2 }}
              className="text-center py-4 md:py-0 border-t border-b md:border-t-0 md:border-b-0 border-white/20"
            >
              <div className="text-4xl md:text-4xl lg:text-5xl mb-2 font-bold">{stats.happyCustomers}+</div>
              <div className="text-sm md:text-base opacity-90">Spokojných zákazníkov</div>
            </motion.div>
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6, delay: 0.4 }}
              className="text-center py-4 md:py-0"
            >
              <div className="text-4xl md:text-4xl lg:text-5xl mb-2 font-bold">{stats.yearsExperience}</div>
              <div className="text-sm md:text-base opacity-90">Rokov skúseností</div>
            </motion.div>
          </div>
        </div>
      </section>

      {/* Gallery */}
      <section className="py-16">
        <div className="container mx-auto px-4 max-w-7xl">
          <ResponsiveMasonry columnsCountBreakPoints={{ 350: 1, 750: 2, 1024: 3 }}>
            <Masonry gutter="20px">
              {galleryItems.map((item, index) => (
                <motion.div
                  key={item.id}
                  initial={{ opacity: 0, scale: 0.9 }}
                  animate={{ opacity: 1, scale: 1 }}
                  transition={{ duration: 0.5, delay: index * 0.1 }}
                  className="bg-white rounded-xl shadow-lg overflow-hidden cursor-pointer hover:shadow-2xl transition-shadow duration-300"
                  onClick={() => openLightbox(item)}
                >
                  <div className="relative group overflow-hidden">
                    <img
                      src={item.image}
                      alt={item.productName}
                      className="w-full h-auto block transition-transform duration-300 group-hover:scale-105"
                    />
                    <div className="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-full flex items-center gap-2 shadow-lg">
                      <img src={item.countryFlag} alt={item.country} className="w-5 h-5 rounded-full object-cover" />
                      <span className="text-xs font-semibold text-gray-700">{item.country}</span>
                    </div>
                  </div>

                  <div className="p-5">
                    <div className="flex items-center gap-1 mb-2">
                      {[...Array(5)].map((_, i) => (
                        <Star
                          key={i}
                          className={`w-4 h-4 ${
                            i < item.rating ? "fill-yellow-400 text-yellow-400" : "fill-gray-200 text-gray-200"
                          }`}
                        />
                      ))}
                      <span className="text-sm text-gray-500 ml-2">{item.date}</span>
                    </div>

                    <h3 className="mb-2 line-clamp-2">{item.productName}</h3>

                    <div className="flex items-center gap-2 text-sm text-gray-600 mb-3">
                      <span>{item.customerName}</span>
                    </div>

                    <div className="relative">
                      <Quote className="absolute -left-1 -top-1 w-6 h-6 text-[#7e9b84] opacity-20" />
                      <p className="text-gray-600 text-sm italic line-clamp-3 pl-5">
                        "{item.review}"
                      </p>
                    </div>
                  </div>
                </motion.div>
              ))}
            </Masonry>
          </ResponsiveMasonry>
        </div>
      </section>

      {/* Lightbox Modal */}
      <AnimatePresence>
        {lightboxItem && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 bg-black/95 z-50 flex items-center justify-center p-4"
            onClick={closeLightbox}
          >
            <button
              onClick={closeLightbox}
              className="absolute top-4 right-4 bg-white/10 hover:bg-white/20 text-white p-3 rounded-full transition-colors z-10"
            >
              <X className="w-6 h-6" />
            </button>

            {/* Previous Button */}
            <button
              onClick={(e) => {
                e.stopPropagation();
                goToPrevious();
              }}
              className="absolute left-4 bg-white/10 hover:bg-white/20 text-white p-3 rounded-full transition-colors z-10"
            >
              <ChevronLeft className="w-6 h-6" />
            </button>

            {/* Next Button */}
            <button
              onClick={(e) => {
                e.stopPropagation();
                goToNext();
              }}
              className="absolute right-4 bg-white/10 hover:bg-white/20 text-white p-3 rounded-full transition-colors z-10"
            >
              <ChevronRight className="w-6 h-6" />
            </button>

            {/* Content - 2 column layout */}
            <motion.div
              initial={{ scale: 0.9, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.9, opacity: 0 }}
              className="max-w-5xl w-full flex flex-col sm:grid sm:grid-cols-2 gap-0 sm:gap-4 md:gap-6 bg-white rounded-xl md:rounded-2xl overflow-hidden max-h-[95vh] sm:max-h-[92vh] md:max-h-[90vh]"
              onClick={(e) => e.stopPropagation()}
            >
              {/* Left: Customer Photo */}
              <div className="relative bg-gray-100 h-[50vh] sm:h-auto">
                <img
                  src={lightboxItem.image}
                  alt={lightboxItem.productName}
                  className="w-full h-full object-cover"
                />
                <div className="absolute top-3 sm:top-3.5 md:top-4 left-3 sm:left-3.5 md:left-4 bg-white px-2 py-1 sm:px-2.5 sm:py-1.5 md:px-3 md:py-1.5 rounded-full flex items-center gap-1.5 md:gap-2 shadow-lg">
                  <img src={lightboxItem.countryFlag} alt={lightboxItem.country} className="w-4 h-4 md:w-5 md:h-5 rounded-full object-cover" />
                  <span className="text-xs font-semibold text-gray-700">{lightboxItem.country}</span>
                </div>
              </div>

              {/* Right: Review & Product */}
              <div className="p-4 sm:p-5 md:p-8 flex flex-col justify-between overflow-y-auto">
                <div>
                  <div className="flex items-center gap-1 mb-3 md:mb-4">
                    {[...Array(5)].map((_, i) => (
                      <Star
                        key={i}
                        className={`w-4 h-4 sm:w-4.5 sm:h-4.5 md:w-5 md:h-5 ${
                          i < lightboxItem.rating ? "fill-yellow-400 text-yellow-400" : "fill-yellow-200 text-gray-200"
                        }`}
                      />
                    ))}
                    <span className="text-xs md:text-sm text-gray-500 ml-2">{lightboxItem.rating}.0</span>
                  </div>

                  <div className="flex items-center gap-2 mb-4 md:mb-6 text-sm md:text-base text-gray-600">
                    <span className="text-xs sm:text-sm md:text-base">{lightboxItem.customerName}</span>
                  </div>

                  <div className="bg-gray-50 rounded-lg md:rounded-xl p-4 sm:p-5 md:p-6 mb-4 md:mb-6">
                    <div className="flex items-start gap-2 md:gap-3">
                      <Quote className="w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8 text-[#7e9b84] flex-shrink-0" />
                      <div>
                        <p className="text-gray-700 italic text-sm sm:text-base md:text-lg mb-2 md:mb-3">"{lightboxItem.review}"</p>
                        <p className="text-xs md:text-sm text-gray-500">{lightboxItem.date}</p>
                      </div>
                    </div>
                  </div>
                </div>

                {/* Product Card in one row */}
                <div className="bg-white border-2 border-[#7e9b84]/20 rounded-lg md:rounded-xl p-3 md:p-4 flex items-center gap-3 md:gap-4">
                  <img
                    src={lightboxItem.productImage}
                    alt={lightboxItem.productName}
                    className="w-16 h-16 sm:w-18 sm:h-18 md:w-20 md:h-20 object-cover rounded-lg flex-shrink-0"
                  />
                  <div className="flex-1 min-w-0">
                    <h4 className="text-xs sm:text-sm md:text-sm mb-1 line-clamp-2">{lightboxItem.productName}</h4>
                    <p className="text-lg sm:text-xl md:text-xl text-[#7e9b84]">{lightboxItem.productPrice}</p>
                  </div>
                  <a
                    href={lightboxItem.productUrl}
                    className="bg-[#7e9b84] hover:bg-[#6d8a73] text-white px-3 sm:px-4 md:px-6 py-2 sm:py-2.5 md:py-3 rounded-full transition-colors flex items-center gap-1.5 md:gap-2 flex-shrink-0 text-xs sm:text-sm md:text-base"
                  >
                    <span className="hidden sm:inline">Prejsť na produkt</span>
                    <span className="sm:hidden">Prejsť</span>
                    <ArrowRight className="w-4 h-4" />
                  </a>
                </div>
              </div>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>

      {/* CTA Section */}
      <section className="py-16 bg-gray-50">
        <div className="container mx-auto px-4 max-w-4xl text-center">
          <h2 className="mb-4">Pridajte sa k našim spokojným zákazníkom</h2>
          <p className="text-lg text-gray-600 mb-8">
            Pošlite nám fotografiu vášho produktu a získajte <strong className="text-[#7e9b84]">10% zľavu</strong> na ďalší nákup!
          </p>
          <button 
            onClick={() => setShowUploadModal(true)}
            className="bg-[#7e9b84] hover:bg-[#6d8a73] text-white px-8 py-4 rounded-full transition-colors flex items-center gap-2 mx-auto"
          >
            <Camera className="w-5 h-5" />
            <span>Odoslať fotografiu</span>
          </button>
        </div>
      </section>

      {/* Upload Modal */}
      <AnimatePresence>
        {showUploadModal && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4"
            onClick={() => setShowUploadModal(false)}
          >
            <motion.div
              initial={{ scale: 0.9, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.9, opacity: 0 }}
              className="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
              onClick={(e) => e.stopPropagation()}
            >
              <div className="sticky top-0 bg-white border-b border-gray-200 p-6 flex items-center justify-between">
                <h2 className="text-2xl">Odoslať fotografiu produktu</h2>
                <button
                  onClick={() => setShowUploadModal(false)}
                  className="p-2 hover:bg-gray-100 rounded-full transition-colors"
                >
                  <X className="w-6 h-6" />
                </button>
              </div>

              <form onSubmit={handleSubmitPhoto} className="p-6 space-y-6">
                {/* Upload Area */}
                <div>
                  <label className="block mb-2 font-medium">Fotografia *</label>
                  <div className="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-[#7e9b84] transition-colors">
                    {uploadedImage ? (
                      <div className="relative">
                        <img src={uploadedImage} alt="Preview" className="max-h-64 mx-auto rounded-lg" />
                        <button
                          type="button"
                          onClick={() => setUploadedImage(null)}
                          className="absolute top-2 right-2 bg-red-500 text-white p-2 rounded-full hover:bg-red-600"
                        >
                          <X className="w-4 h-4" />
                        </button>
                      </div>
                    ) : (
                      <div>
                        <Upload className="w-12 h-12 mx-auto mb-4 text-gray-400" />
                        <p className="text-gray-600 mb-2">Kliknite pre výber fotografie alebo pretiahnite sem</p>
                        <p className="text-sm text-gray-500">PNG, JPG max. 10MB</p>
                        <input
                          type="file"
                          accept="image/*"
                          onChange={handleImageUpload}
                          className="hidden"
                          id="photo-upload"
                          required
                        />
                        <label
                          htmlFor="photo-upload"
                          className="inline-block mt-4 bg-[#7e9b84] hover:bg-[#6d8a73] text-white px-6 py-3 rounded-full cursor-pointer transition-colors"
                        >
                          Vybrať fotografiu
                        </label>
                      </div>
                    )}
                  </div>
                </div>

                {/* Name */}
                <div>
                  <label htmlFor="name" className="block mb-2 font-medium">Vaše meno *</label>
                  <input
                    type="text"
                    id="name"
                    required
                    className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#7e9b84] focus:border-transparent"
                    placeholder="Napr. Ján Novák"
                  />
                </div>

                {/* Email */}
                <div>
                  <label htmlFor="email" className="block mb-2 font-medium">Email *</label>
                  <input
                    type="email"
                    id="email"
                    required
                    className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#7e9b84] focus:border-transparent"
                    placeholder="vas@email.sk"
                  />
                </div>

                {/* City */}
                <div>
                  <label htmlFor="country" className="block mb-2 font-medium">Krajina *</label>
                  <input
                    type="text"
                    id="country"
                    required
                    className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#7e9b84] focus:border-transparent"
                    placeholder="Napr. Slovensko"
                  />
                </div>

                {/* Review */}
                <div>
                  <label htmlFor="review" className="block mb-2 font-medium">Vaša recenzia (voliteľné)</label>
                  <textarea
                    id="review"
                    rows={4}
                    className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#7e9b84] focus:border-transparent resize-none"
                    placeholder="Podeľte sa o vašu skúsenosť s produktom..."
                  ></textarea>
                </div>

                {/* Submit */}
                <div className="flex gap-4">
                  <button
                    type="submit"
                    className="flex-1 bg-[#7e9b84] hover:bg-[#6d8a73] text-white px-6 py-4 rounded-full transition-colors flex items-center justify-center gap-2"
                  >
                    <Upload className="w-5 h-5" />
                    <span>Odoslať a získať 10% zľavu</span>
                  </button>
                  <button
                    type="button"
                    onClick={() => setShowUploadModal(false)}
                    className="px-6 py-4 border-2 border-gray-300 rounded-full hover:bg-gray-50 transition-colors"
                  >
                    Zrušiť
                  </button>
                </div>

                <p className="text-xs text-gray-500 text-center">
                  Odoslaním fotografie súhlasíte s jej použitím v našej galérii a marketingových materiáloch.
                </p>
              </form>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
}