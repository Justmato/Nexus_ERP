import { useQuery } from '@tanstack/react-query'
import { DollarSign, Package, Users, AlertTriangle, TrendingUp, ShoppingBag } from 'lucide-react'
import { AreaChart, Area, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, BarChart, Bar } from 'recharts'
import api, { ApiResponse } from '@/lib/api'
import StatCard from '@/components/ui/StatCard'
import PageHeader from '@/components/ui/PageHeader'

interface DashboardData {
  metrics: {
    sales_today: number
    sales_month: number
    purchases_month: number
    profit_month: number
    products_count: number
    customers_count: number
    low_stock_count: number
    pending_sales: number
  }
  sales_chart: { labels: string[]; data: number[] }
  top_products: { name: string; sku: string; qty: number; revenue: number }[]
}

const formatMoney = (n: number) =>
  new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(n)

export default function DashboardPage() {
  const { data, isLoading } = useQuery({
    queryKey: ['dashboard'],
    queryFn: async () => {
      const res = await api.get<ApiResponse<DashboardData>>('/dashboard')
      return res.data.data
    },
  })

  const chartData = data?.sales_chart.labels.map((label, i) => ({
    name: label,
    ventas: data.sales_chart.data[i],
  })) ?? []

  const m = data?.metrics

  return (
    <div className="animate-fade-in">
      <PageHeader title="Dashboard" description="Resumen ejecutivo de tu negocio" />
      {isLoading ? (
        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
          {[...Array(4)].map((_, i) => (
            <div key={i} className="card h-32 animate-pulse bg-zinc-100 dark:bg-zinc-800" />
          ))}
        </div>
      ) : (
        <>
          <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <StatCard title="Ventas hoy" value={formatMoney(m?.sales_today ?? 0)} icon={DollarSign} color="brand" />
            <StatCard title="Ventas del mes" value={formatMoney(m?.sales_month ?? 0)} icon={TrendingUp} color="green" />
            <StatCard title="Utilidad estimada" value={formatMoney(m?.profit_month ?? 0)} icon={ShoppingBag} color="brand" />
            <StatCard
              title="Stock bajo"
              value={m?.low_stock_count ?? 0}
              icon={AlertTriangle}
              color="amber"
              trend={m?.low_stock_count ? 'Requiere atención' : 'Todo en orden'}
            />
          </div>
          <div className="mt-6 grid gap-6 lg:grid-cols-3">
            <div className="card lg:col-span-2">
              <h3 className="mb-4 font-semibold">Ventas últimos 30 días</h3>
              <ResponsiveContainer width="100%" height={280}>
                <AreaChart data={chartData}>
                  <defs>
                    <linearGradient id="colorVentas" x1="0" y1="0" x2="0" y2="1">
                      <stop offset="5%" stopColor="#6366f1" stopOpacity={0.3} />
                      <stop offset="95%" stopColor="#6366f1" stopOpacity={0} />
                    </linearGradient>
                  </defs>
                  <CartesianGrid strokeDasharray="3 3" className="stroke-zinc-200 dark:stroke-zinc-700" />
                  <XAxis dataKey="name" tick={{ fontSize: 11 }} />
                  <YAxis tick={{ fontSize: 11 }} />
                  <Tooltip formatter={(v: number) => formatMoney(v)} />
                  <Area type="monotone" dataKey="ventas" stroke="#6366f1" fill="url(#colorVentas)" strokeWidth={2} />
                </AreaChart>
              </ResponsiveContainer>
            </div>
            <div className="card">
              <h3 className="mb-4 font-semibold">Top productos</h3>
              <ResponsiveContainer width="100%" height={280}>
                <BarChart data={data?.top_products ?? []} layout="vertical">
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis type="number" tick={{ fontSize: 11 }} />
                  <YAxis dataKey="name" type="category" width={80} tick={{ fontSize: 10 }} />
                  <Tooltip />
                  <Bar dataKey="revenue" fill="#6366f1" radius={[0, 4, 4, 0]} />
                </BarChart>
              </ResponsiveContainer>
            </div>
          </div>
          <div className="mt-6 grid gap-4 sm:grid-cols-3">
            <StatCard title="Productos activos" value={m?.products_count ?? 0} icon={Package} />
            <StatCard title="Clientes" value={m?.customers_count ?? 0} icon={Users} />
            <StatCard title="Ventas pendientes" value={m?.pending_sales ?? 0} icon={ShoppingBag} color="amber" />
          </div>
        </>
      )}
    </div>
  )
}
