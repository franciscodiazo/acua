const prisma = require('../../../../lib/prisma')
const { generateInvoicePdf } = require('../../../../lib/invoicePdf')
const nodemailer = require('nodemailer')
const { generateInvoiceEmailHTML, generateInvoiceEmailText } = require('../../../../lib/emailTemplates')

async function handler(req, res) {
  if (req.method !== 'POST') return res.status(405).json({ error: 'Only POST' })

  const { id } = req.query
  const invoiceId = Number(id)
  const { to } = req.body || {}

  const invoice = await prisma.invoice.findUnique({ where: { id: invoiceId }, include: { reading: { include: { subscriber: true } } } })
  if (!invoice) return res.status(404).json({ error: 'Factura no encontrada' })

  const recipient = to || invoice.reading.subscriber?.correo
  if (!recipient) return res.status(400).json({ error: 'No hay correo del suscriptor, indicar "to" en body' })

  if (!process.env.SMTP_HOST || !process.env.SMTP_USER || !process.env.SMTP_PASS) return res.status(500).json({ error: 'SMTP no configurado en .env' })

  try {
    const pdfBuffer = await generateInvoicePdf(invoice)

    const transporter = nodemailer.createTransport({
      host: process.env.SMTP_HOST,
      port: Number(process.env.SMTP_PORT || 587),
      secure: process.env.SMTP_PORT == '465',
      auth: { user: process.env.SMTP_USER, pass: process.env.SMTP_PASS }
    })

    const from = process.env.FROM_EMAIL || process.env.SMTP_USER

    // derivar BASE_URL (puede configurarse en .env como BASE_URL)
    const host = req.headers.host
    const proto = req.headers['x-forwarded-proto'] || (process.env.NODE_ENV === 'production' ? 'https' : 'http')
    const baseUrl = process.env.BASE_URL || `${proto}://${host}`

    const html = generateInvoiceEmailHTML(invoice, baseUrl)
    const text = generateInvoiceEmailText(invoice)

    await transporter.sendMail({
      from,
      to: recipient,
      subject: `Factura #${invoice.id} - Acueducto Rural`,
      text,
      html,
      attachments: [{ filename: `invoice-${invoice.id}.pdf`, content: pdfBuffer }]
    })

    const updated = await prisma.invoice.update({ where: { id: invoiceId }, data: { estado: 'enviado' } })

    res.json({ success: true, estado: updated.estado })
  } catch (e) {
    res.status(500).json({ error: e.message })
  }
}

module.exports = handler
module.exports.default = handler
