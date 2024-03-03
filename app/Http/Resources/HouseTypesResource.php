<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

use App\Models\HouseType;

class HouseTypesResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->collection->transform(function (HouseType $house_type) {
            return (new HouseTypeResource($house_type));
        });

        return [
            'version' => '1.0.0',
            'author_url' => env('APP_URL'),
            'data' => $this->collection
        ];
    }
}