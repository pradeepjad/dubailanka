import { BadgeCheck, Heart, MapPin } from "lucide-react";

export type ProductPurchaseMode =
    | "direct"
    | "inquiry"
    | "price_on_request"
    | "preorder";

export interface ProductCardData {
    id: number;
    name: string;
    storeName: string;
    verified?: boolean;
    location: string;

    image?: string;

    currency?: string;
    price?: number;
    oldPrice?: number;

    purchaseMode: ProductPurchaseMode;

    featured?: boolean;
    sponsored?: boolean;

    href?: string;
}

interface ProductCardProps {
    product: ProductCardData;
}

function formatPrice(currency?: string, price?: number) {
    if (!currency || price === undefined) {
        return null;
    }

    return `${currency} ${price.toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    })}`;
}

export default function ProductCard({ product }: ProductCardProps) {
    const price = formatPrice(product.currency, product.price);

    const oldPrice = formatPrice(product.currency, product.oldPrice);

    const commercialLabel = {
        direct: null,
        inquiry: "Contact Seller",
        price_on_request: "Price on Request",
        preorder: "Pre-order",
    }[product.purchaseMode];

    return (
        <article className="group overflow-hidden rounded-lg border border-border bg-surface">
            {/* Product image */}
            <div className="relative aspect-square overflow-hidden bg-surface-muted">
                <a
                    href={product.href ?? "#"}
                    aria-label={product.name}
                    className="block size-full"
                >
                    {product.image ? (
                        <img
                            src={product.image}
                            alt={product.name}
                            loading="lazy"
                            className="size-full object-cover transition-transform duration-300 group-hover:scale-[1.02]"
                        />
                    ) : (
                        <div className="flex size-full items-center justify-center text-sm text-text-muted">
                            Product image
                        </div>
                    )}
                </a>

                {/* Promotion labels */}
                <div className="absolute left-2 top-2 flex flex-col items-start gap-1">
                    {product.sponsored && (
                        <span className="rounded bg-surface/95 px-2 py-1 text-[10px] font-semibold text-text-secondary">
                            Sponsored
                        </span>
                    )}

                    {!product.sponsored && product.featured && (
                        <span className="rounded bg-brand-primary px-2 py-1 text-[10px] font-bold text-text-primary">
                            Featured
                        </span>
                    )}

                    {product.purchaseMode === "preorder" && (
                        <span className="rounded bg-brand-secondary px-2 py-1 text-[10px] font-bold text-text-inverse">
                            Pre-order
                        </span>
                    )}
                </div>

                {/* Save */}
                <button
                    type="button"
                    aria-label={`Save ${product.name}`}
                    className="absolute right-2 top-2 flex size-9 items-center justify-center rounded-full border border-border bg-surface/95 text-text-secondary transition-colors hover:text-brand-secondary"
                >
                    <Heart size={18} strokeWidth={1.8} />
                </button>
            </div>

            {/* Product information */}
            <div className="p-3 sm:p-4">
                <a href={product.href ?? "#"} className="block">
                    <h3 className="line-clamp-2 min-h-10 text-sm font-semibold leading-5 text-text-primary transition-colors group-hover:text-brand-secondary sm:text-[15px]">
                        {product.name}
                    </h3>
                </a>

                {/* Store */}
                <div className="mt-2 flex min-w-0 items-center gap-1">
                    <span className="truncate text-xs font-medium text-text-secondary">
                        {product.storeName}
                    </span>

                    {product.verified && (
                        <BadgeCheck
                            aria-label="Verified business"
                            size={15}
                            strokeWidth={2}
                            className="shrink-0 text-brand-secondary"
                        />
                    )}
                </div>

                {/* Location */}
                <div className="mt-1.5 flex items-center gap-1 text-xs text-text-muted">
                    <MapPin size={13} strokeWidth={1.8} />

                    <span className="truncate">{product.location}</span>
                </div>

                {/* Commercial information */}
                <div className="mt-3">
                    {product.purchaseMode === "price_on_request" ? (
                        <p className="text-sm font-bold text-brand-secondary">
                            Price on Request
                        </p>
                    ) : (
                        <>
                            {price && (
                                <div className="flex flex-wrap items-baseline gap-x-2 gap-y-1">
                                    <span className="text-base font-bold text-text-primary sm:text-lg">
                                        {price}
                                    </span>

                                    {oldPrice && (
                                        <span className="text-xs text-text-muted line-through">
                                            {oldPrice}
                                        </span>
                                    )}
                                </div>
                            )}

                            {commercialLabel && (
                                <p
                                    className={[
                                        "mt-1 text-xs font-semibold",
                                        product.purchaseMode === "preorder"
                                            ? "text-brand-secondary"
                                            : "text-text-secondary",
                                    ].join(" ")}
                                >
                                    {commercialLabel}
                                </p>
                            )}
                        </>
                    )}
                </div>
            </div>
        </article>
    );
}
