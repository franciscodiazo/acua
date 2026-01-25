import { useEffect, useState } from 'react'
import { useRouter } from 'next/router'

export default function NewCredit() {
  const [subs, setSubs] = useState([])
  const [form, setForm] = useState({ matricula: '', amount: '', description: '' })
  const [msg, setMsg] = useState(null)
  const router = useRouter()

  useEffect(() => {
    fetch('/api/subscribers').then(r => r.json()).then(json => setSubs(Array.isArray(json) ? json : (json.data || []))).catch(() => setSubs([]))
  }, [])

  async function handleSubmit(e) {
    e.preventDefault()
    setMsg(null)
    const res = await fetch('/api/credits', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(form) })
    const data = await res.json()
    if (!res.ok) setMsg(data.error || 'Error')
    else {
      setMsg('Crédito registrado ✅')
      router.push('/credits')
    }
  }

  return (
    <div className="container">
      <div className="header"><h2 className="h1">Agregar crédito</h2></div>
      {msg && <div className="card mt-3 p-3"><div className="small-muted">{msg}</div></div>}
      <form onSubmit={handleSubmit} style={{ maxWidth: 480 }}>
        <div className="field">
          <label className="label">Suscriptor</label>
          <select className="input" value={form.matricula} onChange={e => setForm({ ...form, matricula: e.target.value })} required>
            <option value="">-- seleccione matricula --</option>
            {subs.map(s => <option key={s.id} value={s.matricula}>{s.matricula} - {s.nombres} {s.apellidos}</option>)}
          </select>
        </div>
        <div className="field">
          <label className="label">Monto</label>
          <input className="input" type="number" placeholder="monto" value={form.amount} onChange={e => setForm({ ...form, amount: e.target.value })} required />
        </div>
        <div className="field">
          <label className="label">Descripción</label>
          <input className="input" placeholder="descripcion" value={form.description} onChange={e => setForm({ ...form, description: e.target.value })} />
        </div>
        <div className="field"> 
          <button className="btn btn-primary" type="submit">Agregar crédito</button>
        </div>
      </form>
    </div>
  )
}
