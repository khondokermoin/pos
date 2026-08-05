import React from "react";
import { usePage } from "@inertiajs/react";
import Preloader from "../../Helpers/Preloader";
import HeaderOne from "../../Components/MarketPro/HeaderOne";
import ProductDetailsOne from "../../Components/MarketPro/ProductDetailsOne";
import NewArrivalTwo from "../../Components/MarketPro/NewArrivalTwo";
import ShippingOne from "../../Components/MarketPro/ShippingOne";
import NewsletterOne from "../../Components/MarketPro/NewsletterOne";
import FooterOne from "../../Components/MarketPro/FooterOne";
import BottomFooter from "../../Components/MarketPro/BottomFooter";
import BreadcrumbTwo from './../../Components/MarketPro/BreadcrumbTwo';
import ScrollToTop from "react-scroll-to-top";
import ColorInit from "../../Helpers/ColorInit";

/**
 * ProductDetailsPageOne — Inertia page for /product/{id}.
 *
 * DATA SOURCE: ShopController::productDetail() via
 *   Inertia::render('MarketPro/ProductDetailsPageOne', ['product' => ..., 'relatedProducts' => ...])
 *
 * On the static /product-details demo route, these props are simply absent —
 * ProductDetailsOne renders its built-in placeholder in that case.
 */
const ProductDetailsPageOne = () => {
  const { product, relatedProducts, tenant } = usePage().props;

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
      <BreadcrumbTwo title={product?.name ?? "Product Details"} />

      {/* ProductDetailsOne */}
      <ProductDetailsOne product={product} currency={tenant?.currency ?? "BDT"} />

      {/* NewArrivalTwo */}
      <NewArrivalTwo products={relatedProducts} />

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
