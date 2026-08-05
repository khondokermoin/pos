import React, { useEffect, useState } from "react";
import query from "jquery";
import initSelect2 from "select2";
import "select2/dist/css/select2.min.css";
import { Link } from "@inertiajs/react";

// select2's UMD build exports a factory — `import "select2"` alone never calls it,
// so `$.fn.select2` is never attached under Vite. It must be invoked explicitly
// against the same jQuery instance used below (window.jQuery so the plugin's
// internal `require('jquery')` fallback resolves to this instance too).
if (typeof window !== "undefined") {
    window.jQuery = window.jQuery || query;
    window.$ = window.$ || query;
    if (typeof query.fn.select2 !== "function") {
        initSelect2(window, query);
    }
}

/**
 * HeaderOne — Homepage header component.
 *
 * FIXED (Phase 1): Replaced react-router-dom Link/NavLink with @inertiajs/react Link.
 * The BrowserRouter SPA pattern has been eliminated — there is no Router context
 * available, so react-router-dom Link would throw a runtime error.
 *
 * Props:
 *   - tenant     : Company branding data (from usePage().props.tenant)
 *   - categories : Category[] (from usePage().props.categories)
 */

// All 64 districts of Bangladesh, grouped by division, for the "Your Location" selector.
const BD_DISTRICTS_BY_DIVISION = {
    Dhaka: [
        "Dhaka",
        "Faridpur",
        "Gazipur",
        "Gopalganj",
        "Kishoreganj",
        "Madaripur",
        "Manikganj",
        "Munshiganj",
        "Narayanganj",
        "Narsingdi",
        "Rajbari",
        "Shariatpur",
        "Tangail",
    ],
    Chattogram: [
        "Bandarban",
        "Brahmanbaria",
        "Chandpur",
        "Chattogram",
        "Cumilla",
        "Cox's Bazar",
        "Feni",
        "Khagrachhari",
        "Lakshmipur",
        "Noakhali",
        "Rangamati",
    ],
    Rajshahi: [
        "Bogura",
        "Chapainawabganj",
        "Joypurhat",
        "Naogaon",
        "Natore",
        "Pabna",
        "Rajshahi",
        "Sirajganj",
    ],
    Khulna: [
        "Bagerhat",
        "Chuadanga",
        "Jashore",
        "Jhenaidah",
        "Khulna",
        "Kushtia",
        "Magura",
        "Meherpur",
        "Narail",
        "Satkhira",
    ],
    Barishal: [
        "Barguna",
        "Barishal",
        "Bhola",
        "Jhalokati",
        "Patuakhali",
        "Pirojpur",
    ],
    Sylhet: ["Habiganj", "Moulvibazar", "Sunamganj", "Sylhet"],
    Rangpur: [
        "Dinajpur",
        "Gaibandha",
        "Kurigram",
        "Lalmonirhat",
        "Nilphamari",
        "Panchagarh",
        "Rangpur",
        "Thakurgaon",
    ],
    Mymensingh: ["Jamalpur", "Mymensingh", "Netrokona", "Sherpur"],
};

