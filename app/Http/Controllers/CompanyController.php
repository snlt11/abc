<?php

namespace App\Http\Controllers;

use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Models\Company;
use App\Models\File;
use App\Models\Industry;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::with(['industry', 'location', 'file'])->get();
        return response()->json($companies);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'registration_number' => 'required|string|unique:companies,registration_number',
                'founding_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                'industry_id' => 'required|uuid|exists:industries,id',
                'file_id' => 'nullable|uuid|exists:files,id',
                'time_zone' => 'required|timezone',
                'location_id' => 'required|uuid|exists:locations,id',
            ]);

            DB::beginTransaction();

            $company = Company::create($validated);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Company created successfully',
                'data' => $company->load(['industry', 'location', 'file'])
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create company',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function show(Company $company)
    {
        return response()->json($company->load(['industry', 'location', 'file']));
    }

    public function update(Request $request, Company $company)
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'registration_number' => 'sometimes|required|string|unique:companies,registration_number,' . $company->id,
                'founding_year' => 'sometimes|required|integer|min:1900|max:' . (date('Y') + 1),
                'industry_id' => 'sometimes|required|uuid|exists:industries,id',
                'file_id' => 'nullable|uuid|exists:files,id',
                'time_zone' => 'sometimes|required|timezone',
                'location_id' => 'sometimes|required|uuid|exists:locations,id',
            ]);

            DB::beginTransaction();

            $company->update($validated);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Company updated successfully',
                'data' => $company->load(['industry', 'location', 'file'])
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update company',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return response()->json(['message' => 'Company deleted successfully']);
    }
}