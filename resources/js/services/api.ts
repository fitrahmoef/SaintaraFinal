import axios, { AxiosInstance, AxiosError } from 'axios';

// Create axios instance
const api: AxiosInstance = axios.create({
  baseURL: '/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  withCredentials: true, // Important for Laravel Sanctum
});

// Request interceptor
api.interceptors.request.use(
  (config) => {
    // Get CSRF token from meta tag
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) {
      config.headers['X-CSRF-TOKEN'] = token;
    }

    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor
api.interceptors.response.use(
  (response) => {
    return response;
  },
  (error: AxiosError) => {
    // Handle errors globally
    if (error.response) {
      switch (error.response.status) {
        case 401:
          // Unauthorized - redirect to login
          console.error('Unauthorized. Please login again.');
          if (!window.location.pathname.includes('/login')) {
            window.location.href = '/login';
          }
          break;
        case 403:
          // Forbidden
          console.error('Access forbidden.');
          break;
        case 404:
          // Not found
          console.error('Resource not found.');
          break;
        case 422:
          // Validation error
          console.error('Validation error:', error.response.data);
          break;
        case 500:
          // Server error
          console.error('Server error. Please try again later.');
          break;
        default:
          console.error('An error occurred:', error.message);
      }
    } else if (error.request) {
      // Request made but no response
      console.error('No response from server. Please check your connection.');
    } else {
      // Something else happened
      console.error('Error:', error.message);
    }

    return Promise.reject(error);
  }
);

// Token API
export const tokenApi = {
  // Get token balance and transactions
  getTokens: () => api.get('/personal/tokens'),

  // Get available packages
  getPackages: () => api.get('/personal/tokens/packages'),

  // Get token balance only
  getBalance: () => api.get('/personal/tokens/balance'),

  // Purchase tokens
  purchase: (data: { package_id: number; payment_gateway_id?: number }) =>
    api.post('/personal/tokens/purchase', data),
};

// Test API
export const testApi = {
  // Get available tests
  getTests: () => api.get('/personal/tests'),

  // Get test by ID
  getTest: (id: number) => api.get(`/personal/tests/${id}`),

  // Submit test
  submit: (data: any) => api.post('/personal/tests/submit', data),

  // Get test results
  getResults: () => api.get('/personal/results'),

  // Get result detail
  getResultDetail: (id: number) => api.get(`/personal/results/${id}`),
};

// Payment API
export const paymentApi = {
  // Get transaction status
  getStatus: (orderId: string) =>
    api.get('/payment/status', { params: { order_id: orderId } }),

  // Cancel transaction
  cancel: (orderId: string) => api.post('/payment/cancel', { order_id: orderId }),
};

// Certificate API
export const certificateApi = {
  // Download certificate
  download: (id: number) => api.get(`/personal/certificates/${id}/download`, {
    responseType: 'blob',
  }),

  // View certificate
  view: (id: number) => api.get(`/personal/certificates/${id}/view`),

  // Download test result
  downloadResult: (id: number) => api.get(`/personal/results/${id}/download`, {
    responseType: 'blob',
  }),
};

// Admin APIs (if needed)
export const adminApi = {
  // User management
  getUsers: () => api.get('/admin/users'),
  getUserStats: () => api.get('/admin/users/stats'),
  getUser: (id: number) => api.get(`/admin/users/${id}`),
  createUser: (data: any) => api.post('/admin/users', data),
  updateUser: (id: number, data: any) => api.put(`/admin/users/${id}`, data),
  deleteUser: (id: number) => api.delete(`/admin/users/${id}`),

  // Dashboard stats
  getDashboardStats: () => api.get('/admin/dashboard/stats'),
};

export default api;
