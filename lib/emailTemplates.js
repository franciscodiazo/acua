export function generateInvoiceEmailHTML(invoice, baseUrl) {
  const s = invoice.reading.subscriber || {}
  const r = invoice.reading || {}
  const pdfUrl = `${(baseUrl || '').replace(/\/$/, '')}/api/invoices/${invoice.id}/pdf`

  return `<!doctype html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <style>
      body { font-family: Arial, Helvetica, sans-serif; color:#222 }
      .card { max-width:640px; margin:0 auto; border:1px solid #eee; padding:20px }
      h1 { color:#0b5fff }
      table { width:100%; border-collapse:collapse }
      td, th { padding:8px; border:1px solid #eee; text-align:left }
      .total { font-weight:700; font-size:1.1em }
      .btn { display:inline-block; padding:8px 12px; background:#0b5fff; color:#fff; text-decoration:none; border-radius:4px }
    </style>
  </head>
  <body>
    <div class="card">
      <h1>Factura #${invoice.id}</h1>
      <p>Fecha: ${new Date(invoice.createdAt).toLocaleString()}</p>

      <h3>Suscriptor</h3>
      <p>${s.matricula || ''} — ${s.nombres || ''} ${s.apellidos || ''}</p>
      ${s.direccion ? `<p>Dirección: ${s.direccion}</p>` : ''}
      ${s.telefono ? `<p>Tel: ${s.telefono}</p>` : ''}

      <h3>Lectura</h3>
      <p>Contador: ${r.contador || ''}</p>
      <p>Anterior: ${r.lecturaAnterior || ''} — Actual: ${r.lecturaActual || ''}</p>
      <p>Consumo: ${invoice.valorConsumo} m³</p>

      <h3>Detalle de cobro</h3>
      <table>
        <thead>
          <tr><th>Concepto</th><th>Valor</th></tr>
        </thead>
        <tbody>
          <tr><td>Tarifa básica</td><td>${formatCurrency(invoice.tarifaBasica)}</td></tr>
          <tr><td>Adicional</td><td>${formatCurrency(invoice.adicional)}</td></tr>
          <tr class="total"><td>Total</td><td>${formatCurrency(invoice.total)}</td></tr>
        </tbody>
      </table>

      <p style="margin-top:16px;">Puede descargar la factura en PDF con el siguiente enlace:</p>
      <p><a class="btn" href="${pdfUrl}">Descargar PDF</a></p>

      <p style="margin-top:20px; color:#555; font-size:0.9em">Gracias por su pago y por apoyar al acueducto rural.</p>
    </div>
  </body>
</html>`
}

export function generateInvoiceEmailText(invoice) {
  const s = invoice.reading.subscriber || {}
  const r = invoice.reading || {}
  return `Factura #${invoice.id}\nFecha: ${new Date(invoice.createdAt).toLocaleString()}\n\nSuscriptor: ${s.matricula || ''} - ${s.nombres || ''} ${s.apellidos || ''}\nLectura: Contador ${r.contador || ''}, Anterior ${r.lecturaAnterior || ''}, Actual ${r.lecturaActual || ''}\nConsumo: ${invoice.valorConsumo} m³\n\nTarifa básica: ${formatCurrency(invoice.tarifaBasica)}\nAdicional: ${formatCurrency(invoice.adicional)}\nTotal: ${formatCurrency(invoice.total)}\n\nGracias por su pago.`
}

function formatCurrency(n) {
  if (typeof n !== 'number') n = Number(n) || 0
  return n.toLocaleString('es-CO', { style: 'currency', currency: 'COP' })
}
