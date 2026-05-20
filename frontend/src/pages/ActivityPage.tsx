import { useState } from 'react'
import { useQuery } from '@tanstack/react-query'
import api, { ApiResponse, Paginated } from '@/lib/api'
import PageHeader from '@/components/ui/PageHeader'
import DataTable from '@/components/ui/DataTable'

interface ActivityLog {
  id: number
  action: string
  module: string
  created_at: string
  user?: { name: string }
  ip_address?: string
}

export default function ActivityPage() {
  const [page, setPage] = useState(1)

  const { data, isLoading } = useQuery({
    queryKey: ['activity-logs', page],
    queryFn: async () => {
      const res = await api.get<ApiResponse<Paginated<ActivityLog>>>('/activity-logs', { params: { page } })
      return res.data.data
    },
  })

  return (
    <div>
      <PageHeader title="Logs de actividad" description="Auditoría de acciones del sistema" />
      <DataTable
        loading={isLoading}
        data={data?.data ?? []}
        columns={[
          { key: 'created_at', label: 'Fecha', render: (r) => new Date(r.created_at).toLocaleString('es-MX') },
          { key: 'user', label: 'Usuario', render: (r) => r.user?.name ?? 'Sistema' },
          { key: 'module', label: 'Módulo' },
          { key: 'action', label: 'Acción' },
          { key: 'ip_address', label: 'IP' },
        ]}
        pagination={data ? { page: data.current_page, lastPage: data.last_page, total: data.total, onPageChange: setPage } : undefined}
      />
    </div>
  )
}
