# Setup DB script for Acueducto Rural
# Usage: powershell -ExecutionPolicy Bypass -File .\scripts\setup-db.ps1

param(
  [switch]$Force
)

Write-Host "==> Creando base de datos 'acuarius_db' (usuario: root)..."
# Create DB if not exists
$mysqlCmd = Get-Command mysql -ErrorAction SilentlyContinue
if (-not $mysqlCmd) {
  # Try common XAMPP path
  $xamppPath = "C:\\xampp\\mysql\\bin\\mysql.exe"
  if (Test-Path $xamppPath) { $mysqlCmd = $xamppPath }
}

if ($mysqlCmd) {
  & $mysqlCmd -u root -e "CREATE DATABASE IF NOT EXISTS acuarius_db;"
  if ($LASTEXITCODE -ne 0) {
    Write-Host "ERROR: no se pudo crear la base de datos. Verifica que MySQL esté corriendo y las credenciales." -ForegroundColor Red
    exit 1
  }
} else {
  Write-Host "ADVERTENCIA: no se encontró el comando 'mysql' en PATH ni en C:\\xampp\\mysql\\bin. Puedes crear la base de datos manualmente o añadir 'mysql' a PATH." -ForegroundColor Yellow
  Write-Host "Crea la DB manualmente con: mysql -u root -e \"CREATE DATABASE IF NOT EXISTS acuarius_db;\"" -ForegroundColor Yellow
  if (-not (Read-Host "¿Continuar sin crear la DB? (s/n)")) { Write-Host "Abortando."; exit 1 }
}

Write-Host "==> Generando cliente Prisma..."
# Attempt to remove possible locked prisma binary before generating
$prismaDir = Join-Path -Path (Get-Location) -ChildPath "node_modules\.prisma\client"
if (Test-Path $prismaDir) {
  try {
    Remove-Item -Recurse -Force $prismaDir -ErrorAction Stop
    Write-Host "Se eliminó carpeta previa de prisma client para evitar conflictos." -ForegroundColor Green
  } catch {
    Write-Host "ADVERTENCIA: no se pudo eliminar 'node_modules/.prisma/client'. Puede que otro proceso esté usando el archivo (p. ej. servidor en ejecución o antivirus)." -ForegroundColor Yellow
    Write-Host "Cierra procesos Node/Next que usen la aplicación y vuelve a ejecutar este script o ejecuta 'npx prisma generate' manualmente en una terminal con permisos." -ForegroundColor Yellow
    Write-Host "Mensaje de error: $_" -ForegroundColor Yellow
    exit 1
  }
}

npx prisma generate
if ($LASTEXITCODE -ne 0) { Write-Host "ERROR: prisma generate falló" -ForegroundColor Red; exit 1 }

$hasMigrations = Test-Path "prisma/migrations"
if ($hasMigrations -and -Not $Force) {
  Write-Host "==> Aplicando migraciones existentes (deploy)..."
  npx prisma migrate deploy
  if ($LASTEXITCODE -ne 0) { Write-Host "ERROR: prisma migrate deploy falló" -ForegroundColor Red; exit 1 }
} else {
  Write-Host "==> Ejecutando migración de desarrollo (crea primeras tablas)..."
  npx prisma migrate dev --name init
  if ($LASTEXITCODE -ne 0) { Write-Host "ERROR: prisma migrate dev falló" -ForegroundColor Red; exit 1 }
}

Write-Host "==> Estado de migraciones:"
npx prisma migrate status

Write-Host "==> Completado. Puedes arrancar la app con: npm run dev" -ForegroundColor Green
