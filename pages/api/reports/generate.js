import jsPDF from 'jspdf'
import { PrismaClient } from '@prisma/client'

const prisma = new PrismaClient()

export default async function handler(req, res) {
  if (req.method !== 'GET') return res.status(405).json({ error: 'Only GET' })

  try {
    const { type = 'summary' } = req.query

    if (type === 'summary') {
      const [totalSubs, invoices, payments] = await Promise.all([
        prisma.subscriber.count(),
        prisma.invoice.findMany(),
        prisma.creditPayment.findMany()
      ])

      const totalInvoiced = invoices.reduce((s, i) => s + (i.total || 0), 0)
      const totalPaid = payments.reduce((s, p) => s + (p.amount || 0), 0)

      const doc = new jsPDF()
      doc.setFontSize(16)
      doc.text('Reporte de Acueducto Rural', 20, 20)

      doc.setFontSize(12)
      doc.text(`Fecha: ${new Date().toLocaleDateString('es-CO')}`, 20, 40)
      doc.text(`Total de Suscriptores: ${totalSubs}`, 20, 50)
      doc.text(`Facturas Emitidas: ${invoices.length}`, 20, 60)
      doc.text(`Total Facturado: $${totalInvoiced.toLocaleString()}`, 20, 70)
      doc.text(`Total Pagado: $${totalPaid.toLocaleString()}`, 20, 80)
      doc.text(`Saldo Pendiente: $${(totalInvoiced - totalPaid).toLocaleString()}`, 20, 90)

      res.setHeader('Content-Type', 'application/pdf')
      res.setHeader('Content-Disposition', `attachment; filename="reporte_${new Date().toISOString().split('T')[0]}.pdf"`)
      return res.send(doc.output('arraybuffer'))
    }

    return res.status(400).json({ error: 'Tipo de reporte no soportado' })
  } catch (e) {
    return res.status(500).json({ error: e.message })
  }
}
