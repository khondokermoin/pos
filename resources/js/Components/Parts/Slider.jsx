import React from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay, Navigation, EffectFade } from "swiper/modules";

// Swiper-এর প্রয়োজনীয় CSS ফাইলগুলো ইমপোর্ট করা আবশ্যক
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/effect-fade";
import "./Slider.css";

export default function Slider() {
    return (
        <div className="tp-slider-area">
            <div className="tp-slider-wrapper p-relative">
                {/* Navigation Arrows */}
                <div className="tp-slider-arrow-wrap d-none d-xxl-block">
                    <div className="tp-slider-arrow-box">
                        <button className="slider-next">
                            <i className="fa-regular fa-arrow-right" />
                        </button>
                        <button className="slider-prev active">
                            <i className="fa-regular fa-arrow-left" />
                        </button>
                    </div>
                </div>

                {/* Swiper Component (অটোমেটিক সাইজ ও ফাংশনালিটি হ্যান্ডেল করবে) */}
                <Swiper
                    modules={[Autoplay, Navigation, EffectFade]}
                    spaceBetween={0}
                    slidesPerView={1}
                    loop={true}
                    effect="fade"
                    fadeEffect={{ crossFade: true }}
                    autoplay={{
                        delay: 5000,
                        disableOnInteraction: false,
                    }}
                    navigation={{
                        nextEl: ".slider-next",
                        prevEl: ".slider-prev",
                    }}
                    className="tp-slider-active"
                >
                    {/* ================= Slide 1 ================= */}
                    <SwiperSlide>
                        <div className="tp-slider-height tp-slider-overlay p-relative">
                            <div className="tp-slider-shape-1 d-none d-lg-block">
                                <a href="#">
                                    <img
                                        src="/assets/img/slider/shape-1.png"
                                        alt="Slider Shape 1"
                                    />
                                </a>
                            </div>
                            
                                <div className="tp-slider-shape-2">
                                    <img
                                        src="/assets/img/slider/shape-2.png"
                                        alt="Slider Shape 2"
                                        style={{
                                            width: "100px",
                                            height: "100px",
                                            objectFit: "contain", // ছবি চ্যাপ্টা বা লম্বা হওয়া আটকাবে
                                            display: "block",
                                            border: "3px solid red", // ✅ ডিবাগিং এর জন্য রাখা আছে। সমস্যা ঠিক হয়ে গেলে এই লাইনটি মুছে দেবেন
                                            position: "absolute", // relative এর বদলে absolute ব্যবহার করুন, এতে লেআউট ভাঙবে না
                                            top: "50px", // আপনার ডিজাইন অনুযায়ী উপরের দূরত্ব অ্যাডজাস্ট করুন
                                            left: "50px", // আপনার ডিজাইন অনুযায়ী বাম দিকের দূরত্ব অ্যাডজাস্ট করুন
                                            zIndex: 10,
                                        }}
                                    />
                                </div>
                            
                            <div className="tp-slider-shape-3 d-none d-lg-block">
                                <img
                                    src="/assets/img/slider/shape-3.png"
                                    alt="Slider Shape 3"
                                />
                            </div>
                            <div className="tp-slider-play-box">
                                <a
                                    className="popup-video"
                                    href="https://www.youtube.com/watch?v=K527oNxtO7o"
                                >
                                    <i className="fa-sharp fa-light fa-play" />
                                </a>
                                <img
                                    src="/assets/img/slider/shape-4.png"
                                    alt="Slider Shape 4"
                                />
                            </div>

                            <div
                                className="tp-slider-bg"
                                style={{
                                    backgroundImage:
                                        "url(/assets/img/slider/slider-1-1.jpg)",
                                }}
                                data-background="/assets/img/slider/slider-1-1.jpg"
                            />

                            <div className="container">
                                <div className="row">
                                    <div className="col-xl-12">
                                        <div className="tp-slider-content z-index">
                                            <div className="tp-slider-title-box mb-30">
                                                <h4 className="tp-slider-subtitle">
                                                    Trusted Internet Service
                                                    Provider
                                                </h4>
                                                <h1 className="tp-slider-title">
                                                    Best Internet <br />{" "}
                                                    Provider Company
                                                </h1>
                                            </div>
                                            <div className="tp-slider-button d-flex align-items-center">
                                                <div className="tp-slider-price d-none d-sm-block">
                                                    <span>
                                                        <b>
                                                            <i>$</i> 39/
                                                        </b>{" "}
                                                        Per Month
                                                    </span>
                                                </div>
                                                <a
                                                    className="tp-btn"
                                                    href="/about-us"
                                                >
                                                    <span>Discover More</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </SwiperSlide>

                    {/* ================= Slide 2 ================= */}
                    <SwiperSlide>
                        <div className="tp-slider-height tp-slider-overlay p-relative">
                            <div className="tp-slider-shape-1 d-none d-lg-block">
                                <a href="#">
                                    <img
                                        src="/assets/img/slider/shape-1.png"
                                        alt="Slider Shape 1"
                                    />
                                </a>
                            </div>
                            <div className="tp-slider-shape-2">
                                <img
                                    src="/assets/img/slider/shape-2.png"
                                    alt="Slider Shape 2"
                                    style={{
                                        border: "3px solid red",
                                        width: "100px",
                                        height: "100px",
                                        opacity: 1,
                                        zIndex: 9999,
                                        position: "relative",
                                        display: "block",
                                    }}
                                />
                            </div>
                            <div className="tp-slider-shape-3 d-none d-lg-block">
                                <img
                                    src="/assets/img/slider/shape-3.png"
                                    alt="Slider Shape 3"
                                />
                            </div>
                            <div className="tp-slider-play-box">
                                <a
                                    className="popup-video"
                                    href="https://www.youtube.com/watch?v=K527oNxtO7o"
                                >
                                    <i className="fa-sharp fa-light fa-play" />
                                </a>
                                <img
                                    src="/assets/img/slider/shape-4.png"
                                    alt="Slider Shape 4"
                                />
                            </div>

                            <div
                                className="tp-slider-bg"
                                style={{
                                    backgroundImage:
                                        "url(/assets/img/slider/slider-1-2.jpg)",
                                }}
                                data-background="/assets/img/slider/slider-1-2.jpg"
                            />

                            <div className="container">
                                <div className="row">
                                    <div className="col-xl-12">
                                        <div className="tp-slider-content z-index">
                                            <div className="tp-slider-title-box mb-30">
                                                <h4 className="tp-slider-subtitle">
                                                    Trusted Internet Service
                                                    Provider
                                                </h4>
                                                <h1 className="tp-slider-title">
                                                    Best Internet <br />{" "}
                                                    Provider Company
                                                </h1>
                                            </div>
                                            <div className="tp-slider-button d-flex align-items-center">
                                                <div className="tp-slider-price d-none d-sm-block">
                                                    <span>
                                                        <b>
                                                            <i>$</i> 39/
                                                        </b>{" "}
                                                        Per Month
                                                    </span>
                                                </div>
                                                <a
                                                    className="tp-btn"
                                                    href="/about-us"
                                                >
                                                    <span>Discover More</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </SwiperSlide>

                    {/* ================= Slide 3 ================= */}
                    <SwiperSlide>
                        <div className="tp-slider-height tp-slider-overlay p-relative">
                            <div className="tp-slider-shape-1 d-none d-lg-block">
                                <a href="#">
                                    <img
                                        src="/assets/img/slider/shape-1.png"
                                        alt="Slider Shape 1"
                                    />
                                </a>
                            </div>
                            <div className="tp-slider-shape-2">
                                <img
                                    src="/assets/img/slider/shape-2.png"
                                    alt="Slider Shape 2"
                                    style={{
                                        border: "3px solid red",
                                        width: "100px",
                                        height: "100px",
                                        opacity: 1,
                                        zIndex: 9999,
                                        position: "relative",
                                        display: "block",
                                    }}
                                />
                            </div>
                            <div className="tp-slider-shape-3 d-none d-lg-block">
                                <img
                                    src="/assets/img/slider/shape-3.png"
                                    alt="Slider Shape 3"
                                />
                            </div>
                            <div className="tp-slider-play-box">
                                <a
                                    className="popup-video"
                                    href="https://www.youtube.com/watch?v=K527oNxtO7o"
                                >
                                    <i className="fa-sharp fa-light fa-play" />
                                </a>
                                <img
                                    src="/assets/img/slider/shape-4.png"
                                    alt="Slider Shape 4"
                                />
                            </div>

                            <div
                                className="tp-slider-bg"
                                style={{
                                    backgroundImage:
                                        "url(/assets/img/slider/slider-1-3.jpg)",
                                }}
                                data-background="/assets/img/slider/slider-1-3.jpg"
                            />

                            <div className="container">
                                <div className="row">
                                    <div className="col-xl-12">
                                        <div className="tp-slider-content z-index">
                                            <div className="tp-slider-title-box mb-30">
                                                <h4 className="tp-slider-subtitle">
                                                    Trusted Internet Service
                                                    Provider
                                                </h4>
                                                <h1 className="tp-slider-title">
                                                    Best Internet <br />{" "}
                                                    Provider Company
                                                </h1>
                                            </div>
                                            <div className="tp-slider-button d-flex align-items-center">
                                                <div className="tp-slider-price d-none d-sm-block">
                                                    <span>
                                                        <b>
                                                            <i>$</i> 39/
                                                        </b>{" "}
                                                        Per Month
                                                    </span>
                                                </div>
                                                <a
                                                    className="tp-btn"
                                                    href="/about-us"
                                                >
                                                    <span>Discover More</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </SwiperSlide>
                </Swiper>
            </div>
        </div>
    );
}
