import { Form, Head, Link } from "@inertiajs/react";
import { ArrowLeft, Building2, UserRound } from "lucide-react";
import { useState } from "react";
import PublicLayout from "../../../layouts/PublicLayout";
import Button from "../../../components/ui/Button";
import Input from "../../../components/ui/Input";
import Select from "../../../components/ui/Select";

type BusinessType = "personal_business" | "registered_company";

type Props = {
    initialType: BusinessType;
    accountEmail: string;
};

export default function Create({ initialType, accountEmail }: Props) {
    const registered = initialType === "registered_company";
    const [countryCode, setCountryCode] = useState("AE");
    const phonePlaceholder = countryCode === "LK" ? "+94 77 123 4567" : "+971 50 123 4567";

    return (
        <PublicLayout>
            <Head title="Create Business | Dubai Lanka" />

            <main className="bg-surface-subtle py-8 sm:py-12">
                <div className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                    <Link href="/seller/start" className="inline-flex cursor-pointer items-center gap-2 text-sm font-semibold text-text-secondary hover:text-brand-secondary">
                        <ArrowLeft size={16} /> Back
                    </Link>

                    <div className="mt-5 rounded-xl border border-border bg-surface p-5 sm:p-8">
                        <div className="flex items-start gap-4">
                            <div className={`flex size-11 shrink-0 items-center justify-center rounded-lg ${registered ? "bg-brand-primary-soft text-warning" : "bg-brand-secondary-soft text-brand-secondary"}`}>
                                {registered ? <Building2 size={22} /> : <UserRound size={22} />}
                            </div>
                            <div>
                                <p className="text-xs font-bold uppercase tracking-wide text-brand-secondary">Business setup</p>
                                <h1 className="mt-1 text-2xl font-bold text-text-primary sm:text-3xl">
                                    {registered ? "Registered Company" : "Personal Business"}
                                </h1>
                                <p className="mt-2 text-sm leading-6 text-text-secondary">
                                    Add the business information that will own your future Dubai Lanka store.
                                </p>
                            </div>
                        </div>

                        <Form action="/seller/businesses" method="post" className="mt-8" disableWhileProcessing>
                            {({ errors, processing }) => (
                                <>
                                    <input type="hidden" name="type" value={initialType} />

                                    <div className="grid gap-5 sm:grid-cols-2">
                                        <div className="sm:col-span-2">
                                            <Input
                                                name="legal_name"
                                                label={registered ? "Legal Company Name" : "Business / Trading Name"}
                                                placeholder={registered ? "Example Trading LLC" : "Example Trading"}
                                                error={errors.legal_name}
                                                required
                                            />
                                        </div>

                                        {registered && (
                                            <>
                                                <Input
                                                    name="trading_name"
                                                    label="Trading Name (Optional)"
                                                    placeholder="Public trading name"
                                                    error={errors.trading_name}
                                                />
                                                <Input
                                                    name="registration_number"
                                                    label="Company Registration Number"
                                                    placeholder="Registration number"
                                                    error={errors.registration_number}
                                                    required
                                                />
                                            </>
                                        )}

                                        <Select
                                            name="country_code"
                                            label="Country"
                                            value={countryCode}
                                            onChange={(event) => setCountryCode(event.target.value)}
                                            error={errors.country_code}
                                            required
                                        >
                                            <option value="AE">United Arab Emirates</option>
                                            <option value="LK">Sri Lanka</option>
                                        </Select>

                                        <Input
                                            name="phone"
                                            label="Business Phone"
                                            type="tel"
                                            placeholder={phonePlaceholder}
                                            error={errors.phone}
                                            required
                                        />

                                        <Input
                                            name="whatsapp"
                                            label="WhatsApp (Optional)"
                                            type="tel"
                                            placeholder={phonePlaceholder}
                                            error={errors.whatsapp}
                                        />

                                        <Input
                                            name="email"
                                            label="Business Email"
                                            type="email"
                                            defaultValue={accountEmail}
                                            placeholder="business@example.com"
                                            error={errors.email}
                                            required
                                        />

                                        {registered && (
                                            <div className="sm:col-span-2">
                                                <label htmlFor="address" className="mb-1.5 block text-sm font-semibold text-text-primary">
                                                    Registered Business Address
                                                    <span className="ml-1 text-danger" aria-hidden="true">*</span>
                                                </label>
                                                <textarea
                                                    id="address"
                                                    name="address"
                                                    rows={3}
                                                    className="w-full rounded-md border border-border-strong bg-surface px-3 py-2.5 text-sm text-text-primary outline-none transition focus:border-brand-secondary focus:ring-2 focus:ring-brand-secondary/15"
                                                    placeholder="Registered company address"
                                                    required
                                                />
                                                {errors.address && <p className="mt-1.5 text-sm text-danger">{errors.address}</p>}
                                            </div>
                                        )}
                                    </div>

                                    <div className="mt-8 flex flex-col-reverse gap-3 border-t border-border pt-6 sm:flex-row sm:justify-between">
                                        <Link href="/seller/start" className="inline-flex min-h-11 cursor-pointer items-center justify-center rounded-md border border-border-strong px-4 text-sm font-semibold text-text-primary hover:bg-surface-subtle">
                                            Cancel
                                        </Link>
                                        <Button type="submit" loading={processing}>
                                            Create Business
                                        </Button>
                                    </div>
                                </>
                            )}
                        </Form>
                    </div>

                    <p className="mt-4 text-center text-xs leading-5 text-text-muted">
                        Business verification documents are not required in this step. Verification will be handled separately when that feature is introduced.
                    </p>
                </div>
            </main>
        </PublicLayout>
    );
}
