import { ButtonHTMLAttributes, ReactNode } from "react";

type ButtonVariant = "primary" | "secondary" | "outline" | "ghost" | "danger";

type ButtonSize = "sm" | "md" | "lg";

interface ButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
    children: ReactNode;
    variant?: ButtonVariant;
    size?: ButtonSize;
    fullWidth?: boolean;
    loading?: boolean;
}

const variantClasses: Record<ButtonVariant, string> = {
    primary:
        "border-brand-primary bg-brand-primary text-text-primary hover:bg-brand-primary-hover hover:border-brand-primary-hover",

    secondary:
        "border-brand-secondary bg-brand-secondary text-white hover:bg-brand-secondary-hover hover:border-brand-secondary-hover",

    outline:
        "border-border-strong bg-surface text-text-primary hover:border-text-secondary hover:bg-surface-subtle",

    ghost: "border-transparent bg-transparent text-text-secondary hover:bg-surface-muted hover:text-text-primary",

    danger: "border-danger bg-danger text-white hover:opacity-90",
};

const sizeClasses: Record<ButtonSize, string> = {
    sm: "min-h-9 px-3 text-sm",
    md: "min-h-11 px-4 text-sm",
    lg: "min-h-12 px-5 text-base",
};

export default function Button({
    children,
    variant = "primary",
    size = "md",
    fullWidth = false,
    loading = false,
    disabled,
    className = "",
    type = "button",
    ...props
}: ButtonProps) {
    const isDisabled = disabled || loading;

    return (
        <button
            type={type}
            disabled={isDisabled}
            aria-busy={loading || undefined}
            className={[
                "inline-flex items-center justify-center gap-2 rounded-md border font-semibold",
                "transition-colors duration-150",
                "focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-secondary focus-visible:ring-offset-2",
                "disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50",
                sizeClasses[size],
                variantClasses[variant],
                fullWidth ? "w-full" : "",
                className,
            ]
                .filter(Boolean)
                .join(" ")}
            {...props}
        >
            {loading && (
                <span
                    aria-hidden="true"
                    className="size-4 animate-spin rounded-full border-2 border-current border-r-transparent"
                />
            )}

            {children}
        </button>
    );
}
