const prisma = require('../../../lib/prisma')

async function handler(req, res) {
  if (req.method === 'GET') {
    const page = Number(req.query.page || 1)
    const limit = Math.min(Number(req.query.limit || 10), 100)
    const skip = (page - 1) * limit

    const [total, invoices, config] = await Promise.all([
      prisma.invoice.count(),
      prisma.invoice.findMany({ 
        select: {
          id: true, total: true, estado: true, fecha: true,
          reading: { 
            select: {
              id: true, ciclo: true, lecturaAnterior: true, lecturaActual: true, consumo: true, valorTotal: true, fecha: true,
              subscriber: { select: { id: true, nombres: true, apellidos: true, matricula: true, direccion: true } }
            }
          }
        },
        skip, take: limit, orderBy: { fecha: 'desc' } 
      }),
      prisma.config.findFirst()
    ])

    return res.json({ data: invoices, config: config || {}, total, page, totalPages: Math.ceil(total / limit) })
  }
  res.setHeader('Allow', ['GET'])
  res.status(405).end(`Method ${req.method} Not Allowed`)
}

module.exports = handler
module.exports.default = handler
