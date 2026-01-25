# =========================================
# 🚀 Instalador Acua - Windows
# Sistema de Gestión de Acueducto Rural
# =========================================

Write-Host "`n" -ForegroundColor Green
Write-Host "╔════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║      INSTALADOR - ACUA v1.0           ║" -ForegroundColor Cyan
Write-Host "║   Sistema de Gestión de Acueducto     ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host "`n" -ForegroundColor Green

# ========== VALIDACIONES ==========

Write-Host "📋 Validando requisitos..." -ForegroundColor Yellow

# Verificar Node.js
$nodeVersion = node --version 2>$null
if (-not $nodeVersion) {
    Write-Host "❌ Node.js no está instalado" -ForegroundColor Red
    Write-Host "   Descargar en: https://nodejs.org/" -ForegroundColor Yellow
    exit 1
} else {
    Write-Host "✅ Node.js $nodeVersion" -ForegroundColor Green
}

# Verificar npm
$npmVersion = npm --version 2>$null
if (-not $npmVersion) {
    Write-Host "❌ npm no está instalado" -ForegroundColor Red
    exit 1
} else {
    Write-Host "✅ npm $npmVersion" -ForegroundColor Green
}

# Verificar MySQL
$mysqlCheck = mysql --version 2>$null
if (-not $mysqlCheck) {
    Write-Host "⚠️  MySQL no encontrado en PATH" -ForegroundColor Yellow
    Write-Host "   Asegúrate que XAMPP/MySQL está instalado" -ForegroundColor Yellow
} else {
    Write-Host "✅ MySQL detectado" -ForegroundColor Green
}

Write-Host "`n✅ Validaciones completadas" -ForegroundColor Green

# ========== INSTALACIÓN ==========

Write-Host "`n📦 Instalando dependencias..." -ForegroundColor Yellow
npm install --no-audit --no-fund

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Error instalando dependencias" -ForegroundColor Red
    exit 1
}
Write-Host "✅ Dependencias instaladas" -ForegroundColor Green

# ========== PRISMA ==========

Write-Host "`n🔧 Configurando Prisma..." -ForegroundColor Yellow
npx prisma generate

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Error generando Prisma Client" -ForegroundColor Red
    exit 1
}
Write-Host "✅ Prisma Client generado" -ForegroundColor Green

# ========== BASE DE DATOS ==========

Write-Host "`n💾 Configurando base de datos..." -ForegroundColor Yellow

# Verificar si existe .env
if (-not (Test-Path ".env")) {
    Write-Host "`n⚙️  Creando archivo .env..." -ForegroundColor Cyan
    
    $envContent = @"
# Base de Datos MySQL
DATABASE_URL="mysql://root:@localhost:3306/acua_db"

# Servidor
PORT=3000
NODE_ENV=development

# Email (opcional)
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=tu_email@gmail.com
SMTP_PASS=tu_contraseña_app
SMTP_FROM=noreply@acua.com

# API (opcional)
API_URL=http://localhost:3000
API_TIMEOUT=30000
"@
    
    $envContent | Out-File -FilePath ".env" -Encoding UTF8
    Write-Host "✅ Archivo .env creado (revisar y ajustar según sea necesario)" -ForegroundColor Green
} else {
    Write-Host "✅ Archivo .env ya existe" -ForegroundColor Green
}

# Crear base de datos MySQL
Write-Host "`n🗄️  Creando base de datos..." -ForegroundColor Cyan
$createDbQuery = "CREATE DATABASE IF NOT EXISTS acua_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

try {
    $createDbQuery | mysql -u root 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Base de datos creada/verificada" -ForegroundColor Green
    } else {
        Write-Host "⚠️  No se pudo crear BD automáticamente" -ForegroundColor Yellow
        Write-Host "   Crea manualmente:" -ForegroundColor Yellow
        Write-Host "   1. Abre XAMPP Control Panel" -ForegroundColor Yellow
        Write-Host "   2. Inicia MySQL" -ForegroundColor Yellow
        Write-Host "   3. Ve a http://localhost/phpmyadmin" -ForegroundColor Yellow
        Write-Host "   4. Crea BD: acua_db (UTF8MB4)" -ForegroundColor Yellow
    }
} catch {
    Write-Host "⚠️  No se pudo verificar MySQL (¿está corriendo?)" -ForegroundColor Yellow
}

