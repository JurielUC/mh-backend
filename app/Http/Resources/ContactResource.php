<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
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

            'name' => (string)$this->name,
            'image' => (string)$this->image,

            'email' => (string)$this->email,
            'phone' => (string)$this->phone,
            'facebook' => (string)$this->facebook,

            'created_at' => (string)$this->created_at
        ];
    }
}