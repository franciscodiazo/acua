import { useEffect, useRef, useState } from 'react'
import useSWR from 'swr'
import { LineChart, Line, AreaChart, Area, BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer } from 'recharts'

const fetcher = (url) => fetch(url).then(r => r.json())

export default function Home({ dashboard = null }) {
  const [isHydrated, setIsHydrated] = useState(false)
  
  useEffect(() => {
    setIsHydrated(true)
  }, [])

  const fmtCurrency = (v) => {
    if (v == null) return null
    const currency = (remote && remote.currency) || (dashboard && dashboard.currency) || 'COP'
    try { return new Intl.NumberFormat('es-CO', { style: 'currency', currency, maximumFractionDigits: 0 }).format(v) } catch (e) { return `${v}` }
  }
  const subsValue = dashboard && dashboard.totalSubscribers != null ? dashboard.totalSubscribers : 'Ejemplo: 120'
  const subsSub = dashboard && dashboard.totalSubscribers != null ? `${dashboard.totalSubscribers} total registrados` : 'Ejemplo: 120 registrados'

  const timeAgo = (iso) => {
    if (!iso) return ''
    const d = new Date(iso)
    const diff = Math.floor((Date.now() - d.getTime()) / 1000)
    if (diff < 60) return `Hace ${diff}s`
    if (diff < 3600) return `Hace ${Math.floor(diff/60)}m`
    if (diff < 86400) return `Hace ${Math.floor(diff/3600)}h`
    return `Hace ${Math.floor(diff/86400)}d`
  }

  const TimeAgoText = ({ iso }) => {
    if (!isHydrated || !iso) return null
    return <span>{timeAgo(iso)}</span>
  }

  const ActivityItem = ({ item }) => {
    const typeIcons = { reading: '📋', invoice: '💲', payment: '✅', subscriber: '👤', credit: '💳' }
    const icon = typeIcons[item.type] || item.icon || 'ℹ️'
    return (
      <div className="activity-item">
        <div className="activity-icon">{icon}</div>
        <div>
          <div style={{fontWeight:700}}>{item.title}</div>
          <div className="small-muted">{item.subtitle} • <TimeAgoText iso={item.date} /></div>
        </div>
      </div>
    )
  }

  const ConsumptionChart = ({ series = [], height = 260 }) => {
    if (!series || series.length === 0) return <div className="small-muted">Gráfico de ejemplo (sin datos)</div>
    const data = series.map(s => ({ name: s.label, value: s.value }))
    return (
      <ResponsiveContainer width="100%" height={height}>
        <AreaChart data={data} margin={{ top: 10, right: 30, left: 0, bottom: 50 }}>
          <defs>
            <linearGradient id="colorValue" x1="0" y1="0" x2="0" y2="1">
              <stop offset="5%" stopColor="#0ea5a4" stopOpacity={0.8}/>
              <stop offset="95%" stopColor="#0ea5a4" stopOpacity={0}/>
            </linearGradient>
          </defs>
          <CartesianGrid strokeDasharray="3 3" stroke="rgba(0,0,0,0.05)" />
          <XAxis dataKey="name" fontSize={12} />
          <YAxis fontSize={12} />
          <Tooltip contentStyle={{ background: '#fff', border: '1px solid #e5e7eb', borderRadius: '8px' }} />
          <Area type="monotone" dataKey="value" stroke="#0ea5a4" fillOpacity={1} fill="url(#colorValue)" />
        </AreaChart>
      </ResponsiveContainer>
    )
  }

  const { data, error, isValidating } = useSWR('/api/dashboard', fetcher, { 
    fallbackData: { dashboard }, 
    revalidateOnFocus: false,
    revalidateOnReconnect: false,
    revalidateIfStale: false,
    dedupingInterval: 300000,
    focusThrottleInterval: 600000
  })
  const remote = data?.dashboard || dashboard
  const [applyCredits, setApplyCredits] = useState(true)

  // Tariffs and consumption examples
  const tariffsData = remote?.tariffs || dashboard?.tariffs || { basicTariff: 25000, threshold: 40, unitPrice: 1500 }
  const lowCons = Math.max(1, tariffsData.threshold - 10)
  const exactCons = tariffsData.threshold
  const highCons = tariffsData.threshold + 10
  const calc = (consumo) => {
    const adicional = consumo > tariffsData.threshold ? (consumo - tariffsData.threshold) * tariffsData.unitPrice : 0
    const total = (tariffsData.basicTariff || 0) + adicional
    return { consumo, adicional, total }
  }
  const c1 = calc(lowCons)
  const c2 = calc(exactCons)
  const c3 = calc(highCons)

  return (
    <div>
      <div className="page-header">
        <div>
          <div className="page-title">Dashboard</div>
          <div className="page-sub">Bienvenido al sistema de gestión del acueducto</div>
          {isHydrated && remote?.lastUpdated && <div className="small-muted">Última actualización: <TimeAgoText iso={remote.lastUpdated} /></div>}
        </div>
        <div className="row">
          <button className="btn btn-ghost">Exportar</button>
          <button className="btn btn-primary">Nuevo</button>
        </div>
      </div>

      <div className="metrics">
        <div className="metric metric-teal">
          <div className="label">Suscriptores Activos</div>
          <div className="value">{remote && remote.totalSubscribers != null ? remote.totalSubscribers : subsValue}</div>
          <div className="sub">{remote && remote.totalSubscribers != null ? `${remote.totalSubscribers} total registrados` : subsSub}</div>
        </div>
        <div className="metric metric-white">
          <div className="label">Consumo Total Ciclo</div>
          <div className="value">{remote && remote.monthlyConsumption ? `${remote.monthlyConsumption.reduce((s,m)=>s+m.value,0)} m³` : 'Ejemplo: 3,200 m³'}</div>
          <div className="sub">{remote && remote.monthlyConsumption ? 'Últimos 6 meses' : 'Ciclo de ejemplo'}</div>
        </div>
        <div className="metric metric-orange">
          <div className="label">Recibos Pendientes</div>
          <div className="value">{remote && remote.pendingInvoicesCount != null ? remote.pendingInvoicesCount : 'Ejemplo: 5'}</div>
          <div className="sub">{remote && remote.pendingInvoicesAmount != null ? fmtCurrency(remote.pendingInvoicesAmount) : '$12.000'}</div>
        </div>
        <div className="metric metric-green">
          <div className="label">Créditos Activos</div>
          <div className="value">{remote && remote.creditsCount != null ? remote.creditsCount : 'Ejemplo: 2'}</div>
          <div className="sub">Saldo: {remote && remote.creditsBalance != null ? fmtCurrency(remote.creditsBalance) : '$30.000'}</div>
        </div>
      </div>

      <div className="card card-wide" style={{marginTop:16}}>
        <div style={{display:'flex', gap:18}}>
          <div style={{flex:1, padding:18, background:'#fff', borderRadius:10}}>
            <div className="small-muted">Tarifas</div>
            <div style={{fontSize:16, fontWeight:700, marginTop:6}}>Básica: {remote && remote.tariffs ? fmtCurrency(remote.tariffs.basicTariff) : fmtCurrency((dashboard && dashboard.basicTariff) || 25000)}</div>
            <div className="small-muted">Umbral (m³): {remote && remote.tariffs ? remote.tariffs.threshold : (dashboard && dashboard.threshold) || 40}</div>
            <div className="small-muted">Precio por m³: {remote && remote.tariffs ? fmtCurrency(remote.tariffs.unitPrice) : fmtCurrency((dashboard && dashboard.unitPrice) || 1500)}</div>
          </div>

          <div style={{flex:1, padding:18, background:'#f8fafc', borderRadius:10}}>
            <div className="small-muted">Totales financieros</div>
            <div style={{fontSize:16, fontWeight:700, marginTop:6}}>Créditos: {remote && remote.totalCredits != null ? fmtCurrency(remote.totalCredits) : (dashboard && dashboard.totalCredits ? fmtCurrency(dashboard.totalCredits) : '$0')}</div>
            <div className="small-muted">Abonos: {remote && remote.totalPayments != null ? fmtCurrency(remote.totalPayments) : (dashboard && dashboard.totalPayments ? fmtCurrency(dashboard.totalPayments) : '$0')}</div>
            <div className="small-muted">Facturas pendientes: {remote && remote.pendingInvoicesCount != null ? `${remote.pendingInvoicesCount} — ${fmtCurrency(remote.pendingInvoicesAmount)}` : 'Ejemplo: 5 — $12.000'}</div>
            <div className="small-muted">Facturas facturadas: {remote && remote.invoicesBilledCount != null ? `${remote.invoicesBilledCount} — ${fmtCurrency(remote.invoicesBilledAmount)}` : 'Ejemplo: 10 — $120.000'}</div>
            <div style={{fontWeight:700, marginTop:8}}>Total a pagar: {remote && remote.totalToPay != null ? fmtCurrency(remote.totalToPay) : '$0'}</div>
          </div>
        </div>
      </div>

      <div className="card-wide">
        <div className="card-large card">
          <div className="h2">Consumo Total por Ciclo</div>
          <p className="small-muted">Metros cúbicos consumidos por todos los suscriptores</p>
          <div style={{height:320, marginTop:18, borderRadius:10, background:'linear-gradient(180deg, #eaf7f7, #ffffff)'}}>
            <ConsumptionChart series={(remote && remote.monthlyConsumption) || []} height={300} />
          </div>
        </div>

        <div className="card-side card">
          <div className="h2">Actividad Reciente</div>
          <p className="small-muted">Últimas acciones del sistema</p>

          <div className="mt-2">
            {(remote && remote.activity && remote.activity.length > 0) ? (
              remote.activity.map((a, idx) => <ActivityItem key={idx} item={a} />)
            ) : (
              <>
                <div className="activity-item">
                  <div className="activity-icon">📋</div>
                  <div>
                    <div style={{fontWeight:700}}>Nueva lectura registrada para María García</div>
                    <div className="small-muted">Hace 2 horas</div>
                  </div>
                </div>
                <div className="activity-item">
                  <div className="activity-icon">✅</div>
                  <div>
                    <div style={{fontWeight:700}}>Lectura aprobada - Juan Pérez (48 m³)</div>
                    <div className="small-muted">Hace 3 horas</div>
                  </div>
                </div>
              </>
            )}
          </div>
        </div>
      </div>

      <div className="card card-wide">
        <div style={{display:'flex', gap:18}}>
          {[c1, c2, c3].map((c, idx) => {
            const bg = idx === 0 ? '#e9f8f6' : idx === 1 ? '#eaf2ff' : '#fff7ea'
            const mainColor = idx === 2 ? '#ff8b2b' : '#000'
            const label = idx === 0 ? `(bajo el base)` : idx === 1 ? `(exacto)` : `(+${Math.max(0, c.consumo - (tariffsData.threshold || 0))} adicionales)`
            return (
              <div key={idx} style={{flex:1, padding:18, background:bg, borderRadius:10}}>
                <div className="small-muted">Consumo: {c.consumo} m³ {label}</div>
                <div style={{fontSize:20, fontWeight:800, marginTop:6, color: mainColor}}>{fmtCurrency(c.total)}</div>
                <div className="small-muted">{c.adicional > 0 ? `${fmtCurrency(tariffsData.basicTariff)} + ${fmtCurrency(c.adicional)}` : 'Solo tarifa básica'}</div>
              </div>
            )
          })}
        </div>
      </div>
    </div>
  )
}

