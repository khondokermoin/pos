import React, { useEffect, useState } from 'react'
import { Link } from "@inertiajs/react";
import Slider from 'react-slick';
import { getCountdown } from '../../Helpers/Countdown';

/**
 * ProductDetailsOne — product detail view.
 *
 * PROPS:
 *   - product  : real product object from ShopController::productDetail() → formatProductDetail()
 *                { id, name, description, image, category, brand, currency, has_variants,
 *                  variants: [{ id, name, sku, selling_price, cost_price, attributes, is_active, stock: { quantity } }] }
 *                Undefined on the static /product-details demo route — falls back to placeholder content.
 *   - currency : tenant currency code (e.g. "BDT")
 *
 * CART: "Add To Cart" writes to localStorage in the same format ShopPage.jsx's ProductCard
 * and CheckoutPage.jsx already use: { variant_id, product_id, name, variant_name, unit_price, quantity, image }
 */
const ProductDetailsOne = ({ product, currency = "BDT" }) => {
    const [timeLeft, setTimeLeft] = useState(getCountdown());

    useEffect(() => {
        const interval = setInterval(() => {
            setTimeLeft(getCountdown());
        }, 1000);

        return () => clearInterval(interval);
    }, []);

    const variants = product?.variants ?? [];
    const [selectedVariantId, setSelectedVariantId] = useState(variants[0]?.id ?? null);

    useEffect(() => {
        setSelectedVariantId(variants[0]?.id ?? null);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [product?.id]);

    const selectedVariant = variants.find((v) => v.id === selectedVariantId) ?? variants[0] ?? null;
    const stockQty = selectedVariant?.stock?.quantity ?? 0;
    const inStock = stockQty > 0;

    // increment & decrement — clamped to available stock
    const [quantity, setQuantity] = useState(1);
    useEffect(() => setQuantity(1), [selectedVariantId]);
    const incrementQuantity = () =>
        setQuantity((q) => (inStock ? Math.min(q + 1, stockQty) : q));
    const decrementQuantity = () => setQuantity((q) => (q > 1 ? q - 1 : q));

    const fallbackImage = "/assets/images/thumbs/product-details-thumb1.png";
    const [mainImage, setMainImage] = useState(product?.image ?? fallbackImage);
    useEffect(() => setMainImage(product?.image ?? fallbackImage), [product?.image]);

    const settingsThumbs = {
        dots: false,
        infinite: true,
        speed: 500,
        slidesToShow: 4,
        slidesToScroll: 1,
        focusOnSelect: true,
    };

    const variantLabel = (v) => {
        if (v.attributes?.length) {
            return v.attributes.map((a) => `${a.key}: ${a.value}`).join(', ');
        }
        return v.name || v.sku;
    };

    const [added, setAdded] = useState(false);

    const handleAddToCart = () => {
        if (!product || !selectedVariant || !inStock) return;
        try {
            const cart = JSON.parse(localStorage.getItem("cart") ?? "[]");
            const existing = cart.findIndex((i) => i.variant_id === selectedVariant.id);

            if (existing >= 0) {
                cart[existing].quantity += quantity;
            } else {
                cart.push({
                    variant_id: selectedVariant.id,
                    product_id: product.id,
                    name: product.name,
                    variant_name: selectedVariant.name ?? null,
                    unit_price: selectedVariant.selling_price ?? 0,
                    quantity,
                    image: product.image ?? null,
                });
            }
            localStorage.setItem("cart", JSON.stringify(cart));

            setAdded(true);
            setTimeout(() => setAdded(false), 1500);
        } catch {
            // localStorage unavailable (private browsing, etc.) — fail silently
        }
    };

    // Placeholder demo copy used only when no `product` prop is supplied (static /product-details route)
    const name = product?.name ?? "Lay's Potato Chips Onion Flavored";
    const description =
        product?.description ??
        "Vivamus adipiscing nisl ut dolor dignissim semper. Nulla luctus malesuada tincidunt. Class aptent taciti sociosqu ad litora torquent";
    const sku = selectedVariant?.sku ?? "EB4DRP";
    const price = selectedVariant?.selling_price ?? null;
    const compareAtPrice = selectedVariant?.cost_price ?? null;

    return (
        <section className="product-details py-80">
            <div className="container container-lg">
                <div className="row gy-4">
                    <div className="col-lg-9">
                        <div className="row gy-4">
                            <div className="col-xl-6">
                                <div className="product-details__left">
                                    <div className="product-details__thumb-slider border border-gray-100 rounded-16">
                                        <div className="">
                                            <div className="product-details__thumb flex-center h-100">
                                                <img
                                                    src={mainImage}
                                                    alt={name}
                                                    onError={(e) => {
                                                        e.target.src = fallbackImage;
                                                    }}
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    {!product && (
                                        <div className="mt-24">
                                            <div className="product-details__images-slider">
                                                <Slider {...settingsThumbs}>
                                                    {[
                                                        "/assets/images/thumbs/product-details-thumb1.png",
                                                        "/assets/images/thumbs/product-details-thumb2.png",
                                                        "/assets/images/thumbs/product-details-thumb3.png",
                                                        "/assets/images/thumbs/product-details-thumb2.png",
                                                    ].map((image, index) => (
                                                        <div className="center max-w-120 max-h-120 h-100 flex-center border border-gray-100 rounded-16 p-8" key={index} onClick={() => setMainImage(image)}>
                                                            <img className='thum' src={image} alt={`Thumbnail ${index}`} />
                                                        </div>
                                                    ))}
                                                </Slider>
                                            </div>
                                        </div>
                                    )}
                                </div>
                            </div>
                            <div className="col-xl-6">
                                <div className="product-details__content">
                                    <h5 className="mb-12">{name}</h5>
                                    <div className="flex-align flex-wrap gap-12">
                                        <div className="flex-align gap-12 flex-wrap">
                                            <div className="flex-align gap-8">
                                                <span className="text-15 fw-medium text-warning-600 d-flex">
                                                    <i className="ph-fill ph-star" />
                                                </span>
                                                <span className="text-15 fw-medium text-warning-600 d-flex">
                                                    <i className="ph-fill ph-star" />
                                                </span>
                                                <span className="text-15 fw-medium text-warning-600 d-flex">
                                                    <i className="ph-fill ph-star" />
                                                </span>
                                                <span className="text-15 fw-medium text-warning-600 d-flex">
                                                    <i className="ph-fill ph-star" />
                                                </span>
                                                <span className="text-15 fw-medium text-warning-600 d-flex">
                                                    <i className="ph-fill ph-star" />
                                                </span>
                                            </div>
                                            <span className="text-sm fw-medium text-neutral-600">
                                                4.7 Star Rating
                                            </span>
                                            <span className="text-sm fw-medium text-gray-500">
                                                (21,671)
                                            </span>
                                        </div>
                                        <span className="text-sm fw-medium text-gray-500">|</span>
                                        <span className="text-gray-900">
                                            {" "}
                                            <span className="text-gray-400">SKU:</span> {sku}{" "}
                                        </span>
                                    </div>
                                    <span className="mt-32 pt-32 text-gray-700 border-top border-gray-100 d-block" />
                                    <p className="text-gray-700">
                                        {description}
                                    </p>

                                    {variants.length > 1 && (
                                        <div className="mt-24">
                                            <span className="text-gray-900 d-block mb-8">Options:</span>
                                            <select
                                                className="form-select common-input rounded-8"
                                                style={{ maxWidth: 320 }}
                                                value={selectedVariantId ?? ""}
                                                onChange={(e) => setSelectedVariantId(Number(e.target.value))}
                                            >
                                                {variants.map((v) => (
                                                    <option key={v.id} value={v.id} disabled={!v.is_active}>
                                                        {variantLabel(v)}
                                                        {v.stock?.quantity === 0 ? ' (Out of stock)' : ''}
                                                    </option>
                                                ))}
                                            </select>
                                        </div>
                                    )}

                                    <div className="mt-32 flex-align flex-wrap gap-32">
                                        <div className="flex-align gap-8">
                                            {price !== null ? (
                                                <h4 className="mb-0">{currency} {Number(price).toLocaleString()}</h4>
                                            ) : (
                                                <h4 className="mb-0 text-gray-400">Price on request</h4>
                                            )}
                                        </div>
                                        <Link href="#" className="btn btn-main rounded-pill">
                                            Order on What'sApp
                                        </Link>
                                    </div>
                                    <span className="mt-32 pt-32 text-gray-700 border-top border-gray-100 d-block" />
                                    <div className="flex-align flex-wrap gap-16 bg-color-one rounded-8 py-16 px-24">
                                        <div className="flex-align gap-16">
                                            <span className="text-main-600 text-sm">Special Offer:</span>
                                        </div>
                                        <div className="countdown" id="countdown11">
                                            <ul className="countdown-list flex-align flex-wrap">
                                                <li className="countdown-list__item text-heading flex-align gap-4 text-xs fw-medium w-28 h-28 rounded-4 border border-main-600 p-0 flex-center">
                                                    {timeLeft.days} <span className="days" />
                                                </li>
                                                <li className="countdown-list__item text-heading flex-align gap-4 text-xs fw-medium w-28 h-28 rounded-4 border border-main-600 p-0 flex-center">
                                                    {timeLeft.hours}<span className="hours" />
                                                </li>
                                                <li className="countdown-list__item text-heading flex-align gap-4 text-xs fw-medium w-28 h-28 rounded-4 border border-main-600 p-0 flex-center">
                                                    {timeLeft.minutes}<span className="minutes" />
                                                </li>
                                                <li className="countdown-list__item text-heading flex-align gap-4 text-xs fw-medium w-28 h-28 rounded-4 border border-main-600 p-0 flex-center">
                                                    {timeLeft.seconds}<span className="seconds" />
                                                </li>
                                            </ul>
                                        </div>
                                        <span className="text-gray-900 text-xs">
                                            Remains untill the end of the offer
                                        </span>
                                    </div>
                                    <div className="mb-24">
                                        <div className="mt-32 flex-align gap-12 mb-16">
                                            <span className="w-32 h-32 bg-white flex-center rounded-circle text-main-600 box-shadow-xl">
                                                <i className="ph-fill ph-lightning" />
                                            </span>
                                            <h6 className="text-md mb-0 fw-bold text-gray-900">
                                                {inStock ? "In Stock" : "Out of Stock"}
                                            </h6>
                                        </div>
                                        {product && (
                                            <span className="text-sm text-gray-700 mt-8">
                                                Available only: {stockQty}
                                            </span>
                                        )}
                                    </div>
                                    <span className="text-gray-900 d-block mb-8">Quantity:</span>
                                    <div className="flex-between gap-16 flex-wrap">
                                        <div className="flex-align flex-wrap gap-16">
                                            <div className="border border-gray-100 rounded-pill py-9 px-16 flex-align">
                                                <button onClick={decrementQuantity}
                                                    type="button"
                                                    className="quantity__minus p-4 text-gray-700 hover-text-main-600 flex-center"
                                                >
                                                    <i className="ph ph-minus" />
                                                </button>
                                                <input
                                                    type="number"
                                                    className="quantity__input border-0 text-center w-32"
                                                    value={quantity} readOnly
                                                />
                                                <button onClick={incrementQuantity}
                                                    type="button"
                                                    className="quantity__plus p-4 text-gray-700 hover-text-main-600 flex-center"
                                                >
                                                    <i className="ph ph-plus" />
                                                </button>
                                            </div>
                                            <button
                                                type="button"
                                                onClick={handleAddToCart}
                                                disabled={product ? !inStock : false}
                                                className="btn btn-main rounded-pill flex-align d-inline-flex gap-8 px-48"
                                            >
                                                <i className="ph ph-shopping-cart" />
                                                {added ? "Added!" : inStock || !product ? "Add To Cart" : "Out of Stock"}
                                            </button>
                                        </div>
                                        <div className="flex-align gap-12">
                                            <Link
                                                href="#"
                                                className="w-52 h-52 bg-main-50 text-main-600 text-xl hover-bg-main-600 hover-text-white flex-center rounded-circle"
                                            >
                                                <i className="ph ph-heart" />
                                            </Link>
                                            <Link
                                                href="#"
                                                className="w-52 h-52 bg-main-50 text-main-600 text-xl hover-bg-main-600 hover-text-white flex-center rounded-circle"
                                            >
                                                <i className="ph ph-shuffle" />
                                            </Link>
                                            <Link
                                                href="#"
                                                className="w-52 h-52 bg-main-50 text-main-600 text-xl hover-bg-main-600 hover-text-white flex-center rounded-circle"
                                            >
                                                <i className="ph ph-share-network" />
                                            </Link>
                                        </div>
                                    </div>
                                    <span className="mt-32 pt-32 text-gray-700 border-top border-gray-100 d-block" />
                                    <div className="flex-between gap-16 p-12 border border-main-two-600 border-dashed rounded-8 mb-16">
                                        <div className="flex-align gap-12">
                                            <button
                                                type="button"
                                                className="w-18 h-18 flex-center border border-gray-900 text-xs rounded-circle hover-bg-gray-100"
                                            >
                                                <i className="ph ph-plus" />
                                            </button>
                                            <span className="text-gray-900 fw-medium text-xs">
                                                Mfr. coupon. $3.00 off 5
                                            </span>
                                        </div>
                                        <Link
                                            href="/cart"
                                            className="text-xs fw-semibold text-main-two-600 text-decoration-underline hover-text-main-two-700"
                                        >
                                            View Details
                                        </Link>
                                    </div>
                                    <ul className="list-inside ms-12">
                                        <li className="text-gray-900 text-sm mb-8">
                                            Buy 1, Get 1 FREE
                                        </li>
                                        <li className="text-gray-900 text-sm mb-0">
                                            Buy 1, Get 1 FREE
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="col-lg-3">
                        <div className="product-details__sidebar border border-gray-100 rounded-16 overflow-hidden">
                            <div className="p-24">
                                <div className="flex-between bg-main-600 rounded-pill p-8">
                                    <div className="flex-align gap-8">
                                        <span className="w-44 h-44 bg-white rounded-circle flex-center text-2xl">
                                            <i className="ph ph-storefront" />
                                        </span>
                                        <span className="text-white">by Marketpro</span>
                                    </div>
                                    <Link
                                        href="/shop"
                                        className="btn btn-white rounded-pill text-uppercase"
                                    >
                                        View Store
                                    </Link>
                                </div>
                            </div>
                            <div className="p-24 bg-color-one d-flex align-items-start gap-24 border-bottom border-gray-100">
                                <span className="w-44 h-44 bg-white text-main-600 rounded-circle flex-center text-2xl flex-shrink-0">
                                    <i className="ph-fill ph-truck" />
                                </span>
                                <div className="">
                                    <h6 className="text-sm mb-8">Fast Delivery</h6>
                                    <p className="text-gray-700">
                                        Lightning-fast shipping, guaranteed.
                                    </p>
                                </div>
                            </div>
                            <div className="p-24 bg-color-one d-flex align-items-start gap-24 border-bottom border-gray-100">
                                <span className="w-44 h-44 bg-white text-main-600 rounded-circle flex-center text-2xl flex-shrink-0">
                                    <i className="ph-fill ph-arrow-u-up-left" />
                                </span>
                                <div className="">
                                    <h6 className="text-sm mb-8">Free 90-day returns</h6>
                                    <p className="text-gray-700">Shop risk-free with easy returns.</p>
                                </div>
                            </div>
                            <div className="p-24 bg-color-one d-flex align-items-start gap-24 border-bottom border-gray-100">
                                <span className="w-44 h-44 bg-white text-main-600 rounded-circle flex-center text-2xl flex-shrink-0">
                                    <i className="ph-fill ph-check-circle" />
                                </span>
                                <div className="">
                                    <h6 className="text-sm mb-8">
                                        Pickup available at Shop location
                                    </h6>
                                    <p className="text-gray-700">Usually ready in 24 hours</p>
                                </div>
                            </div>
                            <div className="p-24 bg-color-one d-flex align-items-start gap-24 border-bottom border-gray-100">
                                <span className="w-44 h-44 bg-white text-main-600 rounded-circle flex-center text-2xl flex-shrink-0">
                                    <i className="ph-fill ph-credit-card" />
                                </span>
                                <div className="">
                                    <h6 className="text-sm mb-8">Payment</h6>
                                    <p className="text-gray-700">
                                        Payment upon receipt of goods, Payment by card in the
                                        department, Google Pay, Online card.
                                    </p>
                                </div>
                            </div>
                            <div className="p-24 bg-color-one d-flex align-items-start gap-24 border-bottom border-gray-100">
                                <span className="w-44 h-44 bg-white text-main-600 rounded-circle flex-center text-2xl flex-shrink-0">
                                    <i className="ph-fill ph-check-circle" />
                                </span>
                                <div className="">
                                    <h6 className="text-sm mb-8">Warranty</h6>
                                    <p className="text-gray-700">
                                        The Consumer Protection Act does not provide for the return of
                                        this product of proper quality.
                                    </p>
                                </div>
                            </div>
                            <div className="p-24 bg-color-one d-flex align-items-start gap-24 border-bottom border-gray-100">
                                <span className="w-44 h-44 bg-white text-main-600 rounded-circle flex-center text-2xl flex-shrink-0">
                                    <i className="ph-fill ph-package" />
                                </span>
                                <div className="">
                                    <h6 className="text-sm mb-8">Packaging</h6>
                                    <p className="text-gray-700">
                                        Research &amp; development value proposition graphical user
                                        interface investor.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="pt-80">
                    <div className="product-dContent border rounded-24">
                        <div className="product-dContent__header border-bottom border-gray-100 flex-between flex-wrap gap-16">
                            <ul
                                className="nav common-tab nav-pills mb-3"
                                id="pills-tab"
                                role="tablist"
                            >
                                <li className="nav-item" role="presentation">
                                    <button
                                        className="nav-link active"
                                        id="pills-description-tab"
                                        data-bs-toggle="pill"
                                        data-bs-target="#pills-description"
                                        type="button"
                                        role="tab"
                                        aria-controls="pills-description"
                                        aria-selected="true"
                                    >
                                        Description
                                    </button>
                                </li>
                                <li className="nav-item" role="presentation">
                                    <button
                                        className="nav-link"
                                        id="pills-reviews-tab"
                                        data-bs-toggle="pill"
                                        data-bs-target="#pills-reviews"
                                        type="button"
                                        role="tab"
                                        aria-controls="pills-reviews"
                                        aria-selected="false"
                                    >
                                        Reviews
                                    </button>
                                </li>
                            </ul>
                            <Link
                                href="#"
                                className="btn bg-color-one rounded-16 flex-align gap-8 text-main-600 hover-bg-main-600 hover-text-white"
                            >
                                <img src="/assets/images/icon/satisfaction-icon.png" alt="" />
                                100% Satisfaction Guaranteed
                            </Link>
                        </div>
                        <div className="product-dContent__box">
                            <div className="tab-content" id="pills-tabContent">
                                <div
                                    className="tab-pane fade show active"
                                    id="pills-description"
                                    role="tabpanel"
                                    aria-labelledby="pills-description-tab"
                                    tabIndex={0}
                                >
                                    <div className="mb-40">
                                        <h6 className="mb-24">Product Description</h6>
                                        <p>
                                            {description}
                                        </p>
                                    </div>
                                    <div className="mb-40">
                                        <h6 className="mb-24">Product Specifications</h6>
                                        <ul className="mt-32">
                                            <li className="text-gray-400 mb-14 flex-align gap-14">
                                                <span className="w-20 h-20 bg-main-50 text-main-600 text-xs flex-center rounded-circle">
                                                    <i className="ph ph-check" />
                                                </span>
                                                <span className="text-heading fw-medium">
                                                    Category:
                                                    <span className="text-gray-500"> {product?.category?.name ?? 'General'}</span>
                                                </span>
                                            </li>
                                            <li className="text-gray-400 mb-14 flex-align gap-14">
                                                <span className="w-20 h-20 bg-main-50 text-main-600 text-xs flex-center rounded-circle">
                                                    <i className="ph ph-check" />
                                                </span>
                                                <span className="text-heading fw-medium">
                                                    Product Name:
                                                    <span className="text-gray-500"> {name}</span>
                                                </span>
                                            </li>
                                            {product?.brand && (
                                                <li className="text-gray-400 mb-14 flex-align gap-14">
                                                    <span className="w-20 h-20 bg-main-50 text-main-600 text-xs flex-center rounded-circle">
                                                        <i className="ph ph-check" />
                                                    </span>
                                                    <span className="text-heading fw-medium">
                                                        Brand:
                                                        <span className="text-gray-500"> {product.brand.name}</span>
                                                    </span>
                                                </li>
                                            )}
                                            <li className="text-gray-400 mb-14 flex-align gap-14">
                                                <span className="w-20 h-20 bg-main-50 text-main-600 text-xs flex-center rounded-circle">
                                                    <i className="ph ph-check" />
                                                </span>
                                                <span className="text-heading fw-medium">
                                                    SKU:
                                                    <span className="text-gray-500"> {sku}</span>
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div
                                    className="tab-pane fade"
                                    id="pills-reviews"
                                    role="tabpanel"
                                    aria-labelledby="pills-reviews-tab"
                                    tabIndex={0}
                                >
                                    <div className="row g-4">
                                        <div className="col-lg-6">
                                            <h6 className="mb-24">Reviews</h6>
                                            <p className="text-gray-700">No reviews yet for this product.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    )
}

export default ProductDetailsOne
