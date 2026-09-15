import { ChevronRight } from "lucide-react";

import ProductCard, { ProductCardData } from "../marketplace/ProductCard";

const newArrivals: ProductCardData[] = [
    {
        id: 101,
        name: "Ceylon Herbal Tea Selection Box",
        storeName: "Ceylon Heritage Store",
        verified: true,
        location: "Dubai, UAE",
        currency: "AED",
        price: 32,
        purchaseMode: "direct",
        href: "#",
    },
    {
        id: 102,
        name: "Handcrafted Sri Lankan Coconut Bowl Set",
        storeName: "Island Craft Market",
        verified: true,
        location: "Colombo, Sri Lanka",
        currency: "LKR",
        price: 4800,
        purchaseMode: "preorder",
        href: "#",
    },
    {
        id: 103,
        name: "Premium Ceylon Spice Gift Collection",
        storeName: "Ceylon Spice Market",
        verified: true,
        location: "Colombo, Sri Lanka",
        currency: "LKR",
        price: 6250,
        purchaseMode: "preorder",
        href: "#",
    },
    {
        id: 104,
        name: "Sri Lankan Handmade Lifestyle Gift Set",
        storeName: "Island Gift Studio",
        location: "Dubai, UAE",
        purchaseMode: "price_on_request",
        href: "#",
    },
    {
        id: 105,
        name: "Traditional Sri Lankan Snack Selection",
        storeName: "Lanka Food Corner",
        verified: true,
        location: "Sharjah, UAE",
        currency: "AED",
        price: 28,
        purchaseMode: "direct",
        href: "#",
    },
];

export default function NewArrivals() {
    return (
        <section className="bg-surface-subtle">
            <div className="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
                <div className="flex items-end justify-between gap-4">
                    <div>
                        <p className="text-sm font-semibold text-brand-secondary">
                            Recently Added
                        </p>

                        <h2 className="mt-1 text-xl font-bold text-text-primary sm:text-2xl">
                            New Arrivals
                        </h2>

                        <p className="mt-1.5 hidden text-sm text-text-secondary sm:block">
                            Explore recently added products from Dubai Lanka
                            stores.
                        </p>
                    </div>

                    <a
                        href="#"
                        className="inline-flex shrink-0 items-center gap-1 text-sm font-semibold text-brand-secondary transition-colors hover:text-brand-secondary-hover"
                    >
                        View all
                        <ChevronRight size={16} strokeWidth={2} />
                    </a>
                </div>

                <div className="mt-6 hidden grid-cols-3 gap-3 md:grid lg:grid-cols-5">
                    {newArrivals.map((product) => (
                        <ProductCard key={product.id} product={product} />
                    ))}
                </div>

                <div className="-mx-4 mt-5 overflow-x-auto px-4 pb-2 md:hidden">
                    <div className="flex w-max gap-3">
                        {newArrivals.map((product) => (
                            <div
                                key={product.id}
                                className="w-[190px] shrink-0 sm:w-[220px]"
                            >
                                <ProductCard product={product} />
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </section>
    );
}
