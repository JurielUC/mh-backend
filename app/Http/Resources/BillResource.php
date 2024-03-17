<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BillResource extends JsonResource
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

            'bill' => (string)$this->bill,

            'name' => (string)$this->name,
            'description' => (string)$this->description,

            'price' => (string)$this->price,
            'date' => (string)$this->date,
            'from_date' => (string)$this->from_date,
            'to_date' => (string)$this->to_date,
            'due' => (string)$this->due,

            'admin' => $this->admin ?? '',
            'user' => $this->user ?? '',

            'user_id' => $this->user->id ?? '',

            'created_at' => (string)$this->created_at
        ];
    }
}