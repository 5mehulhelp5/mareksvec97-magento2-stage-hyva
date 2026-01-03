import { useState } from "react";
import { motion } from "motion/react";
import { ChevronLeft, ChevronRight, Heart, Star } from "lucide-react";
import { TrustBanner } from "../components/TrustBanner";
import { Button } from "../components/ui/button";
import { Link } from "react-router-dom";
import { CartIcon } from "../components/icons/CartIcon";

// Import produktových obrázkov z Figma
import productImage1 from "figma:asset/04f0a15636d95bda95f42982c378c883dd93fb53.png";
import productImage2 from "figma:asset/55ae23da63723ad380aec2b614591a750318afeb.png";
import productImage3 from "figma:asset/c41554294d3abbf2ad0ce6c41bc47edd1f2694d7.png";

// Import obrázkov pre kategórie
import categoryHairpin from "figma:asset/eff5a502f8be9012ff3a694d0b59c3dbc398424a.png";
import categoryCentral from "figma:asset/47a5db653d2f6acae5585aea60f8a35ceef2750e.png";
import categoryXShape from "figma:asset/9ab8cdf79d0a48396c1521467bc8e17374a8233d.png";

interface Product {
  id: number;
  name: string;
  image: string;
  price: string;
  originalPrice?: string;
  badge?: string;
  inStock: boolean;
}

// Mock produkty - v reále by prišli z API
const allProducts: Product[] = [
  {
    id: 1,
    name: "Stolová noha Leg - Hairpin 72cm",
    image: productImage1,
    price: "24,90 €",
    originalPrice: "29,90 €",
    badge: "TOP",
    inStock: true,
  },
  {
    id: 2,
    name: "Stolová Noha - Acamar Spider 75cm",
    image: productImage3,
    price: "32,50 €",
    inStock: true,
  },
  {
    id: 3,
    name: "Stolová Noha Acamar Frame Extreme X-tvar",
    image: productImage2,
    price: "38,90 €",
    badge: "NOVINKA",
    inStock: true,
  },
  {
    id: 4,
    name: "Stolová Noha - Borax Special Extreme U-tvar",
    image: productImage1,
    price: "35,90 €",
    inStock: true,
  },
  {
    id: 5,
    name: "Trapézové nohy U-tvar 73cm - Čierna matná",
    image: productImage3,
    price: "29,90 €",
    inStock: true,
  },
  {
    id: 6,
    name: "Kovové nohy V-tvar 70cm - Antracit",
    image: productImage2,
    price: "31,50 €",
    originalPrice: "35,90 €",
    badge: "AKCIA",
    inStock: true,
  },
  {
    id: 7,
    name: "Stolové nohy Spider Extreme 75cm",
    image: productImage1,
    price: "42,90 €",
    inStock: true,
  },
  {
    id: 8,
    name: "Centrálne nohy Y-tvar 72cm - Čierna",
    image: productImage3,
    price: "36,90 €",
    inStock: true,
  },
  {
    id: 9,
    name: "Hairpin nohy 75cm - Čierna matná",
    image: productImage2,
    price: "25,50 €",
    badge: "TOP",
    inStock: true,
  },
  {
    id: 10,
    name: "Kovové nohy so stredovou tyčou 70cm",
    image: productImage1,
    price: "33,90 €",
    inStock: true,
  },
  {
    id: 11,
    name: "X-tvar nohy Extreme 73cm - Nerez",
    image: productImage3,
    price: "39,90 €",
    inStock: true,
  },
  {
    id: 12,
    name: "Trapézové nohy A-tvar 75cm",
    image: productImage2,
    price: "34,50 €",
    originalPrice: "38,90 €",
    badge: "AKCIA",
    inStock: true,
  },
];

