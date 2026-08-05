import React from 'react'
import { Link } from '@inertiajs/react';
import Slider from 'react-slick';

const FeaturedOne = ({ products = [], currency = 'BDT' }) => {
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
        slidesToShow: 2,
        slidesToScroll: 1,
        initialSlide: 0,
        autoplay: true,
        nextArrow: <SampleNextArrow />,
        prevArrow: <SamplePrevArrow />,
        responsive: [
            {
                breakpoint: 991,
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

    // Group products into pairs — each slide shows 2 stacked product cards
    const productPairs = [];
    for (let i = 0; i < products.length; i += 2) {
        productPairs.push(products.slice(i, i + 2));
    }

    const renderProductRow = (product) => {
        const primaryVariant = product.variants?.[0];
        const price = product.selling_price ?? primaryVariant?.selling_price;
        const imageUrl = product.image ?? "/assets/images/thumbs/product-two-img1.png";

        return (
            <div className="" key={product.id}>
                <div className="mt-24 product-card d-flex gap-16 p-16 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2">
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
                    <div className="product-card__content my-20 flex-grow-1">
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
                        <div className="product-card__price my-20">
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
                            className="product-card__cart btn bg-gray-50 text-heading hover-bg-main-600 hover-text-white py-11 px-24 rounded-8 flex-center gap-8 fw-medium"
                        >
                            Add To Cart <i className="ph ph-shopping-cart" />
                        </button>
                    </div>
                </div>
            </div>
        );
    };

    return (
        <section className="featured-products">
            <div className="container container-lg">
                <div className="row g-4 flex-wrap-reverse">
                    <div className="col-xxl-8">
                        <div className="border border-gray-100 p-24 rounded-16">
                            <div className="section-heading mb-24">
                                <div className="flex-between flex-wrap gap-8">
                                    <h5 className="mb-0">Featured Products </h5>
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
                            {productPairs.length > 0 && (
                                <div className="row gy-4 featured-product-slider">
                                    <Slider {...settings}>
                                        {productPairs.map((pair, idx) => (
                                            <div className="col-xxl-6" key={idx}>
                                                <div className="featured-products__sliders">
                                                    {pair.map((product) => renderProductRow(product))}
                                                </div>
                                            </div>
                                        ))}
                                    </Slider>
                                </div>
                            )}
                        </div>
                    </div>
                    <div className="col-xxl-4">
                        <div className="position-relative rounded-16 bg-light-purple overflow-hidden p-28 pb-0 z-1 text-center h-100">
                            <img
                                src="/assets/images/bg/featured-product-bg.png"
                                alt=""
                                className="position-absolute inset-block-start-0 inset-inline-start-0 z-n1 w-100 h-100 cover-img"
                            />
                            <div className="py-xl-4 text-center">
                                <span className="h6 mb-20 text-white">
                                    iPhone Smart Phone - Red
                                </span>
                                <div className="flex-center gap-12 text-white">
                                    <span className="">FROM</span>
                                    <h4 className="mb-8 text-white">$890</h4>
                                    <span className="badge-style-two position-relative me-8 bg-main-two-600 text-white text-sm py-2 px-8 rounded-4">
                                        20% off
                                    </span>
                                </div>
                                <Link
                                    href="/shop"
                                    className="mt-16 mb-24 btn btn-main-two fw-medium d-inline-flex align-items-center rounded-pill gap-8"
                                    tabIndex={0}
                                >
                                    Shop Now
                                    <span className="icon text-xl d-flex">
                                        <i className="ph ph-arrow-right" />
                                    </span>
                                </Link>
                            </div>
                            <img
                                src="/assets/images/thumbs/featured-product-img.png"
                                alt=""
                                className="d-xxl-inline-flex d-none"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </section>

    )
}

export default FeaturedOne
