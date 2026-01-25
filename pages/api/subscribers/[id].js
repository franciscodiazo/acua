const prisma = require('../../../lib/prisma')

async function handler(req, res) {
  const { id } = req.query

  if (req.method === 'PATCH') {
    try {
      const { matricula, documento, apellidos, nombres, telefono, direccion, sector, correo, estrato, no_personas } = req.body
      const payload = {}
      if (matricula !== undefined) payload.matricula = matricula
      if (documento !== undefined) payload.documento = documento
      if (apellidos !== undefined) payload.apellidos = apellidos
      if (nombres !== undefined) payload.nombres = nombres
      if (telefono !== undefined) payload.telefono = telefono || null
      if (direccion !== undefined) payload.direccion = direccion || null
      if (sector !== undefined) payload.sector = sector || null
      if (correo !== undefined) payload.correo = correo || null
      if (estrato !== undefined) payload.estrato = estrato ? Number(estrato) : null
      if (no_personas !== undefined) payload.no_personas = no_personas ? Number(no_personas) : null

      const subscriber = await prisma.subscriber.update({
        where: { id: Number(id) },
        data: payload
      })
      return res.json(subscriber)
    } catch (e) {
      if (e.code === 'P2025') return res.status(404).json({ error: 'Suscriptor no encontrado' })
      return res.status(500).json({ error: e.message })
    }
  }

  if (req.method === 'DELETE') {
    try {
      await prisma.subscriber.delete({ where: { id: Number(id) } })
      return res.json({ success: true })
    } catch (e) {
      if (e.code === 'P2025') return res.status(404).json({ error: 'Suscriptor no encontrado' })
      return res.status(500).json({ error: e.message })
    }
  }

  res.setHeader('Allow', ['PATCH', 'DELETE'])
  res.status(405).end(`Method ${req.method} Not Allowed`)
}

module.exports = handler
module.exports.default = handler
