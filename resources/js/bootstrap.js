import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Ambil CSRF token dari meta tag (bukan cookie).
 * XSRF-TOKEN cookie sekarang HttpOnly=true sehingga
 * tidak bisa dibaca JavaScript.
 */
function getCsrfToken() {
    return document.head.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

window.axios.defaults.headers.common['X-CSRF-TOKEN'] = getCsrfToken();

/**
 * Auto-refresh token jika 419 (expired).
 */
axios.interceptors.response.use(
    response => response,
    async error => {
        if (error.response?.status === 419) {
            try {
                const res = await axios.get(window.location.href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const parser   = new DOMParser();
                const doc      = parser.parseFromString(res.data, 'text/html');
                const newToken = doc.head
                    .querySelector('meta[name="csrf-token"]')?.content;

                if (newToken) {
                    const metaTag = document.head
                        .querySelector('meta[name="csrf-token"]');
                    if (metaTag) metaTag.setAttribute('content', newToken);
                    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = newToken;
                    error.config.headers['X-CSRF-TOKEN'] = newToken;
                    return axios.request(error.config);
                }
            } catch (_) {
                window.location.href = '/login';
            }
        }
        return Promise.reject(error);
    }
);
