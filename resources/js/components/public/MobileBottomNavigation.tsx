import { Compass, Heart, Home, ShoppingCart, UserRound } from "lucide-react";

const navigation = [
    {
        label: "Home",
        icon: Home,
        href: "/",
        active: true,
    },
    {
        label: "Explore",
        icon: Compass,
        href: "#",
    },
    {
        label: "Saved",
        icon: Heart,
        href: "#",
    },
    {
        label: "Cart",
        icon: ShoppingCart,
        href: "#",
        badge: 0,
    },
    {
        label: "Account",
        icon: UserRound,
        href: "#",
    },
];

export default function MobileBottomNavigation() {
    return (
        <nav
            aria-label="Mobile navigation"
            className="fixed inset-x-0 bottom-0 z-50 border-t border-border bg-surface md:hidden"
        >
            <div className="grid h-16 grid-cols-5">
                {navigation.map((item) => {
                    const Icon = item.icon;

                    return (
                        <a
                            key={item.label}
                            href={item.href}
                            aria-current={item.active ? "page" : undefined}
                            className={[
                                "relative flex min-w-0 flex-col items-center justify-center gap-1 px-1 text-[11px] font-medium",
                                item.active
                                    ? "text-brand-secondary"
                                    : "text-text-muted",
                            ].join(" ")}
                        >
                            <span className="relative">
                                <Icon
                                    size={20}
                                    strokeWidth={item.active ? 2.2 : 1.8}
                                />

                                {item.badge !== undefined && item.badge > 0 && (
                                    <span className="absolute -right-2.5 -top-2 flex min-w-4 items-center justify-center rounded-full bg-brand-primary px-1 text-[9px] font-bold leading-4 text-text-primary">
                                        {item.badge}
                                    </span>
                                )}
                            </span>

                            <span className="truncate">{item.label}</span>
                        </a>
                    );
                })}
            </div>
        </nav>
    );
}