const categories = [
  { id: "hairpin", name: "Stolové nohy Hairpin", image: categoryHairpin },
  { id: "spider", name: "Stolové nohy Spider", image: categoryXShape },
  { id: "central", name: "Centrálne stolové nohy", image: categoryCentral },
  { id: "u-shape", name: "Stolové nohy tvar U", image: categoryXShape },
  { id: "x-shape", name: "Stolové nohy tvar X", image: categoryXShape },
  { id: "v-shape", name: "Stolové nohy tvar V", image: categoryCentral },
  { id: "rod", name: "Stolové nohy so stredovou tyčou", image: categoryHairpin },
  { id: "extension", name: "Stolové nohy s výsuvom", image: categoryCentral },
  { id: "y-shape", name: "Stolové nohy tvar Y", image: categoryCentral },
  { id: "a-shape", name: "Stolové nohy tvar A", image: categoryXShape },
  { id: "extreme", name: "Stolové nohy Extreme", image: categoryXShape },
  { id: "other", name: "Ostatné", image: categoryHairpin },
];

const sortOptions = [
  { id: "newest", label: "Najnovšie" },
  { id: "bestselling", label: "Najpredávanejšie" },
  { id: "cheapest", label: "Najlacnejšie" },
  { id: "expensive", label: "Najdrahšie" },
];

