<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LostFoundCommentResource extends JsonResource
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
            'lost_found_id' => (int)$this->lost_found_id,

            'status' => (string)$this->status,
            'type' => (string)$this->type,
            'code' => (string)$this->code,

            'comment' => (string)$this->comment,

            'created_at' => (string)$this->created_at
        ];
    }
}