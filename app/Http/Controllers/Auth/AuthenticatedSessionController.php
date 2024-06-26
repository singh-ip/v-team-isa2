<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\UserService;
use App\Traits\ActivityLog;
use App\Traits\HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DateTime;

class AuthenticatedSessionController extends Controller
{
    use ActivityLog;
    use HttpResponse;

    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();
        $user = Auth::user();
        $expiration = (new DateTime())->modify('+' . config('sanctum.expiration') . 'minutes');
        $token = $user->createToken(request()->userAgent(), expiresAt: $expiration)->plainTextToken;
        $this->activity('Log in', $user, $user);

        return $this->response(
            array_merge(Auth::user()->toArray(), ['token' => $token]),
            __('messages.user.logged_in')
        );
    }

    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();
        $service = new UserService();
        $service->expireTokens($user);
        $this->activity('Log out', $user, $user);

        return $this->response(['token' => ''], __('messages.user.logged_out'));
    }
}
