import React from "react";
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


  return (

    <>
      {/* ColorInit */}
      <ColorInit color={true} />

      {/* ScrollToTop */}
      <ScrollToTop smooth color="#FA6400" />

      {/* Preloader */}
      <Preloader />

      {/* HeaderTwo */}
      <HeaderTwo category={false} />

      {/* BannerTwo */}
      <BannerTwo />

      {/* PromotionalTwo */}
      <PromotionalTwo />

      {/* DealsOne */}
      <DealsOne />

      {/* TopSellingOne */}
      <TopSellingOne />

      {/* TrendingOne */}
      <TrendingOne />

      {/* DiscountOne */}
      <DiscountOne />

      {/* FeaturedOne */}
      <FeaturedOne />

      {/* BigDealOne */}
      <BigDealOne />

      {/* TopSellingTwo */}
      <TopSellingTwo />

      {/* PopularProductsOne */}
      <PopularProductsOne />

      {/* TopVendorsTwo */}
      <TopVendorsTwo />

      {/* DaySaleOne */}
      <DaySaleOne />

      {/* RecentlyViewedOne */}
      <RecentlyViewedOne />

      {/* BrandTwo */}
      <BrandTwo />

      {/* ShippingTwo */}
      <ShippingTwo />

      {/* NewsletterTwo */}
      <NewsletterTwo />

      {/* FooterTwo */}
      <FooterTwo />

      {/* BottomFooter */}
      <BottomFooter />


    </>
  );
};

export default HomePageTwo;
