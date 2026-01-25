const PDFDocument = require('pdfkit')
const path = require('path')
const QRCode = require('qrcode')
const prisma = require('./prisma')

const COLORS = {
  primary: '#0b5e94',
  darkBlue: '#003d6b',
  lightGray: '#f9f9f9',
  mediumGray: '#e8e8e8',
  darkGray: '#333333',
  lightText: '#666666',
  border: '#d0d0d0',
  green: '#27ae60',
  lightGreen: '#d5f4e6',
  orange: '#e67e22',
  red: '#c0392b'
}

const LOGO_PATH = path.join(process.cwd(), 'public', 'logo-acua.png')
const MARGINS = { top: 50, bottom: 50, left: 40, right: 40 }

function formatCurrency(value) {
  return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }).format(value)
}

function formatDate(date) {
  return new Date(date).toLocaleDateString('es-CO', { year: 'numeric', month: '2-digit', day: '2-digit' })
}

async function generateInvoicePdf(invoice) {
  return new Promise(async (resolve, reject) => {
    try {
      const doc = new PDFDocument({ margin: 0, size: 'letter', bufferPages: true, lineGap: 3 })
      let y = MARGINS.top
      const pw = doc.page.width - MARGINS.left - MARGINS.right

      // ===== DATOS DE BASE DE DATOS =====
      const s = invoice.reading.subscriber
      const r = invoice.reading
      const consumo = Math.max(0, (r.lecturaActual || 0) - (r.lecturaAnterior || 0))
      const cfg = (await prisma.config.findFirst()) || {}
      const subscriberId = invoice.reading.subscriberId
      const dueDate = new Date(new Date().setDate(new Date().getDate() + 10))

      // Lecturas históricas
      const lastReadings = await prisma.reading.findMany({
        where: { subscriberId },
        orderBy: { fecha: 'desc' },
        take: 6
      })

      // Cálculos de tarifa desde BD
      const baseCharge = cfg.baseCharge || 45000
      const unitPrice = cfg.unitPrice || 2500
      const additionalCharge = Math.max(0, consumo - 20) * unitPrice
      const otherCharges = cfg.otherCharges || 8500
      const total = baseCharge + additionalCharge + otherCharges
      const avgNeighborConsumption = cfg.avgConsumption || 32

      // ========== SECCIÓN 1: ENCABEZADO INSTITUCIONAL ==========
      doc.rect(MARGINS.left, y, pw, 65).fill(COLORS.lightGray).stroke(COLORS.border)
      
      // Logo
      try {
        doc.image(LOGO_PATH, MARGINS.left + 6, y + 8, { width: 40, height: 40 })
      } catch (e) {
        doc.fontSize(16).font('Helvetica-Bold').fillColor(COLORS.primary).text('ACUA', MARGINS.left + 12, y + 12)
      }

      // Datos empresa (izquierda)
      doc.fontSize(14).font('Helvetica-Bold').fillColor(COLORS.darkBlue)
        .text(cfg.name || 'ACUAPALTRES', MARGINS.left + 52, y + 6)
      
      doc.fontSize(9).font('Helvetica').fillColor(COLORS.darkGray)
      doc.text(`NIT: ${cfg.nit || '000.000.000-0'}`, MARGINS.left + 52, y + 22, { lineGap: 2 })
      doc.text(`Teléfono: ${cfg.phone || '(1) 234-5678'}`, MARGINS.left + 52, y + 30, { lineGap: 2 })
      doc.text(`Email: ${cfg.email || 'info@acua.com'}`, MARGINS.left + 52, y + 38, { lineGap: 2 })
      doc.text(`Dirección: ${cfg.address || 'Dirección'}`, MARGINS.left + 52, y + 46, { lineGap: 2 })

      // Título derecha
      const titleX = MARGINS.left + pw * 0.55
      doc.fontSize(13).font('Helvetica-Bold').fillColor(COLORS.darkBlue)
      doc.text('RECIBO SERVICIO PÚBLICO', titleX, y + 8, { width: pw * 0.45 - 8, align: 'right', lineGap: 1 })
      doc.fontSize(12).font('Helvetica-Bold').fillColor(COLORS.darkBlue)
      doc.text('DOMICILIARIO - ACUEDUCTO', titleX, y + 22, { width: pw * 0.45 - 8, align: 'right', lineGap: 1 })

      doc.fontSize(12).font('Helvetica-Bold').fillColor(COLORS.green)
      doc.text(`Factura No. ${invoice.id}`, titleX, y + 37, { width: pw * 0.45 - 8, align: 'right' })
      doc.fontSize(10).font('Helvetica').fillColor(COLORS.darkGray)
      doc.text(`Fecha: ${formatDate(invoice.fecha)}`, titleX, y + 50, { width: pw * 0.45 - 8, align: 'right' })

      y += 72

      // ========== SECCIÓN 2: DOS COLUMNAS PRINCIPALES ==========
      const colW = (pw - 4) / 2
      const col1X = MARGINS.left
      const col2X = MARGINS.left + colW + 3

      // COLUMNA 1: DATOS DEL SUSCRIPTOR
      doc.rect(col1X, y, colW, 14).fill(COLORS.primary)
      doc.fontSize(11).font('Helvetica-Bold').fillColor('#ffffff')
        .text('DATOS DEL SUSCRIPTOR', col1X + 5, y + 3, { lineGap: 2 })
      y += 16

      // Contenido columna 1
      const dataStartY = y
      doc.fontSize(9).font('Helvetica-Bold').fillColor(COLORS.primary)
      doc.text('Nombre Completo:', col1X + 3, y, { lineGap: 1 })
      doc.fontSize(10).font('Helvetica').fillColor(COLORS.darkGray)
      doc.text(`${s.nombres} ${s.apellidos}`, col1X + 5, y + 9, { width: colW - 8, lineGap: 2 })

      y += 24
      doc.fontSize(9).font('Helvetica-Bold').fillColor(COLORS.primary)
      doc.text('Dirección del Predio:', col1X + 3, y, { lineGap: 1 })
      doc.fontSize(10).font('Helvetica').fillColor(COLORS.darkGray)
      doc.text(s.direccion || 'No especificado', col1X + 5, y + 9, { width: colW - 8, lineGap: 2 })

      y += 24
      doc.fontSize(9).font('Helvetica-Bold').fillColor(COLORS.primary)
      doc.text('Matrícula del Predio:', col1X + 3, y, { lineGap: 1 })
      doc.fontSize(10).font('Helvetica').fillColor(COLORS.darkGray)
      doc.text(s.matricula || 'No asignada', col1X + 5, y + 9, { lineGap: 2 })

      y += 24
      doc.fontSize(9).font('Helvetica-Bold').fillColor(COLORS.primary)
      doc.text('Período Facturado:', col1X + 3, y, { lineGap: 1 })
      doc.fontSize(10).font('Helvetica').fillColor(COLORS.darkGray)
      doc.text(r.ciclo || 'Enero 2026', col1X + 5, y + 9, { lineGap: 2 })

      y += 24
      doc.fontSize(9).font('Helvetica-Bold').fillColor(COLORS.primary)
      doc.text('Tipo de Uso:', col1X + 3, y, { lineGap: 1 })
      doc.fontSize(10).font('Helvetica').fillColor(COLORS.darkGray)
      doc.text('Residencial', col1X + 5, y + 9, { lineGap: 2 })

      y += 24
      doc.fontSize(9).font('Helvetica-Bold').fillColor(COLORS.primary)
      doc.text('Fecha Vencimiento:', col1X + 3, y, { lineGap: 1 })
      doc.fontSize(11).font('Helvetica-Bold').fillColor(COLORS.red)
      doc.text(formatDate(dueDate), col1X + 5, y + 9, { lineGap: 2 })

      // COLUMNA 2: DETALLE DE FACTURACIÓN
      y = dataStartY
      doc.rect(col2X, y - 2, colW, 14).fill(COLORS.primary)
      doc.fontSize(11).font('Helvetica-Bold').fillColor('#ffffff')
        .text('DETALLE DE FACTURACIÓN', col2X + 5, y + 1, { lineGap: 2 })
      y += 16

      const billItems = [
        { label: 'Cargo Fijo Mensual', value: baseCharge },
        { label: `Consumo Complementario (${consumo} m³)`, value: additionalCharge },
        { label: 'Otros Conceptos e Impuestos', value: otherCharges }
      ]

      billItems.forEach((item, idx) => {
        doc.fontSize(9).font('Helvetica').fillColor(COLORS.darkGray)
        doc.text(item.label, col2X + 5, y, { width: colW - 65, lineGap: 2 })
        doc.fontSize(10).font('Helvetica-Bold').fillColor(COLORS.darkGray)
        doc.text(formatCurrency(item.value), col2X + colW - 58, y, { align: 'right', width: 55 })
        y += 20
      })

      // Línea separadora
      doc.moveTo(col2X + 5, y).lineTo(col2X + colW - 5, y).stroke(COLORS.mediumGray)
      y += 8

      // TOTAL A PAGAR
      doc.rect(col2X, y, colW, 32).fill(COLORS.lightGreen).stroke(COLORS.green)
      doc.fontSize(10).font('Helvetica-Bold').fillColor(COLORS.primary)
      doc.text('TOTAL A PAGAR', col2X + 5, y + 4, { lineGap: 2 })
      doc.fontSize(18).font('Helvetica-Bold').fillColor(COLORS.green)
      doc.text(formatCurrency(total), col2X + 5, y + 13, { lineGap: 2 })
      doc.fontSize(8).font('Helvetica').fillColor(COLORS.lightText)
      doc.text(`Hasta el ${formatDate(dueDate)} sin intereses`, col2X + 5, y + 26, { lineGap: 1 })

      y += 38

      // ========== SECCIÓN 3: LECTURAS Y CONSUMO ==========
      y += 6
      doc.rect(MARGINS.left, y, pw, 15).fill(COLORS.primary)
      doc.fontSize(11).font('Helvetica-Bold').fillColor('#ffffff')
        .text('LECTURAS Y CONSUMO', MARGINS.left + 5, y + 3, { lineGap: 2 })
      y += 17

      const readCols = ['Lectura Anterior', 'Lectura Actual', 'Consumo (m³)', 'Días Facturados']
      const readVals = [r.lecturaAnterior || '0', r.lecturaActual || '0', consumo.toString(), '30']
      const readColW = (pw - 2) / 4

      let readX = MARGINS.left + 1
      readCols.forEach((col, i) => {
        doc.fontSize(8).font('Helvetica-Bold').fillColor(COLORS.darkBlue)
        doc.text(col, readX, y, { width: readColW, align: 'center', lineGap: 1 })
        doc.fontSize(11).font('Helvetica-Bold').fillColor(COLORS.green)
        doc.text(readVals[i], readX, y + 11, { width: readColW, align: 'center' })
        readX += readColW
      })

      y += 26

      // ========== SECCIÓN 4: GRÁFICO DE CONSUMO (BARRAS HORIZONTALES) ==========
      y += 4
      doc.rect(MARGINS.left, y, pw, 15).fill(COLORS.primary)
      doc.fontSize(11).font('Helvetica-Bold').fillColor('#ffffff')
        .text('CONSUMO ÚLTIMOS 6 CICLOS', MARGINS.left + 5, y + 3, { lineGap: 2 })
      y += 17

      const sortedReadings = [...lastReadings].reverse()
      const historicoConsumo = sortedReadings.map(r => r.consumo || 0)
      const maxConsumo = Math.max(...historicoConsumo, 50)
      const barH = 9
      const spacing = 3

      historicoConsumo.forEach((val, idx) => {
        const barW = (pw - 120) * (val / maxConsumo)
        const barColor = val > 30 ? COLORS.orange : val > 20 ? COLORS.green : COLORS.primary

        // Mes
        const readDate = new Date(sortedReadings[idx].fecha)
        const monthName = ['Enero', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'][readDate.getMonth()]
        doc.fontSize(8).font('Helvetica-Bold').fillColor(COLORS.darkGray)
        doc.text(monthName, MARGINS.left + 3, y + 1)

        // Barra
        doc.rect(MARGINS.left + 50, y, barW, barH).fill(barColor)

        // Valor
        doc.fontSize(8).font('Helvetica-Bold').fillColor(COLORS.darkGray)
        doc.text(`${val} m³`, MARGINS.left + 52 + barW + 3, y + 1)

        y += barH + spacing
      })

      y += 4

      // ========== SECCIÓN 5: COMPARATIVA CON CONSUMO PROMEDIO ==========
      y += 4
      doc.rect(MARGINS.left, y, pw, 16).fill(COLORS.orange)
      const consumptionDiff = ((consumo - avgNeighborConsumption) / avgNeighborConsumption * 100).toFixed(0)
      const compText = consumo < avgNeighborConsumption
        ? `CONSUMISTE ${Math.abs(consumptionDiff)}% MENOS que el promedio de vecinos`
        : `CONSUMISTE ${consumptionDiff}% MÁS que el promedio de vecinos`
      doc.fontSize(10).font('Helvetica-Bold').fillColor('#ffffff')
      doc.text(compText, MARGINS.left + 5, y + 3, { width: pw - 10, lineGap: 2 })
      y += 18

      // Barra comparativa
      y += 3
      const compBarX = MARGINS.left + 140
      const compBarW = pw - 160
      const maxComp = Math.max(avgNeighborConsumption, consumo, 50)

      doc.fontSize(9).font('Helvetica-Bold').fillColor(COLORS.darkGray)
      doc.text('Promedio vecinos:', MARGINS.left + 3, y, { lineGap: 2 })
      doc.rect(compBarX, y - 1, compBarW * (avgNeighborConsumption / maxComp), 11).fill(COLORS.mediumGray)
      doc.fontSize(9).font('Helvetica-Bold').fillColor(COLORS.darkGray)
      doc.text(`${avgNeighborConsumption} m³`, compBarX + 6, y + 1)

      y += 16
      doc.fontSize(9).font('Helvetica-Bold').fillColor(COLORS.darkGray)
      doc.text('Tu consumo:', MARGINS.left + 3, y, { lineGap: 2 })
      const yourColor = consumo < avgNeighborConsumption ? COLORS.green : COLORS.red
      doc.rect(compBarX, y - 1, compBarW * (consumo / maxComp), 11).fill(yourColor)
      doc.fontSize(9).font('Helvetica-Bold').fillColor('#ffffff')
      doc.text(`${consumo} m³`, compBarX + 6, y + 1)

      y += 18

      // ========== SECCIÓN 6: RECOMENDACIONES ==========
      y += 4
      doc.fontSize(10).font('Helvetica-Bold').fillColor(COLORS.primary)
      doc.text('💧 CONSEJOS PARA AHORRAR AGUA:', MARGINS.left + 3, y, { lineGap: 2 })
      y += 14

      const recommendations = [
        '• Verifica regularmente tuberías y grifos para detectar fugas',
        '• Instala regaderas de bajo flujo y reductores de caudal',
        '• No dejes correr agua mientras te enjabonas o cepillas los dientes'
      ]

      doc.fontSize(9).font('Helvetica').fillColor(COLORS.darkGray)
      recommendations.forEach(rec => {
        doc.text(rec, MARGINS.left + 5, y, { width: pw - 10, lineGap: 2 })
        y += 11
      })

      y += 4

      // ========== LÍNEA DESPRENDIBLE ==========
      y += 2
      doc.moveTo(MARGINS.left, y).lineTo(MARGINS.left + pw, y)
        .dash(4, { space: 4 }).stroke(COLORS.mediumGray)
      doc.undash()
      y += 8

      // ========== SECCIÓN 7: DESPRENDIBLE DE PAGO ==========
      doc.fontSize(10).font('Helvetica-Bold').fillColor(COLORS.darkBlue)
      doc.text('✂ DESPRENDIBLE DE PAGO - Separe y pague', MARGINS.left + 3, y, { lineGap: 2 })
      y += 12

      const despW = (pw - 3) / 2
      const desp1X = MARGINS.left + 2
      const desp2X = MARGINS.left + despW + 3

      // Columna izquierda
      doc.fontSize(8).font('Helvetica-Bold').fillColor(COLORS.primary)
      doc.text('Empresa:', desp1X + 2, y, { lineGap: 1 })
      doc.fontSize(9).font('Helvetica').fillColor(COLORS.darkGray)
      doc.text(cfg.name || 'ACUAPALTRES', desp1X + 2, y + 8, { lineGap: 1 })

      doc.fontSize(8).font('Helvetica-Bold').fillColor(COLORS.primary)
      doc.text('Suscriptor:', desp1X + 2, y + 17, { lineGap: 1 })
      doc.fontSize(9).font('Helvetica').fillColor(COLORS.darkGray)
      doc.text(`${s.nombres} ${s.apellidos}`, desp1X + 2, y + 25, { width: despW - 8, lineGap: 1 })

      doc.fontSize(8).font('Helvetica-Bold').fillColor(COLORS.primary)
      doc.text('Matrícula:', desp1X + 2, y + 36, { lineGap: 1 })
      doc.fontSize(9).font('Helvetica').fillColor(COLORS.darkGray)
      doc.text(s.matricula, desp1X + 2, y + 44, { lineGap: 1 })

      // Columna derecha
      doc.fontSize(8).font('Helvetica-Bold').fillColor(COLORS.primary)
      doc.text('Período:', desp2X + 2, y, { lineGap: 1 })
      doc.fontSize(9).font('Helvetica').fillColor(COLORS.darkGray)
      doc.text(r.ciclo || 'Enero 2026', desp2X + 2, y + 8, { lineGap: 1 })

      doc.fontSize(8).font('Helvetica-Bold').fillColor(COLORS.primary)
      doc.text('Vencimiento:', desp2X + 2, y + 17, { lineGap: 1 })
      doc.fontSize(10).font('Helvetica-Bold').fillColor(COLORS.red)
      doc.text(formatDate(dueDate), desp2X + 2, y + 25, { lineGap: 1 })

      doc.fontSize(9).font('Helvetica-Bold').fillColor(COLORS.primary)
      doc.text('TOTAL A PAGAR:', desp2X + 2, y + 36, { lineGap: 1 })
      doc.fontSize(13).font('Helvetica-Bold').fillColor(COLORS.green)
      doc.text(formatCurrency(total), desp2X + 2, y + 44, { lineGap: 2 })

      y += 54

      // AVISO DE SUSPENSIÓN
      y += 6
      doc.rect(MARGINS.left + 2, y, pw - 4, 32).fill(COLORS.lightGray).stroke(COLORS.red)
      doc.fontSize(9).font('Helvetica-Bold').fillColor(COLORS.red)
      doc.text('⚠ AVISO IMPORTANTE - SUSPENSIÓN POR FALTA DE PAGO', MARGINS.left + 5, y + 4, { lineGap: 2 })
      doc.fontSize(8).font('Helvetica').fillColor(COLORS.darkGray)
      doc.text('El no pago oportuno dentro de la fecha de vencimiento generará SUSPENSIÓN del servicio de acueducto.', MARGINS.left + 5, y + 14, { width: pw - 10, lineGap: 2 })
      doc.fontSize(8).font('Helvetica').fillColor(COLORS.darkGray)
      doc.text('Período de gracia: Hasta 10 días después del vencimiento indicado arriba.', MARGINS.left + 5, y + 21, { width: pw - 10, lineGap: 2 })

      // Stream PDF
      const chunks = []
      doc.on('data', (chunk) => chunks.push(chunk))
      doc.on('end', () => {
        resolve(Buffer.concat(chunks))
      })
      doc.on('error', (err) => {
        reject(err)
      })

      doc.end()

    } catch (error) {
      reject(error)
    }
  })
}

module.exports = { generateInvoicePdf }
