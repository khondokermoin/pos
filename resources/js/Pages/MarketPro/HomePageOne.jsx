import React from "react";
import { usePage } from "@inertiajs/react";
import Preloader from "../../Helpers/Preloader";
import HeaderOne from "../../Components/MarketPro/HeaderOne";
import BannerOne from "../../Components/MarketPro/BannerOne";
import FeatureOne from "../../Components/MarketPro/FeatureOne";
import PromotionalOne from "../../Components/MarketPro/PromotionalOne";
import FlashSalesOne from "../../Components/MarketPro/FlashSalesOne";
import ProductListOne from "../../Components/MarketPro/ProductListOne";
import OfferOne from "../../Components/MarketPro/OfferOne";
import RecommendedOne from "../../Components/MarketPro/RecommendedOne";
import HotDealsOne from "../../Components/MarketPro/HotDealsOne";
import TopVendorsOne from "../../Components/MarketPro/TopVendorsOne";
import BestSellsOne from "../../Components/MarketPro/BestSellsOne";
import DeliveryOne from "../../Components/MarketPro/DeliveryOne";
import OrganicOne from "../../Components/MarketPro/OrganicOne";
import ShortProductOne from "../../Components/MarketPro/ShortProductOne";
import BrandOne from "../../Components/MarketPro/BrandOne";
import NewArrivalOne from "../../Components/MarketPro/NewArrivalOne";
import ShippingOne from "../../Components/MarketPro/ShippingOne";
import NewsletterOne from "../../Components/MarketPro/NewsletterOne";
import FooterOne from "../../Components/MarketPro/FooterOne";
import BottomFooter from "../../Components/MarketPro/BottomFooter";
import ScrollToTop from "react-scroll-to-top";
import ColorInit from "../../Helpers/ColorInit";

/**
 * HomePageOne — Inertia-powered homepage.
 *
 * DATA SOURCE: ShopController::home() via Inertia::render('MarketPro/HomePageOne', [...])
 *
 * PROPS (from usePage().props):
 *   - featuredProducts : Product[] — for FlashSalesOne, BestSellsOne
 *   - newArrivals      : Product[] — for NewArrivalOne
 *   - categories       : Category[] — for navigation/banners
 *   - tenant           : Company branding (shared props from HandleInertiaRequests)
 */
const HomePageOne = () => {
    const { featuredProducts, newArrivals, categories, tenant } =
        usePage().props;

    return (
        <>
            <Preloader />
            <ScrollToTop smooth color="#299E60" />
            <ColorInit color={false} />

            {/* Pass tenant branding to header */}
            <HeaderOne tenant={tenant} categories={categories} />

            <BannerOne />
            <FeatureOne />
            <PromotionalOne />

            {/* Pass real product data to product sections */}
            <FlashSalesOne
                products={featuredProducts}
                currency={tenant?.currency}
            />
            <ProductListOne
                products={featuredProducts}
                currency={tenant?.currency}
            />
            <OfferOne />
            <RecommendedOne
                products={featuredProducts}
                currency={tenant?.currency}
            />
            <HotDealsOne
                products={featuredProducts}
                currency={tenant?.currency}
            />
            <TopVendorsOne />
            <BestSellsOne
                products={featuredProducts}
                currency={tenant?.currency}
            />
            <DeliveryOne />
            <OrganicOne />
            <ShortProductOne
                products={newArrivals}
                currency={tenant?.currency}
            />
            <BrandOne />
            <NewArrivalOne products={newArrivals} currency={tenant?.currency} />
            <ShippingOne />
            <NewsletterOne />
            <FooterOne tenant={tenant} />
            <BottomFooter />
        </>
    );
};

export default HomePageOne;
