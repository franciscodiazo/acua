<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use App\Models\Subscriber;
use App\Models\Reading;
use App\Models\Credit;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\CreditPayment;
use Carbon\Carbon;

class BackupController extends Controller
{
    /**
     * Mostrar página de copias de respaldo
     */
    public function index()
    {
        $stats = [
            'subscribers' => Subscriber::count(),
            'readings' => Reading::count(),
            'invoices' => Invoice::count(),
            'credits' => Credit::count(),
            'payments' => Payment::count(),
            'credit_payments' => CreditPayment::count(),
        ];
        
        return view('backups.index', compact('stats'));
    }
    
    /**
     * Exportar base de datos completa a SQL
     */
    public function exportDatabase()
    {
        $tables = [
            'companies',
            'price_settings', 
            'subscribers',
            'readings',
            'invoices',
            'credits',
            'payments',
            'credit_payments'
        ];
        
        $sql = "-- Backup de Base de Datos ACUA\n";
        $sql .= "-- Generado: " . now()->format('Y-m-d H:i:s') . "\n";
        $sql .= "-- =========================================\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
        
        foreach ($tables as $table) {
            try {
                // Estructura de la tabla
                $createTable = DB::select("SHOW CREATE TABLE `{$table}`");
                if (!empty($createTable)) {
                    $sql .= "-- Estructura de tabla `{$table}`\n";
                    $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
                    $sql .= $createTable[0]->{'Create Table'} . ";\n\n";
                    
                    // Datos de la tabla
                    $rows = DB::table($table)->get();
                    if ($rows->count() > 0) {
                        $sql .= "-- Datos de tabla `{$table}`\n";
                        foreach ($rows as $row) {
                            $values = [];
                            foreach ((array)$row as $value) {
                                if (is_null($value)) {
                                    $values[] = 'NULL';
                                } else {
                                    $values[] = "'" . addslashes($value) . "'";
                                }
                            }
                            $columns = implode('`, `', array_keys((array)$row));
                            $sql .= "INSERT INTO `{$table}` (`{$columns}`) VALUES (" . implode(', ', $values) . ");\n";
                        }
                        $sql .= "\n";
                    }
                }
            } catch (\Exception $e) {
                $sql .= "-- Error al exportar tabla `{$table}`: " . $e->getMessage() . "\n\n";
            }
        }
        
        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        
        $filename = 'backup_acua_' . now()->format('Y-m-d_His') . '.sql';
        
        return Response::make($sql, 200, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    
    /**
     * Exportar suscriptores a CSV
     */
    public function exportSubscribers()
    {
        $subscribers = Subscriber::orderBy('matricula')->get();
        
        $csv = "matricula,cedula_nit,nombres,apellidos,direccion,telefono,email,sector,estrato,estado,fecha_instalacion\n";
        
        foreach ($subscribers as $s) {
            $csv .= '"' . $s->matricula . '",';
            $csv .= '"' . $s->cedula_nit . '",';
            $csv .= '"' . $s->nombres . '",';
            $csv .= '"' . $s->apellidos . '",';
            $csv .= '"' . addslashes($s->direccion) . '",';
            $csv .= '"' . $s->telefono . '",';
            $csv .= '"' . $s->email . '",';
            $csv .= '"' . $s->sector . '",';
            $csv .= '"' . $s->estrato . '",';
            $csv .= '"' . $s->estado . '",';
            $csv .= '"' . ($s->fecha_instalacion ? $s->fecha_instalacion->format('Y-m-d') : '') . '"';
            $csv .= "\n";
        }
        
        $filename = 'suscriptores_' . now()->format('Y-m-d_His') . '.csv';
        
        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    
    /**
     * Exportar lecturas a CSV
     */
    public function exportReadings()
    {
        $readings = Reading::with('subscriber')
            ->orderBy('fecha', 'desc')
            ->get();
        
        $csv = "matricula,fecha,ciclo,lectura_anterior,lectura_actual,consumo,observaciones\n";
        
        foreach ($readings as $r) {
            $csv .= '"' . ($r->subscriber->matricula ?? '') . '",';
            $csv .= '"' . $r->fecha->format('Y-m-d') . '",';
            $csv .= '"' . $r->ciclo . '",';
            $csv .= '"' . $r->lectura_anterior . '",';
            $csv .= '"' . $r->lectura_actual . '",';
            $csv .= '"' . $r->consumo . '",';
            $csv .= '"' . addslashes($r->observaciones ?? '') . '"';
            $csv .= "\n";
        }
        
        $filename = 'lecturas_' . now()->format('Y-m-d_His') . '.csv';
        
        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    
    /**
     * Exportar créditos a CSV
     */
    public function exportCredits()
    {
        $credits = Credit::with('subscriber')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $csv = "numero,matricula,tipo,concepto,monto,saldo,estado,fecha_creacion\n";
        
        foreach ($credits as $c) {
            $csv .= '"' . $c->numero . '",';
            $csv .= '"' . ($c->subscriber->matricula ?? '') . '",';
            $csv .= '"' . $c->tipo . '",';
            $csv .= '"' . addslashes($c->concepto) . '",';
            $csv .= '"' . $c->monto . '",';
            $csv .= '"' . $c->saldo . '",';
            $csv .= '"' . $c->estado . '",';
            $csv .= '"' . $c->created_at->format('Y-m-d H:i:s') . '"';
            $csv .= "\n";
        }
        
        $filename = 'creditos_' . now()->format('Y-m-d_His') . '.csv';
        
        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    
    /**
     * Importar suscriptores desde CSV
     */
    public function importSubscribers(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240'
        ]);
        
        $file = $request->file('file');
        $handle = fopen($file->getPathname(), 'r');
        
        // Saltar encabezado
        $header = fgetcsv($handle);
        
        $imported = 0;
        $updated = 0;
        $errors = [];
        $line = 1;
        
        while (($data = fgetcsv($handle)) !== false) {
            $line++;
            try {
                if (count($data) < 5) {
                    $errors[] = "Línea {$line}: Datos incompletos";
                    continue;
                }
                
                $matricula = trim($data[0]);
                if (empty($matricula)) {
                    $errors[] = "Línea {$line}: Matrícula vacía";
                    continue;
                }
                
                $subscriber = Subscriber::where('matricula', $matricula)->first();
                
                $subscriberData = [
                    'matricula' => $matricula,
                    'cedula_nit' => trim($data[1] ?? ''),
                    'nombres' => trim($data[2] ?? ''),
                    'apellidos' => trim($data[3] ?? ''),
                    'direccion' => trim($data[4] ?? ''),
                    'telefono' => trim($data[5] ?? null),
                    'email' => trim($data[6] ?? null) ?: null,
                    'sector' => trim($data[7] ?? null),
                    'estrato' => isset($data[8]) && is_numeric($data[8]) ? (int)$data[8] : 1,
                    'estado' => in_array(trim($data[9] ?? 'activo'), ['activo', 'inactivo', 'suspendido']) ? trim($data[9]) : 'activo',
                    'fecha_instalacion' => !empty($data[10]) ? Carbon::parse($data[10]) : null,
                ];
                
                if ($subscriber) {
                    $subscriber->update($subscriberData);
                    $updated++;
                } else {
                    Subscriber::create($subscriberData);
                    $imported++;
                }
            } catch (\Exception $e) {
                $errors[] = "Línea {$line}: " . $e->getMessage();
            }
        }
        
        fclose($handle);
        
        $message = "Importación completada: {$imported} nuevos, {$updated} actualizados.";
        if (count($errors) > 0) {
            $message .= " Errores: " . count($errors);
        }
        
        return redirect()->route('backups.index')
            ->with('success', $message)
            ->with('import_errors', $errors);
    }
    
    /**
     * Importar lecturas desde CSV
     */
    public function importReadings(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240'
        ]);
        
        $file = $request->file('file');
        $handle = fopen($file->getPathname(), 'r');
        
        // Saltar encabezado
        $header = fgetcsv($handle);
        
        $imported = 0;
        $errors = [];
        $line = 1;
        
        while (($data = fgetcsv($handle)) !== false) {
            $line++;
            try {
                if (count($data) < 5) {
                    $errors[] = "Línea {$line}: Datos incompletos";
                    continue;
                }
                
                $matricula = trim($data[0]);
                $subscriber = Subscriber::where('matricula', $matricula)->first();
                
                if (!$subscriber) {
                    $errors[] = "Línea {$line}: Suscriptor con matrícula '{$matricula}' no encontrado";
                    continue;
                }
                
                $fecha = Carbon::parse($data[1]);
                $ciclo = trim($data[2]);
                
                // Verificar si ya existe esta lectura
                $exists = Reading::where('subscriber_id', $subscriber->id)
                    ->where('ciclo', $ciclo)
                    ->exists();
                    
                if ($exists) {
                    $errors[] = "Línea {$line}: Ya existe lectura para {$matricula} en ciclo {$ciclo}";
                    continue;
                }
                
                Reading::create([
                    'subscriber_id' => $subscriber->id,
                    'fecha' => $fecha,
                    'ciclo' => $ciclo,
                    'lectura_anterior' => (float)$data[3],
                    'lectura_actual' => (float)$data[4],
                    'consumo' => (float)($data[5] ?? ($data[4] - $data[3])),
                    'observaciones' => trim($data[6] ?? null),
                ]);
                
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Línea {$line}: " . $e->getMessage();
            }
        }
        
        fclose($handle);
        
        $message = "Importación completada: {$imported} lecturas importadas.";
        if (count($errors) > 0) {
            $message .= " Errores: " . count($errors);
        }
        
        return redirect()->route('backups.index')
            ->with('success', $message)
            ->with('import_errors', $errors);
    }
    
    /**
     * Importar créditos desde CSV
     */
    public function importCredits(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240'
        ]);
        
