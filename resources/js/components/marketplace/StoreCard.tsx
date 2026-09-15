import { ArrowRight, BadgeCheck, MapPin, Store } from "lucide-react";

export interface StoreCardData {
    id: number;
    name: string;
    category: string;
    location: string;
    description?: string;

    logo?: string;
    coverImage?: string;

    verified?: boolean;
    featured?: boolean;
    sponsored?: boolean;

    href?: string;
}

interface StoreCardProps {
    store: StoreCardData;
}

export default function StoreCard({ store }: StoreCardProps) {
    return (
        <article className="group overflow-hidden rounded-lg border border-border bg-surface">
            {/* Store visual */}
            <div className="relative aspect-[16/7] overflow-hidden bg-brand-secondary-soft">
                {store.coverImage ? (
                    <img
                        src={store.coverImage}
                        alt=""
                        loading="lazy"
                        className="size-full object-cover"
                    />
                ) : (
                    <div className="size-full bg-gradient-to-br from-brand-secondary-soft to-surface-muted" />
                )}

                <div className="absolute left-3 top-3 flex gap-1.5">
                    {store.sponsored && (
                        <span className="rounded bg-surface/95 px-2 py-1 text-[10px] font-semibold text-text-secondary">
                            Sponsored
                        </span>
                    )}

                    {!store.sponsored && store.featured && (
                        <span className="rounded bg-brand-primary px-2 py-1 text-[10px] font-bold text-text-primary">
                            Featured
                        </span>
                    )}
                </div>
            </div>

            <div className="relative px-4 pb-4">
                {/* Store logo */}
                <div className="-mt-7 flex size-14 items-center justify-center overflow-hidden rounded-lg border-2 border-surface bg-surface">
                    {store.logo ? (
                        <img
                            src={store.logo}
                            alt={`${store.name} logo`}
                            loading="lazy"
                            className="size-full object-cover"
                        />
                    ) : (
                        <Store
                            size={24}
                            strokeWidth={1.7}
                            className="text-brand-secondary"
                        />
                    )}
                </div>

                {/* Store identity */}
                <div className="mt-3">
                    <div className="flex min-w-0 items-center gap-1.5">
                        <a href={store.href ?? "#"} className="min-w-0">
                            <h3 className="truncate text-base font-bold text-text-primary transition-colors group-hover:text-brand-secondary">
                                {store.name}
                            </h3>
                        </a>

                        {store.verified && (
                            <BadgeCheck
                                aria-label="Verified business"
                                size={16}
                                strokeWidth={2}
                                className="shrink-0 text-brand-secondary"
                            />
                        )}
                    </div>

                    <p className="mt-1 text-xs font-medium text-text-secondary">
                        {store.category}
                    </p>
                </div>

                {/* Location */}
                <div className="mt-2 flex items-center gap-1 text-xs text-text-muted">
                    <MapPin size={13} strokeWidth={1.8} />

                    <span className="truncate">{store.location}</span>
                </div>

                {/* Description */}
                {store.description && (
                    <p className="mt-3 line-clamp-2 min-h-10 text-sm leading-5 text-text-secondary">
                        {store.description}
                    </p>
                )}

                <a
                    href={store.href ?? "#"}
                    className="mt-4 inline-flex items-center gap-1.5 text-sm font-bold text-brand-secondary transition-colors hover:text-brand-secondary-hover"
                >
                    Visit Store
                    <ArrowRight size={15} strokeWidth={2} />
                </a>
            </div>
        </article>
    );
}
