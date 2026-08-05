import React, { useEffect, useState } from 'react'
import { Link } from "@inertiajs/react";
import { getCountdown } from '../../Helpers/Countdown';

const BestSellsOne = ({ products = [], currency = 'BDT' }) => {
    const [timeLeft, setTimeLeft] = useState(getCountdown());

    useEffect(() => {
        const interval = setInterval(() => {
            setTimeLeft(getCountdown());
        }, 1000);

        return () => clearInterval(interval);
    }, []);

    const handleAddToCart = (product, primaryVariant, price) => {
        try {
            const cart = JSON.parse(localStorage.getItem("cart") ?? "[]");
            const variantId = primaryVariant?.id ?? product.id;
            const existing = cart.findIndex((i) => i.variant_id === variantId);

            if (existing >= 0) {
                cart[existing].quantity += 1;
            } else {
                cart.push({
                    variant_id: variantId,
                    product_id: product.id,
                    name: product.name,
                    variant_name: primaryVariant?.name ?? null,
                    unit_price: price ?? 0,
                    quantity: 1,
                    image: product.image ?? null,
                });
            }
            localStorage.setItem("cart", JSON.stringify(cart));
        } catch {
            // localStorage unavailable (private browsing, etc.) — fail silently
        }
    };

    return (
        <section className="best sells pb-80">
            <div className="container container-lg">
                <div className="section-heading">
                    <div className="flex-between flex-wrap gap-8">
                        <h5 className="mb-0">Daily Best Sells</h5>
                    </div>
                </div>
                <div className="row g-12">
                    <div className="col-xxl-8">
                        <div className="row gy-4">
                            {products.map((product) => {
                                const primaryVariant = product.variants?.[0];
                                const price =
                                    product.selling_price ??
                                    primaryVariant?.selling_price;
                                const imageUrl =
                                    product.image ??
                                    "/assets/images/thumbs/best-sell1.png";

                                return (
                                    <div className="col-md-6" key={product.id}>
                                        <div className="product-card style-two h-100 p-8 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 flex-align gap-16">
                                            <div className="">
                                                <Link
                                                    href={`/product/${product.id}`}
                                                    className="product-card__thumb flex-center"
                                                >
                                                    <img
                                                        src={imageUrl}
                                                        alt={product.name}
                                                        onError={(e) => {
                                                            e.target.src =
                                                                "/assets/images/thumbs/best-sell1.png";
                                                        }}
                                                    />
                                                </Link>
                                            </div>
                                            <div className="product-card__content">
                                                <div className="product-card__price mb-16">
                                                    {price ? (
                                                        <span className="text-heading text-md fw-semibold ">
                                                            {currency}{" "}
                                                            {Number(
                                                                price,
                                                            ).toLocaleString()}{" "}
                                                            <span className="text-gray-500 fw-normal">
                                                                /Qty
                                                            </span>{" "}
                                                        </span>
                                                    ) : (
                                                        <span className="text-gray-400 text-sm">
                                                            Price on request
                                                        </span>
                                                    )}
                                                </div>
                                                <h6 className="title text-lg fw-semibold mt-12 mb-8">
                                                    <Link
                                                        href={`/product/${product.id}`}
                                                        className="link text-line-2"
                                                    >
                                                        {product.name}
                                                    </Link>
                                                </h6>
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        handleAddToCart(
                                                            product,
                                                            primaryVariant,
                                                            price,
                                                        )
                                                    }
                                                    disabled={!price}
                                                    className="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center"
                                                >
                                                    Add To Cart{" "}
                                                    <i className="ph ph-shopping-cart" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    </div>
                    <div className="col-xxl-4">
                        <div className="position-relative rounded-16 bg-light-purple overflow-hidden p-28 z-1 text-center">
                            <div className="">
                                <img
                                    src="/assets/images/bg/special-snacks.png"
                                    alt=""
                                    className="position-absolute inset-block-start-0 inset-inline-start-0 z-n1 w-100 h-100 cover-img"
                                />
                                <div className="d-xxl-block d-none">
                                    <img src="/assets/images/thumbs/special-snacks-img.png" alt="" />
                                </div>
                            </div>
                            <div className="py-xl-4">
                                <h4 className="mb-8">Special Snacks</h4>
                                <div className="countdown my-32" id="countdown5">
                                    <ul className="countdown-list style-two flex-center flex-wrap">

                                        <li className="countdown-list__item text-heading flex-align gap-4 text-sm fw-medium colon-white">
                                            <span className="hours" />
                                            {timeLeft.hours} Hours
                                        </li>
                                        <li className="countdown-list__item text-heading flex-align gap-4 text-sm fw-medium colon-white">
                                            <span className="minutes" />
                                            {timeLeft.minutes} Min
                                        </li>
                                        <li className="countdown-list__item text-heading flex-align gap-4 text-sm fw-medium colon-white">
                                            <span className="seconds" />
                                            {timeLeft.seconds} Sec
                                        </li>
                                    </ul>
                                </div>
                                <Link
                                    href="/shop"
                                    className="mt-16 btn btn-main-two fw-medium d-inline-flex align-items-center rounded-pill gap-8"
                                    tabIndex={0}
                                >
                                    Shop Now
                                    <span className="icon text-xl d-flex">
                                        <i className="ph ph-arrow-right" />
                                    </span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    )
}

export default BestSellsOne