import { useState } from "react";
import { motion, AnimatePresence } from "motion/react";
import { ArrowRight, Plus, ChevronDown, ChevronUp, X, ChevronLeft, ChevronRight } from "lucide-react";
import { Button } from "./ui/button";
import { Link } from "react-router-dom";

// Import customer photos
import customerPhoto1 from "figma:asset/cd0412c49d42047ff40e8a25b233b9237de15f83.png";
import customerPhoto2 from "figma:asset/e982e1c9cc00362c451562f6ea45d4f55bc6c901.png";
import customerPhoto3 from "figma:asset/1a8ac6948991523bf9857ed90b175162d2fb914d.png";
import customerPhoto4 from "figma:asset/b318ae15b35fee5c6a7a1be34b76219701ffd878.png";
import customerPhoto6 from "figma:asset/a375adb5f3f6c7e72f6d38d46f2ad069da946358.png";

interface InspirationPhoto {
  id: number;
  image: string;
  customerName: string;
  country: string;
  countryFlag: string;
}

const photos: InspirationPhoto[] = [
  {
    id: 1,
    image: customerPhoto1,
    customerName: "Martina K.",
    country: "Slovensko",
    countryFlag: "https://dev.topfero.sk/static/frontend/BigConnect/Hyva-Starter/sk_SK/images/flags/sk1.svg",
  },
  {
    id: 2,
    image: customerPhoto2,
    customerName: "Peter B.",
    country: "Slovensko",
    countryFlag: "https://dev.topfero.sk/static/frontend/BigConnect/Hyva-Starter/sk_SK/images/flags/sk1.svg",
  },
  {
    id: 3,
    image: customerPhoto3,
    customerName: "Jana S.",
    country: "Slovensko",
    countryFlag: "https://dev.topfero.sk/static/frontend/BigConnect/Hyva-Starter/sk_SK/images/flags/sk1.svg",
  },
  {
    id: 4,
    image: customerPhoto4,
    customerName: "Katarína V.",
    country: "Slovensko",
    countryFlag: "https://dev.topfero.sk/static/frontend/BigConnect/Hyva-Starter/sk_SK/images/flags/sk1.svg",
  },
  {
    id: 5,
    image: customerPhoto6,
    customerName: "Andrea P.",
    country: "Slovensko",
    countryFlag: "https://dev.topfero.sk/static/frontend/BigConnect/Hyva-Starter/sk_SK/images/flags/sk1.svg",
  },
  {
    id: 6,
    image: customerPhoto1,
    customerName: "Martina K.",
    country: "Slovensko",
    countryFlag: "https://dev.topfero.sk/static/frontend/BigConnect/Hyva-Starter/sk_SK/images/flags/sk1.svg",
  },
];

