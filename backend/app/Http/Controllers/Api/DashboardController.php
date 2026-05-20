<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Dashboard')]
class DashboardController extends Controller
{
    public function __construct(protected DashboardService $dashboard) {}

    #[OA\Get(path: '/api/dashboard', summary: 'Métricas ejecutivas')]
    public function index(): JsonResponse
    {
        return $this->success([
            'metrics' => $this->dashboard->getMetrics(),
            'sales_chart' => $this->dashboard->getSalesChart(),
            'top_products' => $this->dashboard->getTopProducts(),
        ]);
    }
}
