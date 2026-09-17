import { useEffect, useState } from 'react';

type Props = {
    initialSeconds?: number;
    onResend: (done: () => void) => void;
};

export default function ResendControl({ initialSeconds = 0, onResend }: Props) {
    const [seconds, setSeconds] = useState(Math.max(0, initialSeconds));
    const [sending, setSending] = useState(false);

    useEffect(() => setSeconds(Math.max(0, initialSeconds)), [initialSeconds]);

    useEffect(() => {
        if (seconds <= 0) return;
        const timer = window.setInterval(() => setSeconds(value => Math.max(0, value - 1)), 1000);
        return () => window.clearInterval(timer);
    }, [seconds]);

    if (seconds > 0) {
        return <span className="text-[#9ca3af]">Resend code available in {seconds} seconds</span>;
    }

    return (
        <button
            type="button"
            disabled={sending}
            onClick={() => {
                setSending(true);
                onResend(() => setSending(false));
            }}
            className="font-semibold text-[#005bff] disabled:opacity-60"
        >
            {sending ? 'Sending...' : 'Resend verification code'}
        </button>
    );
}
