<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\UserSetting\UpdateUserSettingRequest;
use App\Services\UserSettingService;
use Illuminate\Http\Request;

class UserSettingController extends BaseController
{
    protected $service;

    public function __construct(UserSettingService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return $this->success(
            $this->service->getSettings(auth()->id()),
            'User settings retrieved successfully'
        );
    }

    public function update(UpdateUserSettingRequest $request)
    {
        return $this->success(
            $this->service->updateSettings(auth()->id(), $request->validated()['settings']),
            'User settings updated successfully'
        );
    }
}
