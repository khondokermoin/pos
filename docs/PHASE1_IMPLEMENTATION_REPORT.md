# 📋 Phase 1 Implementation Report — Hybrid Architecture + Location-Based Ordering

**Project:** Cloud POS Inventory v5  
**Date:** August 2026  
**Status:** ✅ COMPLETE  
**Architecture Decision:** Hybrid — Backend stays Blade, Frontend powered by React + Inertia.js

---

## EXECUTIVE SUMMARY

Phase 1 is fully implemented. The codebase now has:

1. **Hybrid Architecture** — The `BrowserRouter` SPA anti-pattern has been eliminated. Each frontend route now renders its own dedicated Inertia page component with real server-side data. The backend (Company Admin, Super Admin) remains 100% Blade — untouched.

2. **Location-Based Online Ordering** — A complete online ordering system with 4-step smart branch routing (Coverage Area Match → GPS Distance → Default Branch → Fallback), full checkout flow, order confirmation, and Company Admin reporting dashboard.

3. **Phase 2 Cancelled** — Per architectural decision, the backend Product Management pages will NOT be converted from Blade to React. The Hybrid Architecture is the final target state.

---

## SECTION 1 — ARCHITECTURAL CHANGE: THE CRITICAL FIX

### Before (Broken SPA Pattern)

```
ALL 18 routes → Inertia::render('Welcome') → BrowserRouter → react-router-dom
                                              ↑
                              No per-page data injection possible
```

### After (Correct Inertia Pattern)

```
GET /          → ShopController::home()    → Inertia::render('MarketPro/HomePageOne', $data)
GET /shop      → ShopController::shop()    → Inertia::render('MarketPro/ShopPage', $data)
GET /product/1 → ShopController::productDetail() → Inertia::render('MarketPro/ProductDetailsPageOne', $data)
GET /checkout  → ShopController::checkout() → Inertia::render('MarketPro/CheckoutPage', $branches)
POST /order    → OnlineOrderController::store() → JSON response
GET /order/confirmation/{no} → OnlineOrderController::confirmation() → Inertia::render(...)
```

Each page now receives its own real data from the database, scoped to the resolved tenant.

---

## SECTION 2 — FILES CREATED / MODIFIED

### 2.1 Database Migrations

| File                                                           | Purpose                                                                                                                                                                                    |
| -------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `2026_08_05_100001_add_location_to_branches_table.php`         | Adds `city`, `district`, `latitude`, `longitude`, `coverage_areas` (JSON), `is_default`, `accepts_online_orders` to `branches`                                                             |
| `2026_08_05_100002_add_online_order_fields_to_sales_table.php` | Adds `order_type`, `delivery_city/district/area/address`, `customer_lat/lng`, `routed_branch_id`, `routing_method`, `routing_distance_km`, `customer_name/phone/email`, `notes` to `sales` |

**To apply:**

```bash
php artisan migrate
```

### 2.2 Models Updated

| File                    | Change                                                                                                                                             |
| ----------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------- |
| `app/Models/Branch.php` | Added `coverage_areas`, `is_default`, `accepts_online_orders` casts; added `onlineOrders()` relationship                                           |
| `app/Models/Sale.php`   | Added all online order fields to `$fillable`; added `routedBranch()` relationship; added `scopeOnlineOrders()` and `scopePosOrders()` query scopes |

### 2.3 New Services

| File                                    | Purpose                                                                                                      |
| --------------------------------------- | ------------------------------------------------------------------------------------------------------------ |
| `app/Services/BranchRoutingService.php` | **The core routing engine.** Implements 4-step branch routing algorithm with Haversine distance calculation. |

**BranchRoutingService — Routing Algorithm:**

```
Step 1: Coverage Area Match
  → Customer city/district/area is checked against each branch's coverage_areas JSON array
  → Case-insensitive partial matching (e.g. "Mirpur" matches "Mirpur-1", "Mirpur-10")
  → If no coverage_areas defined, falls back to matching branch.city / branch.district directly

Step 2: Haversine Distance (GPS)
  → Only runs if customer provided lat/lng (browser Geolocation API)
  → Calculates great-circle distance to every branch with lat/lng coordinates
  → Assigns the nearest branch

Step 3: Default Branch
  → Assigns the branch flagged with is_default = true
  → This is the "Barisal customer → Dhaka branch" fallback scenario

Step 4: Fallback
  → Assigns the first active branch of the company
  → Last resort — always succeeds
```

### 2.4 New Controllers

| File                                                           | Purpose                                                                        |
| -------------------------------------------------------------- | ------------------------------------------------------------------------------ |
| `app/Http/Controllers/Frontend/ShopController.php`             | Serves all frontend pages with real tenant-scoped data via `Inertia::render()` |
| `app/Http/Controllers/Frontend/OnlineOrderController.php`      | Handles `POST /order` (place order) and `GET /order/confirmation/{no}`         |
| `app/Http/Controllers/Company/OnlineOrderReportController.php` | Company Admin reporting dashboard for online orders (Blade)                    |

