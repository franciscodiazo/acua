import { useEffect, useState } from 'react'

export default function Settings() {
  const [config, setConfig] = useState(null)
  const [loading, setLoading] = useState(false)
  const [saving, setSaving] = useState(false)
  const [message, setMessage] = useState('')

  const [form, setForm] = useState({
    name: '',
    nit: '',
    phone: '',
    address: '',
    logoUrl: '',
    basicTariff: 25000,
    threshold: 40,
    unitPrice: 1500,
    currency: 'COP'
  })

  useEffect(() => {
    fetchConfig()
  }, [])

  async function fetchConfig() {
    setLoading(true)
    try {
      const res = await fetch('/api/config')
      const json = await res.json()
      if (json.config) {
        setConfig(json.config)
        setForm({
          name: json.config.name || '',
          nit: json.config.nit || '',
          phone: json.config.phone || '',
          address: json.config.address || '',
          logoUrl: json.config.logoUrl || '',
          basicTariff: json.config.basicTariff || 25000,
          threshold: json.config.threshold || 40,
          unitPrice: json.config.unitPrice || 1500,
          currency: json.config.currency || 'COP'
        })
      }
    } catch (e) {
      console.error('Error fetching config:', e)
      setMessage('Error al cargar la configuración')
    }
    setLoading(false)
  }

  async function handleSave(e) {
    e.preventDefault()
    setSaving(true)
    setMessage('')

    try {
      const res = await fetch('/api/config', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          ...form,
          basicTariff: Number(form.basicTariff),
          threshold: Number(form.threshold),
          unitPrice: Number(form.unitPrice)
        })
      })

      const json = await res.json()

      if (!res.ok) throw new Error(json.error)

      setConfig(json.config)
      setMessage('✓ Configuración guardada exitosamente')
      setTimeout(() => setMessage(''), 3000)
    } catch (e) {
      console.error('Error saving config:', e)
      setMessage(`✗ Error: ${e.message}`)
    } finally {
      setSaving(false)
    }
  }

  function handleChange(e) {
    const { name, value } = e.target
    setForm({ ...form, [name]: value })
  }

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <p className="text-gray-600">Cargando configuración...</p>
      </div>
    )
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <div className="max-w-4xl mx-auto py-8 px-4">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-3xl font-bold text-gray-900 mb-2">Configuración del Sistema</h1>
          <p className="text-gray-600">Datos de la empresa y configuración de tarifas</p>
        </div>

        {/* Message */}
        {message && (
          <div className={`mb-6 p-4 rounded-lg ${
            message.includes('✓') 
              ? 'bg-green-50 border-l-4 border-green-500 text-green-700' 
              : 'bg-red-50 border-l-4 border-red-500 text-red-700'
          }`}>
            {message}
          </div>
        )}

        {/* Form */}
        <form onSubmit={handleSave} className="space-y-8">
          {/* Datos de la Empresa */}
          <div className="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <h2 className="text-xl font-bold text-gray-900 mb-6 flex items-center">
              <span className="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
              Datos de la Empresa
            </h2>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              {/* Nombre Empresa */}
              <div>
                <label className="block text-sm font-semibold text-gray-700 mb-2">
                  Nombre de la Empresa
                </label>
                <input
                  type="text"
                  name="name"
                  value={form.name}
                  onChange={handleChange}
                  className="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Ej: Acueducto Rural"
                />
              </div>

              {/* NIT */}
              <div>
                <label className="block text-sm font-semibold text-gray-700 mb-2">
                  NIT
                </label>
                <input
                  type="text"
                  name="nit"
                  value={form.nit}
                  onChange={handleChange}
                  className="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Ej: 123456789"
                />
              </div>

              {/* Teléfono */}
              <div>
                <label className="block text-sm font-semibold text-gray-700 mb-2">
                  Teléfono
                </label>
                <input
                  type="tel"
                  name="phone"
                  value={form.phone}
                  onChange={handleChange}
                  className="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Ej: +57 310 123 4567"
                />
              </div>

              {/* Dirección */}
              <div>
                <label className="block text-sm font-semibold text-gray-700 mb-2">
                  Dirección
                </label>
                <input
                  type="text"
                  name="address"
                  value={form.address}
                  onChange={handleChange}
                  className="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Ej: Calle 1 #123"
                />
              </div>

              {/* URL Logo */}
              <div className="md:col-span-2">
                <label className="block text-sm font-semibold text-gray-700 mb-2">
                  URL del Logo
                </label>
                <input
                  type="url"
                  name="logoUrl"
                  value={form.logoUrl}
                  onChange={handleChange}
                  className="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Ej: https://example.com/logo.png"
                />
              </div>
            </div>
          </div>

          {/* Configuración de Tarifas */}
          <div className="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <h2 className="text-xl font-bold text-gray-900 mb-6 flex items-center">
              <span className="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
              Configuración de Tarifas
            </h2>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              {/* Tarifa Base */}
              <div>
                <label className="block text-sm font-semibold text-gray-700 mb-2">
                  Tarifa Base (COP)
                </label>
                <input
                  type="number"
                  name="basicTariff"
                  value={form.basicTariff}
                  onChange={handleChange}
                  className="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                  placeholder="25000"
                  min="0"
                  step="100"
                />
                <p className="text-xs text-gray-500 mt-1">Costo base mensual</p>
              </div>

              {/* Umbral de Consumo */}
              <div>
                <label className="block text-sm font-semibold text-gray-700 mb-2">
                  Umbral de Consumo (m³)
                </label>
                <input
                  type="number"
                  name="threshold"
                  value={form.threshold}
                  onChange={handleChange}
                  className="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                  placeholder="40"
                  min="0"
                  step="1"
                />
                <p className="text-xs text-gray-500 mt-1">m³ incluido en tarifa base</p>
              </div>

              {/* Precio por m³ */}
              <div>
                <label className="block text-sm font-semibold text-gray-700 mb-2">
                  Precio por m³ (COP)
                </label>
                <input
                  type="number"
                  name="unitPrice"
                  value={form.unitPrice}
                  onChange={handleChange}
                  className="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                  placeholder="1500"
                  min="0"
                  step="100"
                />
                <p className="text-xs text-gray-500 mt-1">Costo adicional por m³ excedente</p>
              </div>

              {/* Moneda */}
              <div>
                <label className="block text-sm font-semibold text-gray-700 mb-2">
                  Moneda
                </label>
                <select
                  name="currency"
                  value={form.currency}
                  onChange={handleChange}
                  className="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
                  <option value="COP">COP - Peso Colombiano</option>
                  <option value="USD">USD - Dólar</option>
                  <option value="EUR">EUR - Euro</option>
                </select>
              </div>
            </div>

            {/* Tariff Preview */}
            <div className="mt-8 p-4 bg-gray-50 rounded-lg border-l-4 border-yellow-400">
              <h3 className="font-bold text-gray-900 mb-3">Ejemplo de Cálculo:</h3>
              <div className="space-y-2 text-sm text-gray-700">
                <p>
                  • Consumo ≤ {form.threshold}m³: <strong>${form.basicTariff.toLocaleString('es-CO')}</strong>
                </p>
                <p>
                  • Consumo &gt; {form.threshold}m³: <strong>${form.basicTariff.toLocaleString('es-CO')} + (excedente × ${form.unitPrice.toLocaleString('es-CO')})</strong>
                </p>
                <p className="mt-3 text-gray-600">
                  Ej: Si consume {form.threshold + 10}m³: ${(form.basicTariff + (10 * form.unitPrice)).toLocaleString('es-CO')} {form.currency}
                </p>
              </div>
            </div>
          </div>

          {/* Buttons */}
          <div className="flex gap-4 justify-end">
            <button
              type="button"
              onClick={() => fetchConfig()}
              className="px-6 py-2 border-2 border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-50 transition"
            >
              Cancelar
            </button>
            <button
              type="submit"
              disabled={saving}
              className={`px-6 py-2 font-bold rounded-lg text-white transition ${
                saving
                  ? 'bg-blue-400 cursor-not-allowed'
                  : 'bg-blue-600 hover:bg-blue-700 shadow-md hover:shadow-lg'
              }`}
            >
              {saving ? 'Guardando...' : 'Guardar Configuración'}
            </button>
          </div>
        </form>
      </div>
    </div>
  )
}
