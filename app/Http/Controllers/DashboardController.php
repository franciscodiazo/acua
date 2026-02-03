<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use App\Models\Reading;
use App\Models\Invoice;
use App\Models\Payment;

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
}
