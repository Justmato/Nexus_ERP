import { useState } from 'react'
import { useQuery } from '@tanstack/react-query'
import { FileSpreadsheet, FileText } from 'lucide-react'
import api, { ApiResponse } from '@/lib/api'
import PageHeader from '@/components/ui/PageHeader'

export default function ReportsPage() {
  const [dateFrom, setDateFrom] = useState(new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0])
  const [dateTo, setDateTo] = useState(new Date().toISOString().split('T')[0])

  const { data, isLoading } = useQuery({
    queryKey: ['reports-sales', dateFrom, dateTo],
    queryFn: async () => {
      const res = await api.get<ApiResponse<{ total: number; count: number }>>('/reports/sales', {
        params: { date_from: dateFrom, date_to: dateTo },
      })
      return res.data.data
    },
  })

  const baseUrl = import.meta.env.VITE_API_URL || '/api'
  const token = localStorage.getItem('erp-auth')
  const parsed = token ? JSON.parse(token) : null
  const authToken = parsed?.state?.token

  const exportUrl = (format: 'excel' | 'pdf') => {
    const params = new URLSearchParams({ date_from: dateFrom, date_to: dateTo })
    return `${baseUrl}/reports/sales/${format}?${params}`
  }

  const handleExport = (format: 'excel' | 'pdf') => {
    const link = document.createElement('a')
    link.href = exportUrl(format)
    link.setAttribute('download', '')
    fetch(exportUrl(format), {
      headers: { Authorization: `Bearer ${authToken}` },
    }).then((res) => res.blob()).then((blob) => {
      const url = URL.createObjectURL(blob)
      link.href = url
      link.download = `ventas_${dateFrom}_${dateTo}.${format === 'excel' ? 'xlsx' : 'pdf'}`
      link.click()
    })
  }

  const formatMoney = (n: number) =>
    new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(n)

  return (
    <div>
      <PageHeader title="Reportes" description="Exportación PDF y Excel" />
      <div className="card max-w-2xl">
        <h3 className="font-semibold">Reporte de ventas</h3>
        <div className="mt-4 flex flex-wrap gap-4">
          <div>
            <label className="mb-1 block text-sm text-zinc-500">Desde</label>
            <input type="date" value={dateFrom} onChange={(e) => setDateFrom(e.target.value)} className="input-field" />
          </div>
          <div>
            <label className="mb-1 block text-sm text-zinc-500">Hasta</label>
            <input type="date" value={dateTo} onChange={(e) => setDateTo(e.target.value)} className="input-field" />
          </div>
        </div>
        {isLoading ? (
          <p className="mt-4 text-zinc-500">Cargando...</p>
        ) : (
          <div className="mt-6 grid grid-cols-2 gap-4">
            <div className="rounded-lg bg-zinc-50 p-4 dark:bg-zinc-800">
              <p className="text-sm text-zinc-500">Total ventas</p>
              <p className="text-2xl font-bold">{formatMoney(data?.total ?? 0)}</p>
            </div>
            <div className="rounded-lg bg-zinc-50 p-4 dark:bg-zinc-800">
              <p className="text-sm text-zinc-500">Transacciones</p>
              <p className="text-2xl font-bold">{data?.count ?? 0}</p>
            </div>
          </div>
        )}
        <div className="mt-6 flex gap-3">
          <button onClick={() => handleExport('excel')} className="btn-secondary">
            <FileSpreadsheet className="h-4 w-4" /> Excel
          </button>
          <button onClick={() => handleExport('pdf')} className="btn-secondary">
            <FileText className="h-4 w-4" /> PDF
          </button>
        </div>
      </div>
    </div>
  )
}
