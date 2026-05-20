<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

#[OA\Tag(name: 'Auth', description: 'Autenticación JWT')]
class AuthController extends Controller
{
    public function __construct(protected ActivityLogService $activityLog) {}

    #[OA\Post(path: '/api/auth/login', summary: 'Iniciar sesión')]
    public function login(LoginRequest $request): JsonResponse
    {
        if (! $token = auth('api')->attempt($request->validated())) {
            return $this->error('Credenciales inválidas', 401);
        }

        $user = auth('api')->user();
        if (! $user->is_active) {
            auth('api')->logout();

            return $this->error('Usuario desactivado', 403);
        }

        $this->activityLog->log('login', 'auth', $user);

        return $this->success([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user' => $this->formatUser($user),
        ], 'Sesión iniciada');
    }

    public function me(): JsonResponse
    {
        return $this->success($this->formatUser(auth('api')->user()));
    }

    public function refresh(): JsonResponse
    {
        return $this->success([
            'access_token' => JWTAuth::refresh(),
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
        ]);
    }

    public function logout(): JsonResponse
    {
        $this->activityLog->log('logout', 'auth', auth('api')->user());
        auth('api')->logout();

        return $this->success(null, 'Sesión cerrada');
    }

    protected function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar,
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ];
    }
}
