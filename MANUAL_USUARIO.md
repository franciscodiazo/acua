# 📖 Manual de Usuario - Acua

## Tabla de Contenidos

1. [Introducción](#introducción)
2. [Dashboard](#dashboard)
3. [Gestión de Suscriptores](#gestión-de-suscriptores)
4. [Registro de Lecturas](#registro-de-lecturas)
5. [Facturas](#facturas)
6. [Reportes](#reportes)
7. [Créditos y Pagos](#créditos-y-pagos)
8. [Configuración](#configuración)

---

## Introducción

Acua es un sistema de gestión integral para acueductos rurales. Te permite:

- 📋 Administrar suscriptores
- 💧 Registrar consumo de agua
- 🧾 Generar facturas automáticas en PDF
- 📊 Ver reportes financieros
- 💰 Gestionar créditos y pagos

### Acceso al Sistema

1. Abre tu navegador
2. Ve a: `http://localhost:3000`
3. ¡Listo! No requiere login en modo desarrollo

---

## Dashboard

### Pantalla Principal

El dashboard muestra un resumen ejecutivo del negocio:

- **Suscriptores Activos**: Total de clientes activos
- **Consumo Total Ciclo**: Cantidad de agua consumida este mes
- **Ingresos Generados**: Total facturado
- **Pagos Pendientes**: Total de facturas sin pagar

### Widgets Disponibles

Cada widget muestra información en tiempo real. Haz clic en cualquiera para ir al módulo detallado.

---

## Gestión de Suscriptores

### Acceder

Menu → Suscriptores → http://localhost:3000/subscribers

### Crear Nuevo Suscriptor

1. Haz clic en botón **"+ Crear Suscriptor"**
2. Completa el formulario:
   - **Nombre**: Nombre del suscriptor
   - **Apellido**: Apellido del suscriptor
   - **Matrícula**: Identificador único (ej: A-001)
   - **Email**: Correo electrónico (opcional)
   - **Teléfono**: Número de contacto (opcional)
   - **Dirección**: Domicilio completo
   - **Estado**: Activo/Inactivo

3. Haz clic en **"Guardar"**

### Buscar Suscriptor

1. Usa la barra de búsqueda en la parte superior
2. Escribe: nombre, apellido o matrícula
3. La tabla se filtra automáticamente

### Editar Suscriptor

1. Busca el suscriptor
2. Haz clic en el botón **"✏️ Editar"**
3. Modifica los datos necesarios
4. Haz clic en **"Guardar cambios"**

### Eliminar Suscriptor

1. Busca el suscriptor
2. Haz clic en el botón **"🗑️ Eliminar"**
3. Confirma la acción

### Filtros Avanzados

**Ordenamiento**: Haz clic en los encabezados de columna para ordenar por:
- Nombre
- Matrícula
- Estado

**Paginación**: Usa el selector en la parte inferior para mostrar:
- 50 registros
- 100 registros
- 200 registros

---

## Registro de Lecturas

### Acceder

Menu → Lecturas → http://localhost:3000/readings

### Registrar Nueva Lectura

1. Haz clic en **"+ Nueva Lectura"**
2. Completa:
   - **Suscriptor**: Selecciona de la lista
   - **Ciclo**: Período (ej: Enero 2026)
   - **Lectura Anterior**: Último valor registrado
   - **Lectura Actual**: Nuevo valor del medidor
   - **Fecha**: Día del registro

3. El sistema calcula automáticamente:
   - **Consumo**: Diferencia entre lecturas
   - **Valor Total**: Según tarifa

4. Haz clic en **"Guardar"**

### Ver Histórico

En la tabla de lecturas, puedes ver:
- Última lectura de cada suscriptor
- Consumo del ciclo
- Fecha de registro
- Histórico de 6 últimos ciclos

### Comparativa de Consumo

Cada lectura muestra:
- Tu consumo vs. promedio del barrio
- Recomendaciones para ahorrar agua
- Gráfica de 6 últimos ciclos

### Paginación de Lecturas

Selector: 50, 100, 200 o 500 registros por página

---

## Facturas

### Acceder

Menu → Facturas → http://localhost:3000/invoices

### Ver Listado

La tabla muestra:
- Número de factura
- Suscriptor
- Total
- Estado (Pendiente/Pagada)
- Fecha de vencimiento

### Descargar Factura en PDF

1. Busca la factura en la lista
2. Haz clic en **"📥 Descargar PDF"**
3. Se abre la factura en PDF para ver/descargar

### PDF de Factura Incluye

- ✅ Logo de la empresa
- ✅ Datos del suscriptor
- ✅ Consumo detallado
- ✅ Gráfica de 6 últimos ciclos
- ✅ Comparativa con vecinos
- ✅ Recomendaciones de ahorro
- ✅ Aviso de suspensión (si aplica)
- ✅ Fecha de vencimiento

### Enviar por Email

1. Selecciona la factura
2. Haz clic en **"📧 Enviar Email"**
3. Ingresa email del suscriptor
4. Haz clic en **"Enviar"**

---

## Reportes

### Acceder

Menu → Reportes → http://localhost:3000/reports

### Generar Reporte

1. Selecciona período:
   - **Día**: Movimiento de hoy (cuadre de caja)
   - **Mes**: Movimiento del mes actual
   - **Año**: Movimiento del año actual

2. Haz clic en **"Generar Reporte"**

### Información del Reporte

Muestra:
- 📊 **Lecturas**: Cantidad registrada en el período
- 💵 **Total Facturado**: Ingresos por consumo
- ✅ **Total Pagado**: Dinero recibido
- 💰 **Total Créditos**: Dinero prestado
- 📈 **Balance**: Ingresos - Pagos - Créditos

### Tabla de Movimientos

Detalle de cada transacción:
- Fecha
- Suscriptor
- Tipo (Lectura/Factura/Pago)
- Monto

### Imprimir Reporte

1. Haz clic en **"🖨️ Imprimir"**
2. Se abre la vista de impresión
3. Selecciona impresora o "Guardar como PDF"
4. Haz clic en **"Imprimir"**

### Reporte Impreso Incluye

- Encabezado con logo
- Período del reporte
- Resumen de 4 métricas
- Balance con código de colores (✅ positivo / ❌ negativo)
- Tabla completa de transacciones
- Pie con fecha de generación

---

## Créditos y Pagos

### Crear Crédito

1. Ve a Menu → Créditos
2. Haz clic en **"+ Crear Crédito"**
3. Selecciona suscriptor
4. Ingresa monto
5. Agrega descripción (opcional)
6. Haz clic en **"Guardar"**

### Registrar Pago de Crédito

1. En la lista de créditos
2. Haz clic en **"💳 Pagar"**
3. Ingresa monto a pagar
4. Selecciona fecha de pago
5. Haz clic en **"Registrar Pago"**

### Ver Histórico de Créditos

- Suscriptor
- Monto original
- Pagos realizados
- Saldo pendiente
- Fecha de creación

---

## Configuración

### Acceder

Menu → Configuración → http://localhost:3000/settings

### Datos de la Empresa

Personaliza:
- Nombre de la empresa
- NIT/RUC
- Teléfono
- Email
- Dirección

### Parámetros de Facturación

- **Tarifa Básica**: Cargo fijo por consumo (ej: $25.000)
- **Umbral de Consumo**: Límite para tarifa básica (ej: 40 m³)
- **Precio Unitario**: Costo por m³ adicional (ej: $1.500)

### Ciclos de Facturación

- **Período**: Días entre lecturas (ej: 30)
- **Vencimiento**: Días para pago (ej: 15)
- **Suspensión**: Días para corte por mora (ej: 45)

### Email/SMTP (Avanzado)

Configura para enviar facturas por email:
- Servidor SMTP
- Puerto
- Usuario
- Contraseña
- Email remitente

---

## 🆘 Solución de Problemas

### "No se carga el dashboard"
```
✅ Verifica que el servidor está corriendo (npm run dev)
✅ Intenta refrescar: Ctrl + F5
✅ Limpia cookies: Ctrl + Shift + Delete
```

### "No puedo crear suscriptor"
```
✅ Verifica que todos los campos requeridos están llenos
✅ La matrícula debe ser única
✅ Abre la consola (F12) para ver errores
```

### "Las facturas no se generan"
```
✅ Verifica que la lectura se registró correctamente
✅ El consumo debe ser > 0
✅ Revisa la consola del navegador (F12)
```

### "Email no se envía"
```
✅ Configura SMTP en Configuración
✅ Verifica que el suscriptor tiene email
✅ Abre consola para ver si hay errores
```

---

## 💡 Tips y Trucos

### Búsqueda Rápida
- Suscriptores: Busca por nombre o matrícula
- Lecturas: Filtra por suscriptor en la tabla
- Facturas: Busca por número o estado

### Atajos de Teclado
- `Ctrl + F5`: Actualizar página sin caché
- `F12`: Abrir consola de desarrollador
- `Tab`: Navegar entre campos

### Exportar Datos
1. Ve al módulo
2. Haz clic en **"⬇️ Exportar"**
3. Selecciona formato (CSV/Excel/PDF)
4. Se descarga automáticamente

### Importar Suscriptores
1. Ve a Suscriptores
2. Haz clic en **"⬆️ Importar"**
3. Selecciona archivo CSV
4. Revisa datos y confirma

---

## 📞 Contacto y Soporte

**Soporte Técnico:**
- Email: franciscojdiazo@gmail.com
- WhatsApp: +57 XXX XXX XXXX (si está disponible)

**Reportar Bug:**
1. Anota el paso que genera el error
2. Toma captura de pantalla
3. Envía detalles al soporte técnico

---

## 📋 Checklist de Operación Diaria

- [ ] Revisar nuevas lecturas registradas
- [ ] Generar facturas del día
- [ ] Verificar pagos recibidos
- [ ] Ver reportes de movimiento diario
- [ ] Actualizar créditos si aplica
- [ ] Respaldar datos (semanal)

---

## 📚 Documentación Relacionada

- [INSTALL.md](./INSTALL.md) - Instalación técnica
- [README.md](./README.md) - Información del proyecto
- [QUICKSTART.md](./QUICKSTART.md) - Inicio rápido

---

**Manual v1.0 - Acua 2026**

*Última actualización: 25 de Enero, 2026*
