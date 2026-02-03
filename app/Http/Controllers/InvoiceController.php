<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Reading;
use App\Models\Company;
use App\Models\Credit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with('subscriber', 'reading');
        
        if ($request->filled('ciclo')) {
            $query->where('ciclo', $request->ciclo);
        }
        
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                  ->orWhereHas('subscriber', function($sq) use ($search) {
                    $sq->where('matricula', 'like', "%{$search}%")
                      ->orWhere('nombres', 'like', "%{$search}%")
                      ->orWhere('apellidos', 'like', "%{$search}%");
                });
            });
        }
        
        $perPage = $request->get('per_page', 15);
        $invoices = $query->orderBy('created_at', 'desc')->paginate($perPage);
        $ciclos = Reading::getCiclosDisponibles();
        
        return view('invoices.index', compact('invoices', 'ciclos'));
    }

    public function facturarLecturas(Request $request)
    {
        $ciclo = $request->ciclo;
        
        if (!$ciclo) {
            return redirect()->route('readings.index')
                ->with('error', 'Debe seleccionar un ciclo para facturar.');
        }

        // Obtener lecturas pendientes del ciclo
        $lecturas = Reading::where('ciclo', $ciclo)
            ->where('estado', 'pendiente')
            ->get();

        if ($lecturas->isEmpty()) {
            return redirect()->route('readings.index')
                ->with('error', 'No hay lecturas pendientes para facturar en este ciclo.');
        }

        $facturadas = 0;

        DB::beginTransaction();
        try {
            foreach ($lecturas as $lectura) {
                $invoice = Invoice::create([
                    'numero' => Invoice::generarNumero(),
                    'subscriber_id' => $lectura->subscriber_id,
                    'reading_id' => $lectura->id,
                    'ciclo' => $lectura->ciclo,
                    'fecha_emision' => now(),
                    'fecha_vencimiento' => now()->addDays(15),
                    'subtotal' => $lectura->valor_total,
                    'descuentos' => 0,
                    'total' => $lectura->valor_total,
                    'saldo' => $lectura->valor_total,
                    'estado' => 'pendiente'
                ]);

                $lectura->update(['estado' => 'facturado']);
                $facturadas++;
            }

            DB::commit();

            return redirect()->route('invoices.index')
                ->with('success', "Se facturaron {$facturadas} lecturas exitosamente.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('readings.index')
                ->with('error', 'Error al facturar lecturas: ' . $e->getMessage());
        }
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('subscriber', 'reading', 'payments');
        $company = Company::first();
        
        return view('invoices.show', compact('invoice', 'company'));
    }

    public function print(Invoice $invoice)
    {
        $invoice->load('subscriber', 'reading', 'payments');
        $company = Company::first();
        
        // Créditos/deudas pendientes del suscriptor
        $creditosPendientes = Credit::where('subscriber_id', $invoice->subscriber_id)
            ->where('estado', 'activo')
            ->where('saldo', '>', 0)
            ->get();
        
        $totalCreditosPendientes = $creditosPendientes->sum('saldo');
        
        // Historial de consumo de los últimos 12 meses
        $historialConsumo = Reading::where('subscriber_id', $invoice->subscriber_id)
            ->where('estado', 'facturado')
            ->orderBy('fecha', 'desc')
            ->take(12)
            ->get()
            ->reverse()
            ->values();
        
        return view('invoices.print', compact('invoice', 'company', 'creditosPendientes', 'totalCreditosPendientes', 'historialConsumo'));
    }

    public function anular(Invoice $invoice)
    {
        if ($invoice->estado === 'pagada') {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'No se puede anular una factura pagada.');
        }

        DB::beginTransaction();
        try {
            // Revertir estado de la lectura
            if ($invoice->reading) {
                $invoice->reading->update(['estado' => 'pendiente']);
            }

            $invoice->update(['estado' => 'anulada', 'saldo' => 0]);

            DB::commit();

            return redirect()->route('invoices.index')
                ->with('success', 'Factura anulada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Error al anular factura: ' . $e->getMessage());
        }
    }

    /**
     * Facturar una lectura individual
     */
    public function facturarIndividual(Reading $reading)
    {
        if ($reading->estado !== 'pendiente') {
            return redirect()->route('readings.index')
                ->with('error', 'Esta lectura no está pendiente de facturación.');
        }

        DB::beginTransaction();
        try {
            $invoice = Invoice::create([
                'numero' => Invoice::generarNumero(),
                'subscriber_id' => $reading->subscriber_id,
                'reading_id' => $reading->id,
                'ciclo' => $reading->ciclo,
                'fecha_emision' => now(),
                'fecha_vencimiento' => now()->addDays(15),
                'subtotal' => $reading->valor_total,
                'descuentos' => 0,
                'total' => $reading->valor_total,
                'saldo' => $reading->valor_total,
                'estado' => 'pendiente'
            ]);

            $reading->update(['estado' => 'facturado']);

            DB::commit();

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Factura generada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('readings.index')
                ->with('error', 'Error al facturar lectura: ' . $e->getMessage());
        }
    }

    /**
     * Facturar lecturas seleccionadas
     */
    public function facturarSeleccionadas(Request $request)
    {
        $readingIds = $request->input('readings', []);

        if (empty($readingIds)) {
            return redirect()->route('readings.index')
                ->with('error', 'Debe seleccionar al menos una lectura para facturar.');
        }

        $lecturas = Reading::whereIn('id', $readingIds)
            ->where('estado', 'pendiente')
            ->get();

        if ($lecturas->isEmpty()) {
            return redirect()->route('readings.index')
                ->with('error', 'No hay lecturas pendientes válidas para facturar.');
        }

        $facturadas = 0;

        DB::beginTransaction();
        try {
            foreach ($lecturas as $lectura) {
                $invoice = Invoice::create([
                    'numero' => Invoice::generarNumero(),
                    'subscriber_id' => $lectura->subscriber_id,
                    'reading_id' => $lectura->id,
                    'ciclo' => $lectura->ciclo,
                    'fecha_emision' => now(),
                    'fecha_vencimiento' => now()->addDays(15),
                    'subtotal' => $lectura->valor_total,
                    'descuentos' => 0,
                    'total' => $lectura->valor_total,
                    'saldo' => $lectura->valor_total,
                    'estado' => 'pendiente'
                ]);

                $lectura->update(['estado' => 'facturado']);
                $facturadas++;
            }

            DB::commit();

            return redirect()->route('invoices.index')
                ->with('success', "Se facturaron {$facturadas} lecturas exitosamente.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('readings.index')
                ->with('error', 'Error al facturar lecturas: ' . $e->getMessage());
        }
    }
}
