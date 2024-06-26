<?php

namespace App\Http\Controllers\v1;

use App\Services\FileService;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserIdRequest;
use App\Http\Requests\UserListRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\SearchService;
use App\Traits\ActivityLog;
use App\Traits\HttpResponse;
use App\Traits\SearchableIndex;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\UpdateUserProfileRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    use ActivityLog;
    use HttpResponse;
    use SearchableIndex;

    public function __construct(
        private readonly string $searchClass = User::class,
        private readonly string $searchResourceClass = UserResource::class
    ) {
    }

    public function index(
        UserListRequest $request,
        SearchService $service
    ): AnonymousResourceCollection|JsonResponse {
        return $this->searchIndex($request, $service);
    }

    public function get(UserIdRequest $request): JsonResponse
    {
        $user = User::find($request->validated('id'));
        if ($user) {
            return $this->resourceResponse(new UserResource($user));
        }
        return $this->response(null, '', Response::HTTP_NOT_FOUND);
    }

    public function updateUser(UpdateUserProfileRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = User::find($data['id']);
        if ($user) {
            if (!empty($data['role'])) {
                $user->roles()->detach();
                $user->assignRole($data['role']);
                unset($data['role']);
            }
            unset($data['id']);
            $user->update($data);
            return $this->response($request->validated(), '', Response::HTTP_OK);
        }
        return $this->response(null, '', Response::HTTP_NOT_FOUND);
    }

    public function destroy(UserIdRequest $request): JsonResponse
    {
        $id = $request->validated('id');

        if (auth()->user()->id == $id) {
            return $this->response(
                null,
                __('messages.resource.cannot_delete_self'),
                Response::HTTP_FORBIDDEN
            );
        }
        $user = User::find($id);
        if ($user) {
            $user->roles()->detach();
            $user->permissions()->detach();
            $image = config('constants.user.profile_image.image_params.path') . $user->image_filename;
            $thumbnail = config('constants.user.profile_image.thumbnail_params.path') . $user->image_filename;
            (new FileService())->deleteFiles([
                $image,
                $thumbnail

            ]);
            $user->delete();
            return $this->response(null, __('messages.resource.deleted'));
        }
        return $this->response(null, __('messages.resource.not_found'), Response::HTTP_NOT_FOUND);
    }
}