### 2.5 Routes Updated

| File                  | Change                                                                                                                                                                                   |
| --------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `routes/frontend.php` | **Complete rewrite.** Replaced the `foreach` closure loop with named controller routes. Each route renders its own Inertia page. Added `POST /order` and `GET /order/confirmation/{no}`. |
| `routes/web.php`      | Added `GET /company/reports/online-orders` → `OnlineOrderReportController::index()`                                                                                                      |

### 2.6 React Pages Updated/Created

| File                                                     | Status        | Change                                                                                                                                            |
| -------------------------------------------------------- | ------------- | ------------------------------------------------------------------------------------------------------------------------------------------------- |
| `resources/js/Pages/MarketPro/ShopPage.jsx`              | **Rewritten** | Now uses `usePage().props` for real data. Dynamic category filter, price range slider, sort, search, pagination — all via Inertia `router.get()`. |
| `resources/js/Pages/MarketPro/HomePageOne.jsx`           | **Updated**   | Now uses `usePage().props` to pass `featuredProducts`, `newArrivals`, `categories`, `tenant` to child components.                                 |
| `resources/js/Pages/MarketPro/CheckoutPage.jsx`          | **Rewritten** | Full checkout form with location capture (City/District/Area + GPS Geolocation API). Reads cart from `localStorage`. Posts to `POST /order`.      |
| `resources/js/Pages/MarketPro/OrderConfirmationPage.jsx` | **New**       | Order success page showing invoice, delivery info, assigned branch, routing method, and order items.                                              |

### 2.7 Blade Views Created

| File                                                      | Purpose                                                                                                                                                                          |
| --------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `resources/views/company/reports/online_orders.blade.php` | Company Admin online orders report with: summary stats, orders-by-city table, orders-by-branch table, routing method breakdown with progress bars, paginated recent orders table |

---

## SECTION 3 — LOCATION-BASED ROUTING: COMPLETE FLOW

```
Customer visits /checkout
    │
    ▼
CheckoutPage.jsx renders
    │  - Reads branches from usePage().props.branches (passed by ShopController::checkout())
    │  - Displays "We deliver to: Mirpur, Savar, Uttara..." banner
    │
    ▼
Customer fills form:
    │  - First Name, Phone, Email
    │  - City: "Barisal"          ← KEY FIELD for routing
    │  - District: "Barisal"
    │  - Area: "Sadar"
    │  - Address: "House 12, Road 3..."
    │  - [Optional] Clicks "Use My GPS Location" → browser Geolocation API → lat/lng captured
    │
    ▼
Customer clicks "Place Order"
    │
    ▼
POST /order (JSON payload)
    │  {
    │    customer: { name, phone, email },
    │    delivery: { city: "Barisal", district: "Barisal", area: "Sadar", address: "...", lat: 22.70, lng: 90.36 },
    │    payment_method: "cash",
    │    items: [{ variant_id: 5, quantity: 2, unit_price: 450 }]
    │  }
    │
    ▼
OnlineOrderController::store()
    │
    ▼
BranchRoutingService::route($company, $location)
    │
    ├── Step 1: Coverage Area Match
    │     → Check all branches' coverage_areas for "barisal"
    │     → Company only has Dhaka branch → NO MATCH
    │
    ├── Step 2: Haversine Distance (GPS provided)
    │     → Dhaka branch: lat=23.81, lng=90.41
    │     → Distance from Barisal (22.70, 90.36) = ~124 km
    │     → Only one branch → ASSIGNED (nearest = only option)
    │
    └── Result: { branch: DhakaBranch, method: 'distance', distance_km: 124.3 }
    │
    ▼
Sale created:
    │  - branch_id = DhakaBranch.id
    │  - order_type = 'online'
    │  - delivery_city = 'Barisal'
    │  - routed_branch_id = DhakaBranch.id
    │  - routing_method = 'distance'
    │  - routing_distance_km = 124.3
    │
    ▼
Response: { success: true, order: { invoice_no: "ONL-A3B7C2D1", ... } }
    │
    ▼
React redirects to: /order/confirmation/ONL-A3B7C2D1
    │
    ▼
OrderConfirmationPage.jsx renders:
    - "Order Placed Successfully!"
    - Fulfilling Branch: Dhaka Main Branch
    - Routing: "Nearest branch by location"
    - Items, total, payment method
```

---

## SECTION 4 — COMPANY ADMIN REPORTING

**Route:** `GET /company/reports/online-orders`  
**Named Route:** `company.reports.online-orders`  
**Controller:** `OnlineOrderReportController::index()`  
**View:** `resources/views/company/reports/online_orders.blade.php`

