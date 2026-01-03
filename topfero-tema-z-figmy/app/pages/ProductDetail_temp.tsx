// Temporary file for toast function only
const handleAddToCart = () => {
  toast.custom((t) => (
    <motion.div
      initial={{ opacity: 0, y: -50, scale: 0.9 }}
      animate={{ opacity: 1, y: 0, scale: 1 }}
      exit={{ opacity: 0, scale: 0.9 }}
      transition={{ type: "spring", duration: 0.5, bounce: 0.3 }}
      className="bg-white rounded-2xl shadow-[0_20px_60px_-15px_rgba(0,0,0,0.3)] border border-gray-100 overflow-hidden max-w-md w-full"
    >
      {/* Green success bar */}
      <div className="h-1.5 bg-gradient-to-r from-emerald-500 via-green-500 to-emerald-500" />
      
      <div className="p-5">
        <div className="flex items-start gap-4">
          {/* Product Image */}
          <div className="flex-shrink-0 w-16 h-16 bg-gray-50 rounded-lg overflow-hidden border border-gray-100">
            <img
              src={images[0]}
              alt="Product"
              className="w-full h-full object-cover"
            />
          </div>
          
          {/* Content */}
          <div className="flex-1 min-w-0">
            <div className="flex items-start gap-2 mb-1">
              <div className="w-5 h-5 bg-emerald-500 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                <CheckCircle className="w-3.5 h-3.5 text-white fill-white" />
              </div>
              <h4 className="font-semibold text-gray-900">
                Pridané do košíka
              </h4>
            </div>
            
            <p className="text-sm text-gray-600 mb-4 ml-7">
              <span className="font-medium text-gray-900">Stolová Noha - Acamar</span> × {quantity}
            </p>
            
            {/* Buttons */}
            <div className="flex items-center gap-2 ml-7">
              <button
                onClick={() => {
                  toast.dismiss(t);
                  // Navigate to cart
                }}
                className="bg-[#7e9b84] hover:bg-[#6d8a73] text-white px-4 py-2 rounded-lg font-medium transition-all text-sm flex items-center gap-1.5 shadow-sm hover:shadow"
              >
                <CartIcon className="w-4 h-4" />
                <span>Zobraziť košík</span>
              </button>
              <button
                onClick={() => toast.dismiss(t)}
                className="text-sm text-gray-600 hover:text-gray-900 px-3 py-2 hover:bg-gray-50 rounded-lg transition-colors font-medium"
              >
                Pokračovať
              </button>
            </div>
          </div>
          
          {/* Close button */}
          <button
            onClick={() => toast.dismiss(t)}
            className="flex-shrink-0 w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all"
          >
            <X className="w-4 h-4" />
          </button>
        </div>
      </div>
    </motion.div>
  ), {
    duration: 6000,
    position: 'top-center',
  });
};
