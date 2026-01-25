export default function Pagination({ page, totalPages, onChange }) {
  if (!totalPages || totalPages <= 1) return null
  const pages = []
  const start = Math.max(1, page - 2)
  const end = Math.min(totalPages, page + 2)
  for (let i = start; i <= end; i++) pages.push(i)

  return (
    <nav aria-label="pagination">
      <div className="inline-flex items-center space-x-2">
        <button className={`px-3 py-1 rounded ${page === 1 ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white border text-gray-700 hover:bg-gray-50'}`} onClick={() => onChange(page - 1)} disabled={page === 1}>Anterior</button>
        {start > 1 && <span className="px-2 text-gray-500">...</span>}
        {pages.map(p => (
          <button key={p} className={`px-3 py-1 rounded ${p === page ? 'bg-sky-600 text-white' : 'bg-white border text-gray-700 hover:bg-gray-50'}`} onClick={() => onChange(p)}>{p}</button>
        ))}
        {end < totalPages && <span className="px-2 text-gray-500">...</span>}
        <button className={`px-3 py-1 rounded ${page === totalPages ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-white border text-gray-700 hover:bg-gray-50'}`} onClick={() => onChange(page + 1)} disabled={page === totalPages}>Siguiente</button>
      </div>
    </nav>
  )
}