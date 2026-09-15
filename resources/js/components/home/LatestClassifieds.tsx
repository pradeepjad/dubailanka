import { ChevronRight } from "lucide-react";

import ClassifiedCard, {
    ClassifiedCardData,
} from "../marketplace/ClassifiedCard";

const latestClassifieds: ClassifiedCardData[] = [
    {
        id: 401,
        title: "Toyota Corolla 2022 GCC",
        category: "Vehicles",
        location: "Dubai, UAE",
        postedAt: "2 hours ago",
        priceMode: "negotiable",
        currency: "AED",
        price: 48000,
        attributes: [
            {
                label: "year",
                value: "2022",
            },
            {
                label: "transmission",
                value: "Automatic",
            },
            {
                label: "mileage",
                value: "45,000 km",
            },
        ],
        featured: true,
        href: "#",
    },
    {
        id: 402,
        title: "Studio Apartment for Rent",
        category: "Property",
        location: "Dubai Marina, Dubai",
        postedAt: "Today",
        priceMode: "fixed",
        currency: "AED",
        price: 42000,
        priceSuffix: "/ year",
        attributes: [
            {
                label: "bedrooms",
                value: "Studio",
            },
            {
                label: "bathrooms",
                value: "1 Bath",
            },
            {
                label: "size",
                value: "520 sq.ft",
            },
        ],
        href: "#",
    },
    {
        id: 403,
        title: "Sales Executive",
        category: "Jobs",
        location: "Dubai, UAE",
        postedAt: "1 day ago",
        priceMode: "none",
        sellerName: "Lanka Trading UAE",
        verified: true,
        attributes: [
            {
                label: "employment",
                value: "Full Time",
            },
            {
                label: "salary",
                value: "AED 4,000–5,000",
            },
        ],
        href: "#",
    },
    {
        id: 404,
        title: "Restaurant Equipment for Sale",
        category: "Business Equipment",
        location: "Sharjah, UAE",
        postedAt: "1 day ago",
        priceMode: "price_on_request",
        sellerName: "Commercial Kitchen UAE",
        verified: true,
        sponsored: true,
        href: "#",
    },
];

export default function LatestClassifieds() {
    return (
        <section className="bg-surface-subtle">
            <div className="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
                <div className="flex items-end justify-between gap-4">
                    <div>
                        <p className="text-sm font-semibold text-brand-secondary">
                            Classifieds
                        </p>

                        <h2 className="mt-1 text-xl font-bold text-text-primary sm:text-2xl">
                            Latest Classifieds
                        </h2>

                        <p className="mt-1.5 hidden max-w-2xl text-sm text-text-secondary sm:block">
                            Browse recently published opportunities and listings
                            across Dubai Lanka.
                        </p>
                    </div>

                    <a
                        href="#"
                        className="inline-flex shrink-0 items-center gap-1 text-sm font-semibold text-brand-secondary transition-colors hover:text-brand-secondary-hover"
                    >
                        View all classifieds
                        <ChevronRight size={16} strokeWidth={2} />
                    </a>
                </div>

                {/* Desktop */}
                <div className="mt-6 hidden grid-cols-2 gap-3 md:grid lg:grid-cols-4">
                    {latestClassifieds.map((classified) => (
                        <ClassifiedCard
                            key={classified.id}
                            classified={classified}
                        />
                    ))}
                </div>

                {/* Mobile */}
                <div className="-mx-4 mt-5 overflow-x-auto px-4 pb-2 md:hidden">
                    <div className="flex w-max gap-3">
                        {latestClassifieds.map((classified) => (
                            <div
                                key={classified.id}
                                className="w-[240px] shrink-0"
                            >
                                <ClassifiedCard classified={classified} />
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </section>
    );
}
