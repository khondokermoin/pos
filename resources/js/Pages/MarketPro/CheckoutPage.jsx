import React, { useState, useEffect, useContext } from "react";
import { usePage, router } from "@inertiajs/react";
import Preloader from "../../Helpers/Preloader";
import ColorInit from "../../Helpers/ColorInit";
import HeaderTwo from "../../Components/MarketPro/HeaderTwo";
import Breadcrumb from "../../Components/MarketPro/Breadcrumb";
import FooterTwo from "../../Components/MarketPro/FooterTwo";
import BottomFooter from "../../Components/MarketPro/BottomFooter";
import ShippingOne from "../../Components/MarketPro/ShippingOne";
import ScrollToTop from "react-scroll-to-top";

/**
 * CheckoutPage — Inertia-powered checkout with Location-Based Branch Routing.
 *
 * DATA SOURCE: ShopController::checkout() via Inertia::render('MarketPro/CheckoutPage', [...])
 *
 * PROPS (from usePage().props):
 *   - branches : Branch[] — active branches with coverage_areas (for delivery info display)
 *   - tenant   : Company branding (shared props)
 *
 * BRANCH ROUTING FLOW:
 *   1. Customer fills in City + District + Area fields
 *   2. Browser Geolocation API optionally captures lat/lng
 *   3. On "Place Order", POST /order is called with all location data
 *   4. BranchRoutingService on the server assigns the nearest/best branch
 *   5. On success, redirect to /order/confirmation/{invoiceNo}
 *
 * CART STATE:
 *   Cart items are stored in localStorage (client-side).
 *   This page reads from localStorage to display the order summary.
 */
