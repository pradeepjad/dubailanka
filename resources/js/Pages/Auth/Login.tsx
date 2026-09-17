import { Form, Link, router, usePage } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { useState } from 'react';
import AuthLayout from '../../components/auth/AuthLayout';
import OtpInput from '../../components/auth/OtpInput';
import PasswordInput from '../../components/auth/PasswordInput';
import ResendControl from '../../components/auth/ResendControl';

type P = { login?: { pending_email?: string | null; resend_seconds?: number } };

export default function Login() {
    const { props } = usePage<P>();
    const pending = props.login?.pending_email ?? '';
    const [mode, setMode] = useState<'password' | 'otp'>(pending ? 'otp' : 'password');
    const [email, setEmail] = useState('');
    const [code, setCode] = useState('');

    return <AuthLayout title="Log in">
        <div className="mb-8"><p className="mb-3 text-sm font-semibold text-[#005bff]">Welcome back</p><h1 className="text-4xl font-bold text-[#111827]">Log in</h1><p className="mt-3 text-sm text-[#6b7280]">Access your Dubai Lanka account.</p></div>
        {mode === 'password' ?
            <Form action="/login" method="post" className="space-y-5">{({ errors, processing }) => <>
                <Field name="email" label="Email address" type="email" error={errors.email} />
                <PasswordInput name="password" label="Password" error={errors.password} />
                <div className="flex items-center justify-between text-sm"><label className="flex gap-2"><input type="checkbox" name="remember" value="1" /> Remember me</label><Link href="/forgot-password" className="font-semibold text-[#005bff]">Forgot password?</Link></div>
                <Button busy={processing} text="Log in" />
                <button type="button" onClick={() => setMode('otp')} className="w-full text-sm font-semibold text-[#005bff]">Log in with email OTP</button>
            </>}</Form>
        : pending ? <>
            <button type="button" onClick={() => router.post('/login/otp/cancel')} className="mb-5 inline-flex items-center gap-2 text-sm font-semibold text-[#6b7280]"><ArrowLeft size={16} /> Use another email</button>
            <p className="mb-2 text-sm text-[#6b7280]">If an account exists for this email, a login code was sent to <b className="text-[#111827]">{pending}</b>.</p>
            <p className="mb-5 text-xs text-[#9ca3af]">For privacy, Dubai Lanka does not confirm whether an email is registered.</p>
            <OtpInput value={code} onChange={setCode} />
            <Form action="/login/otp/verify" method="post" className="mt-5">{({ errors, processing }) => <>
                <input type="hidden" name="email" value={pending} /><input type="hidden" name="code" value={code} />
                {errors.code && <p className="mb-3 text-sm text-[#ef2929]">{errors.code}</p>}
                <Button busy={processing} text="Verify & Log in" />
            </>}</Form>
            <div className="mt-6 text-center text-sm"><ResendControl initialSeconds={props.login?.resend_seconds ?? 0} onResend={done => router.post('/login/otp/resend', {}, { preserveScroll: true, onFinish: done })} /></div>
            <button type="button" onClick={() => router.post('/login/otp/cancel')} className="mt-5 w-full text-sm font-semibold text-[#005bff]">Back to password login</button>
        </> : <div className="space-y-5">
            <Field name="email" label="Email address" type="email" value={email} onChange={setEmail} />
            <button type="button" onClick={() => router.post('/login/otp', { email })} className="w-full rounded-xl bg-[#ffb300] px-5 py-3.5 text-sm font-bold">Send login code</button>
            <button type="button" onClick={() => setMode('password')} className="w-full text-sm font-semibold text-[#005bff]">Use password instead</button>
        </div>}
        <p className="mt-8 text-center text-sm text-[#6b7280]">New to Dubai Lanka? <Link href="/register" className="font-semibold text-[#005bff]">Create account</Link></p>
    </AuthLayout>;
}
function Field({ name, label, type = 'text', error, value, onChange }: { name: string; label: string; type?: string; error?: string; value?: string; onChange?: (v: string) => void }) { return <div><label className="mb-2 block text-sm font-semibold">{label}</label><input name={name} type={type} value={value} onChange={onChange ? e => onChange(e.target.value) : undefined} className="w-full rounded-xl border border-[#d1d5db] px-4 py-3 outline-none focus:border-[#005bff]" />{error && <p className="mt-2 text-sm text-[#ef2929]">{error}</p>}</div> }
function Button({ busy, text }: { busy: boolean; text: string }) { return <button disabled={busy} className="w-full rounded-xl bg-[#ffb300] px-5 py-3.5 text-sm font-bold disabled:opacity-60">{busy ? 'Please wait...' : text}</button> }
