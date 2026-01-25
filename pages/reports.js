import { useState, useRef } from 'react'
import { LineChart, Line, AreaChart, Area, BarChart, Bar, PieChart, Pie, Cell, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer } from 'recharts'

export default function ReportsPage() {
  const [period, setPeriod] = useState('day')
  const [reportData, setReportData] = useState(null)
  const [loading, setLoading] = useState(false)
  const [chartType, setChartType] = useState('line')
  const printRef = useRef()

  const fmtCurrency = (v) => {
    if (v == null) return '0'
    return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(v)
  }

  const fetchReport = async () => {
    setLoading(true)
    try {
      const res = await fetch(`/api/reports?period=${period}`)
      const data = await res.json()
      setReportData(data)
    } catch (error) {
      console.error('Error fetching report:', error)
    }
    setLoading(false)
  }

  const handlePrint = () => {
    const printWindow = window.open('', '', 'height=600,width=800')
    printWindow.document.write(printRef.current.innerHTML)
    printWindow.document.close()
    setTimeout(() => {
      printWindow.print()
    }, 250)
  }

  return (
    <div style={{ padding: '20px' }}>
      <div style={{ marginBottom: '30px' }}>
        <h1 style={{ fontSize: '28px', fontWeight: 'bold', margin: '0 0 10px 0', color: '#0b5e94' }}>📊 Recibos e Informes</h1>
        <p style={{ margin: '0', color: '#666' }}>Genere recibos imprimibles y reportes de movimientos diarios, mensuales y anuales</p>
      </div>

      {/* Selector de período */}
      <div style={{ marginBottom: '20px', display: 'flex', gap: '10px', flexWrap: 'wrap', background: '#f9f9f9', padding: '15px', borderRadius: '8px' }}>
        <select 
          value={period} 
          onChange={(e) => setPeriod(e.target.value)}
          style={{ padding: '10px', borderRadius: '8px', border: '1px solid #ddd', fontSize: '14px' }}
        >
          <option value="day">Movimiento Diario (Hoy)</option>
          <option value="month">Movimiento Mensual (Este mes)</option>
          <option value="year">Balance Anual</option>
        </select>
        <button 
          onClick={fetchReport}
          disabled={loading}
          style={{
            padding: '10px 20px',
            background: '#0b5e94',
            color: 'white',
            border: 'none',
            borderRadius: '8px',
            cursor: 'pointer',
            fontWeight: 'bold'
          }}
        >
          {loading ? 'Cargando...' : 'Generar Reporte'}
        </button>
        {reportData && (
          <button 
            onClick={handlePrint}
            style={{
              padding: '10px 20px',
              background: '#27ae60',
              color: 'white',
              border: 'none',
              borderRadius: '8px',
              cursor: 'pointer',
              fontWeight: 'bold'
            }}
          >
            🖨️ Imprimir Recibo
          </button>
        )}
      </div>

      {/* Contenedor imprimible (oculto en pantalla) */}
      {reportData && (
        <div ref={printRef} style={{ 
          width: '21cm', 
          height: '29.7cm', 
          padding: '40px',
          background: 'white',
          fontFamily: 'Arial, sans-serif',
          fontSize: '12px',
          lineHeight: '1.6',
          display: 'none'
        }}>
          {/* Encabezado */}
          <div style={{ textAlign: 'center', marginBottom: '20px', borderBottom: '2px solid #0b5e94', paddingBottom: '10px' }}>
            <h2 style={{ margin: '0 0 5px 0', color: '#0b5e94' }}>ACUEDUCTO RURAL</h2>
            <p style={{ margin: '0 0 10px 0', fontSize: '11px', color: '#666' }}>Nit: 123.456.789-0</p>
            <h3 style={{ margin: '0', fontSize: '14px' }}>
              {period === 'day' && 'MOVIMIENTO DIARIO - CUADRE DE CAJA'}
              {period === 'month' && 'MOVIMIENTO MENSUAL'}
              {period === 'year' && 'BALANCE ANUAL'}
            </h3>
            <p style={{ margin: '5px 0 0 0', fontSize: '11px', color: '#666' }}>
              Del {new Date(reportData.startDate).toLocaleDateString('es-CO')} al {new Date(reportData.endDate).toLocaleDateString('es-CO')}
            </p>
          </div>

          {/* Resumen */}
          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '15px', marginBottom: '20px' }}>
            <div style={{ padding: '10px', background: '#e8f4f8', borderRadius: '5px', border: '1px solid #0b5e94' }}>
              <p style={{ margin: '0 0 5px 0', fontSize: '11px', color: '#666' }}>Lecturas Registradas</p>
              <p style={{ margin: '0', fontSize: '16px', fontWeight: 'bold', color: '#0b5e94' }}>{reportData.readings}</p>
            </div>
            <div style={{ padding: '10px', background: '#e8f8f0', borderRadius: '5px', border: '1px solid #27ae60' }}>
              <p style={{ margin: '0 0 5px 0', fontSize: '11px', color: '#666' }}>Total Facturado</p>
              <p style={{ margin: '0', fontSize: '16px', fontWeight: 'bold', color: '#27ae60' }}>{fmtCurrency(reportData.totalInvoiced)}</p>
            </div>
            <div style={{ padding: '10px', background: '#f8f0e8', borderRadius: '5px', border: '1px solid #e67e22' }}>
              <p style={{ margin: '0 0 5px 0', fontSize: '11px', color: '#666' }}>Total Pagado</p>
              <p style={{ margin: '0', fontSize: '16px', fontWeight: 'bold', color: '#e67e22' }}>{fmtCurrency(reportData.totalPaid)}</p>
            </div>
            <div style={{ padding: '10px', background: '#f0e8f8', borderRadius: '5px', border: '1px solid #9b59b6' }}>
              <p style={{ margin: '0 0 5px 0', fontSize: '11px', color: '#666' }}>Total Créditos</p>
              <p style={{ margin: '0', fontSize: '16px', fontWeight: 'bold', color: '#9b59b6' }}>{fmtCurrency(reportData.totalCredits)}</p>
            </div>
          </div>

          {/* Balance */}
          <div style={{ 
            padding: '15px', 
            background: reportData.balance >= 0 ? '#d4edda' : '#f8d7da',
            border: `2px solid ${reportData.balance >= 0 ? '#28a745' : '#dc3545'}`,
            borderRadius: '8px',
            marginBottom: '20px',
            textAlign: 'center'
          }}>
            <p style={{ margin: '0 0 5px 0', fontSize: '11px', color: '#666' }}>BALANCE</p>
            <p style={{ margin: '0', fontSize: '20px', fontWeight: 'bold', color: reportData.balance >= 0 ? '#28a745' : '#dc3545' }}>
              {fmtCurrency(reportData.balance)}
            </p>
          </div>

          {/* Tabla de movimientos */}
          <div style={{ marginBottom: '20px' }}>
            <h4 style={{ margin: '10px 0 10px 0', fontSize: '12px', borderBottom: '1px solid #ddd', paddingBottom: '5px' }}>MOVIMIENTOS DETALLADOS</h4>
            <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: '11px' }}>
              <thead>
                <tr style={{ background: '#f5f5f5', borderBottom: '1px solid #ddd' }}>
                  <th style={{ padding: '5px', textAlign: 'left', fontWeight: 'bold' }}>Fecha</th>
                  <th style={{ padding: '5px', textAlign: 'left', fontWeight: 'bold' }}>Suscriptor</th>
                  <th style={{ padding: '5px', textAlign: 'left', fontWeight: 'bold' }}>Tipo</th>
                  <th style={{ padding: '5px', textAlign: 'right', fontWeight: 'bold' }}>Monto</th>
                </tr>
              </thead>
              <tbody>
                {reportData.movimientos && reportData.movimientos.readings && reportData.movimientos.readings.slice(0, 10).map((m, i) => (
                  <tr key={`read-${i}`} style={{ borderBottom: '1px solid #eee' }}>
                    <td style={{ padding: '5px' }}>{new Date(m.date).toLocaleDateString('es-CO')}</td>
                    <td style={{ padding: '5px' }}>{m.subscriber}</td>
                    <td style={{ padding: '5px', color: '#0b5e94', fontWeight: 'bold' }}>📋 {m.type}</td>
                    <td style={{ padding: '5px', textAlign: 'right', color: '#27ae60', fontWeight: 'bold' }}>+{fmtCurrency(m.amount)}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          {/* Pie de página */}
          <div style={{ marginTop: '40px', paddingTop: '15px', borderTop: '1px solid #ddd', textAlign: 'center', fontSize: '10px', color: '#666' }}>
            <p style={{ margin: '5px 0' }}>Reporte generado el {new Date().toLocaleString('es-CO')}</p>
            <p style={{ margin: '0' }}>Este documento es válido como constancia de movimientos</p>
          </div>
        </div>
      )}

      {/* Preview en pantalla */}
      {reportData && (
        <div style={{
          display: 'grid',
          gridTemplateColumns: 'repeat(auto-fit, minmax(250px, 1fr))',
          gap: '20px',
          marginBottom: '30px'
        }}>
          <div style={{ padding: '20px', background: '#fff', borderRadius: '8px', boxShadow: '0 2px 4px rgba(0,0,0,0.1)', borderTop: '4px solid #0b5e94' }}>
            <p style={{ margin: '0 0 10px 0', fontSize: '12px', color: '#666' }}>Lecturas Registradas</p>
            <p style={{ margin: '0', fontSize: '24px', fontWeight: 'bold', color: '#0b5e94' }}>{reportData.readings}</p>
          </div>
          <div style={{ padding: '20px', background: '#fff', borderRadius: '8px', boxShadow: '0 2px 4px rgba(0,0,0,0.1)', borderTop: '4px solid #27ae60' }}>
            <p style={{ margin: '0 0 10px 0', fontSize: '12px', color: '#666' }}>Total Facturado</p>
            <p style={{ margin: '0', fontSize: '24px', fontWeight: 'bold', color: '#27ae60' }}>{fmtCurrency(reportData.totalInvoiced)}</p>
          </div>
          <div style={{ padding: '20px', background: '#fff', borderRadius: '8px', boxShadow: '0 2px 4px rgba(0,0,0,0.1)', borderTop: '4px solid #e67e22' }}>
            <p style={{ margin: '0 0 10px 0', fontSize: '12px', color: '#666' }}>Total Pagado</p>
            <p style={{ margin: '0', fontSize: '24px', fontWeight: 'bold', color: '#e67e22' }}>{fmtCurrency(reportData.totalPaid)}</p>
          </div>
          <div style={{ padding: '20px', background: '#fff', borderRadius: '8px', boxShadow: '0 2px 4px rgba(0,0,0,0.1)', borderTop: '4px solid #9b59b6' }}>
            <p style={{ margin: '0 0 10px 0', fontSize: '12px', color: '#666' }}>Total Créditos</p>
            <p style={{ margin: '0', fontSize: '24px', fontWeight: 'bold', color: '#9b59b6' }}>{fmtCurrency(reportData.totalCredits)}</p>
          </div>
          <div style={{ padding: '20px', background: reportData.balance >= 0 ? '#d4edda' : '#f8d7da', borderRadius: '8px', boxShadow: '0 2px 4px rgba(0,0,0,0.1)', borderTop: `4px solid ${reportData.balance >= 0 ? '#28a745' : '#dc3545'}` }}>
            <p style={{ margin: '0 0 10px 0', fontSize: '12px', color: '#666' }}>BALANCE</p>
            <p style={{ margin: '0', fontSize: '24px', fontWeight: 'bold', color: reportData.balance >= 0 ? '#28a745' : '#dc3545' }}>
              {fmtCurrency(reportData.balance)}
            </p>
          </div>
          <div style={{ padding: '20px', background: '#fff', borderRadius: '8px', boxShadow: '0 2px 4px rgba(0,0,0,0.1)', borderTop: '4px solid #3498db' }}>
            <p style={{ margin: '0 0 10px 0', fontSize: '12px', color: '#666' }}>Período</p>
            <p style={{ margin: '0', fontSize: '14px', fontWeight: 'bold', color: '#3498db' }}>
              {period === 'day' && 'Hoy'} {period === 'month' && 'Este Mes'} {period === 'year' && 'Este Año'}
            </p>
          </div>
        </div>
      )}

      {reportData && reportData.movimientos && (
        <div style={{ marginTop: '30px' }}>
          <h2 style={{ fontSize: '18px', fontWeight: 'bold', color: '#0b5e94', marginBottom: '15px' }}>📋 Movimientos Detallados</h2>
          <div style={{ background: '#fff', borderRadius: '8px', boxShadow: '0 2px 4px rgba(0,0,0,0.1)', overflowX: 'auto' }}>
            <table style={{ width: '100%', borderCollapse: 'collapse' }}>
              <thead>
                <tr style={{ background: '#f5f5f5', borderBottom: '2px solid #ddd' }}>
                  <th style={{ padding: '12px', textAlign: 'left', fontWeight: 'bold', color: '#333' }}>Fecha</th>
                  <th style={{ padding: '12px', textAlign: 'left', fontWeight: 'bold', color: '#333' }}>Suscriptor</th>
                  <th style={{ padding: '12px', textAlign: 'left', fontWeight: 'bold', color: '#333' }}>Tipo</th>
                  <th style={{ padding: '12px', textAlign: 'right', fontWeight: 'bold', color: '#333' }}>Monto</th>
                </tr>
              </thead>
              <tbody>
                {reportData.movimientos.readings && reportData.movimientos.readings.map((m, i) => (
                  <tr key={`read-${i}`} style={{ borderBottom: '1px solid #eee' }}>
                    <td style={{ padding: '12px' }}>{new Date(m.date).toLocaleDateString('es-CO')}</td>
                    <td style={{ padding: '12px' }}>{m.subscriber}</td>
                    <td style={{ padding: '12px', color: '#0b5e94', fontWeight: 'bold' }}>📋 {m.type}</td>
                    <td style={{ padding: '12px', textAlign: 'right', color: '#27ae60', fontWeight: 'bold' }}>+{fmtCurrency(m.amount)}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}
    </div>
  )
}
