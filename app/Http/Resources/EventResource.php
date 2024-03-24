<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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

            'title' => (string)$this->title,
            'description' => (string)$this->description,
            
            'date' => (string)$this->date,
            'time' => (string)$this->time,

            'user' => $this->user ?? '',

            'created_at' => (string)$this->created_at
        ];
    }
}