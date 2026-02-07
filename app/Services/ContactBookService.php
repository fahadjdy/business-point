<?php

namespace App\Services;

use App\Repositories\ContactBookRepository;
use App\Repositories\ContactBookAuditLogRepository;
use App\Repositories\TagRepository;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;

/**
 * @property \App\Repositories\ContactBookRepository $repository
 */
class ContactBookService extends BaseService
{
    protected $auditLogRepository;
    protected $mediaService;
    protected $tagRepository;

    public function __construct(
        ContactBookRepository $repository,
        ContactBookAuditLogRepository $auditLogRepository,
        MediaService $mediaService,
        TagRepository $tagRepository
    ) {
        parent::__construct($repository);
        $this->auditLogRepository = $auditLogRepository;
        $this->mediaService = $mediaService;
        $this->tagRepository = $tagRepository;
    }

    /**
     * Create contact with tag associations
     */
    /**
     * Create contact with tag associations
     */
    public function createWithTags(array $data)
    {
        return DB::transaction(function () use ($data) {
            $image = $data['image'] ?? null;
            $tagIds = $data['tag_ids'] ?? [];
            $contactNumbers = $data['contact_numbers'] ?? [];
            
            // Auto-fill legacy phone field with the first primary number, or first number
            if (empty($data['phone']) && !empty($contactNumbers)) {
                $primary = $contactNumbers[0];
                $data['phone'] = $primary['number'];
                $data['type'] = $primary['type'];
            }
            
            unset($data['image'], $data['tag_ids'], $data['contact_numbers']);

            $contact = $this->repository->create($data);

            // Attach tags
            if (!empty($tagIds)) {
                $contact->syncTags($tagIds);
            }

            // Save contact numbers
            if (!empty($contactNumbers)) {
                foreach ($contactNumbers as $cn) {
                    $contact->contactNumbers()->create([
                        'number' => $cn['number'],
                        'type' => $cn['type']
                    ]);
                }
            }

            // Handle image upload
            if ($image) {
                $this->mediaService->upload($image, $contact, 'default', true, true);
            }

            return $contact->load('tags', 'media', 'contactNumbers');
        });
    }

    /**
     * Update contact with tag management
     */
    public function updateWithTags(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $image = $data['image'] ?? null;
            $tagIds = $data['tag_ids'] ?? null;
            $contactNumbers = $data['contact_numbers'] ?? null;
            
            // Auto-fill legacy phone field
            if (isset($contactNumbers) && !empty($contactNumbers)) {
                 $primary = $contactNumbers[0];
                 $data['phone'] = $primary['number'];
                 $data['type'] = $primary['type'];
            }
            
            unset($data['image'], $data['tag_ids'], $data['contact_numbers']);

            $contact = $this->repository->update($id, $data);

            // Update tags if provided
            if ($tagIds !== null) {
                $contact->syncTags($tagIds);
            }

            // Update contact numbers (Delete all and recreate for simplicity)
            if ($contactNumbers !== null) {
                $contact->contactNumbers()->delete();
                foreach ($contactNumbers as $cn) {
                    $contact->contactNumbers()->create([
                        'number' => $cn['number'],
                        'type' => $cn['type']
                    ]);
                }
            }

            // Handle image upload
            if ($image) {
                $this->mediaService->upload($image, $contact, 'default', true, true);
            }

            return $contact->load('tags', 'media', 'contactNumbers');
        });
    }

    /**
     * Get paginated contacts with enhanced filtering
     */
    public function getPaginatedWithFilters(int $perPage = 20, array $params = [])
    {
        return $this->repository->getPaginatedWithFilters($perPage, $params);
    }

    /**
     * Get contact with all relations
     */
    public function getContactWithRelations(int $id)
    {
        $contact = $this->repository->findWithRelations($id, ['tags', 'media', 'contactNumbers']);
        if ($contact) {
            $this->logAction('view', $contact->id, null);
        }
        return $contact;
    }

    /**
     * Get contacts by tag ID
     */
    public function getContactsByTag(int $tagId, int $perPage = 20)
    {
        return $this->repository->getByTag($tagId, $perPage);
    }

    /**
     * Get contacts by tag slug
     */
    public function getContactsByTagSlug(string $tagSlug, int $perPage = 20)
    {
        return $this->repository->getByTagSlug($tagSlug, $perPage);
    }

    /**
     * Search contacts across multiple fields
     */
    public function searchContacts(string $query, ?string $type = null, int $perPage = 20)
    {
        $this->logAction('search', null, $query);
        return $this->repository->searchPaginated($query, $type, $perPage);
    }

    /**
     * Get available tags for contacts
     */
    public function getAvailableTags(?string $category = null)
    {
        $query = $this->tagRepository->query()->active();
        
        if ($category) {
            $query->byCategory($category);
        }
        
        return $query->ordered()->get();
    }

    /**
     * Bulk update contact status
     */
    public function bulkUpdateStatus(array $contactIds, bool $isActive)
    {
        return $this->repository->bulkUpdateStatus($contactIds, $isActive);
    }

    /**
     * Get contact statistics
     */
    public function getContactStats()
    {
        return [
            'total_contacts' => $this->repository->count(),
            'active_contacts' => $this->repository->countActive(),
            'contacts_by_type' => $this->repository->getCountByType(),
            'contacts_by_tag' => $this->repository->getCountByTag(),
            'recent_contacts' => $this->repository->getRecent(5),
        ];
    }

    /**
     * Get contacts for export with filtering
     */
    public function getContactsForExport(array $params = [])
    {
        return $this->repository->getContactsForExport($params);
    }

    /**
     * Legacy methods for backward compatibility
     */
    public function create(array $data)
    {
        return $this->createWithTags($data);
    }

    public function update(int $id, array $data)
    {
        return $this->updateWithTags($id, $data);
    }

    /**
     * Search contacts and log the search action.
     */
    public function search(string $keyword, ?string $type = null)
    {
        $this->logAction('search', null, $keyword);
        return $this->repository->search($keyword, $type);
    }

    /**
     * Get a contact and log the view action.
     */
    public function getContact(int $id)
    {
        $contact = $this->findById($id);
        if ($contact) {
            $this->logAction('view', $contact->id, null);
        }
        return $contact;
    }

    /**
     * Log an action to the contact book audit log.
     */
    protected function logAction(string $action, ?int $contactId = null, ?string $keyword = null)
    {
        $this->auditLogRepository->create([
            'user_id' => auth()->id(),
            'contact_book_id' => $contactId,
            'action' => $action,
            'search_keyword' => $keyword,
            'ip_address' => Request::ip(),
        ]);
    }

    /**
     * Get paginated audit logs with enhanced filtering.
     */
    public function getAuditLogs(int $perPage = 15, array $params = [])
    {
        return $this->auditLogRepository->getPaginatedWithFilters($perPage, $params);
    }
}