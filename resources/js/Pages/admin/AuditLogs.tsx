import React, { useState, useEffect } from 'react';
import { Head } from '@inertiajs/react';
import DashboardLayout from '@/layouts/dashboardLayoutAdmin';
import axios from 'axios';
import { toast } from 'react-hot-toast';

interface ActivityLog {
    id: number;
    user_id: number;
    action: string;
    description: string;
    module: string | null;
    level: string;
    ip_address: string;
    user_agent: string;
    created_at: string;
    user?: {
        id: number;
        name: string;
        email: string;
        user_type: string;
    };
}

interface PaginatedResponse {
    data: ActivityLog[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface Stats {
    total_logs: number;
    total_users: number;
    by_level: { [key: string]: number };
    by_module: { [key: string]: number };
    by_action: { [key: string]: number };
    daily_activity: { [key: string]: number };
}

export default function AuditLogs() {
    const [logs, setLogs] = useState<ActivityLog[]>([]);
    const [stats, setStats] = useState<Stats | null>(null);
    const [loading, setLoading] = useState(true);
    const [currentPage, setCurrentPage] = useState(1);
    const [lastPage, setLastPage] = useState(1);
    const [total, setTotal] = useState(0);
    const [search, setSearch] = useState('');
    const [filters, setFilters] = useState({
        user_id: '',
        action: '',
        module: '',
        level: '',
        start_date: '',
        end_date: '',
    });
    const [actions, setActions] = useState<string[]>([]);
    const [modules, setModules] = useState<string[]>([]);
    const [showDetailModal, setShowDetailModal] = useState(false);
    const [selectedLog, setSelectedLog] = useState<ActivityLog | null>(null);

    useEffect(() => {
        fetchLogs();
        fetchStats();
        fetchActions();
        fetchModules();
    }, [currentPage, filters]);

    useEffect(() => {
        const delayDebounceFn = setTimeout(() => {
            if (currentPage === 1) {
                fetchLogs();
            } else {
                setCurrentPage(1);
            }
        }, 500);

        return () => clearTimeout(delayDebounceFn);
    }, [search]);

    const fetchLogs = async () => {
        try {
            setLoading(true);
            const response = await axios.get<PaginatedResponse>('/api/admin/audit-logs', {
                params: {
                    page: currentPage,
                    search,
                    ...filters,
                },
            });
            setLogs(response.data.data);
            setLastPage(response.data.last_page);
            setTotal(response.data.total);
        } catch (error: any) {
            toast.error(error.response?.data?.message || 'Gagal memuat audit logs');
        } finally {
            setLoading(false);
        }
    };

    const fetchStats = async () => {
        try {
            const response = await axios.get<Stats>('/api/admin/audit-logs/stats', {
                params: {
                    start_date: filters.start_date,
                    end_date: filters.end_date,
                },
            });
            setStats(response.data);
        } catch (error) {
            console.error('Failed to fetch stats:', error);
        }
    };

    const fetchActions = async () => {
        try {
            const response = await axios.get<string[]>('/api/admin/audit-logs/actions');
            setActions(response.data);
        } catch (error) {
            console.error('Failed to fetch actions:', error);
        }
    };

    const fetchModules = async () => {
        try {
            const response = await axios.get<string[]>('/api/admin/audit-logs/modules');
            setModules(response.data);
        } catch (error) {
            console.error('Failed to fetch modules:', error);
        }
    };

    const handleExport = async () => {
        try {
            const response = await axios.get('/api/admin/audit-logs/export', {
                params: { ...filters, search },
                responseType: 'blob',
            });
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `audit_logs_${new Date().getTime()}.csv`);
            document.body.appendChild(link);
            link.click();
            link.remove();
            toast.success('Audit logs berhasil diexport');
        } catch (error) {
            toast.error('Gagal export audit logs');
        }
    };

    const viewDetails = (log: ActivityLog) => {
        setSelectedLog(log);
        setShowDetailModal(true);
    };

    const resetFilters = () => {
        setFilters({
            user_id: '',
            action: '',
            module: '',
            level: '',
            start_date: '',
            end_date: '',
        });
        setSearch('');
    };

    const getLevelColor = (level: string) => {
        switch (level) {
            case 'critical':
                return 'bg-red-100 text-red-800';
            case 'warning':
                return 'bg-yellow-100 text-yellow-800';
            case 'info':
                return 'bg-blue-100 text-blue-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    return (
        <DashboardLayout>
            <Head title="Audit Logs" />

            <div className="p-6">
                {/* Header */}
                <div className="mb-6">
                    <h1 className="text-3xl font-bold text-gray-800">Audit Logs</h1>
                    <p className="text-gray-600 mt-2">Monitor semua aktivitas pengguna dalam sistem</p>
                </div>

                {/* Stats Cards */}
                {stats && (
                    <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div className="bg-blue-500 text-white p-6 rounded-lg shadow">
                            <div className="text-3xl font-bold">{stats.total_logs}</div>
                            <div className="text-sm opacity-90">Total Logs</div>
                        </div>
                        <div className="bg-green-500 text-white p-6 rounded-lg shadow">
                            <div className="text-3xl font-bold">{stats.total_users}</div>
                            <div className="text-sm opacity-90">Active Users</div>
                        </div>
                        <div className="bg-red-500 text-white p-6 rounded-lg shadow">
                            <div className="text-3xl font-bold">{stats.by_level?.warning || 0}</div>
                            <div className="text-sm opacity-90">Warnings</div>
                        </div>
                        <div className="bg-purple-500 text-white p-6 rounded-lg shadow">
                            <div className="text-3xl font-bold">{Object.keys(stats.by_module || {}).length}</div>
                            <div className="text-sm opacity-90">Modules</div>
                        </div>
                    </div>
                )}

                {/* Filters */}
                <div className="bg-white rounded-lg shadow p-4 mb-6">
                    <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-4">
                        <input
                            type="text"
                            placeholder="Cari..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            className="px-4 py-2 border rounded-lg"
                        />
                        <select
                            value={filters.action}
                            onChange={(e) => setFilters({ ...filters, action: e.target.value })}
                            className="px-4 py-2 border rounded-lg"
                        >
                            <option value="">Semua Aksi</option>
                            {actions.map((action) => (
                                <option key={action} value={action}>
                                    {action}
                                </option>
                            ))}
                        </select>
                        <select
                            value={filters.module}
                            onChange={(e) => setFilters({ ...filters, module: e.target.value })}
                            className="px-4 py-2 border rounded-lg"
                        >
                            <option value="">Semua Modul</option>
                            {modules.map((module) => (
                                <option key={module} value={module}>
                                    {module}
                                </option>
                            ))}
                        </select>
                        <select
                            value={filters.level}
                            onChange={(e) => setFilters({ ...filters, level: e.target.value })}
                            className="px-4 py-2 border rounded-lg"
                        >
                            <option value="">Semua Level</option>
                            <option value="info">Info</option>
                            <option value="warning">Warning</option>
                            <option value="critical">Critical</option>
                        </select>
                        <input
                            type="date"
                            value={filters.start_date}
                            onChange={(e) => setFilters({ ...filters, start_date: e.target.value })}
                            className="px-4 py-2 border rounded-lg"
                        />
                        <input
                            type="date"
                            value={filters.end_date}
                            onChange={(e) => setFilters({ ...filters, end_date: e.target.value })}
                            className="px-4 py-2 border rounded-lg"
                        />
                    </div>
                    <div className="flex gap-2">
                        <button
                            onClick={resetFilters}
                            className="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
                        >
                            Reset Filter
                        </button>
                        <button
                            onClick={handleExport}
                            className="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
                        >
                            Export CSV
                        </button>
                    </div>
                </div>

                {/* Activity Chart */}
                {stats?.daily_activity && Object.keys(stats.daily_activity).length > 0 && (
                    <div className="bg-white rounded-lg shadow p-6 mb-6">
                        <h2 className="text-xl font-bold mb-4">Aktivitas Harian</h2>
                        <div className="h-64">
                            <SimpleBarChart data={stats.daily_activity} />
                        </div>
                    </div>
                )}

                {/* Logs Table */}
                <div className="bg-white rounded-lg shadow overflow-hidden">
                    {loading ? (
                        <div className="p-8 text-center">
                            <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                            <p className="mt-4 text-gray-600">Memuat data...</p>
                        </div>
                    ) : logs.length === 0 ? (
                        <div className="p-8 text-center text-gray-500">Tidak ada data audit logs</div>
                    ) : (
                        <>
                            <div className="overflow-x-auto">
                                <table className="w-full">
                                    <thead className="bg-gray-50 border-b">
                                        <tr>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Waktu
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                User
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Aksi
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Modul
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Deskripsi
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Level
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                IP Address
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Aksi
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody className="bg-white divide-y divide-gray-200">
                                        {logs.map((log) => (
                                            <tr key={log.id} className="hover:bg-gray-50">
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {new Date(log.created_at).toLocaleString('id-ID')}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <div className="text-sm font-medium text-gray-900">
                                                        {log.user?.name || 'Unknown'}
                                                    </div>
                                                    <div className="text-sm text-gray-500">{log.user?.email || 'N/A'}</div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <span className="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        {log.action}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {log.module || '-'}
                                                </td>
                                                <td className="px-6 py-4 text-sm text-gray-500 max-w-md truncate">
                                                    {log.description}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <span
                                                        className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getLevelColor(
                                                            log.level
                                                        )}`}
                                                    >
                                                        {log.level}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {log.ip_address}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <button
                                                        onClick={() => viewDetails(log)}
                                                        className="text-blue-600 hover:text-blue-900"
                                                    >
                                                        Detail
                                                    </button>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>

                            {/* Pagination */}
                            {lastPage > 1 && (
                                <div className="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                                    <div className="flex-1 flex justify-between sm:hidden">
                                        <button
                                            onClick={() => setCurrentPage((p) => Math.max(1, p - 1))}
                                            disabled={currentPage === 1}
                                            className="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                        >
                                            Previous
                                        </button>
                                        <button
                                            onClick={() => setCurrentPage((p) => Math.min(lastPage, p + 1))}
                                            disabled={currentPage === lastPage}
                                            className="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                        >
                                            Next
                                        </button>
                                    </div>
                                    <div className="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                        <div>
                                            <p className="text-sm text-gray-700">
                                                Showing <span className="font-medium">{(currentPage - 1) * 20 + 1}</span> to{' '}
                                                <span className="font-medium">{Math.min(currentPage * 20, total)}</span> of{' '}
                                                <span className="font-medium">{total}</span> results
                                            </p>
                                        </div>
                                        <div>
                                            <nav className="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                                {Array.from({ length: Math.min(lastPage, 10) }, (_, i) => {
                                                    let page;
                                                    if (lastPage <= 10) {
                                                        page = i + 1;
                                                    } else if (currentPage <= 5) {
                                                        page = i + 1;
                                                    } else if (currentPage >= lastPage - 4) {
                                                        page = lastPage - 9 + i;
                                                    } else {
                                                        page = currentPage - 5 + i;
                                                    }
                                                    return (
                                                        <button
                                                            key={page}
                                                            onClick={() => setCurrentPage(page)}
                                                            className={`relative inline-flex items-center px-4 py-2 border text-sm font-medium ${
                                                                page === currentPage
                                                                    ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                                                                    : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                                                            }`}
                                                        >
                                                            {page}
                                                        </button>
                                                    );
                                                })}
                                            </nav>
                                        </div>
                                    </div>
                                </div>
                            )}
                        </>
                    )}
                </div>
            </div>

            {/* Detail Modal */}
            {showDetailModal && selectedLog && (
                <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
                    <div className="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                        <div className="flex justify-between items-center p-6 border-b">
                            <h2 className="text-xl font-bold">Detail Audit Log</h2>
                            <button
                                onClick={() => setShowDetailModal(false)}
                                className="text-gray-400 hover:text-gray-600"
                            >
                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div className="p-6 space-y-4">
                            <div>
                                <label className="text-sm font-medium text-gray-600">Waktu</label>
                                <p className="text-lg">{new Date(selectedLog.created_at).toLocaleString('id-ID')}</p>
                            </div>
                            <div>
                                <label className="text-sm font-medium text-gray-600">User</label>
                                <p className="text-lg">
                                    {selectedLog.user?.name} ({selectedLog.user?.email})
                                </p>
                            </div>
                            <div>
                                <label className="text-sm font-medium text-gray-600">Aksi</label>
                                <p className="text-lg font-semibold">{selectedLog.action}</p>
                            </div>
                            <div>
                                <label className="text-sm font-medium text-gray-600">Modul</label>
                                <p className="text-lg">{selectedLog.module || '-'}</p>
                            </div>
                            <div>
                                <label className="text-sm font-medium text-gray-600">Deskripsi</label>
                                <p className="text-lg">{selectedLog.description}</p>
                            </div>
                            <div>
                                <label className="text-sm font-medium text-gray-600">Level</label>
                                <p>
                                    <span
                                        className={`px-3 py-1 inline-flex text-sm font-semibold rounded-full ${getLevelColor(
                                            selectedLog.level
                                        )}`}
                                    >
                                        {selectedLog.level}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <label className="text-sm font-medium text-gray-600">IP Address</label>
                                <p className="text-lg font-mono">{selectedLog.ip_address}</p>
                            </div>
                            <div>
                                <label className="text-sm font-medium text-gray-600">User Agent</label>
                                <p className="text-sm text-gray-600 break-all">{selectedLog.user_agent}</p>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </DashboardLayout>
    );
}

// Simple Bar Chart Component
function SimpleBarChart({ data }: { data: { [key: string]: number } }) {
    const entries = Object.entries(data).sort((a, b) => a[0].localeCompare(b[0]));
    const maxValue = Math.max(...entries.map(([_, value]) => value));

    return (
        <div className="flex items-end justify-between h-full gap-2">
            {entries.map(([date, count]) => {
                const height = (count / maxValue) * 100;
                return (
                    <div key={date} className="flex flex-col items-center flex-1 group">
                        <div className="relative w-full">
                            <div
                                className="bg-blue-500 hover:bg-blue-600 rounded-t transition-all cursor-pointer"
                                style={{ height: `${height}%`, minHeight: count > 0 ? '20px' : '0' }}
                                title={`${date}: ${count} aktivitas`}
                            >
                                <span className="absolute -top-6 left-1/2 transform -translate-x-1/2 text-xs font-semibold opacity-0 group-hover:opacity-100 transition-opacity">
                                    {count}
                                </span>
                            </div>
                        </div>
                        <div className="text-xs text-gray-600 mt-2 transform rotate-45 origin-left">
                            {new Date(date).toLocaleDateString('id-ID', { month: 'short', day: 'numeric' })}
                        </div>
                    </div>
                );
            })}
        </div>
    );
}
