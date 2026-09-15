import { ChevronRight } from "lucide-react";

import ProductCard, { ProductCardData } from "../marketplace/ProductCard";

const featuredProducts: ProductCardData[] = [
    {
        id: 1,
        name: "Ceylon Premium Black Tea Gift Collection",
        storeName: "Ceylon Heritage Store",
        verified: true,
        location: "Dubai, UAE",
        currency: "AED",
        price: 45,
        oldPrice: 55,
        purchaseMode: "direct",
        featured: true,
        href: "#",
    },
    {
        id: 2,
        name: "Traditional Sri Lankan Handmade Batik Shirt",
        storeName: "Lanka Batik House",
        verified: true,
        location: "Dubai, UAE",
        currency: "AED",
        price: 89,
        purchaseMode: "direct",
        href: "#",
    },
    {
        id: 3,
        name: "Pure Ceylon Cinnamon Premium Pack",
        storeName: "Ceylon Spice Market",
        verified: true,
        location: "Colombo, Sri Lanka",
        currency: "LKR",
        price: 3500,
        purchaseMode: "preorder",
        href: "#",
    },
    {
        id: 4,
        name: "Custom Corporate Gift Box from Sri Lanka",
        storeName: "Island Gift Studio",
        verified: false,
        location: "Colombo, Sri Lanka",
        purchaseMode: "price_on_request",
        featured: true,
        href: "#",
    },
    {
        id: 5,
        name: "Sri Lankan Natural Coconut Products Collection",
        storeName: "Coco Lanka",
        verified: true,
        location: "Sharjah, UAE",
        currency: "AED",
        price: 38,
        purchaseMode: "direct",
        sponsored: true,
        href: "#",
    },
];

export default function FeaturedProducts() {
    return (
        <section className="bg-surface-subtle">
            <div className="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
                <div className="flex items-end justify-between gap-4">
                    <div>
                        <p className="text-sm font-semibold text-brand-secondary">
                            Marketplace
                        </p>

                        <h2 className="mt-1 text-xl font-bold text-text-primary sm:text-2xl">
                            Featured Products
                        </h2>

                        <p className="mt-1.5 hidden text-sm text-text-secondary sm:block">
                            Discover selected products from businesses on Dubai
                            Lanka.
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

                {/* Desktop */}
                <div className="mt-6 hidden grid-cols-3 gap-3 md:grid lg:grid-cols-5">
                    {featuredProducts.map((product) => (
                        <ProductCard key={product.id} product={product} />
                    ))}
                </div>

                {/* Mobile */}
                <div className="-mx-4 mt-5 overflow-x-auto px-4 pb-2 md:hidden">
                    <div className="flex w-max gap-3">
                        {featuredProducts.map((product) => (
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
