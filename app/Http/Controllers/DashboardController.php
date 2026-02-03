<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use App\Models\Reading;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Company;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_subscribers' => Subscriber::where('activo', true)->count(),
            'readings_pendientes' => Reading::where('estado', 'pendiente')->count(),
            'invoices_pendientes' => Invoice::where('estado', 'pendiente')->count(),
            'total_recaudado_mes' => Payment::where('estado', 'activo')
                ->whereMonth('fecha', now()->month)
                ->whereYear('fecha', now()->year)
                ->sum('monto'),
            'saldo_por_cobrar' => Invoice::whereIn('estado', ['pendiente', 'parcial'])->sum('saldo')
        ];
        
        $ultimasLecturas = Reading::with('subscriber')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        $ultimosAbonos = Payment::with('subscriber', 'invoice')
            ->where('estado', 'activo')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('dashboard', compact('stats', 'ultimasLecturas', 'ultimosAbonos'));
    }

    public function reporteDiario(Request $request)
    {
        $fecha = $request->get('fecha', now()->format('Y-m-d'));
        
        $pagos = Payment::with('subscriber', 'invoice')
            ->whereDate('fecha', $fecha)
            ->orderBy('created_at')
            ->get();
        
        $cuotasEmitidas = Invoice::with('subscriber')
            ->whereDate('fecha_emision', $fecha)
            ->orderBy('created_at')
            ->get();
        
        $company = Company::first();
        
        $resumen = [
            'total_recibos' => $pagos->where('estado', 'activo')->count(),
            'total_recaudado' => $pagos->where('estado', 'activo')->sum('monto'),
            'cuotas_facturadas' => $cuotasEmitidas->count(),
            'anulaciones' => $pagos->where('estado', 'anulado')->count()
        ];
        
        $resumenMetodos = [];
        foreach (Payment::$metodosPago as $key => $label) {
            $resumenMetodos[$key] = $pagos->where('estado', 'activo')->where('metodo_pago', $key)->sum('monto');
        }
        
        $titulo = 'REPORTE MOVIMIENTO DIARIO';
        $subtitulo = 'Fecha: ' . \Carbon\Carbon::parse($fecha)->format('d/m/Y');
        
        return view('reports.movimientos', compact(
            'pagos', 
            'cuotasEmitidas', 
            'company', 
            'resumen', 
            'resumenMetodos',
            'titulo',
            'subtitulo'
        ));
    }

    public function reporteFechas(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));
        
        $pagos = Payment::with('subscriber', 'invoice')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->orderBy('fecha')
            ->orderBy('created_at')
            ->get();
        
        $cuotasEmitidas = Invoice::with('subscriber')
            ->whereBetween('fecha_emision', [$fechaInicio, $fechaFin])
            ->orderBy('fecha_emision')
            ->get();
        
        $company = Company::first();
        
        $resumen = [
            'total_recibos' => $pagos->where('estado', 'activo')->count(),
            'total_recaudado' => $pagos->where('estado', 'activo')->sum('monto'),
            'cuotas_facturadas' => $cuotasEmitidas->count(),
            'anulaciones' => $pagos->where('estado', 'anulado')->count()
        ];
        
        $resumenMetodos = [];
        foreach (Payment::$metodosPago as $key => $label) {
            $resumenMetodos[$key] = $pagos->where('estado', 'activo')->where('metodo_pago', $key)->sum('monto');
        }
        
        $titulo = 'REPORTE DE MOVIMIENTOS';
        $subtitulo = 'Período: ' . \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') . ' al ' . \Carbon\Carbon::parse($fechaFin)->format('d/m/Y');
        
        return view('reports.movimientos', compact(
            'pagos', 
            'cuotasEmitidas', 
            'company', 
            'resumen', 
            'resumenMetodos',
            'titulo',
            'subtitulo'
        ));
    }

    public function cierreAnual(Request $request)
    {
        $anio = $request->get('anio', now()->year);
        
        $pagos = Payment::with('subscriber', 'invoice')
            ->whereYear('fecha', $anio)
            ->orderBy('fecha')
            ->get();
        
        $cuotasEmitidas = Invoice::with('subscriber')
            ->whereYear('fecha_emision', $anio)
            ->orderBy('fecha_emision')
            ->get();
        
        $company = Company::first();
        
        $resumen = [
            'total_recibos' => $pagos->where('estado', 'activo')->count(),
            'total_recaudado' => $pagos->where('estado', 'activo')->sum('monto'),
            'cuotas_facturadas' => $cuotasEmitidas->count(),
            'anulaciones' => $pagos->where('estado', 'anulado')->count()
        ];
        
        $resumenMetodos = [];
        foreach (Payment::$metodosPago as $key => $label) {
            $resumenMetodos[$key] = $pagos->where('estado', 'activo')->where('metodo_pago', $key)->sum('monto');
        }
        
        $titulo = 'CIERRE ANUAL ' . $anio;
        $subtitulo = 'Resumen de movimientos del año ' . $anio;
        
        return view('reports.movimientos', compact(
            'pagos', 
            'cuotasEmitidas', 
            'company', 
            'resumen', 
            'resumenMetodos',
            'titulo',
            'subtitulo'
        ));
    }
}
