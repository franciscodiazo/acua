<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('invoice', 'subscriber');
        
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
        
        return view('payments.index', compact('payments'));
    }

    public function create(Request $request)
    {
        $invoice = null;
        if ($request->filled('invoice_id')) {
            $invoice = Invoice::with('subscriber')->find($request->invoice_id);
        }
        
        $invoices = Invoice::with('subscriber')
            ->whereIn('estado', ['pendiente', 'parcial'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('payments.create', compact('invoices', 'invoice'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'monto' => 'required|numeric|min:1',
            'fecha' => 'required|date',
            'metodo_pago' => 'required|string',
            'observaciones' => 'nullable|string'
        ]);

        $invoice = Invoice::findOrFail($validated['invoice_id']);
        
        if ($invoice->estado === 'anulada') {
            return back()->withErrors(['invoice_id' => 'No se puede abonar a una cuota anulada.'])
                ->withInput();
        }

        if ($validated['monto'] > $invoice->saldo) {
            return back()->withErrors(['monto' => 'El monto no puede ser mayor al saldo pendiente.'])
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'subscriber_id' => $invoice->subscriber_id,
                'numero_recibo' => Payment::generarNumeroRecibo(),
                'monto' => $validated['monto'],
                'fecha' => $validated['fecha'],
                'metodo_pago' => $validated['metodo_pago'],
                'observaciones' => $validated['observaciones'] ?? null
            ]);

            $invoice->actualizarSaldo();

            DB::commit();

            return redirect()->route('payments.show', $payment)
                ->with('success', 'Pago registrado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar pago: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Payment $payment)
    {
        $payment->load('invoice', 'subscriber');
        return view('payments.show', compact('payment'));
    }

    public function print(Payment $payment)
    {
        $payment->load('invoice.reading', 'subscriber');
        $company = Company::first();
        
        // Obtener otras deudas pendientes del suscriptor
        $otrasDeudasPendientes = Invoice::where('subscriber_id', $payment->subscriber_id)
            ->where('id', '!=', $payment->invoice_id)
            ->whereIn('estado', ['pendiente', 'parcial'])
            ->orderBy('fecha_emision')
            ->get();
        
        return view('payments.print', compact('payment', 'company', 'otrasDeudasPendientes'));
    }

    public function anular(Payment $payment)
    {
        if ($payment->estado === 'anulado') {
            return redirect()->route('payments.show', $payment)
                ->with('error', 'Este pago ya está anulado.');
        }

        DB::beginTransaction();
        try {
            $payment->update(['estado' => 'anulado']);
            $payment->invoice->actualizarSaldo();

            DB::commit();

            return redirect()->route('payments.index')
                ->with('success', 'Pago anulado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('payments.show', $payment)
                ->with('error', 'Error al anular pago: ' . $e->getMessage());
        }
    }
}
