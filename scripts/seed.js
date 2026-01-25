const prisma = require('../lib/prisma')

async function main() {
  console.log('🔁 Limpiando datos existentes...')
  await prisma.invoice.deleteMany().catch(() => {})
  await prisma.reading.deleteMany().catch(() => {})
  await prisma.credit.deleteMany().catch(() => {})
  await prisma.subscriber.deleteMany().catch(() => {})

  console.log('✨ Creando suscriptores de ejemplo...')
  const subs = []
  for (let i = 1; i <= 12; i++) {
    const s = await prisma.subscriber.create({
      data: {
        matricula: `M${String(i).padStart(3, '0')}`,
        documento: `D${1000 + i}`,
        apellidos: `Apellido${i}`,
        nombres: `Nombre${i}`,
        correo: `user${i}@example.com`,
        estrato: (i % 6) + 1,
        telefono: `3000000${i}`,
        sector: `Sector ${Math.ceil(i/3)}`,
        no_personas: (2 + (i % 4)),
        direccion: `Calle ${i} #${i}00`
      }
    })
    subs.push(s)
  }

  console.log('💧 Creando lecturas y facturas de ejemplo...')
  for (let i = 0; i < subs.length; i++) {
    const s = subs[i]
    const readingsCount = i % 3 === 0 ? 2 : 1
    for (let r = 0; r < readingsCount; r++) {
      const lecturaAnterior = 100 * (r + 1) + i
      const lecturaActual = lecturaAnterior + (20 + (i % 60))
      const consumo = lecturaActual - lecturaAnterior

      const BASIC = Number(process.env.BASIC_TARIFF || 25000)
      const TH = Number(process.env.THRESHOLD || 40)
      const UNIT = Number(process.env.UNIT_PRICE || 1500)

      const adicional = consumo > TH ? (consumo - TH) * UNIT : 0
      const total = BASIC + adicional

      const reading = await prisma.reading.create({ data: {
        subscriberId: s.id,
        matricula: s.matricula,
        contador: `CNT-${s.matricula}-${r+1}`,
        lecturaAnterior,
        lecturaActual,
        consumo,
        valorTotal: total,
        estado: 'pendiente'
      }})

      await prisma.invoice.create({ data: {
        readingId: reading.id,
        valorConsumo: consumo,
        tarifaBasica: BASIC,
        adicional,
        total,
        estado: 'pendiente'
      }})
    }
  }

  console.log('💳 Añadiendo algunos créditos...')
  for (let i = 0; i < 5; i++) {
    await prisma.credit.create({ data: { subscriberId: subs[i].id, amount: (i+1)*10000, description: 'Crédito inicial' } })
  }

  console.log('🔧 Creando roles por defecto...')
  const adminRole = await prisma.role.upsert({ where: { name: 'admin' }, update: {}, create: { name: 'admin', description: 'Administrador con acceso completo' } })
  const userRole = await prisma.role.upsert({ where: { name: 'user' }, update: {}, create: { name: 'user', description: 'Usuario normal' } })

  console.log('👤 Creando usuario admin (no incluye contraseña real — cámbiala en producción)')
  await prisma.user.upsert({ where: { email: 'admin@example.com' }, update: {}, create: { email: 'admin@example.com', name: 'Administrador', password: 'changeme', roleId: adminRole.id } })

  console.log('⚙️ Creando configuración por defecto...')
  await prisma.config.upsert({ where: { id: 1 }, update: {}, create: {
    name: 'Acueducto Rural',
    nit: '900123456-7',
    logoUrl: '',
    phone: '3100000000',
    address: 'Carrera 1 #1-01',
    basicTariff: Number(process.env.BASIC_TARIFF || 25000),
    threshold: Number(process.env.THRESHOLD || 40),
    unitPrice: Number(process.env.UNIT_PRICE || 1500),
    currency: 'COP'
  } })

  const totalSubs = await prisma.subscriber.count()
  const totalReadings = await prisma.reading.count()
  const totalInvoices = await prisma.invoice.count()
  const totalCredits = await prisma.credit.count()

  console.log(`✅ Seed completado: ${totalSubs} suscriptores, ${totalReadings} lecturas, ${totalInvoices} facturas, ${totalCredits} créditos`)
}

main()
  .catch(e => {
    console.error(e)
    process.exit(1)
  })
  .finally(async () => {
    await prisma.$disconnect()
  })
