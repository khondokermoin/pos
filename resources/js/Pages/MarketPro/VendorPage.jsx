import React from "react";
import ColorInit from "../../Helpers/ColorInit";
import ScrollToTop from "react-scroll-to-top";
import Preloader from "../../Helpers/Preloader";
import HeaderOne from "../../Components/MarketPro/HeaderOne";
import BreadcrumbThree from "../../Components/MarketPro/BreadcrumbThree";
import VendorsList from "../../Components/MarketPro/VendorsList";
import ShippingOne from "../../Components/MarketPro/ShippingOne";
import NewsletterOne from "../../Components/MarketPro/NewsletterOne";
import FooterOne from "../../Components/MarketPro/FooterOne";
import BottomFooter from "../../Components/MarketPro/BottomFooter";

const VendorPage = () => {
  return (
    <>
      {/* Preloader */}
      <Preloader />

      {/* ColorInit */}
      <ColorInit color={false} />

      {/* ScrollToTop */}
      <ScrollToTop smooth color='#299E60' />

      {/* HeaderOne */}
      <HeaderOne />

      {/* BreadcrumbThree */}
      <BreadcrumbThree title={"Vendor List"} />

      {/* VendorsList */}
      <VendorsList />

      {/* ShippingOne */}
      <ShippingOne />

      {/* NewsletterOne */}
      <NewsletterOne />

      {/* FooterOne */}
      <FooterOne />

      {/* BottomFooter */}
      <BottomFooter />
    </>
  );
};

export default VendorPage;
