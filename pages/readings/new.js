import { useEffect, useState } from 'react'

export default function NewReading() {
  const [subs, setSubs] = useState([])
  const [form, setForm] = useState({ matricula: '', contador: '', lecturaAnterior: '', lecturaActual: '' })
  const [result, setResult] = useState(null)
  const [error, setError] = useState(null)

  useEffect(() => {
    fetch('/api/subscribers').then(r => r.json()).then(json => setSubs(Array.isArray(json) ? json : (json.data || []))).catch(() => setSubs([]))
  }, [])

  async function handleSubmit(e) {
    e.preventDefault()
    setError(null)
    setResult(null)
    const res = await fetch('/api/readings', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(form) })
    const data = await res.json()
    if (!res.ok) setError(data.error || 'Error')
    else setResult(data)
  }

  return (
    <div className="container">
      <h2 className="h1">Tomar lectura</h2>
      <form onSubmit={handleSubmit} style={{ maxWidth: 640 }}>
        <div className="field">
          <label className="label">Suscriptor</label>
          <select className="input" value={form.matricula} onChange={e => setForm({ ...form, matricula: e.target.value })} required>
            <option value="">-- seleccione matricula --</option>
            {subs.map(s => <option key={s.id} value={s.matricula}>{s.matricula} - {s.nombres} {s.apellidos}</option>)}
          </select>
        </div>
        <div className="field">
          <label className="label">Número de contador</label>
          <input className="input" placeholder="numero de contador" value={form.contador} onChange={e => setForm({ ...form, contador: e.target.value })} required />
        </div>
        <div className="field">
          <label className="label">Lectura anterior</label>
          <input className="input" type="number" placeholder="lectura anterior" value={form.lecturaAnterior} onChange={e => setForm({ ...form, lecturaAnterior: e.target.value })} required />
        </div>
        <div className="field">
          <label className="label">Lectura actual</label>
          <input className="input" type="number" placeholder="lectura actual" value={form.lecturaActual} onChange={e => setForm({ ...form, lecturaActual: e.target.value })} required />
        </div>
        <button className="btn btn-primary" type="submit">Registrar lectura</button>
      </form>

      {error && <div className="card mt-3 p-3"><div style={{color: 'var(--danger)'}}>{error}</div></div>}
      {result && (
        <div className="mt-3 card p-3">
          <h3 className="h2">Lectura registrada ✅</h3>
          <pre>{JSON.stringify(result, null, 2)}</pre>
        </div>
      )}
    </div>
  )
}
