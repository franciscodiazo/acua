import { useEffect, useRef, useState } from 'react'
import Pagination from '../../components/Pagination'
import Papa from 'papaparse'

export default function SubscribersList() {
  const [data, setData] = useState([])
  const [page, setPage] = useState(1)
  const [totalPages, setTotalPages] = useState(1)
  const [loading, setLoading] = useState(false)
  const [importing, setImporting] = useState(false)
  const [importMessage, setImportMessage] = useState('')
  const [showModal, setShowModal] = useState(false)
  const [showDetailsModal, setShowDetailsModal] = useState(false)
  const [detailsLoading, setDetailsLoading] = useState(false)
  const [subscriberDetails, setSubscriberDetails] = useState(null)
  const [editingId, setEditingId] = useState(null)
  const [formData, setFormData] = useState({})
  const [search, setSearch] = useState('')
  const [orderBy, setOrderBy] = useState('id')
  const [orderDir, setOrderDir] = useState('desc')
  const [limit, setLimit] = useState(50)
  const fileInputRef = useRef()

  useEffect(() => { fetchPage(1) }, [search, orderBy, orderDir, limit])

  async function fetchPage(p) {
    setLoading(true)
    const res = await fetch(`/api/subscribers?page=${p}&limit=${limit}&search=${encodeURIComponent(search)}&orderBy=${orderBy}&orderDir=${orderDir}`)
    const json = await res.json()
    setData(json.data || [])
    setPage(json.page || 1)
    setTotalPages(json.totalPages || 1)
    setLoading(false)
  }

  async function openDetails(subscriber) {
    setSubscriberDetails(subscriber)
    setDetailsLoading(true)
    setShowDetailsModal(true)
    
    try {
      const [credits, readings, invoices] = await Promise.all([
        fetch(`/api/credits?limit=1000`).then(r => r.json()),
        fetch(`/api/readings?limit=1000`).then(r => r.json()),
        fetch(`/api/invoices?limit=1000`).then(r => r.json())
      ])
      
      const subCredits = credits.data?.filter(c => c.subscriber?.id === subscriber.id) || []
      const subReadings = readings.data?.filter(r => r.subscriber?.id === subscriber.id) || []
      const subInvoices = invoices.data?.filter(i => i.reading?.subscriber?.id === subscriber.id) || []
      
      setSubscriberDetails({
        ...subscriber,
        credits: subCredits,
        readings: subReadings,
        invoices: subInvoices
      })
    } catch (e) {
      console.error('Error cargando detalles:', e)
    } finally {
      setDetailsLoading(false)
    }
  }

  function openModal(subscriber = null) {
    if (subscriber) {
      setEditingId(subscriber.id)
      setFormData(subscriber)
    } else {
      setEditingId(null)
      setFormData({ matricula: '', documento: '', apellidos: '', nombres: '', correo: '', telefono: '', sector: '', estrato: '', no_personas: '', direccion: '' })
    }
    setShowModal(true)
  }

  async function handleSave() {
    try {
      const url = editingId ? `/api/subscribers/${editingId}` : '/api/subscribers'
      const method = editingId ? 'PATCH' : 'POST'
      const res = await fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
      })
      if (!res.ok) throw new Error('Error al guardar')
      setShowModal(false)
      fetchPage(page)
    } catch (e) {
      alert(`Error: ${e.message}`)
    }
  }

  async function handleDelete(id) {
    if (!confirm('¿Está seguro?')) return
    try {
      const res = await fetch(`/api/subscribers/${id}`, { method: 'DELETE' })
      if (!res.ok) throw new Error('Error al eliminar')
      fetchPage(page)
    } catch (e) {
      alert(`Error: ${e.message}`)
    }
  }

  async function handleImport(file) {
    if (!file) return
    setImporting(true)
    setImportMessage('')

    try {
      Papa.parse(file, {
        header: true,
        skipEmptyLines: true,
        complete: async (results) => {
          try {
            const res = await fetch('/api/subscribers/import', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ subscribers: results.data })
            })
            const json = await res.json()

            if (!res.ok) {
              setImportMessage(`❌ Error: ${json.error}`)
            } else {
              setImportMessage(`✅ ${json.message}. ${json.errors?.length > 0 ? `${json.errors.length} errores.` : ''}`)
              fetchPage(1)
            }
          } catch (e) {
            setImportMessage(`❌ Error: ${e.message}`)
          } finally {
            setImporting(false)
            fileInputRef.current.value = ''
          }
        },
        error: (e) => {
          setImportMessage(`❌ Error al leer archivo: ${e.message}`)
          setImporting(false)
        }
      })
    } catch (e) {
      setImportMessage(`❌ Error: ${e.message}`)
      setImporting(false)
    }
  }

  async function handleExport() {
    try {
      const res = await fetch('/api/subscribers/export')
      if (!res.ok) throw new Error('Error al exportar')
      const blob = await res.blob()
      const url = URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `suscriptores_${new Date().toISOString().split('T')[0]}.csv`
      document.body.appendChild(a)
      a.click()
      document.body.removeChild(a)
      URL.revokeObjectURL(url)
    } catch (e) {
      alert(`Error al exportar: ${e.message}`)
    }
  }

  return (
    <div className="max-w-7xl mx-auto py-6 px-4">
      <div className="flex justify-between items-center mb-6">
        <h2 className="text-xl font-semibold">Suscriptores</h2>
        <div className="flex gap-2">
          <button
            onClick={() => openModal()}
            className="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded text-sm font-medium"
          >
            ➕ Crear
          </button>
          <button
            onClick={() => fileInputRef.current?.click()}
            disabled={importing}
            className="bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white px-4 py-2 rounded text-sm"
          >
            {importing ? '⟳ Importando...' : '📥 Importar'}
          </button>
          <button
            onClick={handleExport}
            className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm"
          >
            📤 Exportar
          </button>
        </div>
        <input
          ref={fileInputRef}
          type="file"
          accept=".csv"
          onChange={(e) => handleImport(e.target.files?.[0])}
          style={{ display: 'none' }}
        />
      </div>

      {importMessage && (
        <div className="mb-4 p-3 rounded bg-gray-100 text-sm text-gray-700">
          {importMessage}
        </div>
      )}

      <div className="mb-6 flex gap-4 items-center">
        <input
          type="text"
          placeholder="🔍 Buscar por matrícula, nombres, correo..."
          value={search}
          onChange={(e) => {
            setSearch(e.target.value)
            setPage(1)
          }}
          className="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
        />
        <select
          value={limit}
          onChange={(e) => {
            setLimit(Number(e.target.value))
            setPage(1)
          }}
          className="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm"
        >
          <option value={50}>50 registros</option>
          <option value={100}>100 registros</option>
          <option value={200}>200 registros</option>
        </select>
      </div>

      {loading && <div className="mt-3 text-sm text-gray-500">Cargando...</div>}

      <div className="mt-4 overflow-hidden rounded-lg border border-gray-200">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              <th 
                className="px-4 py-3 text-left text-xs font-medium text-gray-500 cursor-pointer hover:bg-gray-100"
                onClick={() => {
                  if (orderBy === 'matricula') setOrderDir(orderDir === 'asc' ? 'desc' : 'asc')
                  else { setOrderBy('matricula'); setOrderDir('asc') }
                }}
              >
                Matrícula {orderBy === 'matricula' && (orderDir === 'asc' ? '▲' : '▼')}
              </th>
              <th 
                className="px-4 py-3 text-left text-xs font-medium text-gray-500 cursor-pointer hover:bg-gray-100"
                onClick={() => {
                  if (orderBy === 'nombres') setOrderDir(orderDir === 'asc' ? 'desc' : 'asc')
                  else { setOrderBy('nombres'); setOrderDir('asc') }
                }}
              >
                Nombres {orderBy === 'nombres' && (orderDir === 'asc' ? '▲' : '▼')}
              </th>
              <th 
                className="px-4 py-3 text-left text-xs font-medium text-gray-500 cursor-pointer hover:bg-gray-100"
                onClick={() => {
                  if (orderBy === 'correo') setOrderDir(orderDir === 'asc' ? 'desc' : 'asc')
                  else { setOrderBy('correo'); setOrderDir('asc') }
                }}
              >
                Correo {orderBy === 'correo' && (orderDir === 'asc' ? '▲' : '▼')}
              </th>
              <th 
                className="px-4 py-3 text-left text-xs font-medium text-gray-500 cursor-pointer hover:bg-gray-100"
                onClick={() => {
                  if (orderBy === 'telefono') setOrderDir(orderDir === 'asc' ? 'desc' : 'asc')
                  else { setOrderBy('telefono'); setOrderDir('asc') }
                }}
              >
                Teléfono {orderBy === 'telefono' && (orderDir === 'asc' ? '▲' : '▼')}
              </th>
              <th 
                className="px-4 py-3 text-left text-xs font-medium text-gray-500 cursor-pointer hover:bg-gray-100"
                onClick={() => {
                  if (orderBy === 'sector') setOrderDir(orderDir === 'asc' ? 'desc' : 'asc')
                  else { setOrderBy('sector'); setOrderDir('asc') }
                }}
              >
                Sector {orderBy === 'sector' && (orderDir === 'asc' ? '▲' : '▼')}
              </th>
              <th className="px-4 py-3 text-left text-xs font-medium text-gray-500">Acciones</th>
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-100">
            {data.map(s => (
              <tr key={s.id} className="hover:bg-gray-50">
                <td className="px-4 py-2 text-sm text-gray-700 font-medium">{s.matricula}</td>
                <td className="px-4 py-2 text-sm text-gray-700">{s.nombres} {s.apellidos}</td>
                <td className="px-4 py-2 text-sm text-gray-700">{s.correo || '-'}</td>
                <td className="px-4 py-2 text-sm text-gray-700">{s.telefono || '-'}</td>
                <td className="px-4 py-2 text-sm text-gray-700">{s.sector || '-'}</td>
                <td className="px-4 py-2 text-sm flex gap-2">
                  <button onClick={() => openDetails(s)} className="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs">👁 Ver Detalles</button>
                  <button onClick={() => openModal(s)} className="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs">✎ Editar</button>
                  <button onClick={() => handleDelete(s.id)} className="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">✕ Borrar</button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <div className="flex justify-center mt-4">
        <Pagination page={page} totalPages={totalPages} onChange={fetchPage} />
      </div>

      {showModal && (
        <div style={{ position: 'fixed', top: 0, left: 0, right: 0, bottom: 0, background: 'rgba(0,0,0,0.5)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 1000 }}>
          <div style={{ background: '#fff', borderRadius: '10px', padding: '24px', width: '90%', maxWidth: '500px' }}>
            <h3 style={{ fontSize: '18px', fontWeight: 700, marginBottom: '16px' }}>{editingId ? 'Editar' : 'Crear'} Suscriptor</h3>
            
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '12px', marginBottom: '16px' }}>
              <input type="text" placeholder="Matrícula" value={formData.matricula || ''} onChange={(e) => setFormData({...formData, matricula: e.target.value})} style={{ padding: '8px', border: '1px solid #ddd', borderRadius: '4px', fontSize: '14px' }} />
              <input type="text" placeholder="Documento" value={formData.documento || ''} onChange={(e) => setFormData({...formData, documento: e.target.value})} style={{ padding: '8px', border: '1px solid #ddd', borderRadius: '4px', fontSize: '14px' }} />
              <input type="text" placeholder="Nombres" value={formData.nombres || ''} onChange={(e) => setFormData({...formData, nombres: e.target.value})} style={{ padding: '8px', border: '1px solid #ddd', borderRadius: '4px', fontSize: '14px' }} />
              <input type="text" placeholder="Apellidos" value={formData.apellidos || ''} onChange={(e) => setFormData({...formData, apellidos: e.target.value})} style={{ padding: '8px', border: '1px solid #ddd', borderRadius: '4px', fontSize: '14px' }} />
              <input type="email" placeholder="Correo" value={formData.correo || ''} onChange={(e) => setFormData({...formData, correo: e.target.value})} style={{ padding: '8px', border: '1px solid #ddd', borderRadius: '4px', fontSize: '14px' }} />
              <input type="text" placeholder="Teléfono" value={formData.telefono || ''} onChange={(e) => setFormData({...formData, telefono: e.target.value})} style={{ padding: '8px', border: '1px solid #ddd', borderRadius: '4px', fontSize: '14px' }} />
              <input type="text" placeholder="Sector" value={formData.sector || ''} onChange={(e) => setFormData({...formData, sector: e.target.value})} style={{ padding: '8px', border: '1px solid #ddd', borderRadius: '4px', fontSize: '14px' }} />
              <input type="text" placeholder="Dirección" value={formData.direccion || ''} onChange={(e) => setFormData({...formData, direccion: e.target.value})} style={{ padding: '8px', border: '1px solid #ddd', borderRadius: '4px', fontSize: '14px' }} />
              <input type="number" placeholder="Estrato" value={formData.estrato || ''} onChange={(e) => setFormData({...formData, estrato: e.target.value})} style={{ padding: '8px', border: '1px solid #ddd', borderRadius: '4px', fontSize: '14px' }} />
              <input type="number" placeholder="Nº de personas" value={formData.no_personas || ''} onChange={(e) => setFormData({...formData, no_personas: e.target.value})} style={{ padding: '8px', border: '1px solid #ddd', borderRadius: '4px', fontSize: '14px' }} />
            </div>

            <div style={{ display: 'flex', gap: '8px', justifyContent: 'flex-end' }}>
              <button onClick={() => setShowModal(false)} style={{ padding: '8px 16px', border: '1px solid #ddd', borderRadius: '4px', background: '#f3f4f6', cursor: 'pointer' }}>Cancelar</button>
              <button onClick={handleSave} style={{ padding: '8px 16px', background: '#0ea5a4', color: '#fff', borderRadius: '4px', border: 'none', cursor: 'pointer', fontWeight: 500 }}>Guardar</button>
            </div>
          </div>
        </div>
      )}

      {showDetailsModal && (
        <div style={{ position: 'fixed', top: 0, left: 0, right: 0, bottom: 0, background: 'rgba(0,0,0,0.5)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 1000 }}>
          <div style={{ background: '#fff', borderRadius: '10px', padding: '24px', width: '90%', maxWidth: '1000px', maxHeight: '80vh', overflowY: 'auto' }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '16px' }}>
              <h3 style={{ fontSize: '18px', fontWeight: 700 }}>
                Detalles: {subscriberDetails?.nombres} {subscriberDetails?.apellidos} ({subscriberDetails?.matricula})
              </h3>
              <button onClick={() => setShowDetailsModal(false)} style={{ fontSize: '20px', cursor: 'pointer', background: 'none', border: 'none' }}>✕</button>
            </div>

            {detailsLoading ? (
              <div style={{ textAlign: 'center', padding: '20px', color: '#666' }}>⏳ Cargando detalles...</div>
            ) : (
              <div>
                {/* CRÉDITOS */}
                <div style={{ marginBottom: '24px' }}>
                  <h4 style={{ fontSize: '14px', fontWeight: 700, color: '#0ea5a4', marginBottom: '8px', paddingBottom: '4px', borderBottom: '2px solid #0ea5a4' }}>📋 Créditos ({subscriberDetails?.credits?.length || 0})</h4>
                  {subscriberDetails?.credits && subscriberDetails.credits.length > 0 ? (
                    <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: '12px' }}>
                      <thead>
                        <tr style={{ background: '#f3f4f6' }}>
                          <th style={{ padding: '8px', textAlign: 'left', borderBottom: '1px solid #ddd', fontWeight: 600 }}>Monto</th>
                          <th style={{ padding: '8px', textAlign: 'left', borderBottom: '1px solid #ddd', fontWeight: 600 }}>Pagos</th>
                          <th style={{ padding: '8px', textAlign: 'left', borderBottom: '1px solid #ddd', fontWeight: 600 }}>Saldo</th>
                        </tr>
                      </thead>
                      <tbody>
                        {subscriberDetails.credits.map((c) => (
                          <tr key={c.id} style={{ borderBottom: '1px solid #ddd' }}>
                            <td style={{ padding: '8px' }}>${c.cantidad?.toLocaleString('es-CO')}</td>
                            <td style={{ padding: '8px' }}>{c.creditsPayments?.length || 0}</td>
                            <td style={{ padding: '8px', color: c.cantidad > (c.creditsPayments?.reduce((s, p) => s + p.valor, 0) || 0) ? '#dc2626' : '#16a34a' }}>
                              ${(c.cantidad - (c.creditsPayments?.reduce((s, p) => s + p.valor, 0) || 0)).toLocaleString('es-CO')}
                            </td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  ) : (
                    <p style={{ color: '#999', fontSize: '12px' }}>Sin créditos registrados</p>
                  )}
                </div>

                {/* LECTURAS */}
                <div style={{ marginBottom: '24px' }}>
                  <h4 style={{ fontSize: '14px', fontWeight: 700, color: '#0ea5a4', marginBottom: '8px', paddingBottom: '4px', borderBottom: '2px solid #0ea5a4' }}>📖 Lecturas ({subscriberDetails?.readings?.length || 0})</h4>
                  {subscriberDetails?.readings && subscriberDetails.readings.length > 0 ? (
                    <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: '12px' }}>
                      <thead>
                        <tr style={{ background: '#f3f4f6' }}>
                          <th style={{ padding: '8px', textAlign: 'left', borderBottom: '1px solid #ddd', fontWeight: 600 }}>Contador</th>
                          <th style={{ padding: '8px', textAlign: 'left', borderBottom: '1px solid #ddd', fontWeight: 600 }}>Consumo</th>
                          <th style={{ padding: '8px', textAlign: 'left', borderBottom: '1px solid #ddd', fontWeight: 600 }}>Valor Total</th>
                          <th style={{ padding: '8px', textAlign: 'left', borderBottom: '1px solid #ddd', fontWeight: 600 }}>Fecha</th>
                        </tr>
                      </thead>
                      <tbody>
                        {subscriberDetails.readings.sort((a, b) => new Date(b.fecha) - new Date(a.fecha)).map((r) => (
                          <tr key={r.id} style={{ borderBottom: '1px solid #ddd' }}>
                            <td style={{ padding: '8px' }}>{r.contador}</td>
                            <td style={{ padding: '8px' }}>{r.consumo} m³</td>
                            <td style={{ padding: '8px', fontWeight: 600 }}>${r.valorTotal?.toLocaleString('es-CO')}</td>
                            <td style={{ padding: '8px' }}>{new Date(r.fecha).toLocaleDateString('es-CO')}</td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  ) : (
                    <p style={{ color: '#999', fontSize: '12px' }}>Sin lecturas registradas</p>
                  )}
                </div>

                {/* FACTURAS */}
                <div style={{ marginBottom: '24px' }}>
                  <h4 style={{ fontSize: '14px', fontWeight: 700, color: '#0ea5a4', marginBottom: '8px', paddingBottom: '4px', borderBottom: '2px solid #0ea5a4' }}>💲 Facturas ({subscriberDetails?.invoices?.length || 0})</h4>
                  {subscriberDetails?.invoices && subscriberDetails.invoices.length > 0 ? (
                    <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: '12px' }}>
                      <thead>
                        <tr style={{ background: '#f3f4f6' }}>
                          <th style={{ padding: '8px', textAlign: 'left', borderBottom: '1px solid #ddd', fontWeight: 600 }}>ID</th>
                          <th style={{ padding: '8px', textAlign: 'left', borderBottom: '1px solid #ddd', fontWeight: 600 }}>Total</th>
                          <th style={{ padding: '8px', textAlign: 'left', borderBottom: '1px solid #ddd', fontWeight: 600 }}>Estado</th>
                          <th style={{ padding: '8px', textAlign: 'left', borderBottom: '1px solid #ddd', fontWeight: 600 }}>Fecha</th>
                        </tr>
                      </thead>
                      <tbody>
                        {subscriberDetails.invoices.sort((a, b) => new Date(b.fecha) - new Date(a.fecha)).map((inv) => (
                          <tr key={inv.id} style={{ borderBottom: '1px solid #ddd' }}>
                            <td style={{ padding: '8px' }}>#{inv.id}</td>
                            <td style={{ padding: '8px', fontWeight: 600 }}>${inv.total?.toLocaleString('es-CO')}</td>
                            <td style={{ padding: '8px' }}>
                              <span style={{ display: 'inline-block', padding: '2px 8px', borderRadius: '4px', fontSize: '11px', fontWeight: 600, background: inv.estado === 'paid' ? '#d1fae5' : inv.estado === 'pending' ? '#fef3c7' : '#fee2e2', color: inv.estado === 'paid' ? '#065f46' : inv.estado === 'pending' ? '#92400e' : '#7f1d1d' }}>
                                {inv.estado === 'paid' ? '✓ Pagada' : inv.estado === 'pending' ? '⏳ Pendiente' : '✕ Vencida'}
                              </span>
                            </td>
                            <td style={{ padding: '8px' }}>{new Date(inv.fecha).toLocaleDateString('es-CO')}</td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  ) : (
                    <p style={{ color: '#999', fontSize: '12px' }}>Sin facturas registradas</p>
                  )}
                </div>

                {/* ÚLTIMO PAGO */}
                {subscriberDetails?.credits && subscriberDetails.credits.length > 0 && (
                  <div style={{ background: '#ecfdf5', padding: '12px', borderRadius: '6px', borderLeft: '4px solid #10b981' }}>
                    <p style={{ fontSize: '12px', fontWeight: 600, color: '#065f46', margin: 0 }}>
                      💳 Último Pago: {subscriberDetails.credits.some(c => c.creditsPayments?.length > 0) 
                        ? new Date(Math.max(...subscriberDetails.credits.flatMap(c => c.creditsPayments || []).map(p => new Date(p.fecha)))).toLocaleDateString('es-CO')
                        : 'Sin pagos registrados'}
                    </p>
                  </div>
                )}
              </div>
            )}

            <div style={{ display: 'flex', gap: '8px', justifyContent: 'flex-end', marginTop: '16px' }}>
              <button onClick={() => setShowDetailsModal(false)} style={{ padding: '8px 16px', border: '1px solid #ddd', borderRadius: '4px', background: '#f3f4f6', cursor: 'pointer' }}>Cerrar</button>
            </div>
          </div>
        </div>
      )}
    </div>
  )
}
