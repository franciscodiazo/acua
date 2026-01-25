describe('generateInvoicePdf', () => {
  beforeEach(() => { jest.resetModules() })

  test('generates a PDF buffer for an invoice', async () => {
    const prisma = require('../../lib/prisma')
    prisma.config = { findFirst: jest.fn().mockResolvedValue({ name: 'Acueducto Rural', nit: '900123456-7', phone: '3100000000', address: 'C/ 1', logoUrl: '' }) }

    const { generateInvoicePdf } = require('../../lib/invoicePdf')

    const invoice = {
      id: 123,
      createdAt: new Date().toISOString(),
      dueDate: new Date().toISOString(),
      total: 44681,
      tarifaBasica: 25000,
      adicional: 0,
      valorConsumo: 420,
      discount: 20563,
      reading: {
        contador: 'CNT-1',
        lecturaAnterior: 100,
        lecturaActual: 520,
        subscriber: { matricula: 'M001', nombres: 'Juan', apellidos: 'Pérez', direccion: 'Calle 1' }
      }
    }

    const buf = await generateInvoicePdf(invoice)
    expect(buf).toBeInstanceOf(Buffer)
    expect(buf.length).toBeGreaterThan(2000)
  })
})