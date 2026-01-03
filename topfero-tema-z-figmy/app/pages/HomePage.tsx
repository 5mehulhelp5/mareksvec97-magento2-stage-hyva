import { HeroSection } from "../components/HeroSection";
import { CategoriesSection } from "../components/CategoriesSection";
import { AboutSection } from "../components/AboutSection";
import { ProductSection } from "../components/ProductSection";
import { TestimonialsSection } from "../components/TestimonialsSection";
import { TrustBanner } from "../components/TrustBanner";
import { FaqSection } from "../components/FaqSection";
import { CtaSection } from "../components/CtaSection";
import { BestSellersSection } from "../components/BestSellersSection";

export function HomePage() {
  return (
    <>
      <HeroSection />
      <TrustBanner />
      <CategoriesSection />
      <BestSellersSection />
      <AboutSection />
      <TestimonialsSection />
      <ProductSection />
      <FaqSection />
      <CtaSection />
    </>
  );
}
