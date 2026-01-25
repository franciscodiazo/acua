const httpMocks = require('node-mocks-http')

describe('Readings API', () => {
  beforeEach(() => { jest.resetModules() })

  test('GET paginated readings', async () => {
    const prisma = require('../../lib/prisma')
    const mockReadings = []
    for (let i = 1; i <= 3; i++) mockReadings.push({ id: i, matricula: `M${i}`, contador: `CNT-${i}`, consumo: i * 10, valorTotal: i * 1000, fecha: new Date().toISOString(), estado: 'pendiente' })

    prisma.reading = {
      count: jest.fn().mockResolvedValue(50),
      findMany: jest.fn().mockResolvedValue(mockReadings)
    }

    const handler = require('../../pages/api/readings/index.js').default
    const req = httpMocks.createRequest({ method: 'GET', query: { page: '1', limit: '3' } })
    const res = httpMocks.createResponse()

    await handler(req, res)

    expect(res.statusCode).toBe(200)
    const json = res._getJSONData()
    expect(json.data).toHaveLength(3)
    expect(json.total).toBe(50)
    expect(json.page).toBe(1)
  })

  test('POST creates reading and invoice', async () => {
    const prisma = require('../../lib/prisma')

    prisma.subscriber = { findUnique: jest.fn().mockResolvedValue({ id: 1, matricula: 'M1' }) }
    prisma.reading = { create: jest.fn().mockResolvedValue({ id: 10, consumo: 45, valorTotal: 25000 + (5 * 1500), estado: 'pendiente' }) }
    prisma.invoice = { create: jest.fn().mockResolvedValue({ id: 99, total: 32500, estado: 'pendiente' }) }

    const handler = require('../../pages/api/readings/index.js').default
    const req = httpMocks.createRequest({ method: 'POST', body: { matricula: 'M1', contador: 'CNT1', lecturaAnterior: 100, lecturaActual: 145 } })
    const res = httpMocks.createResponse()

    await handler(req, res)

    expect(res.statusCode).toBe(201)
    const json = res._getJSONData()
    expect(json).toHaveProperty('reading')
    expect(json).toHaveProperty('invoice')
    expect(prisma.reading.create).toHaveBeenCalled()
    expect(prisma.invoice.create).toHaveBeenCalled()
  })
})
