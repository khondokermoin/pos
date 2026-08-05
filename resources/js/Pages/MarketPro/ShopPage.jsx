import React, { useState } from "react";
import { usePage, Link, router } from "@inertiajs/react";
import Preloader from "../../Helpers/Preloader";
import HeaderTwo from "../../Components/MarketPro/HeaderTwo";
import Breadcrumb from "../../Components/MarketPro/Breadcrumb";
import ShippingTwo from "../../Components/MarketPro/ShippingTwo";
import FooterTwo from "../../Components/MarketPro/FooterTwo";
import ColorInit from "../../Helpers/ColorInit";
import ScrollToTop from "react-scroll-to-top";
import ReactSlider from "react-slider";

/**
 * ShopPage — Inertia-powered product listing page.
 *
 * DATA SOURCE: ShopController::shop() via Inertia::render('MarketPro/ShopPage', [...])
 *
 * PROPS (from usePage().props):
 *   - products   : { data: Product[], meta: {...}, links: [...] }
 *   - categories : Category[]
 *   - filters    : { category, search, min, max, sort }
 *   - tenant     : Company branding (from HandleInertiaRequests shared props)
 */
const ShopPage = () => {
    const { products, categories, filters, tenant } = usePage().props;

    const [grid, setGrid] = useState(false);
    const [active, setActive] = useState(false);
    const [priceRange, setPriceRange] = useState([
        filters?.min ? Number(filters.min) : 0,
        filters?.max ? Number(filters.max) : 10000,
    ]);

    const currency = tenant?.currency ?? "BDT";

    const sidebarController = () => setActive(!active);

    // Navigate with filters — Inertia preserves scroll and only updates changed props
    const applyFilter = (newFilters) => {
        router.get(
            "/shop",
            { ...filters, ...newFilters },
            { preserveState: true, preserveScroll: true },
        );
    };

    const handleCategoryFilter = (categoryId) => {
        applyFilter({ category: categoryId || undefined, page: 1 });
    };

    const handleSortChange = (e) => {
        applyFilter({ sort: e.target.value, page: 1 });
    };

    const handlePriceFilter = () => {
        applyFilter({ min: priceRange[0], max: priceRange[1], page: 1 });
    };

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        const search = e.target.search.value;
        applyFilter({ search: search || undefined, page: 1 });
    };

    const productList = products?.data ?? [];
    const meta = products?.meta ?? {};

    return (
        <>
            <ColorInit color={true} />
            <ScrollToTop smooth color="#FA6400" />
            <Preloader />
            <HeaderTwo category={true} />
            <Breadcrumb title={"Shop"} />

            <section className="shop py-80">
                <div className={`side-overlay ${active && "show"}`}></div>
                <div className="container container-lg">
                    <div className="row">
                        {/* ── Sidebar ─────────────────────────────────────── */}
                        <div className="col-lg-3">
                            <div
                                className={`shop-sidebar ${active && "active"}`}
                            >
                                <button
                                    onClick={sidebarController}
                                    type="button"
                                    className="shop-sidebar__close d-lg-none d-flex w-32 h-32 flex-center border border-gray-100 rounded-circle hover-bg-main-600 position-absolute inset-inline-end-0 me-10 mt-8 hover-text-white hover-border-main-600"
                                >
                                    <i className="ph ph-x" />
                                </button>

                                {/* Category Filter */}
                                <div className="shop-sidebar__box border border-gray-100 rounded-8 p-32 mb-32">
                                    <h6 className="text-xl border-bottom border-gray-100 pb-24 mb-24">
                                        Product Category
                                    </h6>
                                    <ul className="max-h-540 overflow-y-auto scroll-sm">
                                        <li className="mb-16">
                                            <button
                                                onClick={() =>
                                                    handleCategoryFilter(null)
                                                }
                                                className={`text-gray-900 hover-text-main-600 bg-transparent border-0 p-0 ${!filters?.category ? "fw-bold text-main-600" : ""}`}
                                            >
                                                All Categories
                                                {meta?.total
                                                    ? ` (${meta.total})`
                                                    : ""}
                                            </button>
                                        </li>
                                        {(categories ?? []).map((cat) => (
                                            <li key={cat.id} className="mb-16">
                                                <button
                                                    onClick={() =>
                                                        handleCategoryFilter(
                                                            cat.id,
                                                        )
                                                    }
                                                    className={`text-gray-900 hover-text-main-600 bg-transparent border-0 p-0 ${filters?.category == cat.id ? "fw-bold text-main-600" : ""}`}
                                                >
                                                    {cat.name}
                                                    {cat.products_count !==
                                                    undefined
                                                        ? ` (${cat.products_count})`
                                                        : ""}
                                                </button>
                                            </li>
                                        ))}
                                    </ul>
                                </div>

                                {/* Price Filter */}
                                <div className="shop-sidebar__box border border-gray-100 rounded-8 p-32 mb-32">
                                    <h6 className="text-xl border-bottom border-gray-100 pb-24 mb-24">
                                        Filter by Price
                                    </h6>
                                    <div className="custom--range">
                                        <ReactSlider
                                            className="horizontal-slider"
                                            thumbClassName="example-thumb"
                                            trackClassName="example-track"
                                            value={priceRange}
                                            onChange={setPriceRange}
                                            min={0}
                                            max={100000}
                                            ariaLabel={[
                                                "Lower thumb",
                                                "Upper thumb",
                                            ]}
                                            renderThumb={(props, state) => {
                                                const { key, ...restProps } =
                                                    props;
                                                return (
                                                    <div
                                                        {...restProps}
                                                        key={state.index}
                                                    >
                                                        {state.valueNow}
                                                    </div>
                                                );
                                            }}
                                            pearling
                                            minDistance={100}
                                        />
                                        <br />
                                        <br />
                                        <div className="flex-between flex-wrap-reverse gap-8 mt-8">
                                            <span className="text-sm text-gray-600">
                                                {currency}{" "}
                                                {priceRange[0].toLocaleString()}{" "}
                                                — {currency}{" "}
                                                {priceRange[1].toLocaleString()}
                                            </span>
                                            <button
                                                type="button"
                                                onClick={handlePriceFilter}
                                                className="btn btn-main h-40 flex-align px-16"
                                            >
                                                Filter
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {/* ── Sidebar End ──────────────────────────────────── */}

                        {/* ── Product Grid ─────────────────────────────────── */}
                        <div className="col-lg-9">
                            {/* Top bar */}
                            <div className="flex-between gap-16 flex-wrap mb-40">
                                <span className="text-gray-900">
                                    {meta?.from && meta?.to
                                        ? `Showing ${meta.from}–${meta.to} of ${meta.total} results`
                                        : `${productList.length} products`}
                                </span>
                                <div className="position-relative flex-align gap-16 flex-wrap">
                                    <div className="list-grid-btns flex-align gap-16">
                                        <button
                                            onClick={() => setGrid(true)}
                                            type="button"
                                            className={`w-44 h-44 flex-center border rounded-6 text-2xl list-btn border-gray-100 ${grid === true && "border-main-600 text-white bg-main-600"}`}
                                        >
                                            <i className="ph-bold ph-list-dashes" />
                                        </button>
                                        <button
                                            onClick={() => setGrid(false)}
                                            type="button"
                                            className={`w-44 h-44 flex-center border rounded-6 text-2xl grid-btn border-gray-100 ${grid === false && "border-main-600 text-white bg-main-600"}`}
                                        >
                                            <i className="ph ph-squares-four" />
                                        </button>
                                    </div>
                                    <div className="position-relative text-gray-500 flex-align gap-4 text-14">
                                        <label
                                            htmlFor="sorting"
                                            className="text-inherit flex-shrink-0"
                                        >
                                            Sort by:{" "}
                                        </label>
                                        <select
                                            value={filters?.sort ?? "latest"}
                                            onChange={handleSortChange}
                                            className="form-control common-input px-14 py-14 text-inherit rounded-6 w-auto"
                                            id="sorting"
                                        >
                                            <option value="latest">
                                                Latest
                                            </option>
                                            <option value="price_asc">
                                                Price: Low to High
                                            </option>
                                            <option value="price_desc">
                                                Price: High to Low
                                            </option>
                                        </select>
                                    </div>
                                    <button
                                        onClick={sidebarController}
                                        type="button"
                                        className="w-44 h-44 d-lg-none d-flex flex-center border border-gray-100 rounded-6 text-2xl sidebar-btn"
                                    >
                                        <i className="ph-bold ph-funnel" />
                                    </button>
                                </div>
                            </div>

                            {/* Search bar */}
                            <form
                                onSubmit={handleSearchSubmit}
                                className="mb-32 d-flex gap-8"
                            >
                                <input
                                    type="text"
                                    name="search"
                                    defaultValue={filters?.search ?? ""}
                                    placeholder="Search products..."
                                    className="common-input border-gray-100 flex-grow-1"
                                />
                                <button
                                    type="submit"
                                    className="btn btn-main px-24"
                                >
                                    <i className="ph ph-magnifying-glass" />
                                </button>
                            </form>

                            {/* Product cards */}
                            {productList.length === 0 ? (
                                <div className="text-center py-80">
                                    <i className="ph ph-package text-6xl text-gray-300 mb-16 d-block" />
                                    <h5 className="text-gray-500">
                                        No products found
                                    </h5>
                                    <p className="text-gray-400">
                                        Try adjusting your filters or search
                                        term.
                                    </p>
                                </div>
                            ) : (
                                <div
                                    className={`list-grid-wrapper ${grid && "list-view"}`}
                                >
                                    {productList.map((product) => (
                                        <ProductCard
                                            key={product.id}
                                            product={product}
                                            currency={currency}
                                        />
                                    ))}
                                </div>
                            )}

                            {/* Pagination */}
                            {meta?.last_page > 1 && (
                                <div className="flex-center gap-8 mt-40 flex-wrap">
                                    {Array.from(
                                        { length: meta.last_page },
                                        (_, i) => i + 1,
                                    ).map((page) => (
                                        <button
                                            key={page}
                                            onClick={() =>
                                                applyFilter({ page })
                                            }
                                            className={`w-40 h-40 flex-center border rounded-8 text-sm fw-medium transition-1 ${
                                                page === meta.current_page
                                                    ? "bg-main-600 text-white border-main-600"
                                                    : "border-gray-100 text-gray-700 hover-bg-main-600 hover-text-white hover-border-main-600"
                                            }`}
                                        >
                                            {page}
                                        </button>
                                    ))}
                                </div>
                            )}
                        </div>
                        {/* ── Product Grid End ──────────────────────────────── */}
                    </div>
                </div>
            </section>

            <ShippingTwo />
            <FooterTwo />
        </>
    );
};

