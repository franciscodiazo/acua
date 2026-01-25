// CommonJS export to be compatible with Jest (require)
const { PrismaClient } = require('@prisma/client')

const globalForPrisma = globalThis

const prisma = globalForPrisma.prisma || new PrismaClient()
if (process.env.NODE_ENV === 'development') globalForPrisma.prisma = prisma

module.exports = prisma
