<?php

namespace App\Http\Controllers\Admin\Community;

use App\Http\Controllers\Controller;
use App\Services\ContactBookService;
use App\Http\Requests\ContactBook\StoreContactBookRequest;
use App\Http\Requests\ContactBook\UpdateContactBookRequest;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    protected $service;

    public function __construct(ContactBookService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->getPaginatedWithFilters($request->query('per_page', 15), $request->all())
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->getContactWithRelations($id)
        ]);
    }

    public function store(StoreContactBookRequest $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Contact added successfully',
            'data' => $this->service->createWithTags($request->validated())
        ]);
    }

    public function update(UpdateContactBookRequest $request, $id)
    {
        return response()->json([
            'success' => true,
            'message' => 'Contact updated successfully',
            'data' => $this->service->updateWithTags($id, $request->validated())
        ]);
    }

    public function destroy($id)
    {
        $this->service->delete($id, request('reason'));
        return response()->json([
            'success' => true,
            'message' => 'Contact deleted successfully'
        ]);
    }

    public function auditLogs(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->getAuditLogs($request->query('per_page', 15), $request->all())
        ]);
    }

    // Proxy methods for additional functionality if needed
    public function getTags(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->getAvailableTags($request->category)
        ]);
    }
    public function getByTagSlug(Request $request, string $tagSlug)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->getContactsByTagSlug($tagSlug, $request->input('per_page', 20))
        ]);
    }

    public function getStats()
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->getContactStats()
        ]);
    }

    public function bulkUpdateStatus(Request $request)
    {
        $updated = $this->service->bulkUpdateStatus($request->contact_ids, $request->is_active);
        return response()->json([
            'success' => true,
            'message' => 'Contacts status updated successfully',
            'data' => ['updated_count' => $updated]
        ]);
    }

    /*
     * Note: Export functionality requires ExportService which is not injected here yet.
     * Assuming ExportService is available in App\Services namespace.
     */
    public function export(Request $request)
    {
         // Implementation deferred or needs Service injection.
         // For now, returning error or implementing basic logic if service allows.
         // Based on API controller, it uses App\Services\ExportService directly.
         
         $exportService = new \App\Services\ExportService();
         $contacts = $this->service->getContactsForExport($request->all());
         
         if ($request->format === 'csv') {
             return $exportService->exportToCsv($contacts);
         } elseif ($request->format === 'excel') {
             return $exportService->exportToExcel($contacts);
         }
         
         return response()->json(['success' => false, 'message' => 'Invalid format'], 400);
    }
}
