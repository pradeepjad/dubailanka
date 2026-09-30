import {Head,Link} from '@inertiajs/react';
import AdminLayout from '../../../layouts/AdminLayout';

type S={
    id:number;name:string;slug:string;status:string;business_name:string;country_code:string;
    updated_at:string;change_status?:string|null;
    assigned_admin?:{id:number;name:string;email:string}|null;
};

export default function Index({stores}:{stores:S[]}) {
    return <AdminLayout>
        <Head title="Store Moderation | Dubai Lanka"/>
        <main className="bg-surface-subtle py-10">
            <div className="mx-auto max-w-6xl px-4">
                <p className="text-sm font-bold text-brand-secondary">ADMIN · STORE MODERATION</p>
                <h1 className="mt-1 text-3xl font-bold">Store review queue</h1>
                <div className="mt-3"><Link href="/admin/onboarding" className="text-sm font-bold text-brand-secondary hover:underline">Seller Onboarding →</Link></div>
                <div className="mt-6 overflow-hidden rounded-xl border border-border bg-surface">
                    {stores.length===0
                        ? <p className="p-6 text-sm text-text-muted">No stores in the moderation queue yet.</p>
                        : stores.map(s=><Link key={s.id} href={`/admin/stores/${s.id}`} className="flex cursor-pointer flex-wrap items-center justify-between gap-3 border-b border-border p-4 last:border-0 hover:bg-surface-subtle">
                            <div>
                                <p className="font-bold text-text-primary">{s.name}</p>
                                <p className="mt-1 text-xs text-text-muted">{s.business_name} · /{s.slug} · {s.country_code}</p>
                                <p className="mt-1 text-xs font-semibold text-text-secondary">
                                    Responsible Admin: {s.assigned_admin ? `${s.assigned_admin.name} · ${s.assigned_admin.email}` : 'Unassigned'}
                                </p>
                            </div>
                            <div className="flex flex-wrap gap-2">
                                <span className="rounded-full bg-warning-soft px-3 py-1 text-xs font-bold capitalize text-warning">{s.status.replaceAll('_',' ')}</span>
                                {s.change_status&&<span className="rounded-full bg-brand-secondary-soft px-3 py-1 text-xs font-bold text-brand-secondary">Identity changes · {s.change_status.replaceAll('_',' ')}</span>}
                            </div>
                        </Link>)}
                </div>
            </div>
        </main>
    </AdminLayout>;
}
