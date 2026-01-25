import { PrismaClient } from '@prisma/client'

const prisma = new PrismaClient()

export default async function handler(req, res) {
  if (req.method !== 'POST') return res.status(405).json({ error: 'Only POST allowed' })

  try {
    const { subscribers } = req.body

    if (!Array.isArray(subscribers) || subscribers.length === 0) {
      return res.status(400).json({ error: 'No subscribers provided' })
    }

    const results = {
      created: 0,
      updated: 0,
      errors: []
    }

    for (const sub of subscribers) {
      try {
        // Validar campos obligatorios
        if (!sub.matricula || !sub.documento || !sub.nombres || !sub.apellidos) {
          results.errors.push({ matricula: sub.matricula, error: 'Faltan campos obligatorios' })
          continue
        }

        // Buscar si existe
        const existing = await prisma.subscriber.findUnique({ where: { matricula: sub.matricula } })

        if (existing) {
          // Actualizar
          await prisma.subscriber.update({
            where: { matricula: sub.matricula },
            data: {
              documento: sub.documento || existing.documento,
              apellidos: sub.apellidos || existing.apellidos,
              nombres: sub.nombres || existing.nombres,
              correo: sub.correo || existing.correo,
              estrato: sub.estrato ? parseInt(sub.estrato) : existing.estrato,
              telefono: sub.telefono || existing.telefono,
              sector: sub.sector || existing.sector,
              no_personas: sub.no_personas ? parseInt(sub.no_personas) : existing.no_personas,
              direccion: sub.direccion || existing.direccion
            }
          })
          results.updated++
        } else {
          // Crear nuevo
          await prisma.subscriber.create({
            data: {
              matricula: sub.matricula,
              documento: sub.documento,
              apellidos: sub.apellidos,
              nombres: sub.nombres,
              correo: sub.correo || null,
              estrato: sub.estrato ? parseInt(sub.estrato) : null,
              telefono: sub.telefono || null,
              sector: sub.sector || null,
              no_personas: sub.no_personas ? parseInt(sub.no_personas) : null,
              direccion: sub.direccion || null
            }
          })
          results.created++
        }
      } catch (e) {
        results.errors.push({ matricula: sub.matricula, error: e.message })
      }
    }

    res.setHeader('Cache-Control', 'no-cache, no-store, must-revalidate')
    return res.status(200).json({
      success: true,
      ...results,
      message: `${results.created} creados, ${results.updated} actualizados`
    })
  } catch (e) {
    console.error('Import error:', e)
    return res.status(500).json({ error: e.message })
  }
}
