import { useRouter } from 'next/router'
import { useEffect, useState } from 'react'
import { formatCurrency } from '../../lib/utils'

const AcuaLogo = () => (
  <svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
    <circle cx="30" cy="30" r="28" fill="#f0f9ff" stroke="#0ea5a0" strokeWidth="2"/>
    {/* Water droplet */}
    <path d="M30 10C30 10 25 18 25 24C25 29.5228 27.2386 32 30 32C32.7614 32 35 29.5228 35 24C35 18 30 10 30 10Z" fill="#0ea5a0"/>
    {/* Leaf */}
    <path d="M38 18C38 18 42 22 42 28C42 32 40 36 36 36C32 36 30 32 30 28" fill="none" stroke="#10b981" strokeWidth="2" strokeLinecap="round"/>
    <circle cx="40" cy="20" r="2" fill="#10b981"/>
  </svg>
)

export default function InvoiceDetail() {
  const router = useRouter()
  const { id } = router.query
  const [invoice, setInvoice] = useState(null)
  const [config, setConfig] = useState(null)
  const [credits, setCredits] = useState([])
  const [lastPayment, setLastPayment] = useState(null)
  const [creditAvailable, setCreditAvailable] = useState(0)
  const [error, setError] = useState(null)
  const [loading, setLoading] = useState(true)
  const [downloading, setDownloading] = useState(false)
  const [sending, setSending] = useState(false)

  useEffect(() => {
    if (!id) return
    setLoading(true)
    fetch(`/api/invoices/${id}`)
      .then(r => r.json())
      .then(data => {
        if (data.error) setError(data.error)
        else {
          setInvoice(data)
          setConfig(data.config)
          setCredits(data.credits || [])
          setLastPayment(data.lastPayment || null)
          setCreditAvailable(data.creditAvailable || 0)
        }
      })
      .catch(e => setError(e.message))
      .finally(() => setLoading(false))
  }, [id])

  async function markFacturado() {
    if (!confirm('¿Marcar factura como pagada?')) return
    try {
      const res = await fetch(`/api/invoices/${id}`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ estado: 'pagado' })
      })
      const data = await res.json()
      if (!res.ok) setError(data.error || 'Error')
      else setInvoice({ ...invoice, estado: 'pagado' })
    } catch (e) {
      setError(e.message)
    }
  }

  async function downloadPdf() {
    setDownloading(true)
    setError(null)
    try {
      const res = await fetch(`/api/invoices/${id}/pdf`)
      if (!res.ok) {
        const data = await res.json()
        setError(data.error || 'Error al generar PDF')
        return
      }
      const blob = await res.blob()
      const url = URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `recibo-${invoice.id}.pdf`
      document.body.appendChild(a)
      a.click()
      a.remove()
      URL.revokeObjectURL(url)
    } catch (e) {
      setError(e.message)
    } finally {
      setDownloading(false)
    }
  }

  async function sendEmailTo(to) {
    setSending(true)
    setError(null)
    try {
      const res = await fetch(`/api/invoices/${id}/send-email`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ to })
      })
      const data = await res.json()
      if (!res.ok) {
        setError(data.error || 'Error al enviar')
      } else {
        alert('✓ Recibo enviado exitosamente')
        setInvoice({ ...invoice, estado: data.estado || invoice.estado })
      }
    } catch (e) {
      setError(e.message)
    } finally {
      setSending(false)
    }
  }

  async function sendEmail() {
    const to = invoice?.reading?.subscriber?.correo
    if (!to) {
      const input = prompt('No hay correo registrado. Ingresa un correo para enviar:')
      if (!input) return
      return await sendEmailTo(input)
    }
    await sendEmailTo(to)
  }

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <p className="text-gray-600">Cargando recibo...</p>
      </div>
    )
  }

  if (error) {
    return (
      <div className="min-h-screen bg-gray-50">
        <div className="max-w-4xl mx-auto py-8 px-4">
          <div className="bg-red-50 border-l-4 border-red-500 p-6 rounded-lg">
            <p className="text-red-700 font-bold">⚠ Error</p>
            <p className="text-red-600">{error}</p>
          </div>
        </div>
      </div>
    )
  }

  if (!invoice) return null

  const r = invoice.reading
  const s = r.subscriber

  return (
    <div className="min-h-screen bg-gray-50">
      <div className="max-w-4xl mx-auto py-8 px-4">
        {/* Back Button */}
        <button
          onClick={() => router.back()}
          className="mb-6 text-teal-600 hover:text-teal-700 font-semibold flex items-center gap-2"
        >
          ← Volver
        </button>

        {/* Header Card */}
        <div className="bg-white rounded-lg shadow-md overflow-hidden mb-6 border-l-4 border-teal-500">
          <div className="bg-gradient-to-r from-teal-50 to-blue-50 p-6">
            <div className="flex items-center gap-4 mb-4">
              <AcuaLogo />
              <div>
                <h1 className="text-2xl font-bold text-gray-900">Recibo de Agua</h1>
                <p className="text-gray-600">#{invoice.id}</p>
              </div>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
              <div>
                <p className="text-sm text-gray-600">Estado</p>
                <span className={`inline-block px-4 py-2 rounded-full font-bold text-sm ${
                  invoice.estado === 'pagado'
                    ? 'bg-green-100 text-green-800'
                    : invoice.estado === 'facturado'
                    ? 'bg-blue-100 text-blue-800'
                    : 'bg-yellow-100 text-yellow-800'
                }`}>
                  {invoice.estado === 'pagado' ? '✓ PAGADO' : invoice.estado === 'facturado' ? '✓ FACTURADO' : '● PENDIENTE'}
                </span>
              </div>
              <div>
                <p className="text-sm text-gray-600">Fecha</p>
                <p className="font-bold text-gray-900">{new Date(invoice.fecha).toLocaleDateString('es-CO')}</p>
              </div>
              <div>
                <p className="text-sm text-gray-600">Total a Pagar</p>
                <p className="text-2xl font-bold text-teal-600">{formatCurrency(invoice.total)}</p>
              </div>
            </div>
          </div>
        </div>

        {/* Company Info */}
        {config && config.name && (
          <div className="bg-white rounded-lg shadow-md p-6 mb-6 border-l-4 border-blue-500">
            <h3 className="text-lg font-bold text-gray-900 mb-4">Información de la Empresa</h3>
            <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <p className="text-sm text-gray-600">Empresa</p>
                <p className="font-bold text-gray-900">{config.name}</p>
              </div>
              <div>
                <p className="text-sm text-gray-600">NIT</p>
                <p className="font-bold text-gray-900">{config.nit || '-'}</p>
              </div>
              <div>
                <p className="text-sm text-gray-600">Teléfono</p>
                <p className="font-bold text-gray-900">{config.phone || '-'}</p>
              </div>
              <div>
                <p className="text-sm text-gray-600">Dirección</p>
                <p className="font-bold text-gray-900 text-sm">{config.address || '-'}</p>
              </div>
            </div>
          </div>
        )}

        {/* Subscriber Info */}
        <div className="bg-white rounded-lg shadow-md p-6 mb-6 border-l-4 border-purple-500">
          <h3 className="text-lg font-bold text-gray-900 mb-4">Datos del Suscriptor</h3>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <p className="text-sm text-gray-600 mb-1">Nombres</p>
              <p className="text-lg font-bold text-gray-900">{s.nombres} {s.apellidos}</p>
            </div>
            <div>
              <p className="text-sm text-gray-600 mb-1">Matrícula</p>
              <p className="text-lg font-bold text-gray-900 bg-purple-100 px-3 py-2 rounded-lg inline-block">{s.matricula}</p>
            </div>
            <div className="md:col-span-2">
              <p className="text-sm text-gray-600 mb-1">Dirección</p>
              <p className="text-gray-900">{s.direccion || '-'}</p>
            </div>
            {s.correo && (
              <div className="md:col-span-2">
                <p className="text-sm text-gray-600 mb-1">Correo Electrónico</p>
                <p className="text-gray-900">{s.correo}</p>
              </div>
            )}
          </div>
        </div>

        {/* Consumption Details */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          {/* Readings */}
          <div className="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500">
            <h3 className="text-lg font-bold text-gray-900 mb-4">Lecturas del Medidor</h3>
            <div className="space-y-4">
              <div className="bg-orange-50 p-4 rounded-lg">
                <p className="text-sm text-gray-600">Lectura Anterior</p>
                <p className="text-2xl font-bold text-orange-900">{r.lecturaAnterior} <span className="text-lg">m³</span></p>
              </div>
              <div className="bg-orange-50 p-4 rounded-lg">
                <p className="text-sm text-gray-600">Lectura Actual</p>
                <p className="text-2xl font-bold text-orange-900">{r.lecturaActual} <span className="text-lg">m³</span></p>
              </div>
              <div className="bg-orange-100 p-4 rounded-lg border-2 border-orange-400">
                <p className="text-sm text-gray-600">Consumo</p>
                <p className="text-3xl font-bold text-orange-900">{r.consumo.toFixed(2)} <span className="text-lg">m³</span></p>
              </div>
            </div>
          </div>

          {/* Tariff Details */}
          <div className="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <h3 className="text-lg font-bold text-gray-900 mb-4">Ciclo y Tarificación</h3>
            <div className="space-y-4">
              <div className="bg-green-50 p-4 rounded-lg">
                <p className="text-sm text-gray-600">Ciclo de Facturación</p>
                <p className="text-2xl font-bold text-green-900">{r.ciclo}</p>
              </div>
              <div className="bg-green-50 p-4 rounded-lg">
                <p className="text-sm text-gray-600">Tarifa Básica</p>
                <p className="text-lg font-bold text-green-900">{formatCurrency(config?.basicTariff || 0)}</p>
              </div>
              <div className="bg-green-50 p-4 rounded-lg">
                <p className="text-sm text-gray-600">Umbral: {config?.threshold || 0}m³ | Adicional: {formatCurrency(config?.unitPrice || 0)}/m³</p>
                <p className="text-xs text-gray-600 mt-2">Moneda: {config?.currency || 'COP'}</p>
              </div>
            </div>
          </div>
        </div>

        {/* Credits and Payments */}
        {(creditAvailable > 0 || lastPayment) && (
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            {/* Available Credit */}
            {creditAvailable > 0 && (
              <div className="bg-white rounded-lg shadow-md p-6 border-l-4 border-emerald-500">
                <h3 className="text-lg font-bold text-gray-900 mb-4">💳 Crédito Disponible</h3>
                <div className="bg-emerald-50 p-4 rounded-lg">
                  <p className="text-sm text-gray-600 mb-2">Crédito disponible en la cuenta</p>
                  <p className="text-3xl font-bold text-emerald-600">{formatCurrency(creditAvailable)}</p>
                </div>
                {credits.length > 0 && (
                  <div className="mt-4">
                    <p className="text-xs text-gray-600 mb-2 font-bold">Historial de créditos:</p>
                    <div className="space-y-2">
                      {credits.slice(0, 3).map(credit => (
                        <div key={credit.id} className="text-xs text-gray-700 bg-gray-50 p-2 rounded">
                          <p>{credit.description || `Crédito #${credit.id}`}</p>
                          <p className="text-emerald-600 font-bold">{formatCurrency(credit.amount - credit.payments.reduce((sum, p) => sum + p.amount, 0))}</p>
                        </div>
                      ))}
                    </div>
                  </div>
                )}
              </div>
            )}

            {/* Last Payment */}
            {lastPayment && (
              <div className="bg-white rounded-lg shadow-md p-6 border-l-4 border-indigo-500">
                <h3 className="text-lg font-bold text-gray-900 mb-4">✓ Último Abono Registrado</h3>
                <div className="bg-indigo-50 p-4 rounded-lg">
                  <p className="text-sm text-gray-600 mb-2">Monto del abono</p>
                  <p className="text-3xl font-bold text-indigo-600">{formatCurrency(lastPayment.amount)}</p>
                </div>
                <div className="mt-4 text-sm text-gray-600">
                  <p className="mb-2"><strong>Fecha:</strong> {new Date(lastPayment.createdAt).toLocaleDateString('es-CO')}</p>
                  <p><strong>Hora:</strong> {new Date(lastPayment.createdAt).toLocaleTimeString('es-CO')}</p>
                </div>
              </div>
            )}
          </div>
        )}

        {/* Charges Breakdown */}
        <div className="bg-white rounded-lg shadow-md overflow-hidden mb-6">
          <div className="bg-gray-900 text-white px-6 py-4">
            <h3 className="text-lg font-bold">Desglose de Cargos</h3>
          </div>
          <div className="p-6">
            <div className="space-y-3">
              <div className="flex justify-between items-center py-3 border-b border-gray-200">
                <span className="text-gray-700">Tarifa Básica Mensual</span>
                <span className="font-bold text-gray-900">{formatCurrency(config?.basicTariff || 0)}</span>
              </div>
              {r.consumo > (config?.threshold || 0) && (
                <div className="flex justify-between items-center py-3 border-b border-gray-200">
                  <span className="text-gray-700">
                    Consumo Adicional ({(r.consumo - (config?.threshold || 0)).toFixed(2)} m³ × {formatCurrency(config?.unitPrice || 0)}/m³)
                  </span>
                  <span className="font-bold text-gray-900">
                    {formatCurrency((r.consumo - (config?.threshold || 0)) * (config?.unitPrice || 0))}
                  </span>
                </div>
              )}
              <div className="flex justify-between items-center py-4 mt-4 pt-4 border-t-2 border-gray-300 bg-teal-50 px-4 rounded-lg">
                <span className="text-lg font-bold text-gray-900">TOTAL A PAGAR</span>
                <span className="text-2xl font-bold text-teal-600">{formatCurrency(invoice.total)}</span>
              </div>
            </div>
          </div>
        </div>

        {/* Action Buttons */}
        <div className="flex flex-col sm:flex-row gap-4">
          <button
            onClick={downloadPdf}
            disabled={downloading}
            className={`flex-1 py-3 px-6 rounded-lg font-bold text-white transition shadow-md hover:shadow-lg ${
              downloading
                ? 'bg-gray-400 cursor-not-allowed'
                : 'bg-blue-600 hover:bg-blue-700'
            }`}
          >
            {downloading ? '⏳ Generando PDF...' : '⬇️ Descargar Recibo'}
          </button>

          <button
            onClick={sendEmail}
            disabled={sending}
            className={`flex-1 py-3 px-6 rounded-lg font-bold text-white transition shadow-md hover:shadow-lg ${
              sending
                ? 'bg-gray-400 cursor-not-allowed'
                : 'bg-purple-600 hover:bg-purple-700'
            }`}
          >
            {sending ? '📤 Enviando...' : '📧 Enviar por Correo'}
          </button>

          {invoice.estado !== 'pagado' && (
            <button
              onClick={markFacturado}
              className="flex-1 py-3 px-6 rounded-lg font-bold text-white bg-green-600 hover:bg-green-700 transition shadow-md hover:shadow-lg"
            >
              ✓ Marcar como Pagado
            </button>
          )}
        </div>

        {/* Status Message */}
        {invoice.estado === 'pagado' && (
          <div className="mt-6 bg-green-50 border-l-4 border-green-500 p-6 rounded-lg">
            <p className="text-green-700 font-bold">✓ Recibo Pagado</p>
            <p className="text-green-600 text-sm">Este recibo ha sido marcado como pagado.</p>
          </div>
        )}
      </div>
    </div>
  )
}
