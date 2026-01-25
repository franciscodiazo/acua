const prisma = require('../../../lib/prisma')

module.exports = async (req, res) => {
  if (req.method === 'GET') {
    try {
      const cfg = await prisma.config.findFirst()
      return res.json({ config: cfg || {} })
    } catch (e) {
      console.error(e)
      return res.status(500).json({ error: 'Error al obtener configuración' })
    }
  }

  if (req.method === 'POST') {
    try {
      const { name, nit, phone, address, logoUrl, basicTariff, threshold, unitPrice, currency } = req.body
      
      const existing = await prisma.config.findFirst()
      let config

      if (existing) {
        config = await prisma.config.update({
          where: { id: existing.id },
          data: {
            name: name || existing.name,
            nit: nit || existing.nit,
            phone: phone || existing.phone,
            address: address || existing.address,
            logoUrl: logoUrl || existing.logoUrl,
            basicTariff: basicTariff !== undefined ? Number(basicTariff) : existing.basicTariff,
            threshold: threshold !== undefined ? Number(threshold) : existing.threshold,
            unitPrice: unitPrice !== undefined ? Number(unitPrice) : existing.unitPrice,
            currency: currency || existing.currency
          }
        })
      } else {
        config = await prisma.config.create({
          data: {
            name: name || 'Acueducto Rural',
            nit: nit || '',
            phone: phone || '',
            address: address || '',
            logoUrl: logoUrl || '',
            basicTariff: Number(basicTariff) || 25000,
            threshold: Number(threshold) || 40,
            unitPrice: Number(unitPrice) || 1500,
            currency: currency || 'COP'
          }
        })
      }

      res.setHeader('Cache-Control', 'no-cache, no-store, must-revalidate')
      return res.json({ success: true, config })
    } catch (e) {
      console.error(e)
      return res.status(500).json({ error: e.message })
    }
  }

  return res.status(405).json({ error: 'Only GET and POST allowed' })
}
