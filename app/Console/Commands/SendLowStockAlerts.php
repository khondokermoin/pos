<?php

namespace App\Console\Commands;

use App\Mail\LowStockAlertMail;
use App\Models\Stock;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendLowStockAlerts extends Command
{
    protected $signature = 'stock:check-low';

    protected $description = 'Email each company a low-stock digest for variants at or below their reorder level.';

    public function handle(): int
    {
        $lowStockByCompany = Stock::with(['variant.product.company', 'branch'])
            ->whereHas('variant.product', fn ($q) => $q->whereNotNull('company_id'))
            ->whereColumn('quantity', '<=', 'reorder_level')
            ->get()
            ->groupBy(fn ($stock) => $stock->variant->product->company_id);

        $sent = 0;
        foreach ($lowStockByCompany as $items) {
            $company = $items->first()->variant->product->company;

            if (! $company || ! $company->email) {
                continue;
            }

            Mail::to($company->email)->queue(new LowStockAlertMail($company, $items));
            $sent++;
            $this->info("Low stock alert queued: {$company->name} ({$items->count()} item(s))");
        }

        $this->info("Processed {$lowStockByCompany->count()} compan(y/ies) with low stock, {$sent} alert(s) queued.");

        return self::SUCCESS;
    }
}
