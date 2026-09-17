import { forwardRef, InputHTMLAttributes, ReactNode } from "react";

interface InputProps extends InputHTMLAttributes<HTMLInputElement> {
    label?: string;
    error?: string;
    hint?: string;
    startIcon?: ReactNode;
    endIcon?: ReactNode;
}

const Input = forwardRef<HTMLInputElement, InputProps>(
    (
        {
            label,
            error,
            hint,
            startIcon,
            endIcon,
            id,
            className = "",
            disabled,
            required,
            ...props
        },
        ref,
    ) => {
        const inputId =
            id || (typeof props.name === "string" ? props.name : undefined);

        const describedBy = error
            ? inputId
                ? `${inputId}-error`
                : undefined
            : hint
              ? inputId
                  ? `${inputId}-hint`
                  : undefined
              : undefined;

        return (
            <div className="w-full">
                {label && (
                    <label
                        htmlFor={inputId}
                        className="mb-1.5 block text-sm font-semibold text-text-primary"
                    >
                        {label}
                        {required && (
                            <span className="ml-1 text-danger" aria-hidden="true">
                                *
                            </span>
                        )}
                    </label>
                )}

                <div className="relative">
                    {startIcon && (
                        <div
                            aria-hidden="true"
                            className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-text-muted"
                        >
                            {startIcon}
                        </div>
                    )}

                    <input
                        ref={ref}
                        id={inputId}
                        disabled={disabled}
                        required={required}
                        aria-invalid={error ? true : undefined}
                        aria-describedby={describedBy}
                        className={[
                            "min-h-11 w-full rounded-md border bg-surface px-3 text-sm text-text-primary",
                            "placeholder:text-text-muted",
                            "transition-colors duration-150",
                            "focus:border-brand-secondary focus:outline-none focus:ring-2 focus:ring-brand-secondary/15",
                            "disabled:cursor-not-allowed disabled:bg-surface-muted disabled:text-text-muted",
                            error
                                ? "border-danger focus:border-danger focus:ring-danger/15"
                                : "border-border-strong",
                            startIcon ? "pl-10" : "",
                            endIcon ? "pr-10" : "",
                            className,
                        ]
                            .filter(Boolean)
                            .join(" ")}
                        {...props}
                    />

                    {endIcon && (
                        <div
                            aria-hidden="true"
                            className="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-text-muted"
                        >
                            {endIcon}
                        </div>
                    )}
                </div>

                {error ? (
                    <p
                        id={inputId ? `${inputId}-error` : undefined}
                        className="mt-1.5 text-sm text-danger"
                    >
                        {error}
                    </p>
                ) : hint ? (
                    <p
                        id={inputId ? `${inputId}-hint` : undefined}
                        className="mt-1.5 text-sm text-text-muted"
                    >
                        {hint}
                    </p>
                ) : null}
            </div>
        );
    },
);

Input.displayName = "Input";

export default Input;
