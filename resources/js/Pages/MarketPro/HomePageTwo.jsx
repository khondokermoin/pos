import React from "react";
import { usePage } from "@inertiajs/react";
import Preloader from "../../Helpers/Preloader";
import HeaderTwo from "../../Components/MarketPro/HeaderTwo";
import BannerTwo from "../../Components/MarketPro/BannerTwo";
import PromotionalTwo from "../../Components/MarketPro/PromotionalTwo";
import DealsOne from "../../Components/MarketPro/DealsOne";
import TopSellingOne from "../../Components/MarketPro/TopSellingOne";
import TrendingOne from "../../Components/MarketPro/TrendingOne";
import DiscountOne from "../../Components/MarketPro/DiscountOne";
import FeaturedOne from "../../Components/MarketPro/FeaturedOne";
import BigDealOne from "../../Components/MarketPro/BigDealOne";
import TopSellingTwo from "../../Components/MarketPro/TopSellingTwo";
import PopularProductsOne from "../../Components/MarketPro/PopularProductsOne";
import TopVendorsTwo from "../../Components/MarketPro/TopVendorsTwo";
import DaySaleOne from "../../Components/MarketPro/DaySaleOne";
import RecentlyViewedOne from "../../Components/MarketPro/RecentlyViewedOne";
import BrandTwo from "../../Components/MarketPro/BrandTwo";
import ShippingTwo from "../../Components/MarketPro/ShippingTwo";
import NewsletterTwo from "../../Components/MarketPro/NewsletterTwo";
import FooterTwo from "../../Components/MarketPro/FooterTwo";
import BottomFooter from "../../Components/MarketPro/BottomFooter";
import ColorInit from "../../Helpers/ColorInit";
import ScrollToTop from "react-scroll-to-top";

const HomePageTwo = () => {
  const { featuredProducts, newArrivals, categories, tenant } =
    usePage().props;

  return (

    <>
      {/* ColorInit */}
      <ColorInit color={true} />

      {/* ScrollToTop */}
      <ScrollToTop smooth color="#FA6400" />

      {/* Preloader */}
      <Preloader />

      {/* HeaderTwo */}
      <HeaderTwo category={false} tenant={tenant} categories={categories} />

      {/* BannerTwo */}
      <BannerTwo />

      {/* PromotionalTwo */}
      <PromotionalTwo />

      {/* DealsOne */}
      <DealsOne products={newArrivals} currency={tenant?.currency} />

      {/* TopSellingOne */}
      <TopSellingOne products={featuredProducts} currency={tenant?.currency} />

      {/* TrendingOne */}
      <TrendingOne
        products={featuredProducts}
        currency={tenant?.currency}
        categories={categories}
      />

      {/* DiscountOne */}
      <DiscountOne />

      {/* FeaturedOne */}
      <FeaturedOne products={featuredProducts} currency={tenant?.currency} />

      {/* BigDealOne */}
      <BigDealOne />

      {/* TopSellingTwo */}
      <TopSellingTwo products={newArrivals} currency={tenant?.currency} />

      {/* PopularProductsOne */}
      <PopularProductsOne
        products={featuredProducts}
        currency={tenant?.currency}
      />

      {/* TopVendorsTwo */}
      <TopVendorsTwo />

      {/* DaySaleOne */}
      <DaySaleOne />

      {/* RecentlyViewedOne */}
      <RecentlyViewedOne products={newArrivals} currency={tenant?.currency} />

      {/* BrandTwo */}
      <BrandTwo />

      {/* ShippingTwo */}
      <ShippingTwo />

      {/* NewsletterTwo */}
      <NewsletterTwo />

      {/* FooterTwo */}
      <FooterTwo tenant={tenant} />

      {/* BottomFooter */}
      <BottomFooter />


    </>
  );
};

export default HomePageTwo;
