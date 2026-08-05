import React from 'react'
import { Link } from '@inertiajs/react';
import Slider from 'react-slick';

const TopSellingTwo = ({ products = [], currency = 'BDT' }) => {
    function SampleNextArrow(props) {
        const { className, onClick } = props;
        return (
            <button
                type="button" onClick={onClick}
                className={` ${className} slick-next slick-arrow flex-center rounded-circle border border-gray-100 hover-border-neutral-600 text-xl hover-bg-neutral-600 hover-text-white transition-1`}
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
                className={`${className} slick-prev slick-arrow flex-center rounded-circle border border-gray-100 hover-border-neutral-600 text-xl hover-bg-neutral-600 hover-text-white transition-1`}
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
        <section className="recommended">
            <div className="container container-lg">
                <div className="row g-12">
                    <div className="col-xxl-4">
                        <div className="position-relative rounded-16 bg-light-purple overflow-hidden p-28 z-1 text-center h-100">
                            <img
                                src="/assets/images/bg/recommended-bg.png"
                                alt=""
                                className="position-absolute inset-block-start-0 inset-inline-start-0 z-n1 w-100 h-100 cover-img"
                            />
                            <div className="py-xl-4 text-center">
                                <span className="h6 mb-20 text-white">
                                    Insta360 GO 3S Action Camera - White
                                </span>
                                <div className="flex-center gap-12 text-white">
                                    <span className="">FROM</span>
                                    <h4 className="mb-8 text-white">$430</h4>
                                    <span className="badge-style-two position-relative me-8 bg-main-two-600 text-white text-sm py-2 px-8 rounded-4">
                                        20% off
                                    </span>
                                </div>
                            </div>
                            <img
                                src="/assets/images/thumbs/recommended-img.png"
                                alt=""
                                className="mt-48 d-xxl-block d-none"
                            />
                        </div>
                    </div>
                    <div className="col-xxl-8">
                        <div className="border border-gray-100 p-24 rounded-16">
                            <div className="section-heading mb-24">
                                <div className="flex-between flex-wrap gap-8">
                                    <h5 className="mb-0">Recommended For You</h5>
                                    <div className="flex-align mr-point gap-16">
                                        <Link
                                            href="/shop"
                                            className="text-sm fw-medium text-gray-700 hover-text-main-600 hover-text-decoration-underline"
                                        >
                                            View All
                                        </Link>

                                    </div>
                                </div>
                            </div>
                            {products.length > 0 && (
                                <div className="recommended-slider">
                                    <Slider {...settings}>
                                        {products.map((product) => {
                                            const primaryVariant = product.variants?.[0];
                                            const price = product.selling_price ?? primaryVariant?.selling_price;
                                            const imageUrl = product.image ?? "/assets/images/thumbs/product-two-img1.png";

                                            return (
                                                <div key={product.id}>
                                                    <div className="product-card h-100 p-16 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2">
                                                        <Link
                                                            href={`/product/${product.id}`}
                                                            className="product-card__thumb flex-center rounded-8 bg-gray-50 position-relative"
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
                                                        <div className="product-card__content mt-16">
                                                            {product.category && (
                                                                <span className="text-xs text-gray-400 mb-4 d-block">
                                                                    {product.category.name}
                                                                </span>
                                                            )}
                                                            <h6 className="title text-lg fw-semibold my-16">
                                                                <Link
                                                                    href={`/product/${product.id}`}
                                                                    className="link text-line-2"
                                                                    tabIndex={0}
                                                                >
                                                                    {product.name}
                                                                </Link>
                                                            </h6>
                                                            <div className="product-card__price mt-16 mb-30">
                                                                {price ? (
                                                                    <span className="text-heading text-md fw-semibold ">
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
                                                                className="product-card__cart btn bg-gray-50 text-heading hover-bg-main-600 hover-text-white py-11 px-24 rounded-8 flex-center gap-8 fw-medium w-100"
                                                            >
                                                                Add To Cart <i className="ph ph-shopping-cart" />
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            );
                                        })}
                                    </Slider>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </section>

    )
}

export default TopSellingTwo
