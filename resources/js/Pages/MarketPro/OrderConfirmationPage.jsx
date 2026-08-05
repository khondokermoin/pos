import React from "react";
import { usePage, Link } from "@inertiajs/react";
import Preloader from "../../Helpers/Preloader";
import ColorInit from "../../Helpers/ColorInit";
import HeaderTwo from "../../Components/MarketPro/HeaderTwo";
import FooterTwo from "../../Components/MarketPro/FooterTwo";
import BottomFooter from "../../Components/MarketPro/BottomFooter";
import ScrollToTop from "react-scroll-to-top";

/**
 * OrderConfirmationPage — Shown after a successful online order placement.
 *
 * DATA SOURCE: OnlineOrderController::confirmation() via Inertia::render(...)
 *
 * PROPS (from usePage().props):
 *   - order : {
 *       id, invoice_no, total_amount, status, payment_method,
 *       delivery_city, delivery_address,
 *       branch_name, branch_city, routing_method,
 *       notes, created_at,
 *       items: [{ product_name, variant_name, quantity, unit_price, subtotal }]
 *     }
 *   - tenant : Company branding (shared props)
 */
const OrderConfirmationPage = () => {
    const { order, tenant } = usePage().props;
    const currency = tenant?.currency ?? "BDT";

    const routingMethodLabel = {
        coverage_match: "Matched to your delivery area",
        distance: "Nearest branch by location",
        default: "Default branch",
        fallback: "Available branch",
    };

    return (
        <>
            <ColorInit color={true} />
            <ScrollToTop smooth color="#FA6400" />
            <Preloader />
            <HeaderTwo category={true} />

            <section className="order-confirmation py-80">
                <div className="container container-lg">
                    <div className="row justify-content-center">
                        <div className="col-lg-8">
                            {/* Success Banner */}
                            <div className="text-center mb-48">
                                <div className="w-80 h-80 rounded-circle bg-success-100 flex-center mx-auto mb-24">
                                    <i className="ph-fill ph-check-circle text-success-600 text-5xl" />
                                </div>
                                <h2 className="text-3xl fw-bold text-gray-900 mb-8">
                                    Order Placed Successfully!
                                </h2>
                                <p className="text-gray-500 text-lg">
                                    Thank you for your order. We'll contact you
                                    shortly to confirm delivery.
                                </p>
                            </div>

                            {/* Order Summary Card */}
                            <div className="border border-gray-100 rounded-16 overflow-hidden mb-32">
                                {/* Card Header */}
                                <div className="bg-main-600 px-32 py-20 flex-between gap-16 flex-wrap">
                                    <div>
                                        <span className="text-white text-sm opacity-75 d-block mb-4">
                                            Order Invoice
                                        </span>
                                        <span className="text-white fw-bold text-xl">
                                            {order.invoice_no}
                                        </span>
                                    </div>
                                    <div className="text-end">
                                        <span className="text-white text-sm opacity-75 d-block mb-4">
                                            Placed on
                                        </span>
                                        <span className="text-white fw-medium">
                                            {order.created_at}
                                        </span>
                                    </div>
                                </div>

                                {/* Card Body */}
                                <div className="p-32">
                                    <div className="row gy-24">
                                        {/* Delivery Info */}
                                        <div className="col-sm-6">
                                            <h6 className="text-sm text-gray-400 fw-medium mb-12 text-uppercase tracking-wide">
                                                Delivery To
                                            </h6>
                                            <p className="text-gray-900 fw-medium mb-4">
                                                {order.delivery_city}
                                            </p>
                                            <p className="text-gray-600 text-sm mb-0">
                                                {order.delivery_address}
                                            </p>
                                        </div>

                                        {/* Branch Info */}
                                        <div className="col-sm-6">
                                            <h6 className="text-sm text-gray-400 fw-medium mb-12 text-uppercase tracking-wide">
                                                Fulfilling Branch
                                            </h6>
                                            <p className="text-gray-900 fw-medium mb-4">
                                                <i className="ph ph-storefront me-8 text-main-600" />
                                                {order.branch_name ??
                                                    "Our nearest branch"}
                                            </p>
                                            {order.branch_city && (
                                                <p className="text-gray-500 text-sm mb-4">
                                                    <i className="ph ph-map-pin me-6" />
                                                    {order.branch_city}
                                                </p>
                                            )}
                                            {order.routing_method && (
                                                <span className="badge bg-main-50 text-main-700 text-xs px-10 py-4 rounded-pill">
                                                    {routingMethodLabel[
                                                        order.routing_method
                                                    ] ?? order.routing_method}
                                                </span>
                                            )}
                                        </div>

                                        {/* Payment */}
                                        <div className="col-sm-6">
                                            <h6 className="text-sm text-gray-400 fw-medium mb-12 text-uppercase tracking-wide">
                                                Payment Method
                                            </h6>
                                            <p className="text-gray-900 fw-medium mb-0 text-capitalize">
                                                <i className="ph ph-credit-card me-8 text-main-600" />
                                                {order.payment_method?.replace(
                                                    "_",
                                                    " ",
                                                )}
                                            </p>
                                        </div>

                                        {/* Status */}
                                        <div className="col-sm-6">
                                            <h6 className="text-sm text-gray-400 fw-medium mb-12 text-uppercase tracking-wide">
                                                Order Status
                                            </h6>
                                            <span
                                                className={`badge px-16 py-8 rounded-pill text-sm fw-medium ${
                                                    order.status === "completed"
                                                        ? "bg-success-100 text-success-700"
                                                        : order.status ===
                                                            "cancelled"
                                                          ? "bg-danger-100 text-danger-700"
                                                          : "bg-warning-100 text-warning-700"
                                                }`}
                                            >
                                                {order.status
                                                    ?.charAt(0)
                                                    .toUpperCase() +
                                                    order.status?.slice(1)}
                                            </span>
                                        </div>

                                        {/* Notes */}
                                        {order.notes && (
                                            <div className="col-12">
                                                <h6 className="text-sm text-gray-400 fw-medium mb-8 text-uppercase tracking-wide">
                                                    Order Notes
                                                </h6>
                                                <p className="text-gray-600 text-sm bg-gray-50 rounded-8 p-16 mb-0">
                                                    {order.notes}
                                                </p>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>

                            {/* Order Items */}
                            <div className="border border-gray-100 rounded-16 overflow-hidden mb-32">
                                <div className="px-32 py-20 border-bottom border-gray-100">
                                    <h5 className="fw-semibold mb-0">
                                        Order Items
                                    </h5>
                                </div>
                                <div className="p-32">
                                    {(order.items ?? []).map((item, idx) => (
                                        <div
                                            key={idx}
                                            className={`flex-between gap-16 ${
                                                idx < order.items.length - 1
                                                    ? "pb-20 mb-20 border-bottom border-gray-100"
                                                    : ""
                                            }`}
                                        >
                                            <div>
                                                <p className="text-gray-900 fw-medium mb-4">
                                                    {item.product_name}
                                                </p>
                                                {item.variant_name && (
                                                    <span className="text-gray-400 text-xs">
                                                        {item.variant_name}
                                                    </span>
                                                )}
                                            </div>
                                            <div className="text-end flex-shrink-0">
                                                <p className="text-gray-500 text-sm mb-4">
                                                    {currency}{" "}
                                                    {Number(
                                                        item.unit_price,
                                                    ).toLocaleString()}{" "}
                                                    × {item.quantity}
                                                </p>
                                                <p className="text-gray-900 fw-bold mb-0">
                                                    {currency}{" "}
                                                    {Number(
                                                        item.subtotal,
                                                    ).toLocaleString()}
                                                </p>
                                            </div>
                                        </div>
                                    ))}

                                    {/* Total */}
                                    <div className="border-top border-gray-100 pt-20 mt-8 flex-between gap-8">
                                        <span className="text-gray-900 text-xl fw-bold">
                                            Total
                                        </span>
                                        <span className="text-main-600 text-xl fw-bold">
                                            {currency}{" "}
                                            {Number(
                                                order.total_amount,
                                            ).toLocaleString()}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {/* Action Buttons */}
                            <div className="flex-center gap-16 flex-wrap">
                                <Link
                                    href="/shop"
                                    className="btn btn-main py-14 px-40 rounded-8"
                                >
                                    <i className="ph ph-storefront me-8" />
                                    Continue Shopping
                                </Link>
                                <Link
                                    href="/"
                                    className="btn btn-outline-main py-14 px-40 rounded-8"
                                >
                                    <i className="ph ph-house me-8" />
                                    Back to Home
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <FooterTwo />
            <BottomFooter />
        </>
    );
};

export default OrderConfirmationPage;
