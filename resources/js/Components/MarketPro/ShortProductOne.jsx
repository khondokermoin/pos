import React from 'react'
import { Link } from "@inertiajs/react";
import Slider from 'react-slick';

const chunk = (arr, size) => {
    const chunks = [];
    for (let i = 0; i < arr.length; i += size) {
        chunks.push(arr.slice(i, i + size));
    }
    return chunks;
};

const ShortProductItem = ({ product, currency, isLast }) => {
    const primaryVariant = product.variants?.[0];
    const price = product.selling_price ?? primaryVariant?.selling_price;
    const imageUrl =
        product.image ?? "/assets/images/thumbs/short-product-img1.png";

    return (
        <div className={`flex-align gap-16 ${isLast ? "" : "mb-40"}`}>
            <div className="w-90 h-90 rounded-12 border border-gray-100 flex-shrink-0">
                <Link href={`/product/${product.id}`} className="link">
                    <img
                        src={imageUrl}
                        alt={product.name}
                        onError={(e) => {
                            e.target.src =
                                "/assets/images/thumbs/short-product-img1.png";
                        }}
                    />
                </Link>
            </div>
            <div className="product-card__content mt-12">
                <h6 className="title text-lg fw-semibold mt-8 mb-8">
                    <Link href={`/product/${product.id}`} className="link text-line-1">
                        {product.name}
                    </Link>
                </h6>
                <div className="product-card__price flex-align gap-8">
                    {price ? (
                        <span className="text-heading text-md fw-semibold d-block">
                            {currency} {Number(price).toLocaleString()}
                        </span>
                    ) : (
                        <span className="text-gray-400 text-sm">
                            Price on request
                        </span>
                    )}
                </div>
            </div>
        </div>
    );
};

const ShortProductColumn = ({ title, products, currency, settings }) => {
    const slides = chunk(products, 4);

    return (
        <div className="col-xxl-3 col-lg-4 col-sm-6">
            <div className="p-16 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 ">
                <div className="p-16 bg-main-50 rounded-16 mb-32">
                    <h6 className="underlined-line position-relative mb-0 pb-16 d-inline-block">
                        {title}
                    </h6>
                </div>
                <div className="short-product-list arrow-style-two">
                    {slides.length > 0 && (
                        <Slider {...settings}>
                            {slides.map((slideProducts, slideIndex) => (
                                <div key={slideIndex}>
                                    {slideProducts.map((product, i) => (
                                        <ShortProductItem
                                            key={product.id}
                                            product={product}
                                            currency={currency}
                                            isLast={i === slideProducts.length - 1}
                                        />
                                    ))}
                                </div>
                            ))}
                        </Slider>
                    )}
                </div>
            </div>
        </div>
    );
};

const ShortProductOne = ({ products = [], currency = 'BDT' }) => {

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
        speed: 1500,
        slidesToShow: 1,
        slidesToScroll: 1,
        initialSlide: 0,
        autoplay: true,
        nextArrow: <SampleNextArrow />,
        prevArrow: <SamplePrevArrow />,
        responsive: [
            {
                breakpoint: 768,
                settings: {
                    arrows: false,
                },
            },
            {
                breakpoint: 575,
                settings: {
                    arrows: true,
                },
            },

        ],

    };
    return (
        <div className="short-product">
            <div className="container container-lg">
                <div className="row gy-4">
                    <ShortProductColumn
                        title="Featured Products"
                        products={products}
                        currency={currency}
                        settings={settings}
                    />
                    <ShortProductColumn
                        title="Top Selling Products"
                        products={products}
                        currency={currency}
                        settings={settings}
                    />
                    <ShortProductColumn
                        title="On-sale Products"
                        products={products}
                        currency={currency}
                        settings={settings}
                    />
                    <ShortProductColumn
                        title="Top Rated Products"
                        products={products}
                        currency={currency}
                        settings={settings}
                    />
                </div>
            </div>
        </div>

    )
}

export default ShortProductOne
