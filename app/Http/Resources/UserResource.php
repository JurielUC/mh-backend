<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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

            'first_name' => (string)$this->first_name,
            'middle_name' => (string)$this->middle_name,
            'last_name' => (string)$this->last_name,
            'suffix' => (string)$this->suffix,
            'gender' => (string)$this->gender,

            'email' => (string)$this->email,
            'phone' => (string)$this->phone,

            'house_no' => (string)$this->house_no,
            'street' => (string)$this->street,
            'house_type' => (string)$this->house_type,
            'house_type_id' => (int)$this->house_type_id,

            'role' => (string)$this->role,
            'image' => (string)$this->image,
            
            'email_verified_at' => (string)$this->email_verified_at,
            'token'=>(string)$this->remember_token,

            'created_at' => (string)$this->created_at
        ];
    }
}