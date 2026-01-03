import { X, Pencil, Trash2, Plus, Minus } from 'lucide-react';
import { Button } from './ui/button';
import { useState } from 'react';
import productImg1 from 'figma:asset/9ca27e24a4cf405b7beb8eb9ab4a281b7ffbe045.png';
import productImg2 from 'figma:asset/ba816421f2a66d222229f7f20617cc842c3a3231.png';
import { Link } from 'react-router-dom';

interface MinicartProduct {
  id: string;
  name: string;
  attributes: string;
  price: number;
  quantity: number;
  image: string;
}

interface MinicartProps {
  isOpen: boolean;
  onClose: () => void;
}

export function Minicart({ isOpen, onClose }: MinicartProps) {
  // Mock data - v reálnej aplikácii by to boli dáta zo state manažmentu
  const [products, setProducts] = useState<MinicartProduct[]>([
    {
      id: '1',
      name: 'Kovové nohy - Trapéz Life',
      attributes: 'Čierna, X tvar, Výška: 72 cm',
      price: 219.00,
      quantity: 1,
      image: productImg1
    },
    {
      id: '2',
      name: 'Kovový regál na víno',
      attributes: '7 políc, Čierna farba, Výška: 120 cm',
      price: 219.00,
      quantity: 1,
      image: productImg2
    }
  ]);

  const updateQuantity = (id: string, change: number) => {
    setProducts(prev => prev.map(product => {
      if (product.id === id) {
        const newQuantity = Math.max(1, product.quantity + change);
        return { ...product, quantity: newQuantity };
      }
      return product;
    }));
  };

  const subtotal = products.reduce((sum, product) => sum + (product.price * product.quantity), 0);

  return (
    <>
      {/* Overlay */}
      <div 
        className={`fixed inset-0 bg-black/50 transition-opacity z-40 ${
          isOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'
        }`}
        onClick={onClose}
      />

      {/* Minicart panel */}
      <div 
        className={`fixed top-0 right-0 h-full bg-white shadow-2xl z-50 w-full max-w-md transform transition-transform duration-300 ease-in-out ${
          isOpen ? 'translate-x-0' : 'translate-x-full'
        }`}
      >
        <div className="flex flex-col h-full">
          {/* Header */}
          <div className="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h2 className="font-semibold text-lg text-gray-900">Nákupný košík</h2>
            <button 
              onClick={onClose}
              className="p-2 hover:bg-gray-100 rounded-lg transition-colors"
            >
              <X className="w-5 h-5 text-gray-500" />
            </button>
          </div>

          {/* Products list */}
          <div className="flex-1 overflow-y-auto px-6 py-4">
            <div className="space-y-4">
              {products.map((product) => (
                <div key={product.id} className="flex gap-4 pb-4 border-b border-gray-100 last:border-0">
                  {/* Product image */}
                  <div className="flex-shrink-0">
                    <img 
                      src={product.image} 
                      alt={product.name}
                      className="w-20 h-20 object-cover rounded-lg"
                    />
                  </div>

                  {/* Product info and controls */}
                  <div className="flex-1 flex flex-col justify-between">
                    <div className="flex justify-between gap-4">
                      <div className="flex-1">
                        <h3 className="font-medium text-gray-900 text-sm mb-1">
                          {product.name}
                        </h3>
                        <p className="text-xs text-gray-500">
                          {product.attributes}
                        </p>
                      </div>
                      <div className="flex-shrink-0">
                        <span className="font-semibold text-gray-900">
                          {(product.price * product.quantity).toFixed(2)} €
                        </span>
                      </div>
                    </div>
                    
                    <div className="flex justify-between items-center gap-4 mt-2">
                      {/* Quantity controls */}
                      <div className="flex items-center border border-gray-200 rounded-lg">
                        <button 
                          onClick={() => updateQuantity(product.id, -1)}
                          className="p-2 hover:bg-gray-100 rounded-l-lg transition-colors"
                        >
                          <Minus className="w-3.5 h-3.5 text-gray-600" />
                        </button>
                        <span className="px-3 py-1 text-sm font-medium text-gray-900 min-w-[40px] text-center">
                          {product.quantity}
                        </span>
                        <button 
                          onClick={() => updateQuantity(product.id, 1)}
                          className="p-2 hover:bg-gray-100 rounded-r-lg transition-colors"
                        >
                          <Plus className="w-3.5 h-3.5 text-gray-600" />
                        </button>
                      </div>
                      
                      {/* Edit and Delete buttons */}
                      <div className="flex items-center gap-2">
                        <button className="p-1.5 hover:bg-gray-100 rounded transition-colors">
                          <Pencil className="w-4 h-4 text-gray-400 hover:text-gray-600" />
                        </button>
                        <button className="p-1.5 hover:bg-gray-100 rounded transition-colors">
                          <Trash2 className="w-4 h-4 text-gray-400 hover:text-red-500" />
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* Footer */}
          <div className="border-t border-gray-200 px-6 py-4 space-y-4">
            {/* Subtotal */}
            <div className="flex items-center justify-between text-sm">
              <span className="text-gray-600">Cena celkom</span>
              <span className="font-bold text-xl text-gray-900">
                {subtotal.toFixed(2)} €
              </span>
            </div>

            {/* Order button */}
            <Link to="/kosik" onClick={onClose}>
              <Button 
                className="w-full text-white font-semibold py-6 text-base shadow-md hover:shadow-lg transition-all"
                style={{ backgroundColor: '#7e9b84' }}
                onMouseEnter={(e) => e.currentTarget.style.backgroundColor = '#6d8a73'}
                onMouseLeave={(e) => e.currentTarget.style.backgroundColor = '#7e9b84'}
              >
                Zobraziť košík
              </Button>
            </Link>

            {/* Continue shopping link */}
            <button 
              onClick={onClose}
              className="w-full text-center text-sm text-gray-600 hover:text-gray-900 transition-colors py-2"
            >
              Pokračovať v nákupe
            </button>
          </div>
        </div>
      </div>
    </>
  );
}