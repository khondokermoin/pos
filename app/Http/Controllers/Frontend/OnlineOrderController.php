<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\ProductVariant;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Services\BranchRoutingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * OnlineOrderController — Handles online order placement from the React checkout.
 *
 * FLOW:
 *   1. React CheckoutPage submits POST /order with cart items + delivery location
 *   2. BranchRoutingService determines the best branch
 *   3. A Sale record is created with order_type = 'online' and routing audit data
 *   4. SaleItem records are created for each cart item
 *   5. Returns JSON response (Inertia-compatible)
 *
 * BRANCH ROUTING:
 *   - Customer provides city, district, area (text fields)
 *   - Browser optionally provides lat/lng via Geolocation API
 *   - BranchRoutingService runs the 4-step routing algorithm
 *   - The assigned branch_id and routing_method are stored on the sale
 */
class OnlineOrderController extends Controller
{
    public function __construct(
        protected BranchRoutingService $routingService
    ) {}

    /**
     * Place an online order.
     *
     * Expected request body:
     * {
     *   "customer": { "name", "phone", "email" },
     *   "delivery": { "city", "district", "area", "address", "lat", "lng" },
     *   "payment_method": "cash_on_delivery" | "mobile_banking" | "card",
     *   "notes": "...",
     *   "items": [
     *     { "product_id", "variant_id", "quantity", "unit_price" },
     *     ...
     *   ]
     * }
     */
    public function store(Request $request): JsonResponse
    {
        $tenant = app('tenant');

        if (! $tenant) {
            return response()->json(['error' => 'Store not found.'], 404);
        }

        $validated = $request->validate([
            'customer.name'    => 'required|string|max:255',
            'customer.phone'   => 'required|string|max:30',
            'customer.email'   => 'nullable|email|max:255',
            'delivery.city'    => 'required|string|max:100',
            'delivery.district' => 'nullable|string|max:100',
            'delivery.area'    => 'nullable|string|max:100',
            'delivery.address' => 'required|string|max:500',
            'delivery.lat'     => 'nullable|numeric|between:-90,90',
            'delivery.lng'     => 'nullable|numeric|between:-180,180',
            'payment_method'   => 'required|in:cash,card,mobile_banking',
            'notes'            => 'nullable|string|max:1000',
            'items'            => 'required|array|min:1',
            'items.*.variant_id'  => 'required|integer',
            'items.*.quantity'    => 'required|integer|min:1',
        ]);

        // ── Branch Routing ─────────────────────────────────────────────────────
        $routingResult = $this->routingService->route($tenant, [
            'city'     => $validated['delivery']['city'],
            'district' => $validated['delivery']['district'] ?? '',
            'area'     => $validated['delivery']['area'] ?? '',
            'lat'      => $validated['delivery']['lat'] ?? null,
            'lng'      => $validated['delivery']['lng'] ?? null,
        ]);

        $assignedBranch = $routingResult['branch'];

        if (! $assignedBranch) {
            return response()->json([
                'error' => 'We are unable to process your order at this time. No branch is available.',
            ], 422);
        }

        // ── Create Sale & Items in a Transaction ───────────────────────────────
        // Prices are never trusted from the client — every variant is re-fetched
        // and re-priced server-side, scoped to this tenant, and stock is locked
        // and deducted just like the in-store POS checkout.
        DB::beginTransaction();
        try {
            $subtotal      = 0;
            $saleItemsData = [];

            foreach ($validated['items'] as $item) {
                $variant = ProductVariant::with('product')
                    ->where('id', $item['variant_id'])
                    ->where('is_active', true)
                    ->whereHas('product', fn($q) => $q->where('company_id', $tenant->id)->where('is_active', true))
                    ->first();

                if (! $variant) {
                    throw new \RuntimeException('One or more items in your cart are no longer available.');
                }

                // ── Lock the stock row at the assigned branch to prevent race conditions ──
                $stock = Stock::where('variant_id', $variant->id)
                    ->where('branch_id', $assignedBranch->id)
                    ->lockForUpdate()
                    ->first();

                if (! $stock || $stock->quantity < $item['quantity']) {
                    $variantName = $variant->product->name . ($variant->name ? ' — ' . $variant->name : '');
                    throw new \RuntimeException("Insufficient stock for \"{$variantName}\" at our nearest branch.");
                }

                $unitPrice    = (float) $variant->selling_price;
                $lineSubtotal = round($unitPrice * (int) $item['quantity'], 2);
                $subtotal += $lineSubtotal;

                $saleItemsData[] = [
                    'stock'        => $stock,
                    'variant_id'   => $variant->id,
                    'product_name' => $variant->product->name . ($variant->name ? ' — ' . $variant->name : ''),
                    'quantity'     => (int) $item['quantity'],
                    'unit_price'   => $unitPrice,
                    'subtotal'     => $lineSubtotal,
                ];
            }

            // Find or create a customer record
            $customer = Customer::firstOrCreate(
                [
                    'company_id' => $tenant->id,
                    'phone'      => $validated['customer']['phone'],
                ],
                [
                    'name'       => $validated['customer']['name'],
                    'email'      => $validated['customer']['email'] ?? null,
                    'is_walk_in' => false,
                ]
            );

            // Create the sale
            $sale = Sale::create([
                'company_id'          => $tenant->id,
                'branch_id'           => $assignedBranch->id,
                'customer_id'         => $customer->id,
                'user_id'             => null, // No logged-in staff for online orders
                'invoice_no'          => 'ONL-' . strtoupper(Str::random(8)),
                'subtotal'            => $subtotal,
                'discount'            => 0,
                'total_amount'        => $subtotal,
                'received_amount'     => 0,
                'payment_method'      => $validated['payment_method'],
                'status'              => 'pending',
                // Online order fields
                'order_type'          => 'online',
                'delivery_city'       => $validated['delivery']['city'],
                'delivery_district'   => $validated['delivery']['district'] ?? null,
                'delivery_area'       => $validated['delivery']['area'] ?? null,
                'delivery_address'    => $validated['delivery']['address'],
                'customer_lat'        => $validated['delivery']['lat'] ?? null,
                'customer_lng'        => $validated['delivery']['lng'] ?? null,
                'routed_branch_id'    => $assignedBranch->id,
                'routing_method'      => $routingResult['method'],
                'routing_distance_km' => $routingResult['distance_km'],
                'customer_name'       => $validated['customer']['name'],
                'customer_phone'      => $validated['customer']['phone'],
                'customer_email'      => $validated['customer']['email'] ?? null,
                'notes'               => $validated['notes'] ?? null,
            ]);

            // Create sale items, deduct stock, log movements
            foreach ($saleItemsData as $itemData) {
                $sale->items()->create([
                    'variant_id'   => $itemData['variant_id'],
                    'product_name' => $itemData['product_name'],
                    'quantity'     => $itemData['quantity'],
                    'unit_price'   => $itemData['unit_price'],
                    'subtotal'     => $itemData['subtotal'],
                ]);

                $itemData['stock']->decrement('quantity', $itemData['quantity']);

                StockMovement::create([
                    'company_id'     => $tenant->id,
                    'branch_id'      => $assignedBranch->id,
                    'variant_id'     => $itemData['variant_id'],
                    'type'           => 'sale_out',
                    'quantity'       => -$itemData['quantity'],
                    'reference_type' => Sale::class,
                    'reference_id'   => $sale->id,
                    'user_id'        => null,
                ]);
            }

            DB::commit();

            return response()->json([
                'success'    => true,
                'message'    => 'Your order has been placed successfully!',
                'order'      => [
                    'id'             => $sale->id,
                    'invoice_no'     => $sale->invoice_no,
                    'total_amount'   => $sale->total_amount,
                    'status'         => $sale->status,
                    'branch_name'    => $assignedBranch->name,
                    'routing_method' => $routingResult['method'],
                ],
            ], 201);
        } catch (\RuntimeException $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Online order checkout error', [
                'company_id' => $tenant->id,
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
            ]);
            report($e);
            return response()->json([
                'error' => 'Failed to place order. Please try again.',
            ], 500);
        }
    }

    /**
     * Order confirmation page — shown after successful order placement.
     */
    public function confirmation(string $invoiceNo): \Inertia\Response
    {
        $tenant = app('tenant');

        $sale = Sale::where('invoice_no', $invoiceNo)
            ->where('order_type', 'online')
            ->when($tenant, fn($q) => $q->where('company_id', $tenant->id))
            ->with(['routedBranch', 'items'])
            ->firstOrFail();

        return \Inertia\Inertia::render('MarketPro/OrderConfirmationPage', [
            'order' => [
                'id'             => $sale->id,
                'invoice_no'     => $sale->invoice_no,
                'total_amount'   => $sale->total_amount,
                'status'         => $sale->status,
                'payment_method' => $sale->payment_method,
                'delivery_city'  => $sale->delivery_city,
                'delivery_address' => $sale->delivery_address,
                'branch_name'    => $sale->routedBranch?->name,
                'branch_city'    => $sale->routedBranch?->city,
                'routing_method' => $sale->routing_method,
                'notes'          => $sale->notes,
                'created_at'     => $sale->created_at->format('d M Y, h:i A'),
                'items'          => $sale->items->map(fn($item) => [
                    'product_name'  => $item->product_name,
                    'quantity'      => $item->quantity,
                    'unit_price'    => $item->unit_price,
                    'subtotal'      => $item->subtotal,
                ])->toArray(),
            ],
        ]);
    }
}
