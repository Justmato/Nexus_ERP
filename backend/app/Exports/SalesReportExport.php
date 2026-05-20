<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesReportExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        protected string $from,
        protected string $to
    ) {}

    public function collection()
    {
        return Sale::with('customer')
            ->where('status', '!=', 'cancelled')
            ->whereBetween('sale_date', [$this->from, $this->to])
            ->orderByDesc('sale_date')
            ->get();
    }

    public function headings(): array
    {
        return ['Folio', 'Fecha', 'Cliente', 'Subtotal', 'IVA', 'Total', 'Estado'];
    }

    public function map($sale): array
    {
        return [
            $sale->folio,
            $sale->sale_date->format('Y-m-d'),
            $sale->customer?->name ?? 'Mostrador',
            $sale->subtotal,
            $sale->tax,
            $sale->total,
            $sale->status,
        ];
    }
}
