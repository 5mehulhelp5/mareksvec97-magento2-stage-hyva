import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from "./ui/accordion";
import { HelpCircle } from "lucide-react";

export function FaqSection() {
  const faqs = [
    {
      question: "Aký je termín dodania?",
      answer: "Štandardný termín dodania je 3-5 pracovných dní od potvrdenia objednávky. Pre expresné dodanie ponúkame možnosť dodania do 24-48 hodín za príplatok."
    },
    {
      question: "Môžem si vybrať vlastný dizajn?",
      answer: "Samozrejme! Ponúkame možnosť plnej personalizácie. Môžete si vybrať z našich šablón alebo nahrať vlastný návrh. Náš tím vám pomôže s optimalizáciou dizajnu pre najlepší výsledok."
    },
    {
      question: "Aké materiály používate?",
      answer: "Používame výhradne kvalitné materiály - oceľ s práškovou farbou pre stolové nohy a hliník/nerez pre domové čísla. Všetky materiály sú odolné voči poveternostným vplyvom a korózii."
    },
    {
      question: "Ponúkate záruku na produkty?",
      answer: "Áno, na všetky naše produkty poskytujeme 2-ročnú záruku. Záruka pokrýva výrobné chyby a defekty materiálu. Navyše máte 14-dňovú možnosť vrátenia tovaru bez udania důvodu."
    },
    {
      question: "Ako prebieha montáž stolových nôh?",
      answer: "Montáž je veľmi jednoduchá a zvládne ju každý. K nohám dodávame kompletný montážny materiál a podrobný návod. Celý proces trvá približne 15-30 minút."
    }
  ];

  return null;
}
