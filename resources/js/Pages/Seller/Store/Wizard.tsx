import { Form, Head, Link, usePage } from "@inertiajs/react";
import { ArrowLeft, ArrowRight, Check, CheckCircle2, ImagePlus, Save, Store as StoreIcon } from "lucide-react";
import { ChangeEvent, useEffect, useMemo, useState } from "react";
import PublicLayout from "../../../layouts/PublicLayout";
import Button from "../../../components/ui/Button";
import Input from "../../../components/ui/Input";
import Select from "../../../components/ui/Select";

type Business = { id: number; name: string; legal_name: string; country_code: string };
type StoreDraft = {
    id: number; business_entity_id: number; name: string; slug: string; description?: string | null;
    country_code: string; city?: string | null; address?: string | null; phone: string; whatsapp?: string | null;
    email: string; website?: string | null; business_hours?: string | null; logo_url?: string | null; cover_url?: string | null; status: string;
};
type Props = { businesses: Business[]; selectedBusinessId: number; store: StoreDraft | null; flash?: { success?: string } };

const steps = ["Store Identity", "Contact & Location", "Branding", "Store Details", "Review"];
const slugify = (value: string) => value.toLowerCase().trim().replace(/[^a-z0-9]+/g, "-").replace(/^-+|-+$/g, "").slice(0, 120);
const sanitizeSlugInput = (value: string) => value
    .toLowerCase()
    .replace(/\s+/g, "-")
    .replace(/[^a-z0-9-]/g, "")
    .replace(/-{2,}/g, "-")
    .replace(/^-+/, "")
    .slice(0, 120);

