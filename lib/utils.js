export function formatCurrency(n) {
  if (typeof n !== 'number') n = Number(n) || 0
  return n.toLocaleString('es-CO', { style: 'currency', currency: 'COP' })
}
