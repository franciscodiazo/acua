const prisma = require('../../../lib/prisma')

async function handler(req, res) {
  if (req.method === 'POST') {
    try {
      const { subscriberId, lecturaActual, ciclo } = req.body

      if (!subscriberId || lecturaActual === undefined) {
        return res.status(400).json({ error: 'subscriberId y lecturaActual son requeridos' })
      }

      // Get config for tariffs
      const config = await prisma.config.findFirst()
      const basicTariff = config?.basicTariff || 25000
      const threshold = config?.threshold || 40
      const unitPrice = config?.unitPrice || 1500

      // Get previous reading for this subscriber
      const previousReading = await prisma.reading.findFirst({
        where: { subscriberId: Number(subscriberId) },
        orderBy: { fecha: 'desc' }
      })

      // Lectura anterior: 0 si no existe, o lecturaActual de la lectura anterior
      const lecturaAnterior = previousReading?.lecturaActual || 0
      const consumo = Math.max(0, Number(lecturaActual) - lecturaAnterior)

      // Calculate charges
      const additionalCharge = consumo > threshold ? (consumo - threshold) * unitPrice : 0
      const valorTotal = basicTariff + additionalCharge

      return res.json({
        lecturaAnterior,
        lecturaActual: Number(lecturaActual),
        consumo,
        basicTariff,
        threshold,
        unitPrice,
        additionalCharge,
        valorTotal
      })
    } catch (e) {
      return res.status(500).json({ error: e.message })
    }
  }

  res.setHeader('Allow', ['POST'])
  res.status(405).end(`Method ${req.method} Not Allowed`)
}

module.exports = handler
module.exports.default = handler
