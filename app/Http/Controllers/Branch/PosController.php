<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\HeldOrder;
use App\Models\InvoiceTemplate;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Shift;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PosController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // Public Pages
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * POS Terminal — Main Page
     * Loads the full-screen POS interface with all bootstrap data.
     */
    public function index()
    {
        $user     = auth()->user();
        $branchId = $user->branch_id;

        // Enforce open shift for non-admin roles
        $hasOpenShift = Shift::where('branch_id', $branchId)
            ->where('opened_by', $user->id)
            ->where('status', 'open')
            ->exists();

        if (! $hasOpenShift && ! $user->hasRole('Company Admin') && ! $user->hasRole('Super Admin')) {
            return redirect()->route('branch.shifts.create')
                ->with('error', 'Please open your cash register (shift) before using the POS.');
        }

        // Load categories for the product grid filter tabs
        $categories = Category::where('company_id', $user->company_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        // Load customers for the customer selector (scoped to company)
        $customers = Customer::where('company_id', $user->company_id)
            ->orderBy('name')
            ->get(['id', 'name', 'phone']);

        // Load initial product grid (first page, all categories)
        $products = $this->getProductsForBranch($branchId, $user->company_id, null, '');

        // Load held orders for this branch
        $heldOrders = HeldOrder::where('branch_id', $branchId)
            ->with('customer:id,name')
            ->orderByDesc('updated_at')
            ->get(['id', 'customer_id', 'label', 'items', 'discount', 'updated_at']);

        return view('branch.pos.index', compact('categories', 'customers', 'products', 'heldOrders'));
    }

    /**
     * Invoice Print View — thermal receipt layout.
     */
    public function printInvoice(Sale $sale)
    {
        if ($sale->branch_id !== auth()->user()->branch_id) {
            abort(403, 'Access denied.');
        }

        $sale->load(['items.variant.product', 'customer', 'user', 'branch.company']);

        // Load the active default invoice template (set by Super Admin)
        $template = InvoiceTemplate::getDefault();

        return view('branch.pos.invoice_print', compact('sale', 'template'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // API: Product Lookup
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * API: Search products by barcode, SKU, or name.
     * Returns a single best-match product for barcode scanning.
     */
    public function search(Request $request)
    {
        $query    = trim($request->input('q', ''));
        $user     = auth()->user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            return response()->json(['success' => false, 'message' => 'No branch assigned to your account.'], 400);
        }

        if (strlen($query) < 1) {
            return response()->json(['success' => false, 'message' => 'Search query is empty.'], 400);
        }

        // Priority: exact barcode → exact SKU → name LIKE
        $variant = ProductVariant::with(['product', 'tax'])
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('barcode', $query)
                    ->orWhere('sku', $query)
                    ->orWhereHas('product', fn($p) => $p->where('name', 'LIKE', "%{$query}%"));
            })
            ->whereHas('product', fn($p) => $p->where('company_id', $user->company_id)->where('is_active', true))
            ->first();

        if (! $variant) {
            return response()->json(['success' => false, 'message' => "No product found for: \"{$query}\""], 404);
        }

        $stock = Stock::where('variant_id', $variant->id)
            ->where('branch_id', $branchId)
            ->first();

        $qty = $stock ? (int) $stock->quantity : 0;

        if ($qty <= 0) {
            return response()->json([
                'success' => false,
                'message' => '"' . $variant->product->name . '" is out of stock.',
            ], 422);
        }

        return response()->json([
            'success'         => true,
            'variant_id'      => $variant->id,
            'name'            => $variant->product->name . ($variant->name ? ' — ' . $variant->name : ''),
            'sku'             => $variant->sku,
            'barcode'         => $variant->barcode,
            'price'           => (float) $variant->selling_price,
            'tax_rate'        => $variant->tax ? (float) $variant->tax->rate : 0,
            'tax_name'        => $variant->tax ? $variant->tax->name : null,
            'available_stock' => $qty,
            'reorder_level'   => (int) ($stock->reorder_level ?? $variant->reorder_level ?? 5),
            'image'           => $variant->product->image
                ? asset('storage/' . $variant->product->image)
                : null,
        ]);
    }

    /**
     * API: Load product grid with optional category filter and search.
     * Used for the visual product browser on the left panel.
     */
    public function products(Request $request)
    {
        $user       = auth()->user();
        $branchId   = $user->branch_id;
        $categoryId = $request->input('category_id');
        $search     = trim($request->input('q', ''));

        $products = $this->getProductsForBranch($branchId, $user->company_id, $categoryId, $search);

        return response()->json(['success' => true, 'products' => $products]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // API: Checkout
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * API: Process checkout — atomic transaction with stock deduction.
     *
     * SECURITY: Client-supplied price/tax_rate are IGNORED.
     * Authoritative selling_price and tax_rate are fetched from the DB
     * inside the transaction, preventing price-manipulation attacks.
     */
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.variant_id' => 'required|integer|exists:product_variants,id',
            'items.*.qty'        => 'required|integer|min:1',
            'customer_id'        => 'nullable|integer',
            'payment_method'     => 'required|in:cash,card,mobile_banking',
            'received_amount'    => 'required|numeric|min:0',
            'discount'           => 'nullable|numeric|min:0',
            'note'               => 'nullable|string|max:500',
        ]);

        $user      = auth()->user();
        $branchId  = $user->branch_id;
        $companyId = $user->company_id;
        $userId    = $user->id;
        $discount  = (float) ($validated['discount'] ?? 0);

        // ── Validate shift is still open (server-side, not just UI) ──
        $hasOpenShift = Shift::where('branch_id', $branchId)
            ->where('opened_by', $userId)
            ->where('status', 'open')
            ->exists();

        if (! $hasOpenShift && ! $user->hasRole('Company Admin') && ! $user->hasRole('Super Admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Your shift is not open. Please open a shift before processing sales.',
            ], 422);
        }

        // ── Validate customer belongs to this company ─────────────────
        if (! empty($validated['customer_id'])) {
            $customerExists = Customer::where('id', $validated['customer_id'])
                ->where('company_id', $companyId)
                ->exists();

            if (! $customerExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid customer selected.',
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            $subtotal      = 0;
            $totalTax      = 0;
            $saleItemsData = [];

            foreach ($validated['items'] as $item) {
                // ── Fetch authoritative price & tax from DB (ignore client values) ──
                $variant = ProductVariant::with('tax')
                    ->where('id', $item['variant_id'])
                    ->where('is_active', true)
                    ->whereHas('product', fn($q) => $q->where('company_id', $companyId)->where('is_active', true))
                    ->first();

                if (! $variant) {
                    throw new \RuntimeException('Product variant not found or is inactive.');
                }

                // ── Lock the stock row to prevent race conditions ──────
                $stock = Stock::where('variant_id', $item['variant_id'])
                    ->where('branch_id', $branchId)
                    ->lockForUpdate()
                    ->first();

                if (! $stock || $stock->quantity < $item['qty']) {
                    $variantName = $variant->product->name . ($variant->name ? ' — ' . $variant->name : '');
                    throw new \RuntimeException(
                        "Insufficient stock for \"{$variantName}\". Available: " . ($stock->quantity ?? 0) . ', requested: ' . $item['qty'] . '.'
                    );
                }

                // ── Use server-authoritative price & tax ──────────────
                $unitPrice    = (float) $variant->selling_price;
                $taxRate      = $variant->tax ? (float) $variant->tax->rate : 0;
                $lineSubtotal = round($unitPrice * (int) $item['qty'], 2);
                $lineTax      = round($lineSubtotal * ($taxRate / 100), 2);

                $subtotal += $lineSubtotal;
                $totalTax += $lineTax;

                $saleItemsData[] = [
                    'stock'        => $stock,
                    'variant_id'   => $item['variant_id'],
                    'product_name' => $variant->product->name . ($variant->name ? ' — ' . $variant->name : ''),
                    'quantity'     => (int) $item['qty'],
                    'unit_price'   => $unitPrice,
                    'subtotal'     => $lineSubtotal,
                    'tax_rate'     => $taxRate,
                    'tax_amount'   => $lineTax,
                ];
            }

            $totalAmount = round($subtotal + $totalTax - $discount, 2);
            if ($totalAmount < 0) {
                $totalAmount = 0;
            }

            // ── Validate received amount covers total (cash/mobile) ───
            // For card payments the terminal handles auth; we still require
            // received_amount >= total to record the transaction correctly.
            if ((float) $validated['received_amount'] < $totalAmount) {
                throw new \RuntimeException(
                    'Received amount (৳' . number_format($validated['received_amount'], 2) .
                        ') is less than total payable (৳' . number_format($totalAmount, 2) . ').'
                );
            }

            // ── Generate unique invoice number ────────────────────────
            $invoiceNo = 'INV-' . strtoupper(base_convert(time(), 10, 36)) . '-' . strtoupper(substr(uniqid(), -4));

            // ── Create the Sale record ────────────────────────────────
            $sale = Sale::create([
                'company_id'      => $companyId,
                'branch_id'       => $branchId,
                'customer_id'     => $validated['customer_id'] ?? null,
                'user_id'         => $userId,
                'invoice_no'      => $invoiceNo,
                'subtotal'        => $subtotal,
                'discount'        => $discount,
                'total_amount'    => $totalAmount,
                'received_amount' => (float) $validated['received_amount'],
                'payment_method'  => $validated['payment_method'],
                'status'          => 'completed',
            ]);

            // ── Create sale items, deduct stock, log movements ────────
            foreach ($saleItemsData as $itemData) {
                $sale->items()->create([
                    'variant_id'   => $itemData['variant_id'],
                    'product_name' => $itemData['product_name'],
                    'quantity'     => $itemData['quantity'],
                    'unit_price'   => $itemData['unit_price'],
                    'subtotal'     => $itemData['subtotal'],
                    'tax_rate'     => $itemData['tax_rate'],
                    'tax_amount'   => $itemData['tax_amount'],
                ]);

                $itemData['stock']->decrement('quantity', $itemData['quantity']);

                StockMovement::create([
                    'company_id'     => $companyId,
                    'branch_id'      => $branchId,
                    'variant_id'     => $itemData['variant_id'],
                    'type'           => 'sale_out',
                    'quantity'       => -$itemData['quantity'],
                    'reference_type' => Sale::class,
                    'reference_id'   => $sale->id,
                    'user_id'        => $userId,
                ]);
            }

            DB::commit();

            $change = round((float) $validated['received_amount'] - $totalAmount, 2);

            return response()->json([
                'success'       => true,
                'message'       => 'Sale completed successfully!',
                'sale_id'       => $sale->id,
                'invoice_no'    => $sale->invoice_no,
                'print_url'     => route('branch.pos.invoice-print', $sale->id),
                'change'        => $change,
                'server_total'  => $totalAmount,
            ]);
        } catch (\RuntimeException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('POS checkout error', [
                'user_id'   => auth()->id(),
                'branch_id' => $branchId ?? null,
                'error'     => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again or contact support.',
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // API: Quick-Create Customer
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * API: Create a new customer inline from the POS terminal.
     */
    public function quickCreateCustomer(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
        ]);

        $user = auth()->user();

        $customer = Customer::create([
            'company_id' => $user->company_id,
            'name'       => $validated['name'],
            'phone'      => $validated['phone'] ?? null,
            'email'      => $validated['email'] ?? null,
            'is_walk_in' => false,
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Customer created successfully.',
            'customer' => [
                'id'    => $customer->id,
                'name'  => $customer->name,
                'phone' => $customer->phone,
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // API: Hold / Suspend Orders
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * API: Hold (suspend) the current cart for later recall.
     */
    public function holdOrder(Request $request)
    {
        $validated = $request->validate([
            'items'       => 'required|array|min:1',
            'items.*.variant_id'      => 'required|integer',
            'items.*.name'            => 'required|string',
            'items.*.price'           => 'required|numeric',
            'items.*.qty'             => 'required|integer|min:1',
            'items.*.tax_rate'        => 'nullable|numeric',
            'items.*.available_stock' => 'nullable|integer',
            'items.*.reorder_level'   => 'nullable|integer',
            'customer_id' => 'nullable|integer',
            'discount'    => 'nullable|numeric|min:0',
            'label'       => 'nullable|string|max:100',
        ]);

        $user = auth()->user();

        $held = HeldOrder::create([
            'company_id'  => $user->company_id,
            'branch_id'   => $user->branch_id,
            'user_id'     => $user->id,
            'customer_id' => $validated['customer_id'] ?? null,
            'label'       => $validated['label'] ?? null,
            'items'       => $validated['items'],
            'discount'    => (float) ($validated['discount'] ?? 0),
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Order held successfully.',
            'held_id'  => $held->id,
            'label'    => $held->label,
        ]);
    }

    /**
     * API: List all held orders for this branch.
     */
    public function heldOrders()
    {
        $user = auth()->user();

        $orders = HeldOrder::where('branch_id', $user->branch_id)
            ->with('customer:id,name')
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn($h) => [
                'id'          => $h->id,
                'label'       => $h->label,
                'customer'    => $h->customer?->name ?? 'Walk-in',
                'item_count'  => count($h->items),
                'discount'    => (float) $h->discount,
                'items'       => $h->items,
                'customer_id' => $h->customer_id,
                'updated_at'  => $h->updated_at->diffForHumans(),
            ]);

        return response()->json(['success' => true, 'orders' => $orders]);
    }

    /**
     * API: Delete (discard) a held order.
     */
    public function deleteHeldOrder(HeldOrder $heldOrder)
    {
        if ($heldOrder->branch_id !== auth()->user()->branch_id) {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }

        $heldOrder->delete();

        return response()->json(['success' => true, 'message' => 'Held order discarded.']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private Helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Fetch products with stock info for the visual product grid.
     */
    private function getProductsForBranch(int $branchId, int $companyId, ?int $categoryId, string $search): array
    {
        $query = ProductVariant::with(['product.category', 'tax'])
            ->where('is_active', true)
            ->whereHas('product', function ($q) use ($companyId, $categoryId, $search) {
                $q->where('company_id', $companyId)->where('is_active', true);
                if ($categoryId) {
                    $q->where('category_id', $categoryId);
                }
                if ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                }
            })
            ->whereHas('branchStock', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->with(['branchStock' => fn($q) => $q->where('branch_id', $branchId)])
            ->orderBy('id')
            ->limit(80); // Grid cap for performance

        return $query->get()->map(function ($variant) {
            $stock = $variant->branchStock->first();
            $qty   = $stock ? (int) $stock->quantity : 0;

            return [
                'variant_id'      => $variant->id,
                'name'            => $variant->product->name . ($variant->name ? ' — ' . $variant->name : ''),
                'short_name'      => $variant->product->name,
                'sku'             => $variant->sku,
                'barcode'         => $variant->barcode,
                'price'           => (float) $variant->selling_price,
                'tax_rate'        => $variant->tax ? (float) $variant->tax->rate : 0,
                'tax_name'        => $variant->tax ? $variant->tax->name : null,
                'available_stock' => $qty,
                'reorder_level'   => (int) ($stock->reorder_level ?? $variant->reorder_level ?? 5),
                'category'        => $variant->product->category?->name ?? 'Uncategorized',
                'image'           => $variant->product->image
                    ? asset('storage/' . $variant->product->image)
                    : null,
                'stock_status'    => $qty <= 0 ? 'out' : ($qty <= ($stock->reorder_level ?? 5) ? 'low' : 'ok'),
            ];
        })->toArray();
    }
}
