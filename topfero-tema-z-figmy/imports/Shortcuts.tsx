import svgPaths from "./svg-rrewuqmewt";

function IconOutlineGlobe() {
  return (
    <div className="relative shrink-0 size-[24px]" data-name="Icon/Outline/globe">
      <svg className="block size-full" fill="none" preserveAspectRatio="none" viewBox="0 0 24 24">
        <g id="Icon/Outline/globe">
          <path d={svgPaths.p1398a920} id="Icon" stroke="var(--stroke-0, #1E293B)" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" />
        </g>
      </svg>
    </div>
  );
}

function IconOutlineChevronDown() {
  return (
    <div className="relative shrink-0 size-[12px]" data-name="Icon/Outline/chevron-down">
      <svg className="block size-full" fill="none" preserveAspectRatio="none" viewBox="0 0 12 12">
        <g id="Icon/Outline/chevron-down">
          <path d="M9.5 4.5L6 8L2.5 4.5" id="Icon" stroke="var(--stroke-0, #64748B)" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" />
        </g>
      </svg>
    </div>
  );
}

function LanguageCurrency() {
  return (
    <div className="content-stretch flex gap-[4px] items-center relative shrink-0" data-name="Language/Currency">
      <p className="font-['Inter:Regular',sans-serif] font-normal leading-[16px] not-italic relative shrink-0 text-[#64748b] text-[12px] text-nowrap">English (€)</p>
      <IconOutlineChevronDown />
    </div>
  );
}

function LanguageAndCurrency() {
  return (
    <div className="content-stretch flex gap-[6px] items-center relative shrink-0" data-name="Language and currency">
      <IconOutlineGlobe />
      <LanguageCurrency />
    </div>
  );
}

function IconOutlineSearch() {
  return (
    <div className="relative shrink-0 size-[24px]" data-name="Icon/Outline/search">
      <svg className="block size-full" fill="none" preserveAspectRatio="none" viewBox="0 0 24 24">
        <g id="Icon/Outline/search">
          <path d={svgPaths.p1e319980} id="Icon" stroke="var(--stroke-0, #1E293B)" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" />
        </g>
      </svg>
    </div>
  );
}

function IconOutlineUserCircle() {
  return (
    <div className="relative shrink-0 size-[24px]" data-name="Icon/Outline/user-circle">
      <svg className="block size-full" fill="none" preserveAspectRatio="none" viewBox="0 0 24 24">
        <g id="Icon/Outline/user-circle">
          <path d={svgPaths.p2d8c3c20} id="Icon" stroke="var(--stroke-0, #1E293B)" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" />
        </g>
      </svg>
    </div>
  );
}

function IconOutlineShoppingCart() {
  return (
    <div className="relative shrink-0 size-[24px]" data-name="Icon/Outline/shopping-cart">
      <svg className="block size-full" fill="none" preserveAspectRatio="none" viewBox="0 0 24 24">
        <g id="Icon/Outline/shopping-cart">
          <path d={svgPaths.p1dfd3500} id="Icon" stroke="var(--stroke-0, #1E293B)" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" />
        </g>
      </svg>
    </div>
  );
}

export default function Shortcuts() {
  return (
    <div className="content-stretch flex gap-[8px] items-start relative size-full" data-name="Shortcuts">
      <LanguageAndCurrency />
      <IconOutlineSearch />
      <IconOutlineUserCircle />
      <IconOutlineShoppingCart />
    </div>
  );
}