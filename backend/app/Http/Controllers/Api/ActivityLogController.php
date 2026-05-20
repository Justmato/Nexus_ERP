<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\Filterable;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    use Filterable;

    public function index(Request $request): JsonResponse
    {
        $query = ActivityLog::with('user')->orderByDesc('created_at');

        if ($module = $request->get('module')) {
            $query->where('module', $module);
        }

        if ($userId = $request->get('user_id')) {
            $query->where('user_id', $userId);
        }

        return $this->success($query->paginate($request->integer('per_page', 20)));
    }
}
