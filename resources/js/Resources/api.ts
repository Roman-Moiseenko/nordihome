import axios from 'axios'
import { ElMessage } from 'element-plus'

type HttpMethod = 'get' | 'post' | 'put' | 'delete'

interface ApiOptions {
    /** Показывать сообщение об успехе */
    showSuccess?: boolean
    /** Показывать сообщение об ошибке */
    showError?: boolean
    /** Кастомное сообщение об успехе (переопределяет message из ответа) */
    successMessage?: string
}

interface ApiResponse<T = any> {
    data: T
    message?: string
}

/**
 * Универсальный API-клиент для работы с бэкендом.
 * Автоматически обрабатывает flash-сообщения через ElMessage.
 *
 * Ответ от сервера ожидается в формате:
 * { message?: string, ...data }
 *
 * @example
 * import api from '@Res/api'
 *
 * // POST — покажет "Сохранено" или message из ответа
 * await api.post(route('admin.catalog.product.categories', {product: id}), { categories: [1,2,3] })
 *
 * // GET — без сообщения по умолчанию
 * const data = await api.get(route('admin.catalog.room.tree'))
 *
 * // PUT — с кастомным сообщением
 * await api.put(route('admin.catalog.product.update', {product: id}), data, {
 *     successMessage: 'Обновлено!'
 * })
 *
 * // DELETE — без показа успеха
 * await api.delete(route('admin.catalog.product.destroy', {product: id}), null, {
 *     showSuccess: false
 * })
 */
function request<T = any>(
    method: HttpMethod,
    url: string,
    data?: any,
    options: ApiOptions = {},
): Promise<T> {
    const {
        showSuccess = true,
        showError = true,
        successMessage,
    } = options

    const axiosPromise = method === 'get'
        ? axios.get<T>(url, { params: data })
        : axios[method]<T>(url, data)

    return axiosPromise
        .then(response => {
        const responseData = response.data as any
        const message = responseData?.message || successMessage

        if (showSuccess) {
            // @ts-ignore
            ElMessage({
                message: message || 'Сохранено',
                type: 'success',
                plain: true,
                showClose: true,
                duration: 3000,
                center: true,
            })
        }

        return response.data as T
        })
        .catch(error => {
        if (showError) {
            const message = error?.response?.data?.message
                || error?.response?.data?.error
                || 'Произошла ошибка'
            // @ts-ignore
            ElMessage({
                message: message,
                type: 'error',
                plain: true,
                showClose: true,
                duration: 7000,
                center: true,
            })
        }

        throw error
        })
    }
const api = {
    get<T = any>(url: string, params?: any, options?: ApiOptions): Promise<T> {
        return request<T>('get', url, params, options)
    },
    post<T = any>(url: string, data?: any, options?: ApiOptions): Promise<T> {
        return request<T>('post', url, data, options)
    },
    put<T = any>(url: string, data?: any, options?: ApiOptions): Promise<T> {
        return request<T>('put', url, data, options)
    },
    delete<T = any>(url: string, data?: any, options?: ApiOptions): Promise<T> {
        return request<T>('delete', url, data, options)
    },
}

export default api

/**
 * Если нужно отдельно импортировать типы
 */
export type { ApiOptions, ApiResponse }
