import React from "react";
import Preloader from "../../Helpers/Preloader";
import HeaderOne from "../../Components/MarketPro/HeaderOne";
import ProductDetailsOne from "../../Components/MarketPro/ProductDetailsOne";
import NewArrivalTwo from "../../Components/MarketPro/NewArrivalTwo";
import ShippingOne from "../../Components/MarketPro/ShippingOne";
import NewsletterOne from "../../Components/MarketPro/NewsletterOne";
import FooterOne from "../../Components/MarketPro/FooterOne";
import BottomFooter from "../../Components/MarketPro/BottomFooter";
import BreadcrumbTwo from "../../Components/MarketPro/BreadcrumbTwo";
import ScrollToTop from "react-scroll-to-top";
import ColorInit from "../../Helpers/ColorInit";

const ProductDetailsPageOne = () => {



  return (
    <>

      {/* Preloader */}
      <Preloader />

      {/* ColorInit */}
      <ColorInit color={false} />

      {/* ScrollToTop */}
      <ScrollToTop smooth color="#299E60" />

      {/* HeaderOne */}
      <HeaderOne />

      {/* Breadcrumb */}
      <BreadcrumbTwo title={"Product Details"} />

      {/* ProductDetailsOne */}
      <ProductDetailsOne />

      {/* NewArrivalTwo */}
      <NewArrivalTwo />

      {/* ShippingOne */}
      <ShippingOne />

      {/* NewsletterOne */}
      <NewsletterOne />

      {/* FooterTwo */}
      <FooterOne />

      {/* BottomFooter */}
      <BottomFooter />



    </>
  );
};

export default ProductDetailsPageOne;
