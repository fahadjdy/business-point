<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\EmergencyContactService;
use Illuminate\Http\Request;

class EmergencyContactController extends BaseController
{
    protected $service;

    public function __construct(EmergencyContactService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return $this->success(
            $this->service->getPaginated(50, $request->all()),
            'Emergency contacts retrieved'
        );
    }

    public function store(\App\Http\Requests\EmergencyContact\StoreEmergencyContactRequest $request)
    {
        return $this->success(
            $this->service->create($request->validated()),
            'Emergency contact created',
            201
        );
    }

    public function update(\App\Http\Requests\EmergencyContact\UpdateEmergencyContactRequest $request, $id)
    {
        return $this->success(
            $this->service->update($id, $request->validated()),
            'Emergency contact updated'
        );
    }

    public function destroy($id)
    {
        $this->service->delete($id, request('reason'));
        return $this->success(null, 'Emergency contact deleted');
    }

    public function show($id)
    {
        return $this->success(
            $this->service->findById($id),
            'Emergency contact retrieved'
        );
    }
}
