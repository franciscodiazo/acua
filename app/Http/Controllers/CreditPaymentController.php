<?php

namespace App\Http\Controllers;

use App\Models\CreditPayment;
use App\Models\Credit;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreditPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = CreditPayment::with('credit', 'subscriber');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_recibo', 'like', "%{$search}%")
                  ->orWhereHas('subscriber', function($sq) use ($search) {
                    $sq->where('matricula', 'like', "%{$search}%")
                      ->orWhere('nombres', 'like', "%{$search}%")
                      ->orWhere('apellidos', 'like', "%{$search}%");
                });
            });
        }
        
        $payments = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('credit-payments.index', compact('payments'));
    }

    public function create(Request $request)
    {
        $credit = null;
        if ($request->filled('credit_id')) {
            $credit = Credit::with('subscriber')->find($request->credit_id);
        }
        
        // Mostrar todos los créditos/deudas con saldo pendiente
        $credits = Credit::with('subscriber')
            ->where('estado', 'activo')
            ->where('saldo', '>', 0)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('credit-payments.create', compact('credits', 'credit'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'credit_id' => 'required|exists:credits,id',
            'monto' => 'required|numeric|min:1',
            'fecha' => 'required|date',
            'metodo_pago' => 'required|string',
            'observaciones' => 'nullable|string'
        ]);

        $credit = Credit::findOrFail($validated['credit_id']);
        
        if ($credit->estado === 'anulado') {
            return back()->withErrors(['credit_id' => 'No se puede abonar a un crédito anulado.'])
                ->withInput();
        }

        if ($validated['monto'] > $credit->saldo) {
            return back()->withErrors(['monto' => 'El monto no puede ser mayor al saldo pendiente.'])
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $payment = CreditPayment::create([
                'credit_id' => $credit->id,
                'subscriber_id' => $credit->subscriber_id,
                'numero_recibo' => CreditPayment::generarNumeroRecibo(),
                'monto' => $validated['monto'],
                'fecha' => $validated['fecha'],
                'metodo_pago' => $validated['metodo_pago'],
                'observaciones' => $validated['observaciones'] ?? null
            ]);

            $credit->actualizarSaldo();

            DB::commit();

            return redirect()->route('credit-payments.show', $payment)
                ->with('success', 'Abono registrado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar abono: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(CreditPayment $creditPayment)
    {
        $creditPayment->load('credit', 'subscriber');
        return view('credit-payments.show', compact('creditPayment'));
    }

    public function print(CreditPayment $creditPayment)
    {
        $creditPayment->load('credit', 'subscriber');
        $company = Company::first();
        
        return view('credit-payments.print', compact('creditPayment', 'company'));
    }

    public function anular(CreditPayment $creditPayment)
    {
        if ($creditPayment->estado === 'anulado') {
            return redirect()->route('credit-payments.show', $creditPayment)
                ->with('error', 'Este abono ya está anulado.');
        }

        DB::beginTransaction();
        try {
            $creditPayment->update(['estado' => 'anulado']);
            $creditPayment->credit->actualizarSaldo();

            DB::commit();

            return redirect()->route('credit-payments.index')
                ->with('success', 'Abono anulado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('credit-payments.show', $creditPayment)
                ->with('error', 'Error al anular abono: ' . $e->getMessage());
        }
    }
}
