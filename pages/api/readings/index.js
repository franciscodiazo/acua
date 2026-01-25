const prisma = require('../../../lib/prisma')

async function handler(req, res) {
  if (req.method === 'GET') {
    const page = Number(req.query.page || 1)
    const limit = Math.min(Number(req.query.limit || 10), 100)
    const skip = (page - 1) * limit

    const [total, readings] = await Promise.all([
      prisma.reading.count(),
      prisma.reading.findMany({ 
        select: {
          id: true, subscriberId: true, ciclo: true, lecturaAnterior: true, lecturaActual: true, consumo: true, valorTotal: true, fecha: true, estado: true,
          subscriber: { select: { id: true, nombres: true, apellidos: true, matricula: true } }
        },
        skip, take: limit, orderBy: { fecha: 'desc' } 
      })
    ])

    return res.json({ data: readings, total, page, totalPages: Math.ceil(total / limit) })
  }

  if (req.method === 'POST') {
    try {
      const { subscriberId, ciclo, lecturaActual, consumo, valorTotal, fecha } = req.body
      
      if (!subscriberId) return res.status(400).json({ error: 'subscriberId es requerido' })
      if (!ciclo) return res.status(400).json({ error: 'ciclo es requerido' })
      if (lecturaActual == null) return res.status(400).json({ error: 'lecturaActual es requerido' })
      if (consumo == null) return res.status(400).json({ error: 'consumo es requerido' })
      if (valorTotal == null) return res.status(400).json({ error: 'valorTotal es requerido' })

      const subscriber = await prisma.subscriber.findUnique({ where: { id: Number(subscriberId) } })
      if (!subscriber) return res.status(404).json({ error: 'Suscriptor no encontrado' })

      // Validar que no exista ya una lectura para este ciclo
      const existingReading = await prisma.reading.findFirst({
        where: {
          subscriberId: Number(subscriberId),
          ciclo: ciclo
        }
      })

      if (existingReading) {
        return res.status(400).json({ error: `Ya existe una lectura registrada para el ciclo ${ciclo}` })
      }

      // Obtener lectura anterior
      const previousReading = await prisma.reading.findFirst({
        where: { subscriberId: Number(subscriberId) },
        orderBy: { fecha: 'desc' }
      })
      const lecturaAnterior = previousReading?.lecturaActual || 0

      const reading = await prisma.reading.create({
        data: {
          subscriberId: Number(subscriberId),
          ciclo,
          lecturaAnterior,
          lecturaActual: Number(lecturaActual),
          consumo: Number(consumo),
          valorTotal: Number(valorTotal),
          fecha: fecha ? new Date(fecha) : new Date(),
          estado: 'registrado'
        },
        select: {
          id: true,
          ciclo: true,
          lecturaAnterior: true,
          lecturaActual: true,
          consumo: true,
          valorTotal: true,
          fecha: true,
          estado: true,
          subscriber: { select: { id: true, nombres: true, apellidos: true, matricula: true } }
        }
      })

      // Auto-create invoice for this reading
      await prisma.invoice.create({
        data: {
          readingId: reading.id,
          total: Number(valorTotal),
          estado: 'pendiente',
          fecha: fecha ? new Date(fecha) : new Date()
        }
      })

      return res.status(201).json({ success: true, data: reading, message: 'Lectura guardada exitosamente' })
    } catch (e) {
      console.error('Error guardando lectura:', e)
      return res.status(500).json({ error: `Error al guardar: ${e.message}` })
    }
  }

  res.setHeader('Allow', ['GET', 'POST'])
  res.status(405).end(`Method ${req.method} Not Allowed`)
}

module.exports = handler
module.exports.default = handler
