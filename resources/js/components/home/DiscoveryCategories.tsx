import {
    BriefcaseBusiness,
    Building2,
    CarFront,
    ChevronRight,
    House,
    Laptop,
    Package,
    Shirt,
    ShoppingBasket,
    Sparkles,
} from "lucide-react";

const categories = [
    {
        name: "Food & Grocery",
        icon: ShoppingBasket,
        href: "#",
    },
    {
        name: "Fashion",
        icon: Shirt,
        href: "#",
    },
    {
        name: "Beauty & Wellness",
        icon: Sparkles,
        href: "#",
    },
    {
        name: "Home & Living",
        icon: House,
        href: "#",
    },
    {
        name: "Electronics",
        icon: Laptop,
        href: "#",
    },
    {
        name: "Business Services",
        icon: BriefcaseBusiness,
        href: "#",
    },
    {
        name: "Vehicles",
        icon: CarFront,
        href: "#",
    },
    {
        name: "Property",
        icon: Building2,
        href: "#",
    },
    {
        name: "Other",
        icon: Package,
        href: "#",
    },
];

export default function DiscoveryCategories() {
    return (
        <section className="border-b border-border bg-surface">
            <div className="mx-auto max-w-7xl px-4 py-7 sm:px-6 sm:py-8 lg:px-8">
                <div className="flex items-end justify-between gap-4">
                    <div>
                        <p className="text-sm font-semibold text-brand-secondary">
                            Discover
                        </p>

                        <h2 className="mt-1 text-xl font-bold text-text-primary sm:text-2xl">
                            Explore by category
                        </h2>
                    </div>

                    <a
                        href="#"
                        className="hidden items-center gap-1 text-sm font-semibold text-brand-secondary transition-colors hover:text-brand-secondary-hover sm:inline-flex"
                    >
                        View all categories
                        <ChevronRight size={16} strokeWidth={2} />
                    </a>
                </div>

                {/* Desktop / tablet */}
                <div className="mt-6 hidden grid-cols-3 gap-3 sm:grid md:grid-cols-5 lg:grid-cols-9">
                    {categories.map((category) => {
                        const Icon = category.icon;

                        return (
                            <a
                                key={category.name}
                                href={category.href}
                                className="group flex min-h-32 flex-col items-center justify-center rounded-lg border border-border bg-surface px-3 py-4 text-center transition-colors hover:border-border-strong hover:bg-surface-subtle"
                            >
                                <div className="flex size-11 items-center justify-center rounded-full bg-brand-secondary-soft text-brand-secondary transition-colors group-hover:bg-brand-secondary group-hover:text-text-inverse">
                                    <Icon size={21} strokeWidth={1.8} />
                                </div>

                                <span className="mt-3 text-sm font-semibold leading-5 text-text-primary">
                                    {category.name}
                                </span>
                            </a>
                        );
                    })}
                </div>

                {/* Mobile horizontal discovery */}
                <div className="-mx-4 mt-5 overflow-x-auto px-4 pb-1 sm:hidden">
                    <div className="flex w-max gap-3">
                        {categories.map((category) => {
                            const Icon = category.icon;

                            return (
                                <a
                                    key={category.name}
                                    href={category.href}
                                    className="flex w-[92px] shrink-0 flex-col items-center text-center"
                                >
                                    <div className="flex size-14 items-center justify-center rounded-full border border-border bg-brand-secondary-soft text-brand-secondary">
                                        <Icon size={23} strokeWidth={1.8} />
                                    </div>

                                    <span className="mt-2 text-xs font-semibold leading-4 text-text-primary">
                                        {category.name}
                                    </span>
                                </a>
                            );
                        })}
                    </div>
                </div>

                <a
                    href="#"
                    className="mt-5 inline-flex items-center gap-1 text-sm font-semibold text-brand-secondary sm:hidden"
                >
                    View all categories
                    <ChevronRight size={16} strokeWidth={2} />
                </a>
            </div>
        </section>
    );
}
