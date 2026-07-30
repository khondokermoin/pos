import React from "react";

export default function HeaderMain() {
    return (
        <header className="tp-header-height">
            <div className="tp-header-top-area tp-header-top-height black-bg">
                <div className="container custom-container-1">
                    <div className="row">
                        <div className="col-xl-7 col-lg-6 col-md-6 col-sm-6">
                            <div className="tp-header-top-left">
                                <ul className="text-center text-sm-start">
                                    <li>
                                        <a
                                            target="_blank"
                                            href="https://www.google.com/maps/place/Cumberland+House,+SK,+Canada/@53.6729773,-103.7836571,8z/data=!4m15!1m8!3m7!1s0x4b0d03d337cc6ad9:0x9968b72aa2438fa5!2sCanada!3b1!8m2!3d56.130366!4d-106.346771!16zL20vMGQwNjBn!3m5!1s0x52f917b0cc93e6c1:0x44da1470d37ba724!8m2!3d53.958266!4d-102.267444!16zL20vMDZteWx5?entry=ttu"
                                        >
                                            <i className="fa-light fa-location-dot" /> Manchester 21, Zurich, CH
                                        </a>
                                    </li>
                                    <li className="d-none d-lg-inline-block">
                                        <a href="mailto:broadxinfo@gmail.com">
                                            <i className="fa-light fa-envelope" />
                                            broadxinfo@gmail.com
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div className="col-xl-5 col-lg-6 col-md-6 col-sm-6 d-none d-sm-block">
                            <div className="tp-header-top-social text-end">
                                <span>You can follow us:</span>
                                <a href="#">
                                    <i className="flaticon-facebook" />
                                </a>
                                <a href="#">
                                    <i className="flaticon-instagram" />
                                </a>
                                <a href="#">
                                    <i className="flaticon-tik-tok" />
                                </a>
                                <a href="#">
                                    <i className="flaticon-youtube" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="header-sticky" className="tp-header-area">
                <div className="container custom-container-1">
                    <div className="row align-items-center">
                        <div className="col-xl-2 col-lg-4 col-6">
                            <div className="tp-header-logo">
                                <a href="index.html">
                                    <img src="assets/img/logo/logo-black.png" />
                                </a>
                            </div>
                        </div>
                        <div className="col-xl-6 d-none d-xl-block">
                            <div className="tp-header-main-menu">
                                <nav className="tp-main-menu-content">
                                    <ul>
                                        <li className="has-dropdown">
                                            <a href="index.html">Home</a>
                                            <div className="tp-submenu submenu has-homemenu">
                                                <div className="row gx-6 row-cols-1 row-cols-md-2 row-cols-xl-3">
                                                    <div className="col homemenu">
                                                        <div className="homemenu-thumb mb-15">
                                                            <img src="assets/img/menu/home-1.jpg" />
                                                            <div className="homemenu-btn">
                                                                <a className="tp-menu-btn" href="index.html">Multi page</a>
                                                                <a className="tp-menu-btn" href="index-one-page.html">One Page</a>
                                                            </div>
                                                        </div>
                                                        <div className="homemenu-content text-center">
                                                            <h4 className="homemenu-title">
                                                                <a href="index.html">Home 01</a>
                                                            </h4>
                                                        </div>
                                                    </div>
                                                    <div className="col homemenu">
                                                        <div className="homemenu-thumb mb-15">
                                                            <img src="assets/img/menu/home-2.jpg" />
                                                            <div className="homemenu-btn">
                                                                <a className="tp-menu-btn" href="index-2.html">Multi page</a>
                                                                <a className="tp-menu-btn" href="index-2-one-page.html">One Page</a>
                                                            </div>
                                                        </div>
                                                        <div className="homemenu-content text-center">
                                                            <h4 className="homemenu-title">
                                                                <a href="index-2.html">Home 02</a>
                                                            </h4>
                                                        </div>
                                                    </div>
                                                    <div className="col homemenu">
                                                        <div className="homemenu-thumb mb-15">
                                                            <img src="assets/img/menu/home-3.jpg" />
                                                            <div className="homemenu-btn">
                                                                <a className="tp-menu-btn" href="index-3.html">Multi page</a>
                                                                <a className="tp-menu-btn" href="index-3-one-page.html">One Page</a>
                                                            </div>
                                                        </div>
                                                        <div className="homemenu-content text-center">
                                                            <h4 className="homemenu-title">
                                                                <a href="index-3.html">Home 03</a>
                                                            </h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li className="has-dropdown">
                                            <a href="#">Pages</a>
                                            <ul className="submenu tp-submenu">
                                                <li><a href="about-us.html">about us</a></li>
                                                <li><a href="movie.html">Movie</a></li>
                                                <li><a href="team.html">team</a></li>
                                                <li><a href="team-details.html">team details</a></li>
                                                <li><a href="cart.html">cart</a></li>
                                                <li><a href="checkout.html">checkout</a></li>
                                                <li><a href="price.html">price</a></li>
                                                <li><a href="faq.html">faq</a></li>
                                                <li><a href="404.html">error</a></li>
                                            </ul>
                                        </li>
                                        <li className="has-dropdown">
                                            <a href="service.html">Service</a>
                                            <ul className="submenu tp-submenu">
                                                <li><a href="service.html">Service</a></li>
                                                <li><a href="service-details.html">Service Details</a></li>
                                            </ul>
                                        </li>
                                        <li className="has-dropdown">
                                            <a href="blog.html">News</a>
                                            <ul className="submenu tp-submenu">
                                                <li><a href="blog.html">Blog</a></li>
                                                <li><a href="blog-details.html">Blog Details</a></li>
                                            </ul>
                                        </li>
                                        <li className="has-dropdown">
                                            <a href="shop.html">Shop</a>
                                            <ul className="submenu tp-submenu">
                                                <li><a href="shop.html">Shop</a></li>
                                                <li><a href="shop-details.html">Shop Details</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="contact.html">Contact</a></li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                        <div className="col-xl-4 col-lg-8 col-6">
                            <div className="tp-header-right d-flex align-items-center justify-content-end">
                                <div className="tp-header-btn d-none d-md-block">
                                    <a className="tp-btn-sm" href="contact.html">
                                        <span>Get Started Now</span>
                                    </a>
                                </div>
                                <div className="tp-header-icon d-none d-xl-block">
                                    <button className="search-open-btn">
                                        <i className="flaticon-search" />
                                    </button>
                                    <a href="cart.html">
                                        <i className="flaticon-cart" />
                                    </a>
                                </div>
                                <div className="tp-header-bar d-xl-none">
                                    <button className="tp-menu-bar">
                                        <i className="fa-solid fa-bars" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
    );
}
