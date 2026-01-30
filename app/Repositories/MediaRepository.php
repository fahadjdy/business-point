<?php

namespace App\Repositories;

use App\Models\Media;

class MediaRepository extends BaseRepository
{
    public function __construct(Media $media)
    {
        parent::__construct($media);
    }

    /**
     * Get primary media for a model.
     */
    public function getPrimaryForModel(string $modelType, int $modelId)
    {
        return $this->model->where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->where('is_primary', true)
            ->first();
    }

    /**
     * Get all media for a model.
     */
    public function getForModel(string $modelType, int $modelId)
    {
        return $this->model->where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->get();
    }
}
