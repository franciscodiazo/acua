const prisma = require('../../../lib/prisma')

async function handler(req, res) {
  if (req.method === 'GET') {
    const page = Number(req.query.page || 1)
    const limit = Math.min(Number(req.query.limit || 10), 100)
    const skip = (page - 1) * limit
    const search = req.query.search || ''
    const orderBy = req.query.orderBy || 'id'
    const orderDir = req.query.orderDir || 'desc'

    try {
      // Build search filter
      const where = search ? {
        OR: [
          { matricula: { contains: search } },
          { nombres: { contains: search } },
          { apellidos: { contains: search } },
          { correo: { contains: search } },
          { documento: { contains: search } }
        ]
      } : {}

      // Build orderBy
      const orderByObj = {}
      orderByObj[orderBy] = orderDir

      const [total, subscribers] = await Promise.all([
        prisma.subscriber.count({ where }),
        prisma.subscriber.findMany({ 
          where,
          select: {
            id: true, matricula: true, documento: true, apellidos: true, nombres: true,
            correo: true, estrato: true, telefono: true, sector: true, no_personas: true, direccion: true
          },
          skip, take: limit, orderBy: orderByObj
        })
      ])

      return res.json({ data: subscribers, total, page, totalPages: Math.ceil(total / limit) })
    } catch (e) {
      return res.status(500).json({ error: e.message })
    }
  }

  if (req.method === 'POST') {
    try {
      const { matricula, documento, apellidos, nombres, telefono, direccion, sector, correo, estrato, no_personas } = req.body
      if (!matricula || !documento || !apellidos || !nombres) return res.status(400).json({ error: 'matricula, documento, apellidos y nombres son obligatorios' })
      const payload = {
        matricula,
        documento,
        apellidos,
        nombres,
        telefono: telefono || null,
        direccion: direccion || null,
        sector: sector || null,
        correo: correo || null,
        estrato: estrato ? Number(estrato) : null,
        no_personas: no_personas ? Number(no_personas) : null
      }

      try {
        const subscriber = await prisma.subscriber.create({ data: payload })
        return res.status(201).json(subscriber)
      } catch (err) {
        // Prisma unique constraint error
        if (err.code === 'P2002' && err.meta && err.meta.target) {
          const field = Array.isArray(err.meta.target) ? err.meta.target.join(', ') : err.meta.target
          return res.status(409).json({ error: `Valor duplicado en campo(s): ${field}` })
        }
        throw err
      }
    } catch (e) {
      return res.status(500).json({ error: e.message })
    }
  }

  res.setHeader('Allow', ['GET', 'POST'])
  res.status(405).end(`Method ${req.method} Not Allowed`)
}

module.exports = handler
module.exports.default = handler
