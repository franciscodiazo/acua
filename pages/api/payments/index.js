const prisma = require('../../../lib/prisma')

async function handler(req, res) {
  if (req.method === 'GET') {
    try {
      const page = Number(req.query.page || 1)
      const limit = Math.min(Number(req.query.limit || 20), 200)
      const skip = (page - 1) * limit
      const where = {}
      if (req.query.creditId) where.creditId = Number(req.query.creditId)

      const [total, payments] = await Promise.all([
        prisma.creditPayment.count({ where }),
        prisma.creditPayment.findMany({ 
          where, 
          select: { 
            id: true, amount: true,
            credit: { select: { id: true, amount: true, subscriber: { select: { id: true, nombres: true, apellidos: true, matricula: true } } } }
          },
          skip, take: limit, orderBy: { id: 'desc' } 
        })
      ])

      return res.json({ data: payments, total, page, totalPages: Math.ceil(total / limit) })
    } catch (e) {
      console.error(e)
      return res.status(500).json({ error: 'Error al obtener pagos' })
    }
  }

  res.setHeader('Allow', ['GET'])
  res.status(405).end(`Method ${req.method} Not Allowed`)
}

module.exports = handler
module.exports.default = handler
