import { useQuery } from '@tanstack/react-query'
import api, { ApiResponse } from '@/lib/api'
import PageHeader from '@/components/ui/PageHeader'

interface Role {
  id: number
  name: string
  description?: string
  permissions: { name: string; module?: string }[]
}

export default function RolesPage() {
  const { data, isLoading } = useQuery({
    queryKey: ['roles'],
    queryFn: async () => {
      const res = await api.get<ApiResponse<Role[]>>('/roles')
      return res.data.data
    },
  })

  return (
    <div>
      <PageHeader title="Roles y permisos" description="Control de acceso basado en roles" />
      {isLoading ? (
        <p className="text-zinc-500">Cargando...</p>
      ) : (
        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          {data?.map((role) => (
            <div key={role.id} className="card">
              <div className="flex items-center justify-between">
                <h3 className="font-semibold capitalize">{role.name}</h3>
                <span className="rounded-full bg-brand-100 px-2 py-0.5 text-xs text-brand-700 dark:bg-brand-900 dark:text-brand-300">
                  {role.permissions.length} permisos
                </span>
              </div>
              {role.description && <p className="mt-1 text-sm text-zinc-500">{role.description}</p>}
              <div className="mt-4 flex flex-wrap gap-1">
                {role.permissions.slice(0, 8).map((p) => (
                  <span key={p.name} className="rounded bg-zinc-100 px-2 py-0.5 text-xs dark:bg-zinc-800">
                    {p.name}
                  </span>
                ))}
                {role.permissions.length > 8 && (
                  <span className="text-xs text-zinc-500">+{role.permissions.length - 8} más</span>
                )}
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  )
}
