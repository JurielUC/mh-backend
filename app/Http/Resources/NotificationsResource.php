<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

use App\Models\Notification;

class NotificationsResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        
        $this->collection->transform(function (Notification $notification) {
            return (new NotificationResource($notification));
        });

        return [
            'version' => '1.0.0',
            'author_url' => env('APP_URL'),
            'data' => $this->collection
        ];
    }
}