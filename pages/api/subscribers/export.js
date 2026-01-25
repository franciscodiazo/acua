import { PrismaClient } from '@prisma/client'

const prisma = new PrismaClient()

export default async function handler(req, res) {
  if (req.method !== 'GET') return res.status(405).json({ error: 'Only GET allowed' })

  try {
    const subscribers = await prisma.subscriber.findMany({
      orderBy: { createdAt: 'desc' }
    })

    // Convertir a CSV
    const headers = ['matricula', 'documento', 'nombres', 'apellidos', 'correo', 'estrato', 'telefono', 'sector', 'no_personas', 'direccion']
    const rows = subscribers.map(s => [
      s.matricula,
      s.documento,
      s.nombres,
      s.apellidos,
      s.correo || '',
      s.estrato || '',
      s.telefono || '',
      s.sector || '',
      s.no_personas || '',
      s.direccion || ''
    ])

    const csv = [headers, ...rows]
      .map(row => row.map(cell => `"${(cell || '').toString().replace(/"/g, '""')}"`).join(','))
      .join('\n')

    res.setHeader('Content-Type', 'text/csv; charset=utf-8')
    res.setHeader('Content-Disposition', `attachment; filename="suscriptores_${new Date().toISOString().split('T')[0]}.csv"`)
    return res.status(200).send(csv)
  } catch (e) {
    console.error('Export error:', e)
    return res.status(500).json({ error: e.message })
  }
}
