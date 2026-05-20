import { motion } from 'framer-motion'
import { LucideIcon } from 'lucide-react'
import clsx from 'clsx'

interface StatCardProps {
  title: string
  value: string | number
  icon: LucideIcon
  trend?: string
  trendUp?: boolean
  color?: 'brand' | 'green' | 'amber' | 'red'
}

const colors = {
  brand: 'bg-brand-50 text-brand-600 dark:bg-brand-950 dark:text-brand-400',
  green: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400',
  amber: 'bg-amber-50 text-amber-600 dark:bg-amber-950 dark:text-amber-400',
  red: 'bg-red-50 text-red-600 dark:bg-red-950 dark:text-red-400',
}

export default function StatCard({ title, value, icon: Icon, trend, trendUp, color = 'brand' }: StatCardProps) {
  return (
    <motion.div
      initial={{ opacity: 0, y: 12 }}
      animate={{ opacity: 1, y: 0 }}
      className="card group hover:shadow-card-hover"
    >
      <div className="flex items-start justify-between">
        <div>
          <p className="text-sm font-medium text-zinc-500">{title}</p>
          <p className="mt-2 text-2xl font-bold tracking-tight">{value}</p>
          {trend && (
            <p className={clsx('mt-1 text-xs font-medium', trendUp ? 'text-emerald-600' : 'text-red-500')}>
              {trend}
            </p>
          )}
        </div>
        <div className={clsx('rounded-xl p-3', colors[color])}>
          <Icon className="h-5 w-5" />
        </div>
      </div>
    </motion.div>
  )
}
