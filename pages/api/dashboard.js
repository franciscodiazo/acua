const prisma = require('../../lib/prisma')

export default async function handler(req, res) {
  try {
    // Basic counts and config (including billed invoices)
    const [totalSubscribers, pendingInvoicesCount, pendingInvoicesAmountAgg, creditsCount, creditsSumAgg, paymentsSumAgg, invoicesBilledCount, invoicesBilledAmountAgg, config] = await Promise.all([
      prisma.subscriber.count(),
      prisma.invoice.count({ where: { estado: 'pendiente' } }),
      prisma.invoice.aggregate({ _sum: { total: true }, where: { estado: 'pendiente' } }),
      prisma.credit.count(),
      prisma.credit.aggregate({ _sum: { amount: true } }),
      prisma.creditPayment.aggregate({ _sum: { amount: true } }),
      prisma.invoice.count({ where: { estado: 'facturado' } }),
      prisma.invoice.aggregate({ _sum: { total: true }, where: { estado: 'facturado' } }),
      prisma.config.findFirst()
    ])

    const pendingInvoicesAmount = (pendingInvoicesAmountAgg && pendingInvoicesAmountAgg._sum && pendingInvoicesAmountAgg._sum.total) || 0
    const creditsSum = (creditsSumAgg && creditsSumAgg._sum && creditsSumAgg._sum.amount) || 0
    const paymentsSum = (paymentsSumAgg && paymentsSumAgg._sum && paymentsSumAgg._sum.amount) || 0
    const creditsBalance = creditsSum - paymentsSum
    const invoicesBilledAmount = (invoicesBilledAmountAgg && invoicesBilledAmountAgg._sum && invoicesBilledAmountAgg._sum.total) || 0

    // Totals with/without applying available credits
    const totalToPayWithCredits = Math.max(0, pendingInvoicesAmount - creditsBalance)
    const totalToPayWithoutCredits = pendingInvoicesAmount
    const totalCredits = creditsSum
    const totalPayments = paymentsSum
    const tariffs = {
      basicTariff: config?.basicTariff || 0,
      threshold: config?.threshold || 0,
      unitPrice: config?.unitPrice || 0
    }

    const lastUpdated = new Date().toISOString()

    // Monthly consumption for last 6 months
    const months = []
    const now = new Date()
    for (let i = 5; i >= 0; i--) {
      const m = new Date(now.getFullYear(), now.getMonth() - i, 1)
      months.push({ label: `${m.toLocaleString('es-CO',{month:'short'})} ${m.getFullYear()}`, from: new Date(m.getFullYear(), m.getMonth(), 1), to: new Date(m.getFullYear(), m.getMonth()+1, 1) })
    }

    const readingsLastMonths = await prisma.reading.findMany({ where: { fecha: { lte: new Date() } }, select: { consumo: true, fecha: true } })

    const monthlyConsumption = months.map(ms => {
      const total = readingsLastMonths.filter(r => new Date(r.fecha) >= ms.from && new Date(r.fecha) < ms.to).reduce((s,n) => s + (n.consumo || 0), 0)
      return { label: ms.label, value: total }
    })

    // Recent activities: readings, invoices, payments, subscribers, credits
    const [latestReadings, latestInvoices, latestPayments, latestSubscribers, latestCredits] = await Promise.all([
      prisma.reading.findMany({ 
        select: { id: true, ciclo: true, lecturaAnterior: true, lecturaActual: true, consumo: true, fecha: true, subscriber: { select: { nombres: true, matricula: true } } },
        orderBy: { fecha: 'desc' }, take: 6 
      }),
      prisma.invoice.findMany({ 
        select: { id: true, total: true, fecha: true, reading: { select: { subscriber: { select: { nombres: true } } } } },
        orderBy: { fecha: 'desc' }, take: 6 
      }),
      prisma.creditPayment.findMany({ 
        select: { id: true, amount: true, credit: { select: { subscriber: { select: { nombres: true } } } } },
        orderBy: { id: 'desc' }, take: 6 
      }),
      prisma.subscriber.findMany({ 
        select: { id: true, nombres: true, apellidos: true, matricula: true },
        orderBy: { id: 'desc' }, take: 6 
      }),
      prisma.credit.findMany({ 
        select: { id: true, amount: true, subscriber: { select: { nombres: true } } },
        orderBy: { id: 'desc' }, take: 6 
      })
    ])

    const activities = []
    latestReadings.forEach(r => activities.push({ type: 'reading', date: r.fecha.toISOString(), icon: '📋', title: `Lectura registrada para ${r.subscriber?.nombres || r.matricula}`, subtitle: `${r.consumo || 0} m³` }))
    latestInvoices.forEach(inv => activities.push({ type: 'invoice', date: new Date().toISOString(), icon: '💲', title: `Recibo #${inv.id}`, subtitle: `${inv.total || 0}` }))
    latestPayments.forEach(p => activities.push({ type: 'payment', date: new Date().toISOString(), icon: '✅', title: `Pago crédito #${p.id}`, subtitle: `${p.amount || 0}` }))
    latestSubscribers.forEach(s => activities.push({ type: 'subscriber', date: new Date().toISOString(), icon: '👤', title: `Nuevo suscriptor: ${s.nombres} ${s.apellidos}`, subtitle: s.matricula || '' }))
    latestCredits.forEach(c => activities.push({ type: 'credit', date: new Date().toISOString(), icon: '💳', title: `Crédito #${c.id} - ${c.subscriber?.nombres || ''}`, subtitle: `${c.amount || 0}` }))

    activities.sort((a,b) => new Date(b.date) - new Date(a.date))

    // Response with aggressive caching (5 minutes)
    res.setHeader('Cache-Control', 's-maxage=300, stale-while-revalidate=600')
    return res.json({
      dashboard: {
        totalSubscribers,
        monthlyConsumption,
        pendingInvoicesCount,
        pendingInvoicesAmount,
        invoicesBilledCount,
        invoicesBilledAmount,
        creditsCount,
        totalCredits,
        totalPayments,
        creditsBalance,
        totalToPayWithCredits,
        totalToPayWithoutCredits,
        tariffs,
        currency: config?.currency || 'COP',
        activity: activities.slice(0, 8),
        lastUpdated
      }
    })
  } catch (e) {
    console.error('API /api/dashboard error', e)
    return res.status(500).json({ error: 'Error obteniendo datos del dashboard' })
  }
}
