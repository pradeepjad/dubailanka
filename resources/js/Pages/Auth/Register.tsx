import { Form, Link, router, usePage } from '@inertiajs/react';
import { ArrowLeft, ArrowRight } from 'lucide-react';
import { useState } from 'react';
import AuthLayout from '../../components/auth/AuthLayout';
import OtpInput from '../../components/auth/OtpInput';
import PasswordInput from '../../components/auth/PasswordInput';
import ResendControl from '../../components/auth/ResendControl';

type P = { registration?: { pending?: boolean; name?: string | null; email?: string | null; verified?: boolean; resend_seconds?: number } };

export default function Register() {
    const { props } = usePage<P>();
    const r = props.registration ?? {};
    const step = r.verified ? 3 : r.pending ? 2 : 1;
    const [otp, setOtp] = useState('');

    return <AuthLayout title="Create Account">
        {step === 1 ? <>
            <Header eyebrow="Join Dubai Lanka" title="Create your account" text="One account for buying, selling and managing your business." />
            <Form action="/register" method="post" className="space-y-5">{({ errors, processing }) => <>
                <Field name="name" label="Full name" placeholder="Enter your full name" error={errors.name} />
                <Field name="email" label="Email address" type="email" placeholder="you@example.com" error={errors.email} />
                <Submit processing={processing} text="Continue" busy="Sending verification code..." />
            </>}</Form>
            <Foot href="/login" text="Already have an account?" action="Log in" />
        </> : step === 2 ? <>
            <button type="button" onClick={() => router.post('/register/cancel')} className="mb-7 inline-flex items-center gap-2 text-sm font-semibold text-[#6b7280]"><ArrowLeft size={16} /> Change email</button>
            <Header eyebrow="Email verification" title="Verify your email" text={<>We sent a 6-digit verification code to <b className="text-[#111827]">{r.email}</b></>} />
            <OtpInput value={otp} onChange={setOtp} />
            <Form action="/register/verify" method="post" className="mt-6">{({ errors, processing }) => <>
                <input type="hidden" name="email" value={r.email ?? ''} /><input type="hidden" name="code" value={otp} />
                {errors.code && <p className="mb-3 text-sm text-[#ef2929]">{errors.code}</p>}
                <Submit processing={processing} text="Verify Email" busy="Verifying..." />
            </>}</Form>
            <div className="mt-7 text-center text-sm"><ResendControl initialSeconds={r.resend_seconds ?? 0} onResend={done => router.post('/register/resend', {}, { preserveScroll: true, onFinish: done })} /></div>
        </> : <>
            <Header eyebrow="Final step" title="Create your password" text="Your email is verified. Create a password to complete your Dubai Lanka account." />
            <Form action="/register/complete" method="post" className="space-y-5">{({ errors, processing }) => <>
                <PasswordInput name="password" label="Password" autoComplete="new-password" error={errors.password} />
                <PasswordInput name="password_confirmation" label="Confirm password" autoComplete="new-password" />
                <p className="text-xs text-[#6b7280]">Use at least 8 characters.</p>
                <Submit processing={processing} text="Create Account" busy="Creating account..." />
            </>}</Form>
            <button
                type="button"
                onClick={() => router.post('/register/cancel')}
                className="mt-6 inline-flex w-full items-center justify-center gap-2 text-sm font-semibold text-[#6b7280] transition hover:text-[#005bff]"
            >
                <ArrowLeft size={16} />
                Start again with another email
            </button>
        </>}
    </AuthLayout>;
}
function Header({ eyebrow, title, text }: { eyebrow: string; title: string; text: React.ReactNode }) { return <div className="mb-8"><p className="mb-3 text-sm font-semibold text-[#005bff]">{eyebrow}</p><h1 className="text-3xl font-bold tracking-tight text-[#111827] sm:text-4xl">{title}</h1><p className="mt-3 text-sm leading-6 text-[#6b7280]">{text}</p></div> }
function Field({ name, label, type = 'text', placeholder, error }: { name: string; label: string; type?: string; placeholder?: string; error?: string }) { return <div><label className="mb-2 block text-sm font-semibold text-[#374151]">{label}</label><input name={name} type={type} placeholder={placeholder} className="w-full rounded-xl border border-[#d1d5db] px-4 py-3 outline-none focus:border-[#005bff] focus:ring-4 focus:ring-[#005bff]/10" />{error && <p className="mt-2 text-sm text-[#ef2929]">{error}</p>}</div> }
function Submit({ processing, text, busy }: { processing: boolean; text: string; busy: string }) { return <button disabled={processing} className="flex w-full items-center justify-center gap-2 rounded-xl bg-[#ffb300] px-5 py-3.5 text-sm font-bold text-[#111827] disabled:opacity-60">{processing ? busy : text}{!processing && <ArrowRight size={17} />}</button> }
function Foot({ href, text, action }: { href: string; text: string; action: string }) { return <p className="mt-7 text-center text-sm text-[#6b7280]">{text} <Link href={href} className="font-semibold text-[#005bff]">{action}</Link></p> }
