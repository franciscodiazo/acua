import { useEffect, useState } from 'react'
import Link from 'next/link'
import { formatCurrency } from '../../lib/utils'
import Pagination from '../../components/Pagination'

export default function Payments() {
  const [payments, setPayments] = useState([])
  const [page, setPage] = useState(1)
  const [totalPages, setTotalPages] = useState(1)

  useEffect(() => { fetchPage(1) }, [])

  async function fetchPage(p) {
    const res = await fetch(`/api/payments?page=${p}&limit=20`)
    const json = await res.json()
    setPayments(json.data || [])
    setPage(json.page || 1)
    setTotalPages(json.totalPages || 1)
  }

  return (
    <div className="container max-w-4xl">
      <div className="flex items-center justify-between">
        <h2 className="h1">Abonos</h2>
        <Link href="/credits" className="btn">Ver créditos</Link>
      </div>

      <div className="mt-4 overflow-hidden rounded-lg border border-gray-200">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-4 py-3 text-left text-xs font-medium text-gray-500">ID</th>
              <th className="px-4 py-3 text-left text-xs font-medium text-gray-500">Crédito</th>
              <th className="px-4 py-3 text-left text-xs font-medium text-gray-500">Matrícula</th>
              <th className="px-4 py-3 text-left text-xs font-medium text-gray-500">Monto</th>
              <th className="px-4 py-3 text-left text-xs font-medium text-gray-500">Fecha</th>
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-100">
            {payments.map(p => (
              <tr key={p.id} className="hover:bg-gray-50">
                <td className="px-4 py-2 text-sm text-gray-700">{p.id}</td>
                <td className="px-4 py-2 text-sm text-gray-700"><a href={`/credits/${p.creditId}`} className="text-blue-600">#{p.creditId}</a></td>
                <td className="px-4 py-2 text-sm text-gray-700">{p.credit?.subscriber?.matricula}</td>
                <td className="px-4 py-2 text-sm text-gray-700">{formatCurrency(p.amount)}</td>
                <td className="px-4 py-2 text-sm text-gray-700">{new Date(p.createdAt).toLocaleString()}</td>
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
