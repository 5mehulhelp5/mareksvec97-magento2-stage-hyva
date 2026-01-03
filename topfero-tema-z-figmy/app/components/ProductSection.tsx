import { Button } from "./ui/button";
import { Badge } from "./ui/badge";
import { ArrowRight, Check, Star } from "lucide-react";
import { ImageWithFallback } from "./figma/ImageWithFallback";

export function ProductSection() {
  const products = [
    {
      title: "Plechové štítky na moderných strojoch",
      description: "Maximálne sa sústredíme na najlepšie prevody. Od najlepších výrobcov používame najvyššie kvalitné materiály. V porovnaní so súčinom výrobkov, ktorú máme pri nás na sklade.",
      image: "https://images.unsplash.com/photo-1651843783807-0ac94750f1cc?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtZXRhbCUyMGVuZ3JhdmluZyUyMHdvcmtzaG9wfGVufDF8fHx8MTc2NzMzNzA0N3ww&ixlib=rb-4.1.0&q=80&w=1080",
      badge: "Najpredávanejšie",
      badgeColor: "bg-green-600",
      features: [
        "Laserové gravírovanie",
        "Odolné materiály",
        "Moderné technológie",
        "Presná výroba"
      ],
      price: "Od 19,90€",
      discount: "-15%"
    },
    {
      title: "Potrebujete vyplniť čísla na mieru?",
      description: "Je to až príliš jednoduché. S popísanou službou, ktorú práve hľadáte a využívam niektoré technológie od jedného výrobcu. S nami sa s tým môžete stráviť pomerne rýchlo. Naplánovať sa pred vami cenu.",
      image: "https://images.unsplash.com/photo-1747862962568-75e850a47de6?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxob3VzZSUyMG51bWJlciUyMHBsYXRlJTIwZG9vcnxlbnwxfHx8fDE3NjczMzcwNDh8MA&ixlib=rb-4.1.0&q=80&w=1080",
      badge: "Nová kolekcia",
      badgeColor: "bg-purple-600",
      features: [
        "Rýchla konfigurácia",
        "Online návrh",
        "Okamžitá cena",
        "Jednoduchá objednávka"
      ],
      price: "Od 24,90€",
      discount: null
    }
  ];

  return null;
}