        $file = $request->file('file');
        $handle = fopen($file->getPathname(), 'r');
        
        // Saltar encabezado
        $header = fgetcsv($handle);
        
        $imported = 0;
        $errors = [];
        $line = 1;
        
        while (($data = fgetcsv($handle)) !== false) {
            $line++;
            try {
                if (count($data) < 5) {
                    $errors[] = "Línea {$line}: Datos incompletos";
                    continue;
                }
                
                $matricula = trim($data[1]);
                $subscriber = Subscriber::where('matricula', $matricula)->first();
                
                if (!$subscriber) {
                    $errors[] = "Línea {$line}: Suscriptor con matrícula '{$matricula}' no encontrado";
                    continue;
                }
                
                $numero = trim($data[0]);
                
                // Verificar si ya existe este crédito
                $exists = Credit::where('numero', $numero)->exists();
                if ($exists) {
                    $errors[] = "Línea {$line}: Ya existe crédito con número '{$numero}'";
                    continue;
                }
                
                $monto = (float)$data[4];
                $saldo = isset($data[5]) && is_numeric($data[5]) ? (float)$data[5] : $monto;
                
                Credit::create([
                    'numero' => $numero,
                    'subscriber_id' => $subscriber->id,
                    'tipo' => in_array($data[2], ['credito', 'deuda', 'cuota_pendiente']) ? $data[2] : 'deuda',
                    'concepto' => trim($data[3]),
                    'monto' => $monto,
                    'saldo' => $saldo,
                    'estado' => $saldo > 0 ? 'pendiente' : 'pagado',
                ]);
                
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Línea {$line}: " . $e->getMessage();
            }
        }
        
