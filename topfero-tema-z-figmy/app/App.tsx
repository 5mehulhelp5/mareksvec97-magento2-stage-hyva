import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import { Header } from "./components/Header";
import { Footer } from "./components/Footer";
import { LiveChat } from "./components/LiveChat";
import { CookieConsent } from "./components/CookieConsent";
import { Toaster } from "./components/ui/sonner";
import { HomePage } from "./pages/HomePage";
import { CustomerGalleryPage } from "./pages/CustomerGalleryPage";
import { TableLegsCategory } from "./pages/TableLegsCategory";
import { ProductDetail } from "./pages/ProductDetail";
import { AboutUsPage } from "./pages/AboutUsPage";
import { CartPage } from "./pages/CartPage";

export default function App() {
  return (
    <Router>
      <div className="min-h-screen bg-white">
        <Toaster />
        
        <Header />
        
        <main>
          <Routes>
            <Route path="/" element={<HomePage />} />
            <Route path="/galeria" element={<CustomerGalleryPage />} />
            <Route path="/stolove-nohy" element={<TableLegsCategory />} />
            <Route path="/produkt/:id" element={<ProductDetail />} />
            <Route path="/o-nas" element={<AboutUsPage />} />
            <Route path="/kosik" element={<CartPage />} />
          </Routes>
        </main>
        
        <Footer />
        <LiveChat />
        <CookieConsent />
      </div>
    </Router>
  );
}