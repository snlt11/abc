<?php

namespace App\Http\Controllers;

use App\Http\Requests\Industry\StoreIndustryRequest;
use App\Http\Requests\Industry\UpdateIndustryRequest;
use App\Http\Resources\Industry\IndustryCollection;
use App\Http\Resources\Industry\IndustryResource;
use App\Models\Industry;
use Illuminate\Support\Facades\DB;


class IndustryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $industries = Industry::all();
        return new IndustryCollection($industries);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIndustryRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $industries = Industry::create($validated);

            DB::commit();

            return (new IndustryResource($industries))->response()->setStatusCode(201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Industry $industry)
    {
        return new IndustryResource($industry);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIndustryRequest $request, Industry $industry)
    {
        $validated = $request->validated();
        $industry->update($validated);
        return new IndustryResource($industry);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Industry $industry)
    {
        $industry->delete();
        return response()->json([
            'success' => true,
            'message' => 'Industry deleted successfully'
        ]);
    }
}
