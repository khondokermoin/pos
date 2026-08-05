import React, { useState } from "react";
import { Link } from "@inertiajs/react";

/**
 * ProductCard — renders a single product in the New Arrivals grid.
 * Mirrors the ProductCard pattern used in ShopPage.jsx.
 */
const ProductCard = ({ product, currency }) => {
  const primaryVariant = product.variants?.[0];
  const price = product.selling_price ?? primaryVariant?.selling_price;
  const imageUrl =
    product.image ?? "/assets/images/thumbs/product-two-img1.png";

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
    <div className='product-card h-100 p-16 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2'>
      <Link
        href={`/product/${product.id}`}
        className='product-card__thumb flex-center rounded-8 bg-gray-50 position-relative'
      >
        <img
          src={imageUrl}
          alt={product.name}
          className='w-auto max-w-unset'
          onError={(e) => {
            e.target.src = "/assets/images/thumbs/product-two-img1.png";
          }}
        />
      </Link>
      <div className='product-card__content mt-16'>
        {product.category && (
          <span className='text-xs text-gray-400 mb-4 d-block'>
            {product.category.name}
          </span>
        )}
        <h6 className='title text-lg fw-semibold mt-8 mb-8'>
          <Link href={`/product/${product.id}`} className='link text-line-2'>
            {product.name}
          </Link>
        </h6>
        <div className='product-card__price my-16'>
          {price ? (
            <span className='text-heading text-md fw-semibold'>
              {currency} {Number(price).toLocaleString()}
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
  );
};

/**
 * NewArrivalThree — "New Arrivals" storefront section (HomePageThree).
 *
 * Tabs are derived from the real `categories` prop ("All" first). Selecting a
 * tab filters `products` by `product.category?.id`. Product cards render
 * real database data — no fake ratings/badges/compare-prices.
 */
const NewArrivalThree = ({ products = [], currency = "BDT", categories = [] }) => {
  const tabs = [{ id: "all", name: "All" }, ...categories];
  const [activeTab, setActiveTab] = useState("all");

  const filteredProducts =
    activeTab === "all"
      ? products
      : products.filter((product) => product.category?.id === activeTab);

  return (
    <section className='new-arrival-three py-120 overflow-hidden'>
      <div className='container container-lg'>
        <div className='section-heading text-center '>
          <h5 className='mb-0 text-uppercase '>New Arrivals</h5>
        </div>
        <ul
          className='nav common-tab style-two nav-pills justify-content-center mb-40 '
          id='pills-tabThree'
          role='tablist'
        >
          {tabs.map((tab) => (
            <li className='nav-item' role='presentation' key={tab.id}>
              <button
                className={`nav-link ${activeTab === tab.id ? "active" : ""}`}
                type='button'
                role='tab'
                aria-selected={activeTab === tab.id}
                onClick={() => setActiveTab(tab.id)}
              >
                {tab.name}
              </button>
            </li>
          ))}
        </ul>
        <div className='tab-content' id='pills-tabContentThree'>
          <div className='tab-pane fade show active' role='tabpanel'>
            <div className='new-arrival-three-wrapper'>
              <div className='row gy-4'>
                <div className='col-xl-4'>
                  <div className='rounded-24 overflow-hidden border border-main-two-600 p-16 bg-color-three h-100'>
                    <div
                      className='bg-img w-100 h-100 min-h-485 rounded-24 overflow-hidden'
                      style={{
                        backgroundImage:
                          "url(/assets/images/thumbs/new-arrival-promo-img1.png)",
                      }}
                    >
                      <div className='py-32 pe-32 text-end'>
                        <span className='text-uppercase fw-semibold text-neutral-600 text-md'>
                          Summer offer
                        </span>
                        <h5 className='mb-0'>Get 85% Off</h5>
                        <Link
                          href='/shop'
                          className='btn btn-black rounded-pill gap-8 mt-32 flex-align d-inline-flex'
                          tabIndex={0}
                        >
                          Shop Now
                          <span className='text-xl d-flex'>
                            <i className='ph ph-shopping-cart-simple' />
                          </span>
                        </Link>
                      </div>
                    </div>
                  </div>
                </div>
                <div className='col-xl-8'>
                  <div className='row gy-4'>
                    {filteredProducts.length === 0 ? (
                      <div className='col-12 text-center py-40'>
                        <i className='ph ph-package text-6xl text-gray-300 mb-16 d-block' />
                        <p className='text-gray-500 mb-0'>
                          No products found.
                        </p>
                      </div>
                    ) : (
                      filteredProducts.map((product) => (
                        <div className='col-lg-4 col-sm-6' key={product.id}>
                          <ProductCard product={product} currency={currency} />
                        </div>
                      ))
                    )}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default NewArrivalThree;
