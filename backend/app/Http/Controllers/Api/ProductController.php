<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Traits\Filterable;
use App\Models\Product;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Products')]
class ProductController extends Controller
{
    use Filterable;

    protected array $sortable = ['name', 'sku', 'stock', 'sale_price', 'created_at'];

    public function __construct(protected ActivityLogService $activityLog) {}

    public function index(Request $request): JsonResponse
    {
        $query = Product::with('category');
        $this->applyFilters($query, $request, ['name', 'sku', 'barcode']);

        if ($request->boolean('low_stock')) {
            $query->where('track_stock', true)->whereColumn('stock', '<=', 'min_stock');
        }

        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }

        return $this->success($query->paginate($request->integer('per_page', 15)));
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());
        $this->activityLog->log('created', 'products', $product);

        return $this->success($product->load('category'), 'Producto creado', 201);
    }

    public function show(Product $product): JsonResponse
    {
        return $this->success($product->load('category'));
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());
        $this->activityLog->log('updated', 'products', $product);

        return $this->success($product->fresh('category'), 'Producto actualizado');
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        $this->activityLog->log('deleted', 'products', $product);

        return $this->success(null, 'Producto eliminado');
    }
}
