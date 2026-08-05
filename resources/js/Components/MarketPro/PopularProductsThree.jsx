import React from "react";
import { Link } from "@inertiajs/react";

const PopularProductsThree = ({ products = [], currency = "BDT" }) => {
  return (
    <section className='popular-products-three pb-120 overflow-hidden'>
      <div className='container container-lg'>
        <div className='section-heading mb-24'>
          <h5 className='mb-0 text-uppercase '>Popular Products</h5>
        </div>
        {products.length === 0 ? (
          <p className='text-gray-500'>No products available</p>
        ) : (
          <div className='row gy-4'>
            {products.map((product) => (
              <ProductCard key={product.id} product={product} currency={currency} />
            ))}
          </div>
        )}
      </div>
    </section>
  );
};

const ProductCard = ({ product, currency }) => {
  const primaryVariant = product.variants?.[0];
  const price = product.selling_price ?? primaryVariant?.selling_price;
  const imageUrl =
    product.image ?? "/assets/images/thumbs/trending-three-img7.png";

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

      setAdded(true);
      setTimeout(() => setAdded(false), 1500);
    } catch {
      // localStorage unavailable (private browsing, etc.) — fail silently
    }
  };

  return (
    <div className='col-xxl-3 col-xl-4 col-sm-6'>
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
                e.target.src = "/assets/images/thumbs/trending-three-img7.png";
              }}
            />
          </Link>
          <div className='group bg-white p-2 rounded-pill z-1 position-absolute inset-inline-end-0 inset-block-start-0 me-16 mt-16 shadow-sm'>
            <button
              type='button'
              className='expand-btn w-40 h-40 text-md d-flex justify-content-center align-items-center rounded-circle hover-bg-main-two-600 hover-text-white'
            >
              <i className='ph ph-plus' />
            </button>
            <div className='expand-icons gap-20 my-20'>
              <button
                type='button'
                className='text-neutral-600 text-xl flex-center hover-text-main-two-600 wishlist-btn'
              >
                <i className='ph ph-heart' />
              </button>
              <Link
                href={`/product/${product.id}`}
                className='text-neutral-600 text-xl flex-center hover-text-main-two-600'
              >
                <i className='ph ph-eye' />
              </Link>
            </div>
          </div>
        </div>
        <div className='product-card__content mt-16 w-100'>
          <h6 className='title text-lg fw-semibold my-16'>
            <Link
              href={`/product/${product.id}`}
              className='link text-line-2'
              tabIndex={0}
            >
              {product.name}
            </Link>
          </h6>
          {product.category && (
            <span className='py-2 px-8 text-xs rounded-pill text-main-two-600 bg-main-two-50 mt-16'>
              {product.category.name}
            </span>
          )}
          <div className='product-card__price mt-16 mb-30'>
            {price ? (
              <span className='text-heading text-md fw-semibold '>
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
            tabIndex={0}
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
    </div>
  );
};

export default PopularProductsThree;
