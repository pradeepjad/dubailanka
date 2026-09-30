import { Form, Head, Link } from "@inertiajs/react";
import { useEffect, useState, type ReactNode } from "react";
import AdminLayout from "../../../layouts/AdminLayout";
import {
    sanitizeStoreSlugInput,
    slugifyStoreName,
} from "../../../lib/storeSlug";

const C = ({ children }: { children: ReactNode }) => (
    <section className="rounded-xl border border-border bg-surface p-5 sm:p-6">
        {children}
    </section>
);
const I = ({
    label,
    name,
    type = "text",
    required = false,
}: {
    label: string;
    name: string;
    type?: string;
    required?: boolean;
}) => (
    <label className="block">
        <span className="mb-1.5 block text-sm font-bold text-text-primary">
            {label}
            {required && <span className="text-danger"> *</span>}
        </span>
        <input
            name={name}
            type={type}
            required={required}
            className="w-full rounded-lg border border-border bg-surface px-3 py-2.5 outline-none focus:border-brand-secondary"
        />
    </label>
);
type SlugState = "idle" | "checking" | "available" | "taken" | "invalid";
type Mode = "existing" | "invite" | "unassigned";
type Intent = "handover" | "business_only" | "complete_setup";

export default function Create() {
    const [mode, setMode] = useState<Mode>("invite");
    const [intent, setIntent] = useState<Intent>("complete_setup");
    const [ownerEmail, setOwnerEmail] = useState("");
    const [ownerLookup, setOwnerLookup] = useState<any>(null);
    const [ownerLookupState, setOwnerLookupState] = useState<
        "idle" | "checking" | "found" | "missing" | "unavailable"
    >("idle");
    const [storeName, setStoreName] = useState("");
    const [storeSlug, setStoreSlug] = useState("");
    const [slugTouched, setSlugTouched] = useState(false);
    const [slugState, setSlugState] = useState<SlugState>("idle");
    useEffect(() => {
        if (
            (mode === "unassigned" || mode === "existing") &&
            intent === "handover"
        )
            setIntent(
                mode === "unassigned" ? "business_only" : "complete_setup",
            );
        if (mode !== "existing") {
            setOwnerLookup(null);
            setOwnerLookupState("idle");
        }
    }, [mode, intent]);
    useEffect(() => {
        if (!slugTouched) setStoreSlug(slugifyStoreName(storeName));
    }, [storeName, slugTouched]);
    const checkExistingOwner = async () => {
        const email = ownerEmail.trim().toLowerCase();
        if (!email) return;
        setOwnerLookupState("checking");
        setOwnerLookup(null);
        try {
            const r = await fetch(
                `/admin/onboarding/owner-lookup?${new URLSearchParams({ email })}`,
                { headers: { Accept: "application/json" } },
            );
            const j = await r.json();
            if (!r.ok || j.available === false) {
                setOwnerLookup(j);
                setOwnerLookupState("unavailable");
            } else if (j.exists) {
                setOwnerLookup(j);
                setOwnerLookupState("found");
            } else {
                setOwnerLookup(j);
                setOwnerLookupState("missing");
            }
        } catch {
            setOwnerLookupState("unavailable");
        }
    };
    useEffect(() => {
        if (intent !== "complete_setup" || !storeSlug) {
            setSlugState("idle");
            return;
        }
        const timer = window.setTimeout(async () => {
            setSlugState("checking");
            try {
                const r = await fetch(
                    `/seller/stores/slug-availability?${new URLSearchParams({ slug: storeSlug })}`,
                    { headers: { Accept: "application/json" } },
                );
                const j = await r.json();
                setSlugState(
                    !j.valid ? "invalid" : j.available ? "available" : "taken",
                );
            } catch {
                setSlugState("idle");
            }
        }, 350);
        return () => clearTimeout(timer);
    }, [storeSlug, intent]);
    return (
        <AdminLayout>
            <Head title="Create Seller Setup | Dubai Lanka" />
            <main className="bg-surface-subtle py-10">
                <div className="mx-auto max-w-4xl px-4">
                    <p className="text-sm font-bold text-brand-secondary">
                        ADMIN · SELLER ONBOARDING
                    </p>
                    <h1 className="mt-1 text-3xl font-bold">
                        Create seller setup
                    </h1>
                    <p className="mt-2 text-sm text-text-muted">
                        Choose the owner path first. Existing accounts are set
                        up by Admin; invited sellers can continue themselves or
                        Admin can continue on their behalf.
                    </p>
                    <Form
                        action="/admin/onboarding"
                        method="post"
                        className="mt-7 space-y-5"
                        resetOnSuccess
                    >
                        {({ errors, processing }) => (
                            <>
                                {Object.keys(errors).length > 0 && (
                                    <div className="rounded-lg bg-danger-soft p-4 text-sm text-danger">
                                        {Object.entries(errors).map(
                                            ([k, e]) => (
                                                <p key={k}>{e}</p>
                                            ),
                                        )}
                                    </div>
                                )}
                                <input
                                    type="hidden"
                                    name="submit_intent"
                                    value={intent}
                                />
                                <C>
                                    <h2 className="text-lg font-bold">
                                        1. Ownership
                                    </h2>
                                    <p className="mt-1 text-sm text-text-muted">
                                        Who should own this seller setup?
                                    </p>
                                    <div className="mt-4 grid gap-3 md:grid-cols-3">
                                        {(
                                            [
                                                [
                                                    "existing",
                                                    "Existing account",
                                                    "Seller already has a Dubai Lanka account.",
                                                ],
                                                [
                                                    "invite",
                                                    "Invite seller",
                                                    "Seller is known by email but has no completed account yet.",
                                                ],
                                                [
                                                    "unassigned",
                                                    "Owner not assigned",
                                                    "No owner identity is attached yet. Assign one later.",
                                                ],
                                            ] as const
                                        ).map(([v, t, d]) => (
                                            <label
                                                key={v}
                                                className={`cursor-pointer rounded-lg border p-4 ${mode === v ? "border-brand-secondary bg-brand-secondary-soft" : "border-border"}`}
                                            >
                                                <input
                                                    type="radio"
                                                    name="ownership_mode"
                                                    value={v}
                                                    checked={mode === v}
                                                    onChange={() => setMode(v)}
                                                    className="mr-2"
                                                />
                                                <strong>{t}</strong>
                                                <span className="mt-2 block text-xs text-text-muted">
                                                    {d}
                                                </span>
                                            </label>
                                        ))}
                                    </div>
                                    {mode === "existing" ? (
                                        <div className="mt-4">
                                            <label className="block">
                                                <span className="mb-1.5 block text-sm font-bold text-text-primary">
                                                    Owner email{" "}
                                                    <span className="text-danger">
                                                        *
                                                    </span>
                                                </span>
                                                <div className="flex flex-col gap-2 sm:flex-row">
                                                    <input
                                                        name="owner_email"
                                                        type="email"
                                                        required
                                                        value={ownerEmail}
                                                        onChange={(e) => {
                                                            setOwnerEmail(
                                                                e.target.value,
                                                            );
                                                            setOwnerLookup(
                                                                null,
                                                            );
                                                            setOwnerLookupState(
                                                                "idle",
                                                            );
                                                        }}
                                                        className="min-w-0 flex-1 rounded-lg border border-border bg-surface px-3 py-2.5 outline-none focus:border-brand-secondary"
                                                    />
                                                    <button
                                                        type="button"
                                                        onClick={
                                                            checkExistingOwner
                                                        }
                                                        disabled={
                                                            !ownerEmail.trim() ||
                                                            ownerLookupState ===
                                                                "checking"
                                                        }
                                                        className="min-h-11 cursor-pointer rounded-lg border border-brand-secondary px-4 py-2.5 text-sm font-bold text-brand-secondary disabled:cursor-not-allowed disabled:opacity-60"
                                                    >
                                                        {ownerLookupState ===
                                                        "checking"
                                                            ? "Checking..."
                                                            : "Check Account"}
                                                    </button>
                                                </div>
                                            </label>
                                            {ownerLookupState === "found" && (
                                                <div className="mt-3 rounded-lg bg-success-soft p-3 text-sm text-success">
                                                    <strong>
                                                        Existing Dubai Lanka
                                                        account found
                                                    </strong>
                                                    <p>
                                                        {
                                                            ownerLookup?.user
                                                                ?.name
                                                        }{" "}
                                                        ·{" "}
                                                        {
                                                            ownerLookup?.user
                                                                ?.email
                                                        }
                                                    </p>
                                                </div>
                                            )}
                                            {ownerLookupState === "missing" && (
                                                <div className="mt-3 rounded-lg bg-warning-soft p-3 text-sm text-warning">
                                                    <strong>
                                                        No Dubai Lanka account
                                                        found for this email.
                                                    </strong>{" "}
                                                    Use Invite Seller instead.
                                                </div>
                                            )}
                                            {ownerLookupState ===
                                                "unavailable" && (
                                                <div className="mt-3 rounded-lg bg-danger-soft p-3 text-sm text-danger">
                                                    {ownerLookup?.message ||
                                                        "Could not use this account for seller onboarding."}
                                                </div>
                                            )}
                                            <p className="mt-3 text-xs text-text-muted">
                                                The Super Admin will continue
                                                the setup for this existing
                                                account. There is no handover
                                                step before a Business Entity
                                                exists.
                                            </p>
                                        </div>
                                    ) : mode === "invite" ? (
                                        <div className="mt-4 grid gap-4 sm:grid-cols-2">
                                            <I
                                                label="Owner name"
                                                name="owner_name"
                                                required
                                            />
                                            <I
                                                label="Owner email"
                                                name="owner_email"
                                                type="email"
                                                required
                                            />
                                        </div>
                                    ) : (
                                        <div className="mt-4 rounded-lg bg-brand-secondary-soft p-4 text-sm text-brand-secondary">
                                            <strong>
                                                No owner will be created.
                                            </strong>{" "}
                                            The Business and Store can remain
                                            Admin Managed until a Primary Owner
                                            is assigned later.
                                        </div>
                                    )}
                                    {mode === "invite" && (
                                        <div className="mt-5">
                                            <p className="text-sm font-bold">
                                                Who continues the setup?
                                            </p>
                                            <div className="mt-2 grid gap-3 sm:grid-cols-2">
                                                <button
                                                    type="button"
                                                    onClick={() => setIntent("handover")}
                                                    aria-pressed={intent === "handover"}
                                                    className={`cursor-pointer rounded-lg border p-4 text-left transition ${intent === "handover" ? "border-brand-secondary bg-brand-secondary-soft ring-1 ring-brand-secondary" : "border-border hover:border-brand-secondary/50"}`}
                                                >
                                                    <span className="block text-sm font-bold text-text-primary">Seller continues setup</span>
                                                    <span className="mt-1 block text-xs leading-5 text-text-muted">Send the invitation and let the seller create their Business Entity.</span>
                                                </button>
                                                <button
                                                    type="button"
                                                    onClick={() => setIntent("complete_setup")}
                                                    aria-pressed={intent !== "handover"}
                                                    className={`cursor-pointer rounded-lg border p-4 text-left transition ${intent !== "handover" ? "border-brand-secondary bg-brand-secondary-soft ring-1 ring-brand-secondary" : "border-border hover:border-brand-secondary/50"}`}
                                                >
                                                    <span className="block text-sm font-bold text-text-primary">Admin completes setup</span>
                                                    <span className="mt-1 block text-xs leading-5 text-text-muted">Create the Business Entity now, with the option to create its first Store.</span>
                                                </button>
                                            </div>
                                        </div>
                                    )}
                                </C>
                                {intent !== "handover" && (
                                    <C>
                                        <h2 className="text-lg font-bold">
                                            2. Business Entity
                                        </h2>
                                        <div className="mt-4 grid gap-4 sm:grid-cols-2">
                                            <label>
                                                <span className="mb-1.5 block text-sm font-bold">
                                                    Business type{" "}
                                                    <span className="text-danger">
                                                        *
                                                    </span>
                                                </span>
                                                <select
                                                    name="business_type"
                                                    defaultValue="personal_business"
                                                    className="w-full cursor-pointer rounded-lg border border-border px-3 py-2.5"
                                                >
                                                    <option value="personal_business">
                                                        Personal Business
                                                    </option>
                                                    <option value="registered_company">
                                                        Registered Company
                                                    </option>
                                                </select>
                                            </label>
                                            <I
                                                label="Business / Legal name"
                                                name="legal_name"
                                                required
                                            />
                                            <I
                                                label="Trading name"
                                                name="trading_name"
                                            />
                                            <I
                                                label="Registration number"
                                                name="registration_number"
                                            />
                                            <label>
                                                <span className="mb-1.5 block text-sm font-bold">
                                                    Country{" "}
                                                    <span className="text-danger">
                                                        *
                                                    </span>
                                                </span>
                                                <select
                                                    name="business_country_code"
                                                    defaultValue="AE"
                                                    className="w-full cursor-pointer rounded-lg border border-border px-3 py-2.5"
                                                >
                                                    <option value="AE">
                                                        United Arab Emirates
                                                    </option>
                                                    <option value="LK">
                                                        Sri Lanka
                                                    </option>
                                                </select>
                                            </label>
                                            <I
                                                label="Business phone"
                                                name="business_phone"
                                                required
                                            />
                                            <I
                                                label="WhatsApp"
                                                name="business_whatsapp"
                                            />
                                            <I
                                                label="Business email"
                                                name="business_email"
                                                type="email"
                                            />
                                            <I
                                                label="Business address"
                                                name="business_address"
                                            />
                                        </div>
                                        <div className="mt-5">
                                            <p className="text-sm font-bold text-text-primary">What should happen after this Business is saved?</p>
                                            <div className="mt-2 grid gap-3 sm:grid-cols-2">
                                                <button
                                                    type="button"
                                                    onClick={() => setIntent("business_only")}
                                                    aria-pressed={intent === "business_only"}
                                                    className={`cursor-pointer rounded-lg border p-4 text-left transition ${intent === "business_only" ? "border-brand-secondary bg-brand-secondary-soft ring-1 ring-brand-secondary" : "border-border hover:border-brand-secondary/50"}`}
                                                >
                                                    <span className="block text-sm font-bold text-text-primary">Save Business & Finish</span>
                                                    <span className="mt-1 block text-xs leading-5 text-text-muted">Create the Business only. A Store can be added later.</span>
                                                </button>
                                                <button
                                                    type="button"
                                                    onClick={() => setIntent("complete_setup")}
                                                    aria-pressed={intent === "complete_setup"}
                                                    className={`cursor-pointer rounded-lg border p-4 text-left transition ${intent === "complete_setup" ? "border-brand-secondary bg-brand-secondary-soft ring-1 ring-brand-secondary" : "border-border hover:border-brand-secondary/50"}`}
                                                >
                                                    <span className="block text-sm font-bold text-text-primary">Create Store Now</span>
                                                    <span className="mt-1 block text-xs leading-5 text-text-muted">Keep Step 3 open and create the first Store in the same setup.</span>
                                                </button>
                                            </div>
                                        </div>
                                    </C>
                                )}
                                {intent === "complete_setup" && (
                                    <C>
                                        <h2 className="text-lg font-bold">
                                            3. Store
                                        </h2>
                                        <div className="mt-4 grid gap-4 sm:grid-cols-2">
                                            <label>
                                                <span className="mb-1.5 block text-sm font-bold">
                                                    Store name{" "}
                                                    <span className="text-danger">
                                                        *
                                                    </span>
                                                </span>
                                                <input
                                                    name="store_name"
                                                    required
                                                    value={storeName}
                                                    onChange={(e) =>
                                                        setStoreName(
                                                            e.target.value,
                                                        )
                                                    }
                                                    className="w-full rounded-lg border border-border px-3 py-2.5"
                                                />
                                            </label>
                                            <label>
                                                <span className="mb-1.5 block text-sm font-bold">
                                                    Store URL{" "}
                                                    <span className="text-danger">
                                                        *
                                                    </span>
                                                </span>
                                                <input
                                                    type="hidden"
                                                    name="store_slug"
                                                    value={storeSlug}
                                                />
                                                <div className="flex overflow-hidden rounded-lg border border-border">
                                                    <span className="flex items-center border-r border-border bg-surface-subtle px-3 text-sm text-text-muted">
                                                        dubailanka.com/
                                                    </span>
                                                    <input
                                                        required
                                                        value={storeSlug}
                                                        onChange={(e) => {
                                                            setSlugTouched(
                                                                true,
                                                            );
                                                            setStoreSlug(
                                                                sanitizeStoreSlugInput(
                                                                    e.target
                                                                        .value,
                                                                ),
                                                            );
                                                        }}
                                                        className="min-w-0 flex-1 px-3 py-2.5 outline-none"
                                                    />
                                                </div>
                                                <p
                                                    className={`mt-1 text-xs font-semibold ${slugState === "available" ? "text-success" : slugState === "taken" || slugState === "invalid" ? "text-danger" : "text-text-muted"}`}
                                                >
                                                    {slugState === "checking"
                                                        ? "Checking…"
                                                        : slugState ===
                                                            "available"
                                                          ? "Available"
                                                          : slugState ===
                                                              "taken"
                                                            ? "Already in use"
                                                            : slugState ===
                                                                "invalid"
                                                              ? "Invalid Store URL"
                                                              : "Letters, numbers and hyphens only."}
                                                </p>
                                            </label>
                                            <label>
                                                <span className="mb-1.5 block text-sm font-bold">
                                                    Country{" "}
                                                    <span className="text-danger">
                                                        *
                                                    </span>
                                                </span>
                                                <select
                                                    name="store_country_code"
                                                    defaultValue="AE"
                                                    className="w-full cursor-pointer rounded-lg border border-border px-3 py-2.5"
                                                >
                                                    <option value="AE">
                                                        United Arab Emirates
                                                    </option>
                                                    <option value="LK">
                                                        Sri Lanka
                                                    </option>
                                                </select>
                                            </label>
                                            <I label="City" name="store_city" />
                                            <I
                                                label="Store phone"
                                                name="store_phone"
                                                required
                                            />
                                            <I
                                                label="WhatsApp"
                                                name="store_whatsapp"
                                            />
                                            <I
                                                label="Store email"
                                                name="store_email"
                                                type="email"
                                            />
                                            <I
                                                label="Website"
                                                name="store_website"
                                            />
                                            <I
                                                label="Physical address"
                                                name="store_address"
                                            />
                                        </div>
                                        <label className="mt-4 block">
                                            <span className="mb-1.5 block text-sm font-bold">
                                                Description
                                            </span>
                                            <textarea
                                                name="store_description"
                                                rows={4}
                                                className="w-full rounded-lg border border-border px-3 py-2.5"
                                            />
                                        </label>
                                    </C>
                                )}

                                <div className="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <Link
                                        href="/admin/onboarding"
                                        className="inline-flex min-h-11 cursor-pointer items-center justify-center rounded-lg border border-border px-4 py-2.5 text-sm font-bold"
                                    >
                                        Cancel
                                    </Link>
                                    <button
                                        disabled={
                                            processing ||
                                            (mode === "existing" &&
                                                ownerLookupState !== "found") ||
                                            (intent === "complete_setup" &&
                                                [
                                                    "taken",
                                                    "invalid",
                                                    "checking",
                                                ].includes(slugState))
                                        }
                                        className="inline-flex min-h-11 cursor-pointer items-center justify-center rounded-lg bg-brand-secondary px-5 py-2.5 text-sm font-bold text-white disabled:cursor-not-allowed disabled:opacity-60"
                                    >
                                        {processing
                                            ? "Saving..."
                                            : intent === "handover"
                                              ? "Hand Over to Seller"
                                              : intent === "business_only"
                                                ? "Save Business & Finish"
                                                : "Create Business & Store"}
                                    </button>
                                </div>
                            </>
                        )}
                    </Form>
                </div>
            </main>
        </AdminLayout>
    );
}
