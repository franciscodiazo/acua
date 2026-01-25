const { PrismaClient } = require('@prisma/client');
const prisma = new PrismaClient();

async function fixDates() {
  try {
    console.log('Reparando fechas inválidas...');
    
    await prisma.$executeRawUnsafe(`UPDATE subscriber SET createdAt = NOW() WHERE YEAR(createdAt) = 0 OR MONTH(createdAt) = 0 OR DAY(createdAt) = 0`);
    console.log('✓ Subscriber');
    
    await prisma.$executeRawUnsafe(`UPDATE reading SET fecha = NOW() WHERE YEAR(fecha) = 0 OR MONTH(fecha) = 0 OR DAY(fecha) = 0`);
    console.log('✓ Reading');
    
    await prisma.$executeRawUnsafe(`UPDATE credit SET createdAt = NOW() WHERE YEAR(createdAt) = 0 OR MONTH(createdAt) = 0 OR DAY(createdAt) = 0`);
    console.log('✓ Credit');
    
    await prisma.$executeRawUnsafe(`UPDATE invoice SET fecha = NOW() WHERE YEAR(fecha) = 0 OR MONTH(fecha) = 0 OR DAY(fecha) = 0`);
    console.log('✓ Invoice');
    
    console.log('✓ Todas las fechas han sido reparadas');
    process.exit(0);
  } catch (error) {
    console.error('Error:', error);
    process.exit(1);
  }
}

fixDates();
