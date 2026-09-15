import {
    Heart,
    HelpCircle,
    MapPin,
    ShoppingCart,
    UserRound,
} from "lucide-react";

import DesktopNavigation from "./DesktopNavigation";
import LocationSelector from "./LocationSelector";
import SearchBar from "./SearchBar";

export default function PublicHeader() {
    return (
        <header className="sticky top-0 z-40 border-b border-border bg-surface">
            {/* Desktop utility bar */}
            <div className="hidden border-b border-border bg-surface-subtle lg:block">
                <div className="mx-auto flex h-9 max-w-7xl items-center justify-between px-6 lg:px-8">
                    <LocationSelector compact />

                    <div className="flex items-center gap-5 text-xs font-medium text-text-secondary">
                        <a
                            href="#"
                            className="inline-flex items-center gap-1.5 transition-colors hover:text-text-primary"
                        >
                            <HelpCircle size={14} />
                            Help
                        </a>

                        <a
                            href="#"
                            className="font-semibold text-brand-secondary transition-colors hover:text-brand-secondary-hover"
                        >
                            Sell on Dubai Lanka
                        </a>
                    </div>
                </div>
            </div>

            {/* Main header */}
            <div className="bg-surface">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    {/* Desktop */}
                    <div className="hidden min-h-20 items-center gap-6 lg:flex">
                        <a
                            href="/"
                            className="shrink-0"
                            aria-label="Dubai Lanka home"
                        >
                            <div className="text-xl font-bold tracking-tight text-text-primary">
                                Dubai
                                <span className="text-brand-secondary">
                                    Lanka
                                </span>
                            </div>
                        </a>

                        <div className="min-w-0 flex-1">
                            <SearchBar />
                        </div>

                        <div className="flex shrink-0 items-center gap-1">
                            <a
                                href="#"
                                className="flex min-h-11 items-center gap-2 rounded-md px-3 text-sm font-medium text-text-secondary transition-colors hover:bg-surface-muted hover:text-text-primary"
                            >
                                <Heart size={19} strokeWidth={1.8} />
                                <span>Saved</span>
                            </a>

                            <a
                                href="#"
                                className="flex min-h-11 items-center gap-2 rounded-md px-3 text-sm font-medium text-text-secondary transition-colors hover:bg-surface-muted hover:text-text-primary"
                            >
                                <UserRound size={19} strokeWidth={1.8} />

                                <span className="leading-tight">
                                    <span className="block text-xs text-text-muted">
                                        Account
                                    </span>
                                    <span className="block font-semibold text-text-primary">
                                        Sign In / Register
                                    </span>
                                </span>
                            </a>

                            <a
                                href="#"
                                className="relative flex min-h-11 items-center gap-2 rounded-md px-3 text-sm font-semibold text-text-primary transition-colors hover:bg-surface-muted"
                            >
                                <span className="relative">
                                    <ShoppingCart size={21} strokeWidth={1.8} />

                                    <span className="absolute -right-2 -top-2 flex size-4 items-center justify-center rounded-full bg-brand-primary text-[10px] font-bold text-text-primary">
                                        0
                                    </span>
                                </span>
                                Cart
                            </a>
                        </div>
                    </div>

                    {/* Mobile / tablet */}
                    <div className="lg:hidden">
                        <div className="flex min-h-14 items-center justify-between gap-3">
                            <a
                                href="/"
                                aria-label="Dubai Lanka home"
                                className="text-lg font-bold tracking-tight"
                            >
                                Dubai
                                <span className="text-brand-secondary">
                                    Lanka
                                </span>
                            </a>

                            <div className="flex items-center gap-1">
                                <a
                                    href="#"
                                    aria-label="Saved items"
                                    className="flex size-10 items-center justify-center rounded-md text-text-secondary"
                                >
                                    <Heart size={20} strokeWidth={1.8} />
                                </a>

                                <a
                                    href="#"
                                    aria-label="Shopping cart"
                                    className="relative flex size-10 items-center justify-center rounded-md text-text-primary"
                                >
                                    <ShoppingCart size={21} strokeWidth={1.8} />

                                    <span className="absolute right-0.5 top-0.5 flex size-4 items-center justify-center rounded-full bg-brand-primary text-[9px] font-bold">
                                        0
                                    </span>
                                </a>
                            </div>
                        </div>

                        <div className="pb-2">
                            <LocationSelector />
                        </div>

                        <div className="pb-3">
                            <SearchBar mobile />
                        </div>
                    </div>
                </div>
            </div>

            <DesktopNavigation />
        </header>
    );
}
