const httpMocks = require('node-mocks-http')

describe('GET /api/subscribers pagination', () => {
  beforeEach(() => {
    jest.resetModules()
  })

  test('returns paginated subscribers', async () => {
    const prisma = require('../../lib/prisma')

    // mock prisma methods
    const mockSubs = []
    for (let i = 1; i <= 5; i++) mockSubs.push({ id: i, matricula: `M${i}`, documento: `D${i}`, apellidos: `A${i}`, nombres: `N${i}`, correo: `u${i}@e.com`, estrato: i })

    prisma.subscriber = {
      count: jest.fn().mockResolvedValue(25),
      findMany: jest.fn().mockResolvedValue(mockSubs)
    }

    const handler = require('../../pages/api/subscribers/index.js').default

    const req = httpMocks.createRequest({ method: 'GET', query: { page: '2', limit: '5' } })
    const res = httpMocks.createResponse()

    await handler(req, res)

    expect(res.statusCode).toBe(200)
    const json = res._getJSONData()
    expect(json).toHaveProperty('data')
    expect(json.data).toHaveLength(5)
    expect(json.total).toBe(25)
    expect(json.page).toBe(2)
    expect(json.totalPages).toBe(Math.ceil(25 / 5))
  })
})
