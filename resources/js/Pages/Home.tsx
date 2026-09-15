import {
    BriefcaseBusiness,
    LayoutGrid,
    PackageSearch,
    Store,
} from "lucide-react";

import PublicLayout from "../layouts/PublicLayout";

const discoveryItems = [
    {
        title: "Products",
        description:
            "Discover products from Sri Lankan sellers and businesses.",
        icon: PackageSearch,
    },
    {
        title: "Services",
        description: "Find trusted professionals and business services.",
        icon: BriefcaseBusiness,
    },
    {
        title: "Classifieds",
        description: "Browse opportunities, listings and local classifieds.",
        icon: LayoutGrid,
    },
    {
        title: "Stores",
        description: "Explore businesses and sellers on Dubai Lanka.",
        icon: Store,
    },
];

export default function Home() {
    return (
        <PublicLayout>
            {/* Temporary homepage preview */}
            <section className="border-b border-border bg-surface">
                <div className="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8 lg:py-16">
                    <div className="max-w-3xl">
                        <p className="text-sm font-bold uppercase tracking-wide text-brand-secondary">
                            Dubai Lanka
                        </p>

                        <h1 className="mt-3 text-3xl font-bold leading-tight text-text-primary sm:text-4xl lg:text-[40px]">
                            Discover Sri Lankan Products, Businesses &amp;
                            Services in the UAE
                        </h1>

                        <p className="mt-4 max-w-2xl text-base leading-7 text-text-secondary sm:text-lg">
                            Discover products, services, stores and
                            opportunities connecting Sri Lankan businesses and
                            communities across the UAE.
                        </p>
                    </div>

                    <div className="mt-8 grid grid-cols-2 gap-3 lg:grid-cols-4">
                        {discoveryItems.map((item) => {
                            const Icon = item.icon;

                            return (
                                <a
                                    key={item.title}
                                    href="#"
                                    className="group rounded-lg border border-border bg-surface p-4 transition-colors hover:border-border-strong sm:p-5"
                                >
                                    <div className="flex size-10 items-center justify-center rounded-md bg-brand-secondary-soft text-brand-secondary">
                                        <Icon size={20} strokeWidth={1.8} />
                                    </div>

                                    <h2 className="mt-4 font-bold text-text-primary group-hover:text-brand-secondary">
                                        {item.title}
                                    </h2>

                                    <p className="mt-1.5 hidden text-sm leading-6 text-text-secondary sm:block">
                                        {item.description}
                                    </p>
                                </a>
                            );
                        })}
                    </div>
                </div>
            </section>

            {/* Temporary content to test sticky header + scrolling */}
            <section className="bg-surface-subtle">
                <div className="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                    <div className="flex items-end justify-between gap-4">
                        <div>
                            <p className="text-sm font-semibold text-brand-secondary">
                                Explore
                            </p>

                            <h2 className="mt-1 text-2xl font-bold text-text-primary">
                                Featured on Dubai Lanka
                            </h2>
                        </div>

                        <a
                            href="#"
                            className="hidden text-sm font-semibold text-brand-secondary sm:block"
                        >
                            View all
                        </a>
                    </div>

                    <div className="mt-6 grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-4">
                        {Array.from({ length: 8 }).map((_, index) => (
                            <div
                                key={index}
                                className="overflow-hidden rounded-lg border border-border bg-surface"
                            >
                                <div className="aspect-[4/3] bg-surface-muted" />

                                <div className="p-4">
                                    <div className="h-3 w-20 rounded bg-surface-muted" />

                                    <div className="mt-3 h-4 w-4/5 rounded bg-surface-muted" />

                                    <div className="mt-2 h-4 w-3/5 rounded bg-surface-muted" />

                                    <div className="mt-4 h-5 w-24 rounded bg-brand-primary-soft" />
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}
