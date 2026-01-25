const prisma = require('../../../lib/prisma')

async function handler(req, res) {
  const id = Number(req.query.id)
  if (!id) return res.status(400).json({ error: 'ID inválido' })

  if (req.method === 'GET') {
    try {
      const credit = await prisma.credit.findUnique({ where: { id }, include: { subscriber: true, payments: true } })
      if (!credit) return res.status(404).json({ error: 'Crédito no encontrado' })
      return res.json(credit)
    } catch (e) {
      console.error(e)
      return res.status(500).json({ error: 'Error al obtener crédito' })
    }
  }

  if (req.method === 'POST') {
    try {
      const { amount } = req.body
      if (amount == null) return res.status(400).json({ error: 'amount obligatorio' })
      const credit = await prisma.credit.findUnique({ where: { id } })
      if (!credit) return res.status(404).json({ error: 'Crédito no encontrado' })
      const payAmount = Number(amount)
      if (payAmount <= 0) return res.status(400).json({ error: 'El monto debe ser mayor a cero' })
      if (payAmount > credit.amount) return res.status(400).json({ error: 'El monto a abonar supera el saldo del crédito' })

      // registrar pago
      const payment = await prisma.creditPayment.create({ data: { creditId: id, amount: payAmount } })

      // descontar del monto del crédito
      const updated = await prisma.credit.update({ where: { id }, data: { amount: credit.amount - payAmount } })

      return res.status(201).json({ payment, credit: updated })
    } catch (e) {
      console.error(e)
      return res.status(500).json({ error: 'Error al procesar abono' })
    }
  }

  res.setHeader('Allow', ['GET','POST'])
  res.status(405).end(`Method ${req.method} Not Allowed`)
}

module.exports = handler
module.exports.default = handler
