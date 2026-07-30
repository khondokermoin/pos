import React from "react";

export default function Footer() {
    return (
        <footer>
            <div className="tp-footer-area  black-bg pt-95">
                <div className="container">
                    <div className="tp-footer-border pb-40">
                        <div className="row">
                            <div className="col-xl-4 col-lg-3 col-md-6 col-sm-6 mb-50  wow tpfadeUp" data-wow-duration=".9s" data-wow-delay=".3s">
                                <div className="tp-footer-widget footer-cols-1">
                                    <div className="tp-footer-logo pb-35">
                                        <a href="index.html">
                                            <img src="assets/img/logo/logo-white.png" />
                                        </a>
                                    </div>
                                    <div className="tp-footer-text">
                                        <p>Broadx means more than just TV or Connect Internet</p>
                                    </div>
                                    <div className="tp-footer-social">
                                        <a href="#">
                                            <i className="fa-brands fa-facebook-f" />
                                        </a>
                                        <a href="#">
                                            <i className="fa-brands fa-instagram" />
                                        </a>
                                        <a href="#">
                                            <i className="fa-brands fa-pinterest-p" />
                                        </a>
                                        <a href="#">
                                            <i className="fa-brands fa-twitter" />
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div className="col-xl-3 col-lg-3 col-md-6 col-sm-6 mb-50  wow tpfadeUp" data-wow-duration=".9s" data-wow-delay=".5s">
                                <div className="tp-footer-widget footer-cols-2">
                                    <h4 className="tp-footer-title">Navigation</h4>
                                    <div className="tp-footer-list">
                                        <ul>
                                            <li><a href="#">Home</a></li>
                                            <li><a href="#">About</a></li>
                                            <li><a href="#">Our Package</a></li>
                                            <li><a href="#">Services</a></li>
                                            <li><a href="#">News</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div className="col-xl-2 col-lg-3 col-md-6 col-sm-6 mb-50  wow tpfadeUp" data-wow-duration=".9s" data-wow-delay=".7s">
                                <div className="tp-footer-widget footer-cols-3">
                                    <h4 className="tp-footer-title">Customer</h4>
                                    <div className="tp-footer-list">
                                        <ul>
                                            <li><a href="#">Laptop &amp; Computers</a></li>
                                            <li><a href="#">Home &amp; Life Style</a></li>
                                            <li><a href="#">Customer Gurantee</a></li>
                                            <li><a href="#">Broadx Media</a></li>
                                            <li><a href="#">Internet Connection</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div className="col-xl-3 col-lg-3 col-md-6 col-sm-6 mb-50  wow tpfadeUp" data-wow-duration=".9s" data-wow-delay=".9s">
                                <div className="tp-footer-widget footer-cols-4">
                                    <h4 className="tp-footer-title">Contact</h4>
                                    <div className="tp-footer-contact">
                                        <a href="#">
                                            Bouvet Island Jeanetteside 53 Brannon Falls Suite <br /> NY, USA
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div className="tp-copyright-area tp-copyright-space black-bg">
                <div className="container">
                    <div className="row align-items-center">
                        <div className="col-xl-6 col-lg-6 col-md-6 col-12 wow tpfadeUp" data-wow-duration=".9s" data-wow-delay=".3s">
                            <div className="tp-copyright-left text-center text-md-start">
                                <p>
                                    © 2023 <a href="#">Broadx</a> is Proudly Powered by Themepure
                                </p>
                            </div>
                        </div>
                        <div className="col-xl-6 col-lg-6 col-md-6 col-12 wow tpfadeUp" data-wow-duration=".9s" data-wow-delay=".5s">
                            <div className="tp-copyright-right text-center text-md-end">
                                <a href="#">Privacy Policy</a>
                                <span>/</span>
                                <a href="#">Terms of Use</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    );
}
