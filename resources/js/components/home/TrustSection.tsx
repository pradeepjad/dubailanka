import { ArrowRight, BadgeCheck, MessagesSquare, Store } from "lucide-react";

const trustItems = [
    {
        title: "Verified Businesses",
        description:
            "Verification badges help identify businesses that have completed Dubai Lanka's verification process.",
        icon: BadgeCheck,
    },
    {
        title: "Clear Seller Identity",
        description:
            "See the Store or business behind products, services and business listings.",
        icon: Store,
    },
    {
        title: "Safer Platform Connections",
        description:
            "Use Dubai Lanka's inquiry and platform interactions instead of exposing unnecessary personal contact information everywhere.",
        icon: MessagesSquare,
    },
];

export default function TrustSection() {
    return (
        <section className="border-y border-border bg-brand-secondary-soft">
            <div className="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-12 lg:px-8 lg:py-14">
                <div className="mx-auto max-w-3xl text-center">
                    <p className="text-xs font-bold uppercase tracking-wider text-brand-secondary">
                        Built for Trusted Connections
                    </p>

                    <h2 className="mt-2 text-2xl font-bold leading-tight text-text-primary sm:text-3xl">
                        Connect with greater confidence on Dubai Lanka
                    </h2>

                    <p className="mx-auto mt-4 max-w-2xl text-sm leading-6 text-text-secondary sm:text-base">
                        Dubai Lanka provides verification and platform tools
                        designed to help buyers, sellers and businesses connect
                        more confidently.
                    </p>
                </div>

                <div className="mx-auto mt-8 grid max-w-5xl gap-3 md:grid-cols-3">
                    {trustItems.map((item) => {
                        const Icon = item.icon;

                        return (
                            <article
                                key={item.title}
                                className="rounded-lg border border-border bg-surface p-5 text-center sm:p-6"
                            >
                                <div className="mx-auto flex size-11 items-center justify-center rounded-full bg-brand-secondary-soft text-brand-secondary">
                                    <Icon size={21} strokeWidth={1.8} />
                                </div>

                                <h3 className="mt-4 text-base font-bold text-text-primary">
                                    {item.title}
                                </h3>

                                <p className="mt-2 text-sm leading-6 text-text-secondary">
                                    {item.description}
                                </p>
                            </article>
                        );
                    })}
                </div>

                <div className="mt-7 text-center">
                    <a
                        href="#"
                        className="inline-flex items-center gap-1.5 text-sm font-bold text-brand-secondary transition-colors hover:text-brand-secondary-hover"
                    >
                        Learn about verification
                        <ArrowRight size={15} strokeWidth={2} />
                    </a>
                </div>
            </div>
        </section>
    );
}
