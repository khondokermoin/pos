import React from "react";
import Preloader from "../../Helpers/Preloader";
import ColorInit from "../../Helpers/ColorInit";
import ScrollToTop from "react-scroll-to-top";
import HeaderThree from "../../Components/MarketPro/HeaderThree";
import BannerThree from "../../Components/MarketPro/BannerThree";
import PromotionalThree from "../../Components/MarketPro/PromotionalThree";
import FeatureThree from "../../Components/MarketPro/FeatureThree";
import TextSlider from "../../Components/MarketPro/TextSlider";
import TrendingThree from "../../Components/MarketPro/TrendingThree";
import DiscountThree from "../../Components/MarketPro/DiscountThree";
import NewArrivalThree from "../../Components/MarketPro/NewArrivalThree";
import DealsSection from "../../Components/MarketPro/DealsSection";
import PopularProductsThree from "../../Components/MarketPro/PopularProductsThree";
import BrandThree from "../../Components/MarketPro/BrandThree";
import ShippingThree from "../../Components/MarketPro/ShippingThree";
import TestimonialOne from "../../Components/MarketPro/TestimonialOne";
import InstagramSection from "../../Components/MarketPro/InstagramSection";
import NewsletterThree from "../../Components/MarketPro/NewsletterThree";
import FooterThree from "../../Components/MarketPro/FooterThree";

const HomePageThree = () => {
  return (
    <>
      {/* ColorInit */}
      <ColorInit color={true} />

      {/* ScrollToTop */}
      <ScrollToTop smooth color='#FA6400' />

      {/* Preloader */}
      <Preloader />

      {/* HeaderThree */}
      <HeaderThree />

      {/* BannerThree */}
      <BannerThree />

      {/* PromotionalThree */}
      <PromotionalThree />

      {/* FeatureThree */}
      <FeatureThree />

      {/* TextSlider */}
      <TextSlider />

      {/* TrendingThree */}
      <TrendingThree />

      {/* DiscountThree */}
      <DiscountThree />

      {/* NewArrivalThree */}
      <NewArrivalThree />

      {/* DealsSection */}
      <DealsSection />

      {/* PopularProductsThree */}
      <PopularProductsThree />

      {/* BrandThree */}
      <BrandThree />

      {/* ShippingThree */}
      <ShippingThree />

      {/* TestimonialOne */}
      <TestimonialOne />

      {/* InstagramSection */}
      <InstagramSection />

      {/* NewsletterThree */}
      <NewsletterThree />

      {/* FooterThree */}
      <FooterThree />
    </>
  );
};

export default HomePageThree;
