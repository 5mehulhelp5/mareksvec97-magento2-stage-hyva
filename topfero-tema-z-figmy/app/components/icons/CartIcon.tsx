import svgPaths from "../../../imports/svg-rrewuqmewt";

export function CartIcon({ className = "w-5 h-5" }: { className?: string }) {
  return (
    <div className={className}>
      <svg className="block size-full" fill="none" viewBox="0 0 24 24">
        <path d={svgPaths.p1dfd3500} stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" />
      </svg>
    </div>
  );
}
