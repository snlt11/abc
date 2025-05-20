<?php

namespace App\Http\Resources\Industry;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class IndustryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            "id"=>$this->id,
            "name"=>$this->name
            ];
    }

     /**
     * Disable the data wrapping for this resource.
     *
     * @return bool
     */
    public static function withoutWrapping()
    {
        return true;
    }
}
