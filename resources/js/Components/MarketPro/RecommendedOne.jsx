import React from 'react'
import { Link } from "@inertiajs/react";

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

const RecommendedProductCard = ({ product, currency }) => {
    const primaryVariant = product.variants?.[0];
    const price = product.selling_price ?? primaryVariant?.selling_price;
    const imageUrl = product.image ?? "/assets/images/thumbs/product-img7.png";

    return (
        <div className="col-xxl-2 col-lg-3 col-sm-4 col-6">
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
                                "/assets/images/thumbs/product-img7.png";
                        }}
                    />
                </Link>
                <div className="product-card__content p-sm-2">
                    <h6 className="title text-lg fw-semibold mt-12 mb-8">
                        <Link href={`/product/${product.id}`} className="link text-line-2">
                            {product.name}
                        </Link>
                    </h6>
                    <div className="product-card__content mt-12">
                        <div className="product-card__price mb-8">
                            {price ? (
                                <span className="text-heading text-md fw-semibold ">
                                    {currency} {Number(price).toLocaleString()}{" "}
                                    <span className="text-gray-500 fw-normal">/Qty</span>{" "}
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
                                handleAddToCart(product, primaryVariant, price)
                            }
                            disabled={!price}
                            className="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center"
                        >
                            Add To Cart <i className="ph ph-shopping-cart" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};

const RecommendedTabPane = ({ id, active, ariaLabelledBy, products, currency }) => (
    <div
        className={`tab-pane fade${active ? " show active" : ""}`}
        id={id}
        role="tabpanel"
        aria-labelledby={ariaLabelledBy}
        tabIndex={0}
    >
        <div className="row g-12">
            {products.map((product) => (
                <RecommendedProductCard
                    key={product.id}
                    product={product}
                    currency={currency}
                />
            ))}
        </div>
    </div>
);

const RecommendedOne = ({ products = [], currency = 'BDT' }) => {
    return (
        <section className="recommended">
            <div className="container container-lg">
                <div className="section-heading flex-between flex-wrap gap-16">
                    <h5 className="mb-0">Recommended for you</h5>
                    <ul className="nav common-tab nav-pills" id="pills-tab" role="tablist">
                        <li className="nav-item" role="presentation">
                            <button
                                className="nav-link active"
                                id="pills-all-tab"
                                data-bs-toggle="pill"
                                data-bs-target="#pills-all"
                                type="button"
                                role="tab"
                                aria-controls="pills-all"
                                aria-selected="true"
                            >
                                All
                            </button>
                        </li>
                        <li className="nav-item" role="presentation">
                            <button
                                className="nav-link"
                                id="pills-grocery-tab"
                                data-bs-toggle="pill"
                                data-bs-target="#pills-grocery"
                                type="button"
                                role="tab"
                                aria-controls="pills-grocery"
                                aria-selected="false"
                            >
                                Grocery
                            </button>
                        </li>
                        <li className="nav-item" role="presentation">
                            <button
                                className="nav-link"
                                id="pills-fruits-tab"
                                data-bs-toggle="pill"
                                data-bs-target="#pills-fruits"
                                type="button"
                                role="tab"
                                aria-controls="pills-fruits"
                                aria-selected="false"
                            >
                                Fruits
                            </button>
                        </li>
                        <li className="nav-item" role="presentation">
                            <button
                                className="nav-link"
                                id="pills-juices-tab"
                                data-bs-toggle="pill"
                                data-bs-target="#pills-juices"
                                type="button"
                                role="tab"
                                aria-controls="pills-juices"
                                aria-selected="false"
                            >
                                Juices
                            </button>
                        </li>
                        <li className="nav-item" role="presentation">
                            <button
                                className="nav-link"
                                id="pills-vegetables-tab"
                                data-bs-toggle="pill"
                                data-bs-target="#pills-vegetables"
                                type="button"
                                role="tab"
                                aria-controls="pills-vegetables"
                                aria-selected="false"
                            >
                                Vegetables
                            </button>
                        </li>
                        <li className="nav-item" role="presentation">
                            <button
                                className="nav-link"
                                id="pills-snacks-tab"
                                data-bs-toggle="pill"
                                data-bs-target="#pills-snacks"
                                type="button"
                                role="tab"
                                aria-controls="pills-snacks"
                                aria-selected="false"
                            >
                                Snacks
                            </button>
                        </li>
                        <li className="nav-item" role="presentation">
                            <button
                                className="nav-link"
                                id="pills-organic-tab"
                                data-bs-toggle="pill"
                                data-bs-target="#pills-organic"
                                type="button"
                                role="tab"
                                aria-controls="pills-organic"
                                aria-selected="false"
                            >
                                Organic Foods
                            </button>
                        </li>
                    </ul>
                </div>
                <div className="tab-content" id="pills-tabContent">
                    <RecommendedTabPane
                        id="pills-all"
                        active
                        ariaLabelledBy="pills-all-tab"
                        products={products}
                        currency={currency}
                    />
                    <RecommendedTabPane
                        id="pills-grocery"
                        ariaLabelledBy="pills-grocery-tab"
                        products={products}
                        currency={currency}
                    />
                    <RecommendedTabPane
                        id="pills-fruits"
                        ariaLabelledBy="pills-fruits-tab"
                        products={products}
                        currency={currency}
                    />
                    <RecommendedTabPane
                        id="pills-juices"
                        ariaLabelledBy="pills-juices-tab"
                        products={products}
                        currency={currency}
                    />
                    <RecommendedTabPane
                        id="pills-vegetables"
                        ariaLabelledBy="pills-vegetables-tab"
                        products={products}
                        currency={currency}
                    />
                    <RecommendedTabPane
                        id="pills-snacks"
                        ariaLabelledBy="pills-snacks-tab"
                        products={products}
                        currency={currency}
                    />
                    <RecommendedTabPane
                        id="pills-organic"
                        ariaLabelledBy="pills-organic-tab"
                        products={products}
                        currency={currency}
                    />
                </div>
            </div>
        </section>

    )
}

export default RecommendedOne
