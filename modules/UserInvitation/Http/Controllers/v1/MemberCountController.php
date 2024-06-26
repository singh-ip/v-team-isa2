<?php

namespace Modules\UserInvitation\Http\Controllers\v1;

use App\Models\User;
use App\Traits\HttpResponse;
use Illuminate\Http\JsonResponse;
use Modules\UserInvitation\Models\UserInvitation;
use App\Http\Controllers\Controller;

class MemberCountController extends Controller
{
    use HttpResponse;

    public function get(): JsonResponse
    {
        return $this->response([
            'active' => User::query()->count(),
            'pending' => UserInvitation::query()->count(),
        ]);
    }

}
