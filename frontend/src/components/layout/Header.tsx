import { Moon, Sun, LogOut, Search, Menu } from 'lucide-react'
import { useAuthStore } from '@/stores/authStore'
import { useThemeStore } from '@/stores/themeStore'
import { useNavigate } from 'react-router-dom'
import api from '@/lib/api'

export default function Header() {
  const { user, logout, token } = useAuthStore()
  const { dark, toggle } = useThemeStore()
  const navigate = useNavigate()

  const handleLogout = async () => {
    try {
      if (token) await api.post('/auth/logout')
    } finally {
      logout()
      navigate('/login')
    }
  }

  return (
    <header className="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-zinc-200/80 bg-white/80 px-4 backdrop-blur-md dark:border-zinc-800 dark:bg-zinc-900/80 lg:px-8">
      <div className="flex items-center gap-4">
        <button className="rounded-lg p-2 hover:bg-zinc-100 lg:hidden dark:hover:bg-zinc-800">
          <Menu className="h-5 w-5" />
        </button>
        <div className="relative hidden sm:block">
          <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
          <input
            type="search"
            placeholder="Buscar..."
            className="w-64 rounded-lg border border-zinc-200 bg-zinc-50 py-2 pl-10 pr-4 text-sm outline-none focus:border-brand-500 dark:border-zinc-700 dark:bg-zinc-800"
          />
        </div>
      </div>
      <div className="flex items-center gap-3">
        <button onClick={toggle} className="rounded-lg p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800">
          {dark ? <Sun className="h-5 w-5" /> : <Moon className="h-5 w-5" />}
        </button>
        <div className="hidden text-right sm:block">
          <p className="text-sm font-medium">{user?.name}</p>
          <p className="text-xs text-zinc-500">{user?.roles[0]}</p>
        </div>
        <div className="flex h-9 w-9 items-center justify-center rounded-full bg-brand-100 text-sm font-semibold text-brand-700 dark:bg-brand-900 dark:text-brand-300">
          {user?.name?.charAt(0)}
        </div>
        <button onClick={handleLogout} className="rounded-lg p-2 text-zinc-500 hover:bg-zinc-100 hover:text-red-600 dark:hover:bg-zinc-800">
          <LogOut className="h-5 w-5" />
        </button>
      </div>
    </header>
  )
}
