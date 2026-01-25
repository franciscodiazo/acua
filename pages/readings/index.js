import { useEffect, useState } from 'react'
import Pagination from '../../components/Pagination'

export default function ReadingsList() {
  const [subscribers, setSubscribers] = useState([])
  const [loading, setLoading] = useState(false)
  const [config, setConfig] = useState(null)
  const [page, setPage] = useState(1)
  const [totalPages, setTotalPages] = useState(1)
  const [lastReadings, setLastReadings] = useState({})
  const [allReadings, setAllReadings] = useState({})
  const [currentCycleReadings, setCurrentCycleReadings] = useState({})
  const [cicloActual, setCicloActual] = useState('2026-1')
  const [limit, setLimit] = useState(50)
  
  const [inlineReadings, setInlineReadings] = useState({})
  const [calculatingId, setCalculatingId] = useState(null)
  const [calculatedBySubscriber, setCalculatedBySubscriber] = useState({})
  
  const [showConfirmModal, setShowConfirmModal] = useState(false)
  const [pendingInvoice, setPendingInvoice] = useState(null)
  const [savingInvoice, setSavingInvoice] = useState(false)

  useEffect(() => { 
    fetchConfig()
    fetchSubscribers()
    // Cargar todas las lecturas una sola vez al montar el componente
    fetchLastReadings()
  }, [])

  useEffect(() => {
    if (subscribers.length > 0) {
      fetchLastReadings()
    }
  }, [cicloActual])

  async function fetchLastReadings() {
    try {
      const res = await fetch('/api/readings?limit=999999')
      const json = await res.json()
      const readings = json.data || []
      
      console.log('Fetched readings:', readings.length, readings)
      
      // Guardar todas las lecturas ordenadas por subscriber
      const allReadingsBySubscriber = {}
      readings.forEach(r => {
        const subId = String(r.subscriberId)
        if (!allReadingsBySubscriber[subId]) {
          allReadingsBySubscriber[subId] = []
        }
        allReadingsBySubscriber[subId].push(r)
      })
      
      console.log('All readings by subscriber:', allReadingsBySubscriber)
      
      // Ordenar por fecha descendente
      Object.keys(allReadingsBySubscriber).forEach(id => {
        allReadingsBySubscriber[id].sort((a, b) => new Date(b.fecha) - new Date(a.fecha))
      })
      
      // Obtener la lectura más reciente por subscriber
      const mostRecentReading = {}
      Object.keys(allReadingsBySubscriber).forEach(subscriberId => {
        if (allReadingsBySubscriber[subscriberId].length > 0) {
          mostRecentReading[subscriberId] = allReadingsBySubscriber[subscriberId][0]
        }
      })
      
      console.log('Most recent readings:', mostRecentReading)
      
      // Crear mapa de lectura del ciclo actual (para bloquear input si existe)
      const currentCycleReading = {}
      Object.keys(allReadingsBySubscriber).forEach(subscriberId => {
        const subReadings = allReadingsBySubscriber[subscriberId]
        const cycleReading = subReadings.find(r => r.ciclo === cicloActual)
        if (cycleReading) {
          currentCycleReading[subscriberId] = cycleReading
        }
      })
      
      setAllReadings(allReadingsBySubscriber)
      setLastReadings(mostRecentReading)
      setCurrentCycleReadings(currentCycleReading)
    } catch (e) {
      console.error('Error fetching last readings:', e)
    }
  }

  async function fetchSubscribers(p = 1) {
    setLoading(true)
    try {
      const res = await fetch(`/api/subscribers?page=${p}&limit=${limit}`)
      const json = await res.json()
      setSubscribers(json.data || [])
      setPage(json.page || 1)
      setTotalPages(json.totalPages || 1)
      // Cargar las lecturas cada vez que se cargan nuevos suscriptores
      await fetchLastReadings()
    } catch (e) {
      console.error('Error fetching subscribers:', e)
    }
    setLoading(false)
  }

  async function fetchConfig() {
    try {
      const res = await fetch('/api/config')
      const json = await res.json()
      setConfig(json.config)
    } catch (e) {
      console.error('Error fetching config:', e)
    }
  }

  async function handleCalculateInline(subscriber, lecturaActual) {
    if (!lecturaActual) {
      setCalculatedBySubscriber({...calculatedBySubscriber, [subscriber.id]: null})
      return
    }
    
    setCalculatingId(subscriber.id)
    try {
      const lastReading = lastReadings[subscriber.id]
      if (!lastReading) {
        console.warn(`No hay lecturas anteriores para ${subscriber.nombres}`)
      }
      
      const res = await fetch('/api/readings/calculate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          subscriberId: subscriber.id,
          lecturaActual: Number(lecturaActual),
          ciclo: cicloActual
        })
      })
      const json = await res.json()
      if (!res.ok) throw new Error(json.error)
      
      json.tieneLecturaAnterior = !!lastReading
      json.ultimoRegistro = lastReading
      
      setCalculatedBySubscriber({...calculatedBySubscriber, [subscriber.id]: json})
    } catch (e) {
      console.error(`Error calculando: ${e.message}`)
      alert(`Error al calcular: ${e.message}`)
      setCalculatedBySubscriber({...calculatedBySubscriber, [subscriber.id]: null})
    } finally {
      setCalculatingId(null)
    }
  }

  function handleOpenInvoiceModal(subscriber) {
    const calc = calculatedBySubscriber[subscriber.id]
    const lecturaActual = inlineReadings[subscriber.id]
    const lastReading = lastReadings[subscriber.id]
    
    if (!calc) {
      alert('Primero calcula la lectura')
      return
    }
    
    if (!lecturaActual) {
      alert('La lectura actual no puede estar vacia')
      return
    }
    
    if (Number(lecturaActual) < calc.lecturaAnterior) {
      alert(`La lectura actual (${lecturaActual}) no puede ser menor a la anterior (${calc.lecturaAnterior})`)
      return
    }
    
    setPendingInvoice({
      subscriber,
      ciclo: cicloActual,
      lecturaAnterior: calc.lecturaAnterior,
      lecturaActual: calc.lecturaActual,
      consumo: calc.consumo,
      basicTariff: calc.basicTariff,
      threshold: calc.threshold,
      unitPrice: calc.unitPrice,
      additionalCharge: calc.additionalCharge,
      valorTotal: calc.valorTotal,
      fecha: new Date().toISOString().split('T')[0],
      tieneLecturaAnterior: calc.tieneLecturaAnterior,
      ultimoRegistro: calc.ultimoRegistro
    })
    setShowConfirmModal(true)
  }

  async function handleConfirmInvoice() {
    if (!pendingInvoice) return
    
    try {
      setSavingInvoice(true)
      
      const payload = {
        subscriberId: pendingInvoice.subscriber.id,
        ciclo: pendingInvoice.ciclo,
        lecturaActual: pendingInvoice.lecturaActual,
        consumo: pendingInvoice.consumo,
        valorTotal: pendingInvoice.valorTotal,
        fecha: pendingInvoice.fecha
      }
      
      const res = await fetch('/api/readings', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      })
      
      const json = await res.json()
      
      if (!res.ok) throw new Error(json.error)
      
      setShowConfirmModal(false)
      setPendingInvoice(null)
      setInlineReadings({...inlineReadings, [pendingInvoice.subscriber.id]: ''})
      setCalculatedBySubscriber({...calculatedBySubscriber, [pendingInvoice.subscriber.id]: null})
      
      await fetchLastReadings()
      alert('Lectura y factura registradas exitosamente')
    } catch (e) {
      console.error(`Error confirmando factura: ${e.message}`)
      alert(`Error: ${e.message}`)
    } finally {
      setSavingInvoice(false)
    }
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <div className="max-w-7xl mx-auto py-8 px-4">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-3xl font-bold text-gray-900 mb-2">Registrar Lecturas</h1>
          <p className="text-gray-600">Ingresa nuevas lecturas de contadores y genera facturas</p>
        </div>

        {/* Ciclo Input */}
        <div className="mb-6 bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
          <div className="flex items-center gap-4 mb-6">
            <span className="font-semibold text-gray-700 text-lg">Ciclo Actual:</span>
            <input
              type="text"
              value={cicloActual}
              onChange={(e) => setCicloActual(e.target.value)}
              className="px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg font-semibold"
              placeholder="Ej: 2026-1"
            />
          </div>
          <div className="flex items-center gap-4">
            <span className="font-semibold text-gray-700">Registros por página:</span>
            <select
              value={limit}
              onChange={(e) => {
                setLimit(Number(e.target.value))
                setPage(1)
              }}
              className="px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-semibold"
            >
              <option value={50}>50 registros</option>
              <option value={100}>100 registros</option>
              <option value={200}>200 registros</option>
              <option value={500}>500 registros</option>
            </select>
          </div>
        </div>

        {/* Table */}
        {loading ? (
          <div className="text-center py-8 text-gray-500">
            <p>Cargando suscriptores...</p>
          </div>
        ) : (
          <>
            <div className="bg-white rounded-lg shadow overflow-hidden">
              <table className="w-full">
                <thead className="bg-gradient-to-r from-gray-100 to-gray-50 border-b-2 border-gray-200">
                  <tr>
                    <th className="px-4 py-4 text-left text-sm font-bold text-gray-700">Matricula</th>
                    <th className="px-4 py-4 text-left text-sm font-bold text-gray-700">Suscriptor</th>
                    <th className="px-4 py-4 text-left text-sm font-bold text-gray-700">
                      <span className="text-orange-600">Ult. Lectura</span>
                    </th>
                    <th className="px-4 py-4 text-left text-sm font-bold text-gray-700">
                      <span className="text-blue-600">Lectura Anterior</span>
                    </th>
                    <th className="px-4 py-4 text-left text-sm font-bold text-gray-700">
                      <span className="text-green-600">Nueva Lectura</span>
                    </th>
                    <th className="px-4 py-4 text-left text-sm font-bold text-gray-700">
                      <span className="text-purple-600">Consumo</span>
                    </th>
                    <th className="px-4 py-4 text-left text-sm font-bold text-gray-700">Acciones</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-200">
                  {subscribers.map((sub) => {
                    const subId = String(sub.id)
                    const mostRecentReading = lastReadings[subId] // Lectura más reciente registrada
                    const allSubReadings = allReadings[subId] || [] // Todas las lecturas del subscriber
                    const currentCycleReading = currentCycleReadings[subId] // Lectura del ciclo actual (para bloquear)
                    const previousCycleReading = allSubReadings.length > 0 
                      ? allSubReadings.find(r => r.ciclo !== cicloActual) 
                      : null // Ultima lectura de ciclo anterior
                    
                    const calc = calculatedBySubscriber[sub.id]
                    const lecturaInput = inlineReadings[sub.id] || ''
                    const nombreCompleto = `${sub.nombres} ${sub.apellidos}`.trim()

                    return (
                      <tr key={sub.id} className="hover:bg-blue-50 transition">
                        <td className="px-4 py-4 text-sm font-bold">
                          <span className="bg-blue-100 text-blue-900 px-3 py-1 rounded-full text-center">{sub.matricula}</span>
                        </td>
                        <td className="px-4 py-4 text-sm">
                          <div className="font-bold text-gray-900">{nombreCompleto}</div>
                        </td>
                        
                        {/* Ultima Lectura (más reciente registrada) */}
                        <td className="px-4 py-4 text-sm">
                          {mostRecentReading ? (
                            <div className="bg-orange-50 p-4 rounded-lg border-l-4 border-orange-400 space-y-1">
                              <div className="text-xs font-bold text-orange-700 uppercase tracking-wide">Ciclo {mostRecentReading.ciclo}</div>
                              <div className="text-2xl font-bold text-orange-900">{mostRecentReading.lecturaActual}</div>
                              <div className="text-xs text-orange-600 font-medium">m3 - {new Date(mostRecentReading.fecha).toLocaleDateString()}</div>
                            </div>
                          ) : (
                            <div className="bg-yellow-50 p-3 rounded-lg border-l-4 border-yellow-300">
                              <div className="text-sm font-semibold text-yellow-800">Sin registros</div>
                            </div>
                          )}
                        </td>
                        
                        {/* Lectura Anterior */}
                        <td className="px-4 py-4 text-sm">
                          {calc?.lecturaAnterior !== undefined ? (
                            <div className="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-400 space-y-1">
                              <div className="text-xs font-bold text-blue-700 uppercase tracking-wide">{calc.lecturaAnterior === 0 ? 'Primera' : 'Ciclo Anterior'}</div>
                              <div className="text-2xl font-bold text-blue-900">{calc.lecturaAnterior}</div>
                              <div className="text-xs text-blue-600 font-medium">m3</div>
                            </div>
                          ) : previousCycleReading ? (
                            <div className="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-400 space-y-1">
                              <div className="text-xs font-bold text-blue-700 uppercase tracking-wide">Ciclo {previousCycleReading.ciclo}</div>
                              <div className="text-2xl font-bold text-blue-900">{previousCycleReading.lecturaActual}</div>
                              <div className="text-xs text-blue-600 font-medium">m3 - {new Date(previousCycleReading.fecha).toLocaleDateString()}</div>
                            </div>
                          ) : (
                            <div className="bg-yellow-50 p-3 rounded-lg border-l-4 border-yellow-300">
                              <div className="text-xs font-bold text-yellow-700 uppercase tracking-wide">Primera</div>
                              <div className="text-2xl font-bold text-yellow-900">0</div>
                              <div className="text-xs text-yellow-600 font-medium">m3</div>
                            </div>
                          )}
                        </td>
                        
                        {/* Nueva Lectura */}
                        <td className="px-4 py-4 text-sm">
                          <input
                            type="number"
                            value={lecturaInput}
                            onChange={(e) => setInlineReadings({...inlineReadings, [sub.id]: e.target.value})}
                            onBlur={() => handleCalculateInline(sub, lecturaInput)}
                            onKeyDown={(e) => e.key === 'Tab' && handleCalculateInline(sub, lecturaInput)}
                            disabled={currentCycleReading ? true : false}
                            className={`w-full px-3 py-2 border-2 rounded-lg focus:outline-none focus:ring-2 focus:border-transparent font-semibold ${
                              currentCycleReading 
                                ? 'bg-gray-100 border-gray-200 text-gray-500 cursor-not-allowed' 
                                : 'border-gray-300 focus:ring-green-500'
                            }`}
                            placeholder="0.00"
                            step="0.01"
                          />
                        </td>
                        
                        {/* Consumo */}
                        <td className="px-4 py-4 text-sm">
                          {calc ? (
                            <div className="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-400 space-y-1">
                              <div className="text-xs font-bold text-purple-700 uppercase tracking-wide">Consumo Ciclo</div>
                              <div className="text-2xl font-bold text-purple-900">{calc.consumo.toFixed(2)}</div>
                              <div className="text-xs text-purple-600 font-medium">m3 - {calc.lecturaActual} - {calc.lecturaAnterior}</div>
                            </div>
                          ) : (
                            <div className="bg-gray-50 p-3 rounded-lg border-l-4 border-gray-300">
                              <div className="text-xs font-bold text-gray-600 uppercase tracking-wide">Pendiente</div>
                              <div className="text-xl font-bold text-gray-700">-</div>
                            </div>
                          )}
                        </td>
                        
                        {/* Acciones */}
                        <td className="px-4 py-4 text-sm">
                          {currentCycleReading ? (
                            <div className="text-green-600 font-bold bg-green-50 p-2 rounded text-center">
                              Registrado
                            </div>
                          ) : calculatingId === sub.id ? (
                            <span className="text-blue-600 font-semibold">Calculando...</span>
                          ) : calc ? (
                            <button
                              onClick={() => handleOpenInvoiceModal(sub)}
                              className="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg text-sm transition shadow-md hover:shadow-lg"
                            >
                              Facturar
                            </button>
                          ) : (
                            <button
                              onClick={() => handleCalculateInline(sub, lecturaInput)}
                              disabled={!lecturaInput}
                              className={`font-bold py-2 px-4 rounded-lg text-sm transition ${
                                lecturaInput 
                                  ? 'bg-blue-600 hover:bg-blue-700 text-white cursor-pointer shadow-md hover:shadow-lg' 
                                  : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                              }`}
                            >
                              Calcular
                            </button>
                          )}
                        </td>
                      </tr>
                    )
                  })}
                </tbody>
              </table>
            </div>

            <div className="mt-6">
              <Pagination page={page} totalPages={totalPages} onChange={fetchSubscribers} />
            </div>
          </>
        )}
      </div>

      {/* Modal */}
      {showConfirmModal && pendingInvoice && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
          <div className="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div className="sticky top-0 bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6 border-b">
              <h2 className="text-2xl font-bold">Confirmar Lectura y Factura</h2>
              <p className="text-blue-100 mt-1">Ciclo: {pendingInvoice.ciclo}</p>
            </div>

            <div className="p-6 space-y-4">
              {/* Suscriptor */}
              <div className="bg-gray-50 p-4 rounded-lg border-l-4 border-gray-400">
                <h3 className="font-semibold text-gray-700 mb-2">Suscriptor</h3>
                <p className="text-gray-900 font-bold">{pendingInvoice.subscriber.nombres} {pendingInvoice.subscriber.apellidos}</p>
                <p className="text-sm text-gray-600">Matricula: {pendingInvoice.subscriber.matricula}</p>
              </div>

              {/* Ultima Lectura Registrada */}
              {pendingInvoice.tieneLecturaAnterior && pendingInvoice.ultimoRegistro && (
                <div className="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                  <h3 className="font-semibold text-blue-900 mb-2">Ultima Lectura Registrada</h3>
                  <div className="text-sm text-blue-800 space-y-1">
                    <p><strong>Ciclo:</strong> {pendingInvoice.ultimoRegistro.ciclo}</p>
                    <p><strong>Lectura Actual:</strong> {pendingInvoice.ultimoRegistro.lecturaActual} m3</p>
                    <p><strong>Fecha:</strong> {new Date(pendingInvoice.ultimoRegistro.fecha).toLocaleDateString()}</p>
                  </div>
                </div>
              )}

              {!pendingInvoice.tieneLecturaAnterior && (
                <div className="bg-yellow-50 p-4 rounded-lg border-l-4 border-yellow-500">
                  <h3 className="font-semibold text-yellow-900 mb-2">Primera Lectura</h3>
                  <p className="text-sm text-yellow-800">No hay ciclos anteriores. Se inicia en 0 m3</p>
                </div>
              )}

              {/* Calculo */}
              <div className="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500">
                <h3 className="font-semibold text-purple-900 mb-3">Calculo del Consumo</h3>
                <div className="space-y-2 text-sm text-purple-800">
                  <div className="flex justify-between">
                    <span>Lectura Anterior (ciclo pasado):</span>
                    <strong className="text-lg">{pendingInvoice.lecturaAnterior} m3</strong>
                  </div>
                  <div className="flex justify-between">
                    <span>Nueva Lectura (hoy):</span>
                    <strong className="text-lg">{pendingInvoice.lecturaActual} m3</strong>
                  </div>
                  <div className="border-t border-purple-300 pt-2 mt-2">
                    <div className="flex justify-between font-bold text-purple-900">
                      <span>Consumo = {pendingInvoice.lecturaActual} - {pendingInvoice.lecturaAnterior}</span>
                      <span className="text-lg text-purple-700">{pendingInvoice.consumo.toFixed(2)} m3</span>
                    </div>
                  </div>
                </div>
              </div>

              {/* Tarifa */}
              <div className="bg-amber-50 p-4 rounded-lg border-l-4 border-amber-500">
                <h3 className="font-semibold text-amber-900 mb-3">Desglose de Tarifa</h3>
                <div className="space-y-2 text-sm text-amber-800">
                  <div className="flex justify-between">
                    <span>Tarifa Base:</span>
                    <strong>${pendingInvoice.basicTariff.toLocaleString()}</strong>
                  </div>
                  {pendingInvoice.additionalCharge > 0 && (
                    <div className="flex justify-between">
                      <span>Excedente ({pendingInvoice.consumo - pendingInvoice.threshold} m3 x ${pendingInvoice.unitPrice}):</span>
                      <strong>${pendingInvoice.additionalCharge.toLocaleString()}</strong>
                    </div>
                  )}
                  <div className="border-t border-amber-300 pt-2 mt-2">
                    <div className="flex justify-between font-bold text-amber-900 text-lg">
                      <span>TOTAL A PAGAR:</span>
                      <span className="text-amber-700">${pendingInvoice.valorTotal.toLocaleString()}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {/* Botones */}
            <div className="bg-gray-50 p-6 border-t flex gap-3">
              <button
                onClick={() => {
                  setShowConfirmModal(false)
                  setPendingInvoice(null)
                }}
                disabled={savingInvoice}
                className="flex-1 px-4 py-2 bg-gray-300 text-gray-700 font-semibold rounded hover:bg-gray-400 transition disabled:opacity-50"
              >
                Cancelar
              </button>
              <button
                onClick={handleConfirmInvoice}
                disabled={savingInvoice}
                className="flex-1 px-4 py-2 bg-green-600 text-white font-semibold rounded hover:bg-green-700 transition disabled:opacity-50"
              >
                {savingInvoice ? 'Guardando...' : 'Confirmar y Facturar'}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  )
}
