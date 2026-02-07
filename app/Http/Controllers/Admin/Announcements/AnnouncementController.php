<?php

namespace App\Http\Controllers\Admin\Announcements;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use App\Http\Requests\Notification\StoreNotificationRequest;
use App\Http\Requests\Notification\UpdateNotificationRequest;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    protected $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->getPaginated($request->query('per_page', 15), $request->all())
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->findById($id)
        ]);
    }

    public function store(StoreNotificationRequest $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Announcement created successfully',
            'data' => $this->service->create($request->validated())
        ]);
    }

    public function update(UpdateNotificationRequest $request, $id)
    {
        return response()->json([
            'success' => true,
            'message' => 'Announcement updated successfully',
            'data' => $this->service->update($id, $request->validated())
        ]);
    }

    public function destroy($id)
    {
        $this->service->delete($id, request('reason'));
        return response()->json([
            'success' => true,
            'message' => 'Announcement deleted successfully'
        ]);
    }
}
