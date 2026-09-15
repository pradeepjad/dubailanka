import { HTMLAttributes, ReactNode } from "react";

type StatusBadgeVariant = "neutral" | "success" | "warning" | "danger" | "info";

interface StatusBadgeProps extends HTMLAttributes<HTMLSpanElement> {
    children: ReactNode;
    variant?: StatusBadgeVariant;
    dot?: boolean;
}

const variantClasses: Record<StatusBadgeVariant, string> = {
    neutral: "border-border bg-surface-muted text-text-secondary",

    success: "border-success/20 bg-success-soft text-success",

    warning: "border-warning/20 bg-warning-soft text-warning",

    danger: "border-danger/20 bg-danger-soft text-danger",

    info: "border-brand-secondary/20 bg-brand-secondary-soft text-brand-secondary",
};

const dotClasses: Record<StatusBadgeVariant, string> = {
    neutral: "bg-text-muted",
    success: "bg-success",
    warning: "bg-warning",
    danger: "bg-danger",
    info: "bg-brand-secondary",
};

export default function StatusBadge({
    children,
    variant = "neutral",
    dot = false,
    className = "",
    ...props
}: StatusBadgeProps) {
    return (
        <span
            className={[
                "inline-flex min-h-6 items-center gap-1.5 rounded-full border px-2.5 py-0.5",
                "text-xs font-semibold leading-5",
                variantClasses[variant],
                className,
            ]
                .filter(Boolean)
                .join(" ")}
            {...props}
        >
            {dot && (
                <span
                    aria-hidden="true"
                    className={[
                        "size-1.5 shrink-0 rounded-full",
                        dotClasses[variant],
                    ].join(" ")}
                />
            )}

            {children}
        </span>
    );
}
