import { ChevronRight } from "lucide-react";

import StoreCard, { StoreCardData } from "../marketplace/StoreCard";

const featuredStores: StoreCardData[] = [
    {
        id: 1,
        name: "Ceylon Heritage Store",
        category: "Food & Grocery",
        location: "Dubai, UAE",
        description:
            "Sri Lankan tea, food and specialty products for customers in the UAE.",
        verified: true,
        featured: true,
        href: "#",
    },
    {
        id: 2,
        name: "Lanka Batik House",
        category: "Fashion & Lifestyle",
        location: "Dubai, UAE",
        description:
            "Traditional and contemporary Sri Lankan batik fashion and accessories.",
        verified: true,
        href: "#",
    },
    {
        id: 3,
        name: "Ceylon Spice Market",
        category: "Food & Grocery",
        location: "Colombo, Sri Lanka",
        description:
            "Ceylon spices and specialty products available for UAE customers and businesses.",
        verified: true,
        href: "#",
    },
    {
        id: 4,
        name: "Island Gift Studio",
        category: "Gifts & Handmade",
        location: "Colombo, Sri Lanka",
        description:
            "Custom gifts and Sri Lankan handmade products for personal and corporate orders.",
        sponsored: true,
        href: "#",
    },
];

export default function FeaturedStores() {
    return (
        <section className="bg-surface">
            <div className="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
                <div className="flex items-end justify-between gap-4">
                    <div>
                        <p className="text-sm font-semibold text-brand-secondary">
                            Businesses & Stores
                        </p>

                        <h2 className="mt-1 text-xl font-bold text-text-primary sm:text-2xl">
                            Featured Stores
                        </h2>

                        <p className="mt-1.5 hidden max-w-2xl text-sm text-text-secondary sm:block">
                            Discover businesses and sellers connecting Sri Lanka
                            and the UAE.
                        </p>
                    </div>

                    <a
                        href="#"
                        className="inline-flex shrink-0 items-center gap-1 text-sm font-semibold text-brand-secondary transition-colors hover:text-brand-secondary-hover"
                    >
                        View all stores
                        <ChevronRight size={16} strokeWidth={2} />
                    </a>
                </div>

                {/* Desktop */}
                <div className="mt-6 hidden grid-cols-2 gap-3 md:grid lg:grid-cols-4">
                    {featuredStores.map((store) => (
                        <StoreCard key={store.id} store={store} />
                    ))}
                </div>

                {/* Mobile */}
                <div className="-mx-4 mt-5 overflow-x-auto px-4 pb-2 md:hidden">
                    <div className="flex w-max gap-3">
                        {featuredStores.map((store) => (
                            <div key={store.id} className="w-[260px] shrink-0">
                                <StoreCard store={store} />
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </section>
    );
}
