<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\ContactBookService;
use Illuminate\Http\Request;
use App\Http\Requests\ContactBook\StoreContactBookRequest;
use App\Http\Requests\ContactBook\UpdateContactBookRequest;

class ContactBookController extends BaseController
{
    protected $service;

    public function __construct(ContactBookService $service)
    {
        $this->service = $service;
    }

    /**
     * List/Search contacts with enhanced filtering and pagination.
     */
    public function index(Request $request)
    {
        $params = $request->validate([
            'keyword' => 'nullable|string|max:255',
            'type' => 'nullable|string|in:person,business,service',
            'tag' => 'nullable|string',
            'tag_id' => 'nullable|integer|exists:tags,id',
            'is_active' => 'nullable|boolean',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
            'sort_by' => 'nullable|string|in:name,created_at,sort_order',
            'sort_order' => 'nullable|string|in:asc,desc',
        ]);

        // Set default pagination
        $perPage = $params['per_page'] ?? 20;

        return $this->success(
            $this->service->getPaginatedWithFilters($perPage, $params),
            'Contacts retrieved'
        );
    }

    /**
     * Show contact profile with audit logging.
     */
    public function show($id)
    {
        $contact = $this->service->getContactWithRelations((int)$id);
        if (!$contact) {
            return $this->error('Contact not found', [], 404);
        }

        return $this->success(
            $contact,
            'Contact retrieved'
        );
    }

    /**
     * Store new contact with tag associations.
     */
    public function store(StoreContactBookRequest $request)
    {
        $data = $request->validated();
        
        return $this->success(
            $this->service->createWithTags($data),
            'Contact added successfully',
            201
        );
    }

    /**
     * Update contact with tag management.
     */
    public function update(UpdateContactBookRequest $request, $id)
    {
        $data = $request->validated();
        
        return $this->success(
            $this->service->updateWithTags($id, $data),
            'Contact updated successfully'
        );
    }

    /**
     * Delete contact.
     */
    public function destroy($id)
    {
        $this->service->delete($id, request('reason'));
        return $this->success(null, 'Contact deleted successfully');
    }

    /**
     * Get contacts by tag.
     */
    public function getByTag(Request $request)
    {
        $request->validate([
            'tag_id' => 'required|integer|exists:tags,id',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $perPage = $request->input('per_page', 20);
        
        return $this->success(
            $this->service->getContactsByTag($request->tag_id, $perPage),
            'Contacts by tag retrieved'
        );
    }

    /**
     * Get contacts by tag slug.
     */
    public function getByTagSlug(Request $request, string $tagSlug)
    {
        $request->validate([
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $perPage = $request->input('per_page', 20);
        
        return $this->success(
            $this->service->getContactsByTagSlug($tagSlug, $perPage),
            'Contacts by tag retrieved'
        );
    }

    /**
     * Search contacts across multiple fields.
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:255',
            'type' => 'nullable|string|in:person,business,service',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $perPage = $request->input('per_page', 20);
        
        return $this->success(
            $this->service->searchContacts($request->q, $request->type, $perPage),
            'Search results retrieved'
        );
    }

    /**
     * Get available tags for contacts.
     */
    public function getTags(Request $request)
    {
        $request->validate([
            'category' => 'nullable|string|in:profession,skill,business,service',
        ]);

        return $this->success(
            $this->service->getAvailableTags($request->category),
            'Tags retrieved'
        );
    }

    /**
     * Bulk update contact status.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'contact_ids' => 'required|array|min:1',
            'contact_ids.*' => 'integer|exists:contact_books,id',
            'is_active' => 'required|boolean',
        ]);

        $updated = $this->service->bulkUpdateStatus($request->contact_ids, $request->is_active);
        
        return $this->success(
            ['updated_count' => $updated],
            'Contacts status updated successfully'
        );
    }

    /**
     * Get contact statistics.
     */
    public function getStats()
    {
        return $this->success(
            $this->service->getContactStats(),
            'Contact statistics retrieved'
        );
    }

    /**
     * Get audit logs with enhanced filtering.
     */
    public function auditLogs(Request $request)
    {
        $params = $request->validate([
            'contact_id' => 'nullable|integer|exists:contact_books,id',
            'action' => 'nullable|string',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $perPage = $params['per_page'] ?? 15;
        
        return $this->success(
            $this->service->getAuditLogs($perPage, $params),
            'Audit logs retrieved'
        );
    }

    /**
     * Export contacts (CSV and Excel only).
     */
    public function export(Request $request)
    {
        $params = $request->validate([
            'format' => 'required|string|in:csv,excel',
            'keyword' => 'nullable|string|max:255',
            'type' => 'nullable|string|in:person,business,service',
            'tag_id' => 'nullable|integer|exists:tags,id',
            'is_active' => 'nullable|boolean',
        ]);

        // Get filtered contacts for export
        $contacts = $this->service->getContactsForExport($params);
        
        // Initialize export service
        $exportService = new \App\Services\ExportService();
        
        // Export based on format
        switch ($params['format']) {
            case 'csv':
                return $exportService->exportToCsv($contacts);
            case 'excel':
                return $exportService->exportToExcel($contacts);
            default:
                return $this->error('Invalid export format', [], 400);
        }
    }
}