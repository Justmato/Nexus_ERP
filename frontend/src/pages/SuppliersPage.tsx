import { useState } from 'react'
import { useQuery } from '@tanstack/react-query'
import api, { ApiResponse, Paginated } from '@/lib/api'
import PageHeader from '@/components/ui/PageHeader'
import DataTable from '@/components/ui/DataTable'

interface Supplier {
  id: number
  code: string
  name: string
  email?: string
  contact_name?: string
  payment_terms: number
}

export default function SuppliersPage() {
  const [page, setPage] = useState(1)

  const { data, isLoading } = useQuery({
    queryKey: ['suppliers', page],
    queryFn: async () => {
      const res = await api.get<ApiResponse<Paginated<Supplier>>>('/suppliers', { params: { page } })
      return res.data.data
    },
  })

  return (
    <div>
      <PageHeader title="Proveedores" description="Directorio de proveedores" />
      <DataTable
        loading={isLoading}
        data={data?.data ?? []}
        columns={[
          { key: 'code', label: 'Código' },
          { key: 'name', label: 'Nombre' },
          { key: 'contact_name', label: 'Contacto' },
          { key: 'email', label: 'Email' },
          { key: 'payment_terms', label: 'Plazo (días)' },
        ]}
        pagination={data ? { page: data.current_page, lastPage: data.last_page, total: data.total, onPageChange: setPage } : undefined}
      />
    </div>
  )
}
