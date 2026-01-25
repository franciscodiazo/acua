import Sidebar from './Sidebar'

export default function Layout({ children }) {
  return (
    <div className="app-shell">
      <aside className="sidebar">
        <Sidebar />
      </aside>

      <div className="main-area">
        <div className="main-scroll">
          <main className="container">
            {children}
          </main>
          <footer className="mt-8 mb-6 text-center text-sm small-muted">© {new Date().getFullYear()} AquaGestión</footer>
        </div>
      </div>
    </div>
  )
} 
