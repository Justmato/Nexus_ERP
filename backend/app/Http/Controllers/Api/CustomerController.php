<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Traits\Filterable;
use App\Models\Customer;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    use Filterable;

    protected array $sortable = ['name', 'code', 'created_at'];

    public function __construct(protected ActivityLogService $activityLog) {}

    public function index(Request $request): JsonResponse
    {
        $query = Customer::query();
        $this->applyFilters($query, $request, ['name', 'code', 'email', 'tax_id']);

        return $this->success($query->paginate($request->integer('per_page', 15)));
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = Customer::create($request->validated());
        $this->activityLog->log('created', 'customers', $customer);

        return $this->success($customer, 'Cliente creado', 201);
    }

    public function show(Customer $customer): JsonResponse
    {
        return $this->success($customer);
    }

    public function update(StoreCustomerRequest $request, Customer $customer): JsonResponse
    {
        $customer->update($request->validated());
        $this->activityLog->log('updated', 'customers', $customer);

        return $this->success($customer, 'Cliente actualizado');
    }

    public function destroy(Customer $customer): JsonResponse
    {
        $customer->delete();
        $this->activityLog->log('deleted', 'customers', $customer);

        return $this->success(null, 'Cliente eliminado');
    }
}
