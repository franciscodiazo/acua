<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Credit;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscriber::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('matricula', 'like', "%{$search}%")
                  ->orWhere('documento', 'like', "%{$search}%")
                  ->orWhere('nombres', 'like', "%{$search}%")
                  ->orWhere('apellidos', 'like', "%{$search}%");
            });
        }
        
        $subscribers = $query->orderBy('matricula')->paginate(15);
        
        return view('subscribers.index', compact('subscribers'));
    }

    public function create()
    {
        return view('subscribers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'matricula' => 'required|string|unique:subscribers,matricula',
            'documento' => 'required|string',
            'apellidos' => 'required|string|max:100',
            'nombres' => 'required|string|max:100',
            'correo' => 'nullable|email',
            'estrato' => 'required|integer|min:1|max:6',
            'telefono' => 'nullable|string|max:20',
            'sector' => 'nullable|string|max:100',
            'no_personas' => 'required|integer|min:1',
            'direccion' => 'required|string|max:255'
        ]);

        Subscriber::create($validated);

        return redirect()->route('subscribers.index')
            ->with('success', 'Suscriptor creado exitosamente.');
    }

    public function show(Subscriber $subscriber)
    {
        $subscriber->load(['readings' => function($q) {
            $q->orderBy('ciclo', 'desc');
        }, 'invoices' => function($q) {
            $q->orderBy('created_at', 'desc');
        }]);
        
        return view('subscribers.show', compact('subscriber'));
    }

    public function edit(Subscriber $subscriber)
    {
        return view('subscribers.edit', compact('subscriber'));
    }

    public function update(Request $request, Subscriber $subscriber)
    {
        $validated = $request->validate([
            'matricula' => 'required|string|unique:subscribers,matricula,' . $subscriber->id,
            'documento' => 'required|string',
            'apellidos' => 'required|string|max:100',
            'nombres' => 'required|string|max:100',
            'correo' => 'nullable|email',
            'estrato' => 'required|integer|min:1|max:6',
            'telefono' => 'nullable|string|max:20',
            'sector' => 'nullable|string|max:100',
            'no_personas' => 'required|integer|min:1',
            'direccion' => 'required|string|max:255',
            'activo' => 'boolean'
        ]);

        $validated['activo'] = $request->has('activo');
        
        $subscriber->update($validated);

        return redirect()->route('subscribers.index')
            ->with('success', 'Suscriptor actualizado exitosamente.');
    }

    public function destroy(Subscriber $subscriber)
    {
        $subscriber->delete();

        return redirect()->route('subscribers.index')
            ->with('success', 'Suscriptor eliminado exitosamente.');
    }

    public function estadoCuenta(Subscriber $subscriber)
    {
        $company = Company::first();
        
        // Facturas pendientes
        $facturasPendientes = Invoice::where('subscriber_id', $subscriber->id)
            ->whereIn('estado', ['pendiente', 'parcial'])
            ->orderBy('fecha_emision')
            ->get();
        
        // Historial de abonos
        $abonos = Payment::with('invoice')
            ->where('subscriber_id', $subscriber->id)
            ->orderBy('fecha', 'desc')
            ->get();
        
        // Créditos a favor
        $creditos = Credit::where('subscriber_id', $subscriber->id)
            ->where('estado', 'activo')
            ->orderBy('fecha', 'desc')
            ->get();
        
        // Historial de facturas (últimas 12)
        $historialFacturas = Invoice::with('reading')
            ->where('subscriber_id', $subscriber->id)
            ->orderBy('fecha_emision', 'desc')
            ->limit(12)
            ->get();
        
        // Resumen
        $resumen = [
            'saldo_pendiente' => $facturasPendientes->sum('saldo'),
            'total_abonado' => $abonos->where('estado', 'activo')->sum('monto'),
            'creditos_favor' => $creditos->sum('monto'),
            'cuotas_pendientes' => $facturasPendientes->count()
        ];
        
        return view('subscribers.estado-cuenta', compact(
            'subscriber', 
            'company', 
            'facturasPendientes', 
            'abonos', 
            'creditos', 
            'historialFacturas',
            'resumen'
        ));
    }
}