const HeaderOne = ({ tenant, categories }) => {
    const [scroll, setScroll] = useState(false);
    useEffect(() => {
        // Fix: define the handler as a named function so it can be properly
        // removed on unmount. The previous pattern assigned window.onscroll
        // inside the effect but put the cleanup return *inside* the callback
        // (dead code), so the listener was never removed when the component
        // unmounted. Using addEventListener + removeEventListener is also
        // safer than window.onscroll because multiple components can coexist.
        const handleScroll = () => {
            if (window.pageYOffset < 150) {
                setScroll(false);
            } else if (window.pageYOffset > 150) {
                setScroll(true);
            }
        };
        window.addEventListener("scroll", handleScroll);

        const selectElement = query(".js-example-basic-single");

        if (typeof selectElement.select2 === "function") {
            selectElement.select2();
        }

        return () => {
            // Properly clean up both listeners on unmount.
            window.removeEventListener("scroll", handleScroll);
            if (
                typeof selectElement.select2 === "function" &&
                selectElement.data("select2")
            ) {
                selectElement.select2("destroy");
            }
        };
    }, []);

    // Set the default language
    const [selectedLanguage, setSelectedLanguage] = useState("Eng");
    const handleLanguageChange = (language) => {
        setSelectedLanguage(language);
    };

    // Set the default currency
    const [selectedCurrency, setSelectedCurrency] = useState(
        tenant?.currency ?? "BDT",
    );
    const handleCurrencyChange = (currency) => {
        setSelectedCurrency(currency);
    };

    // Mobile menu support
    const [menuActive, setMenuActive] = useState(false);
    const [activeIndex, setActiveIndex] = useState(null);
    const handleMenuClick = (index) => {
        setActiveIndex(activeIndex === index ? null : index);
    };
    const handleMenuToggle = () => {
        setMenuActive(!menuActive);
    };

    // Search control support
    const [activeSearch, setActiveSearch] = useState(false);
    const handleSearchToggle = () => {
        setActiveSearch(!activeSearch);
    };

    // category control support
    const [activeCategory, setActiveCategory] = useState(false);
    const handleCategoryToggle = () => {
        setActiveCategory(!activeCategory);
    };
    const [activeIndexCat, setActiveIndexCat] = useState(null);
    const handleCatClick = (index) => {
        setActiveIndexCat(activeIndexCat === index ? null : index);
    };

    // Live cart item count (cart is stored in localStorage by ShopPage/ProductDetailsOne/CheckoutPage)
    //
    // Fix: the browser's native 'storage' event only fires in OTHER tabs/windows,
    // never in the tab that called localStorage.setItem(). To update the badge in
    // the same tab we also listen for a custom 'cart:updated' event that every
    // add-to-cart handler should dispatch after writing to localStorage:
    //
    //   localStorage.setItem('cart', JSON.stringify(newCart));
    //   window.dispatchEvent(new Event('cart:updated'));
    //
    // This way the badge updates immediately in the current tab AND stays in sync
    // across other tabs via the native 'storage' event.
    const [cartCount, setCartCount] = useState(0);
    useEffect(() => {
        const readCartCount = () => {
            try {
                const cart = JSON.parse(localStorage.getItem("cart") ?? "[]");
                setCartCount(
                    cart.reduce((sum, item) => sum + (item.quantity ?? 0), 0),
                );
            } catch {
                setCartCount(0);
            }
        };
        readCartCount();
        // 'storage' fires in other tabs; 'cart:updated' fires in the same tab.
        window.addEventListener("storage", readCartCount);
        window.addEventListener("cart:updated", readCartCount);
        return () => {
            window.removeEventListener("storage", readCartCount);
            window.removeEventListener("cart:updated", readCartCount);
        };
    }, []);

    // Determine current path for active link styling (Inertia-compatible)
    const currentPath =
        typeof window !== "undefined" ? window.location.pathname : "";
    const isActive = (path) => currentPath === path;
    const navLinkClass = (path) =>
        `common-dropdown__link nav-submenu__link hover-bg-neutral-100${isActive(path) ? " activePage" : ""}`;

    return (
        <>
            <div className="overlay" />
            <div
                className={`side-overlay ${(menuActive || activeCategory) && "show"}`}
            />
            {/* ==================== Search Box Start Here ==================== */}
            <form
                action="#"
                className={`search-box ${activeSearch && "active"}`}
            >
                <button
                    onClick={handleSearchToggle}
                    type="button"
                    className="search-box__close position-absolute inset-block-start-0 inset-inline-end-0 m-16 w-48 h-48 border border-gray-100 rounded-circle flex-center text-white hover-text-gray-800 hover-bg-white text-2xl transition-1"
                >
                    <i className="ph ph-x" />
                </button>
                <div className="container">
                    <div className="position-relative">
                        <input
                            type="text"
                            className="form-control py-16 px-24 text-xl rounded-pill pe-64"
                            placeholder="Search for a product or brand"
                        />
                        <button
                            type="submit"
                            className="w-48 h-48 bg-main-600 rounded-circle flex-center text-xl text-white position-absolute top-50 translate-middle-y inset-inline-end-0 me-8"
                        >
                            <i className="ph ph-magnifying-glass" />
                        </button>
                    </div>
                </div>
            </form>
            {/* ==================== Search Box End Here ==================== */}
            {/* ==================== Mobile Menu Start Here ==================== */}
            <div
                className={`mobile-menu scroll-sm d-lg-none d-block ${menuActive && "active"}`}
            >
                <button
                    onClick={() => {
                        handleMenuToggle();
                        setActiveIndex(null);
                    }}
                    type="button"
                    className="close-button"
                >
                    <i className="ph ph-x" />{" "}
                </button>
                <div className="mobile-menu__inner">
                    <Link href="/" className="mobile-menu__logo">
                        <img
                            src={
                                tenant?.logo_url ??
                                "/assets/images/logo/logo.png"
                            }
                            alt={tenant?.name ?? "Logo"}
                        />
                    </Link>
                    <div className="mobile-menu__menu">
                        {/* Nav Menu Start */}
                        <ul className="nav-menu flex-align nav-menu--mobile">
                            {/* Home Menu */}
                            <li
                                onClick={() => handleMenuClick(0)}
                                className={`on-hover-item nav-menu__item has-submenu ${activeIndex === 0 ? "d-block" : ""}`}
                            >
                                <Link href="#" className="nav-menu__link">
                                    Home
                                </Link>
                                <ul
                                    className={`on-hover-dropdown common-dropdown nav-submenu scroll-sm ${activeIndex === 0 ? "open" : ""}`}
                                >
                                    <li className="common-dropdown__item nav-submenu__item">
                                        <Link
                                            href="/"
                                            className={navLinkClass("/")}
                                            onClick={() => setActiveIndex(null)}
                                        >
                                            Home Grocery
                                        </Link>
                                    </li>
                                    <li className="common-dropdown__item nav-submenu__item">
                                        <Link
                                            href="/index-two"
                                            className={navLinkClass(
                                                "/index-two",
                                            )}
                                            onClick={() => setActiveIndex(null)}
                                        >
                                            Home Electronics
                                        </Link>
                                    </li>
                                    <li className="common-dropdown__item nav-submenu__item">
                                        <Link
                                            href="/index-three"
                                            className={navLinkClass(
                                                "/index-three",
                                            )}
                                            onClick={() => setActiveIndex(null)}
                                        >
                                            Home Fashion
                                        </Link>
                                    </li>
                                </ul>
                            </li>

                            {/* Shop Menu */}
                            <li
                                onClick={() => handleMenuClick(1)}
                                className={`on-hover-item nav-menu__item has-submenu ${activeIndex === 1 ? "d-block" : ""}`}
                            >
                                <Link href="#" className="nav-menu__link">
                                    Shop
                                </Link>
                                <ul
                                    className={`on-hover-dropdown common-dropdown nav-submenu scroll-sm ${activeIndex === 1 ? "open" : ""}`}
                                >
                                    <li className="common-dropdown__item nav-submenu__item">
                                        <Link
                                            href="/shop"
                                            className={navLinkClass("/shop")}
                                            onClick={() => setActiveIndex(null)}
                                        >
                                            Shop
                                        </Link>
                                    </li>
                                    <li className="common-dropdown__item nav-submenu__item">
                                        <Link
                                            href="/product-details"
                                            className={navLinkClass(
                                                "/product-details",
                                            )}
                                            onClick={() => setActiveIndex(null)}
                                        >
                                            Shop Details
                                        </Link>
                                    </li>
                                    <li className="common-dropdown__item nav-submenu__item">
                                        <Link
                                            href="/product-details-two"
                                            className={navLinkClass(
                                                "/product-details-two",
                                            )}
                                            onClick={() => setActiveIndex(null)}
                                        >
                                            Shop Details Two
                                        </Link>
                                    </li>
                                </ul>
                            </li>

                            {/* Pages Menu */}
                            <li
                                onClick={() => handleMenuClick(2)}
                                className={`on-hover-item nav-menu__item has-submenu ${activeIndex === 2 ? "d-block" : ""}`}
                            >
                                <span className="badge-notification bg-warning-600 text-white text-sm py-2 px-8 rounded-4">
                                    New
                                </span>
                                <Link href="#" className="nav-menu__link">
                                    Pages
                                </Link>
                                <ul
                                    className={`on-hover-dropdown common-dropdown nav-submenu scroll-sm ${activeIndex === 2 ? "open" : ""}`}
                                >
                                    <li className="common-dropdown__item nav-submenu__item">
                                        <Link
                                            href="/cart"
                                            className={navLinkClass("/cart")}
                                            onClick={() => setActiveIndex(null)}
                                        >
                                            Cart
                                        </Link>
                                    </li>
                                    <li className="common-dropdown__item nav-submenu__item">
                                        <Link
                                            href="/wishlist"
                                            className={navLinkClass(
                                                "/wishlist",
                                            )}
                                            onClick={() => setActiveIndex(null)}
                                        >
                                            Wishlist
                                        </Link>
                                    </li>
                                    <li className="common-dropdown__item nav-submenu__item">
                                        <Link
                                            href="/checkout"
                                            className={navLinkClass(
                                                "/checkout",
                                            )}
                                            onClick={() => setActiveIndex(null)}
                                        >
                                            Checkout
                                        </Link>
                                    </li>
                                    <li className="common-dropdown__item nav-submenu__item">
                                        <Link
                                            href="/become-seller"
                                            className={navLinkClass(
                                                "/become-seller",
                                            )}
                                            onClick={() => setActiveIndex(null)}
                                        >
                                            Become Seller
                                        </Link>
                                    </li>
                                    <li className="common-dropdown__item nav-submenu__item">
                                        <Link
                                            href="/account"
                                            className={navLinkClass("/account")}
                                            onClick={() => setActiveIndex(null)}
                                        >
                                            Account
                                        </Link>
                                    </li>
                                </ul>
                            </li>

                            {/* Vendors Menu */}
                            <li
                                onClick={() => handleMenuClick(3)}
                                className={`on-hover-item nav-menu__item has-submenu ${activeIndex === 3 ? "d-block" : ""}`}
                            >
                                <span className="badge-notification bg-tertiary-600 text-white text-sm py-2 px-8 rounded-4">
                                    New
                                </span>
                                <Link href="#" className="nav-menu__link">
                                    Vendors
                                </Link>
                                <ul
                                    className={`on-hover-dropdown common-dropdown nav-submenu scroll-sm ${activeIndex === 3 ? "open" : ""}`}
                                >
                                    <li className="common-dropdown__item nav-submenu__item">
                                        <Link
                                            href="/vendor"
                                            className={navLinkClass("/vendor")}
                                            onClick={() => setActiveIndex(null)}
                                        >
                                            Vendors
                                        </Link>
                                    </li>
                                    <li className="common-dropdown__item nav-submenu__item">
                                        <Link
                                            href="/vendor-details"
                                            className={navLinkClass(
                                                "/vendor-details",
                                            )}
                                            onClick={() => setActiveIndex(null)}
                                        >
                                            Vendor Details
                                        </Link>
                                    </li>
                                    <li className="common-dropdown__item nav-submenu__item">
                                        <Link
                                            href="/vendor-two"
                                            className={navLinkClass(
                                                "/vendor-two",
                                            )}
                                            onClick={() => setActiveIndex(null)}
                                        >
                                            Vendors Two
                                        </Link>
                                    </li>
                                    <li className="common-dropdown__item nav-submenu__item">
                                        <Link
                                            href="/vendor-two-details"
                                            className={navLinkClass(
                                                "/vendor-two-details",
                                            )}
                                            onClick={() => setActiveIndex(null)}
                                        >
                                            Vendors Two Details
                                        </Link>
                                    </li>
                                </ul>
                            </li>

                            {/* Blog Menu */}
                            <li
                                onClick={() => handleMenuClick(4)}
                                className={`on-hover-item nav-menu__item has-submenu ${activeIndex === 4 ? "d-block" : ""}`}
                            >
                                <Link href="#" className="nav-menu__link">
                                    Blog
                                </Link>
                                <ul
                                    className={`on-hover-dropdown common-dropdown nav-submenu scroll-sm ${activeIndex === 4 ? "open" : ""}`}
                                >
                                    <li className="common-dropdown__item nav-submenu__item">
                                        <Link
                                            href="/blog"
                                            className={navLinkClass("/blog")}
                                            onClick={() => setActiveIndex(null)}
                                        >
                                            Blog
                                        </Link>
                                    </li>
                                    <li className="common-dropdown__item nav-submenu__item">
                                        <Link
                                            href="/blog-details"
                                            className={navLinkClass(
                                                "/blog-details",
                                            )}
                                            onClick={() => setActiveIndex(null)}
                                        >
                                            Blog Details
                                        </Link>
                                    </li>
                                </ul>
                            </li>

                            {/* Contact Us Menu */}
                            <li className="nav-menu__item">
                                <Link
                                    href="/contact"
                                    className={`nav-menu__link${isActive("/contact") ? " activePage" : ""}`}
                                    onClick={() => setActiveIndex(null)}
                                >
                                    Contact Us
                                </Link>
                            </li>
                        </ul>
                        {/* Nav Menu End */}
                    </div>
                </div>
            </div>
            {/* ==================== Mobile Menu End Here ==================== */}
            {/* ======================= Middle Top Start ========================= */}
            <div className="header-top bg-main-600 flex-between">
                <div className="container container-lg">
                    <div className="flex-between flex-wrap gap-8">
                        <ul className="flex-align flex-wrap d-none d-md-flex">
                            <li className="border-right-item">
                                <Link
                                    href="#"
                                    className="text-white text-sm hover-text-decoration-underline"
                                >
                                    Become A Seller
                                </Link>
                            </li>
                            <li className="border-right-item">
                                <Link
                                    href="#"
                                    className="text-white text-sm hover-text-decoration-underline"
                                >
                                    About us
                                </Link>
                            </li>
                            <li className="border-right-item">
                                <Link
                                    href="#"
                                    className="text-white text-sm hover-text-decoration-underline"
                                >
                                    Free Delivery
                                </Link>
                            </li>
                            <li className="border-right-item">
                                <Link
                                    href="#"
                                    className="text-white text-sm hover-text-decoration-underline"
                                >
                                    Returns Policy
                                </Link>
                            </li>
                        </ul>
                        <ul className="header-top__right flex-align flex-wrap">
                            <li className="on-hover-item border-right-item border-right-item-sm-space has-submenu arrow-white">
                                <Link
                                    href="#"
                                    className="text-white text-sm py-8"
                                >
                                    Help Center
                                </Link>
                                <ul className="on-hover-dropdown common-dropdown common-dropdown--sm max-h-200 scroll-sm px-0 py-8">
                                    <li className="nav-submenu__item">
                                        <Link
                                            href="#"
                                            className="nav-submenu__link hover-bg-gray-100 text-gray-500 text-xs py-6 px-16 flex-align gap-8 rounded-0"
                                        >
                                            <span className="text-sm d-flex">
                                                <i className="ph ph-headset" />
                                            </span>
                                            Call Center
                                        </Link>
                                    </li>
                                    <li className="nav-submenu__item">
                                        <Link
                                            href="#"
                                            className="nav-submenu__link hover-bg-gray-100 text-gray-500 text-xs py-6 px-16 flex-align gap-8 rounded-0"
                                        >
                                            <span className="text-sm d-flex">
                                                <i className="ph ph-chat-circle-dots" />
                                            </span>
                                            Live Chat
                                        </Link>
                                    </li>
                                </ul>
                            </li>
                            <li className="on-hover-item border-right-item border-right-item-sm-space has-submenu arrow-white">
                                <Link
                                    href="#"
                                    className="selected-text text-white text-sm py-8"
                                >
                                    {selectedLanguage}
                                </Link>
                                <ul className="selectable-text-list on-hover-dropdown common-dropdown common-dropdown--sm max-h-200 scroll-sm px-0 py-8">
                                    {[
                                        {
                                            label: "English",
                                            flag: "flag1.png",
                                            code: "English",
                                        },
                                        {
                                            label: "Japan",
                                            flag: "flag2.png",
                                            code: "Japan",
                                        },
                                        {
                                            label: "French",
                                            flag: "flag3.png",
                                            code: "French",
                                        },
                                        {
                                            label: "Germany",
                                            flag: "flag4.png",
                                            code: "Germany",
                                        },
                                        {
                                            label: "Bangladesh",
                                            flag: "flag6.png",
                                            code: "Bangladesh",
                                        },
                                        {
                                            label: "South Korea",
                                            flag: "flag5.png",
                                            code: "South Korea",
                                        },
                                    ].map((lang) => (
                                        <li key={lang.code}>
                                            <button
                                                type="button"
                                                className="hover-bg-gray-100 text-gray-500 text-xs py-6 px-16 flex-align gap-8 rounded-0 w-100 border-0 bg-transparent text-start"
                                                onClick={() =>
                                                    handleLanguageChange(
                                                        lang.code,
                                                    )
                                                }
                                            >
                                                <img
                                                    src={`/assets/images/thumbs/${lang.flag}`}
                                                    alt=""
                                                    className="w-16 h-12 rounded-4 border border-gray-100"
                                                />
                                                {lang.label}
                                            </button>
                                        </li>
                                    ))}
                                </ul>
                            </li>
                            <li className="on-hover-item border-right-item border-right-item-sm-space has-submenu arrow-white">
                                <Link
                                    href="#"
                                    className="selected-text text-white text-sm py-8"
                                >
                                    {selectedCurrency}
                                </Link>
                                <ul className="selectable-text-list on-hover-dropdown common-dropdown common-dropdown--sm max-h-200 scroll-sm px-0 py-8">
                                    {[
                                        {
                                            label: "USD",
                                            flag: "flag1.png",
                                            code: "USD",
                                        },
                                        {
                                            label: "Yen",
                                            flag: "flag2.png",
                                            code: "Yen",
                                        },
                                        {
                                            label: "Franc",
                                            flag: "flag3.png",
                                            code: "Franc",
                                        },
                                        {
                                            label: "EURO",
                                            flag: "flag4.png",
                                            code: "EURO",
                                        },
                                        {
                                            label: "BDT",
                                            flag: "flag6.png",
                                            code: "BDT",
                                        },
                                        {
                                            label: "WON",
                                            flag: "flag5.png",
                                            code: "WON",
                                        },
                                    ].map((cur) => (
                                        <li key={cur.code}>
                                            <button
                                                type="button"
                                                className="hover-bg-gray-100 text-gray-500 text-xs py-6 px-16 flex-align gap-8 rounded-0 w-100 border-0 bg-transparent text-start"
                                                onClick={() =>
                                                    handleCurrencyChange(
                                                        cur.code,
                                                    )
                                                }
                                            >
                                                <img
                                                    src={`/assets/images/thumbs/${cur.flag}`}
                                                    alt=""
                                                    className="w-16 h-12 rounded-4 border border-gray-100"
                                                />
                                                {cur.label}
                                            </button>
                                        </li>
                                    ))}
                                </ul>
                            </li>
                            <li className="border-right-item">
                                <Link
                                    href="/account"
                                    className="text-white text-sm py-8 flex-align gap-6"
                                >
                                    <span className="icon text-md d-flex">
                                        <i className="ph ph-user-circle" />
                                    </span>
                                    <span className="hover-text-decoration-underline">
                                        My Account
                                    </span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            {/* ======================= Middle Top End ========================= */}
            {/* ======================= Middle Header Start ========================= */}
            <header className="header-middle bg-color-one border-bottom border-gray-100">
                <div className="container container-lg">
                    <nav className="header-inner flex-between">
                        {/* Logo */}
                        <div className="logo">
                            <Link href="/" className="link">
                                <img
                                    src={
                                        tenant?.logo_url ??
                                        "/assets/images/logo/logo.png"
                                    }
                                    alt={tenant?.name ?? "Logo"}
                                />
                            </Link>
                        </div>
                        {/* Search form */}
                        <form
                            action="/shop"
                            method="GET"
                            className="flex-align flex-wrap form-location-wrapper"
                        >
                            <div className="search-category d-flex h-48 select-border-end-0 radius-end-0 search-form d-sm-flex d-none">
                                <select
                                    defaultValue=""
                                    className="js-example-basic-single border border-gray-200 border-end-0"
                                    name="category"
                                >
                                    <option value="">All Categories</option>
                                    {(categories ?? []).length > 0
                                        ? categories.map((cat) => (
                                              <option
                                                  key={cat.id}
                                                  value={cat.id}
                                              >
                                                  {cat.name}
                                              </option>
                                          ))
                                        : [
                                              "Grocery",
                                              "Breakfast & Dairy",
                                              "Vegetables",
                                              "Milks and Dairies",
                                              "Pet Foods & Toy",
                                              "Breads & Bakery",
                                              "Fresh Seafood",
                                              "Fronzen Foods",
                                              "Noodles & Rice",
                                              "Ice Cream",
                                          ].map((name) => (
                                              <option key={name} value={name}>
                                                  {name}
                                              </option>
                                          ))}
                                </select>
                                <div className="search-form__wrapper position-relative">
                                    <input
                                        type="text"
                                        name="search"
                                        className="search-form__input common-input py-13 ps-16 pe-18 rounded-end-pill pe-44"
                                        placeholder="Search for a product or brand"
                                    />
                                    <button
                                        type="submit"
                                        className="w-32 h-32 bg-main-600 rounded-circle flex-center text-xl text-white position-absolute top-50 translate-middle-y inset-inline-end-0 me-8"
                                    >
                                        <i className="ph ph-magnifying-glass" />
                                    </button>
                                </div>
                            </div>
                            <div className="location-box bg-white flex-align gap-8 py-6 px-16 rounded-pill border border-gray-100">
                                <span className="text-gray-900 text-xl d-xs-flex d-none">
                                    <i className="ph ph-map-pin" />
                                </span>
                                <div className="line-height-1">
                                    <span className="text-gray-600 text-xs">
                                        Your Location
                                    </span>
                                    <div className="line-height-1">
                                        <select
                                            defaultValue="Dhaka"
                                            className="js-example-basic-single border border-gray-200 border-end-0"
                                            name="district"
                                        >
                                            {Object.entries(
                                                BD_DISTRICTS_BY_DIVISION,
                                            ).map(([division, districts]) => (
                                                <optgroup
                                                    key={division}
                                                    label={division}
                                                >
                                                    {districts.map(
                                                        (district) => (
                                                            <option
                                                                key={district}
                                                                value={district}
                                                            >
                                                                {district}
                                                            </option>
                                                        ),
                                                    )}
                                                </optgroup>
                                            ))}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                        {/* Header Middle Right */}
                        <div className="header-right flex-align d-lg-block d-none">
                            <div className="flex-align flex-wrap gap-12">
                                <button
                                    type="button"
                                    className="search-icon flex-align d-lg-none d-flex gap-4 item-hover"
                                >
                                    <span className="text-2xl text-gray-700 d-flex position-relative item-hover__text">
                                        <i className="ph ph-magnifying-glass" />
                                    </span>
                                </button>
                                <Link
                                    href="/wishlist"
                                    className="flex-align gap-4 item-hover"
                                >
                                    <span className="text-2xl text-gray-700 d-flex position-relative me-6 mt-6 item-hover__text">
                                        <i className="ph ph-heart" />
                                        <span className="w-16 h-16 flex-center rounded-circle bg-main-600 text-white text-xs position-absolute top-n6 end-n4">
                                            2
                                        </span>
                                    </span>
                                    <span className="text-md text-gray-500 item-hover__text d-none d-lg-flex">
                                        Wishlist
                                    </span>
                                </Link>
                                <Link
                                    href="/cart"
                                    className="flex-align gap-4 item-hover"
                                >
                                    <span className="text-2xl text-gray-700 d-flex position-relative me-6 mt-6 item-hover__text">
                                        <i className="ph ph-shopping-cart-simple" />
                                        <span className="w-16 h-16 flex-center rounded-circle bg-main-600 text-white text-xs position-absolute top-n6 end-n4">
                                            {cartCount}
                                        </span>
                                    </span>
                                    <span className="text-md text-gray-500 item-hover__text d-none d-lg-flex">
                                        Cart
                                    </span>
                                </Link>
                            </div>
                        </div>
                    </nav>
                </div>
            </header>
            {/* ======================= Middle Header End ========================= */}
            {/* ==================== Header Start Here ==================== */}
            <header
                className={`header bg-white border-bottom border-gray-100 ${scroll && "fixed-header"}`}
            >
                <div className="container container-lg">
                    <nav className="header-inner d-flex justify-content-between gap-8">
                        <div className="flex-align menu-category-wrapper">
                            {/* Category Dropdown */}
                            <div className="category on-hover-item">
                                <button
                                    onClick={handleCategoryToggle}
                                    type="button"
                                    className="category__button flex-align gap-8 fw-medium p-16 border-end border-start border-gray-100 text-heading"
                                >
                                    <span className="icon text-2xl d-xs-flex d-none">
                                        <i className="ph ph-dots-nine" />
                                    </span>
                                    <span className="d-sm-flex d-none">
                                        All
                                    </span>{" "}
                                    Categories
                                    <span className="arrow-icon text-xl d-flex">
                                        <i className="ph ph-caret-down" />
                                    </span>
                                </button>
                                <div
                                    className={`responsive-dropdown cat on-hover-dropdown common-dropdown nav-submenu p-0 submenus-submenu-wrapper ${activeCategory && "active"}`}
                                >
                                    <button
                                        onClick={() => {
                                            handleCategoryToggle();
                                            setActiveIndexCat(null);
                                        }}
                                        type="button"
                                        className="close-responsive-dropdown rounded-circle text-xl position-absolute inset-inline-end-0 inset-block-start-0 mt-4 me-8 d-lg-none d-flex"
                                    >
                                        <i className="ph ph-x" />
                                    </button>
                                    <div className="logo px-16 d-lg-none d-block">
                                        <Link href="/" className="link">
                                            <img
                                                src={
                                                    tenant?.logo_url ??
                                                    "/assets/images/logo/logo.png"
                                                }
                                                alt={tenant?.name ?? "Logo"}
                                            />
                                        </Link>
                                    </div>
                                    {/* Dynamic categories from DB, fallback to static */}
                                    <ul className="scroll-sm p-0 py-8 w-300 max-h-400 overflow-y-auto">
                                        {(categories ?? []).length > 0
                                            ? (categories ?? []).map(
                                                  (cat, idx) => (
                                                      <li
                                                          key={cat.id}
                                                          onClick={() =>
                                                              handleCatClick(
                                                                  idx,
                                                              )
                                                          }
                                                          className={`has-submenus-submenu ${activeIndexCat === idx ? "active" : ""}`}
                                                      >
                                                          <Link
                                                              href={`/shop?category=${cat.id}`}
                                                              className="text-gray-500 text-15 py-12 px-16 flex-align gap-8 rounded-0"
                                                          >
                                                              <span className="text-xl d-flex">
                                                                  <i className="ph ph-tag" />
                                                              </span>
                                                              <span>
                                                                  {cat.name}
                                                              </span>
                                                          </Link>
                                                      </li>
                                                  ),
                                              )
                                            : /* Static fallback categories */
                                              [
                                                  "Vegetables & Fruit",
                                                  "Beverages",
                                                  "Meats & Seafood",
                                                  "Breakfast & Dairy",
                                                  "Frozen Foods",
                                                  "Biscuits & Snacks",
                                                  "Grocery & Staples",
                                              ].map((name, idx) => (
                                                  <li
                                                      key={idx}
                                                      className="has-submenus-submenu"
                                                  >
                                                      <Link
                                                          href="/shop"
                                                          className="text-gray-500 text-15 py-12 px-16 flex-align gap-8 rounded-0"
                                                      >
                                                          <span className="text-xl d-flex">
                                                              <i className="ph ph-tag" />
                                                          </span>
                                                          <span>{name}</span>
                                                      </Link>
                                                  </li>
                                              ))}
                                    </ul>
                                </div>
                            </div>
                            {/* Category Dropdown End */}

                            {/* Desktop Nav Menu */}
                            <div className="header-menu d-lg-block d-none">
                                <ul className="nav-menu flex-align">
                                    <li className="on-hover-item nav-menu__item has-submenu">
                                        <Link
                                            href="#"
                                            className="nav-menu__link"
                                        >
                                            Home
                                        </Link>
                                        <ul className="on-hover-dropdown common-dropdown nav-submenu scroll-sm">
                                            <li className="common-dropdown__item nav-submenu__item">
                                                <Link
                                                    href="/"
                                                    className={navLinkClass(
                                                        "/",
                                                    )}
                                                >
                                                    Home Grocery
                                                </Link>
                                            </li>
                                            <li className="common-dropdown__item nav-submenu__item">
                                                <Link
                                                    href="/index-two"
                                                    className={navLinkClass(
                                                        "/index-two",
                                                    )}
                                                >
                                                    Home Electronics
                                                </Link>
                                            </li>
                                            <li className="common-dropdown__item nav-submenu__item">
                                                <Link
                                                    href="/index-three"
                                                    className={navLinkClass(
                                                        "/index-three",
                                                    )}
                                                >
                                                    Home Fashion
                                                </Link>
                                            </li>
                                        </ul>
                                    </li>
                                    <li className="on-hover-item nav-menu__item has-submenu">
                                        <Link
                                            href="#"
                                            className="nav-menu__link"
                                        >
                                            Shop
                                        </Link>
                                        <ul className="on-hover-dropdown common-dropdown nav-submenu scroll-sm">
                                            <li className="common-dropdown__item nav-submenu__item">
                                                <Link
                                                    href="/shop"
                                                    className={navLinkClass(
                                                        "/shop",
                                                    )}
                                                >
                                                    Shop
                                                </Link>
                                            </li>
                                            <li className="common-dropdown__item nav-submenu__item">
                                                <Link
                                                    href="/product-details"
                                                    className={navLinkClass(
                                                        "/product-details",
                                                    )}
                                                >
                                                    Shop Details
                                                </Link>
                                            </li>
                                            <li className="common-dropdown__item nav-submenu__item">
                                                <Link
                                                    href="/product-details-two"
                                                    className={navLinkClass(
                                                        "/product-details-two",
                                                    )}
                                                >
                                                    Shop Details Two
                                                </Link>
                                            </li>
                                        </ul>
                                    </li>
                                    <li className="on-hover-item nav-menu__item has-submenu">
                                        <span className="badge-notification bg-warning-600 text-white text-sm py-2 px-8 rounded-4">
                                            New
                                        </span>
                                        <Link
                                            href="#"
                                            className="nav-menu__link"
                                        >
                                            Pages
                                        </Link>
                                        <ul className="on-hover-dropdown common-dropdown nav-submenu scroll-sm">
                                            <li className="common-dropdown__item nav-submenu__item">
                                                <Link
                                                    href="/cart"
                                                    className={navLinkClass(
                                                        "/cart",
                                                    )}
                                                >
                                                    Cart
                                                </Link>
                                            </li>
                                            <li className="common-dropdown__item nav-submenu__item">
                                                <Link
                                                    href="/wishlist"
                                                    className={navLinkClass(
                                                        "/wishlist",
                                                    )}
                                                >
                                                    Wishlist
                                                </Link>
                                            </li>
                                            <li className="common-dropdown__item nav-submenu__item">
                                                <Link
                                                    href="/checkout"
                                                    className={navLinkClass(
                                                        "/checkout",
                                                    )}
                                                >
                                                    Checkout
                                                </Link>
                                            </li>
                                            <li className="common-dropdown__item nav-submenu__item">
                                                <Link
                                                    href="/become-seller"
                                                    className={navLinkClass(
                                                        "/become-seller",
                                                    )}
                                                >
                                                    Become Seller
                                                </Link>
                                            </li>
                                            <li className="common-dropdown__item nav-submenu__item">
                                                <Link
                                                    href="/account"
                                                    className={navLinkClass(
                                                        "/account",
                                                    )}
                                                >
                                                    Account
                                                </Link>
                                            </li>
                                        </ul>
                                    </li>
                                    <li className="on-hover-item nav-menu__item has-submenu">
                                        <span className="badge-notification bg-tertiary-600 text-white text-sm py-2 px-8 rounded-4">
                                            New
                                        </span>
                                        <Link
                                            href="#"
                                            className="nav-menu__link"
                                        >
                                            Vendors
                                        </Link>
                                        <ul className="on-hover-dropdown common-dropdown nav-submenu scroll-sm">
                                            <li className="common-dropdown__item nav-submenu__item">
                                                <Link
                                                    href="/vendor"
                                                    className={navLinkClass(
                                                        "/vendor",
                                                    )}
                                                >
                                                    Vendors
                                                </Link>
                                            </li>
                                            <li className="common-dropdown__item nav-submenu__item">
                                                <Link
                                                    href="/vendor-details"
                                                    className={navLinkClass(
                                                        "/vendor-details",
                                                    )}
                                                >
                                                    Vendor Details
                                                </Link>
                                            </li>
                                            <li className="common-dropdown__item nav-submenu__item">
                                                <Link
                                                    href="/vendor-two"
                                                    className={navLinkClass(
                                                        "/vendor-two",
                                                    )}
                                                >
                                                    Vendors Two
                                                </Link>
                                            </li>
                                            <li className="common-dropdown__item nav-submenu__item">
                                                <Link
                                                    href="/vendor-two-details"
                                                    className={navLinkClass(
                                                        "/vendor-two-details",
                                                    )}
                                                >
                                                    Vendors Two Details
                                                </Link>
                                            </li>
                                        </ul>
                                    </li>
                                    <li className="on-hover-item nav-menu__item has-submenu">
                                        <Link
                                            href="#"
                                            className="nav-menu__link"
                                        >
                                            Blog
                                        </Link>
                                        <ul className="on-hover-dropdown common-dropdown nav-submenu scroll-sm">
                                            <li className="common-dropdown__item nav-submenu__item">
                                                <Link
                                                    href="/blog"
                                                    className={navLinkClass(
                                                        "/blog",
                                                    )}
                                                >
                                                    Blog
                                                </Link>
                                            </li>
                                            <li className="common-dropdown__item nav-submenu__item">
                                                <Link
                                                    href="/blog-details"
                                                    className={navLinkClass(
                                                        "/blog-details",
                                                    )}
                                                >
                                                    Blog Details
                                                </Link>
                                            </li>
                                        </ul>
                                    </li>
                                    <li className="nav-menu__item">
                                        <Link
                                            href="/contact"
                                            className={`nav-menu__link${isActive("/contact") ? " activePage" : ""}`}
                                        >
                                            Contact Us
                                        </Link>
                                    </li>
                                </ul>
                            </div>
                            {/* Desktop Nav Menu End */}
                        </div>

                        {/* Header Right */}
                        <div className="header-right flex-align">
                            <a
                                href={`tel:${(tenant?.phone ?? "01-234-567-890").replace(/[^+\d]/g, "")}`}
                                className="bg-main-600 text-white p-12 h-100 hover-bg-main-800 flex-align gap-8 text-lg d-lg-flex d-none"
                            >
                                <div className="d-flex text-32">
                                    <i className="ph ph-phone-call" />
                                </div>
                                {tenant?.phone ?? "01- 234 567 890"}
                            </a>
                            <div className="me-16 d-lg-none d-block">
                                <div className="flex-align flex-wrap gap-12">
                                    <button
                                        onClick={handleSearchToggle}
                                        type="button"
                                        className="search-icon flex-align d-lg-none d-flex gap-4 item-hover"
                                    >
                                        <span className="text-2xl text-gray-700 d-flex position-relative item-hover__text">
                                            <i className="ph ph-magnifying-glass" />
                                        </span>
                                    </button>
                                    <Link
                                        href="/wishlist"
                                        className="flex-align gap-4 item-hover"
                                    >
                                        <span className="text-2xl text-gray-700 d-flex position-relative me-6 mt-6 item-hover__text">
                                            <i className="ph ph-heart" />
                                            <span className="w-16 h-16 flex-center rounded-circle bg-main-600 text-white text-xs position-absolute top-n6 end-n4">
                                                2
                                            </span>
                                        </span>
                                    </Link>
                                    <Link
                                        href="/cart"
                                        className="flex-align gap-4 item-hover"
                                    >
                                        <span className="text-2xl text-gray-700 d-flex position-relative me-6 mt-6 item-hover__text">
                                            <i className="ph ph-shopping-cart-simple" />
                                            <span className="w-16 h-16 flex-center rounded-circle bg-main-600 text-white text-xs position-absolute top-n6 end-n4">
                                                {cartCount}
                                            </span>
                                        </span>
                                    </Link>
                                </div>
                            </div>
                            <button
                                onClick={handleMenuToggle}
                                type="button"
                                className="toggle-mobileMenu d-lg-none ms-3n text-gray-800 text-4xl d-flex"
                            >
                                <i className="ph ph-list" />
                            </button>
                        </div>
                        {/* Header Right End */}
                    </nav>
                </div>
            </header>
            {/* ==================== Header End Here ==================== */}
        </>
    );
};

export default HeaderOne;
