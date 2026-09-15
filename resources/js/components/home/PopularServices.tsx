import { ChevronRight } from "lucide-react";

import ServiceCard, { ServiceCardData } from "../marketplace/ServiceCard";

const popularServices: ServiceCardData[] = [
    {
        id: 201,
        title: "AC Maintenance & Repair",
        providerName: "Lanka Technical Services",
        verified: true,
        category: "Home Services",
        serviceArea: "Dubai, UAE",
        pricingMode: "starting_from",
        currency: "AED",
        price: 120,
        rating: 4.8,
        reviewCount: 37,
        featured: true,
        href: "#",
    },
    {
        id: 202,
        title: "Business Setup Consultation in the UAE",
        providerName: "Lanka Business Solutions",
        verified: true,
        category: "Business Services",
        serviceArea: "UAE",
        pricingMode: "price_on_request",
        rating: 4.9,
        reviewCount: 24,
        href: "#",
    },
    {
        id: 203,
        title: "Website Design & Development",
        providerName: "Digital Lanka Studio",
        verified: true,
        category: "IT & Digital Services",
        serviceArea: "Online",
        pricingMode: "starting_from",
        currency: "AED",
        price: 750,
        rating: 4.7,
        reviewCount: 18,
        href: "#",
    },
    {
        id: 204,
        title: "Sri Lankan Catering for Events",
        providerName: "Ceylon Kitchen Dubai",
        verified: true,
        category: "Food & Catering",
        serviceArea: "Dubai, UAE",
        pricingMode: "contact_provider",
        rating: 4.9,
        reviewCount: 42,
        sponsored: true,
        href: "#",
    },
    {
        id: 205,
        title: "Professional Home Cleaning Service",
        providerName: "Clean Lanka UAE",
        category: "Home Services",
        serviceArea: "Dubai & Sharjah",
        pricingMode: "fixed",
        currency: "AED",
        price: 150,
        rating: 4.6,
        reviewCount: 11,
        href: "#",
    },
];

export default function PopularServices() {
    return (
        <section className="bg-surface">
            <div className="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
                <div className="flex items-end justify-between gap-4">
                    <div>
                        <p className="text-sm font-semibold text-brand-secondary">
                            Services
                        </p>

                        <h2 className="mt-1 text-xl font-bold text-text-primary sm:text-2xl">
                            Popular Services
                        </h2>

                        <p className="mt-1.5 hidden max-w-2xl text-sm text-text-secondary sm:block">
                            Discover services from providers serving customers
                            across the UAE and Sri Lanka.
                        </p>
                    </div>

                    <a
                        href="#"
                        className="inline-flex shrink-0 items-center gap-1 text-sm font-semibold text-brand-secondary transition-colors hover:text-brand-secondary-hover"
                    >
                        View all services
                        <ChevronRight size={16} strokeWidth={2} />
                    </a>
                </div>

                <div className="mt-6 hidden grid-cols-3 gap-3 md:grid lg:grid-cols-5">
                    {popularServices.map((service) => (
                        <ServiceCard key={service.id} service={service} />
                    ))}
                </div>

                <div className="-mx-4 mt-5 overflow-x-auto px-4 pb-2 md:hidden">
                    <div className="flex w-max gap-3">
                        {popularServices.map((service) => (
                            <div
                                key={service.id}
                                className="w-[190px] shrink-0 sm:w-[220px]"
                            >
                                <ServiceCard service={service} />
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </section>
    );
}
