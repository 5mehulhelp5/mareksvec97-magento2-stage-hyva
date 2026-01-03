import { useState } from "react";
import { MessageCircle, X, Send } from "lucide-react";
import { Button } from "./ui/button";
import { motion, AnimatePresence } from "motion/react";

export function LiveChat() {
  const [isOpen, setIsOpen] = useState(false);
  const [message, setMessage] = useState("");

  return (
    <>
      {/* Chat widget button */}
      <AnimatePresence>
        {!isOpen && (
          <motion.div
            initial={{ scale: 0 }}
            animate={{ scale: 1 }}
            exit={{ scale: 0 }}
            className="fixed bottom-6 right-6 z-50"
          >
            <Button
              onClick={() => setIsOpen(true)}
              size="lg"
              className="w-16 h-16 rounded-full shadow-2xl relative text-white"
              style={{ backgroundColor: '#7e9b84' }}
              onMouseEnter={(e) => e.currentTarget.style.backgroundColor = '#6d8a73'}
              onMouseLeave={(e) => e.currentTarget.style.backgroundColor = '#7e9b84'}
            >
              <MessageCircle className="w-7 h-7 text-white" />
              <span className="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center text-xs text-white font-bold animate-pulse">
                1
              </span>
            </Button>
            
            {/* Tooltip */}
            <motion.div
              initial={{ opacity: 0, x: 10 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ delay: 1 }}
              className="absolute bottom-full right-0 mb-4 bg-gray-900 text-white px-4 py-2 rounded-lg shadow-lg whitespace-nowrap"
            >
              Máte otázku? Napíšte nám!
              <div className="absolute top-full right-8 w-0 h-0 border-l-8 border-l-transparent border-r-8 border-r-transparent border-t-8 border-t-gray-900"></div>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>

      {/* Chat window */}
      <AnimatePresence>
        {isOpen && (
          <motion.div
            initial={{ opacity: 0, y: 20, scale: 0.95 }}
            animate={{ opacity: 1, y: 0, scale: 1 }}
            exit={{ opacity: 0, y: 20, scale: 0.95 }}
            className="fixed bottom-6 right-6 w-96 max-w-[calc(100vw-3rem)] bg-white rounded-2xl shadow-2xl z-50 overflow-hidden border border-gray-200"
          >
            {/* Header */}
            <div className="text-white p-4 flex items-center justify-between" style={{ background: 'linear-gradient(to right, #7e9b84, #6d8a73)' }}>
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                  <MessageCircle className="w-6 h-6" />
                </div>
                <div>
                  <div className="font-bold">Zákaznícka podpora</div>
                  <div className="text-xs flex items-center gap-2" style={{ color: '#d4e0d6' }}>
                    <span className="w-2 h-2 bg-green-400 rounded-full"></span>
                    Online - Zvyčajne odpovedáme do 2 minút
                  </div>
                </div>
              </div>
              <button
                onClick={() => setIsOpen(false)}
                className="hover:bg-white/20 rounded-full p-2 transition-colors"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            {/* Messages */}
            <div className="h-96 overflow-y-auto p-4 space-y-4 bg-gray-50">
              {/* Bot message */}
              <div className="flex gap-3">
                <div className="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" style={{ backgroundColor: '#7e9b84' }}>
                  <MessageCircle className="w-5 h-5 text-white" />
                </div>
                <div className="bg-white rounded-2xl rounded-tl-none p-4 shadow-sm max-w-[80%]">
                  <p className="text-gray-800">
                    Dobrý deň! 👋 Vitajte v TOPFEO.sk. Som tu, aby som vám pomohol. 
                    Máte otázky ohľadom našich produktov alebo objednávky?
                  </p>
                  <span className="text-xs text-gray-400 mt-2 block">Práve teraz</span>
                </div>
              </div>

              {/* Quick reply buttons */}
              <div className="flex flex-wrap gap-2">
                <button 
                  className="bg-white border border-gray-200 rounded-full px-4 py-2 text-sm transition-colors"
                  style={{ ['--hover-border' as string]: '#7e9b84', ['--hover-color' as string]: '#7e9b84' }}
                  onMouseEnter={(e) => {
                    e.currentTarget.style.borderColor = '#7e9b84';
                    e.currentTarget.style.color = '#7e9b84';
                  }}
                  onMouseLeave={(e) => {
                    e.currentTarget.style.borderColor = '';
                    e.currentTarget.style.color = '';
                  }}
                >
                  Chcem objednať čísla
                </button>
                <button 
                  className="bg-white border border-gray-200 rounded-full px-4 py-2 text-sm transition-colors"
                  onMouseEnter={(e) => {
                    e.currentTarget.style.borderColor = '#7e9b84';
                    e.currentTarget.style.color = '#7e9b84';
                  }}
                  onMouseLeave={(e) => {
                    e.currentTarget.style.borderColor = '';
                    e.currentTarget.style.color = '';
                  }}
                >
                  Otázka o doprave
                </button>
                <button 
                  className="bg-white border border-gray-200 rounded-full px-4 py-2 text-sm transition-colors"
                  onMouseEnter={(e) => {
                    e.currentTarget.style.borderColor = '#7e9b84';
                    e.currentTarget.style.color = '#7e9b84';
                  }}
                  onMouseLeave={(e) => {
                    e.currentTarget.style.borderColor = '';
                    e.currentTarget.style.color = '';
                  }}
                >
                  Potrebujem pomoc
                </button>
              </div>
            </div>

            {/* Input */}
            <div className="p-4 bg-white border-t border-gray-200">
              <div className="flex gap-2">
                <input
                  type="text"
                  value={message}
                  onChange={(e) => setMessage(e.target.value)}
                  placeholder="Napíšte správu..."
                  className="flex-1 px-4 py-3 border border-gray-200 rounded-full outline-none transition-colors"
                  style={{ ['--focus-border' as string]: '#7e9b84' }}
                  onFocus={(e) => e.currentTarget.style.borderColor = '#7e9b84'}
                  onBlur={(e) => e.currentTarget.style.borderColor = ''}
                  onKeyPress={(e) => {
                    if (e.key === 'Enter') {
                      // Handle send
                      setMessage("");
                    }
                  }}
                />
                <Button
                  size="icon"
                  className="w-12 h-12 rounded-full text-white"
                  style={{ backgroundColor: '#7e9b84' }}
                  onMouseEnter={(e) => e.currentTarget.style.backgroundColor = '#6d8a73'}
                  onMouseLeave={(e) => e.currentTarget.style.backgroundColor = '#7e9b84'}
                >
                  <Send className="w-5 h-5" />
                </Button>
              </div>
              <p className="text-xs text-gray-400 mt-2 text-center">
                Odpovedáme zvyčajne do 2 minút
              </p>
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </>
  );
}