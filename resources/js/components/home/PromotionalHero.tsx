import { ArrowRight, ChevronLeft, ChevronRight } from "lucide-react";

interface Promotion {
    id: number;
    eyebrow?: string;
    title: string;
    description?: string;
    ctaLabel?: string;
    href?: string;
    image?: string;
}

const mainPromotion: Promotion = {
    id: 1,
    eyebrow: "Discover Dubai Lanka",
    title: "Sri Lankan products, businesses & services in the UAE",
    description:
        "Explore trusted businesses, products and opportunities connecting Sri Lanka and the UAE.",
    ctaLabel: "Explore Marketplace",
    href: "#",
};

const sidePromotions: Promotion[] = [
    {
        id: 2,
        eyebrow: "Discover",
        title: "Explore Sri Lankan businesses",
        ctaLabel: "View Stores",
        href: "#",
    },
    {
        id: 3,
        eyebrow: "For Sellers",
        title: "Grow your business with Dubai Lanka",
        ctaLabel: "Sell on Dubai Lanka",
        href: "#",
    },
];

export default function PromotionalHero() {
    return (
        <section className="bg-surface py-4 sm:py-5 lg:py-6">
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div className="grid gap-3 lg:grid-cols-3">
                    {/* Main promotion */}
                    <article className="relative min-h-[300px] overflow-hidden rounded-lg border border-border bg-brand-secondary-soft lg:col-span-2 lg:min-h-[360px]">
                        <div className="relative z-10 flex h-full max-w-xl flex-col justify-center p-6 sm:p-8 lg:p-10">
                            <p className="text-xs font-bold uppercase tracking-wider text-brand-secondary">
                                {mainPromotion.eyebrow}
                            </p>

                            <h1 className="mt-3 text-3xl font-bold leading-tight text-text-primary sm:text-4xl">
                                {mainPromotion.title}
                            </h1>

                            <p className="mt-4 max-w-lg text-sm leading-6 text-text-secondary sm:text-base">
                                {mainPromotion.description}
                            </p>

                            <div className="mt-6">
                                <a
                                    href={mainPromotion.href}
                                    className="inline-flex min-h-11 items-center gap-2 rounded-md bg-brand-primary px-5 text-sm font-bold text-text-primary transition-colors hover:bg-brand-primary-hover"
                                >
                                    {mainPromotion.ctaLabel}

                                    <ArrowRight size={17} strokeWidth={2} />
                                </a>
                            </div>
                        </div>

                        {/* Temporary visual area.
                            Later this uses the admin-managed
                            desktop/mobile promotional image. */}
                        <div className="pointer-events-none absolute bottom-0 right-0 hidden h-full w-[42%] items-end justify-end lg:flex">
                            <div className="mb-8 mr-8 flex size-52 items-center justify-center rounded-full border border-brand-secondary/10 bg-surface/50">
                                <div className="size-36 rounded-full bg-brand-secondary/10" />
                            </div>
                        </div>

                        {/* Slider controls prepared for multiple
                            active backend promotions later. */}
                        <div className="absolute bottom-4 right-4 z-20 flex gap-1">
                            <button
                                type="button"
                                aria-label="Previous promotion"
                                className="flex size-9 items-center justify-center rounded-md border border-border bg-surface/90 text-text-secondary transition-colors hover:text-text-primary"
                            >
                                <ChevronLeft size={17} />
                            </button>

                            <button
                                type="button"
                                aria-label="Next promotion"
                                className="flex size-9 items-center justify-center rounded-md border border-border bg-surface/90 text-text-secondary transition-colors hover:text-text-primary"
                            >
                                <ChevronRight size={17} />
                            </button>
                        </div>
                    </article>

                    {/* Side promotions */}
                    <div className="grid grid-cols-2 gap-3 lg:grid-cols-1">
                        {sidePromotions.map((promotion, index) => (
                            <article
                                key={promotion.id}
                                className={[
                                    "flex min-h-40 flex-col justify-between rounded-lg border border-border p-5 sm:p-6",
                                    index === 0
                                        ? "bg-brand-primary-soft"
                                        : "bg-surface-subtle",
                                ].join(" ")}
                            >
                                <div>
                                    <p
                                        className={[
                                            "text-xs font-bold uppercase tracking-wider",
                                            index === 0
                                                ? "text-warning"
                                                : "text-brand-secondary",
                                        ].join(" ")}
                                    >
                                        {promotion.eyebrow}
                                    </p>

                                    <h2 className="mt-2 max-w-xs text-lg font-bold leading-snug text-text-primary sm:text-xl">
                                        {promotion.title}
                                    </h2>
                                </div>

                                <a
                                    href={promotion.href}
                                    className="mt-4 inline-flex items-center gap-1.5 text-sm font-bold text-brand-secondary"
                                >
                                    {promotion.ctaLabel}

                                    <ArrowRight size={15} strokeWidth={2} />
                                </a>
                            </article>
                        ))}
                    </div>
                </div>

                {/* Slider indicator */}
                <div className="mt-3 flex justify-center gap-1.5 lg:justify-start">
                    <span className="h-1.5 w-6 rounded-full bg-brand-secondary" />
                    <span className="size-1.5 rounded-full bg-border-strong" />
                    <span className="size-1.5 rounded-full bg-border-strong" />
                </div>
            </div>
        </section>
    );
}
