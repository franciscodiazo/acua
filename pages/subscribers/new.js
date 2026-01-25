import { useState } from 'react'
import { useRouter } from 'next/router'

export default function NewSubscriber() {
  const [form, setForm] = useState({ matricula: '', documento: '', apellidos: '', nombres: '', correo: '', estrato: '', telefono: '', sector: '', no_personas: '', direccion: '' })
  const [msg, setMsg] = useState(null)
  const router = useRouter()

  async function handleSubmit(e) {
    e.preventDefault()
    const res = await fetch('/api/subscribers', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(form) })
    const data = await res.json()
    if (!res.ok) setMsg(data.error || 'Error')
    else {
      setMsg('Suscriptor creado ✅')
      router.push('/')
    }
  }

  return (
    <div className="container">
      <h2 className="h1">Crear suscriptor</h2>
      {msg && <div className="card mt-3 p-3"><div className="small-muted">{msg}</div></div>}
      <form onSubmit={handleSubmit} style={{ maxWidth: 640 }}>
        <div className="field">
          <label className="label">Matrícula</label>
          <input className="input" placeholder="matrícula" value={form.matricula} onChange={e => setForm({ ...form, matricula: e.target.value })} required />
        </div>
        <div className="field">
          <label className="label">Documento</label>
          <input className="input" placeholder="Número de documento" value={form.documento} onChange={e => setForm({ ...form, documento: e.target.value })} required />
        </div>
        <div className="field">
          <label className="label">Apellidos</label>
          <input className="input" placeholder="apellidos" value={form.apellidos} onChange={e => setForm({ ...form, apellidos: e.target.value })} required />
        </div>
        <div className="field">
          <label className="label">Nombres</label>
          <input className="input" placeholder="nombres" value={form.nombres} onChange={e => setForm({ ...form, nombres: e.target.value })} required />
        </div>
        <div className="field">
          <label className="label">Correo</label>
          <input className="input" placeholder="correo" value={form.correo} onChange={e => setForm({ ...form, correo: e.target.value })} />
        </div>
        <div className="field">
          <label className="label">Estrato</label>
          <input className="input" type="number" placeholder="estrato" value={form.estrato} onChange={e => setForm({ ...form, estrato: e.target.value })} />
        </div>
        <div className="field">
          <label className="label">Teléfono</label>
          <input className="input" placeholder="telefono" value={form.telefono} onChange={e => setForm({ ...form, telefono: e.target.value })} />
        </div>
        <div className="field">
          <label className="label">Sector</label>
          <input className="input" placeholder="sector" value={form.sector} onChange={e => setForm({ ...form, sector: e.target.value })} />
        </div>
        <div className="field">
          <label className="label">Número de personas</label>
          <input className="input" type="number" placeholder="no_personas" value={form.no_personas} onChange={e => setForm({ ...form, no_personas: e.target.value })} />
        </div>
        <div className="field">
          <label className="label">Dirección</label>
          <input className="input" placeholder="dirección" value={form.direccion} onChange={e => setForm({ ...form, direccion: e.target.value })} />
        </div>
        <button className="btn btn-primary" type="submit">Crear suscriptor</button>
      </form>
    </div>
  )
}
