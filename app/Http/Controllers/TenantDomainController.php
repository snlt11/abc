<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stancl\Tenancy\Database\Models\Domain;
use App\Models\Tenant;

class TenantDomainController extends Controller
{
    public function index()
    {
        return view('welcome');
    }
    
    public function checkDomain(Request $request)
    {
        $request->validate([
            'domain' => 'required|string|max:255'
        ]);
        
        $inputDomain = $request->domain;
        
        // Append .localhost if not already included
        if (!str_contains($inputDomain, '.')) {
            $inputDomain = $inputDomain . '.localhost';
        }
        
        // Check if domain exists
        $domain = Domain::where('domain', $inputDomain)->first();
        
        if ($domain) {
            // Check if the tenant is active
            $tenant = Tenant::find($domain->tenant_id);
            
            if ($tenant && $tenant->active) {
                // Domain exists and tenant is active, redirect to tenant
                return redirect()->away('http://' . $inputDomain . ':9000');
            } else {
                // Domain exists but tenant is inactive
                return back()->with('error', "The tenant '{$request->domain}' exists but is currently inactive.");
            }
        }
        
        // Domain doesn't exist, return with error message
        return back()->with('error', "There is no tenant with domain '{$request->domain}'.");
    }
}