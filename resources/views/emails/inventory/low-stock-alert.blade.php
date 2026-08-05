<x-mail::message>
# Low Stock Alert ⚠️

Dear **{{ $company->name }}**,

The following {{ $lowStockItems->count() }} item(s) are at or below their reorder level and may need restocking:

<x-mail::table>
| Product | Branch | Available | Reorder Level |
| :--- | :--- | ---: | ---: |
@foreach ($lowStockItems as $stock)
| {{ $stock->variant?->product?->name ?? 'Unknown' }}{{ $stock->variant?->name ? ' (' . $stock->variant->name . ')' : '' }} | {{ $stock->branch?->name ?? 'Central Warehouse' }} | {{ $stock->quantity }} | {{ $stock->reorder_level }} |
@endforeach
</x-mail::table>

<x-mail::button :url="route('company.inventory.low-stock')">
View Low Stock Report
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
