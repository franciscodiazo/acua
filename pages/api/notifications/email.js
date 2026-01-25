export default async function handler(req, res) {
  if (req.method !== 'POST') return res.status(405).json({ error: 'Only POST' })

  try {
    const { email, subject, message } = req.body

    if (!email || !subject || !message) {
      return res.status(400).json({ error: 'Email, subject, y message son requeridos' })
    }

    // TODO: Implementar sendEmail con Nodemailer
    // Por ahora es un endpoint placeholder que registra la intención
    console.log(`Email notification: To=${email}, Subject=${subject}`)

    return res.json({
      success: true,
      message: 'Notificación registrada (envío real requiere configuración de SMTP)',
      queued: true
    })
  } catch (e) {
    return res.status(500).json({ error: e.message })
  }
}
