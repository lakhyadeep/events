<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoShortResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'platform' => $this->platform?->value ?? $this->platform,
            'video_url' => $this->video_url,
            'embed_url' => $this->embed_url,
            'video_id' => $this->video_id,
            'caption' => $this->caption,
            'thumbnail_image_url' => $this->thumbnail_image_url,
            'sort_order' => $this->sort_order,
        ];
    }
}
