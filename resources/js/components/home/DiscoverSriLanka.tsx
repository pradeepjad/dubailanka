import {
    ArrowRight,
    BadgeCheck,
    Building2,
    MapPin,
    Package,
} from "lucide-react";

interface SriLankaDiscoveryItem {
    id: number;
    type: "product" | "business";
    title: string;
    subtitle: string;
    location: string;
    verified?: boolean;
    badge?: string;
    href?: string;
}

const discoveryItems: SriLankaDiscoveryItem[] = [
    {
        id: 301,
        type: "product",
        title: "Premium Ceylon Cinnamon",
        subtitle: "Ceylon Spice Market",
        location: "Colombo, Sri Lanka",
        verified: true,
        badge: "Pre-order",
        href: "#",
    },
    {
        id: 302,
        type: "business",
        title: "Island Craft Collective",
        subtitle: "Handmade & Lifestyle",
        location: "Galle, Sri Lanka",
        verified: true,
        badge: "Seeking UAE Buyers",
        href: "#",
    },
    {
        id: 303,
        type: "product",
        title: "Sri Lankan Artisan Gift Collection",
        subtitle: "Ceylon Makers",
        location: "Kandy, Sri Lanka",
        badge: "Contact Seller",
        href: "#",
    },
];

export default function DiscoverSriLanka() {
    return (
        <section className="border-y border-border bg-surface">
            <div className="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
                <div className="overflow-hidden rounded-xl border border-border bg-surface-subtle">
                    <div className="grid lg:grid-cols-[1.15fr_1.85fr]">
                        {/* Introduction */}
                        <div className="flex flex-col justify-center border-b border-border p-6 sm:p-8 lg:border-b-0 lg:border-r lg:p-10">
                            <div className="flex size-11 items-center justify-center rounded-full bg-brand-primary-soft text-warning">
                                <span aria-hidden="true" className="text-xl">
                                    🇱🇰
                                </span>
                            </div>

                            <p className="mt-5 text-xs font-bold uppercase tracking-wider text-brand-secondary">
                                Sri Lanka → UAE
                            </p>

                            <h2 className="mt-2 text-2xl font-bold leading-tight text-text-primary sm:text-3xl">
                                Discover from Sri Lanka
                            </h2>

                            <p className="mt-4 max-w-md text-sm leading-6 text-text-secondary sm:text-base">
                                Explore products, makers and businesses in Sri
                                Lanka looking to connect with customers and
                                opportunities in the UAE.
                            </p>

                            <div className="mt-6">
                                <a
                                    href="#"
                                    className="inline-flex min-h-10 items-center gap-2 rounded-md bg-brand-secondary px-4 text-sm font-bold text-text-inverse transition-colors hover:bg-brand-secondary-hover"
                                >
                                    Explore Sri Lanka
                                    <ArrowRight size={16} strokeWidth={2} />
                                </a>
                            </div>

                            <p className="mt-5 text-xs leading-5 text-text-muted">
                                Availability and purchasing options vary by
                                seller and listing.
                            </p>
                        </div>

                        {/* Discovery cards */}
                        <div className="min-w-0 p-4 sm:p-6 lg:p-8">
                            <div className="-mx-4 overflow-x-auto px-4 pb-1 sm:mx-0 sm:px-0">
                                <div className="flex w-max gap-3 sm:grid sm:w-auto sm:grid-cols-3">
                                    {discoveryItems.map((item) => {
                                        const Icon =
                                            item.type === "product"
                                                ? Package
                                                : Building2;

                                        return (
                                            <a
                                                key={item.id}
                                                href={item.href ?? "#"}
                                                className="group w-[220px] shrink-0 overflow-hidden rounded-lg border border-border bg-surface transition-colors hover:border-border-strong sm:w-auto"
                                            >
                                                {/* Temporary visual */}
                                                <div className="flex aspect-[4/3] items-center justify-center bg-brand-secondary-soft">
                                                    <div className="flex size-14 items-center justify-center rounded-full bg-surface text-brand-secondary">
                                                        <Icon
                                                            size={25}
                                                            strokeWidth={1.7}
                                                        />
                                                    </div>
                                                </div>

                                                <div className="p-4">
                                                    <div className="flex items-start justify-between gap-2">
                                                        <span className="rounded bg-brand-primary-soft px-2 py-1 text-[10px] font-bold text-warning">
                                                            {item.badge}
                                                        </span>

                                                        <span className="text-[10px] font-semibold uppercase tracking-wide text-text-muted">
                                                            {item.type}
                                                        </span>
                                                    </div>

                                                    <h3 className="mt-3 line-clamp-2 min-h-10 text-sm font-bold leading-5 text-text-primary transition-colors group-hover:text-brand-secondary">
                                                        {item.title}
                                                    </h3>

                                                    <div className="mt-2 flex min-w-0 items-center gap-1">
                                                        <span className="truncate text-xs font-medium text-text-secondary">
                                                            {item.subtitle}
                                                        </span>

                                                        {item.verified && (
                                                            <BadgeCheck
                                                                aria-label="Verified business"
                                                                size={14}
                                                                strokeWidth={2}
                                                                className="shrink-0 text-brand-secondary"
                                                            />
                                                        )}
                                                    </div>

                                                    <div className="mt-2 flex items-center gap-1 text-xs text-text-muted">
                                                        <MapPin
                                                            size={13}
                                                            strokeWidth={1.8}
                                                        />

                                                        <span className="truncate">
                                                            {item.location}
                                                        </span>
                                                    </div>

                                                    <div className="mt-4 inline-flex items-center gap-1 text-xs font-bold text-brand-secondary">
                                                        View details
                                                        <ArrowRight
                                                            size={14}
                                                            strokeWidth={2}
                                                        />
                                                    </div>
                                                </div>
                                            </a>
                                        );
                                    })}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
