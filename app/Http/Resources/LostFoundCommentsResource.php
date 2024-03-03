<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

use App\Models\LostFoundComment;

class LostFoundCommentsResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->collection->transform(function (LostFoundComment $lost_found_comment) {
            return (new LostFoundCommentResource($lost_found_comment));
        });

        return [
            'version' => '1.0.0',
            'author_url' => env('APP_URL'),
            'data' => $this->collection
        ];
    }
}