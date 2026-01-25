import Link from 'next/link'

export default function Sidebar() {
  return (
    <div className="sidebar-inner">
      <div className="brand">
        <div className="logo">{/* simple drop icon */}
          <svg width="34" height="34" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2C10.3 4.7 7 8 7 11.5A5 5 0 0 0 12 22a5 5 0 0 0 5-5c0-3.5-3.3-6.8-5-9.5z" fill="#10B981" opacity="0.95"/>
          </svg>
        </div>
        <div className="brand-text">
          <div className="brand-title">AquaGestión</div>
          <div className="brand-sub">Acueducto Rural</div>
        </div>
      </div>

      <div className="role-select card mt-2">
        <label className="label small-muted">Rol Actual</label>
        <select className="input">
          <option>Administradir</option>
          <option>Supernumerario</option>
          <option>Auxiliar</option>
        </select>
      </div>

      <nav className="nav mt-3">
        <Link href="/" className="nav-item active">
          <span className="icon">🏠</span>
          <span>Dashboard</span>
        </Link>
        <Link href="/subscribers" className="nav-item">
          <span className="icon">👥</span>
          <span>Suscriptores</span>
        </Link>
        <Link href="/readings" className="nav-item">
          <span className="icon">📋</span>
          <span>Lecturas</span>
        </Link>
        <Link href="/invoices" className="nav-item">
          <span className="icon">💲</span>
          <span>Recibos</span>
        </Link>
        <Link href="/credits" className="nav-item">
          <span className="icon">💳</span>
          <span>Créditos</span>
        </Link>
        <Link href="/payments" className="nav-item">
          <span className="icon">💸</span>
          <span>Abonos</span>
        </Link>
        <Link href="/settings" className="nav-item">
          <span className="icon">⚙️</span>
          <span>Configuración</span>
        </Link>
      </nav>

      <div className="spacer" />

      <div className="cycle card">
        <div className="small-muted">Ciclo Actual</div>
        <div className="cycle-value">2024 - Ciclo 2</div>
      </div>
    </div>
  )
}