export default function Wizard({ businesses, selectedBusinessId, store }: Props) {
    const { props } = usePage<Props>();
    const [step, setStep] = useState(0);
    const [businessId, setBusinessId] = useState(String(store?.business_entity_id ?? selectedBusinessId));
    const selectedBusiness = businesses.find((business) => String(business.id) === businessId) ?? businesses[0];
    const [name, setName] = useState(store?.name ?? "");
    const [slug, setSlug] = useState(store?.slug ?? "");
    const [slugTouched, setSlugTouched] = useState(Boolean(store));
    const [slugState, setSlugState] = useState<"idle" | "checking" | "available" | "taken" | "invalid">("idle");
    const [countryCode, setCountryCode] = useState(store?.country_code ?? selectedBusiness?.country_code ?? "AE");
    const [city, setCity] = useState(store?.city ?? "");
    const [address, setAddress] = useState(store?.address ?? "");
    const [phone, setPhone] = useState(store?.phone ?? "");
    const [whatsapp, setWhatsapp] = useState(store?.whatsapp ?? "");
    const [email, setEmail] = useState(store?.email ?? "");
    const [website, setWebsite] = useState(store?.website ?? "");
    const [description, setDescription] = useState(store?.description ?? "");
    const [businessHours, setBusinessHours] = useState(store?.business_hours ?? "");
    const [logoName, setLogoName] = useState("");
    const [coverName, setCoverName] = useState("");
    const [logoFile, setLogoFile] = useState<File | null>(null);
    const [coverFile, setCoverFile] = useState<File | null>(null);

    const phonePlaceholder = countryCode === "LK" ? "+94 77 123 4567" : "+971 50 123 4567";
    const action = store ? `/seller/stores/${store.id}` : "/seller/stores";
    const method = store ? "put" : "post";

    useEffect(() => {
        if (!slugTouched) setSlug(slugify(name));
    }, [name, slugTouched]);

    useEffect(() => {
        if (!slug) { setSlugState("idle"); return; }
        const timer = window.setTimeout(async () => {
            setSlugState("checking");
            const params = new URLSearchParams({ slug });
            if (store) params.set("ignore", String(store.id));
            try {
                const response = await fetch(`/seller/stores/slug-availability?${params.toString()}`, { headers: { Accept: "application/json" } });
                const result = await response.json();
                setSlugState(!result.valid ? "invalid" : result.available ? "available" : "taken");
            } catch { setSlugState("idle"); }
        }, 350);
        return () => window.clearTimeout(timer);
    }, [slug, store?.id]);

    const summary = useMemo(() => ({ business: selectedBusiness?.name, name, slug, countryCode, city, address, phone, whatsapp, email, website, description, businessHours }), [selectedBusiness, name, slug, countryCode, city, address, phone, whatsapp, email, website, description, businessHours]);

    const next = () => setStep((value) => Math.min(value + 1, steps.length - 1));
    const back = () => setStep((value) => Math.max(value - 1, 0));
    const selectFile = (event: ChangeEvent<HTMLInputElement>, type: "logo" | "cover") => {
        const file = event.target.files?.[0] ?? null;
        if (type === "logo") { setLogoFile(file); setLogoName(file?.name ?? ""); }
        else { setCoverFile(file); setCoverName(file?.name ?? ""); }
    };

    return <PublicLayout>
        <Head title={`${store ? "Edit" : "Create"} Store | Dubai Lanka`} />
        <main className="bg-surface-subtle py-8 sm:py-12">
            <div className="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                <Link href="/seller/start" className="inline-flex cursor-pointer items-center gap-2 text-sm font-semibold text-text-secondary hover:text-brand-secondary"><ArrowLeft size={16}/> Seller setup</Link>
                {props.flash?.success && <div className="mt-5 flex items-start gap-3 rounded-lg border border-success/20 bg-success-soft p-4 text-sm text-success"><CheckCircle2 className="mt-0.5 size-5 shrink-0"/>{props.flash.success}</div>}
                <div className="mt-5 rounded-xl border border-border bg-surface p-5 sm:p-8">
                    <div className="flex items-start gap-4"><div className="flex size-11 shrink-0 items-center justify-center rounded-lg bg-brand-secondary-soft text-brand-secondary"><StoreIcon size={22}/></div><div><p className="text-xs font-bold uppercase tracking-wide text-brand-secondary">Store setup · Draft</p><h1 className="mt-1 text-2xl font-bold text-text-primary sm:text-3xl">{store ? `Edit ${store.name}` : "Create your store"}</h1><p className="mt-2 text-sm leading-6 text-text-secondary">Complete each step, review your information, then save the store as a draft. You can return and edit it anytime.</p></div></div>

                    <div className="mt-8 grid grid-cols-5 gap-2" aria-label="Store setup progress">{steps.map((label,index)=><button type="button" key={label} onClick={()=>setStep(index)} className="cursor-pointer text-left"><div className={`h-1.5 rounded-full ${index <= step ? "bg-brand-secondary" : "bg-surface-muted"}`}/><span className={`mt-2 hidden text-xs font-semibold sm:block ${index===step ? "text-brand-secondary":"text-text-muted"}`}>{index+1}. {label}</span></button>)}</div>

                    <Form action={action} method={method} encType="multipart/form-data" className="mt-8" disableWhileProcessing transform={(data) => ({ ...data, ...(logoFile ? { logo: logoFile } : {}), ...(coverFile ? { cover: coverFile } : {}) })}>
                        {({errors,processing}) => <>
                            <input type="hidden" name="business_entity_id" value={businessId}/><input type="hidden" name="name" value={name}/><input type="hidden" name="slug" value={slug}/><input type="hidden" name="country_code" value={countryCode}/><input type="hidden" name="city" value={city}/><input type="hidden" name="address" value={address}/><input type="hidden" name="phone" value={phone}/><input type="hidden" name="whatsapp" value={whatsapp}/><input type="hidden" name="email" value={email}/><input type="hidden" name="website" value={website}/><input type="hidden" name="description" value={description}/><input type="hidden" name="business_hours" value={businessHours}/>

                            {step===0 && <section className="grid gap-5 sm:grid-cols-2"><div className="sm:col-span-2"><Select label="Business Owner" value={businessId} onChange={e=>{setBusinessId(e.target.value); const b=businesses.find(x=>String(x.id)===e.target.value); if(b && !store) setCountryCode(b.country_code)}} required>{businesses.map(b=><option key={b.id} value={b.id}>{b.name}</option>)}</Select></div><div className="sm:col-span-2"><Input label="Store Name" value={name} onChange={e=>setName(e.target.value)} error={errors.name} placeholder="Example: Ceylon Gifts" required/></div><div className="sm:col-span-2">
                                    <Input
                                        label="Unique Store Address"
                                        value={slug}
                                        onChange={e=>{setSlugTouched(true);setSlug(sanitizeSlugInput(e.target.value))}}
                                        error={errors.slug}
                                        placeholder="ceylon-gifts"
                                        hint={slugState==="checking"?"Checking availability…":slugState==="available"?"Available — this store address can be used.":slugState==="taken"?"Already taken — please choose another store address.":slugState==="invalid"?"Use lowercase letters, numbers and hyphens only.":store ? "Draft store address can be changed. The new address must be available." : "This will become your unique Dubai Lanka store address."}
                                        required
                                    />
                                    <div className="mt-2 flex flex-wrap items-center justify-between gap-2">
                                        <p className={`text-xs font-semibold ${slugState==="available"?"text-success":slugState==="taken"||slugState==="invalid"?"text-danger":"text-text-muted"}`}>
                                            {slug && `dubailanka.com/${slug}`}
                                        </p>
                                        {slugTouched && (
                                            <button
                                                type="button"
                                                onClick={()=>{setSlugTouched(false);setSlug(slugify(name));}}
                                                className="cursor-pointer text-xs font-semibold text-brand-secondary hover:underline"
                                            >
                                                Reset from store name
                                            </button>
                                        )}
                                    </div>
                                </div></section>}

                            {step===1 && <section className="grid gap-5 sm:grid-cols-2"><Select label="Country" value={countryCode} onChange={e=>setCountryCode(e.target.value)} error={errors.country_code} required><option value="AE">United Arab Emirates</option><option value="LK">Sri Lanka</option></Select><Input label="City / Area" value={city} onChange={e=>setCity(e.target.value)} error={errors.city} placeholder={countryCode==="LK"?"Colombo":"Dubai"}/><div className="sm:col-span-2"><Input label="Store Address" value={address} onChange={e=>setAddress(e.target.value)} error={errors.address} placeholder="Building, street, area"/></div><Input label="Store Phone" value={phone} onChange={e=>setPhone(e.target.value)} error={errors.phone} placeholder={phonePlaceholder} required/><Input label="WhatsApp (Optional)" value={whatsapp} onChange={e=>setWhatsapp(e.target.value)} error={errors.whatsapp} placeholder={phonePlaceholder}/><Input type="email" label="Store Email" value={email} onChange={e=>setEmail(e.target.value)} error={errors.email} placeholder="store@example.com" required/><Input label="Website (Optional)" value={website} onChange={e=>setWebsite(e.target.value)} error={errors.website} placeholder="https://example.com"/></section>}

                            {step===2 && <section className="grid gap-5"><div><label className="mb-1.5 block text-sm font-semibold text-text-primary">Store Logo</label><label className="flex cursor-pointer items-center gap-3 rounded-lg border border-dashed border-border-strong p-4 hover:border-brand-secondary"><ImagePlus className="text-brand-secondary"/><span className="text-sm text-text-secondary">{logoName || (store?.logo_url ? "Choose a new logo to replace the current one" : "Choose JPG, PNG or WebP · max 3 MB")}</span><input className="sr-only" type="file" accept="image/jpeg,image/png,image/webp" onChange={e=>selectFile(e,"logo")}/></label>{errors.logo&&<p className="mt-1.5 text-sm text-danger">{errors.logo}</p>}</div><div><label className="mb-1.5 block text-sm font-semibold text-text-primary">Cover Image</label><label className="flex cursor-pointer items-center gap-3 rounded-lg border border-dashed border-border-strong p-4 hover:border-brand-secondary"><ImagePlus className="text-brand-secondary"/><span className="text-sm text-text-secondary">{coverName || (store?.cover_url ? "Choose a new cover to replace the current one" : "Choose JPG, PNG or WebP · max 5 MB")}</span><input className="sr-only" type="file" accept="image/jpeg,image/png,image/webp" onChange={e=>selectFile(e,"cover")}/></label>{errors.cover&&<p className="mt-1.5 text-sm text-danger">{errors.cover}</p>}</div><div><label className="mb-1.5 block text-sm font-semibold text-text-primary">Store Description</label><textarea value={description} onChange={e=>setDescription(e.target.value)} rows={6} maxLength={2000} className="w-full rounded-md border border-border-strong bg-surface px-3 py-2 text-sm text-text-primary focus:border-brand-secondary focus:outline-none focus:ring-2 focus:ring-brand-secondary/15" placeholder="Tell customers what your store offers and what makes it special."/><div className="mt-1 flex justify-between text-xs text-text-muted"><span>{errors.description}</span><span>{description.length}/2000</span></div></div></section>}

                            {step===3 && <section className="grid gap-5"><div><label className="mb-1.5 block text-sm font-semibold text-text-primary">Business Hours (Optional)</label><textarea value={businessHours} onChange={e=>setBusinessHours(e.target.value)} rows={5} maxLength={500} className="w-full rounded-md border border-border-strong bg-surface px-3 py-2 text-sm text-text-primary focus:border-brand-secondary focus:outline-none focus:ring-2 focus:ring-brand-secondary/15" placeholder={countryCode==="LK"?"Mon–Sat: 9:00 AM – 6:00 PM":"Mon–Sat: 9:00 AM – 7:00 PM"}/><p className="mt-1.5 text-xs text-text-muted">Keep this simple for now. Detailed scheduling can be improved later.</p>{errors.business_hours&&<p className="mt-1.5 text-sm text-danger">{errors.business_hours}</p>}</div><div className="rounded-lg border border-info/20 bg-info-soft p-4 text-sm leading-6 text-text-secondary"><strong className="text-text-primary">Not included in 03.02:</strong> delivery zones, verification, package limits and store approval. Those stay in their planned milestones.</div></section>}

                            {step===4 && <section><h2 className="text-lg font-bold text-text-primary">Review your store draft</h2><p className="mt-1 text-sm text-text-muted">Nothing becomes public at this stage.</p><div className="mt-5 grid gap-3 sm:grid-cols-2">{Object.entries(summary).map(([key,value])=>value?<div key={key} className="rounded-lg border border-border p-4"><p className="text-xs font-bold uppercase tracking-wide text-text-muted">{key.replace(/([A-Z])/g," $1").replace(/^./,s=>s.toUpperCase())}</p><p className="mt-1 break-words text-sm font-semibold text-text-primary">{String(value)}</p></div>:null)}</div><div className="mt-5 rounded-lg border border-warning/20 bg-warning-soft p-4 text-sm text-text-secondary"><div className="flex gap-2"><Check className="mt-0.5 size-4 shrink-0 text-warning"/><span>Saving creates or updates a <strong>Draft</strong> only. Submission and Admin approval will be added in 03.04.</span></div></div></section>}

                            <div className="mt-8 flex flex-wrap items-center justify-between gap-3 border-t border-border pt-5"><div>{step>0&&<Button type="button" variant="outline" onClick={back}><ArrowLeft size={16}/> Back</Button>}</div><div className="flex flex-wrap gap-3">{store && step<4 && <Button type="submit" variant="outline" loading={processing}><Save size={16}/> Save Draft</Button>}{step<4?<Button type="button" variant="secondary" onClick={next}>Next <ArrowRight size={16}/></Button>:<Button type="submit" variant="secondary" loading={processing} disabled={slugState==="taken"||slugState==="invalid"}><Save size={16}/> {store?"Update Store Draft":"Save Store Draft"}</Button>}</div></div>
                        </>}
                    </Form>
                </div>
            </div>
        </main>
    </PublicLayout>;
}
