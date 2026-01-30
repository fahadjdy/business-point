<?php

namespace App\Repositories;

use App\Models\EmergencyContact;

class EmergencyContactRepository extends BaseRepository
{
    public function __construct(EmergencyContact $contact)
    {
        parent::__construct($contact);
    }

    /**
     * Get active emergency contacts.
     */
    public function getActive()
    {
        return $this->model->where('is_active', true)->get();
    }
}
