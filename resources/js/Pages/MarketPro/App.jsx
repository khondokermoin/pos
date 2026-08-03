import React from "react";
import { Route, Routes } from "react-router-dom";

import HomePageOne from "./HomePageOne";
import HomePageTwo from "./HomePageTwo";
import HomePageThree from "./HomePageThree";
import ShopPage from "./ShopPage";
import ProductDetailsPageOne from "./ProductDetailsPageOne";
import ProductDetailsPageTwo from "./ProductDetailsPageTwo";
import CartPage from "./CartPage";
import CheckoutPage from "./CheckoutPage";
import BecomeSellerPage from "./BecomeSellerPage";
import WishlistPage from "./WishlistPage";
import AccountPage from "./AccountPage";
import BlogPage from "./BlogPage";
import BlogDetailsPage from "./BlogDetailsPage";
import ContactPage from "./ContactPage";
import VendorPage from "./VendorPage";
import VendorDetailsPage from "./VendorDetailsPage";
import VendorTwoPage from "./VendorTwoPage";
import VendorTwoDetailsPage from "./VendorTwoDetailsPage";

export default function App() {
    return (
        <Routes>
            <Route path="/" element={<HomePageOne />} />
            <Route path="/index-two" element={<HomePageTwo />} />
            <Route path="/index-three" element={<HomePageThree />} />
            <Route path="/shop" element={<ShopPage />} />
            <Route path="/product-details" element={<ProductDetailsPageOne />} />
            <Route path="/product-details-two" element={<ProductDetailsPageTwo />} />
            <Route path="/cart" element={<CartPage />} />
            <Route path="/checkout" element={<CheckoutPage />} />
            <Route path="/become-seller" element={<BecomeSellerPage />} />
            <Route path="/wishlist" element={<WishlistPage />} />
            <Route path="/account" element={<AccountPage />} />
            <Route path="/blog" element={<BlogPage />} />
            <Route path="/blog-details" element={<BlogDetailsPage />} />
            <Route path="/contact" element={<ContactPage />} />
            <Route path="/vendor" element={<VendorPage />} />
            <Route path="/vendor-details" element={<VendorDetailsPage />} />
            <Route path="/vendor-two" element={<VendorTwoPage />} />
            <Route path="/vendor-two-details" element={<VendorTwoDetailsPage />} />
        </Routes>
    );
}
