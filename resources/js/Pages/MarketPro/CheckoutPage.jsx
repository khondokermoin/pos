import React from "react";
import Preloader from "../../Helpers/Preloader";
import ColorInit from "../../Helpers/ColorInit";
import HeaderTwo from "../../Components/MarketPro/HeaderTwo";
import Breadcrumb from "../../Components/MarketPro/Breadcrumb";
import FooterTwo from "../../Components/MarketPro/FooterTwo";
import BottomFooter from "../../Components/MarketPro/BottomFooter";
import ShippingOne from "../../Components/MarketPro/ShippingOne";
import Checkout from "../../Components/MarketPro/Checkout";
import ScrollToTop from "react-scroll-to-top";


const CheckoutPage = () => {



  return (
    <>
      {/* ColorInit */}
      <ColorInit color={true} />

      {/* ScrollToTop */}
      <ScrollToTop smooth color="#FA6400" />

      {/* Preloader */}
      <Preloader />

      {/* HeaderTwo */}
      <HeaderTwo category={true} />

      {/* Breadcrumb */}
      <Breadcrumb title={"Checkout"} />

      {/* Checkout */}
      <Checkout />

      {/* ShippingOne */}
      <ShippingOne />

      {/* FooterTwo */}
      <FooterTwo />

      {/* BottomFooter */}
      <BottomFooter />


    </>
  );
};

export default CheckoutPage;
