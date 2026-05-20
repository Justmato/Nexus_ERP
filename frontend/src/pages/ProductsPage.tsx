import { useState } from 'react'
import { useQuery } from '@tanstack/react-query'
import { Plus, AlertTriangle } from 'lucide-react'
import api, { ApiResponse, Paginated } from '@/lib/api'
import PageHeader from '@/components/ui/PageHeader'
import DataTable from '@/components/ui/DataTable'

interface Product {
  id: number
  sku: string
  name: string
  stock: number
  min_stock: number
  sale_price: number
  category?: { name: string }
  is_active: boolean
}

export default function ProductsPage() {
  const [page, setPage] = useState(1)
  const [search, setSearch] = useState('')
  const [lowStock, setLowStock] = useState(false)

  const { data, isLoading } = useQuery({
    queryKey: ['products', page, search, lowStock],
    queryFn: async () => {
      const res = await api.get<ApiResponse<Paginated<Product>>>('/products', {
        params: { page, search, low_stock: lowStock || undefined, per_page: 15 },
      })
      return res.data.data
    },
  })

  const formatMoney = (n: number) =>
    new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(n)

  return (
    <div>
      <PageHeader
        title="Inventario"
        description="Gestión de productos y existencias"
        action={
          <div className="flex gap-2">
            <input
              value={search}
              onChange={(e) => { setSearch(e.target.value); setPage(1) }}
              placeholder="Buscar SKU o nombre..."
              className="input-field w-56"
            />
            <button
              onClick={() => setLowStock(!lowStock)}
              className={`btn-secondary ${lowStock ? '!border-amber-500 !text-amber-600' : ''}`}
            >
              <AlertTriangle className="h-4 w-4" /> Stock bajo
            </button>
            <button className="btn-primary"><Plus className="h-4 w-4" /> Nuevo</button>
          </div>
        }
      />
      <DataTable
        loading={isLoading}
        data={data?.data ?? []}
        columns={[
          { key: 'sku', label: 'SKU' },
          { key: 'name', label: 'Producto' },
          { key: 'category', label: 'Categoría', render: (r) => r.category?.name ?? '-' },
          {
            key: 'stock', label: 'Stock',
            render: (p) => {
              const low = p.stock <= p.min_stock
              return (
                <span className={low ? 'font-medium text-amber-600' : ''}>
                  {p.stock} {low && <AlertTriangle className="inline h-3 w-3" />}
                </span>
              )
            },
          },
          { key: 'sale_price', label: 'Precio', render: (r) => formatMoney(r.sale_price) },
          {
            key: 'is_active', label: 'Estado',
            render: (r) => (
              <span className={`rounded-full px-2 py-0.5 text-xs ${r.is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300' : 'bg-zinc-100 text-zinc-500'}`}>
                {r.is_active ? 'Activo' : 'Inactivo'}
              </span>
            ),
          },
        ]}
        pagination={data ? { page: data.current_page, lastPage: data.last_page, total: data.total, onPageChange: setPage } : undefined}
      />
    </div>
  )
}
