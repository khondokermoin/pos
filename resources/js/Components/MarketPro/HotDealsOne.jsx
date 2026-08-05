import React, { memo, useEffect, useState } from 'react'
import { Link } from "@inertiajs/react";
import Slider from 'react-slick';
import { getCountdown } from '../../Helpers/Countdown';



const SampleNextArrow = memo((props) => {
    const { className, onClick } = props;
    return (
        <button
            type="button" onClick={onClick}
            className={` ${className} slick-next slick-arrow flex-center rounded-circle border border-gray-100 hover-border-main-600 text-xl hover-bg-main-600 hover-text-white transition-1`}
        >
            <i className="ph ph-caret-right" />
        </button>
    );
});

const SamplePrevArrow = memo((props) => {
    const { className, onClick } = props;
    return (
        <button
            type="button"
            onClick={onClick}
            className={`${className} slick-prev slick-arrow flex-center rounded-circle border border-gray-100 hover-border-main-600 text-xl hover-bg-main-600 hover-text-white transition-1`}
        >
            <i className="ph ph-caret-left" />
        </button>
    );
});


const HotDealsOne = ({ products = [], currency = 'BDT' }) => {
    const [timeLeft, setTimeLeft] = useState(getCountdown());

    useEffect(() => {
        const interval = setInterval(() => {
            setTimeLeft(getCountdown());
        }, 1000);

        return () => clearInterval(interval);
    }, []);

    const settings = {
        dots: false,
        arrows: true,
        infinite: true,
        speed: 1000,
        slidesToShow: 4,
        slidesToScroll: 1,
        initialSlide: 0,
        autoplay: true,
        nextArrow: <SampleNextArrow />,
        prevArrow: <SamplePrevArrow />,
        responsive: [
            {
                breakpoint: 1399,
                settings: {
                    slidesToShow: 3,
                },
            },
            {
                breakpoint: 1199,
                settings: {
                    slidesToShow: 2,
                },
            },
            {
                breakpoint: 575,
                settings: {
                    slidesToShow: 1,
                },
            },

        ],
    };

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
        <section className="hot-deals pt-80">
            <div className="container container-lg">
                <div className="section-heading">
                    <div className="flex-between flex-wrap gap-8">
                        <h5 className="mb-0">Hot Deals Todays</h5>
                        <div className="flex-align mr-point gap-16">
                            <Link
                                href="/shop"
                                className="text-sm fw-medium text-gray-700 hover-text-main-600 hover-text-decoration-underline"
                            >
                                View All Deals
                            </Link>
                        </div>
                    </div>
                </div>
                <div className="row g-12">
                    <div className="col-md-4">
                        <div className="hot-deals position-relative rounded-16 bg-main-600 overflow-hidden p-28 z-1 text-center">
                            <img
                                src="/assets/images/shape/offer-shape.png"
                                alt=""
                                className="position-absolute inset-block-start-0 inset-inline-start-0 z-n1 w-100 h-100 opacity-6"
                            />
                            <div className="hot-deals__thumb">
                                <img src="/assets/images/thumbs/hot-deals-img.png" alt="" />
                            </div>
                            <div className="py-xl-4">
                                <h4 className="text-white mb-8">Fresh Vegetables</h4>
                                <div className="countdown my-32" id="countdown4">
                                    <ul className="countdown-list flex-center flex-wrap">
                                        <li className="countdown-list__item text-heading flex-align gap-4 text-sm fw-medium colon-white">
                                            <span className="days" />
                                            {timeLeft.days} Days
                                        </li>
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
                    <div className="col-md-8">
                        <div className="hot-deals-slider arrow-style-two">
                            <Slider {...settings}>
                                {products.map((product) => {
                                    const primaryVariant = product.variants?.[0];
                                    const price =
                                        product.selling_price ??
                                        primaryVariant?.selling_price;
                                    const imageUrl =
                                        product.image ??
                                        "/assets/images/thumbs/product-img8.png";

                                    return (
                                        <div key={product.id}>
                                            <div className="product-card h-100 p-8 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2">
                                                <Link
                                                    href={`/product/${product.id}`}
                                                    className="product-card__thumb flex-center"
                                                >
                                                    <img
                                                        src={imageUrl}
                                                        alt={product.name}
                                                        onError={(e) => {
                                                            e.target.src =
                                                                "/assets/images/thumbs/product-img8.png";
                                                        }}
                                                    />
                                                </Link>
                                                <div className="product-card__content p-sm-2">
                                                    <h6 className="title text-lg fw-semibold mt-12 mb-8">
                                                        <Link
                                                            href={`/product/${product.id}`}
                                                            className="link text-line-2"
                                                        >
                                                            {product.name}
                                                        </Link>
                                                    </h6>
                                                    <div className="product-card__content mt-12">
                                                        <div className="product-card__price mb-8">
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
                                        </div>
                                    );
                                })}
                            </Slider>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    )
}

export default HotDealsOne