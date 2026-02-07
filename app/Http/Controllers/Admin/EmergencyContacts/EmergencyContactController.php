<?php

namespace App\Http\Controllers\Admin\EmergencyContacts;

use App\Http\Controllers\Controller;
use App\Services\EmergencyContactService;
use App\Http\Requests\EmergencyContact\StoreEmergencyContactRequest;
use App\Http\Requests\EmergencyContact\UpdateEmergencyContactRequest;
use Illuminate\Http\Request;

class EmergencyContactController extends Controller
{
    protected $service;

    public function __construct(EmergencyContactService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->getPaginated(50, $request->all())
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->findById($id)
        ]);
    }

    public function store(StoreEmergencyContactRequest $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Emergency contact added successfully',
            'data' => $this->service->create($request->validated())
        ]);
    }

    public function update(UpdateEmergencyContactRequest $request, $id)
    {
        return response()->json([
            'success' => true,
            'message' => 'Emergency contact updated successfully',
            'data' => $this->service->update($id, $request->validated())
        ]);
    }

    public function destroy($id)
    {
        $this->service->delete($id, request('reason'));
        return response()->json([
            'success' => true,
            'message' => 'Emergency contact deleted successfully'
        ]);
    }
}
