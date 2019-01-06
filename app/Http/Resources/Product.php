<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Product extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'image_id' => $this->image_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => (int)$this->price,
            'created_at' => (string)$this->created_at
        ];
    }
}
