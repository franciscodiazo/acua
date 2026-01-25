const httpMocks = require('node-mocks-http')

describe('Payments API', () => {
  beforeEach(() => { jest.resetModules() })

  test('GET paginated payments', async () => {
    const prisma = require('../../lib/prisma')
    const mock = [{ id: 1, amount: 1000, creditId: 2, createdAt: new Date().toISOString(), credit: { subscriber: { matricula: 'M001' } } }]
    prisma.creditPayment = { count: jest.fn().mockResolvedValue(1), findMany: jest.fn().mockResolvedValue(mock) }

    const handler = require('../../pages/api/payments/index.js').default
    const req = httpMocks.createRequest({ method: 'GET', query: { page: '1', limit: '10' } })
    const res = httpMocks.createResponse()

    await handler(req, res)

    expect(res.statusCode).toBe(200)
    const json = res._getJSONData()
    expect(json.data).toHaveLength(1)
    expect(json.total).toBe(1)
  })
})