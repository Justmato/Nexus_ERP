<?php

namespace App\Http\Controllers\Api;

use App\Exports\SalesReportExport;
use App\Http\Controllers\Controller;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function sales(Request $request): JsonResponse
    {
        $from = $request->get('date_from', Carbon::now()->startOfMonth()->toDateString());
        $to = $request->get('date_to', Carbon::now()->toDateString());

        $sales = Sale::with(['customer', 'user'])
            ->where('status', '!=', 'cancelled')
            ->whereBetween('sale_date', [$from, $to])
            ->orderByDesc('sale_date')
            ->get();

        return $this->success([
            'period' => ['from' => $from, 'to' => $to],
            'total' => $sales->sum('total'),
            'count' => $sales->count(),
            'sales' => $sales,
        ]);
    }

    public function salesExcel(Request $request): BinaryFileResponse
    {
        $from = $request->get('date_from', Carbon::now()->startOfMonth()->toDateString());
        $to = $request->get('date_to', Carbon::now()->toDateString());

        return Excel::download(
            new SalesReportExport($from, $to),
            "ventas_{$from}_{$to}.xlsx"
        );
    }

    public function salesPdf(Request $request)
    {
        $from = $request->get('date_from', Carbon::now()->startOfMonth()->toDateString());
        $to = $request->get('date_to', Carbon::now()->toDateString());

        $sales = Sale::with('customer')
            ->where('status', '!=', 'cancelled')
            ->whereBetween('sale_date', [$from, $to])
            ->orderByDesc('sale_date')
            ->get();

        $pdf = Pdf::loadView('reports.sales', [
            'sales' => $sales,
            'from' => $from,
            'to' => $to,
            'total' => $sales->sum('total'),
        ]);

        return $pdf->download("ventas_{$from}_{$to}.pdf");
    }
}
