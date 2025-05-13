<?php

namespace App\Http\Resources\Company;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'registration_number' => $this->registration_number,
            'founding_year' => $this->founding_year,
            'industry' => $this->whenLoaded('industry', function () {
                return [
                    'id' => $this->industry->id,
                    'name' => $this->industry->name,
                ];
            }),
            'file' => $this->whenLoaded('file', function () {
                return [
                    'id' => $this->file->id,
                    'name' => $this->file->name,
                    'path' => $this->file->path,
                ];
            }),
            'time_zone' => $this->time_zone,
            'address' => $this->address,
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