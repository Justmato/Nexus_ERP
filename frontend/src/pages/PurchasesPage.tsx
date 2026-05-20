import { useState } from 'react'
import { useQuery } from '@tanstack/react-query'
import api, { ApiResponse, Paginated } from '@/lib/api'
import PageHeader from '@/components/ui/PageHeader'
import DataTable from '@/components/ui/DataTable'

interface Purchase {
  id: number
  folio: string
  purchase_date: string
  supplier?: { name: string }
  total: number
  status: string
}

export default function PurchasesPage() {
  const [page, setPage] = useState(1)

  const { data, isLoading } = useQuery({
    queryKey: ['purchases', page],
    queryFn: async () => {
      const res = await api.get<ApiResponse<Paginated<Purchase>>>('/purchases', { params: { page } })
      return res.data.data
    },
  })

  const formatMoney = (n: number) =>
    new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(n)

  return (
    <div>
      <PageHeader title="Compras" description="Órdenes de compra a proveedores" />
      <DataTable
        loading={isLoading}
        data={data?.data ?? []}
        columns={[
          { key: 'folio', label: 'Folio' },
          { key: 'purchase_date', label: 'Fecha' },
          { key: 'supplier', label: 'Proveedor', render: (r) => r.supplier?.name ?? '-' },
          { key: 'total', label: 'Total', render: (r) => formatMoney(r.total) },
          { key: 'status', label: 'Estado', render: (r) => <span className="capitalize">{r.status}</span> },
        ]}
        pagination={data ? { page: data.current_page, lastPage: data.last_page, total: data.total, onPageChange: setPage } : undefined}
      />
    </div>
  )
}
