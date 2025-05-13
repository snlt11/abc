<?php

namespace App\Http\Controllers;

use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Http\Resources\Company\CompanyResource;
use App\Http\Resources\Company\CompanyCollection;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::with(['industry', 'file'])->get(); 
        return new CompanyCollection($companies);
    }

    public function store(StoreCompanyRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $company = Company::create($validated);

            DB::commit();

            return (new CompanyResource($company->load(['industry', 'file'])))->response()->setStatusCode(201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function show(Company $company)
    {
        return new CompanyResource($company->load(['industry', 'file']));
    }

    public function update(UpdateCompanyRequest $request, Company $company)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $company->update($validated);

            DB::commit();

            return new CompanyResource($company->load(['industry', 'file']));

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return response()->json([
            'success' => true,
            'message' => 'Company deleted successfully'
        ], 200);
    }
}