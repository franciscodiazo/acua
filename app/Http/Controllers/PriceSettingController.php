<?php

namespace App\Http\Controllers;

use App\Models\PriceSetting;
use Illuminate\Http\Request;

class PriceSettingController extends Controller
{
    public function index()
    {
        $settings = PriceSetting::orderBy('anio', 'desc')->paginate(10);
        return view('settings.prices.index', compact('settings'));
    }

    public function create()
    {
        return view('settings.prices.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'anio' => 'required|integer|min:2020|max:2100',
            'consumo_basico' => 'required|numeric|min:1',
            'cuota_basica' => 'required|numeric|min:1',
            'tarifa_adicional' => 'required|numeric|min:1'
        ]);

        // Verificar si ya existe configuración para este año
        $existe = PriceSetting::where('anio', $validated['anio'])->exists();
        if ($existe) {
            return back()->withErrors(['anio' => 'Ya existe una configuración para este año.'])
                ->withInput();
        }

        PriceSetting::create($validated);

        return redirect()->route('settings.prices.index')
            ->with('success', 'Configuración de precios creada exitosamente.');
    }

    public function edit(PriceSetting $price)
    {
        return view('settings.prices.edit', compact('price'));
    }

    public function update(Request $request, PriceSetting $price)
    {
        $validated = $request->validate([
            'consumo_basico' => 'required|numeric|min:1',
            'cuota_basica' => 'required|numeric|min:1',
            'tarifa_adicional' => 'required|numeric|min:1',
            'activo' => 'boolean'
        ]);

        $validated['activo'] = $request->has('activo');
        
        $price->update($validated);

        return redirect()->route('settings.prices.index')
            ->with('success', 'Configuración de precios actualizada exitosamente.');
    }

    public function destroy(PriceSetting $price)
    {
        $price->delete();

        return redirect()->route('settings.prices.index')
            ->with('success', 'Configuración de precios eliminada exitosamente.');
    }
}
