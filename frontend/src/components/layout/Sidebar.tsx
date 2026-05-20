import { NavLink } from 'react-router-dom'
import { motion } from 'framer-motion'
import {
  LayoutDashboard, Package, ShoppingCart, Truck,
  Users, Building2, History, FileBarChart,
  Shield, Activity, Boxes,
} from 'lucide-react'
import clsx from 'clsx'
import { useAuthStore } from '@/stores/authStore'

const navItems = [
  { to: '/dashboard', icon: LayoutDashboard, label: 'Dashboard', permission: 'dashboard.view' },
  { to: '/products', icon: Package, label: 'Inventario', permission: 'products.view' },
  { to: '/sales', icon: ShoppingCart, label: 'Ventas', permission: 'sales.view' },
  { to: '/purchases', icon: Truck, label: 'Compras', permission: 'purchases.view' },
  { to: '/customers', icon: Users, label: 'Clientes', permission: 'customers.view' },
  { to: '/suppliers', icon: Building2, label: 'Proveedores', permission: 'suppliers.view' },
  { to: '/kardex', icon: History, label: 'Kardex', permission: 'inventory.view' },
  { to: '/reports', icon: FileBarChart, label: 'Reportes', permission: 'reports.view' },
  { to: '/activity', icon: Activity, label: 'Actividad', permission: 'activity.view' },
  { to: '/roles', icon: Shield, label: 'Roles', permission: 'roles.manage' },
]

export default function Sidebar() {
  const hasPermission = useAuthStore((s) => s.hasPermission)

  return (
    <aside className="fixed inset-y-0 left-0 z-40 hidden w-64 flex-col border-r border-zinc-200/80 bg-white dark:border-zinc-800 dark:bg-zinc-900 lg:flex">
      <div className="flex h-16 items-center gap-2 border-b border-zinc-200/80 px-6 dark:border-zinc-800">
        <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-600">
          <Boxes className="h-4 w-4 text-white" />
        </div>
        <span className="text-lg font-semibold tracking-tight">Modern ERP</span>
      </div>
      <nav className="flex-1 space-y-1 p-4">
        {navItems.filter((item) => hasPermission(item.permission)).map((item) => (
          <NavLink
            key={item.to}
            to={item.to}
            className={({ isActive }) =>
              clsx(
                'group relative flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors',
                isActive
                  ? 'text-brand-600 dark:text-brand-400'
                  : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100'
              )
            }
          >
            {({ isActive }) => (
              <>
                {isActive && (
                  <motion.div
                    layoutId="sidebar-active"
                    className="absolute inset-0 rounded-lg bg-brand-50 dark:bg-brand-950/50"
                    transition={{ type: 'spring', bounce: 0.2, duration: 0.4 }}
                  />
                )}
                <item.icon className="relative h-4 w-4" />
                <span className="relative">{item.label}</span>
              </>
            )}
          </NavLink>
        ))}
      </nav>
      <div className="border-t border-zinc-200/80 p-4 dark:border-zinc-800">
        <p className="text-xs text-zinc-500">v1.0.0 · PYME Edition</p>
      </div>
    </aside>
  )
}
