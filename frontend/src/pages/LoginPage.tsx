import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { motion } from 'framer-motion'
import { Boxes, Loader2 } from 'lucide-react'
import { useForm } from 'react-hook-form'
import toast from 'react-hot-toast'
import api, { ApiResponse } from '@/lib/api'
import { useAuthStore, User } from '@/stores/authStore'

interface LoginForm {
  email: string
  password: string
}

export default function LoginPage() {
  const navigate = useNavigate()
  const setAuth = useAuthStore((s) => s.setAuth)
  const [loading, setLoading] = useState(false)
  const { register, handleSubmit, formState: { errors } } = useForm<LoginForm>({
    defaultValues: { email: 'admin@erp.local', password: 'password' },
  })

  const onSubmit = async (data: LoginForm) => {
    setLoading(true)
    try {
      const res = await api.post<ApiResponse<{ access_token: string; user: User }>>('/auth/login', data)
      setAuth(res.data.data.access_token, res.data.data.user)
      toast.success('Bienvenido')
      navigate('/dashboard')
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="flex min-h-screen">
      <div className="hidden flex-1 flex-col justify-between bg-gradient-to-br from-brand-600 to-brand-900 p-12 text-white lg:flex">
        <div className="flex items-center gap-3">
          <Boxes className="h-8 w-8" />
          <span className="text-2xl font-bold">Modern ERP</span>
        </div>
        <motion.div initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }}>
          <h2 className="text-4xl font-bold leading-tight">
            Gestión empresarial<br />para PYMEs modernas
          </h2>
          <p className="mt-4 max-w-md text-brand-100">
            Inventario, ventas, compras y reportes en una plataforma unificada con actualizaciones en tiempo real.
          </p>
        </motion.div>
        <p className="text-sm text-brand-200">© 2026 Modern ERP</p>
      </div>
      <div className="flex flex-1 items-center justify-center p-8">
        <motion.form
          initial={{ opacity: 0, scale: 0.98 }}
          animate={{ opacity: 1, scale: 1 }}
          onSubmit={handleSubmit(onSubmit)}
          className="w-full max-w-md space-y-6"
        >
          <div className="lg:hidden flex items-center gap-2 mb-8">
            <Boxes className="h-6 w-6 text-brand-600" />
            <span className="text-xl font-bold">Modern ERP</span>
          </div>
          <div>
            <h1 className="text-2xl font-bold">Iniciar sesión</h1>
            <p className="mt-1 text-sm text-zinc-500">Accede a tu panel de control</p>
          </div>
          <div>
            <label className="mb-1 block text-sm font-medium">Email</label>
            <input {...register('email', { required: 'Requerido' })} type="email" className="input-field" />
            {errors.email && <p className="mt-1 text-xs text-red-500">{errors.email.message}</p>}
          </div>
          <div>
            <label className="mb-1 block text-sm font-medium">Contraseña</label>
            <input {...register('password', { required: 'Requerido' })} type="password" className="input-field" />
          </div>
          <button type="submit" disabled={loading} className="btn-primary w-full py-3">
            {loading ? <Loader2 className="h-4 w-4 animate-spin" /> : 'Entrar'}
          </button>
          <p className="text-center text-xs text-zinc-500">
            Demo: admin@erp.local / password
          </p>
        </motion.form>
      </div>
    </div>
  )
}
