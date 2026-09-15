import { BadgeCheck, Heart, MapPin } from "lucide-react";

export type ClassifiedPriceMode =
    | "fixed"
    | "negotiable"
    | "free"
    | "price_on_request"
    | "none";

export interface ClassifiedAttribute {
    label: string;
    value: string;
}

export interface ClassifiedCardData {
    id: number;
    title: string;
    category: string;
    location: string;
    postedAt: string;

    image?: string;

    priceMode: ClassifiedPriceMode;
    currency?: string;
    price?: number;
    priceSuffix?: string;

    sellerName?: string;
    verified?: boolean;

    attributes?: ClassifiedAttribute[];

    featured?: boolean;
    sponsored?: boolean;

    href?: string;
}

interface ClassifiedCardProps {
    classified: ClassifiedCardData;
}

function formatPrice(currency?: string, price?: number) {
    if (!currency || price === undefined) {
        return null;
    }

    return `${currency} ${price.toLocaleString("en-US")}`;
}

export default function ClassifiedCard({ classified }: ClassifiedCardProps) {
    const formattedPrice = formatPrice(classified.currency, classified.price);

    const priceDisplay = {
        fixed: formattedPrice,
        negotiable: formattedPrice
            ? `${formattedPrice} · Negotiable`
            : "Negotiable",
        free: "Free",
        price_on_request: "Price on Request",
        none: null,
    }[classified.priceMode];

    return (
        <article className="group overflow-hidden rounded-lg border border-border bg-surface">
            {/* Image */}
            <div className="relative aspect-[4/3] overflow-hidden bg-surface-muted">
                <a
                    href={classified.href ?? "#"}
                    aria-label={classified.title}
                    className="block size-full"
                >
                    {classified.image ? (
                        <img
                            src={classified.image}
                            alt={classified.title}
                            loading="lazy"
                            className="size-full object-cover transition-transform duration-300 group-hover:scale-[1.02]"
                        />
                    ) : (
                        <div className="flex size-full items-center justify-center text-sm text-text-muted">
                            Classified image
                        </div>
                    )}
                </a>

                <div className="absolute left-2 top-2 flex flex-col items-start gap-1">
                    {classified.sponsored && (
                        <span className="rounded bg-surface/95 px-2 py-1 text-[10px] font-semibold text-text-secondary">
                            Sponsored
                        </span>
                    )}

                    {!classified.sponsored && classified.featured && (
                        <span className="rounded bg-brand-primary px-2 py-1 text-[10px] font-bold text-text-primary">
                            Featured
                        </span>
                    )}
                </div>

                <button
                    type="button"
                    aria-label={`Save ${classified.title}`}
                    className="absolute right-2 top-2 flex size-9 items-center justify-center rounded-full border border-border bg-surface/95 text-text-secondary transition-colors hover:text-brand-secondary"
                >
                    <Heart size={18} strokeWidth={1.8} />
                </button>
            </div>

            <div className="p-3 sm:p-4">
                {/* Category */}
                <p className="text-[11px] font-bold uppercase tracking-wide text-brand-secondary">
                    {classified.category}
                </p>

                {/* Title */}
                <a href={classified.href ?? "#"} className="mt-1.5 block">
                    <h3 className="line-clamp-2 min-h-10 text-sm font-semibold leading-5 text-text-primary transition-colors group-hover:text-brand-secondary sm:text-[15px]">
                        {classified.title}
                    </h3>
                </a>

                {/* Price */}
                {priceDisplay && (
                    <div className="mt-2">
                        <span
                            className={[
                                "font-bold",
                                classified.priceMode === "price_on_request"
                                    ? "text-sm text-brand-secondary"
                                    : "text-base text-text-primary sm:text-lg",
                            ].join(" ")}
                        >
                            {priceDisplay}
                        </span>

                        {classified.priceSuffix && (
                            <span className="ml-1 text-xs font-medium text-text-muted">
                                {classified.priceSuffix}
                            </span>
                        )}
                    </div>
                )}

                {/* Seller */}
                {classified.sellerName && (
                    <div className="mt-2 flex min-w-0 items-center gap-1">
                        <span className="truncate text-xs font-medium text-text-secondary">
                            {classified.sellerName}
                        </span>

                        {classified.verified && (
                            <BadgeCheck
                                aria-label="Verified business"
                                size={14}
                                strokeWidth={2}
                                className="shrink-0 text-brand-secondary"
                            />
                        )}
                    </div>
                )}

                {/* Location */}
                <div className="mt-2 flex items-center gap-1 text-xs text-text-muted">
                    <MapPin size={13} strokeWidth={1.8} />

                    <span className="truncate">{classified.location}</span>
                </div>

                {/* Category-specific attributes */}
                {classified.attributes && classified.attributes.length > 0 && (
                    <div className="mt-3 flex flex-wrap gap-x-2 gap-y-1 text-xs text-text-secondary">
                        {classified.attributes.map((attribute, index) => (
                            <span
                                key={`${attribute.label}-${attribute.value}`}
                                className="inline-flex items-center"
                            >
                                {index > 0 && (
                                    <span className="mr-2 text-border-strong">
                                        •
                                    </span>
                                )}

                                {attribute.value}
                            </span>
                        ))}
                    </div>
                )}

                {/* Posted time */}
                <div className="mt-3 border-t border-border pt-3">
                    <span className="text-xs text-text-muted">
                        {classified.postedAt}
                    </span>
                </div>
            </div>
        </article>
    );
}
