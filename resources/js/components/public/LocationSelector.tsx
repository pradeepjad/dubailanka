import { useEffect, useState } from "react";
import { ChevronDown, MapPin, X } from "lucide-react";

import Button from "../ui/Button";
import Select from "../ui/Select";

interface LocationSelectorProps {
    compact?: boolean;
}

export default function LocationSelector({
    compact = false,
}: LocationSelectorProps) {
    const [isOpen, setIsOpen] = useState(false);

    const [country, setCountry] = useState("uae");
    const [region, setRegion] = useState("dubai");
    const [area, setArea] = useState("");

    const [appliedLocation, setAppliedLocation] = useState("Dubai, UAE");

    useEffect(() => {
        if (!isOpen) {
            return;
        }

        const handleKeyDown = (event: KeyboardEvent) => {
            if (event.key === "Escape") {
                setIsOpen(false);
            }
        };

        document.addEventListener("keydown", handleKeyDown);

        return () => {
            document.removeEventListener("keydown", handleKeyDown);
        };
    }, [isOpen]);

    const handleCountryChange = (value: string) => {
        setCountry(value);
        setArea("");

        if (value === "uae") {
            setRegion("dubai");
        } else {
            setRegion("western");
        }
    };

    const handleApply = () => {
        const countryLabel = country === "uae" ? "UAE" : "Sri Lanka";

        const regionLabel =
            country === "uae"
                ? {
                      dubai: "Dubai",
                      abudhabi: "Abu Dhabi",
                      sharjah: "Sharjah",
                  }[region]
                : {
                      western: "Western Province",
                      central: "Central Province",
                      southern: "Southern Province",
                  }[region];

        setAppliedLocation(
            regionLabel ? `${regionLabel}, ${countryLabel}` : countryLabel,
        );

        setIsOpen(false);
    };

    return (
        <>
            <button
                type="button"
                onClick={() => setIsOpen(true)}
                className={[
                    "inline-flex items-center text-left transition-colors",
                    compact
                        ? "gap-1.5 text-xs font-medium text-text-secondary hover:text-text-primary"
                        : "min-h-9 max-w-full gap-2 rounded-md text-sm text-text-secondary",
                ].join(" ")}
                aria-haspopup="dialog"
                aria-expanded={isOpen}
            >
                <MapPin
                    size={compact ? 14 : 17}
                    strokeWidth={1.8}
                    className="shrink-0 text-brand-secondary"
                />

                <span className="min-w-0">
                    {!compact && (
                        <span className="mr-1 text-xs text-text-muted">
                            Location:
                        </span>
                    )}

                    <span className="font-semibold text-text-primary">
                        {appliedLocation}
                    </span>
                </span>

                <ChevronDown
                    size={14}
                    strokeWidth={1.8}
                    className="shrink-0 text-text-muted"
                />
            </button>

            {isOpen && (
                <div
                    className="fixed inset-0 z-[100] flex items-end justify-center bg-black/35 sm:items-center sm:p-4"
                    role="presentation"
                    onMouseDown={(event) => {
                        if (event.target === event.currentTarget) {
                            setIsOpen(false);
                        }
                    }}
                >
                    <div
                        role="dialog"
                        aria-modal="true"
                        aria-labelledby="location-dialog-title"
                        className="w-full rounded-t-xl border border-border bg-surface sm:max-w-md sm:rounded-xl"
                    >
                        <div className="flex items-start justify-between border-b border-border px-5 py-4">
                            <div className="pr-4">
                                <h2
                                    id="location-dialog-title"
                                    className="text-lg font-bold text-text-primary"
                                >
                                    Choose your location
                                </h2>

                                <p className="mt-1 text-sm text-text-secondary">
                                    Help us show relevant products, services,
                                    stores and classifieds.
                                </p>
                            </div>

                            <button
                                type="button"
                                onClick={() => setIsOpen(false)}
                                aria-label="Close location selector"
                                className="flex size-9 shrink-0 items-center justify-center rounded-md text-text-muted transition-colors hover:bg-surface-muted hover:text-text-primary"
                            >
                                <X size={19} />
                            </button>
                        </div>

                        <div className="space-y-5 p-5">
                            <Select
                                id="location-country"
                                label="Country"
                                value={country}
                                onChange={(event) =>
                                    handleCountryChange(event.target.value)
                                }
                            >
                                <option value="uae">
                                    United Arab Emirates
                                </option>

                                <option value="sri-lanka">Sri Lanka</option>
                            </Select>

                            <Select
                                id="location-region"
                                label={
                                    country === "uae" ? "Emirate" : "Province"
                                }
                                value={region}
                                onChange={(event) =>
                                    setRegion(event.target.value)
                                }
                            >
                                {country === "uae" ? (
                                    <>
                                        <option value="dubai">Dubai</option>
                                        <option value="abudhabi">
                                            Abu Dhabi
                                        </option>
                                        <option value="sharjah">Sharjah</option>
                                    </>
                                ) : (
                                    <>
                                        <option value="western">
                                            Western Province
                                        </option>
                                        <option value="central">
                                            Central Province
                                        </option>
                                        <option value="southern">
                                            Southern Province
                                        </option>
                                    </>
                                )}
                            </Select>

                            <Select
                                id="location-area"
                                label="City / Area"
                                value={area}
                                onChange={(event) =>
                                    setArea(event.target.value)
                                }
                                hint="Optional for browsing. Delivery addresses will be handled separately."
                            >
                                <option value="">Select area (optional)</option>

                                {country === "uae" ? (
                                    <>
                                        <option value="deira">Deira</option>
                                        <option value="bur-dubai">
                                            Bur Dubai
                                        </option>
                                        <option value="business-bay">
                                            Business Bay
                                        </option>
                                    </>
                                ) : (
                                    <>
                                        <option value="colombo">Colombo</option>
                                        <option value="kandy">Kandy</option>
                                        <option value="galle">Galle</option>
                                    </>
                                )}
                            </Select>

                            <Button
                                type="button"
                                fullWidth
                                onClick={handleApply}
                            >
                                Apply Location
                            </Button>

                            <p className="text-center text-xs leading-5 text-text-muted">
                                Your browsing location is different from your
                                delivery address. We won't request precise
                                device location just to browse Dubai Lanka.
                            </p>
                        </div>
                    </div>
                </div>
            )}
        </>
    );
}
