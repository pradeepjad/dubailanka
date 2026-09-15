import {
    Check,
    Heart,
    MapPin,
    Search,
    ShoppingCart,
    Store,
    User,
} from "lucide-react";

import Button from "../components/ui/Button";
import Input from "../components/ui/Input";
import Select from "../components/ui/Select";
import StatusBadge from "../components/ui/StatusBadge";

function Section({
    title,
    description,
    children,
}: {
    title: string;
    description?: string;
    children: React.ReactNode;
}) {
    return (
        <section className="border-b border-border py-10 last:border-b-0">
            <div className="mb-6">
                <h2 className="text-xl font-bold text-text-primary">{title}</h2>

                {description && (
                    <p className="mt-1 max-w-2xl text-sm text-text-secondary">
                        {description}
                    </p>
                )}
            </div>

            {children}
        </section>
    );
}

export default function DesignSystem() {
    return (
        <div className="min-h-screen bg-surface-subtle">
            <header className="border-b border-border bg-surface">
                <div className="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <div className="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <p className="text-sm font-semibold text-brand-secondary">
                                Dubai Lanka
                            </p>

                            <h1 className="mt-1 text-2xl font-bold text-text-primary sm:text-3xl">
                                UI Foundation
                            </h1>

                            <p className="mt-2 max-w-2xl text-sm text-text-secondary">
                                Internal design reference for the Dubai Lanka
                                marketplace platform.
                            </p>
                        </div>

                        <StatusBadge variant="success" dot>
                            Foundation
                        </StatusBadge>
                    </div>
                </div>
            </header>

            <main className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <Section
                    title="Brand Direction"
                    description="Phase 01 public-facing positioning and core visual identity."
                >
                    <div className="rounded-lg border border-border bg-surface p-6 sm:p-8">
                        <p className="text-sm font-semibold text-brand-secondary">
                            DUBAI LANKA
                        </p>

                        <h2 className="mt-3 max-w-3xl text-3xl font-bold leading-tight text-text-primary sm:text-4xl">
                            Discover Sri Lankan Products, Businesses &amp;
                            Services in the UAE
                        </h2>

                        <p className="mt-4 max-w-2xl text-base text-text-secondary">
                            A simple, trusted way to discover products,
                            services, stores and opportunities connecting Sri
                            Lanka and the UAE.
                        </p>

                        <div className="mt-6 flex flex-wrap gap-3">
                            <Button>Explore Dubai Lanka</Button>
                            <Button variant="outline">Start Selling</Button>
                        </div>
                    </div>
                </Section>

                <Section
                    title="Brand Colours"
                    description="Semantic colours should be used instead of repeating raw colour values throughout the application."
                >
                    <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div className="overflow-hidden rounded-lg border border-border bg-surface">
                            <div className="h-24 bg-brand-primary" />
                            <div className="p-4">
                                <p className="font-semibold">Primary</p>
                                <p className="mt-1 text-sm text-text-muted">
                                    #ffb300
                                </p>
                            </div>
                        </div>

                        <div className="overflow-hidden rounded-lg border border-border bg-surface">
                            <div className="h-24 bg-brand-secondary" />
                            <div className="p-4">
                                <p className="font-semibold">Secondary</p>
                                <p className="mt-1 text-sm text-text-muted">
                                    #005bff
                                </p>
                            </div>
                        </div>

                        <div className="overflow-hidden rounded-lg border border-border bg-surface">
                            <div className="h-24 bg-text-primary" />
                            <div className="p-4">
                                <p className="font-semibold">Primary Text</p>
                                <p className="mt-1 text-sm text-text-muted">
                                    #171717
                                </p>
                            </div>
                        </div>

                        <div className="overflow-hidden rounded-lg border border-border bg-surface">
                            <div className="h-24 bg-danger" />
                            <div className="p-4">
                                <p className="font-semibold">Danger</p>
                                <p className="mt-1 text-sm text-text-muted">
                                    #ef2929
                                </p>
                            </div>
                        </div>
                    </div>
                </Section>

                <Section
                    title="Typography"
                    description="Red Hat Display is the primary Dubai Lanka interface typeface."
                >
                    <div className="rounded-lg border border-border bg-surface p-6">
                        <div className="space-y-5">
                            <div>
                                <p className="text-xs text-text-muted">
                                    40px / Bold
                                </p>
                                <p className="text-[40px] font-bold leading-tight">
                                    Dubai Lanka
                                </p>
                            </div>

                            <div>
                                <p className="text-xs text-text-muted">
                                    32px / Bold
                                </p>
                                <p className="text-3xl font-bold">
                                    Discover Sri Lankan businesses
                                </p>
                            </div>

                            <div>
                                <p className="text-xs text-text-muted">
                                    24px / Semibold
                                </p>
                                <p className="text-2xl font-semibold">
                                    Featured Products
                                </p>
                            </div>

                            <div>
                                <p className="text-xs text-text-muted">
                                    16px / Regular
                                </p>
                                <p className="text-base text-text-secondary">
                                    Find products, services, businesses and
                                    opportunities across Dubai Lanka.
                                </p>
                            </div>

                            <div>
                                <p className="text-xs text-text-muted">
                                    14px / Medium
                                </p>
                                <p className="text-sm font-medium">
                                    Store information and supporting text
                                </p>
                            </div>

                            <div>
                                <p className="text-xs text-text-muted">
                                    12px / Medium
                                </p>
                                <p className="text-xs font-medium">
                                    LABEL / META INFORMATION
                                </p>
                            </div>
                        </div>
                    </div>
                </Section>

                <Section
                    title="Buttons"
                    description="Shared actions for public, buyer, seller and administration interfaces."
                >
                    <div className="space-y-6 rounded-lg border border-border bg-surface p-6">
                        <div className="flex flex-wrap items-center gap-3">
                            <Button>Primary</Button>
                            <Button variant="secondary">Secondary</Button>
                            <Button variant="outline">Outline</Button>
                            <Button variant="ghost">Ghost</Button>
                            <Button variant="danger">Danger</Button>
                        </div>

                        <div className="flex flex-wrap items-center gap-3">
                            <Button size="sm">Small</Button>
                            <Button size="md">Medium</Button>
                            <Button size="lg">Large</Button>
                        </div>

                        <div className="flex flex-wrap items-center gap-3">
                            <Button>
                                <Search size={17} />
                                Search
                            </Button>

                            <Button variant="outline">
                                <Heart size={17} />
                                Save
                            </Button>

                            <Button loading>Processing</Button>

                            <Button disabled>Disabled</Button>
                        </div>
                    </div>
                </Section>

                <Section
                    title="Form Controls"
                    description="Base form controls for onboarding, search, seller forms, checkout and administration."
                >
                    <div className="grid gap-6 rounded-lg border border-border bg-surface p-6 md:grid-cols-2">
                        <Input
                            id="search-example"
                            label="Search"
                            placeholder="Search products, services, stores..."
                            startIcon={<Search size={18} />}
                            hint="Search across the Dubai Lanka platform."
                        />

                        <Input
                            id="location-example"
                            label="Location"
                            placeholder="Dubai, UAE"
                            startIcon={<MapPin size={18} />}
                        />

                        <Input
                            id="email-example"
                            label="Email Address"
                            type="email"
                            placeholder="name@example.com"
                        />

                        <Input
                            id="error-example"
                            label="Example Validation"
                            defaultValue="invalid value"
                            error="Please check this field and try again."
                        />

                        <Select
                            id="country-example"
                            label="Country"
                            defaultValue=""
                        >
                            <option value="" disabled>
                                Select country
                            </option>
                            <option value="uae">United Arab Emirates</option>
                            <option value="lk">Sri Lanka</option>
                        </Select>

                        <Select
                            id="disabled-example"
                            label="Disabled Select"
                            disabled
                            defaultValue="uae"
                        >
                            <option value="uae">United Arab Emirates</option>
                        </Select>
                    </div>
                </Section>

                <Section
                    title="Status Badges"
                    description="Consistent status language across verification, listings, orders and administration."
                >
                    <div className="flex flex-wrap gap-3 rounded-lg border border-border bg-surface p-6">
                        <StatusBadge dot>Draft</StatusBadge>
                        <StatusBadge variant="warning" dot>
                            Pending
                        </StatusBadge>
                        <StatusBadge variant="success" dot>
                            Approved
                        </StatusBadge>
                        <StatusBadge variant="info" dot>
                            Verified
                        </StatusBadge>
                        <StatusBadge variant="danger" dot>
                            Rejected
                        </StatusBadge>
                    </div>
                </Section>

                <Section
                    title="Icon Language"
                    description="Lucide is the primary interface icon library."
                >
                    <div className="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-7">
                        {[
                            [Search, "Search"],
                            [MapPin, "Location"],
                            [Heart, "Saved"],
                            [Store, "Store"],
                            [ShoppingCart, "Cart"],
                            [User, "Account"],
                            [Check, "Success"],
                        ].map(([Icon, label]) => {
                            const IconComponent = Icon as typeof Search;

                            return (
                                <div
                                    key={label as string}
                                    className="flex min-h-24 flex-col items-center justify-center rounded-lg border border-border bg-surface p-4"
                                >
                                    <IconComponent
                                        size={22}
                                        strokeWidth={1.8}
                                    />
                                    <span className="mt-2 text-xs font-medium text-text-secondary">
                                        {label as string}
                                    </span>
                                </div>
                            );
                        })}
                    </div>
                </Section>

                <Section
                    title="Surface & Border Style"
                    description="Dubai Lanka uses spacing, typography and clean borders rather than decorative card shadows."
                >
                    <div className="grid gap-4 md:grid-cols-3">
                        <div className="rounded-md border border-border bg-surface p-5">
                            <p className="font-semibold">Standard Surface</p>
                            <p className="mt-2 text-sm text-text-secondary">
                                Default cards and content containers.
                            </p>
                        </div>

                        <div className="rounded-lg border border-border-strong bg-surface p-5">
                            <p className="font-semibold">Strong Border</p>
                            <p className="mt-2 text-sm text-text-secondary">
                                Useful where additional separation is needed.
                            </p>
                        </div>

                        <div className="rounded-lg border border-brand-secondary/30 bg-brand-secondary-soft p-5">
                            <p className="font-semibold text-brand-secondary">
                                Highlight Surface
                            </p>
                            <p className="mt-2 text-sm text-text-secondary">
                                Controlled emphasis without a shadow.
                            </p>
                        </div>
                    </div>
                </Section>
            </main>

            <footer className="border-t border-border bg-surface">
                <div className="mx-auto max-w-7xl px-4 py-6 text-sm text-text-muted sm:px-6 lg:px-8">
                    Dubai Lanka Design System · Internal Development Reference
                </div>
            </footer>
        </div>
    );
}
