import React from "react";

export default function OffcanvasMenu() {
    return (
        <>
            <div className="tpoffcanvas-area">
                <div className="tpoffcanvas">
                    <div className="tpoffcanvas__close-btn">
                        <button className="close-btn">
                            <i className="fal fa-times" />
                        </button>
                    </div>
                    <div className="tpoffcanvas__logo">
                        <a href="index.html">
                            <img src="assets/img/logo/logo-white.png" />
                        </a>
                    </div>
                    <div className="tpoffcanvas__title">
                        <p>
                            Lorem ipsum dolor sit amet consectetur
                            adipisicing elit. Minima incidunt eaque ab
                            cumque, porro maxime autem sed.
                        </p>
                    </div>
                    <div className="tp-main-menu-mobile d-xl-none" />
                    <div className="tpoffcanvas__contact-info">
                        <div className="tpoffcanvas__contact-title">
                            <h5>Contact us</h5>
                        </div>
                        <ul>
                            <li>
                                <i className="fa-light fa-location-dot" />
                                <a
                                    href="https://www.google.com/maps/@23.8223586,90.3661283,15z"
                                    target="_blank"
                                >
                                    Melbone st, Australia, Ny 12099
                                </a>
                            </li>
                            <li>
                                <i className="fas fa-envelope" />
                                <a href="mailto:solaredge@gmail.com">
                                    themepure@gmail.com
                                </a>
                            </li>
                            <li>
                                <i className="fal fa-phone- " />
                                <a href="tel:+48555223224">
                                    +48 555 223 224
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div className="tpoffcanvas__input">
                        <div className="tpoffcanvas__input-title">
                            <h4>Get UPdate</h4>
                        </div>
                        <form action="#">
                            <div className="p-relative">
                                <input type="text" placeholder="Enter mail" />
                                <button>
                                    <i className="fas fa-paper-plane" />
                                </button>
                            </div>
                        </form>
                    </div>
                    <div className="tpoffcanvas__social">
                        <div className="social-icon">
                            <a href="#">
                                <i className="fab fa-twitter" />
                            </a>
                            <a href="#">
                                <i className="fab fa-instagram" />
                            </a>
                            <a href="#">
                                <i className="fab fa-facebook-f" />
                            </a>
                            <a href="#">
                                <i className="fab fa-pinterest-p" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div className="body-overlay" />
        </>
    );
}
