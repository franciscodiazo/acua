<?php

namespace App\Http\Controllers;

use App\Models\Reading;
use App\Models\Subscriber;
use App\Models\PriceSetting;
use Illuminate\Http\Request;

class ReadingController extends Controller
{
    public function index(Request $request)
    {
        $query = Reading::with('subscriber');
        
        if ($request->filled('ciclo')) {
            $query->where('ciclo', $request->ciclo);
        }
        
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('subscriber', function($q) use ($search) {
                $q->where('matricula', 'like', "%{$search}%")
                  ->orWhere('nombres', 'like', "%{$search}%")
                  ->orWhere('apellidos', 'like', "%{$search}%");
            });
        }
        
        $perPage = $request->get('per_page', 15);
        $readings = $query->orderBy('created_at', 'desc')->paginate($perPage);
        $ciclos = Reading::getCiclosDisponibles();
        
        return view('readings.index', compact('readings', 'ciclos'));
    }

    public function create(Request $request)
    {
        $ciclos = Reading::getCiclosDisponibles();
        $subscribers = Subscriber::where('activo', true)->orderBy('matricula')->get();
        $selectedSubscriber = null;
        $lecturaAnterior = 0;
        
        if ($request->filled('subscriber_id')) {
            $selectedSubscriber = Subscriber::find($request->subscriber_id);
            if ($selectedSubscriber) {
                $lecturaAnterior = Reading::getLecturaAnterior($selectedSubscriber->id);
            }
        }
        
        return view('readings.create', compact('ciclos', 'subscribers', 'selectedSubscriber', 'lecturaAnterior'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subscriber_id' => 'required|exists:subscribers,id',
            'ciclo' => 'required|string',
            'lectura_actual' => 'required|numeric|min:0',
            'fecha' => 'required|date'
        ]);

        // Verificar que no exista lectura para este suscriptor y ciclo
        $existe = Reading::where('subscriber_id', $validated['subscriber_id'])
            ->where('ciclo', $validated['ciclo'])
            ->exists();
            
        if ($existe) {
            return back()->withErrors(['ciclo' => 'Ya existe una lectura para este suscriptor en este ciclo.'])
                ->withInput();
        }

        // Obtener lectura anterior
        $lecturaAnterior = Reading::getLecturaAnterior($validated['subscriber_id'], $validated['ciclo']);
        
        // Validar que la lectura actual sea mayor o igual a la anterior
        if ($validated['lectura_actual'] < $lecturaAnterior) {
            return back()->withErrors(['lectura_actual' => 'La lectura actual no puede ser menor que la lectura anterior.'])
                ->withInput();
        }

        $reading = new Reading($validated);
        $reading->lectura_anterior = $lecturaAnterior;
        $reading->calcularConsumo();
        $reading->calcularValorTotal();
        $reading->save();

        return redirect()->route('readings.index')
            ->with('success', 'Lectura registrada exitosamente.');
    }

    public function show(Reading $reading)
    {
        $reading->load('subscriber', 'invoice');
        return view('readings.show', compact('reading'));
    }

    public function edit(Reading $reading)
    {
        if ($reading->estado === 'facturado') {
            return redirect()->route('readings.index')
                ->with('error', 'No se puede editar una lectura facturada.');
        }
        
        $ciclos = Reading::getCiclosDisponibles();
        return view('readings.edit', compact('reading', 'ciclos'));
    }

    public function update(Request $request, Reading $reading)
    {
        if ($reading->estado === 'facturado') {
            return redirect()->route('readings.index')
                ->with('error', 'No se puede editar una lectura facturada.');
        }

        $validated = $request->validate([
            'lectura_actual' => 'required|numeric|min:0',
            'fecha' => 'required|date'
        ]);

        if ($validated['lectura_actual'] < $reading->lectura_anterior) {
            return back()->withErrors(['lectura_actual' => 'La lectura actual no puede ser menor que la lectura anterior.'])
                ->withInput();
        }

        $reading->lectura_actual = $validated['lectura_actual'];
        $reading->fecha = $validated['fecha'];
        $reading->calcularConsumo();
        $reading->calcularValorTotal();
        $reading->save();

        return redirect()->route('readings.index')
            ->with('success', 'Lectura actualizada exitosamente.');
    }

    public function destroy(Reading $reading)
    {
        if ($reading->estado === 'facturado') {
            return redirect()->route('readings.index')
                ->with('error', 'No se puede eliminar una lectura facturada.');
        }

        $reading->delete();

        return redirect()->route('readings.index')
            ->with('success', 'Lectura eliminada exitosamente.');
    }

    // API para calcular en tiempo real
    public function calcular(Request $request)
    {
        $subscriberId = $request->subscriber_id;
        $lecturaActual = $request->lectura_actual;
        $ciclo = $request->ciclo;
        
        $lecturaAnterior = Reading::getLecturaAnterior($subscriberId, $ciclo);
        $consumo = max(0, $lecturaActual - $lecturaAnterior);
        
        $anio = $ciclo ? substr($ciclo, 0, 4) : date('Y');
        $config = PriceSetting::getActiveForYear($anio);
        
        $valorTotal = 0;
        if ($config) {
            $valorTotal = $config->calcularValor($consumo);
        }
        
        return response()->json([
            'lectura_anterior' => $lecturaAnterior,
            'consumo' => $consumo,
            'valor_total' => $valorTotal,
            'consumo_basico' => $config ? $config->consumo_basico : 40,
            'cuota_basica' => $config ? $config->cuota_basica : 25000,
            'tarifa_adicional' => $config ? $config->tarifa_adicional : 1500
        ]);
    }
}
