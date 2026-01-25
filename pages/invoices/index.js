import Link from 'next/link'
import { useEffect, useState } from 'react'
import { formatCurrency } from '../../lib/utils'
import Pagination from '../../components/Pagination'

export default function Invoices() {
  const [invoices, setInvoices] = useState([])
  const [config, setConfig] = useState(null)
  const [page, setPage] = useState(1)
  const [totalPages, setTotalPages] = useState(1)

  useEffect(() => { fetchPage(1) }, [])

  async function fetchPage(p) {
    const res = await fetch(`/api/invoices?page=${p}&limit=10`)
    const json = await res.json()
    setInvoices(json.data || [])
    setConfig(json.config || {})
    setPage(json.page || 1)
    setTotalPages(json.totalPages || 1)
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <div className="max-w-7xl mx-auto py-8 px-4">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-3xl font-bold text-gray-900 mb-2">Recibos de Agua 📄</h1>
          <p className="text-gray-600">Listado de facturas generadas</p>
        </div>

        {/* Company Info Card */}
        {config && config.name && (
          <div className="mb-6 bg-white rounded-lg shadow-md p-6 border-l-4 border-teal-500">
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
                <p className="text-sm text-gray-600">Total Recaudado</p>
                <p className="font-bold text-teal-600 text-lg">
                  {formatCurrency(invoices.reduce((sum, inv) => sum + (inv.total || 0), 0))}
                </p>
              </div>
            </div>
          </div>
        )}

        {/* Table */}
        <div className="bg-white rounded-lg shadow-md overflow-hidden">
          <table className="w-full">
            <thead className="bg-gradient-to-r from-gray-100 to-gray-50 border-b-2 border-gray-200">
              <tr>
                <th className="px-6 py-4 text-left text-sm font-bold text-gray-700">ID</th>
                <th className="px-6 py-4 text-left text-sm font-bold text-gray-700">Matrícula</th>
                <th className="px-6 py-4 text-left text-sm font-bold text-gray-700">Suscriptor</th>
                <th className="px-6 py-4 text-left text-sm font-bold text-gray-700">Ciclo</th>
                <th className="px-6 py-4 text-left text-sm font-bold text-gray-700">Consumo (m³)</th>
                <th className="px-6 py-4 text-left text-sm font-bold text-gray-700">Total</th>
                <th className="px-6 py-4 text-left text-sm font-bold text-gray-700">Estado</th>
                <th className="px-6 py-4 text-left text-sm font-bold text-gray-700">Acción</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {invoices.map(inv => {
                const sub = inv.reading?.subscriber
                const reading = inv.reading
                return (
                  <tr key={inv.id} className="hover:bg-blue-50 transition">
                    <td className="px-6 py-4 text-sm font-bold">
                      <span className="bg-blue-100 text-blue-900 px-3 py-1 rounded-full">{inv.id}</span>
                    </td>
                    <td className="px-6 py-4 text-sm font-semibold text-gray-900">{sub?.matricula || '-'}</td>
                    <td className="px-6 py-4 text-sm">
                      <div className="font-bold text-gray-900">{sub?.nombres} {sub?.apellidos}</div>
                      <div className="text-xs text-gray-500">{sub?.direccion || '-'}</div>
                    </td>
                    <td className="px-6 py-4 text-sm font-semibold text-gray-900">{reading?.ciclo || '-'}</td>
                    <td className="px-6 py-4 text-sm">
                      <div className="bg-purple-50 px-3 py-2 rounded-lg">
                        <span className="font-bold text-purple-900">{reading?.consumo?.toFixed(2) || '0'}</span>
                        <span className="text-purple-600 text-xs ml-1">m³</span>
                      </div>
                    </td>
                    <td className="px-6 py-4 text-sm font-bold text-teal-600 text-lg">{formatCurrency(inv.total)}</td>
                    <td className="px-6 py-4 text-sm">
                      <span className={`px-3 py-1 rounded-full text-xs font-bold ${
                        inv.estado === 'pagado' 
                          ? 'bg-green-100 text-green-800' 
                          : 'bg-yellow-100 text-yellow-800'
                      }`}>
                        {inv.estado || 'pendiente'}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-sm">
                      <Link 
                        href={`/invoices/${inv.id}`} 
                        className="inline-flex items-center rounded-lg bg-teal-600 px-4 py-2 text-sm font-bold text-white hover:bg-teal-700 transition shadow-md hover:shadow-lg"
                      >
                        Ver →
                      </Link>
                    </td>
                  </tr>
                )
              })}
            </tbody>
          </table>
        </div>

        {/* Pagination */}
        <div className="flex justify-center mt-8">
          <Pagination page={page} totalPages={totalPages} onChange={fetchPage} />
        </div>
      </div>
    </div>
  )
}
