import React, { useState, useEffect } from "react";
import { Link } from "@inertiajs/react";
import QuantityControl from "../../Helpers/QuantityControl";

/**
 * CartSection — reads the real cart from localStorage and renders it.
 *
 * Cart items are written by product listing components (ProductListOne,
 * BestSellsOne, FlashSalesOne, etc.) using the shape:
 *   { id, variantId, name, price, image, quantity }
 *
 * After any add-to-cart action those components should dispatch:
 *   window.dispatchEvent(new Event('cart:updated'));
 * so this component (and the header badge) re-reads immediately.
 */
const CartSection = () => {
    const [cartItems, setCartItems] = useState([]);

    const readCart = () => {
        try {
            const stored = localStorage.getItem("cart");
            setCartItems(stored ? JSON.parse(stored) : []);
        } catch {
            setCartItems([]);
        }
    };

    useEffect(() => {
        readCart();
        // Re-read when another tab changes localStorage OR when the same tab
        // dispatches the custom 'cart:updated' event after an add-to-cart.
        window.addEventListener("storage", readCart);
        window.addEventListener("cart:updated", readCart);
        return () => {
            window.removeEventListener("storage", readCart);
            window.removeEventListener("cart:updated", readCart);
        };
    }, []);

    const removeItem = (index) => {
        const updated = cartItems.filter((_, i) => i !== index);
        localStorage.setItem("cart", JSON.stringify(updated));
        setCartItems(updated);
        window.dispatchEvent(new Event("cart:updated"));
    };

    const updateQty = (index, qty) => {
        const updated = cartItems.map((item, i) =>
            i === index ? { ...item, quantity: Math.max(1, qty) } : item,
        );
        localStorage.setItem("cart", JSON.stringify(updated));
        setCartItems(updated);
        window.dispatchEvent(new Event("cart:updated"));
    };

    const subtotal = cartItems.reduce(
        (sum, item) => sum + parseFloat(item.price ?? 0) * (item.quantity ?? 1),
        0,
    );

    if (cartItems.length === 0) {
        return (
            <section className="cart py-80">
                <div className="container container-lg">
                    <div className="text-center py-80">
                        <span className="text-6xl d-block mb-24">
                            <i className="ph ph-shopping-cart-simple text-gray-300" />
                        </span>
                        <h4 className="mb-16 text-gray-600">
                            Your cart is empty
                        </h4>
                        <p className="text-gray-500 mb-32">
                            Browse our products and add items to your cart.
                        </p>
                        <Link
                            href="/shop"
                            className="btn btn-main py-18 px-40 rounded-8"
                        >
                            Continue Shopping
                        </Link>
                    </div>
                </div>
            </section>
        );
    }

    return (
        <section className="cart py-80">
            <div className="container container-lg">
                <div className="row gy-4">
                    <div className="col-xl-9 col-lg-8">
                        <div className="cart-table border border-gray-100 rounded-8 px-40 py-48">
                            <div className="overflow-x-auto scroll-sm scroll-sm-horizontal">
                                <table className="table style-three">
                                    <thead>
                                        <tr>
                                            <th className="h6 mb-0 text-lg fw-bold">
                                                Delete
                                            </th>
                                            <th className="h6 mb-0 text-lg fw-bold">
                                                Product Name
                                            </th>
                                            <th className="h6 mb-0 text-lg fw-bold">
                                                Price
                                            </th>
                                            <th className="h6 mb-0 text-lg fw-bold">
                                                Quantity
                                            </th>
                                            <th className="h6 mb-0 text-lg fw-bold">
                                                Subtotal
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {cartItems.map((item, index) => (
                                            <tr
                                                key={`${item.variantId ?? item.id}-${index}`}
                                            >
                                                <td>
                                                    <button
                                                        type="button"
                                                        onClick={() =>
                                                            removeItem(index)
                                                        }
                                                        className="remove-tr-btn flex-align gap-12 hover-text-danger-600"
                                                    >
                                                        <i className="ph ph-x-circle text-2xl d-flex" />
                                                        Remove
                                                    </button>
                                                </td>
                                                <td>
                                                    <div className="table-product d-flex align-items-center gap-24">
                                                        <Link
                                                            href={
                                                                item.productId
                                                                    ? `/product/${item.productId}`
                                                                    : "/shop"
                                                            }
                                                            className="table-product__thumb border border-gray-100 rounded-8 flex-center"
                                                        >
                                                            <img
                                                                src={
                                                                    item.image ??
                                                                    "/assets/images/thumbs/product-placeholder.png"
                                                                }
                                                                alt={
                                                                    item.name ??
                                                                    "Product"
                                                                }
                                                                loading="lazy"
                                                            />
                                                        </Link>
                                                        <div className="table-product__content text-start">
                                                            <h6 className="title text-lg fw-semibold mb-8">
                                                                <Link
                                                                    href={
                                                                        item.productId
                                                                            ? `/product/${item.productId}`
                                                                            : "/shop"
                                                                    }
                                                                    className="link text-line-2"
                                                                >
                                                                    {item.name ??
                                                                        "Product"}
                                                                </Link>
                                                            </h6>
                                                            {item.variantLabel && (
                                                                <span className="text-sm text-gray-500">
                                                                    {
                                                                        item.variantLabel
                                                                    }
                                                                </span>
                                                            )}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span className="text-lg h6 mb-0 fw-semibold">
                                                        {item.currency ?? "৳"}
                                                        {parseFloat(
                                                            item.price ?? 0,
                                                        ).toFixed(2)}
                                                    </span>
                                                </td>
                                                <td>
                                                    <QuantityControl
                                                        initialQuantity={
                                                            item.quantity ?? 1
                                                        }
                                                        onChange={(qty) =>
                                                            updateQty(
                                                                index,
                                                                qty,
                                                            )
                                                        }
                                                    />
                                                </td>
                                                <td>
                                                    <span className="text-lg h6 mb-0 fw-semibold">
                                                        {item.currency ?? "৳"}
                                                        {(
                                                            parseFloat(
                                                                item.price ?? 0,
                                                            ) *
                                                            (item.quantity ?? 1)
                                                        ).toFixed(2)}
                                                    </span>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                            <div className="flex-between flex-wrap gap-16 mt-16">
                                <div className="flex-align gap-16">
                                    <input
                                        type="text"
                                        className="common-input"
                                        placeholder="Coupon Code"
                                    />
                                    <button
                                        type="submit"
                                        className="btn btn-main py-18 w-100 rounded-8"
                                    >
                                        Apply Coupon
                                    </button>
                                </div>
                                <button
                                    type="button"
                                    onClick={readCart}
                                    className="text-lg text-gray-500 hover-text-main-600"
                                >
                                    Update Cart
                                </button>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-3 col-lg-4">
                        <div className="cart-sidebar border border-gray-100 rounded-8 px-24 py-40">
                            <h6 className="text-xl mb-32">Cart Totals</h6>
                            <div className="bg-color-three rounded-8 p-24">
                                <div className="mb-32 flex-between gap-8">
                                    <span className="text-gray-900 font-heading-two">
                                        Subtotal
                                    </span>
                                    <span className="text-gray-900 fw-semibold">
                                        ৳{subtotal.toFixed(2)}
                                    </span>
                                </div>
                                <div className="mb-32 flex-between gap-8">
                                    <span className="text-gray-900 font-heading-two">
                                        Estimated Delivery
                                    </span>
                                    <span className="text-gray-900 fw-semibold">
                                        Free
                                    </span>
                                </div>
                                <div className="mb-0 flex-between gap-8">
                                    <span className="text-gray-900 font-heading-two">
                                        Estimated Tax
                                    </span>
                                    <span className="text-gray-900 fw-semibold">
                                        ৳0.00
                                    </span>
                                </div>
                            </div>
                            <div className="bg-color-three rounded-8 p-24 mt-24">
                                <div className="flex-between gap-8">
                                    <span className="text-gray-900 text-xl fw-semibold">
                                        Total
                                    </span>
                                    <span className="text-gray-900 text-xl fw-semibold">
                                        ৳{subtotal.toFixed(2)}
                                    </span>
                                </div>
                            </div>
                            <Link
                                href="/checkout"
                                className="btn btn-main mt-40 py-18 w-100 rounded-8"
                            >
                                Proceed to checkout
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default CartSection;