# ========== MIGRACIONES ==========

Write-Host "`n📤 Ejecutando migraciones..." -ForegroundColor Yellow

# Pequeña pausa para dar tiempo a MySQL
Start-Sleep -Seconds 2

npx prisma migrate deploy 2>$null

if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Migraciones completadas" -ForegroundColor Green
} else {
    Write-Host "⚠️  Migraciones - revisar conexión a BD" -ForegroundColor Yellow
    Write-Host "   Intenta ejecutar manualmente después:" -ForegroundColor Yellow
    Write-Host "   npx prisma migrate deploy" -ForegroundColor Cyan
}

# ========== DATOS INICIALES ==========

Write-Host "`n🌱 Cargando datos iniciales..." -ForegroundColor Yellow

if (Test-Path "scripts/seed.js") {
    node scripts/seed.js 2>$null
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Datos iniciales cargados" -ForegroundColor Green
    } else {
        Write-Host "⚠️  Datos iniciales - revisar BD" -ForegroundColor Yellow
    }
} else {
    Write-Host "⚠️  Script seed.js no encontrado" -ForegroundColor Yellow
}

# ========== RESUMEN ==========

Write-Host "`n" -ForegroundColor Green
Write-Host "╔════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║     ✅ INSTALACIÓN COMPLETADA         ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════╝" -ForegroundColor Cyan

Write-Host "`n📋 Próximos pasos:" -ForegroundColor Yellow

Write-Host "`n1️⃣  Revisa/configura el archivo .env:" -ForegroundColor Cyan
Write-Host "   - DATABASE_URL: Ajusta usuario/contraseña si es necesario" -ForegroundColor White
Write-Host "   - SMTP_*: Configura si quieres enviar emails" -ForegroundColor White

Write-Host "`n2️⃣  Inicia el servidor:" -ForegroundColor Cyan
Write-Host "   npm run dev" -ForegroundColor White

Write-Host "`n3️⃣  Abre en el navegador:" -ForegroundColor Cyan
Write-Host "   http://localhost:3000" -ForegroundColor White

Write-Host "`n4️⃣  Accede a los módulos:" -ForegroundColor Cyan
Write-Host "   - Dashboard:    http://localhost:3000" -ForegroundColor White
Write-Host "   - Suscriptores: http://localhost:3000/subscribers" -ForegroundColor White
Write-Host "   - Lecturas:     http://localhost:3000/readings" -ForegroundColor White
Write-Host "   - Facturas:     http://localhost:3000/invoices" -ForegroundColor White
Write-Host "   - Reportes:     http://localhost:3000/reports" -ForegroundColor White

Write-Host "`n📚 Documentación:" -ForegroundColor Yellow
Write-Host "   - INSTALL.md: Guía detallada de instalación" -ForegroundColor White
Write-Host "   - README.md: Información del proyecto" -ForegroundColor White

Write-Host "`n⚠️  Troubleshooting:" -ForegroundColor Yellow
Write-Host "   Si algo falla:" -ForegroundColor White
Write-Host "   - Verifica que MySQL está corriendo (XAMPP Control Panel)" -ForegroundColor White
Write-Host "   - Ejecuta: npm install" -ForegroundColor White
Write-Host "   - Ejecuta: npx prisma generate" -ForegroundColor White
Write-Host "   - Ejecuta: npx prisma migrate deploy" -ForegroundColor White

Write-Host "`n💡 Comandos útiles:" -ForegroundColor Yellow
Write-Host "   npm run dev              # Iniciar en desarrollo" -ForegroundColor Cyan
Write-Host "   npm run build            # Build para producción" -ForegroundColor Cyan
Write-Host "   npm test                 # Ejecutar tests" -ForegroundColor Cyan
Write-Host "   npx prisma studio       # Ver BD en interfaz gráfica" -ForegroundColor Cyan
Write-Host "   npm run clean            # Limpiar caché" -ForegroundColor Cyan

Write-Host "`n" -ForegroundColor Green
Write-Host "¡Instalación lista! 🚀 Presiona cualquier tecla para continuar..." -ForegroundColor Green
Read-Host
