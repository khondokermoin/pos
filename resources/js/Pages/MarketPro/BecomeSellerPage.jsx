import React from "react";
import ColorInit from "../../Helpers/ColorInit";
import Preloader from "../../Helpers/Preloader";
import HeaderTwo from "../../Components/MarketPro/HeaderTwo";
import Breadcrumb from "../../Components/MarketPro/Breadcrumb";
import BreadcrumbImage from "../../Components/MarketPro/BreadcrumbImage";
import WhyBecomeSeller from "../../Components/MarketPro/WhyBecomeSeller";
import CounterSection from "../../Components/MarketPro/CounterSection";
import StepsSection from "../../Components/MarketPro/StepsSection";
import TestimonialOne from "../../Components/MarketPro/TestimonialOne";
import ShippingOne from "../../Components/MarketPro/ShippingOne";
import FooterTwo from "../../Components/MarketPro/FooterTwo";
import BottomFooter from "../../Components/MarketPro/BottomFooter";

const BecomeSellerPage = () => {
  return (
    <>
      {/* ColorInit */}
      <ColorInit color={true} />

      {/* ScrollToTop */}
      <ScrollToTop smooth color='#FA6400' />

      {/* Preloader */}
      <Preloader />

      {/* HeaderTwo */}
      <HeaderTwo category={true} />

      {/* Breadcrumb */}
      <Breadcrumb title={"Become Seller"} />

      {/* BreadcrumbImage */}
      <BreadcrumbImage />

      {/* WhyBecomeSeller */}
      <WhyBecomeSeller />

      {/* CounterSection */}
      <CounterSection />

      {/* StepsSection */}
      <StepsSection />

      {/* TestimonialOne */}
      <section className='pb-120'>
        <TestimonialOne />
      </section>

      {/* ShippingOne */}
      <ShippingOne />

      {/* FooterTwo */}
      <FooterTwo />

      {/* BottomFooter */}
      <BottomFooter />
    </>
  );
};

export default BecomeSellerPage;
