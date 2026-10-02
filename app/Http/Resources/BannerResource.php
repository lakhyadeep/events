<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'image_desktop_url' => $this->desktop_image_url,
            'image_mobile_url' => $this->mobile_image_url,
            'cta_link' => $this->cta_link,
            'sort_order' => $this->sort_order,
        ];
    }
}
