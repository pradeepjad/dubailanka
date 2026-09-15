import { ArrowRight, Building2, Package, Wrench } from "lucide-react";

export default function SellerCTA() {
    return (
        <section className="bg-surface">
            <div className="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
                <div className="overflow-hidden rounded-xl border border-border bg-text-primary">
                    <div className="grid items-center gap-8 px-6 py-8 sm:px-8 sm:py-10 lg:grid-cols-[1.5fr_1fr] lg:px-10">
                        <div>
                            <p className="text-xs font-bold uppercase tracking-wider text-brand-primary">
                                For Businesses & Sellers
                            </p>

                            <h2 className="mt-2 max-w-2xl text-2xl font-bold leading-tight text-text-inverse sm:text-3xl">
                                Grow your business with Dubai Lanka
                            </h2>

                            <p className="mt-4 max-w-2xl text-sm leading-6 text-white/70 sm:text-base">
                                Showcase your products, services and business to
                                customers across the UAE and Sri Lanka, all from
                                one trusted platform.
                            </p>

                            <div className="mt-6 flex flex-wrap gap-3">
                                <a
                                    href="#"
                                    className="inline-flex min-h-11 items-center gap-2 rounded-md bg-brand-primary px-5 text-sm font-bold text-text-primary transition-colors hover:opacity-90"
                                >
                                    Sell on Dubai Lanka
                                    <ArrowRight size={16} strokeWidth={2} />
                                </a>

                                <a
                                    href="#"
                                    className="inline-flex min-h-11 items-center rounded-md border border-white/20 px-5 text-sm font-semibold text-text-inverse transition-colors hover:border-white/40 hover:bg-white/5"
                                >
                                    Learn More
                                </a>
                            </div>
                        </div>

                        <div className="grid grid-cols-3 gap-2 sm:gap-3">
                            <div className="rounded-lg border border-white/10 bg-white/5 p-4 text-center">
                                <div className="mx-auto flex size-9 items-center justify-center rounded-full bg-white/10 text-brand-primary">
                                    <Package size={18} strokeWidth={1.8} />
                                </div>

                                <p className="mt-3 text-xs font-semibold text-text-inverse">
                                    Products
                                </p>
                            </div>

                            <div className="rounded-lg border border-white/10 bg-white/5 p-4 text-center">
                                <div className="mx-auto flex size-9 items-center justify-center rounded-full bg-white/10 text-brand-primary">
                                    <Wrench size={18} strokeWidth={1.8} />
                                </div>

                                <p className="mt-3 text-xs font-semibold text-text-inverse">
                                    Services
                                </p>
                            </div>

                            <div className="rounded-lg border border-white/10 bg-white/5 p-4 text-center">
                                <div className="mx-auto flex size-9 items-center justify-center rounded-full bg-white/10 text-brand-primary">
                                    <Building2 size={18} strokeWidth={1.8} />
                                </div>

                                <p className="mt-3 text-xs font-semibold text-text-inverse">
                                    Business
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
