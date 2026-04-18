import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * CSRF Protection via meta tag — bukan dari cookie.
 *
 * Alasan:
 * - XSRF-TOKEN cookie sekarang HttpOnly=true
 * - JS tidak bisa membaca HttpOnly cookie
 * - Token diambil dari <meta name="csrf-token"> di HTML head
 * - Dikirim via header X-CSRF-TOKEN di setiap request Axios
 */
function getCsrfToken() {
    return document.head.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

// Set default header untuk semua request Axios
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = getCsrfToken();

/**
 * Interceptor: tangani 419 (token expired) secara otomatis.
 */
axios.interceptors.response.use(
    response => response,
    async error => {
        if (error.response?.status === 419) {
            try {
                // Ambil halaman saat ini untuk mendapat token segar
                const refreshResponse = await axios.get(window.location.href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                // Parse token dari response HTML
                const parser   = new DOMParser();
                const doc      = parser.parseFromString(refreshResponse.data, 'text/html');
                const newToken = doc.head.querySelector('meta[name="csrf-token"]')?.content;

                if (newToken) {
                    // Update meta tag di halaman saat ini
                    const metaTag = document.head.querySelector('meta[name="csrf-token"]');
                    if (metaTag) metaTag.setAttribute('content', newToken);

                    // Update Axios default header
                    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = newToken;

                    // Retry request original dengan token baru
                    error.config.headers['X-CSRF-TOKEN'] = newToken;
                    return axios.request(error.config);
                }
            } catch (_retryError) {
                window.location.href = '/login';
            }
        }
        return Promise.reject(error);
    }
);
