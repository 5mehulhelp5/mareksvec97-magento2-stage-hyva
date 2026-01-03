import { motion } from "motion/react";
import { CheckCircle, Globe, MapPin, Award, Scale, Users, Package } from "lucide-react";
import { Button } from "../components/ui/button";
import { Link } from "react-router-dom";
import productionImage1 from "figma:asset/ac17e16b42611fee064d66c2e3a4e5cb5c86e1fe.png";
import productionImage2 from "figma:asset/79e78ffdebde8548cd517b0933d974f625cf2645.png";
import productionImage3 from "figma:asset/138fd1246e30c8f2d3d60c26e1b85c2fe11eb423.png";
import { ExpansionMap } from "../components/ExpansionMap";

export function AboutUsPage() {
  return (
    <div className="bg-white">
      {/* Hero Section with Stats */}
      <section className="bg-[#7e9b84] text-white py-12 md:py-16 lg:py-24">
        <div className="container mx-auto px-4 max-w-6xl">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            className="text-center"
          >
            <h1 className="mb-3 md:mb-4 text-3xl md:text-4xl lg:text-5xl leading-tight">
              Vyrábame kovovú architektúru, ktorá vydrží generácie
            </h1>
            
            <p className="text-base md:text-xl lg:text-2xl max-w-3xl mx-auto opacity-90 leading-relaxed px-2">
              Nie sme len predajcovia. Sme skutočná výrobná firma s vlastnou dielňou a tímom 
              skúsených remeselníkov, ktorí každý deň vytvárajú kvalitné produkty pripravené na okamžité dodanie.
            </p>
          </motion.div>

          {/* Stats Grid */}
          <div className="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8 mt-10 md:mt-12 max-w-5xl mx-auto">
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6 }}
              className="text-center py-4 md:py-0"
            >
              <Award className="w-8 h-8 md:w-10 md:h-10 mx-auto mb-3 opacity-90" />
              <div className="text-4xl md:text-4xl lg:text-5xl mb-2 font-bold">10+</div>
              <div className="text-sm md:text-base opacity-90">Rokov skúseností</div>
            </motion.div>
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6, delay: 0.1 }}
              className="text-center py-4 md:py-0"
            >
              <Scale className="w-8 h-8 md:w-10 md:h-10 mx-auto mb-3 opacity-90" />
              <div className="text-4xl md:text-4xl lg:text-5xl mb-2 font-bold">1000+</div>
              <div className="text-sm md:text-base opacity-90">Ton železa</div>
            </motion.div>
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6, delay: 0.2 }}
              className="text-center py-4 md:py-0"
            >
              <Users className="w-8 h-8 md:w-10 md:h-10 mx-auto mb-3 opacity-90" />
              <div className="text-4xl md:text-4xl lg:text-5xl mb-2 font-bold">5000+</div>
              <div className="text-sm md:text-base opacity-90">Zákazníkov</div>
            </motion.div>
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6, delay: 0.3 }}
              className="text-center py-4 md:py-0"
            >
              <Package className="w-8 h-8 md:w-10 md:h-10 mx-auto mb-3 opacity-90" />
              <div className="text-4xl md:text-4xl lg:text-5xl mb-2 font-bold">50+</div>
              <div className="text-sm md:text-base opacity-90">Produktov skladom</div>
            </motion.div>
          </div>
        </div>
      </section>

      {/* Všetko pod jednou strechou */}
      <section className="py-16 md:py-24">
        <div className="max-w-7xl mx-auto px-4">
          <div className="grid md:grid-cols-2 gap-12 md:gap-16 items-center">
            {/* Image */}
            <motion.div
              initial={{ opacity: 0, x: -30 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.8 }}
              className="order-2 md:order-1"
            >
              <div className="relative rounded-2xl overflow-hidden shadow-xl">
                <img
                  src={productionImage1}
                  alt="Naša výroba"
                  className="w-full h-auto object-cover"
                />
              </div>
            </motion.div>

            {/* Text Content */}
            <motion.div
              initial={{ opacity: 0, x: 30 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.8 }}
              className="order-1 md:order-2"
            >
              <h2 className="mb-6 text-3xl md:text-4xl lg:text-5xl">
                Všetko pod <span className="text-[#7e9b84]">jednou strechou</span>
              </h2>
              
              <p className="text-gray-600 text-base md:text-lg mb-6">
                Naša vášeň spočíva v tvorbe dokonalých produktov, a preto celý proces výroby 
                zastrešujeme priamo v našej dielni - od prvotného návrhu až po finálnu povrchovú úpravu.
              </p>
              
              <p className="text-gray-600 text-base md:text-lg mb-6">
                Toto nám umožňuje zabezpečiť, že každý výrobok, ktorý opustí naše ruky a dorazí k vám, 
                je bezchybný a splňuje najvyššie štandardy kvality.
              </p>

              <p className="text-gray-600 text-base md:text-lg mb-8">
                <strong className="text-gray-900">Váš spokojný pocit z dokonalého produktu je pre nás tou najlepšou odmenou.</strong>
              </p>

              <div className="flex flex-wrap gap-4">
                <div className="flex items-center gap-2">
                  <CheckCircle className="w-5 h-5 text-[#7e9b84] flex-shrink-0" />
                  <span className="text-gray-700">Vlastná výroba od A po Z</span>
                </div>
                <div className="flex items-center gap-2">
                  <CheckCircle className="w-5 h-5 text-[#7e9b84] flex-shrink-0" />
                  <span className="text-gray-700">Kontrola kvality</span>
                </div>
                <div className="flex items-center gap-2">
                  <CheckCircle className="w-5 h-5 text-[#7e9b84] flex-shrink-0" />
                  <span className="text-gray-700">Európska výroba</span>
                </div>
              </div>
            </motion.div>
          </div>
        </div>
      </section>

      {/* Vlastný vývoj produktov */}
      <section className="py-16 md:py-24 bg-gray-50">
        <div className="max-w-7xl mx-auto px-4">
          <div className="grid md:grid-cols-2 gap-12 md:gap-16 items-center">
            {/* Text Content */}
            <motion.div
              initial={{ opacity: 0, x: -30 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.8 }}
            >
              <h2 className="mb-6 text-3xl md:text-4xl lg:text-5xl">
                Vlastný vývoj <span className="text-[#7e9b84]">produktov</span>
              </h2>
              
              <p className="text-gray-600 text-base md:text-lg mb-6">
                V srdci našej firmy stojí tím vysoko kvalifikovaných inžinierov, ktorí sú zanietení 
                svojou prácou a sú neustále v pohotovosti priniesť nové inovácie do nášho produktového portfólia.
              </p>
              
              <p className="text-gray-600 text-base md:text-lg mb-6">
                Každý člen nášho tímu prispieva svojimi jedinečnými schopnosťami a bohatými skúsenosťami, 
                ktoré získal počas mnohých rokov práce v oblasti kovovýroby.
              </p>

              <p className="text-gray-600 text-base md:text-lg">
                Hoci sú stolové nohy jedným z našich hlavných výrobných zameraní, naše skúsenosti 
                v oblasti kovového spracovania siahajú ďaleko za tento segment. 
                <strong className="text-gray-900"> Od drobných doplnkov až po veľké konštrukcie.</strong>
              </p>
            </motion.div>

            {/* Image */}
            <motion.div
              initial={{ opacity: 0, x: 30 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.8 }}
            >
              <div className="relative rounded-2xl overflow-hidden shadow-xl">
                <img
                  src={productionImage2}
                  alt="Náš tím pri práci"
                  className="w-full h-auto object-cover"
                />
              </div>
            </motion.div>
          </div>
        </div>
      </section>

      {/* Vyrábame ručne a kvalitne */}
      <section className="py-16 md:py-24">
        <div className="max-w-7xl mx-auto px-4">
          <div className="grid md:grid-cols-2 gap-12 md:gap-16 items-center">
            {/* Image */}
            <motion.div
              initial={{ opacity: 0, x: -30 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.8 }}
              className="order-2 md:order-1"
            >
              <div className="relative rounded-2xl overflow-hidden shadow-xl">
                <img
                  src={productionImage3}
                  alt="Ručná práca s kovovým spracovaním"
                  className="w-full h-auto object-cover"
                />
              </div>
            </motion.div>

            {/* Text Content */}
            <motion.div
              initial={{ opacity: 0, x: 30 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.8 }}
              className="order-1 md:order-2"
            >
              <h2 className="mb-6 text-3xl md:text-4xl lg:text-5xl">
                Vyrábame <span className="text-[#7e9b84]">ručne a kvalitne</span>
              </h2>
              
              <p className="text-gray-600 text-base md:text-lg mb-6">
                V srdci našej filozofie leží presvedčenie, že skutočná kvalita sa nedá dosiahnuť 
                strojmi samotnými. Preto sa každý náš produkt rodí z kombinácie moderných technológií 
                a tradičného remeselného umenia.
              </p>
              
              <p className="text-gray-600 text-base md:text-lg mb-6">
                Každá stolová noha, regál či iný výrobok prechádza rukami našich skúsených remeselníkov, 
                ktorí dbajú na dokonalosť každého detailu.
              </p>

              <p className="text-gray-600 text-base md:text-lg">
                <strong className="text-gray-900">Táto ručná práca nie je len o technike, je to aj o láske 
                k remeslu a hrdosti na vytvorenie niečoho trváceho a hodnotného.</strong>
              </p>
            </motion.div>
          </div>
        </div>
      </section>

      {/* Naše poslanie a história */}
      <section className="py-16 md:py-24 bg-gray-50">
        <div className="max-w-4xl mx-auto px-4">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
            className="text-center"
          >
            <h2 className="mb-8 text-3xl md:text-4xl lg:text-5xl">
              Naše poslanie a <span className="text-[#7e9b84]">história</span>
            </h2>
            
            <div className="text-left space-y-6 text-gray-600 text-base md:text-lg">
              <p>
                Naša cesta v kovovýrobe začala <strong className="text-gray-900">pred 3 rokmi v malej dielni</strong>. 
                Od prvých nápadov, cez nespočetné hodiny strávené pri navrhovaní 
                produktov a zlepšovaní procesov až po dnešný deň, kedy sa môžeme pochváliť moderným 
                výrobným zariadením a skvelým tímom.
              </p>
              
              <p>
                Každý krok na tejto ceste nás naučil hodnote precíznej práce, inovácií a vášne k remeslu.
              </p>
              
              <p>
                <strong className="text-gray-900">Našim poslaním je nielen vyrábať výrobky najvyššej kvality, 
                ale aj neustále zlepšovať naše procesy a inovácie v oblasti kovovýroby.</strong>
              </p>
              
              <p>
                V našej práci vidíme viac ako jen železo a náradie - vidíme umenie a oddanosť každého 
                jednotlivca, ktorý prispieva k tvorbe nášho produktu.
              </p>
            </div>
          </motion.div>
        </div>
      </section>

      {/* Medzinárodná expanzia */}
      <section className="py-16 md:py-24 bg-white">
        <div className="max-w-7xl mx-auto px-4">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
            className="text-center mb-12"
          >
            <div className="flex items-center justify-center gap-3 mb-4">
              <Globe className="w-10 h-10 text-[#7e9b84]" />
              <h2 className="text-3xl md:text-4xl lg:text-5xl">
                Expanzia po <span className="text-[#7e9b84]">celej Európe</span>
              </h2>
            </div>
            <p className="text-gray-600 text-base md:text-lg max-w-3xl mx-auto">
              Z malej dielne sme sa rozrástli na medzinárodnú firmu. Naše produkty dnes nájdete 
              v domácnostiach a podnikoch po celej strednej a východnej Európe.
            </p>
          </motion.div>

          <div className="grid md:grid-cols-2 gap-12 md:gap-16 items-center">
            {/* Map Visualization */}
            <motion.div
              initial={{ opacity: 0, x: -30 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.8 }}
            >
              <div className="relative rounded-2xl overflow-hidden shadow-xl bg-gray-100 p-8">
                <ExpansionMap />
              </div>
            </motion.div>

            {/* Countries List */}
            <motion.div
              initial={{ opacity: 0, x: 30 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.8 }}
            >
              <h3 className="mb-8 text-2xl md:text-3xl">
                <strong>Kde nás nájdete</strong>
              </h3>

              <div className="grid gap-6">
                {[
                  { country: "Slovensko", flag: "🇸🇰", year: "2021" },
                  { country: "Česká republika", flag: "🇨🇿", year: "2022" },
                  { country: "Poľsko", flag: "🇵🇱", year: "2023" },
                  { country: "Rumunsko", flag: "🇷🇴", year: "2024" },
                  { country: "Bulharsko", flag: "🇧🇬", year: "2024" },
                ].map((item, index) => (
                  <motion.div
                    key={item.country}
                    initial={{ opacity: 0, x: 20 }}
                    whileInView={{ opacity: 1, x: 0 }}
                    viewport={{ once: true }}
                    transition={{ duration: 0.4, delay: index * 0.1 }}
                    className="flex items-center gap-4 p-4 bg-white rounded-xl border-2 border-gray-100 hover:border-[#7e9b84] transition-colors"
                  >
                    <div className="flex items-center justify-center w-12 h-12 bg-gray-50 rounded-lg text-2xl">
                      {item.flag}
                    </div>
                    <div className="flex-1">
                      <div className="text-gray-900">{item.country}</div>
                      <div className="text-sm text-gray-500">od roku {item.year}</div>
                    </div>
                    <MapPin className="w-5 h-5 text-[#7e9b84]" />
                  </motion.div>
                ))}
              </div>

              <div className="mt-8 p-6 bg-[#7e9b84]/10 rounded-xl">
                <p className="text-gray-700">
                  <strong className="text-gray-900">Viac ako 5 krajín,</strong> tisícky spokojných 
                  zákazníkov a neustále rastúca sieť partnerov. Naša misia je priniesť kvalitné 
                  kovovéo produkty do každého kúta Európy.
                </p>
              </div>
            </motion.div>
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-16 md:py-24 bg-gradient-to-br from-[#7e9b84] to-[#6d8a73] text-white">
        <div className="max-w-4xl mx-auto px-4 text-center">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
          >
            <h2 className="mb-6 text-3xl md:text-4xl lg:text-5xl">
              <strong>Pripravení objednať?</strong>
            </h2>
            <p className="text-lg md:text-xl text-white/90 mb-10 max-w-2xl mx-auto">
              Máme široký výber stolových nôh skladom pripravených na okamžité odoslanie. 
              Rýchle dodanie po celej Európe.
            </p>
            
            <div className="flex flex-wrap gap-4 justify-center">
              <Button 
                asChild
                className="bg-white hover:bg-gray-100 text-[#7e9b84] px-8 py-6 text-base font-semibold border-0"
              >
                <Link to="/stolove-nohy">
                  Zobraziť produkty
                </Link>
              </Button>
              <Button 
                asChild
                variant="outline"
                className="border-2 border-white text-white hover:bg-white/10 px-8 py-6 text-base font-semibold"
              >
                <Link to="/galeria">
                  Galéria inšpirácií
                </Link>
              </Button>
            </div>
          </motion.div>
        </div>
      </section>
    </div>
  );
}