export function CustomerInspirationGallery() {
  const [hoveredSlide, setHoveredSlide] = useState<number | null>(null);
  const [showAll, setShowAll] = useState(false);
  const [selectedPhotoIndex, setSelectedPhotoIndex] = useState<number | null>(null);

  // Zobrazíme prvé 4 obrázky, po kliknutí na "Zobraziť viac" zobrazíme všetky
  const INITIAL_DISPLAY_COUNT = 4;
  const displayedPhotos = showAll ? photos : photos.slice(0, INITIAL_DISPLAY_COUNT);
  const hasMorePhotos = photos.length > INITIAL_DISPLAY_COUNT;

  // Lightbox functions
  const openLightbox = (index: number) => {
    setSelectedPhotoIndex(index);
    document.body.style.overflow = 'hidden'; // Prevent scrolling
  };

  const closeLightbox = () => {
    setSelectedPhotoIndex(null);
    document.body.style.overflow = 'unset';
  };

  const goToNext = () => {
    if (selectedPhotoIndex !== null) {
      setSelectedPhotoIndex((selectedPhotoIndex + 1) % displayedPhotos.length);
    }
  };

  const goToPrev = () => {
    if (selectedPhotoIndex !== null) {
      setSelectedPhotoIndex(
        (selectedPhotoIndex - 1 + displayedPhotos.length) % displayedPhotos.length
      );
    }
  };

  // Keyboard navigation
  const handleKeyDown = (e: React.KeyboardEvent) => {
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowRight') goToNext();
    if (e.key === 'ArrowLeft') goToPrev();
  };

  return (
    <section className="py-10 md:py-16 bg-gradient-to-b from-white via-[#7e9b84]/5 to-white overflow-hidden">
      <div className="max-w-7xl mx-auto px-4">
        {/* Header */}
        <div className="text-center mb-8 md:mb-12">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
          >
            <h2 className="mb-4 text-2xl md:text-3xl lg:text-4xl">
              Pozrite si tento produkt v <span className="text-[#7e9b84]">domovoch našich zákazníkov</span>
            </h2>
          </motion.div>
        </div>

        {/* Grid Gallery */}
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6, delay: 0.2 }}
          className="mb-8 md:mb-10"
        >
          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-6">
            <AnimatePresence>
              {displayedPhotos.map((photo, index) => (
                <motion.div
                  key={photo.id}
                  initial={{ opacity: 0, scale: 0.9 }}
                  animate={{ opacity: 1, scale: 1 }}
                  exit={{ opacity: 0, scale: 0.9 }}
                  transition={{ duration: 0.4, delay: index * 0.05 }}
                  className="relative aspect-[3/4] rounded-xl md:rounded-2xl overflow-hidden cursor-pointer group"
                  onMouseEnter={() => setHoveredSlide(index)}
                  onMouseLeave={() => setHoveredSlide(null)}
                  onClick={() => openLightbox(index)}
                  whileHover={{ scale: 1.02 }}
                >
                  {/* Image */}
                  <img
                    src={photo.image}
                    alt={`Inšpirácia od ${photo.customerName}`}
                    className="w-full h-full object-cover transition-transform duration-700 ease-out md:group-hover:scale-110"
                  />

                  {/* Simplified Overlay - Hyva Style */}
                  <div className="absolute inset-0 bg-black/30 md:group-hover:bg-black/20 transition-all duration-500" />

                  {/* Customer Info */}
                  <div className="absolute bottom-0 left-0 right-0 p-3 md:p-5 text-white backdrop-blur-sm bg-gradient-to-t from-black/60 to-transparent">
                    <p className="font-semibold text-sm md:text-lg mb-1 md:mb-2">{photo.customerName}</p>
                    <div className="flex items-center gap-2">
                      <img
                        src={photo.countryFlag}
                        alt={photo.country}
                        className="w-4 md:w-5 h-3 md:h-3.5 object-cover rounded-sm shadow-sm"
                      />
                      <p className="text-xs md:text-sm text-white/90">{photo.country}</p>
                    </div>
                  </div>

                  {/* Hover Effect - Plus Icon (Desktop only) */}
                  <motion.div
                    className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 hidden md:block"
                    initial={{ opacity: 0, scale: 0.5 }}
                    animate={{
                      opacity: hoveredSlide === index ? 1 : 0,
                      scale: hoveredSlide === index ? 1 : 0.5,
                    }}
                    transition={{ duration: 0.3, type: "spring", stiffness: 300 }}
                  >
                    <div className="w-20 h-20 bg-[#7e9b84]/90 backdrop-blur-sm rounded-full flex items-center justify-center border-3 border-white shadow-2xl">
                      <Plus className="w-10 h-10 text-white" strokeWidth={3} />
                    </div>
                  </motion.div>
                </motion.div>
              ))}
            </AnimatePresence>
          </div>
        </motion.div>

        {/* Show More/Less Button */}
        {hasMorePhotos && (
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6, delay: 0.4 }}
            className="flex items-center justify-center"
          >
            <Button
              onClick={() => setShowAll(!showAll)}
              variant="outline"
              className="border-2 border-[#7e9b84] text-[#7e9b84] hover:bg-[#7e9b84]/5 px-6 md:px-8 py-5 md:py-6 text-sm md:text-base font-semibold flex items-center gap-2 group"
            >
              {showAll ? (
                <>
                  Zobraziť menej
                  <ChevronUp className="w-5 h-5 group-hover:-translate-y-1 transition-transform" />
                </>
              ) : (
                <>
                  Zobraziť viac fotiek ({photos.length - INITIAL_DISPLAY_COUNT})
                  <ChevronDown className="w-5 h-5 group-hover:translate-y-1 transition-transform" />
                </>
              )}
            </Button>
          </motion.div>
        )}
      </div>

      {/* Lightbox */}
      <AnimatePresence>
        {selectedPhotoIndex !== null && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            transition={{ duration: 0.3 }}
            className="fixed inset-0 bg-black/95 backdrop-blur-sm flex items-center justify-center z-50 p-4"
            onClick={closeLightbox}
            onKeyDown={handleKeyDown}
            tabIndex={0}
          >
            {/* Close Button */}
            <motion.button
              initial={{ opacity: 0, scale: 0.8 }}
              animate={{ opacity: 1, scale: 1 }}
              transition={{ delay: 0.2 }}
              className="absolute top-4 md:top-6 right-4 md:right-6 w-12 h-12 md:w-12 md:h-12 bg-white/90 hover:bg-white backdrop-blur-sm rounded-full flex items-center justify-center shadow-2xl z-10 transition-colors"
              onClick={closeLightbox}
              aria-label="Zavrieť"
            >
              <X className="w-6 h-6 text-gray-800" strokeWidth={2.5} />
            </motion.button>

            {/* Previous Button */}
            <motion.button
              initial={{ opacity: 0, x: -20 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ delay: 0.2 }}
              className="absolute left-3 md:left-6 top-1/2 -translate-y-1/2 w-14 h-14 md:w-12 md:h-12 bg-white/90 hover:bg-white backdrop-blur-sm rounded-full flex items-center justify-center shadow-2xl transition-colors border-2 border-[#7e9b84]"
              onClick={(e) => {
                e.stopPropagation();
                goToPrev();
              }}
              aria-label="Predchádzajúci"
            >
              <ChevronLeft className="w-6 h-6 text-[#7e9b84]" strokeWidth={2.5} />
            </motion.button>

            {/* Next Button */}
            <motion.button
              initial={{ opacity: 0, x: 20 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ delay: 0.2 }}
              className="absolute right-3 md:right-6 top-1/2 -translate-y-1/2 w-14 h-14 md:w-12 md:h-12 bg-white/90 hover:bg-white backdrop-blur-sm rounded-full flex items-center justify-center shadow-2xl transition-colors border-2 border-[#7e9b84]"
              onClick={(e) => {
                e.stopPropagation();
                goToNext();
              }}
              aria-label="Ďalší"
            >
              <ChevronRight className="w-6 h-6 text-[#7e9b84]" strokeWidth={2.5} />
            </motion.button>

            {/* Image Container */}
            <motion.div
              initial={{ scale: 0.9, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.9, opacity: 0 }}
              transition={{ duration: 0.3 }}
              className="relative max-w-6xl max-h-[90vh] w-full"
              onClick={(e) => e.stopPropagation()}
            >
              <img
                src={displayedPhotos[selectedPhotoIndex].image}
                alt={`Inšpirácia od ${displayedPhotos[selectedPhotoIndex].customerName}`}
                className="w-full h-full object-contain rounded-xl md:rounded-2xl shadow-2xl"
              />

              {/* Image Info Overlay */}
              <motion.div
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ delay: 0.3 }}
                className="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 via-black/50 to-transparent p-4 md:p-6 rounded-b-xl md:rounded-b-2xl"
              >
                <div className="flex items-center justify-between text-white">
                  <div>
                    <p className="font-semibold text-lg md:text-xl mb-1 md:mb-2">
                      {displayedPhotos[selectedPhotoIndex].customerName}
                    </p>
                    <div className="flex items-center gap-2">
                      <img
                        src={displayedPhotos[selectedPhotoIndex].countryFlag}
                        alt={displayedPhotos[selectedPhotoIndex].country}
                        className="w-5 md:w-6 h-3.5 md:h-4 object-cover rounded-sm shadow-sm"
                      />
                      <p className="text-sm md:text-base text-white/90">
                        {displayedPhotos[selectedPhotoIndex].country}
                      </p>
                    </div>
                  </div>
                  <div className="text-white/70 text-xs md:text-sm">
                    {selectedPhotoIndex + 1} / {displayedPhotos.length}
                  </div>
                </div>
              </motion.div>
            </motion.div>

            {/* Keyboard Hint */}
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              transition={{ delay: 0.5 }}
              className="absolute bottom-4 md:bottom-6 left-1/2 -translate-x-1/2 text-white/60 text-xs md:text-sm flex items-center gap-4"
            >
              <span className="hidden md:block">← → Navigácia</span>
              <span className="hidden md:block">•</span>
              <span>ESC Zavrieť</span>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>
    </section>
  );
}