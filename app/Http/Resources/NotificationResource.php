<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = json_decode($this->data, true);

        $url = '';

        if (isset($data['url'])) {
			$message = $data['url'];
		}

        $customer_url = '';
		if (isset($data['customer_url'])) {
			$message = $data['customer_url'];
		}

		$admin_url = '';
		if (isset($data['admin_url'])) {
			$message = $data['admin_url'];
		}		

		$subject = '';

        $message = '';
		if (isset($data['message'])) {
			$message = $data['message'];
		}

        return [
            'id' => (int)$this->id,
			
			'url' => (string)$url,
			
			'subject' => (string)$subject,
			'message' => (string)$message,
			
			'read_at' => (string)$this->read_at,
			
			'created_at' => (string)$this->created_at
        ];
    }
}