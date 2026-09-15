import { forwardRef, ReactNode, SelectHTMLAttributes } from "react";

interface SelectProps extends SelectHTMLAttributes<HTMLSelectElement> {
    label?: string;
    error?: string;
    hint?: string;
    children: ReactNode;
}

const Select = forwardRef<HTMLSelectElement, SelectProps>(
    (
        {
            label,
            error,
            hint,
            children,
            id,
            className = "",
            disabled,
            ...props
        },
        ref,
    ) => {
        const selectId =
            id || (typeof props.name === "string" ? props.name : undefined);

        const describedBy = error
            ? selectId
                ? `${selectId}-error`
                : undefined
            : hint
              ? selectId
                  ? `${selectId}-hint`
                  : undefined
              : undefined;

        return (
            <div className="w-full">
                {label && (
                    <label
                        htmlFor={selectId}
                        className="mb-1.5 block text-sm font-semibold text-text-primary"
                    >
                        {label}
                    </label>
                )}

                <div className="relative">
                    <select
                        ref={ref}
                        id={selectId}
                        disabled={disabled}
                        aria-invalid={error ? true : undefined}
                        aria-describedby={describedBy}
                        className={[
                            "min-h-11 w-full appearance-none rounded-md border bg-surface px-3 pr-10 text-sm text-text-primary",
                            "transition-colors duration-150",
                            "focus:border-brand-secondary focus:outline-none focus:ring-2 focus:ring-brand-secondary/15",
                            "disabled:cursor-not-allowed disabled:bg-surface-muted disabled:text-text-muted",
                            error
                                ? "border-danger focus:border-danger focus:ring-danger/15"
                                : "border-border-strong",
                            className,
                        ]
                            .filter(Boolean)
                            .join(" ")}
                        {...props}
                    >
                        {children}
                    </select>

                    <svg
                        aria-hidden="true"
                        viewBox="0 0 20 20"
                        fill="none"
                        className="pointer-events-none absolute right-3 top-1/2 size-4 -translate-y-1/2 text-text-muted"
                    >
                        <path
                            d="M5 7.5L10 12.5L15 7.5"
                            stroke="currentColor"
                            strokeWidth="1.5"
                            strokeLinecap="round"
                            strokeLinejoin="round"
                        />
                    </svg>
                </div>

                {error ? (
                    <p
                        id={selectId ? `${selectId}-error` : undefined}
                        className="mt-1.5 text-sm text-danger"
                    >
                        {error}
                    </p>
                ) : hint ? (
                    <p
                        id={selectId ? `${selectId}-hint` : undefined}
                        className="mt-1.5 text-sm text-text-muted"
                    >
                        {hint}
                    </p>
                ) : null}
            </div>
        );
    },
);

Select.displayName = "Select";

export default Select;
