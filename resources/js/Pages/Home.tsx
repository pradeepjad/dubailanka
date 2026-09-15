import DiscoveryCategories from "../components/home/DiscoveryCategories";
import FeaturedProducts from "../components/home/FeaturedProducts";
import FeaturedStores from "../components/home/FeaturedStores";
import NewArrivals from "../components/home/NewArrivals";
import PromotionalHero from "../components/home/PromotionalHero";
import PopularServices from "../components/home/PopularServices";
import DiscoverSriLanka from "../components/home/DiscoverSriLanka";
import LatestClassifieds from "../components/home/LatestClassifieds";
import TrustSection from "../components/home/TrustSection";
import SellerCTA from "../components/home/SellerCTA";

import PublicLayout from "../layouts/PublicLayout";

export default function Home() {
    return (
        <PublicLayout>
            <PromotionalHero />

            <DiscoveryCategories />

            <FeaturedProducts />

            <FeaturedStores />

            <NewArrivals />

            <PopularServices />

            <DiscoverSriLanka />

            <LatestClassifieds />

            <TrustSection />

            <SellerCTA />
        </PublicLayout>
    );
}
