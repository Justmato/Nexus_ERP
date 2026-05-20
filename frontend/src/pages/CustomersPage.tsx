import { useState } from 'react'
import { useQuery } from '@tanstack/react-query'
import api, { ApiResponse, Paginated } from '@/lib/api'
import PageHeader from '@/components/ui/PageHeader'
import DataTable from '@/components/ui/DataTable'

interface Customer {
  id: number
  code: string
  name: string
  email?: string
  phone?: string
  city?: string
  is_active: boolean
}

export default function CustomersPage() {
  const [page, setPage] = useState(1)
  const [search, setSearch] = useState('')

  const { data, isLoading } = useQuery({
    queryKey: ['customers', page, search],
    queryFn: async () => {
      const res = await api.get<ApiResponse<Paginated<Customer>>>('/customers', {
        params: { page, search },
      })
      return res.data.data
    },
  })

  return (
    <div>
      <PageHeader
        title="Clientes"
        description="Directorio de clientes"
        action={
          <input
            value={search}
            onChange={(e) => { setSearch(e.target.value); setPage(1) }}
            placeholder="Buscar..."
            className="input-field w-56"
          />
        }
      />
      <DataTable
        loading={isLoading}
        data={data?.data ?? []}
        columns={[
          { key: 'code', label: 'Código' },
          { key: 'name', label: 'Nombre' },
          { key: 'email', label: 'Email' },
          { key: 'phone', label: 'Teléfono' },
          { key: 'city', label: 'Ciudad' },
        ]}
        pagination={data ? { page: data.current_page, lastPage: data.last_page, total: data.total, onPageChange: setPage } : undefined}
      />
    </div>
  )
}
