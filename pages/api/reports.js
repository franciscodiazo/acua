import { prisma } from '@/lib/prisma'

export default async function handler(req, res) {
  try {
    if (req.method === 'GET') {
      const { period = 'day', date } = req.query
      
      let startDate, endDate
      const now = new Date()
      
      if (period === 'day') {
        // Movimiento del día
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate())
        startDate = today
        endDate = new Date(today.getTime() + 24 * 60 * 60 * 1000)
      } else if (period === 'month') {
        // Movimiento del mes
        startDate = new Date(now.getFullYear(), now.getMonth(), 1)
        endDate = new Date(now.getFullYear(), now.getMonth() + 1, 1)
      } else if (period === 'year') {
        // Balance anual
        startDate = new Date(now.getFullYear(), 0, 1)
        endDate = new Date(now.getFullYear() + 1, 0, 1)
      } else if (period === 'custom' && date) {
        // Rango personalizado
        const [start, end] = date.split(',')
        startDate = new Date(start)
        endDate = new Date(end)
      }

      // Obtener lecturas (cobros)
      const readings = await prisma.reading.findMany({
        where: {
          fecha: {
            gte: startDate,
            lt: endDate
          }
        },
        include: {
          subscriber: true
        }
      })

      // Obtener pagos (si existen en invoices)
      const invoices = await prisma.invoice.findMany({
        where: {
          fecha: {
            gte: startDate,
            lt: endDate
          }
        },
        include: {
          reading: {
            include: {
              subscriber: true
            }
          }
        }
      })

      // Obtener créditos aplicados
      const credits = await prisma.credit.findMany({
        where: {
          createdAt: {
            gte: startDate,
            lt: endDate
          }
        },
        include: {
          subscriber: true
        }
      })

      // Calcular totales
      const totalInvoiced = readings.reduce((sum, r) => sum + (r.valorTotal || 0), 0)
      const totalPaid = invoices.reduce((sum, i) => sum + (i.total || 0), 0)
      const totalCredits = credits.reduce((sum, c) => sum + (c.amount || 0), 0)
      const balance = totalInvoiced - totalPaid - totalCredits

      res.status(200).json({
        period,
        startDate,
        endDate,
        readings: readings.length,
        invoices: invoices.length,
        totalInvoiced,
        totalPaid,
        totalCredits,
        balance,
        movimientos: {
          readings: readings.map(r => ({
            id: r.id,
            date: r.fecha,
            subscriber: r.subscriber.nombres + ' ' + r.subscriber.apellidos,
            type: 'Lectura',
            amount: r.valorTotal
          })),
          invoices: invoices.map(i => ({
            id: i.id,
            date: i.fecha,
            subscriber: i.reading.subscriber.nombres + ' ' + i.reading.subscriber.apellidos,
            type: 'Factura',
            amount: i.total
          })),
          credits: credits.map(c => ({
            id: c.id,
            date: c.createdAt,
            subscriber: c.subscriber.nombres + ' ' + c.subscriber.apellidos,
            type: 'Crédito',
            amount: -c.amount
          }))
        }
      })
    }
  } catch (error) {
    console.error('Error en reports:', error)
    res.status(500).json({ error: error.message })
  }
}
