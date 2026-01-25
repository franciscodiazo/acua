import { useEffect, useState } from 'react'
import Link from 'next/link'
import { formatCurrency } from '../../lib/utils'
import Pagination from '../../components/Pagination'

export default function Credits() {
  const [credits, setCredits] = useState([])
  const [page, setPage] = useState(1)
  const [totalPages, setTotalPages] = useState(1)

  useEffect(() => { fetchPage(1) }, [])

  async function fetchPage(p) {
    const res = await fetch(`/api/credits?page=${p}&limit=10`)
    const json = await res.json()
    setCredits(json.data || [])
    setPage(json.page || 1)
    setTotalPages(json.totalPages || 1)
  }

  return (
    <div className="max-w-4xl mx-auto py-6 px-4">
      <div className="flex items-center justify-between">
        <h2 className="text-xl font-semibold">Créditos</h2>
        <Link href="/credits/new" className="inline-flex items-center rounded bg-green-600 px-3 py-1 text-sm text-white hover:bg-green-700">Agregar crédito</Link>
      </div>

      <div className="mt-4 overflow-hidden rounded-lg border border-gray-200">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-4 py-3 text-left text-xs font-medium text-gray-500">ID</th>
              <th className="px-4 py-3 text-left text-xs font-medium text-gray-500">Matrícula</th>
              <th className="px-4 py-3 text-left text-xs font-medium text-gray-500">Monto</th>
              <th className="px-4 py-3 text-left text-xs font-medium text-gray-500">Descripción</th>
              <th className="px-4 py-3 text-left text-xs font-medium text-gray-500">Fecha</th>
              <th className="px-4 py-3 text-left text-xs font-medium text-gray-500">Acciones</th>
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-100">
            {credits.map(c => (
              <tr key={c.id} className="hover:bg-gray-50">
                <td className="px-4 py-2 text-sm text-gray-700">{c.id}</td>
                <td className="px-4 py-2 text-sm text-gray-700">{c.subscriber?.matricula}</td>
                <td className="px-4 py-2 text-sm text-gray-700">{formatCurrency(c.amount)}</td>
                <td className="px-4 py-2 text-sm text-gray-700">{c.description}</td>
                <td className="px-4 py-2 text-sm text-gray-700">{new Date(c.createdAt).toLocaleString()}</td>
                <td className="px-4 py-2 text-sm text-gray-700">{c.paymentsCount || 0} abonos ({formatCurrency(c.paymentsTotal || 0)})</td>
                <td className="px-4 py-2 text-sm text-gray-700"><a className="inline-flex items-center rounded bg-blue-600 px-3 py-1 text-sm text-white hover:bg-blue-700" href={`/credits/${c.id}`}>Abonar</a></td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <div className="flex justify-center mt-4">
        <Pagination page={page} totalPages={totalPages} onChange={fetchPage} />
      </div>
    </div>
  )
}
