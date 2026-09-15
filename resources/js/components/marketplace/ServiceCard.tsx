import { BadgeCheck, Heart, MapPin, Star, Wrench } from "lucide-react";

export type ServicePricingMode =
    | "fixed"
    | "starting_from"
    | "price_on_request"
    | "contact_provider";

export interface ServiceCardData {
    id: number;
    title: string;

    providerName: string;
    verified?: boolean;

    category: string;
    serviceArea: string;

    image?: string;

    pricingMode: ServicePricingMode;
    currency?: string;
    price?: number;

    rating?: number;
    reviewCount?: number;

    featured?: boolean;
    sponsored?: boolean;

    href?: string;
}

interface ServiceCardProps {
    service: ServiceCardData;
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

export default function ServiceCard({ service }: ServiceCardProps) {
    const price = formatPrice(service.currency, service.price);

    const priceDisplay = {
        fixed: price,
        starting_from: price ? `From ${price}` : null,
        price_on_request: "Price on Request",
        contact_provider: "Contact Provider",
    }[service.pricingMode];

    return (
        <article className="group overflow-hidden rounded-lg border border-border bg-surface">
            {/* Service image */}
            <div className="relative aspect-[4/3] overflow-hidden bg-surface-muted">
                <a
                    href={service.href ?? "#"}
                    aria-label={service.title}
                    className="block size-full"
                >
                    {service.image ? (
                        <img
                            src={service.image}
                            alt={service.title}
                            loading="lazy"
                            className="size-full object-cover transition-transform duration-300 group-hover:scale-[1.02]"
                        />
                    ) : (
                        <div className="flex size-full items-center justify-center">
                            <div className="flex size-14 items-center justify-center rounded-full bg-brand-secondary-soft text-brand-secondary">
                                <Wrench size={25} strokeWidth={1.7} />
                            </div>
                        </div>
                    )}
                </a>

                <div className="absolute left-2 top-2 flex flex-col items-start gap-1">
                    {service.sponsored && (
                        <span className="rounded bg-surface/95 px-2 py-1 text-[10px] font-semibold text-text-secondary">
                            Sponsored
                        </span>
                    )}

                    {!service.sponsored && service.featured && (
                        <span className="rounded bg-brand-primary px-2 py-1 text-[10px] font-bold text-text-primary">
                            Featured
                        </span>
                    )}
                </div>

                <button
                    type="button"
                    aria-label={`Save ${service.title}`}
                    className="absolute right-2 top-2 flex size-9 items-center justify-center rounded-full border border-border bg-surface/95 text-text-secondary transition-colors hover:text-brand-secondary"
                >
                    <Heart size={18} strokeWidth={1.8} />
                </button>
            </div>

            <div className="p-3 sm:p-4">
                {/* Category */}
                <p className="truncate text-xs font-medium text-text-muted">
                    {service.category}
                </p>

                {/* Service */}
                <a href={service.href ?? "#"} className="mt-1 block">
                    <h3 className="line-clamp-2 min-h-10 text-sm font-semibold leading-5 text-text-primary transition-colors group-hover:text-brand-secondary sm:text-[15px]">
                        {service.title}
                    </h3>
                </a>

                {/* Provider */}
                <div className="mt-2 flex min-w-0 items-center gap-1">
                    <span className="truncate text-xs font-medium text-text-secondary">
                        {service.providerName}
                    </span>

                    {service.verified && (
                        <BadgeCheck
                            aria-label="Verified business"
                            size={15}
                            strokeWidth={2}
                            className="shrink-0 text-brand-secondary"
                        />
                    )}
                </div>

                {/* Service area */}
                <div className="mt-1.5 flex items-center gap-1 text-xs text-text-muted">
                    <MapPin size={13} strokeWidth={1.8} />

                    <span className="truncate">{service.serviceArea}</span>
                </div>

                {/* Rating */}
                {service.rating !== undefined &&
                    service.reviewCount !== undefined && (
                        <div className="mt-2 flex items-center gap-1.5">
                            <div className="flex items-center gap-1 font-semibold text-text-primary">
                                <Star
                                    size={14}
                                    strokeWidth={2}
                                    className="fill-brand-primary text-brand-primary"
                                />

                                <span className="text-xs">
                                    {service.rating.toFixed(1)}
                                </span>
                            </div>

                            <span className="text-xs text-text-muted">
                                ({service.reviewCount}{" "}
                                {service.reviewCount === 1
                                    ? "review"
                                    : "reviews"}
                                )
                            </span>
                        </div>
                    )}

                {/* Pricing */}
                <div className="mt-3 min-h-7">
                    {priceDisplay && (
                        <p
                            className={[
                                "font-bold",
                                service.pricingMode === "fixed" ||
                                service.pricingMode === "starting_from"
                                    ? "text-base text-text-primary sm:text-lg"
                                    : "text-sm text-brand-secondary",
                            ].join(" ")}
                        >
                            {priceDisplay}
                        </p>
                    )}
                </div>
            </div>
        </article>
    );
}