        fclose($handle);
        
        $message = "Importación completada: {$imported} créditos importados.";
        if (count($errors) > 0) {
            $message .= " Errores: " . count($errors);
        }
        
        return redirect()->route('backups.index')
            ->with('success', $message)
            ->with('import_errors', $errors);
    }
    
    /**
     * Descargar plantilla CSV para suscriptores
     */
    public function templateSubscribers()
    {
        $csv = "matricula,cedula_nit,nombres,apellidos,direccion,telefono,email,sector,estrato,estado,fecha_instalacion\n";
        $csv .= '"001","123456789","Juan","Pérez González","Calle 1 # 2-34","3001234567","juan@email.com","Centro","2","activo","2024-01-15"' . "\n";
        $csv .= '"002","987654321","María","López Ruiz","Carrera 5 # 10-20","3009876543","","Norte","1","activo",""' . "\n";
        
        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="plantilla_suscriptores.csv"',
        ]);
    }
    
    /**
     * Descargar plantilla CSV para lecturas
     */
    public function templateReadings()
    {
        $ciclo = now()->format('Y-m');
        $csv = "matricula,fecha,ciclo,lectura_anterior,lectura_actual,consumo,observaciones\n";
        $csv .= '"001","' . now()->format('Y-m-d') . '","' . $ciclo . '","100","145","45",""' . "\n";
        $csv .= '"002","' . now()->format('Y-m-d') . '","' . $ciclo . '","200","238","38","Medidor nuevo"' . "\n";
        
        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="plantilla_lecturas.csv"',
        ]);
    }
    
    /**
     * Descargar plantilla CSV para créditos
     */
    public function templateCredits()
    {
        $csv = "numero,matricula,tipo,concepto,monto,saldo,estado,fecha_creacion\n";
        $csv .= '"CRE-001","001","deuda","Reconexión del servicio","50000","50000","pendiente","' . now()->format('Y-m-d H:i:s') . '"' . "\n";
        $csv .= '"CRE-002","002","credito","Instalación de medidor","150000","100000","pendiente","' . now()->format('Y-m-d H:i:s') . '"' . "\n";
        
        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="plantilla_creditos.csv"',
        ]);
    }
}