/**
 * ProductCard — renders a single product in the shop grid.
 * Uses Inertia <Link> for navigation (triggers a new Inertia request with fresh data).
 *
 * FIXED: "Add To Cart" now writes to localStorage instead of navigating to /cart.
 * The cart format matches what CheckoutPage.jsx expects:
 *   { variant_id, product_id, name, variant_name, unit_price, quantity, image }
 */
const ProductCard = ({ product, currency }) => {
    const primaryVariant = product.variants?.[0];
    const price = product.selling_price ?? primaryVariant?.selling_price;
    const imageUrl =
        product.image ?? "/assets/images/thumbs/product-two-img1.png";

    const [added, setAdded] = React.useState(false);

    const handleAddToCart = () => {
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

            // Brief visual feedback
            setAdded(true);
            setTimeout(() => setAdded(false), 1500);
        } catch {
            // localStorage unavailable (private browsing, etc.) — fail silently
        }
    };

    return (
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
                        e.target.src =
                            "/assets/images/thumbs/product-two-img1.png";
                    }}
                />
            </Link>
            <div className="product-card__content mt-16">
                {product.category && (
                    <span className="text-xs text-gray-400 mb-4 d-block">
                        {product.category.name}
                    </span>
                )}
                <h6 className="title text-lg fw-semibold mt-8 mb-8">
                    <Link
                        href={`/product/${product.id}`}
                        className="link text-line-2"
                    >
                        {product.name}
                    </Link>
                </h6>
                <div className="product-card__price my-16">
                    {price ? (
                        <span className="text-heading text-md fw-semibold">
                            {currency} {Number(price).toLocaleString()}
                        </span>
                    ) : (
                        <span className="text-gray-400 text-sm">
                            Price on request
                        </span>
                    )}
                </div>
                <button
                    type="button"
                    onClick={handleAddToCart}
                    disabled={!price}
                    className={`product-card__cart btn py-11 px-24 rounded-8 flex-center gap-8 fw-medium w-100 transition-1 ${
                        added
                            ? "bg-success-600 text-white"
                            : "bg-gray-50 text-heading hover-bg-main-600 hover-text-white"
                    }`}
                >
                    {added ? (
                        <>
                            Added <i className="ph ph-check-circle" />
                        </>
                    ) : (
                        <>
                            Add To Cart <i className="ph ph-shopping-cart" />
                        </>
                    )}
                </button>
            </div>
        </div>
    );
};

export default ShopPage;
