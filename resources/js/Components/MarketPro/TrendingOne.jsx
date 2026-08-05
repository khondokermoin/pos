import React, { useState } from 'react'
import { Link } from '@inertiajs/react'

const TrendingOne = ({ products = [], currency = 'BDT', categories = [] }) => {
    const [activeTab, setActiveTab] = useState('all');

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

    const filteredProducts =
        activeTab === 'all'
            ? products
            : products.filter((product) => product.category?.id === activeTab);

    return (
        <section className="trending-productss pt-80">
            <div className="container container-lg">
                <div className="border border-gray-100 p-24 rounded-16">
                    <div className="section-heading mb-24">
                        <div className="flex-between flex-wrap gap-8">
                            <h5 className="mb-0">Trending Products</h5>
                            <ul
                                className="nav common-tab style-two nav-pills"
                                id="pills-tab"
                                role="tablist"
                            >
                                <li className="nav-item" role="presentation">
                                    <button
                                        className={`nav-link${activeTab === 'all' ? ' active' : ''}`}
                                        type="button"
                                        role="tab"
                                        aria-selected={activeTab === 'all'}
                                        onClick={() => setActiveTab('all')}
                                    >
                                        All
                                    </button>
                                </li>
                                {categories.map((cat) => (
                                    <li className="nav-item" role="presentation" key={cat.id}>
                                        <button
                                            className={`nav-link${activeTab === cat.id ? ' active' : ''}`}
                                            type="button"
                                            role="tab"
                                            aria-selected={activeTab === cat.id}
                                            onClick={() => setActiveTab(cat.id)}
                                        >
                                            {cat.name}
                                        </button>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    </div>
                    <div className="trending-products-box rounded-16 overflow-hidden flex-between position-relative mb-24">
                        <div className="d-md-block d-none ps-xxl-5 ps-md-4">
                            <img src="/assets/images/thumbs/trending-products-img1.png" alt="" />
                        </div>
                        <div className="trending-products-box__content px-4 d-block w-100 text-center py-32">
                            <h6 className="mb-0 trending-products-box__title">
                                Laptop Pro <span className="h4 mb-0 fw-semibold">20%</span> off All
                                Time On Order Now $980
                            </h6>
                        </div>
                        <div className="d-md-block d-none pe-xxl-5 me-xxl-5 pe-md-4">
                            <img src="/assets/images/thumbs/trending-products-img2.png" alt="" />
                        </div>
                    </div>
                    <div className="tab-content" id="pills-tabContent">
                        <div className="tab-pane fade show active" role="tabpanel">
                            <div className="row g-12">
                                {filteredProducts.length === 0 ? (
                                    <div className="text-center py-40">
                                        <p className="text-gray-500 mb-0">No products found in this category.</p>
                                    </div>
                                ) : (
                                    filteredProducts.map((product) => {
                                        const primaryVariant = product.variants?.[0];
                                        const price = product.selling_price ?? primaryVariant?.selling_price;
                                        const imageUrl = product.image ?? "/assets/images/thumbs/product-two-img1.png";

                                        return (
                                            <div className="col-xxl-2 col-xl-3 col-lg-4 col-sm-6" key={product.id}>
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
                                    })
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    )
}

export default TrendingOne
