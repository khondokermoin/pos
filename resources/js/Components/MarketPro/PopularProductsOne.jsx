import React, { useEffect, useState } from 'react'
import { Link } from '@inertiajs/react'
import { getCountdown } from '../../Helpers/Countdown';

const PopularProductsOne = ({ products = [], currency = 'BDT' }) => {
    const [timeLeft, setTimeLeft] = useState(getCountdown());

    useEffect(() => {
        const interval = setInterval(() => {
            setTimeLeft(getCountdown());
        }, 1000);

        return () => clearInterval(interval);
    }, []);

    const handleAddToCart = (product, primaryVariant, price) => {
        try {
            const cart = JSON.parse(localStorage.getItem('cart') ?? '[]');
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
            localStorage.setItem('cart', JSON.stringify(cart));
        } catch {
            // localStorage unavailable (private browsing, etc.) — fail silently
        }
    };

    return (
        <section className="popular-products pt-80">
            <div className="container container-lg">
                <div className="border border-gray-100 p-24 rounded-16">
                    <div className="section-heading mb-24">
                        <div className="flex-between flex-wrap gap-8">
                            <h5 className="mb-0">Popular Products</h5>
                            <div className="flex-align gap-16">
                                <Link
                                    href="/shop"
                                    className="text-sm fw-medium text-gray-700 hover-text-main-600 hover-text-decoration-underline"
                                >
                                    View All Products
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div className="popular-products-box rounded-16 overflow-hidden flex-between position-relative z-1 mb-24">
                        <img
                            src="/assets/images/bg/expensive-offer-bg.png"
                            alt=""
                            className="position-absolute inset-block-start-0 inset-block-start-0 w-100 h-100 z-n1"
                        />
                        <div className="d-lg-block d-none ps-32">
                            <img src="/assets/images/thumbs/expensive-offer1.png" alt="" />
                        </div>
                        <div className="popular-products-box__content px-sm-4 d-block w-100 text-center py-20">
                            <div className="flex-align gap-16 justify-content-center">
                                <h6 className="mb-0">Exclusive Offer</h6>
                                <h4 className="mb-0">45% OFF</h4>
                            </div>
                            <div className="countdown mt-4" id="countdown10">
                                <ul className="countdown-list style-four flex-center flex-wrap">
                                    <li className="countdown-list__item flex-align flex-column text-sm fw-medium text-white rounded-circle bg-neutral-600 w-56 h-56">
                                        <span className="days" />
                                        {timeLeft.days} <br /> Days
                                    </li>
                                    <li className="countdown-list__item flex-align flex-column text-sm fw-medium text-white rounded-circle bg-neutral-600 w-56 h-56">
                                        <span className="hours" />
                                        {timeLeft.hours} <br /> Hour
                                    </li>
                                    <li className="countdown-list__item flex-align flex-column text-sm fw-medium text-white rounded-circle bg-neutral-600 w-56 h-56">
                                        <span className="minutes" />
                                        {timeLeft.minutes} <br /> Min
                                    </li>
                                    <li className="countdown-list__item flex-align flex-column text-sm fw-medium text-white rounded-circle bg-neutral-600 w-56 h-56">
                                        <span className="seconds" />
                                        {timeLeft.seconds} <br /> Sec
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div className="d-lg-block d-none ">
                            <img src="/assets/images/thumbs/expensive-offer2.png" alt="" />
                        </div>
                    </div>
                    <div className="row gy-4">
                        {products.map((product) => {
                            const primaryVariant = product.variants?.[0];
                            const price = product.selling_price ?? primaryVariant?.selling_price;
                            const imageUrl = product.image ?? "/assets/images/thumbs/product-two-img1.png";

                            return (
                                <div className="col-xxl-3 col-xl-4 col-sm-6 col-xs-6" key={product.id}>
                                    <div className="product-card h-100 d-flex gap-16 p-16 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2">
                                        <Link
                                            href={`/product/${product.id}`}
                                            className="product-card__thumb flex-center h-unset rounded-8 bg-gray-50 position-relative w-unset flex-shrink-0 p-24"
                                            tabIndex={0}
                                        >
                                            <img
                                                src={imageUrl}
                                                alt={product.name}
                                                className="w-auto max-w-unset"
                                                onError={(e) => {
                                                    e.target.src = "/assets/images/thumbs/product-two-img1.png";
                                                }}
                                            />
                                        </Link>
                                        <div className="product-card__content flex-grow-1">
                                            {product.category && (
                                                <span className="text-xs text-gray-400 mb-4 d-block">
                                                    {product.category.name}
                                                </span>
                                            )}
                                            <h6 className="title text-lg fw-semibold mb-12">
                                                <Link
                                                    href={`/product/${product.id}`}
                                                    className="link text-line-2"
                                                    tabIndex={0}
                                                >
                                                    {product.name}
                                                </Link>
                                            </h6>
                                            <div className="product-card__price mb-12">
                                                {price ? (
                                                    <span className="text-heading text-md fw-semibold">
                                                        {currency} {Number(price).toLocaleString()}
                                                    </span>
                                                ) : (
                                                    <span className="text-gray-400 text-sm">Price on request</span>
                                                )}
                                            </div>
                                            <button
                                                type="button"
                                                onClick={() => handleAddToCart(product, primaryVariant, price)}
                                                disabled={!price}
                                                className="text-tertiary-600 flex-align gap-8 mt-24 bg-transparent border-0 p-0"
                                            >
                                                Add To Cart
                                                <i className="ph ph-shopping-cart d-flex" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </div>
            </div>
        </section>

    )
}

export default PopularProductsOne
