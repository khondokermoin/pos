import React, { useEffect, useState } from "react";
import { getCountdown } from "../../Helpers/Countdown";
import { Link } from "@inertiajs/react";

const DealsSection = ({ products = [], currency = "BDT" }) => {
  const [timeLeft, setTimeLeft] = useState(getCountdown());

  useEffect(() => {
    const interval = setInterval(() => {
      setTimeLeft(getCountdown());
    }, 1000);

    return () => clearInterval(interval);
  }, []);

  const handleAddToCart = (product) => {
    const primaryVariant = product.variants?.[0];
    const price = product.selling_price ?? primaryVariant?.selling_price;
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
    <section className='deals pb-120'>
      <div className='container container-lg'>
        <div
          className='bg-neutral-600 rounded-48 bg-img'
          style={{ backgroundImage: "url(/assets/images/bg/pattern-two.png)" }}
        >
          <div className='row gy-4 align-items-center'>
            <div className='col-xl-6 d-md-block d-none'>
              <div className='position-relative px-24'>
                {products.length > 0 && (
                  <ul className='products-group'>
                    {products.map((product) => {
                      const primaryVariant = product.variants?.[0];
                      const price =
                        product.selling_price ?? primaryVariant?.selling_price;
                      const imageUrl =
                        product.image ??
                        "/assets/images/thumbs/trending-three-img3.png";

                      return (
                        <li key={product.id} className='products-group__list pt-12'>
                          <span className='w-32 h-32 border border-white rounded-circle flex-center position-relative overflow-hidden border-2 bg-blur cursor-pointer'>
                            <span className='w-12 h-12 rounded-circle bg-white' />
                          </span>
                          <div className='products-group__card product-card w-100 p-16 border border-gray-100 hover-border-main-600 max-w-340 rounded-16 transition-2 bg-white position-absolute bottom-100 start-50 min-width-max-content rotate-10 transition-2'>
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
                                    e.target.src =
                                      "/assets/images/thumbs/trending-three-img3.png";
                                  }}
                                />
                              </Link>
                              <button
                                type='button'
                                onClick={() => handleAddToCart(product)}
                                className='z-1 position-absolute inset-inline-end-0 inset-block-start-0 me-16 mt-16  text-neutral-600 text-xl flex-center hover-text-main-two-600 wishlist-btn'
                              >
                                <i className='ph ph-shopping-cart text-main-two-600' />
                              </button>
                            </div>
                            <div className='product-card__content mt-16 w-100'>
                              <h6 className='title text-2xl fw-semibold my-8'>
                                <Link
                                  href={`/product/${product.id}`}
                                  className='link text-line-2'
                                  tabIndex={0}
                                >
                                  {product.name}
                                </Link>
                              </h6>
                              {product.category && (
                                <span className='text-xs text-gray-400 mb-4 d-block'>
                                  {product.category.name}
                                </span>
                              )}
                              <div className='product-card__price mt-8 mb-8'>
                                {price ? (
                                  <span className='text-neutral-600 text-lg fw-semibold'>
                                    {currency} {Number(price).toLocaleString()}
                                  </span>
                                ) : (
                                  <span className='text-gray-400 text-sm'>
                                    Price on request
                                  </span>
                                )}
                              </div>
                            </div>
                          </div>
                        </li>
                      );
                    })}
                  </ul>
                )}
                <div className='text-end'>
                  <img
                    src='/assets/images/thumbs/deals-img.png'
                    alt=''
                    className='pe-xxl-5 pe-lg-4 deals__img'
                  />
                </div>
              </div>
            </div>
            <div className='col-xl-6' >
              <div className='p-56-px'>
                <div className='text-center border border-white rounded-80 rounded-top-right-0 p-56-px'>
                  <div className='p-56-px bg-white rounded-80 rounded-top-right-0'>
                    <div className='max-w-472 mx-auto'>
                      <span className='text-white bg-neutral-600 py-8 px-16 rounded-pill fw-medium text-md mb-32 text-uppercase'>
                        Only For Today
                      </span>
                      <h3 className='mb-32 fw-medium text-uppercase'>
                        Deal of The Day
                      </h3>
                      <p className='text-neutral-600'>
                        Deal of the day for deals on consumer fashion cloths for
                        many other great daily offers
                      </p>
                    </div>
                    <div className='mt-48'>
                      <div className='countdown' id='countdown24'>
                        <ul className='countdown-list style-four flex-center flex-wrap gap-24'>
                          <li>
                            <span className='days flex-center w-80 h-80 fw-semibold text-white rounded-16 bg-main-two-600 text-28'>
                              {timeLeft.days}
                            </span>
                            <span className='mt-8 text-neutral-600 text-xl text-uppercase fw-medium d-block text-center'>
                              Days
                            </span>
                          </li>
                          <li>
                            <span className='days flex-center w-80 h-80 fw-semibold text-white rounded-16 bg-main-two-600 text-28'>
                              {timeLeft.hours}
                            </span>
                            <span className='mt-8 text-neutral-600 text-xl text-uppercase fw-medium d-block text-center'>
                              Hour
                            </span>
                          </li>
                          <li>
                            <span className='days flex-center w-80 h-80 fw-semibold text-white rounded-16 bg-main-two-600 text-28'>
                              {timeLeft.minutes}
                            </span>
                            <span className='mt-8 text-neutral-600 text-xl text-uppercase fw-medium d-block text-center'>
                              Min
                            </span>
                          </li>
                          <li>
                            <span className='days flex-center w-80 h-80 fw-semibold text-white rounded-16 bg-main-two-600 text-28'>
                              {timeLeft.seconds}
                            </span>
                            <span className='mt-8 text-neutral-600 text-xl text-uppercase fw-medium d-block text-center'>
                              Sec
                            </span>
                          </li>
                        </ul>
                      </div>
                    </div>
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

export default DealsSection;
