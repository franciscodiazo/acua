const { PrismaClient } = require('@prisma/client')
const prisma = new PrismaClient()

async function fixDatesWithRawSQL() {
  try {
    console.log('🔧 Reparando fechas corruptas (versión 2)...')
    
    // Use raw SQL to find and fix invalid dates
    // MySQL stores invalid dates as '0000-00-00' or similar
    
    // Subscriber
    const result1 = await prisma.$executeRawUnsafe(`
      UPDATE Subscriber 
      SET createdAt = NOW() 
      WHERE createdAt = '0000-00-00 00:00:00' 
         OR createdAt IS NULL
         OR YEAR(createdAt) < 1900
    `)
    console.log(`✓ Suscriptores: ${result1} registros actualizados`)

    // Reading
    const result2 = await prisma.$executeRawUnsafe(`
      UPDATE Reading 
      SET fecha = NOW() 
      WHERE fecha = '0000-00-00 00:00:00' 
         OR fecha IS NULL
         OR YEAR(fecha) < 1900
    `)
    console.log(`✓ Lecturas: ${result2} registros actualizados`)

    // Invoice
    const result3 = await prisma.$executeRawUnsafe(`
      UPDATE Invoice 
      SET createdAt = NOW() 
      WHERE createdAt = '0000-00-00 00:00:00' 
         OR createdAt IS NULL
         OR YEAR(createdAt) < 1900
    `)
    console.log(`✓ Facturas: ${result3} registros actualizados`)

    // Credit
    const result4 = await prisma.$executeRawUnsafe(`
      UPDATE Credit 
      SET createdAt = NOW() 
      WHERE createdAt = '0000-00-00 00:00:00' 
         OR createdAt IS NULL
         OR YEAR(createdAt) < 1900
    `)
    console.log(`✓ Créditos: ${result4} registros actualizados`)

    // CreditPayment
    const result5 = await prisma.$executeRawUnsafe(`
      UPDATE CreditPayment 
      SET createdAt = NOW() 
      WHERE createdAt = '0000-00-00 00:00:00' 
         OR createdAt IS NULL
         OR YEAR(createdAt) < 1900
    `)
    console.log(`✓ Pagos: ${result5} registros actualizados`)

    console.log('✅ Reparación completada')
    
    // Now verify
    const subs = await prisma.subscriber.count()
    console.log(`\n📊 Total de suscriptores en BD: ${subs}`)
    
  } catch (e) {
    console.error('❌ Error:', e.message)
  } finally {
    await prisma.$disconnect()
  }
}

fixDatesWithRawSQL()
