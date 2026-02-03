<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
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
}
