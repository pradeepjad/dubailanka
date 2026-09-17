import { Form, Link, router, usePage } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { useState } from 'react';
import AuthLayout from '../../components/auth/AuthLayout';
import OtpInput from '../../components/auth/OtpInput';
import PasswordInput from '../../components/auth/PasswordInput';
import ResendControl from '../../components/auth/ResendControl';

type P = { passwordReset?: { pending_email?: string | null; resend_seconds?: number } };

export default function ForgotPassword() {
    const { props } = usePage<P>();
    const pending = props.passwordReset?.pending_email ?? '';
    const [email, setEmail] = useState('');
    const [code, setCode] = useState('');

    return <AuthLayout title="Reset Password">
        <div className="mb-8"><p className="mb-3 text-sm font-semibold text-[#005bff]">Account recovery</p><h1 className="text-4xl font-bold">Reset password</h1><p className="mt-3 text-sm text-[#6b7280]">{pending ? 'Enter the verification code and create a new password.' : 'Enter your account email and we will send a verification code.'}</p></div>
        {!pending ? <>
            <label className="mb-2 block text-sm font-semibold">Email address</label>
            <input type="email" value={email} onChange={e => setEmail(e.target.value)} className="w-full rounded-xl border border-[#d1d5db] px-4 py-3" />
            <button type="button" onClick={() => router.post('/forgot-password', { email })} className="mt-5 w-full rounded-xl bg-[#ffb300] px-5 py-3.5 text-sm font-bold">Send verification code</button>
            <p className="mt-6 text-center text-sm text-[#6b7280]"><Link href="/login" className="font-semibold text-[#005bff]">Back to login</Link></p>
        </> : <>
            <button type="button" onClick={() => router.post('/forgot-password/cancel')} className="mb-5 inline-flex items-center gap-2 text-sm font-semibold text-[#6b7280]"><ArrowLeft size={16} /> Use another email</button>
            <p className="mb-2 text-sm text-[#6b7280]">If an eligible account exists, a verification code was sent to <b className="text-[#111827]">{pending}</b>.</p>
            <p className="mb-5 text-xs text-[#9ca3af]">For privacy, Dubai Lanka does not confirm whether an email is registered.</p>
            <OtpInput value={code} onChange={setCode} />
            <Form action="/reset-password" method="post" className="mt-5 space-y-5">{({ errors, processing }) => <>
                <input type="hidden" name="email" value={pending} /><input type="hidden" name="code" value={code} />
                {errors.code && <p className="text-sm text-[#ef2929]">{errors.code}</p>}
                <PasswordInput name="password" label="New password" autoComplete="new-password" error={errors.password} />
                <PasswordInput name="password_confirmation" label="Confirm new password" autoComplete="new-password" />
                <button disabled={processing} className="w-full rounded-xl bg-[#ffb300] px-5 py-3.5 text-sm font-bold">{processing ? 'Resetting...' : 'Reset Password'}</button>
            </>}</Form>
            <div className="mt-6 text-center text-sm"><ResendControl initialSeconds={props.passwordReset?.resend_seconds ?? 0} onResend={done => router.post('/forgot-password/resend', {}, { preserveScroll: true, onFinish: done })} /></div>
            <button type="button" onClick={() => router.post('/forgot-password/cancel')} className="mt-5 w-full text-sm font-semibold text-[#005bff]">Start again with another email</button>
        </>}
    </AuthLayout>;
}
