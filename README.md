# 💧 ACUA - Sistema de Gestión para Acueducto Rural

<p align="center">
  <img src="https://raw.githubusercontent.com/franciscodiazo/acua/main/public/acua-banner.png" alt="ACUA Sistema" width="600">
</p>

Sistema completo de gestión y facturación para acueductos rurales, desarrollado con Laravel 10 y Bootstrap 5.

[![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.1+-blue.svg)](https://php.net)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple.svg)](https://getbootstrap.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

## 📋 Descripción

**ACUA** es un sistema integral diseñado específicamente para la gestión administrativa y financiera de acueductos rurales comunitarios. Permite el control completo de suscriptores, lecturas de medidores, facturación automática, gestión de pagos, créditos y reportes financieros.

### ✨ Características Principales

- 👥 **Gestión de Suscriptores**: Registro completo con matrícula, documentos, dirección, sector y estrato
- 📊 **Lecturas de Medidores**: Registro por ciclo con cálculo automático de consumo
- 🧾 **Cuotas Familiares**: Facturación automática basada en consumo con tarifas configurables
- 💰 **Pagos y Abonos**: Registro de pagos con múltiples métodos (efectivo, banco, transferencia, cheque)
- 💳 **Créditos y Deudas**: Gestión de créditos con abonos y control de saldos
- 📈 **Reportes**: Dashboard con gráficos, reportes diarios, por fechas y cierre anual
- 🖨️ **Impresión**: Recibos profesionales para cuotas, pagos y estados de cuenta
- 🔄 **Copias de Respaldo**: Exportación/importación de datos en CSV y SQL
- ⚙️ **Configuración**: Logo, información bancaria, tarifas personalizables

## 🎨 Características Visuales

- 🌊 **Colores institucionales**: Verde y azul (tomados del logo del acueducto)
- 📱 **Diseño responsivo**: Adaptable a móviles, tablets y escritorio
- 🎯 **Interfaz intuitiva**: Navegación clara con menú lateral
- 📄 **Paginación moderna**: Selector de registros por página (15, 50, 100, 200, 500)
- 📊 **Gráficos de consumo**: Visualización de historial en cuotas familiares (3 períodos)

## 🚀 Tecnologías Utilizadas

- **Backend**: Laravel 10.50.0
- **Frontend**: Bootstrap 5.3.2, Bootstrap Icons 1.11.1, jQuery 3.7.1
- **Base de Datos**: MySQL 5.7+
- **PHP**: 8.1.25 o superior
- **Servidor**: Apache (XAMPP recomendado para desarrollo)

## 📦 Requisitos Previos

Antes de instalar, asegúrate de tener instalado:

- [PHP 8.1+](https://www.php.net/downloads)
- [Composer](https://getcomposer.org/)
- [MySQL 5.7+](https://www.mysql.com/) o MariaDB
- [XAMPP](https://www.apachefriends.org/) (recomendado) o servidor web equivalente
- [Git](https://git-scm.com/)
- [Node.js y NPM](https://nodejs.org/) (opcional, para compilar assets)

### Extensiones PHP Requeridas

Verifica que las siguientes extensiones estén habilitadas en `php.ini`:

```ini
extension=fileinfo
extension=pdo_mysql
extension=mbstring
extension=openssl
extension=curl
extension=zip
extension=gd
```

## 🔧 Instalación

### 1. Clonar el Repositorio

```bash
git clone https://github.com/franciscodiazo/acua.git
cd acua
```

### 2. Instalar Dependencias de PHP

```bash
composer install
```

### 3. Configurar Variables de Entorno

Copia el archivo de ejemplo y configura tus credenciales:

```bash
cp .env.example .env
```

Edita el archivo `.env` y configura la base de datos:

```env
APP_NAME=ACUA
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=acua_db
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generar Clave de Aplicación

```bash
php artisan key:generate
```

### 5. Crear Base de Datos

Accede a MySQL y crea la base de datos:

```sql
CREATE DATABASE acua_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

O usando XAMPP:
1. Abre `http://localhost/phpmyadmin`
2. Crea una nueva base de datos llamada `acua_db`
3. Selecciona cotejamiento `utf8mb4_unicode_ci`

### 6. Ejecutar Migraciones y Seeders

```bash
php artisan migrate --seed
```

Esto creará todas las tablas e insertará datos de ejemplo.

### 7. Crear Enlace Simbólico para Storage

```bash
php artisan storage:link
```

Esto permite que los archivos cargados (como el logo) sean accesibles públicamente.

### 8. Compilar Assets (Opcional)

Si deseas compilar los assets frontend:

```bash
npm install
npm run dev
```

Para producción:

```bash
npm run build
```

### 9. Iniciar Servidor de Desarrollo

```bash
php artisan serve
```

La aplicación estará disponible en: `http://127.0.0.1:8000`

## 🗃️ Estructura de la Base de Datos

El sistema utiliza las siguientes tablas principales:

| Tabla | Descripción |
|-------|-------------|
| `companies` | Información de la empresa/acueducto |
| `price_settings` | Configuración de tarifas |
| `subscribers` | Suscriptores del servicio |
| `readings` | Lecturas de medidores |
| `invoices` | Cuotas familiares/facturas |
| `payments` | Pagos realizados |
| `credits` | Créditos y deudas |
| `credit_payments` | Abonos a créditos |

## 👤 Datos de Prueba

El seeder crea datos de ejemplo que puedes usar para explorar el sistema:

- **Empresa**: ACUEDUCTO RURAL
- **Suscriptores**: 5 suscriptores de ejemplo
- **Tarifas**: 
  - Cuota básica: $25,000 (40m³)
  - Valor adicional: $1,500/m³

## 📖 Guía de Uso

### 1. Configuración Inicial

1. Ve a **Configuración → Empresa**
2. Carga el logo de tu acueducto (PNG/JPG, máx. 2MB)
3. Configura información bancaria y mensaje personalizado
4. Ve a **Configuración → Tarifas** y ajusta los precios según tu tarifa local

### 2. Gestión de Suscriptores

1. Ve a **Suscriptores → Nuevo**
2. Registra la matrícula (única), documento, nombres, dirección, sector, estrato
3. Usa la búsqueda para encontrar suscriptores rápidamente
4. Consulta el **Estado de Cuenta** de cada suscriptor con un clic

### 3. Registro de Lecturas

1. Ve a **Lecturas → Nueva**
2. Selecciona el suscriptor
3. Selecciona el ciclo (formato: YYYY-MM)
4. Ingresa lectura anterior y actual
5. El consumo se calcula automáticamente

### 4. Facturación Automática

**Opción 1: Facturación Individual**
- Desde **Lecturas**, haz clic en "Facturar" en una lectura específica

**Opción 2: Facturación Masiva**
1. Ve a **Lecturas**
2. Filtra por ciclo si es necesario
3. Selecciona múltiples lecturas pendientes
4. Haz clic en "Facturar Seleccionadas"
5. Las cuotas se generan con cálculo automático según tarifas

### 5. Registro de Pagos

1. Ve a **Abonos/Pagos → Nuevo**
2. Selecciona el suscriptor
3. Ingresa el monto
4. Selecciona método de pago (efectivo, banco, transferencia, cheque, otro)
5. Agrega referencia si aplica
6. El sistema aplica automáticamente el pago a facturas pendientes (FIFO)
7. Imprime el recibo con doble copia (cliente/archivo)

### 6. Gestión de Créditos/Deudas

**Crear Crédito:**
1. Ve a **Créditos → Nuevo**
2. Selecciona el suscriptor
3. Tipo: crédito, deuda o cuota pendiente
4. Ingresa concepto y monto
5. El saldo se actualiza automáticamente con cada abono

**Registrar Abono:**
1. Ve a **Abonos a Créditos → Nuevo**
2. Selecciona el suscriptor y el crédito
3. Ingresa el monto del abono
4. Selecciona método de pago
5. El saldo del crédito se recalcula automáticamente

### 7. Reportes y Dashboard

**Dashboard Principal:**
- Gráfico de ingresos del mes
- Totales: pagado hoy, pendientes, créditos
- Acceso rápido a reportes

**Reportes Disponibles:**
- **Reporte Diario**: Movimientos y totales del día actual
- **Reporte por Fechas**: Selecciona rango personalizado
- **Cierre Anual**: Resumen completo del año con gráficos

### 8. Copias de Respaldo

**Exportar Datos:**
1. Ve a **Configuración → Copias de Respaldo**
2. **Backup SQL**: Descarga base de datos completa
3. **Exportar CSV**: Suscriptores, lecturas o créditos individuales

**Importar Datos:**
1. Descarga la plantilla CSV del tipo de dato
2. Llena la plantilla en Excel
3. Guarda como CSV (UTF-8)
4. Sube el archivo
5. El sistema valida y reporta errores si los hay

## 🎨 Personalización

### Cambiar Colores Institucionales

Edita `resources/views/invoices/print.blade.php` y modifica las variables CSS:

```css
:root {
    --color-azul: #1E88E5;          /* Azul principal */
    --color-azul-oscuro: #1565C0;   /* Azul oscuro */
    --color-azul-claro: #64B5F6;    /* Azul claro */
    --color-verde: #43A047;         /* Verde principal */
    --color-verde-oscuro: #2E7D32;  /* Verde oscuro */
    --color-verde-claro: #81C784;   /* Verde claro */
}
```

### Modificar Tarifas

Ve a **Configuración → Tarifas** en el sistema o edita directamente:

```php
// database/seeders/DatabaseSeeder.php
PriceSetting::create([
    'consumo_basico' => 40,      // m³ incluidos
    'valor_basico' => 25000,     // Precio base
    'valor_adicional' => 1500,   // Por m³ adicional
]);
```

### Personalizar Plantillas de Impresión

Las plantillas están en:
- Cuotas: `resources/views/invoices/print.blade.php`
- Pagos: `resources/views/payments/print.blade.php`
- Abonos: `resources/views/credit-payments/print.blade.php`

## 🔄 Actualización del Sistema

Para actualizar a la última versión:

```bash
# Detén el servidor si está corriendo
git pull origin main
composer install
php artisan migrate
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan serve
```

## 🐛 Solución de Problemas

### Error: "Class not found"
```bash
composer dump-autoload
php artisan clear-compiled
```

### Error: Migraciones ya ejecutadas
```bash
php artisan migrate:fresh --seed
```
⚠️ **Advertencia**: Esto eliminará todos los datos existentes.

### Error: "SQLSTATE[HY000] [1045] Access denied"
Verifica las credenciales en `.env`:
```env
DB_USERNAME=root
DB_PASSWORD=tu_contraseña
```

### Permisos en Linux/Mac
```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### Error: "The stream or file ... could not be opened"
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Limpiar todos los cachés
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan clear-compiled
```

### Logo no se muestra
```bash
php artisan storage:link
```

## 📊 Lógica de Facturación

El sistema calcula las cuotas familiares de la siguiente manera:

```
Si consumo <= consumo_basico (40m³):
    Total = valor_basico ($25,000)

Si consumo > consumo_basico:
    Excedente = consumo - consumo_basico
    Total = valor_basico + (Excedente × valor_adicional)

Ejemplo:
    Consumo = 55m³
    Excedente = 55 - 40 = 15m³
    Total = $25,000 + (15 × $1,500) = $47,500
```

## 🤝 Contribuir

Las contribuciones son bienvenidas. Por favor:

1. Haz fork del proyecto
2. Crea una rama para tu feature (`git checkout -b feature/NuevaCaracteristica`)
3. Commit tus cambios (`git commit -m 'Agregar nueva característica'`)
4. Push a la rama (`git push origin feature/NuevaCaracteristica`)
5. Abre un Pull Request

## 📝 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo [LICENSE](LICENSE) para más detalles.

## 👨‍💻 Autor

**Francisco Díaz**
- GitHub: [@franciscodiazo](https://github.com/franciscodiazo)
- Repositorio: [https://github.com/franciscodiazo/acua](https://github.com/franciscodiazo/acua)

## 🙏 Agradecimientos

- [Laravel](https://laravel.com) - Framework PHP elegante y expresivo
- [Bootstrap](https://getbootstrap.com) - Framework CSS responsivo
- [Bootstrap Icons](https://icons.getbootstrap.com) - Biblioteca de iconos
- Comunidad de Acueductos Rurales de Colombia

## 📞 Soporte

Si encuentras algún problema o tienes sugerencias:

1. Abre un [Issue](https://github.com/franciscodiazo/acua/issues)
2. Proporciona detalles del error
3. Incluye capturas de pantalla si es posible
4. Especifica versión de PHP, Laravel y sistema operativo

## 🗺️ Roadmap

### Próximas Características

- [ ] Panel de administración de usuarios
- [ ] Notificaciones por correo/SMS
- [ ] Generación de facturas en PDF
- [ ] API REST para integraciones
- [ ] Aplicación móvil
- [ ] Módulo de cortes y reconexiones
- [ ] Reportes Excel avanzados
- [ ] Múltiples tarifas por sector/estrato

---

<p align="center">
  Hecho con ❤️ para las comunidades rurales de Colombia
</p>

<p align="center">
  <strong>ACUA © 2026</strong>
</p>
