export function ExpansionMap() {
  return (
    <svg
      viewBox="0 0 1000 800"
      className="w-full h-auto"
      xmlns="http://www.w3.org/2000/svg"
    >
      {/* Background */}
      <rect width="1000" height="800" fill="#f8f9fa" />
      
      {/* Grid lines for subtle effect */}
      <defs>
        <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
          <path d="M 40 0 L 0 0 0 40" fill="none" stroke="#e9ecef" strokeWidth="0.5"/>
        </pattern>
      </defs>
      <rect width="1000" height="800" fill="url(#grid)" />
      
      {/* EU Countries - Basic shapes (simplified) */}
      {/* Western Europe - Gray */}
      <g fill="#d1d5db" stroke="#fff" strokeWidth="1.5">
        {/* Portugal */}
        <path d="M 180 420 L 165 430 L 160 450 L 165 470 L 175 485 L 180 475 L 185 460 L 190 440 L 185 425 Z" />
        
        {/* Spain */}
        <path d="M 190 440 L 185 460 L 190 480 L 200 495 L 230 505 L 260 500 L 280 490 L 295 475 L 300 455 L 295 435 L 280 420 L 250 415 L 220 420 Z" />
        
        {/* France */}
        <path d="M 280 380 L 260 390 L 250 415 L 280 420 L 295 435 L 310 440 L 340 430 L 360 415 L 370 395 L 365 375 L 350 360 L 320 355 L 300 365 Z" />
        
        {/* Belgium */}
        <path d="M 340 320 L 330 330 L 335 345 L 350 350 L 365 345 L 368 335 L 360 325 Z" />
        
        {/* Netherlands */}
        <path d="M 345 305 L 340 315 L 340 320 L 360 325 L 375 315 L 375 300 L 365 295 Z" />
        
        {/* Germany */}
        <path d="M 375 300 L 365 315 L 368 335 L 380 350 L 400 365 L 430 375 L 455 370 L 475 355 L 485 330 L 490 305 L 480 285 L 460 275 L 430 280 L 400 290 Z" />
        
        {/* Denmark */}
        <path d="M 410 240 L 400 255 L 405 270 L 420 275 L 435 270 L 445 255 L 440 240 L 425 235 Z" />
        
        {/* Italy */}
        <path d="M 450 440 L 440 460 L 435 485 L 440 510 L 445 535 L 450 560 L 455 580 L 465 595 L 475 580 L 480 555 L 485 530 L 485 505 L 480 480 L 470 460 L 460 445 Z" />
        
        {/* Austria */}
        <path d="M 475 355 L 465 365 L 465 380 L 480 390 L 510 395 L 535 390 L 545 380 L 540 365 L 520 358 L 495 355 Z" />
        
        {/* Sweden */}
        <path d="M 480 140 L 470 160 L 465 185 L 470 215 L 480 240 L 495 260 L 515 275 L 530 285 L 545 275 L 555 250 L 560 220 L 560 190 L 555 160 L 545 135 L 530 115 L 510 105 L 495 110 Z" />
        
        {/* Finland */}
        <path d="M 560 90 L 550 110 L 545 135 L 555 160 L 565 185 L 580 210 L 600 230 L 620 240 L 640 235 L 655 215 L 665 185 L 670 155 L 670 125 L 665 100 L 650 80 L 630 70 L 605 70 L 585 75 Z" />
        
        {/* Greece */}
        <path d="M 580 510 L 570 525 L 570 545 L 575 565 L 585 580 L 600 585 L 615 580 L 625 565 L 625 545 L 620 525 L 605 515 L 590 512 Z" />
        
        {/* Croatia */}
        <path d="M 505 420 L 495 435 L 500 455 L 515 465 L 535 465 L 550 455 L 550 440 L 540 425 L 520 420 Z" />
        
        {/* Slovenia */}
        <path d="M 480 390 L 475 400 L 480 410 L 495 415 L 505 410 L 510 395 Z" />
      </g>
      
      {/* Eastern Europe - Lighter Gray */}
      <g fill="#e5e7eb" stroke="#fff" strokeWidth="1.5">
        {/* Lithuania */}
        <path d="M 590 250 L 580 260 L 580 275 L 590 290 L 610 295 L 630 290 L 640 275 L 635 260 L 620 252 Z" />
        
        {/* Latvia */}
        <path d="M 595 220 L 585 235 L 590 250 L 620 252 L 640 245 L 650 230 L 645 215 L 625 210 Z" />
        
        {/* Estonia */}
        <path d="M 605 190 L 595 200 L 595 215 L 625 210 L 645 215 L 660 205 L 660 190 L 640 185 Z" />
      </g>
      
      {/* OUR EXPANSION COUNTRIES - Brand Color #7e9b84 */}
      <g fill="#7e9b84" stroke="#fff" strokeWidth="2" className="expansion-countries">
        {/* Slovakia */}
        <path d="M 545 380 L 535 390 L 540 405 L 560 415 L 585 415 L 600 405 L 600 390 L 585 378 L 560 375 Z">
          <animate attributeName="opacity" values="1;0.8;1" dur="2s" repeatCount="indefinite" />
        </path>
        
        {/* Czech Republic */}
        <path d="M 490 305 L 480 320 L 485 340 L 500 355 L 520 358 L 540 350 L 548 335 L 545 315 L 530 300 L 510 298 Z">
          <animate attributeName="opacity" values="1;0.8;1" dur="2s" repeatCount="indefinite" begin="0.2s" />
        </path>
        
        {/* Poland */}
        <path d="M 530 235 L 515 250 L 510 270 L 515 295 L 530 315 L 560 330 L 595 335 L 625 330 L 650 315 L 665 295 L 670 270 L 665 245 L 650 230 L 620 225 L 585 225 L 555 230 Z">
          <animate attributeName="opacity" values="1;0.8;1" dur="2s" repeatCount="indefinite" begin="0.4s" />
        </path>
        
        {/* Romania */}
        <path d="M 600 405 L 590 420 L 590 445 L 600 470 L 625 485 L 655 490 L 680 480 L 695 460 L 695 435 L 685 415 L 660 405 L 630 400 Z">
          <animate attributeName="opacity" values="1;0.8;1" dur="2s" repeatCount="indefinite" begin="0.6s" />
        </path>
        
        {/* Bulgaria */}
        <path d="M 625 485 L 615 500 L 615 520 L 625 540 L 650 550 L 680 550 L 705 540 L 715 520 L 710 500 L 695 490 L 670 485 Z">
          <animate attributeName="opacity" values="1;0.8;1" dur="2s" repeatCount="indefinite" begin="0.8s" />
        </path>
      </g>
      
      {/* Country Labels for expansion countries */}
      <g fill="#2d5a3d" fontSize="14" fontWeight="600" fontFamily="Jost, sans-serif">
        <text x="572" y="398" textAnchor="middle">SK</text>
        <text x="515" y="332" textAnchor="middle">CZ</text>
        <text x="600" y="285" textAnchor="middle">PL</text>
        <text x="645" y="448" textAnchor="middle">RO</text>
        <text x="665" y="522" textAnchor="middle">BG</text>
      </g>
      
      {/* Pins on capital cities */}
      <g fill="#2d5a3d">
        {/* Bratislava */}
        <circle cx="560" cy="390" r="5">
          <animate attributeName="r" values="5;7;5" dur="1.5s" repeatCount="indefinite" />
        </circle>
        
        {/* Prague */}
        <circle cx="510" cy="325" r="5">
          <animate attributeName="r" values="5;7;5" dur="1.5s" repeatCount="indefinite" begin="0.3s" />
        </circle>
        
        {/* Warsaw */}
        <circle cx="600" cy="280" r="5">
          <animate attributeName="r" values="5;7;5" dur="1.5s" repeatCount="indefinite" begin="0.6s" />
        </circle>
        
        {/* Bucharest */}
        <circle cx="645" cy="450" r="5">
          <animate attributeName="r" values="5;7;5" dur="1.5s" repeatCount="indefinite" begin="0.9s" />
        </circle>
        
        {/* Sofia */}
        <circle cx="600" cy="515" r="5">
          <animate attributeName="r" values="5;7;5" dur="1.5s" repeatCount="indefinite" begin="1.2s" />
        </circle>
      </g>
      
      {/* Legend */}
      <g transform="translate(50, 650)">
        <rect width="280" height="120" fill="white" rx="12" stroke="#e5e7eb" strokeWidth="2" />
        
        <text x="20" y="30" fontSize="16" fontWeight="700" fill="#111827" fontFamily="Jost, sans-serif">Naša expanzia</text>
        
        <rect x="20" y="50" width="30" height="20" fill="#7e9b84" rx="4" />
        <text x="60" y="65" fontSize="13" fill="#6b7280" fontFamily="Jost, sans-serif">Aktívne trhy</text>
        
        <rect x="20" y="85" width="30" height="20" fill="#e5e7eb" rx="4" />
        <text x="60" y="100" fontSize="13" fill="#6b7280" fontFamily="Jost, sans-serif">Ostatné krajiny EÚ</text>
      </g>
      
      {/* Title */}
      <text x="500" y="50" fontSize="28" fontWeight="700" fill="#111827" textAnchor="middle" fontFamily="Jost, sans-serif">
        Stredná a Východná Európa
      </text>
      <text x="500" y="80" fontSize="16" fill="#6b7280" textAnchor="middle" fontFamily="Jost, sans-serif">
        5 krajín • Rýchle dodanie po celej Európe
      </text>
    </svg>
  );
}
