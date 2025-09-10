import axios from 'axios';
window.axios = axios;

// Header común
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// CSRF token
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token no encontrado: verifica que <meta name="csrf-token"> esté en tu layout.');
}


