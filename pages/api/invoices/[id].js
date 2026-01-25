const prisma = require('../../../lib/prisma')

async function handler(req, res) {
  const { id } = req.query
  const invoiceId = Number(id)

  if (req.method === 'GET') {
    const invoice = await prisma.invoice.findUnique({
      where: { id: invoiceId },
      include: { reading: { include: { subscriber: true } } }
    })
    if (!invoice) return res.status(404).json({ error: 'Factura no encontrada' })
    
    const config = await prisma.config.findFirst()
    
    // Get credits and last payment for subscriber
    const subscriberId = invoice.reading.subscriberId
    const credits = await prisma.credit.findMany({
      where: { subscriberId },
      include: { payments: { orderBy: { createdAt: 'desc' } } }
    })
    
    const lastPayment = await prisma.creditPayment.findFirst({
      where: { credit: { subscriberId } },
      orderBy: { createdAt: 'desc' }
    })
    
    // Calculate total credit available
    const totalCredit = credits.reduce((sum, c) => sum + c.amount, 0)
    const totalPayments = credits.reduce((sum, c) => sum + c.payments.reduce((ps, p) => ps + p.amount, 0), 0)
    const creditAvailable = totalCredit - totalPayments
    
    return res.json({ 
      ...invoice, 
      config: config || {},
      credits,
      lastPayment,
      creditAvailable
    })
  }

  if (req.method === 'PATCH') {
    const { estado } = req.body
    if (!estado) return res.status(400).json({ error: 'estado requerido' })

    const existing = await prisma.invoice.findUnique({ where: { id: invoiceId } })
    if (!existing) return res.status(404).json({ error: 'Factura no encontrada' })

    const updated = await prisma.invoice.update({ where: { id: invoiceId }, data: { estado } })

    if (estado === 'facturado') {
      // marcar lectura asociada como facturado
      await prisma.reading.update({ where: { id: existing.readingId }, data: { estado: 'facturado' } })
    }

    return res.json(updated)
  }

  res.setHeader('Allow', ['GET', 'PATCH'])
  res.status(405).end(`Method ${req.method} Not Allowed`)
}

module.exports = handler
module.exports.default = handler
