import { ChevronDown } from "lucide-react";

const navigation = [
    { label: "Marketplace", href: "#" },
    { label: "Services", href: "#" },
    { label: "Classifieds", href: "#" },
    { label: "Stores", href: "#" },
];

export default function DesktopNavigation() {
    return (
        <nav
            aria-label="Main navigation"
            className="hidden border-t border-border bg-surface lg:block"
        >
            <div className="mx-auto flex h-12 max-w-7xl items-center justify-between px-6 lg:px-8">
                <div className="flex h-full items-center">
                    {navigation.map((item) => (
                        <a
                            key={item.label}
                            href={item.href}
                            className="flex h-full items-center px-4 text-sm font-semibold text-text-secondary transition-colors hover:text-brand-secondary"
                        >
                            {item.label}
                        </a>
                    ))}

                    <button
                        type="button"
                        className="flex h-full items-center gap-1 px-4 text-sm font-semibold text-text-secondary transition-colors hover:text-brand-secondary"
                    >
                        Categories
                        <ChevronDown size={14} strokeWidth={1.8} />
                    </button>
                </div>

                <a
                    href="#"
                    className="text-sm font-bold text-brand-secondary transition-colors hover:text-brand-secondary-hover"
                >
                    Sell on Dubai Lanka
                </a>
            </div>
        </nav>
    );
}
