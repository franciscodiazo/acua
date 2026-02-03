<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use App\Models\Subscriber;
use App\Models\Company;
use Illuminate\Http\Request;

class CreditController extends Controller
{
    public function index(Request $request)
    {
        $query = Credit::with('subscriber', 'invoice');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('subscriber', function($q) use ($search) {
                $q->where('matricula', 'like', "%{$search}%")
                  ->orWhere('nombres', 'like', "%{$search}%")
                  ->orWhere('apellidos', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        
        $perPage = $request->get('per_page', 15);
        $credits = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        return view('credits.index', compact('credits'));
    }

    public function create(Request $request)
    {
        $subscriber = null;
        if ($request->filled('subscriber_id')) {
            $subscriber = Subscriber::find($request->subscriber_id);
        }
        
        $subscribers = Subscriber::where('activo', true)->orderBy('matricula')->get();
        
        return view('credits.create', compact('subscribers', 'subscriber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subscriber_id' => 'required|exists:subscribers,id',
            'tipo' => 'required|in:credito,deuda,cuota',
            'concepto' => 'required|string|max:255',
            'monto' => 'required|numeric|min:1',
            'fecha' => 'required|date',
            'observaciones' => 'nullable|string'
        ]);

        $validated['numero'] = Credit::generarNumero($validated['tipo']);
        $validated['saldo'] = $validated['monto'];

        Credit::create($validated);

        return redirect()->route('credits.index')
            ->with('success', 'Crédito registrado exitosamente.');
    }

    public function show(Credit $credit)
    {
        $credit->load('subscriber', 'invoice', 'payments');
        $company = Company::first();
        return view('credits.show', compact('credit', 'company'));
    }

    public function anular(Credit $credit)
    {
        if ($credit->estado === 'aplicado') {
            return redirect()->route('credits.show', $credit)
                ->with('error', 'No se puede anular un crédito ya aplicado.');
        }

        $credit->update(['estado' => 'anulado']);

        return redirect()->route('credits.index')
            ->with('success', 'Crédito anulado exitosamente.');
    }
}
