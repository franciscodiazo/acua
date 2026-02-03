<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'representante_legal' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cuenta_bancaria' => 'nullable|string|max:100',
            'banco' => 'nullable|string|max:100',
            'mensaje_factura' => 'nullable|string|max:500'
        ]);

        $company = Company::first();
        
        // Manejar la subida del logo
        if ($request->hasFile('logo')) {
            // Eliminar logo anterior si existe
            if ($company && $company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        if ($company) {
            $company->update($validated);
        } else {
            Company::create($validated);
        }

        return redirect()->route('settings.company.edit')
            ->with('success', 'Información de la empresa actualizada exitosamente.');
    }

    public function deleteLogo()
    {
        $company = Company::first();
        
        if ($company && $company->logo) {
            Storage::disk('public')->delete($company->logo);
            $company->update(['logo' => null]);
        }

        return redirect()->route('settings.company.edit')
            ->with('success', 'Logo eliminado exitosamente.');
    }
}
