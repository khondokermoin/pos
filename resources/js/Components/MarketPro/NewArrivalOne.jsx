import React from 'react'
import { Link } from "@inertiajs/react";
import Slider from 'react-slick';

const NewArrivalOne = ({ products = [], currency = 'BDT' }) => {
    function SampleNextArrow(props) {
        const { className, onClick } = props;
        return (
            <button
                type="button" onClick={onClick}
                className={` ${className} slick-next slick-arrow flex-center rounded-circle border border-gray-100 hover-border-main-600 text-xl hover-bg-main-600 hover-text-white transition-1`}
            >
                <i className="ph ph-caret-right" />
            </button>
        );
    }
    function SamplePrevArrow(props) {
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
    }
    const settings = {
        dots: false,
        arrows: true,
        infinite: true,
        speed: 1000,
        slidesToShow: 6,
        slidesToScroll: 1,
        initialSlide: 0,
        autoplay: true,
        nextArrow: <SampleNextArrow />,
        prevArrow: <SamplePrevArrow />,
        responsive: [
            {
                breakpoint: 1599,
                settings: {
                    slidesToShow: 6,

                },
            },
            {
                breakpoint: 1399,
                settings: {
                    slidesToShow: 4,

                },
            },
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 3,

                },
            },
            {
                breakpoint: 575,
                settings: {
                    slidesToShow: 2,

                },
            },
            {
                breakpoint: 424,
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
        <section className="new-arrival pb-80">
            <div className="container container-lg">
                <div className="section-heading">
                    <div className="flex-between flex-wrap gap-8">
                        <h5 className="mb-0">New Arrivals</h5>
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
                <div className="new-arrival__slider arrow-style-two">
                    <Slider {...settings}>
                        {products.map((product) => {
                            const primaryVariant = product.variants?.[0];
                            const price =
                                product.selling_price ??
                                primaryVariant?.selling_price;
                            const imageUrl =
                                product.image ??
                                "/assets/images/thumbs/product-img20.png";

                            return (
                                <div key={product.id}>
                                    <div className="product-card px-8 py-16 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2">
                                        <Link
                                            href={`/product/${product.id}`}
                                            className="product-card__thumb flex-center"
                                        >
                                            <img
                                                src={imageUrl}
                                                alt={product.name}
                                                onError={(e) => {
                                                    e.target.src =
                                                        "/assets/images/thumbs/product-img20.png";
                                                }}
                                            />
                                        </Link>
                                        <div className="product-card__content mt-12">
                                            <h6 className="title text-lg fw-semibold mt-12 mb-8">
                                                <Link
                                                    href={`/product/${product.id}`}
                                                    className="link text-line-2"
                                                >
                                                    {product.name}
                                                </Link>
                                            </h6>
                                            <div className="flex-between gap-8 mt-24 flex-wrap">
                                                <div className="product-card__price">
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
                                                    className="product-card__cart btn btn-main py-11 px-24 rounded-pill flex-align gap-8"
                                                >
                                                    Add{" "}
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
        </section>

    )
}

export default NewArrivalOne
