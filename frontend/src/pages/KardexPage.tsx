import { useState } from 'react'
import { useQuery } from '@tanstack/react-query'
import api, { ApiResponse, Paginated } from '@/lib/api'
import PageHeader from '@/components/ui/PageHeader'
import DataTable from '@/components/ui/DataTable'

interface Movement {
  id: number
  folio?: string
  type: string
  quantity: number
  balance_before: number
  balance_after: number
  movement_date: string
  product?: { sku: string; name: string }
}

const typeLabels: Record<string, string> = {
  in: 'Entrada',
  out: 'Salida',
  adjustment: 'Ajuste',
  transfer: 'Transferencia',
}

export default function KardexPage() {
  const [page, setPage] = useState(1)
  const [type, setType] = useState('')

  const { data, isLoading } = useQuery({
    queryKey: ['kardex', page, type],
    queryFn: async () => {
      const res = await api.get<ApiResponse<Paginated<Movement>>>('/kardex', {
        params: { page, type: type || undefined, per_page: 20 },
      })
      return res.data.data
    },
  })

  return (
    <div>
      <PageHeader title="Kardex" description="Historial de movimientos de inventario" />
      <div className="mb-4">
        <select value={type} onChange={(e) => { setType(e.target.value); setPage(1) }} className="input-field w-48">
          <option value="">Todos los tipos</option>
          <option value="in">Entradas</option>
          <option value="out">Salidas</option>
          <option value="adjustment">Ajustes</option>
        </select>
      </div>
      <DataTable
        loading={isLoading}
        data={data?.data ?? []}
        columns={[
          { key: 'movement_date', label: 'Fecha', render: (r) => new Date(r.movement_date).toLocaleString('es-MX') },
          { key: 'folio', label: 'Folio' },
          { key: 'product', label: 'Producto', render: (m) => `${m.product?.sku} - ${m.product?.name}` },
          { key: 'type', label: 'Tipo', render: (r) => typeLabels[r.type] ?? r.type },
          { key: 'quantity', label: 'Cantidad', render: (r) => {
            const q = r.quantity
            return <span className={q > 0 ? 'text-emerald-600' : 'text-red-500'}>{q > 0 ? '+' : ''}{q}</span>
          }},
          { key: 'balance_after', label: 'Saldo', render: (r) => r.balance_after },
        ]}
        pagination={data ? { page: data.current_page, lastPage: data.last_page, total: data.total, onPageChange: setPage } : undefined}
      />
    </div>
  )
}