### Report Sections:

1. **Date Range Filter** — Filter all data by date range (defaults to current month)

2. **Summary Stats (4 cards):**
    - Total Online Orders
    - Total Revenue
    - Pending Orders
    - Unique Delivery Cities

3. **Orders by Delivery Location** — Table showing which cities/districts orders came from, with order count and revenue

4. **Orders by Branch** — Table showing which branches received online orders, how many cities they served, and their revenue

5. **Routing Method Breakdown** — 4 cards with progress bars showing:
    - Coverage Area Match (most accurate)
    - GPS Distance
    - Default Branch
    - Fallback

6. **Recent Online Orders Table** — Paginated (20/page) with:
    - Invoice number
    - Customer name + phone
    - Delivery city/district/area
    - Routed branch + city
    - Routing method badge + distance
    - Amount
    - Status badge
    - Date/time

---

## SECTION 5 — BRANCH SETUP GUIDE (For Company Admin)

To enable smart routing, configure branches via the Branch management UI:

```
Company Admin → Branches → Edit Branch

Fields to fill:
  City:            "Dhaka"
  District:        "Dhaka"
  Latitude:        23.8103
  Longitude:       90.4125
  Coverage Areas:  ["Mirpur", "Pallabi", "Kafrul", "Savar", "Uttara", "Gulshan"]
  Is Default:      ✓ (check for the main/fallback branch)
  Accepts Online:  ✓
```

**Coverage Area Tips:**

- Add all neighbourhoods, thanas, and areas the branch can serve
- Matching is case-insensitive and partial (e.g. "Mirpur" matches "Mirpur-1", "Mirpur-10", "Mirpur DOHS")
- If a customer types "Mirpur-10", it will match a branch with "Mirpur" in coverage_areas
- For GPS-based routing, add accurate lat/lng coordinates (use Google Maps)

---

## SECTION 6 — CART ARCHITECTURE (Client-Side)

Cart state is managed entirely client-side using `localStorage`. This is intentional:

- **No server round-trip** for add-to-cart operations
- **Persists across page navigations** (Inertia visits)
- **Cart format in localStorage:**
    ```json
    [
        {
            "variant_id": 5,
            "product_id": 3,
            "name": "Samsung Galaxy A54",
            "variant_name": "128GB Black",
            "unit_price": 45000,
            "quantity": 1,
            "image": "/storage/products/samsung-a54.jpg"
        }
    ]
    ```
- The `CheckoutPage.jsx` reads this array from `localStorage` to display the order summary and build the POST payload
- On successful order placement, `localStorage.removeItem('cart')` clears the cart

**To implement "Add to Cart" on product pages**, add this to `ProductDetailsPageOne.jsx`:

```jsx
const addToCart = (variant, quantity) => {
    const cart = JSON.parse(localStorage.getItem("cart") ?? "[]");
    const existing = cart.findIndex((i) => i.variant_id === variant.id);
    if (existing >= 0) {
        cart[existing].quantity += quantity;
    } else {
        cart.push({
            variant_id: variant.id,
            product_id: product.id,
            name: product.name,
            variant_name: variant.name,
            unit_price: variant.selling_price,
            quantity,
            image: product.image,
        });
    }
    localStorage.setItem("cart", JSON.stringify(cart));
};
```

---

## SECTION 7 — WHAT REMAINS (Future Work)

| Task                                                        | Priority | Notes                                                            |
| ----------------------------------------------------------- | -------- | ---------------------------------------------------------------- |
| Add "Add to Cart" button to `ProductDetailsPageOne.jsx`     | High     | Uses localStorage pattern above                                  |
| Add cart item count badge to `HeaderTwo.jsx`                | High     | Read from localStorage on mount                                  |
| Wire `tenant` branding into `HeaderOne/Two/Three.jsx` logos | Medium   | `usePage().props.tenant.logo_url`                                |
| Add Branch location fields to Branch edit form (Blade)      | Medium   | `city`, `district`, `lat`, `lng`, `coverage_areas`, `is_default` |
| Add "Online Orders" link to Company Admin sidebar           | Medium   | Route: `company.reports.online-orders`                           |
| Implement `ProductDetailsPageTwo.jsx` with Inertia data     | Low      | Same pattern as PageOne                                          |
| Add order status update for Company Admin                   | Low      | `PATCH /company/sales/{id}/status`                               |

---

## SECTION 8 — HOW TO RUN

```bash
# 1. Run the new migrations
php artisan migrate

# 2. Build frontend assets
npm run dev        # development
npm run build      # production

# 3. Clear caches
php artisan optimize:clear
```

---

_Implementation completed: August 2026_  
_Architecture: Hybrid (Blade backend + React/Inertia frontend)_  
_Phase 2 (Backend Blade → React): CANCELLED per architectural decision_
