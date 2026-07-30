import React from "react";
import Preloader from "../../Helpers/Preloader";
import ColorInit from "../../Helpers/ColorInit";
import ScrollToTop from "react-scroll-to-top";
import HeaderOne from "../../Components/MarketPro/HeaderOne";
import BreadcrumbThree from "../../Components/MarketPro/BreadcrumbThree";
import VendorsListTwo from "../../Components/MarketPro/VendorsListTwo";
import NewsletterOne from "../../Components/MarketPro/NewsletterOne";
import FooterOne from "../../Components/MarketPro/FooterOne";
import BottomFooter from "../../Components/MarketPro/BottomFooter";

const VendorDetailsPage = () => {
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
      <BreadcrumbThree title={"Vendor Details"} />

      {/* VendorsListTwo */}
      <VendorsListTwo />

      {/* NewsletterOne */}
      <NewsletterOne />

      {/* FooterOne */}
      <FooterOne />

      {/* BottomFooter */}
      <BottomFooter />
    </>
  );
};

export default VendorDetailsPage;
