<?php

namespace App\Http\Resources\Industry;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class IndustryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request)
    {

        return $this->collection->map(function ($industry) {
            return [
                'id' => $industry->id,
                'name' => $industry->name,
            ];
        });
    }
    /**
     * Disable the data wrapping for this collection.
     *
     * @return bool
     */
    public static function withoutWrapping()
    {
        return true;
    }
}
