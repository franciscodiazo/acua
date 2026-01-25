const httpMocks = require('node-mocks-http')

describe('Credits API', () => {
  beforeEach(() => { jest.resetModules() })

  test('GET paginated credits', async () => {
    const prisma = require('../../lib/prisma')
    const mock = []
    for (let i = 1; i <= 2; i++) mock.push({ id: i, amount: i * 1000, description: 'test' })

    prisma.credit = { count: jest.fn().mockResolvedValue(6), findMany: jest.fn().mockResolvedValue(mock) }

    const handler = require('../../pages/api/credits/index.js').default
    const req = httpMocks.createRequest({ method: 'GET', query: { page: '1', limit: '2' } })
    const res = httpMocks.createResponse()

    await handler(req, res)

    expect(res.statusCode).toBe(200)
    const json = res._getJSONData()
    expect(json.data).toHaveLength(2)
    expect(json.total).toBe(6)
  })

  test('GET credit detail with payments', async () => {
    const prisma = require('../../lib/prisma')
    const creditMock = { id: 1, amount: 5000, description: 'x', subscriber: { matricula: 'M001' }, payments: [{ id: 1, amount: 1000 }] }
    prisma.credit = { findUnique: jest.fn().mockResolvedValue(creditMock) }

    const handler = require('../../pages/api/credits/[id].js').default
    const req = httpMocks.createRequest({ method: 'GET', query: { id: '1' } })
    const res = httpMocks.createResponse()

    await handler(req, res)

    expect(res.statusCode).toBe(200)
    const json = res._getJSONData()
    expect(json.id).toBe(1)
    expect(json.payments).toHaveLength(1)
  })

  test('POST apply payment reduces credit amount', async () => {
    const prisma = require('../../lib/prisma')
    prisma.credit = { findUnique: jest.fn().mockResolvedValue({ id: 2, amount: 2000 }), update: jest.fn().mockResolvedValue({ id: 2, amount: 1500 }) }
    prisma.creditPayment = { create: jest.fn().mockResolvedValue({ id: 5, amount: 500 }) }

    const handler = require('../../pages/api/credits/[id].js').default
    const req = httpMocks.createRequest({ method: 'POST', query: { id: '2' }, body: { amount: 500 } })
    const res = httpMocks.createResponse()

    await handler(req, res)

    expect(res.statusCode).toBe(201)
    const json = res._getJSONData()
    expect(json.payment.amount).toBe(500)
    expect(json.credit.amount).toBe(1500)
  })
})
