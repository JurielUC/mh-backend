<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
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
            'user_id' => (int)$this->user_id,
            'facility_id' => (int)$this->facility_id,

            'status' => (string)$this->status,
            'type' => (string)$this->type,
            'code' => (string)$this->code,

            'facility' => $this->facility,
            'user' => $this->user,

            'description' => (string)$this->description,

            'start_time' => (string)$this->start_time,
            'end_time' => (string)$this->end_time,
            'date' => (string)$this->date,

            'created_at' => (string)$this->created_at
        ];
    }
}