export async function getServerSideProps() {
  const prisma = require('../lib/prisma')

  try {
    const [totalSubscribers, latestReading, pendingInvoicesCount, pendingInvoicesAmountAgg, creditsCount, creditsSumAgg, paymentsSumAgg, invoicesBilledCount, invoicesBilledAmountAgg, config] = await Promise.all([
      prisma.subscriber.count(),
      prisma.reading.findFirst({ orderBy: { fecha: 'desc' } }),
      prisma.invoice.count({ where: { estado: 'pendiente' } }),
      prisma.invoice.aggregate({ _sum: { total: true }, where: { estado: 'pendiente' } }),
      prisma.credit.count(),
      prisma.credit.aggregate({ _sum: { amount: true } }),
      prisma.creditPayment.aggregate({ _sum: { amount: true } }),
      prisma.invoice.count({ where: { estado: 'facturado' } }),
      prisma.invoice.aggregate({ _sum: { total: true }, where: { estado: 'facturado' } }),
      prisma.config.findFirst()
    ])

    let consumptionSum = null
    let cycleLabel = null
    if (latestReading) {
      const d = latestReading.fecha
      const start = new Date(d.getFullYear(), d.getMonth(), 1)
      const end = new Date(d.getFullYear(), d.getMonth() + 1, 1)
      const agg = await prisma.reading.aggregate({ _sum: { consumo: true }, where: { fecha: { gte: start, lt: end } } })
      consumptionSum = agg._sum.consumo || 0
      cycleLabel = `Ciclo ${start.getMonth() + 1} - ${start.getFullYear()}`
    }

    const pendingInvoicesAmount = (pendingInvoicesAmountAgg && pendingInvoicesAmountAgg._sum && pendingInvoicesAmountAgg._sum.total) || 0
    const creditsSum = (creditsSumAgg && creditsSumAgg._sum && creditsSumAgg._sum.amount) || 0
    const paymentsSum = (paymentsSumAgg && paymentsSumAgg._sum && paymentsSumAgg._sum.amount) || 0
    const creditsBalance = creditsSum - paymentsSum
    const invoicesBilledAmount = (invoicesBilledAmountAgg && invoicesBilledAmountAgg._sum && invoicesBilledAmountAgg._sum.total) || 0
    const totalToPayWithCredits = Math.max(0, pendingInvoicesAmount - creditsBalance)
    const totalToPayWithoutCredits = pendingInvoicesAmount
    const totalCredits = creditsSum
    const totalPayments = paymentsSum
    const tariffs = { basicTariff: config?.basicTariff || 0, threshold: config?.threshold || 0, unitPrice: config?.unitPrice || 0 }
    const lastUpdated = new Date().toISOString()

    // Local formatter for server-side use
    const fmtCurrency = (v) => {
      const currency = config?.currency || 'COP'
      try { return new Intl.NumberFormat('es-CO', { style: 'currency', currency, maximumFractionDigits: 0 }).format(v) } catch (e) { return `${v}` }
    }

    // Monthly consumption series for last 6 months
    const months = []
    const now = new Date()
    for (let i = 5; i >= 0; i--) {
      const m = new Date(now.getFullYear(), now.getMonth() - i, 1)
      months.push({ label: `${m.toLocaleString('es-CO',{month:'short'})} ${m.getFullYear()}`, from: new Date(m.getFullYear(), m.getMonth(), 1), to: new Date(m.getFullYear(), m.getMonth()+1, 1) })
    }

    const readingsLastMonths = await prisma.reading.findMany({ where: { fecha: { gte: months[0].from } }, select: { consumo: true, fecha: true } })

    const monthlyConsumption = months.map(ms => {
      const total = readingsLastMonths.filter(r => new Date(r.fecha) >= ms.from && new Date(r.fecha) < ms.to).reduce((s,n) => s + (n.consumo || 0), 0)
      return { label: ms.label, value: total }
    })

    // Recent activity (readings, invoices, credit payments)
    const [latestReadings, latestInvoices, latestPayments] = await Promise.all([
      prisma.reading.findMany({ include: { subscriber: true }, orderBy: { fecha: 'desc' }, take: 5 }),
      prisma.invoice.findMany({ orderBy: { fecha: 'desc' }, take: 5, include: { reading: { include: { subscriber: true } } } }),
      prisma.creditPayment.findMany({ orderBy: { createdAt: 'desc' }, take: 5, include: { credit: { include: { subscriber: true } } } })
    ])

    const activities = []
    latestReadings.forEach(r => activities.push({ type: 'reading', date: r.fecha.toISOString(), icon: '📋', title: `Lectura registrada para ${r.subscriber?.nombres || r.matricula}`, subtitle: `${r.consumo || 0} m³` }))
    latestInvoices.forEach(inv => activities.push({ type: 'invoice', date: inv.fecha.toISOString(), icon: '💲', title: `Recibo #${inv.id}`, subtitle: fmtCurrency(inv.total || 0) }))
    latestPayments.forEach(p => activities.push({ type: 'payment', date: p.createdAt.toISOString(), icon: '✅', title: `Pago crédito #${p.id}`, subtitle: fmtCurrency(p.amount || 0) }))

    activities.sort((a,b) => new Date(b.date) - new Date(a.date))

    return {
      props: {
        dashboard: {
          totalSubscribers,
          consumptionSum,
          cycleLabel,
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
          monthlyConsumption,
          activity: activities.slice(0,6),
          lastUpdated
        }
      }
    }
  } catch (e) {
    console.error('Dashboard data error', e)
    return { props: { dashboard: null } }
  }
}
