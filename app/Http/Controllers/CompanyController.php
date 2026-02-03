<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function edit()
    {
        $company = Company::first();
        return view('settings.company.edit', compact('company'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'nit' => 'required|string|max:50',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'municipio' => 'nullable|string|max:100',
            'departamento' => 'nullable|string|max:100',
            'representante_legal' => 'nullable|string|max:255'
        ]);

        $company = Company::first();
        
        if ($company) {
            $company->update($validated);
        } else {
            Company::create($validated);
        }

        return redirect()->route('settings.company.edit')
            ->with('success', 'Información de la empresa actualizada exitosamente.');
    }
}
