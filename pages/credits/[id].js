import { useEffect, useState } from 'react'
import { useRouter } from 'next/router'
import { formatCurrency } from '../../lib/utils'

export default function CreditDetail() {
  const router = useRouter()
  const { id } = router.query
  const [credit, setCredit] = useState(null)
  const [amount, setAmount] = useState('')
  const [loading, setLoading] = useState(false)
  const [msg, setMsg] = useState(null)

  useEffect(() => { if (id) fetchCredit() }, [id])

  async function fetchCredit() {
    const res = await fetch(`/api/credits/${id}`)
    if (!res.ok) return setMsg('No se pudo cargar el crédito')
    const json = await res.json()
    setCredit(json)
  }

  async function handlePay(e) {
    e.preventDefault()
    setLoading(true); setMsg(null)
    const res = await fetch(`/api/credits/${id}`, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ amount: Number(amount) }) })
    const json = await res.json()
    if (!res.ok) setMsg(json.error || 'Error')
    else {
      setMsg('Abono realizado ✅')
      setAmount('')
      // actualizar estado
      setCredit(json.credit)
    }
    setLoading(false)
  }

  if (!credit) return <div className="container"><p>Cargando...</p></div>

  return (
    <div className="container max-w-lg">
      <h2 className="h1">Crédito #{credit.id}</h2>
      <div className="card mt-3 p-4">
        <div className="mb-2"><strong>Matrícula:</strong> {credit.subscriber?.matricula}</div>
        <div className="mb-2"><strong>Saldo actual:</strong> {formatCurrency(credit.amount)}</div>
        <div className="mb-2"><strong>Descripción:</strong> {credit.description}</div>
      </div>

      {msg && <div className="card mt-3 p-3"><div className="small-muted">{msg}</div></div>}

      <form onSubmit={handlePay} className="mt-4 card p-4">
        <label className="label">Valor a abonar</label>
        <input className="input" value={amount} onChange={e => setAmount(e.target.value)} type="number" min="1" />
        <div className="mt-3">
          <button className="btn btn-primary" disabled={loading}>{loading ? 'Procesando...' : 'Abonar'}</button>
          <button type="button" className="btn ml-2" onClick={() => router.push('/credits')}>Volver</button>
        </div>
      </form>

      <div className="mt-4">
        <h3 className="h2">Historial de abonos</h3>
        <div className="mt-2">
          {credit.payments && credit.payments.length ? (
            <ul>
              {credit.payments.map(p => (
                <li key={p.id} className="py-1">{new Date(p.createdAt).toLocaleString()} — {formatCurrency(p.amount)}</li>
              ))}
            </ul>
          ) : <div className="small-muted">No hay abonos registrados</div>}
        </div>
      </div>
    </div>
  )
}
