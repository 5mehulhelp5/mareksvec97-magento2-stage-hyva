import { useState } from "react";
import { Link } from "react-router-dom";
import { Trash2, ShoppingCart, Shield, MapPin, Star, Award, Headphones, ChevronDown, ChevronUp, Plus, Minus, ArrowRight, Tag } from "lucide-react";
import { motion } from "motion/react";
import { Button } from "../components/ui/button";

interface CartItem {
  id: string;
  name: string;
  image: string;
  price: number;
  quantity: number;
  size?: string;
}

export function CartPage() {
  const [cartItems, setCartItems] = useState<CartItem[]>([
    {
      id: "1",
      name: "Stolové Nohy - Acornar",
      image: "https://images.unsplash.com/photo-1595515106969-1ce29566ff1c?w=400&h=400&fit=crop",
      price: 299.00,
      quantity: 1,
      size: "72x70x70 cm"
    }
  ]);

  const [couponExpanded, setCouponExpanded] = useState(false);
  const [couponCode, setCouponCode] = useState("");

  const updateQuantity = (id: string, newQuantity: number) => {
    if (newQuantity < 1) return;
    setCartItems(items =>
      items.map(item =>
        item.id === id ? { ...item, quantity: newQuantity } : item
      )
    );
  };

  const removeItem = (id: string) => {
    setCartItems(items => items.filter(item => item.id !== id));
  };

  const subtotal = cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
  const tax = subtotal * 0.20; // 20% DPH
  const total = subtotal + tax;

  const testimonials = [
    {
      name: "Martin K.",
      date: "15.12.2024",
      rating: 5,
      text: "Stolové nohy prišli v perfektnom stave. Kvalita excelenté, montáž jednoduchá. Odporúčam!"
    },
    {
      name: "Jana P.",
      date: "8.1.2025",
      rating: 5,
      text: "Som veľmi spokojná s kvalitou a spracovaním. Dodanie bolo rýchle, produkty presne ako na obrázku."
    },
    {
      name: "Tomáš V.",
      date: "22.12.2024",
      rating: 5,
      text: "Vynikajúca zákaznícka podpora. Pomohli mi vybrať správny produkt. Všetko prišlo včas a dobre zabalené."
    }
  ];

  // Empty cart state
  if (cartItems.length === 0) {
    return (
      <div className="bg-gray-50 py-20">
        <div className="max-w-7xl mx-auto px-4">
          <div className="bg-white rounded-2xl shadow-lg p-12 md:p-16 text-center">
            <div className="w-24 h-24 mx-auto mb-6 rounded-full bg-gray-100 flex items-center justify-center">
              <ShoppingCart className="w-12 h-12 text-gray-400" />
            </div>
            <h1 className="text-3xl md:text-4xl mb-4">Váš košík je prázdny</h1>
            <p className="text-lg mb-8" style={{ color: '#666' }}>
              Pridajte si produkty do košíka a pokračujte v nákupe.
            </p>
            <Link to="/">
              <Button 
                size="lg"
                className="bg-[#7e9b84] hover:bg-[#6a8470] text-white font-bold px-8 py-6"
              >
                Pokračovať v nákupe
                <ArrowRight className="w-5 h-5 ml-2" />
              </Button>
            </Link>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="bg-gray-50">
      {/* Page Header */}
      <div className="bg-white border-b border-gray-200 py-8">
        <div className="max-w-7xl mx-auto px-4">
          <h1 className="text-3xl md:text-4xl font-bold">Nákupný košík</h1>
          <p className="mt-2" style={{ color: '#666' }}>
            {cartItems.length} {cartItems.length === 1 ? 'produkt' : 'produkty'} v košíku
          </p>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 py-12">
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Left: Cart Items */}
          <div className="lg:col-span-2 space-y-4">
            {cartItems.map((item) => (
              <motion.div
                key={item.id}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                className="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all"
              >
                <div className="flex flex-col sm:flex-row gap-6">
                  {/* Product Image */}
                  <div className="w-full sm:w-32 h-32 flex-shrink-0 bg-gray-50 rounded-xl overflow-hidden">
                    <img 
                      src={item.image} 
                      alt={item.name}
                      className="w-full h-full object-cover"
                    />
                  </div>

                  {/* Product Details */}
                  <div className="flex-1 min-w-0">
                    <div className="flex justify-between items-start gap-4 mb-3">
                      <div>
                        <h3 className="font-bold text-lg mb-1">{item.name}</h3>
                        {item.size && (
                          <p className="text-sm" style={{ color: '#666' }}>
                            Rozmer: {item.size}
                          </p>
                        )}
                      </div>
                      <button
                        onClick={() => removeItem(item.id)}
                        className="p-2 hover:bg-red-50 rounded-lg transition-colors flex-shrink-0"
                        title="Odstrániť z košíka"
                      >
                        <Trash2 className="w-5 h-5 text-red-600" />
                      </button>
                    </div>

                    {/* Price & Quantity */}
                    <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mt-4">
                      {/* Quantity Selector */}
                      <div className="flex items-center gap-3">
                        <span className="text-sm" style={{ color: '#666' }}>Množstvo:</span>
                        <div className="flex items-center border-2 border-gray-200 rounded-lg">
                          <button
                            onClick={() => updateQuantity(item.id, item.quantity - 1)}
                            className="p-2 hover:bg-gray-50 transition-colors rounded-l-lg"
                          >
                            <Minus className="w-4 h-4" />
                          </button>
                          <input
                            type="number"
                            value={item.quantity}
                            onChange={(e) => updateQuantity(item.id, parseInt(e.target.value) || 1)}
                            className="w-16 text-center border-x-2 border-gray-200 py-2 focus:outline-none font-bold"
                            min="1"
                          />
                          <button
                            onClick={() => updateQuantity(item.id, item.quantity + 1)}
                            className="p-2 hover:bg-gray-50 transition-colors rounded-r-lg"
                          >
                            <Plus className="w-4 h-4" />
                          </button>
                        </div>
                      </div>

                      {/* Price */}
                      <div className="text-right">
                        <p className="text-2xl font-bold" style={{ color: '#7e9b84' }}>
                          {(item.price * item.quantity).toFixed(2)} €
                        </p>
                        <p className="text-sm" style={{ color: '#666' }}>
                          {item.price.toFixed(2)} € / ks
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </motion.div>
            ))}

            {/* Continue Shopping Button */}
            <Link to="/">
              <Button 
                variant="outline"
                size="lg"
                className="w-full sm:w-auto border-2 border-[#7e9b84] text-[#7e9b84] hover:bg-[#7e9b84] hover:text-white px-8 py-6 font-bold"
              >
                ← Pokračovať v nákupe
              </Button>
            </Link>
          </div>

          {/* Right: Order Summary */}
          <div className="lg:col-span-1">
            <div className="sticky top-24 space-y-6">
              {/* Trust Badge */}
              <motion.div
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                className="bg-white rounded-2xl shadow-lg p-6 text-center border-t-4 border-[#7e9b84]"
              >
                <div className="flex justify-center gap-1 mb-3">
                  {[...Array(5)].map((_, i) => (
                    <Star key={i} className="w-6 h-6 fill-yellow-400 text-yellow-400" />
                  ))}
                </div>
                <h3 className="font-bold text-xl mb-2">Top hodnotenie</h3>
                <p className="text-lg" style={{ color: '#666' }}>
                  1 000+ skladových produktov
                </p>
                <div className="mt-4 pt-4 border-t border-gray-200">
                  <p className="text-sm" style={{ color: '#666' }}>
                    ⚡ Expedícia do 24 hodín
                  </p>
                </div>
              </motion.div>

              {/* Order Summary */}
              <motion.div
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ delay: 0.1 }}
                className="bg-white rounded-2xl shadow-lg p-6 border border-gray-100"
              >
                <h2 className="text-2xl font-bold mb-6">Sumár objednávky</h2>

                {/* Coupon Code */}
                <div className="mb-6 pb-6 border-b border-gray-200">
                  <button
                    onClick={() => setCouponExpanded(!couponExpanded)}
                    className="flex items-center justify-between w-full text-left hover:text-[#7e9b84] transition-colors"
                  >
                    <span className="flex items-center gap-2 font-medium">
                      <Tag className="w-5 h-5" />
                      Zľavový kód
                    </span>
                    {couponExpanded ? (
                      <ChevronUp className="w-5 h-5" />
                    ) : (
                      <ChevronDown className="w-5 h-5" />
                    )}
                  </button>
                  
                  {couponExpanded && (
                    <div className="mt-4 flex gap-2">
                      <input
                        type="text"
                        value={couponCode}
                        onChange={(e) => setCouponCode(e.target.value)}
                        placeholder="Zadajte kód"
                        className="flex-1 px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-[#7e9b84]"
                      />
                      <Button 
                        className="px-6 bg-gray-900 hover:bg-gray-800 text-white"
                      >
                        Použiť
                      </Button>
                    </div>
                  )}
                </div>

                <div className="space-y-4 mb-6">
                  <div className="flex justify-between text-lg">
                    <span style={{ color: '#666' }}>Medzisúčet</span>
                    <span className="font-bold">{subtotal.toFixed(2)} €</span>
                  </div>
                  <div className="flex justify-between text-lg">
                    <span style={{ color: '#666' }}>DPH (20%)</span>
                    <span className="font-bold">{tax.toFixed(2)} €</span>
                  </div>
                </div>

                <div className="flex justify-between items-center mb-6 pb-6 border-t-2 border-gray-200 pt-6">
                  <span className="text-xl font-bold">Celkom</span>
                  <span className="text-3xl font-bold" style={{ color: '#7e9b84' }}>
                    {total.toFixed(2)} €
                  </span>
                </div>

                {/* Checkout Button */}
                <Link to="/pokladna">
                  <Button
                    size="lg"
                    className="w-full bg-[#7e9b84] hover:bg-[#6a8470] text-white font-bold py-6 text-lg shadow-lg hover:shadow-xl transition-all"
                  >
                    Prejsť do pokladne
                    <ArrowRight className="w-5 h-5 ml-2" />
                  </Button>
                </Link>

                {/* Security info */}
                <div className="mt-4 flex items-center justify-center gap-2 text-sm" style={{ color: '#666' }}>
                  <Shield className="w-4 h-4" style={{ color: '#7e9b84' }} />
                  Bezpečná platba SSL
                </div>
              </motion.div>
            </div>
          </div>
        </div>

        {/* Trust Indicators */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.2 }}
          className="mt-16"
        >
          <div className="py-12 rounded-2xl" style={{ backgroundColor: '#7e9b84' }}>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 px-8">
              <div className="flex flex-col items-center text-center gap-3">
                <div className="w-16 h-16 rounded-full flex items-center justify-center bg-white">
                  <Shield className="w-8 h-8" style={{ color: '#7e9b84' }} />
                </div>
                <h3 className="font-bold text-white">
                  Bezpečný nákup
                </h3>
                <p className="text-sm text-white leading-relaxed opacity-90">
                  Platby zabezpečené pomocou SSL šifrovania
                </p>
              </div>
              
              <div className="flex flex-col items-center text-center gap-3">
                <div className="w-16 h-16 rounded-full flex items-center justify-center bg-white">
                  <MapPin className="w-8 h-8" style={{ color: '#7e9b84' }} />
                </div>
                <h3 className="font-bold text-white">
                  Vyrobené v EÚ
                </h3>
                <p className="text-sm text-white leading-relaxed opacity-90">
                  Exkluzívne priamo od výrobcu
                </p>
              </div>
              
              <div className="flex flex-col items-center text-center gap-3">
                <div className="w-16 h-16 rounded-full flex items-center justify-center bg-white">
                  <Award className="w-8 h-8" style={{ color: '#7e9b84' }} />
                </div>
                <h3 className="font-bold text-white">
                  Vysoká kvalita
                </h3>
                <p className="text-sm text-white leading-relaxed opacity-90">
                  Každý produkt prejde kontrolou kvality
                </p>
              </div>
              
              <div className="flex flex-col items-center text-center gap-3">
                <div className="w-16 h-16 rounded-full flex items-center justify-center bg-white">
                  <Headphones className="w-8 h-8" style={{ color: '#7e9b84' }} />
                </div>
                <h3 className="font-bold text-white">
                  Zákaznícka podpora
                </h3>
                <p className="text-sm text-white leading-relaxed opacity-90">
                  Odborná online podpora vždy k dispozícii
                </p>
              </div>
            </div>
          </div>
        </motion.div>

        {/* Customer Reviews */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.3 }}
          className="mt-20"
        >
          {/* Section Header */}
          <div className="text-center mb-12">
            <div className="inline-block px-4 py-2 bg-[#7e9b84] text-white rounded-full mb-4">
              Spokojní zákazníci
            </div>
            <h2 className="text-3xl md:text-4xl font-bold mb-4">
              Hodnotenia zákazníkov
            </h2>
            <p className="text-lg max-w-2xl mx-auto" style={{ color: '#666' }}>
              Pozrite si, čo hovoria naši zákazníci o našich produktoch
            </p>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            {testimonials.map((testimonial, index) => (
              <div key={index} className="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all">
                <div className="flex items-center justify-between mb-4">
                  <div className="flex gap-1">
                    {[...Array(testimonial.rating)].map((_, i) => (
                      <Star key={i} className="w-5 h-5 fill-yellow-400 text-yellow-400" />
                    ))}
                  </div>
                  <span className="text-sm" style={{ color: '#666' }}>{testimonial.date}</span>
                </div>
                
                <p className="font-bold mb-3">{testimonial.name}</p>
                
                <p className="text-sm leading-relaxed" style={{ color: '#666' }}>
                  {testimonial.text}
                </p>
              </div>
            ))}
          </div>

          {/* Overall Rating */}
          <div className="bg-white rounded-2xl shadow-lg p-8 text-center border border-gray-100">
            <div className="flex items-center justify-center gap-3 mb-3">
              <Star className="w-8 h-8 fill-yellow-400 text-yellow-400" />
              <span className="text-5xl font-bold">4.72</span>
            </div>
            <p className="text-lg" style={{ color: '#666' }}>
              z 1 169+ hodnotení
            </p>
          </div>
        </motion.div>
      </div>
    </div>
  );
}