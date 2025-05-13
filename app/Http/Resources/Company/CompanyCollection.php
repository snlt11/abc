<?php

namespace App\Http\Resources\Company;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CompanyCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($company) {
            return [
                'id' => $company->id,
                'name' => $company->name,
                'registration_number' => $company->registration_number,
                'founding_year' => $company->founding_year,
                'industry' => $company->whenLoaded('industry', function () use ($company) {
                    return [
                        'id' => $company->industry->id,
                        'name' => $company->industry->name,
                    ];
                }),
                'file' => $company->whenLoaded('file', function () use ($company) {
                    return [
                        'id' => $company->file->id,
                        'name' => $company->file->name,
                        'path' => $company->file->path,
                    ];
                }),
                'time_zone' => $company->time_zone,
                'address' => $company->address,
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