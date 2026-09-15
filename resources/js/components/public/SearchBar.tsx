import { FormEvent, useState } from "react";
import { ChevronDown, Search } from "lucide-react";

interface SearchBarProps {
    mobile?: boolean;
}

const searchTypes = ["All", "Products", "Services", "Classifieds", "Stores"];

export default function SearchBar({ mobile = false }: SearchBarProps) {
    const [searchType, setSearchType] = useState("All");
    const [query, setQuery] = useState("");

    const handleSubmit = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();

        // Search functionality will be connected
        // during the Marketplace/Search milestone.
        console.log({
            type: searchType,
            query,
        });
    };

    return (
        <form onSubmit={handleSubmit} role="search" className="w-full">
            <div className="flex min-h-11 w-full overflow-hidden rounded-md border border-border-strong bg-surface transition-colors focus-within:border-brand-secondary focus-within:ring-2 focus-within:ring-brand-secondary/15">
                {!mobile && (
                    <div className="relative shrink-0 border-r border-border">
                        <select
                            value={searchType}
                            onChange={(event) =>
                                setSearchType(event.target.value)
                            }
                            aria-label="Search category"
                            className="h-full min-w-28 appearance-none bg-surface-subtle py-2 pl-3 pr-8 text-sm font-medium text-text-secondary outline-none"
                        >
                            {searchTypes.map((type) => (
                                <option key={type} value={type}>
                                    {type}
                                </option>
                            ))}
                        </select>

                        <ChevronDown
                            aria-hidden="true"
                            size={15}
                            strokeWidth={1.8}
                            className="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-text-muted"
                        />
                    </div>
                )}

                <div className="relative min-w-0 flex-1">
                    {mobile && (
                        <Search
                            aria-hidden="true"
                            size={18}
                            strokeWidth={1.8}
                            className="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-text-muted"
                        />
                    )}

                    <input
                        type="search"
                        value={query}
                        onChange={(event) => setQuery(event.target.value)}
                        placeholder={
                            mobile
                                ? "Search Dubai Lanka"
                                : "Search products, services, classifieds & stores"
                        }
                        aria-label="Search Dubai Lanka"
                        className={[
                            "h-full min-h-11 w-full border-0 bg-transparent text-sm text-text-primary outline-none placeholder:text-text-muted",
                            mobile ? "pl-10 pr-3" : "px-4",
                        ].join(" ")}
                    />
                </div>

                {!mobile && (
                    <button
                        type="submit"
                        aria-label="Search"
                        className="flex w-12 shrink-0 items-center justify-center bg-brand-primary text-text-primary transition-colors hover:bg-brand-primary-hover"
                    >
                        <Search size={19} strokeWidth={2} />
                    </button>
                )}
            </div>
        </form>
    );
}
