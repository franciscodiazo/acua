const prisma = require('../../../../lib/prisma')
const { generateInvoicePdf } = require('../../../../lib/invoicePdf')

async function handler(req, res) {
  const { id } = req.query
  const invoiceId = Number(id)

  const invoice = await prisma.invoice.findUnique({ where: { id: invoiceId }, include: { reading: { include: { subscriber: true } } } })
  if (!invoice) return res.status(404).json({ error: 'Factura no encontrada' })

  try {
    const pdfBuffer = await generateInvoicePdf(invoice)
    res.setHeader('Content-Type', 'application/pdf')
    res.setHeader('Content-Disposition', `attachment; filename=invoice-${invoice.id}.pdf`)
    res.status(200).send(pdfBuffer)
  } catch (e) {
    res.status(500).json({ error: e.message })
  }
}

module.exports = handler
module.exports.default = handler
