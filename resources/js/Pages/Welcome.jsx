import { BrowserRouter } from "react-router-dom";
import App from "./MarketPro/App";
import PhosphorIconInit from "../Helpers/PhosphorIconInit";

export default function Welcome() {
    return (
        <>
            <PhosphorIconInit />
            <BrowserRouter>
                <App />
            </BrowserRouter>
        </>
    );
}
