<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactNumber extends Model
{
    protected $fillable = [
        'contact_book_id',
        'number',
        'type',
    ];

    public function contactBook(): BelongsTo
    {
        return $this->belongsTo(ContactBook::class);
    }
}
