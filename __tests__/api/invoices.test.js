const httpMocks = require('node-mocks-http')

describe('Invoices API', () => {
  beforeEach(() => { jest.resetModules() })

  test('GET paginated invoices', async () => {
    const prisma = require('../../lib/prisma')
    const mockInv = []
    for (let i = 1; i <= 4; i++) mockInv.push({ id: i, valorConsumo: i * 10, total: i * 1000, estado: 'pendiente' })

    prisma.invoice = { count: jest.fn().mockResolvedValue(12), findMany: jest.fn().mockResolvedValue(mockInv) }

    const handler = require('../../pages/api/invoices/index.js').default
    const req = httpMocks.createRequest({ method: 'GET', query: { page: '2', limit: '4' } })
    const res = httpMocks.createResponse()

    await handler(req, res)

    expect(res.statusCode).toBe(200)
    const json = res._getJSONData()
    expect(json.data).toHaveLength(4)
    expect(json.total).toBe(12)
  })
})
