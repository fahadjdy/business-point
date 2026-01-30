<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends BaseController
{
    protected $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->all();
        $perPage = $request->get('per_page', 20); // Default to 20 for frontend
        
        if ($request->is('api/*')) {
            $filters['for_public'] = true;
            $filters['is_active'] = 1; // Only active for public
        }
        // Admin routes don't filter by is_active, they see all notifications

        return $this->success(
            $this->service->getPaginated($perPage, $filters),
            'Notifications retrieved'
        );
    }

    public function store(\App\Http\Requests\Notification\StoreNotificationRequest $request)
    {
        return $this->success(
            $this->service->create($request->validated()),
            'Notification created',
            201
        );
    }

    public function update(\App\Http\Requests\Notification\UpdateNotificationRequest $request, $id)
    {
        return $this->success(
            $this->service->update($id, $request->validated()),
            'Notification updated'
        );
    }

    public function destroy($id)
    {
        $this->service->delete($id, request('reason'));
        return $this->success(null, 'Notification deleted');
    }

    public function show($id)
    {
        return $this->success(
            $this->service->findById($id),
            'Notification retrieved'
        );
    }
}
