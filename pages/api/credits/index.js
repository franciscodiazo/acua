const prisma = require('../../../lib/prisma')

async function handler(req, res) {
  if (req.method === 'GET') {
    const page = Number(req.query.page || 1)
    const limit = Math.min(Number(req.query.limit || 10), 100)
    const skip = (page - 1) * limit

    const [total, credits] = await Promise.all([
      prisma.credit.count(),
      prisma.credit.findMany({ 
        select: {
          id: true, amount: true,
          subscriber: { select: { id: true, nombres: true, apellidos: true, matricula: true } },
          payments: { select: { id: true, amount: true } }
        },
        skip, take: limit, orderBy: { id: 'desc' } 
      })
    ])

    // enrich credits with payments metadata
    const data = credits.map(c => ({
      ...c,
      paymentsCount: c.payments ? c.payments.length : 0,
      paymentsTotal: c.payments ? c.payments.reduce((s, p) => s + p.amount, 0) : 0
    }))

    return res.json({ data, total, page, totalPages: Math.ceil(total / limit) })
  }

  if (req.method === 'POST') {
    try {
      const { matricula, amount, description } = req.body
      if (!matricula || amount == null) return res.status(400).json({ error: 'matricula y amount obligatorios' })
      const subscriber = await prisma.subscriber.findUnique({ where: { matricula } })
      if (!subscriber) return res.status(404).json({ error: 'Suscriptor no encontrado' })
      const credit = await prisma.credit.create({ data: { subscriberId: subscriber.id, amount: Number(amount), description } })
      return res.status(201).json(credit)
    } catch (e) {
      return res.status(500).json({ error: e.message })
    }
  }

  res.setHeader('Allow', ['GET', 'POST'])
  res.status(405).end(`Method ${req.method} Not Allowed`)
}

module.exports = handler
module.exports.default = handler
