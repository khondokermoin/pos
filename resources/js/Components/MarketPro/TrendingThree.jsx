import React, { useEffect, useState } from "react";
import { getCountdown } from "../../Helpers/Countdown";
import { Link } from "@inertiajs/react";

/**
 * TrendingThree — trending products section for HomePageThree.
 *
 * PROPS:
 *   - products   : Product[] (from HomePageThree's `featuredProducts` prop)
 *   - currency   : string (tenant currency code)
 *   - categories : Category[] (drives the "All" + per-category nav-pill tabs)
 */
const TrendingThree = ({ products = [], currency = "BDT", categories = [] }) => {
  const [timeLeft, setTimeLeft] = useState(getCountdown());
  const [activeTab, setActiveTab] = useState("all");

  useEffect(() => {
    const interval = setInterval(() => {
      setTimeLeft(getCountdown());
    }, 1000);

    return () => clearInterval(interval);
  }, []);

  const filteredProducts =
    activeTab === "all"
      ? products
      : products.filter((product) => product.category?.id === activeTab);

  return (
    <section className='trending-products-three py-120 overflow-hidden'>
      <div className='container container-lg'>
        <div className='section-heading mb-24'>
          <div className='flex-between flex-wrap gap-8'>
            <h5 className='mb-0 text-uppercase'>Trending Products</h5>
            <ul
              className='nav common-tab style-two nav-pills'
              id='pills-tab'
              role='tablist'
            >
              <li className='nav-item' role='presentation'>
                <button
                  className={`nav-link ${activeTab === "all" ? "active" : ""}`}
                  type='button'
                  role='tab'
                  aria-selected={activeTab === "all"}
                  onClick={() => setActiveTab("all")}
                >
                  All
                </button>
              </li>
              {categories.map((cat) => (
                <li className='nav-item' role='presentation' key={cat.id}>
                  <button
                    className={`nav-link ${activeTab === cat.id ? "active" : ""}`}
                    type='button'
                    role='tab'
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
        <div className='tab-content' id='pills-tabContent'>
          <div
            className='tab-pane fade show active'
            role='tabpanel'
            tabIndex={0}
          >
            <div className='row g-12'>
              {filteredProducts.length === 0 ? (
                <div className='col-12 text-center py-40'>
                  <p className='text-gray-500 mb-0'>No products found.</p>
                </div>
              ) : (
                filteredProducts.map((product) => (
                  <TrendingProductCard
                    key={product.id}
                    product={product}
                    currency={currency}
                    timeLeft={timeLeft}
                  />
                ))
              )}
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

/**
 * TrendingProductCard — single product card used by TrendingThree.
 * Mirrors the ProductCard pattern in Pages/MarketPro/ShopPage.jsx:
 *   - price sourced from product.selling_price ?? variants[0].selling_price
 *   - never displays cost_price or a fabricated "was" price
 *   - "Add To Cart" writes to localStorage in the same shape CheckoutPage.jsx expects
 */
const TrendingProductCard = ({ product, currency, timeLeft }) => {
  const primaryVariant = product.variants?.[0];
  const price = product.selling_price ?? primaryVariant?.selling_price;
  const imageUrl =
    product.image ?? "/assets/images/thumbs/trending-three-img1.png";

  const [added, setAdded] = useState(false);

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

      setAdded(true);
      setTimeout(() => setAdded(false), 1500);
    } catch {
      // localStorage unavailable (private browsing, etc.) — fail silently
    }
  };

  return (
    <div className='col-xl-3 col-lg-4 col-sm-6'>
      <div className='product-card h-100 p-16 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2'>
        <div className='product-card__thumb rounded-8 bg-gray-50 position-relative'>
          <Link
            href={`/product/${product.id}`}
            className='w-100 h-100 flex-center'
          >
            <img
              src={imageUrl}
              alt={product.name}
              className='w-auto max-w-unset'
              onError={(e) => {
                e.target.src = "/assets/images/thumbs/trending-three-img1.png";
              }}
            />
          </Link>
          <div className='countdown position-absolute start-50 inset-block-end-0 mb-20 translate-middle-x w-100'>
            <ul className='countdown-list style-four flex-center flex-wrap gap-8'>
              <li className='countdown-list__item flex-align flex-column text-sm fw-medium text-white rounded-lg bg-neutral-600'>
                <span className='days text-2xl text-main-two-600 fw-medium'>
                  {timeLeft.days}
                </span>
                Days
              </li>
              <li className='countdown-list__item flex-align flex-column text-sm fw-medium text-white rounded-lg bg-neutral-600'>
                <span className='hours text-2xl text-main-two-600 fw-medium'>
                  {timeLeft.hours}
                </span>
                Hour
              </li>
              <li className='countdown-list__item flex-align flex-column text-sm fw-medium text-white rounded-lg bg-neutral-600'>
                <span className='minutes text-2xl text-main-two-600 fw-medium'>
                  {timeLeft.minutes}
                </span>
                Min
              </li>
              <li className='countdown-list__item flex-align flex-column text-sm fw-medium text-white rounded-lg bg-neutral-600'>
                <span className='seconds text-2xl text-main-two-600 fw-medium'>
                  {timeLeft.seconds}
                </span>
                Sec
              </li>
            </ul>
          </div>
        </div>
        <div className='product-card__content mt-16 w-100'>
          {product.category && (
            <span className='py-2 px-8 text-xs rounded-pill text-main-two-600 bg-main-two-50 mb-8 d-inline-block'>
              {product.category.name}
            </span>
          )}
          <h6 className='title text-lg fw-semibold my-16'>
            <Link
              href={`/product/${product.id}`}
              className='link text-line-2'
              tabIndex={0}
            >
              {product.name}
            </Link>
          </h6>
          <div className='product-card__price mt-16 mb-30'>
            {price ? (
              <span className='text-heading text-md fw-semibold'>
                {currency} {Number(price).toLocaleString()}{" "}
                <span className='text-gray-500 fw-normal'>/Qty</span>
              </span>
            ) : (
              <span className='text-gray-400 text-sm'>Price on request</span>
            )}
          </div>
          <button
            type='button'
            onClick={handleAddToCart}
            disabled={!price}
            className={`product-card__cart btn py-11 px-24 rounded-8 flex-center gap-8 fw-medium w-100 transition-1 ${
              added
                ? "bg-success-600 text-white"
                : "bg-gray-50 text-heading hover-bg-main-600 hover-text-white"
            }`}
            tabIndex={0}
          >
            {added ? (
              <>
                Added <i className='ph ph-check-circle' />
              </>
            ) : (
              <>
                Add To Cart <i className='ph ph-shopping-cart' />
              </>
            )}
          </button>
        </div>
      </div>
    </div>
  );
};

export default TrendingThree;