const CheckoutPage = () => {
    const { branches, tenant } = usePage().props;
    const currency = tenant?.currency ?? "BDT";

    // ── Cart State (from localStorage) ────────────────────────────────────────
    const [cartItems, setCartItems] = useState([]);

    useEffect(() => {
        try {
            const stored = localStorage.getItem("cart");
            if (stored) {
                setCartItems(JSON.parse(stored));
            }
        } catch {
            setCartItems([]);
        }
    }, []);

    const subtotal = cartItems.reduce(
        (sum, item) => sum + item.unit_price * item.quantity,
        0,
    );

    // ── Form State ─────────────────────────────────────────────────────────────
    const [form, setForm] = useState({
        firstName: "",
        lastName: "",
        phone: "",
        email: "",
        city: "",
        district: "",
        area: "",
        address: "",
        notes: "",
        paymentMethod: "cash",
    });

    const [errors, setErrors] = useState({});
    const [submitting, setSubmitting] = useState(false);
    const [geoStatus, setGeoStatus] = useState("idle"); // idle | requesting | granted | denied
    const [coords, setCoords] = useState({ lat: null, lng: null });
    const [selectedPayment, setSelectedPayment] = useState("cash");

    // ── Geolocation ────────────────────────────────────────────────────────────
    const requestGeolocation = () => {
        if (!navigator.geolocation) {
            setGeoStatus("denied");
            return;
        }
        setGeoStatus("requesting");
        navigator.geolocation.getCurrentPosition(
            (position) => {
                setCoords({
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                });
                setGeoStatus("granted");
            },
            () => {
                setGeoStatus("denied");
            },
            { timeout: 10000 },
        );
    };

    // ── Coverage Area Display ──────────────────────────────────────────────────
    // Show which areas the company delivers to (informational)
    const allCoverageAreas = branches
        .flatMap((b) => b.coverage_areas ?? [b.city].filter(Boolean))
        .filter((v, i, a) => v && a.indexOf(v) === i)
        .sort();

    // ── Form Handlers ──────────────────────────────────────────────────────────
    const handleChange = (e) => {
        const { name, value } = e.target;
        setForm((prev) => ({ ...prev, [name]: value }));
        if (errors[name]) {
            setErrors((prev) => ({ ...prev, [name]: null }));
        }
    };

    const validate = () => {
        const newErrors = {};
        if (!form.firstName.trim())
            newErrors.firstName = "First name is required.";
        if (!form.phone.trim()) newErrors.phone = "Phone number is required.";
        if (!form.city.trim())
            newErrors.city = "City is required for delivery routing.";
        if (!form.address.trim())
            newErrors.address = "Delivery address is required.";
        if (cartItems.length === 0) newErrors.items = "Your cart is empty.";
        return newErrors;
    };

    // Normalised submit handler — works whether called from a form onSubmit event
    // (which passes a SyntheticEvent) OR from a plain button onClick (no event).
    const handleSubmit = async (e) => {
        if (e && typeof e.preventDefault === "function") e.preventDefault();

        const validationErrors = validate();
        if (Object.keys(validationErrors).length > 0) {
            setErrors(validationErrors);
            return;
        }

        setSubmitting(true);
        setErrors({});

        const payload = {
            customer: {
                name: `${form.firstName} ${form.lastName}`.trim(),
                phone: form.phone,
                email: form.email || null,
            },
            delivery: {
                city: form.city,
                district: form.district || null,
                area: form.area || null,
                address: form.address,
                lat: coords.lat,
                lng: coords.lng,
            },
            payment_method: selectedPayment,
            notes: form.notes || null,
            items: cartItems.map((item) => ({
                variant_id: item.variant_id,
                quantity: item.quantity,
                unit_price: item.unit_price,
            })),
        };

        try {
            const response = await fetch("/order", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute("content"),
                    Accept: "application/json",
                },
                body: JSON.stringify(payload),
            });

            const data = await response.json();

            if (response.ok && data.success) {
                // Clear cart
                localStorage.removeItem("cart");
                // Redirect to confirmation page via Inertia
                router.visit(`/order/confirmation/${data.order.invoice_no}`);
            } else if (response.status === 422 && data.errors) {
                // Laravel validation errors
                const fieldErrors = {};
                Object.entries(data.errors).forEach(([key, messages]) => {
                    const field = key
                        .replace("customer.", "")
                        .replace("delivery.", "");
                    fieldErrors[field] = messages[0];
                });
                setErrors(fieldErrors);
            } else {
                setErrors({
                    general:
                        data.error ??
                        "Failed to place order. Please try again.",
                });
            }
        } catch {
            setErrors({
                general:
                    "Network error. Please check your connection and try again.",
            });
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <>
            <ColorInit color={true} />
            <ScrollToTop smooth color="#FA6400" />
            <Preloader />
            <HeaderTwo category={true} />
            <Breadcrumb title={"Checkout"} />

            <section className="checkout py-80">
                <div className="container container-lg">
                    {/* Coupon bar */}
                    <div className="border border-gray-100 rounded-8 px-30 py-20 mb-40">
                        <span>
                            Have a coupon?{" "}
                            <a
                                href="/cart"
                                className="fw-semibold text-gray-900 hover-text-decoration-underline hover-text-main-600"
                            >
                                Click here to enter your code
                            </a>
                        </span>
                    </div>

                    {/* General error */}
                    {errors.general && (
                        <div className="alert alert-danger rounded-8 mb-24 px-20 py-16">
                            <i className="ph ph-warning me-8" />
                            {errors.general}
                        </div>
                    )}

                    {/* Delivery coverage info */}
                    {allCoverageAreas.length > 0 && (
                        <div className="border border-main-100 bg-main-50 rounded-8 px-24 py-16 mb-32">
                            <p className="text-sm text-main-700 mb-0">
                                <i className="ph ph-map-pin me-8" />
                                <strong>We deliver to:</strong>{" "}
                                {allCoverageAreas.join(", ")} — Orders from
                                other areas will be routed to our nearest
                                branch.
                            </p>
                        </div>
                    )}

                    <div className="row">
                        {/* ── Left: Billing & Delivery Form ─────────────────── */}
                        <div className="col-xl-9 col-lg-8">
                            <form onSubmit={handleSubmit} className="pe-xl-5">
                                <h5 className="mb-24 text-xl fw-semibold">
                                    Billing & Delivery Details
                                </h5>
                                <div className="row gy-3">
                                    {/* Name */}
                                    <div className="col-sm-6">
                                        <input
                                            type="text"
                                            name="firstName"
                                            value={form.firstName}
                                            onChange={handleChange}
                                            className={`common-input border-gray-100 ${errors.firstName ? "border-danger" : ""}`}
                                            placeholder="First Name *"
                                        />
                                        {errors.firstName && (
                                            <span className="text-danger text-xs mt-4 d-block">
                                                {errors.firstName}
                                            </span>
                                        )}
                                    </div>
                                    <div className="col-sm-6">
                                        <input
                                            type="text"
                                            name="lastName"
                                            value={form.lastName}
                                            onChange={handleChange}
                                            className="common-input border-gray-100"
                                            placeholder="Last Name"
                                        />
                                    </div>

                                    {/* Phone */}
                                    <div className="col-sm-6">
                                        <input
                                            type="tel"
                                            name="phone"
                                            value={form.phone}
                                            onChange={handleChange}
                                            className={`common-input border-gray-100 ${errors.phone ? "border-danger" : ""}`}
                                            placeholder="Phone Number *"
                                        />
                                        {errors.phone && (
                                            <span className="text-danger text-xs mt-4 d-block">
                                                {errors.phone}
                                            </span>
                                        )}
                                    </div>

                                    {/* Email */}
                                    <div className="col-sm-6">
                                        <input
                                            type="email"
                                            name="email"
                                            value={form.email}
                                            onChange={handleChange}
                                            className="common-input border-gray-100"
                                            placeholder="Email Address (Optional)"
                                        />
                                    </div>

                                    {/* ── Location Section ──────────────────── */}
                                    <div className="col-12 mt-8">
                                        <h6 className="text-lg fw-semibold mb-16 d-flex align-items-center gap-8">
                                            <i className="ph ph-map-pin text-main-600" />
                                            Delivery Location
                                            <span className="text-xs text-gray-400 fw-normal">
                                                (Used to assign your nearest
                                                branch)
                                            </span>
                                        </h6>
                                    </div>

                                    {/* City */}
                                    <div className="col-sm-4">
                                        <input
                                            type="text"
                                            name="city"
                                            value={form.city}
                                            onChange={handleChange}
                                            className={`common-input border-gray-100 ${errors.city ? "border-danger" : ""}`}
                                            placeholder="City / Town *"
                                        />
                                        {errors.city && (
                                            <span className="text-danger text-xs mt-4 d-block">
                                                {errors.city}
                                            </span>
                                        )}
                                    </div>

                                    {/* District */}
                                    <div className="col-sm-4">
                                        <input
                                            type="text"
                                            name="district"
                                            value={form.district}
                                            onChange={handleChange}
                                            className="common-input border-gray-100"
                                            placeholder="District (Optional)"
                                        />
                                    </div>

                                    {/* Area */}
                                    <div className="col-sm-4">
                                        <input
                                            type="text"
                                            name="area"
                                            value={form.area}
                                            onChange={handleChange}
                                            className="common-input border-gray-100"
                                            placeholder="Area / Thana (Optional)"
                                        />
                                    </div>

                                    {/* GPS Location Button */}
                                    <div className="col-12">
                                        <div className="d-flex align-items-center gap-12 flex-wrap">
                                            <button
                                                type="button"
                                                onClick={requestGeolocation}
                                                disabled={
                                                    geoStatus ===
                                                        "requesting" ||
                                                    geoStatus === "granted"
                                                }
                                                className={`btn py-10 px-20 rounded-8 text-sm d-flex align-items-center gap-8 ${
                                                    geoStatus === "granted"
                                                        ? "btn-success"
                                                        : geoStatus === "denied"
                                                          ? "btn-outline-danger"
                                                          : "btn-outline-main"
                                                }`}
                                            >
                                                <i
                                                    className={`ph ${
                                                        geoStatus === "granted"
                                                            ? "ph-check-circle"
                                                            : geoStatus ===
                                                                "requesting"
                                                              ? "ph-spinner"
                                                              : "ph-crosshair"
                                                    }`}
                                                />
                                                {geoStatus === "granted"
                                                    ? "Location Captured ✓"
                                                    : geoStatus === "requesting"
                                                      ? "Detecting..."
                                                      : geoStatus === "denied"
                                                        ? "Location Denied"
                                                        : "Use My GPS Location"}
                                            </button>
                                            <span className="text-xs text-gray-400">
                                                {geoStatus === "granted"
                                                    ? `GPS: ${coords.lat?.toFixed(4)}, ${coords.lng?.toFixed(4)} — improves branch matching`
                                                    : "Optional: helps us find your nearest branch more accurately"}
                                            </span>
                                        </div>
                                    </div>

                                    {/* Full Address */}
                                    <div className="col-12">
                                        <textarea
                                            name="address"
                                            value={form.address}
                                            onChange={handleChange}
                                            rows={3}
                                            className={`common-input border-gray-100 ${errors.address ? "border-danger" : ""}`}
                                            placeholder="Full Delivery Address (House/Road/Block) *"
                                        />
                                        {errors.address && (
                                            <span className="text-danger text-xs mt-4 d-block">
                                                {errors.address}
                                            </span>
                                        )}
                                    </div>

                                    {/* Notes */}
                                    <div className="col-12">
                                        <div className="my-16">
                                            <h6 className="text-lg mb-16">
                                                Additional Information
                                            </h6>
                                            <textarea
                                                name="notes"
                                                value={form.notes}
                                                onChange={handleChange}
                                                rows={3}
                                                className="common-input border-gray-100"
                                                placeholder="Notes about your order, e.g. special delivery instructions."
                                            />
                                        </div>
                                    </div>
                                </div>

                                {/* Submit button (mobile) */}
                                <div className="d-lg-none mt-24">
                                    <button
                                        type="submit"
                                        disabled={submitting}
                                        className="btn btn-main w-100 py-18 rounded-8"
                                    >
                                        {submitting ? (
                                            <>
                                                <i className="ph ph-spinner me-8" />
                                                Placing Order...
                                            </>
                                        ) : (
                                            "Place Order"
                                        )}
                                    </button>
                                </div>
                            </form>
                        </div>

                        {/* ── Right: Order Summary & Payment ────────────────── */}
                        <div className="col-xl-3 col-lg-4">
                            <div className="checkout-sidebar">
                                <div className="bg-color-three rounded-8 p-24 text-center">
                                    <span className="text-gray-900 text-xl fw-semibold">
                                        Your Orders
                                    </span>
                                </div>

                                <div className="border border-gray-100 rounded-8 px-24 py-32 mt-24">
                                    {/* Header */}
                                    <div className="mb-24 pb-24 border-bottom border-gray-100 flex-between gap-8">
                                        <span className="text-gray-900 fw-medium text-xl">
                                            Product
                                        </span>
                                        <span className="text-gray-900 fw-medium text-xl">
                                            Subtotal
                                        </span>
                                    </div>

                                    {/* Cart items */}
                                    {cartItems.length === 0 ? (
                                        <p className="text-gray-400 text-sm text-center py-16">
                                            Your cart is empty.{" "}
                                            <a
                                                href="/shop"
                                                className="text-main-600"
                                            >
                                                Shop now
                                            </a>
                                        </p>
                                    ) : (
                                        cartItems.map((item, idx) => (
                                            <div
                                                key={idx}
                                                className="flex-between gap-16 mb-20"
                                            >
                                                <div className="flex-align gap-8">
                                                    <span className="text-gray-900 fw-normal text-sm w-120 text-line-2">
                                                        {item.name}
                                                    </span>
                                                    <span className="text-gray-500 text-sm">
                                                        × {item.quantity}
                                                    </span>
                                                </div>
                                                <span className="text-gray-900 fw-semibold text-sm flex-shrink-0">
                                                    {currency}{" "}
                                                    {(
                                                        item.unit_price *
                                                        item.quantity
                                                    ).toLocaleString()}
                                                </span>
                                            </div>
                                        ))
                                    )}

                                    {/* Totals */}
                                    <div className="border-top border-gray-100 pt-24 mt-8">
                                        <div className="mb-16 flex-between gap-8">
                                            <span className="text-gray-900 text-lg fw-semibold">
                                                Subtotal
                                            </span>
                                            <span className="text-gray-900 fw-bold">
                                                {currency}{" "}
                                                {subtotal.toLocaleString()}
                                            </span>
                                        </div>
                                        <div className="flex-between gap-8">
                                            <span className="text-gray-900 text-lg fw-semibold">
                                                Total
                                            </span>
                                            <span className="text-gray-900 fw-bold text-main-600">
                                                {currency}{" "}
                                                {subtotal.toLocaleString()}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {/* Payment Methods */}
                                <div className="mt-24">
                                    {[
                                        {
                                            id: "cash",
                                            label: "Cash on Delivery",
                                            desc: "Pay when your order arrives at your door.",
                                        },
                                        {
                                            id: "mobile_banking",
                                            label: "Mobile Banking (bKash/Nagad)",
                                            desc: "Pay via bKash, Nagad, or Rocket. Our team will contact you with payment details.",
                                        },
                                        {
                                            id: "card",
                                            label: "Card Payment",
                                            desc: "Pay securely with your debit or credit card.",
                                        },
                                    ].map((method) => (
                                        <div
                                            key={method.id}
                                            className="payment-item"
                                        >
                                            <div className="form-check common-check common-radio py-16 mb-0">
                                                <input
                                                    className="form-check-input"
                                                    type="radio"
                                                    name="payment"
                                                    id={method.id}
                                                    checked={
                                                        selectedPayment ===
                                                        method.id
                                                    }
                                                    onChange={() =>
                                                        setSelectedPayment(
                                                            method.id,
                                                        )
                                                    }
                                                />
                                                <label
                                                    className="form-check-label fw-semibold text-neutral-600"
                                                    htmlFor={method.id}
                                                >
                                                    {method.label}
                                                </label>
                                            </div>
                                            {selectedPayment === method.id && (
                                                <div className="payment-item__content px-16 py-16 rounded-8 bg-main-50 d-block">
                                                    <p className="text-gray-700 text-sm mb-0">
                                                        {method.desc}
                                                    </p>
                                                </div>
                                            )}
                                        </div>
                                    ))}
                                </div>

                                {/* Privacy note */}
                                <div className="mt-24 pt-24 border-top border-gray-100">
                                    <p className="text-gray-500 text-sm">
                                        Your personal data will be used to
                                        process your order and support your
                                        experience throughout this website.
                                    </p>
                                </div>

                                {/* Place Order button */}
                                <button
                                    type="button"
                                    onClick={handleSubmit}
                                    disabled={submitting}
                                    className="btn btn-main mt-32 py-18 w-100 rounded-8 d-flex align-items-center justify-content-center gap-8"
                                >
                                    {submitting ? (
                                        <>
                                            <i className="ph ph-spinner" />
                                            Placing Order...
                                        </>
                                    ) : (
                                        <>
                                            <i className="ph ph-check-circle" />
                                            Place Order
                                        </>
                                    )}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <ShippingOne />
            <FooterTwo />
            <BottomFooter />
        </>
    );
};

export default CheckoutPage;