export function TableLegsCategory() {
  const [currentPage, setCurrentPage] = useState(1);
  const [selectedSort, setSelectedSort] = useState("bestselling");
  const [selectedCategory, setSelectedCategory] = useState<string | null>(null);
  const [displayedProducts, setDisplayedProducts] = useState(8);
  
  const productsPerPage = 8;
  const totalPages = Math.ceil(allProducts.length / productsPerPage);
  
  const indexOfLastProduct = currentPage * productsPerPage;
  const indexOfFirstProduct = indexOfLastProduct - productsPerPage;
  const currentProducts = allProducts.slice(indexOfFirstProduct, indexOfLastProduct);
  
  const hasMoreProducts = displayedProducts < allProducts.length;

  const goToPage = (page: number) => {
    setCurrentPage(page);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  const nextPage = () => {
    if (currentPage < totalPages) {
      goToPage(currentPage + 1);
    }
  };

  const prevPage = () => {
    if (currentPage > 1) {
      goToPage(currentPage - 1);
    }
  };
  
  const loadMoreProducts = () => {
    setDisplayedProducts(prev => Math.min(prev + 8, allProducts.length));
  };

  // Generovanie čísiel stránok
  const getPageNumbers = () => {
    const pages = [];
    if (totalPages <= 5) {
      for (let i = 1; i <= totalPages; i++) {
        pages.push(i);
      }
    } else {
      if (currentPage <= 3) {
        pages.push(1, 2, 3, 4, '...', totalPages);
      } else if (currentPage >= totalPages - 2) {
        pages.push(1, '...', totalPages - 3, totalPages - 2, totalPages - 1, totalPages);
      } else {
        pages.push(1, '...', currentPage - 1, currentPage, currentPage + 1, '...', totalPages);
      }
    }
    return pages;
  };

  return (
    <div className="min-h-screen bg-white">
      {/* Breadcrumb */}
      <div className="bg-gray-50 border-b border-gray-200">
        <div className="max-w-7xl mx-auto px-4 py-3">
          <nav className="flex items-center gap-2 text-sm text-gray-600">
            {/* Desktop breadcrumb - zobrazí sa na md+ zariadeniach */}
            <div className="hidden md:flex items-center gap-2">
              <Link to="/" className="hover:text-[#7e9b84] transition-colors">Domov</Link>
              <span>/</span>
              <span className="text-gray-900 font-medium">Stolové nohy</span>
            </div>
            
            {/* Mobile breadcrumb - zobrazí sa len na mobile, len posledná kategória */}
            <div className="flex md:hidden items-center gap-2">
              <Link to="/" className="hover:text-[#7e9b84] transition-colors flex items-center gap-1">
                <ChevronLeft className="w-4 h-4" />
              </Link>
              <span className="text-gray-900 font-medium">Stolové nohy</span>
            </div>
          </nav>
        </div>
      </div>

      {/* Header Section */}
      <section className="py-12 md:py-16 bg-white">
        <div className="max-w-7xl mx-auto px-4">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
          >
            <h1 className="mb-6 text-3xl md:text-4xl lg:text-5xl">Stolové nohy</h1>
            <div className="prose max-w-none text-gray-700 leading-relaxed">
              <p className="text-base md:text-lg mb-4">
                Naše <strong>stolové podnožie</strong> od TopFero sú výsledkom ručnej práce a remeselného umenia. Naše podnožie zaručujú{" "}
                <strong>masívnu silu</strong> a <strong>eleganciu</strong>, premieňajú každý projekt na dizajnový skvost. Ponúkame{" "}
                <strong>širokú škálu tvarov</strong>, od <strong>klasických stolových nôh</strong> až po umelecké konštrukcie. Bez ohľadu na váš štýl, u nás nájdete dokonalé stolové nohy pre váš stôl.
              </p>
              <p className="text-base md:text-lg">
                Kvalita je pre nás na prvom mieste. Naše podnožie sú nielen esteticky atraktívne, ale aj{" "}
                <strong>extrémne pevné a solidné</strong>. Využívame prvotriedy materiály a dbáme na detaily. Máte otázky? Náš zákaznícky tím je tu pre vás.
              </p>
            </div>
          </motion.div>
        </div>
      </section>

      {/* Categories Grid */}
      <section className="pb-8 bg-white">
        <div className="max-w-7xl mx-auto px-4">
          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3 md:gap-4">
            {categories.map((category) => (
              <button
                key={category.id}
                onClick={() => setSelectedCategory(selectedCategory === category.id ? null : category.id)}
                className={`flex items-center gap-2 md:gap-3 p-3 md:p-4 rounded-xl border-2 transition-all hover:shadow-md ${
                  selectedCategory === category.id
                    ? "border-[#7e9b84] bg-[#7e9b84]/5 shadow-md"
                    : "border-gray-200 hover:border-[#7e9b84]/50 bg-white"
                }`}
              >
                <div className="w-10 h-10 md:w-12 md:h-12 flex-shrink-0 flex items-center justify-center bg-gray-50 rounded-lg overflow-hidden">
                  <img
                    src={category.image}
                    alt={category.name}
                    className="w-full h-full object-contain"
                  />
                </div>
                <span className="text-xs md:text-sm font-medium text-gray-900 text-left leading-tight">{category.name}</span>
              </button>
            ))}
          </div>
        </div>
      </section>

      {/* Sort Tabs */}
      <section className="pb-8 bg-white border-b border-gray-200">
        <div className="max-w-7xl mx-auto px-4">
          <div className="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-hide">
            {sortOptions.map((option) => (
              <button
                key={option.id}
                onClick={() => setSelectedSort(option.id)}
                className={`px-6 py-2.5 rounded-full whitespace-nowrap transition-all font-medium text-sm ${
                  selectedSort === option.id
                    ? "bg-[#7e9b84] text-white"
                    : "bg-gray-100 text-gray-700 hover:bg-gray-200"
                }`}
              >
                {option.label}
              </button>
            ))}
          </div>
        </div>
      </section>

      {/* Products Grid */}
      <section className="py-12 md:py-16 bg-white">
        <div className="max-w-7xl mx-auto px-4">
          <div className="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
            {currentProducts.map((product, index) => (
              <motion.div
                key={product.id}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.4, delay: index * 0.05 }}
                className="group bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300"
              >
                <Link to={`/produkt/${product.id}`}>
                  <div className="relative aspect-square bg-gray-100 overflow-hidden">
                    <img
                      src={product.image}
                      alt={product.name}
                      className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                    />
                    {product.badge && (
                      <div
                        className={`absolute top-3 left-3 px-3 py-1 rounded-full text-xs font-bold text-white ${
                          product.badge === "TOP" ? "bg-red-500" :
                          product.badge === "NOVINKA" ? "bg-blue-500" :
                          "bg-orange-500"
                        }`}
                      >
                        {product.badge}
                      </div>
                    )}
                    {product.originalPrice && (
                      <div className="absolute top-3 right-3 bg-red-500 text-white px-2 py-1 rounded-full text-xs font-bold">
                        ZĽAVA
                      </div>
                    )}
                  </div>
                </Link>

                <div className="p-4">
                  <Link to={`/produkt/${product.id}`}>
                    <h3 className="mb-3 line-clamp-2 text-sm md:text-base min-h-[2.5rem] hover:text-[#7e9b84] transition-colors">{product.name}</h3>
                  </Link>

                  <div className="flex items-center gap-2 mb-4">
                    <span className="font-bold text-[#7e9b84] text-base md:text-xl">{product.price}</span>
                    {product.originalPrice && (
                      <span className="text-xs md:text-sm text-gray-400 line-through">{product.originalPrice}</span>
                    )}
                  </div>

                  <Button
                    className="w-full text-white flex items-center justify-center gap-2"
                    style={{ backgroundColor: '#7e9b84' }}
                    onMouseEnter={(e) => e.currentTarget.style.backgroundColor = '#6d8a73'}
                    onMouseLeave={(e) => e.currentTarget.style.backgroundColor = '#7e9b84'}
                  >
                    <CartIcon className="w-4 h-4" />
                    <span>Do košíka</span>
                  </Button>
                </div>
              </motion.div>
            ))}
          </div>

          {/* Load More Button - Infinite Scroll */}
          {hasMoreProducts && (
            <div className="flex items-center justify-center mt-12 mb-8">
              <Button
                className="text-white flex items-center justify-center gap-2 px-12 py-6 text-lg font-semibold"
                style={{ backgroundColor: '#7e9b84' }}
                onMouseEnter={(e) => e.currentTarget.style.backgroundColor = '#6d8a73'}
                onMouseLeave={(e) => e.currentTarget.style.backgroundColor = '#7e9b84'}
                onClick={loadMoreProducts}
              >
                <span>Načítať viac produktov</span>
              </Button>
            </div>
          )}

          {/* Pagination */}
          <div className="flex items-center justify-center gap-2 mt-8">
            <button
              onClick={prevPage}
              disabled={currentPage === 1}
              className={`p-2 rounded-lg border transition-colors ${
                currentPage === 1
                  ? "border-gray-200 text-gray-300 cursor-not-allowed"
                  : "border-gray-300 text-gray-700 hover:bg-gray-100"
              }`}
            >
              <ChevronLeft className="w-5 h-5" />
            </button>

            <div className="flex items-center gap-2">
              {getPageNumbers().map((page, index) =>
                typeof page === "number" ? (
                  <button
                    key={index}
                    onClick={() => goToPage(page)}
                    className={`min-w-[40px] h-[40px] rounded-lg font-medium transition-all ${
                      page === currentPage
                        ? "text-white shadow-md"
                        : "bg-gray-100 text-gray-700 hover:bg-gray-200"
                    }`}
                    style={
                      page === currentPage
                        ? { backgroundColor: "#7e9b84" }
                        : undefined
                    }
                  >
                    {page}
                  </button>
                ) : (
                  <span key={index} className="px-2 text-gray-400">
                    {page}
                  </span>
                )
              )}
            </div>

            <button
              onClick={nextPage}
              disabled={currentPage === totalPages}
              className={`p-2 rounded-lg border transition-colors ${
                currentPage === totalPages
                  ? "border-gray-200 text-gray-300 cursor-not-allowed"
                  : "border-gray-300 text-gray-700 hover:bg-gray-100"
              }`}
            >
              <ChevronRight className="w-5 h-5" />
            </button>
          </div>
        </div>
      </section>

      {/* Trust Banner */}
      <TrustBanner />
    </div>
  );
}