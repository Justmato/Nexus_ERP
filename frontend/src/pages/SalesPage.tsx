import { useState } from 'react'
import { useQuery } from '@tanstack/react-query'
import api, { ApiResponse, Paginated } from '@/lib/api'
import PageHeader from '@/components/ui/PageHeader'
import DataTable from '@/components/ui/DataTable'

interface Sale {
  id: number
  folio: string
  sale_date: string
  customer?: { name: string }
  total: number
  status: string
  payment_method: string
}

const statusColors: Record<string, string> = {
  draft: 'bg-zinc-100 text-zinc-600',
  confirmed: 'bg-emerald-100 text-emerald-700',
  delivered: 'bg-blue-100 text-blue-700',
  cancelled: 'bg-red-100 text-red-600',
}

export default function SalesPage() {
  const [page, setPage] = useState(1)
  const [status, setStatus] = useState('')

  const { data, isLoading } = useQuery({
    queryKey: ['sales', page, status],
    queryFn: async () => {
      const res = await api.get<ApiResponse<Paginated<Sale>>>('/sales', {
        params: { page, status: status || undefined },
      })
      return res.data.data
    },
  })

  const formatMoney = (n: number) =>
    new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(n)

  return (
    <div>
      <PageHeader title="Ventas" description="Órdenes de venta y facturación" />
      <div className="mb-4">
        <select value={status} onChange={(e) => { setStatus(e.target.value); setPage(1) }} className="input-field w-48">
          <option value="">Todos los estados</option>
          <option value="draft">Borrador</option>
          <option value="confirmed">Confirmado</option>
          <option value="delivered">Entregado</option>
          <option value="cancelled">Cancelado</option>
        </select>
      </div>
      <DataTable
        loading={isLoading}
        data={data?.data ?? []}
        columns={[
          { key: 'folio', label: 'Folio' },
          { key: 'sale_date', label: 'Fecha' },
          { key: 'customer', label: 'Cliente', render: (r) => r.customer?.name ?? 'Mostrador' },
          { key: 'total', label: 'Total', render: (r) => formatMoney(r.total) },
          { key: 'payment_method', label: 'Pago' },
          {
            key: 'status', label: 'Estado',
            render: (r) => (
              <span className={`rounded-full px-2 py-0.5 text-xs capitalize ${statusColors[r.status] ?? ''}`}>
                {r.status}
              </span>
            ),
          },
        ]}
        pagination={data ? { page: data.current_page, lastPage: data.last_page, total: data.total, onPageChange: setPage } : undefined}
      />
    </div>
  )
}
