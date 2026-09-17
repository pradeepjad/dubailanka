import { Head, Link, usePage } from "@inertiajs/react";
import { ArrowRight, Building2, CheckCircle2, UserRound } from "lucide-react";
import PublicLayout from "../../layouts/PublicLayout";

type Business = {
    id: number;
    type: "personal_business" | "registered_company";
    legal_name: string;
    trading_name?: string | null;
    country_code: string;
    status: string;
};

type PageProps = {
    businesses: Business[];
    flash?: { success?: string };
};

export default function Start({ businesses }: PageProps) {
    const { props } = usePage<PageProps>();
    const success = props.flash?.success;

    return (
        <PublicLayout>
            <Head title="Start Selling | Dubai Lanka" />

            <main className="bg-surface-subtle py-10 sm:py-14">
                <div className="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                    {success && (
                        <div className="mb-6 flex items-start gap-3 rounded-lg border border-success/20 bg-success-soft p-4 text-sm text-success">
                            <CheckCircle2 className="mt-0.5 size-5 shrink-0" />
                            <span>{success}</span>
                        </div>
                    )}

                    <div className="max-w-2xl">
                        <p className="text-sm font-bold text-brand-secondary">SELL ON DUBAI LANKA</p>
                        <h1 className="mt-2 text-3xl font-bold tracking-tight text-text-primary sm:text-4xl">
                            Start your seller journey
                        </h1>
                        <p className="mt-4 text-base leading-7 text-text-secondary">
                            First, tell us how you operate your business. You can own more than one business entity and add stores under them in the next step.
                        </p>
                    </div>

                    {businesses.length > 0 && (
                        <section className="mt-8 rounded-xl border border-border bg-surface p-5 sm:p-6">
                            <div className="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <h2 className="text-lg font-bold text-text-primary">Your businesses</h2>
                                    <p className="mt-1 text-sm text-text-muted">Business entities already connected to your account.</p>
                                </div>
                                <span className="rounded-full bg-brand-secondary-soft px-3 py-1 text-xs font-bold text-brand-secondary">
                                    {businesses.length} {businesses.length === 1 ? "business" : "businesses"}
                                </span>
                            </div>

                            <div className="mt-5 grid gap-3 sm:grid-cols-2">
                                {businesses.map((business) => (
                                    <div key={business.id} className="rounded-lg border border-border p-4">
                                        <p className="text-xs font-bold uppercase tracking-wide text-text-muted">
                                            {business.type === "registered_company" ? "Registered Company" : "Personal Business"}
                                        </p>
                                        <p className="mt-1 font-bold text-text-primary">{business.trading_name || business.legal_name}</p>
                                        {business.trading_name && <p className="mt-1 text-xs text-text-muted">{business.legal_name}</p>}
                                        <p className="mt-3 text-xs font-semibold text-success">Active</p>
                                    </div>
                                ))}
                            </div>
                        </section>
                    )}

                    <section className="mt-8">
                        <h2 className="text-xl font-bold text-text-primary">
                            {businesses.length ? "Add another business" : "How are you selling?"}
                        </h2>

                        <div className="mt-4 grid gap-4 md:grid-cols-2">
                            <Link
                                href="/seller/businesses/create?type=personal_business"
                                className="group cursor-pointer rounded-xl border border-border bg-surface p-6 transition hover:border-brand-secondary"
                            >
                                <div className="flex size-11 items-center justify-center rounded-lg bg-brand-secondary-soft text-brand-secondary">
                                    <UserRound size={22} />
                                </div>
                                <h3 className="mt-5 text-lg font-bold text-text-primary">Personal Business</h3>
                                <p className="mt-2 text-sm leading-6 text-text-secondary">
                                    For an individual selling under a personal or trading business name without a registered company.
                                </p>
                                <span className="mt-5 inline-flex items-center gap-2 text-sm font-bold text-brand-secondary">
                                    Continue <ArrowRight size={16} className="transition-transform group-hover:translate-x-1" />
                                </span>
                            </Link>

                            <Link
                                href="/seller/businesses/create?type=registered_company"
                                className="group cursor-pointer rounded-xl border border-border bg-surface p-6 transition hover:border-brand-secondary"
                            >
                                <div className="flex size-11 items-center justify-center rounded-lg bg-brand-primary-soft text-warning">
                                    <Building2 size={22} />
                                </div>
                                <h3 className="mt-5 text-lg font-bold text-text-primary">Registered Company</h3>
                                <p className="mt-2 text-sm leading-6 text-text-secondary">
                                    For a legally registered company operating in the UAE or Sri Lanka.
                                </p>
                                <span className="mt-5 inline-flex items-center gap-2 text-sm font-bold text-brand-secondary">
                                    Continue <ArrowRight size={16} className="transition-transform group-hover:translate-x-1" />
                                </span>
                            </Link>
                        </div>
                    </section>
                </div>
            </main>
        </PublicLayout>
    );
}
