<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LostFoundResource extends JsonResource
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
            'id' => (int)$this->id,

            'status' => (string)$this->status,
            'type' => (string)$this->type,
            'code' => (string)$this->code,

            'item_name' => (string)$this->item_name,
            'image_urls' => json_decode($this->image_urls,true) ?? [],
            'location' => (string)$this->location,
            'date_time' => (string)$this->date_time,
            'finder_name' => (string)$this->finder_name,

            'created_at' => (string)$this->created_at
        ];
    }
}