import { router, usePage } from "@inertiajs/react";
import {
    ChevronDown,
    Heart,
    HelpCircle,
    LogOut,
    ShoppingCart,
    UserRound,
} from "lucide-react";
import { useEffect, useRef, useState } from "react";

import DesktopNavigation from "./DesktopNavigation";
import LocationSelector from "./LocationSelector";
import SearchBar from "./SearchBar";

type AuthUser = {
    id: number;
    name: string;
    email: string;
    status: string;
    suspension_reason?: string | null;
};

type SharedPageProps = {
    auth?: {
        user?: AuthUser | null;
    };
};

export default function PublicHeader() {
    const { props } = usePage<SharedPageProps>();
    const user = props.auth?.user ?? null;
    const [accountOpen, setAccountOpen] = useState(false);
    const accountRef = useRef<HTMLDivElement | null>(null);

    useEffect(() => {
        const closeOnOutsideClick = (event: MouseEvent) => {
            if (
                accountRef.current &&
                !accountRef.current.contains(event.target as Node)
            ) {
                setAccountOpen(false);
            }
        };

        document.addEventListener("mousedown", closeOnOutsideClick);
        return () => document.removeEventListener("mousedown", closeOnOutsideClick);
    }, []);

    const logout = () => {
        setAccountOpen(false);
        router.post("/logout");
    };

    return (
        <header className="sticky top-0 z-40 border-b border-border bg-surface">
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

            <div className="bg-surface">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div className="hidden min-h-20 items-center gap-6 lg:flex">
                        <a href="/" className="shrink-0" aria-label="Dubai Lanka home">
                            <div className="text-xl font-bold tracking-tight text-text-primary">
                                Dubai<span className="text-brand-secondary">Lanka</span>
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

                            {user ? (
                                <div ref={accountRef} className="relative">
                                    <button
                                        type="button"
                                        onClick={() => setAccountOpen((open) => !open)}
                                        className="flex min-h-11 items-center gap-2 rounded-md px-3 text-left text-sm font-medium text-text-secondary transition-colors hover:bg-surface-muted hover:text-text-primary"
                                        aria-expanded={accountOpen}
                                        aria-haspopup="menu"
                                    >
                                        <UserRound size={19} strokeWidth={1.8} />
                                        <span className="max-w-36 leading-tight">
                                            <span className="block text-xs text-text-muted">Account</span>
                                            <span className="block truncate font-semibold text-text-primary">
                                                {user.name}
                                            </span>
                                        </span>
                                        <ChevronDown
                                            size={15}
                                            className={`transition-transform ${accountOpen ? "rotate-180" : ""}`}
                                        />
                                    </button>

                                    {accountOpen && (
                                        <div
                                            role="menu"
                                            className="absolute right-0 top-full mt-2 w-64 overflow-hidden rounded-xl border border-border bg-surface shadow-lg"
                                        >
                                            <div className="border-b border-border px-4 py-3">
                                                <p className="truncate text-sm font-semibold text-text-primary">
                                                    {user.name}
                                                </p>
                                                <p className="mt-0.5 truncate text-xs text-text-muted">
                                                    {user.email}
                                                </p>
                                            </div>

                                            <div className="p-2">
                                                <button
                                                    type="button"
                                                    onClick={logout}
                                                    role="menuitem"
                                                    className="flex w-full items-center gap-2 rounded-lg px-3 py-2.5 text-left text-sm font-medium text-danger transition-colors hover:bg-surface-muted"
                                                >
                                                    <LogOut size={17} strokeWidth={1.8} />
                                                    Log out
                                                </button>
                                            </div>
                                        </div>
                                    )}
                                </div>
                            ) : (
                                <a
                                    href="/login"
                                    className="flex min-h-11 items-center gap-2 rounded-md px-3 text-sm font-medium text-text-secondary transition-colors hover:bg-surface-muted hover:text-text-primary"
                                >
                                    <UserRound size={19} strokeWidth={1.8} />
                                    <span className="leading-tight">
                                        <span className="block text-xs text-text-muted">Account</span>
                                        <span className="block font-semibold text-text-primary">
                                            Sign In / Register
                                        </span>
                                    </span>
                                </a>
                            )}

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

                    <div className="lg:hidden">
                        <div className="flex min-h-14 items-center justify-between gap-3">
                            <a href="/" aria-label="Dubai Lanka home" className="text-lg font-bold tracking-tight">
                                Dubai<span className="text-brand-secondary">Lanka</span>
                            </a>

                            <div className="flex items-center gap-1">
                                <a
                                    href="#"
                                    aria-label="Saved items"
                                    className="flex size-10 items-center justify-center rounded-md text-text-secondary"
                                >
                                    <Heart size={20} strokeWidth={1.8} />
                                </a>

                                {user ? (
                                    <button
                                        type="button"
                                        onClick={logout}
                                        aria-label={`Log out ${user.name}`}
                                        className="flex size-10 items-center justify-center rounded-md text-text-secondary"
                                    >
                                        <LogOut size={20} strokeWidth={1.8} />
                                    </button>
                                ) : (
                                    <a
                                        href="/login"
                                        aria-label="Sign in or register"
                                        className="flex size-10 items-center justify-center rounded-md text-text-secondary"
                                    >
                                        <UserRound size={20} strokeWidth={1.8} />
                                    </a>
                                )}

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
