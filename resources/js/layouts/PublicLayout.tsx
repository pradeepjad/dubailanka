import { ReactNode } from "react";

import PublicHeader from "../components/public/PublicHeader";
import MobileBottomNavigation from "../components/public/MobileBottomNavigation";
import PublicFooter from "../components/public/PublicFooter";

interface PublicLayoutProps {
    children: ReactNode;
}

export default function PublicLayout({ children }: PublicLayoutProps) {
    return (
        <div className="min-h-screen bg-surface-subtle text-text-primary">
            <PublicHeader />

            <main className="pb-20 md:pb-0">{children}</main>

            <PublicFooter />

            <MobileBottomNavigation />
        </div>
    );
}
