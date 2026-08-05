import React from 'react'
import { Link } from "@inertiajs/react";

const ProductListOne = ({ products = [], currency = 'BDT' }) => {
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
        <div className="product mt-24">
            <div className="container container-lg">
                <div className="row gy-4 g-12">
                    {products.map((product) => {
                        const primaryVariant = product.variants?.[0];
                        const price =
                            product.selling_price ?? primaryVariant?.selling_price;
                        const imageUrl =
                            product.image ?? "/assets/images/thumbs/product-img1.png";

                        return (
                            <div
                                className="col-xxl-2 col-lg-3 col-sm-4 col-6"
                                key={product.id}
                            >
                                <div className="product-card px-8 py-16 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2">
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
                                        className="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 position-absolute inset-block-start-0 inset-inline-end-0 me-16 mt-16"
                                    >
                                        Add <i className="ph ph-shopping-cart" />
                                    </button>
                                    <Link
                                        href={`/product/${product.id}`}
                                        className="product-card__thumb flex-center"
                                    >
                                        <img
                                            src={imageUrl}
                                            alt={product.name}
                                            onError={(e) => {
                                                e.target.src =
                                                    "/assets/images/thumbs/product-img1.png";
                                            }}
                                        />
                                    </Link>
                                    <div className="product-card__content mt-12">
                                        <div className="product-card__price mb-16">
                                            {price ? (
                                                <span className="text-heading text-md fw-semibold ">
                                                    {currency}{" "}
                                                    {Number(price).toLocaleString()}{" "}
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
                                    </div>
                                </div>
                            </div>
                        );
                    })}
                </div>
            </div>
        </div>

    )
}

export default ProductListOne
