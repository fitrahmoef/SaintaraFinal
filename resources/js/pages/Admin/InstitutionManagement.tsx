import AdminDashboardLayout from '@/layouts/dashboardLayoutAdmin';
import { Head, router } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import {
    HiPlus,
    HiSearch,
    HiPencil,
    HiTrash,
    HiEye,
    HiX,
    HiChevronLeft,
    HiChevronRight,
    HiOfficeBuilding,
    HiUsers,
    HiCheckCircle,
    HiClock,
    HiXCircle,
    HiDownload,
    HiCalendar,
} from 'react-icons/hi';
import axios from 'axios';
import { toast } from 'react-hot-toast';

interface Institution {
    id: number;
    nama_instansi: string;
    nama_admin: string;
    email_instansi: string;
    nomor_telepon: string;
    kota_instansi: string;
    status_akun: 'aktif' | 'tidak_aktif' | 'pending';
    tanggal_bergabung: string;
    tanggal_berakhir: string;
    stats: {
        total_employees: number;
        total_tests_completed: number;
        current_token_balance: number;
    };
}

export default function InstitutionManagement() {
    const [institutions, setInstitutions] = useState<Institution[]>([]);
    const [stats, setStats] = useState<any>(null);
    const [loading, setLoading] = useState(false);
    const [searchQuery, setSearchQuery] = useState('');
    const [statusFilter, setStatusFilter] = useState<string>('');
    const [pagination, setPagination] = useState({
        current_page: 1,
        last_page: 1,
        per_page: 15,
        total: 0,
    });

    const [showCreateModal, setShowCreateModal] = useState(false);
    const [showEditModal, setShowEditModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [showExtendModal, setShowExtendModal] = useState(false);
    const [selectedInstitution, setSelectedInstitution] = useState<Institution | null>(null);

    const [formData, setFormData] = useState({
        name: '',
        email: '',
        password: '',
        nama_admin: '',
        nama_instansi: '',
        nomor_telepon: '',
        email_instansi: '',
        alamat_instansi: '',
        kota_instansi: '',
        provinsi_instansi: '',
        kode_pos: '',
        status_akun: 'pending',
        tanggal_berakhir: '',
        catatan: '',
    });

    const [extendDays, setExtendDays] = useState(30);

    useEffect(() => {
        fetchInstitutions();
        fetchStats();
    }, [statusFilter]);

    useEffect(() => {
        const timer = setTimeout(() => {
            if (searchQuery !== '') {
                fetchInstitutions();
            }
        }, 500);
        return () => clearTimeout(timer);
    }, [searchQuery]);

    const fetchStats = async () => {
        try {
            const response = await axios.get('/api/admin/institutions/stats');
            setStats(response.data);
        } catch (error: any) {
            console.error('Error fetching stats:', error);
        }
    };

    const fetchInstitutions = async (page = 1) => {
        setLoading(true);
        try {
            const params: any = {
                page,
                per_page: pagination.per_page,
            };

            if (statusFilter) params.status = statusFilter;
            if (searchQuery) params.search = searchQuery;

            const response = await axios.get('/api/admin/institutions', { params });
            setInstitutions(response.data.data);
            setPagination({
                current_page: response.data.current_page,
                last_page: response.data.last_page,
                per_page: response.data.per_page,
                total: response.data.total,
            });
        } catch (error: any) {
            toast.error(error.response?.data?.message || 'Failed to fetch institutions');
        } finally {
            setLoading(false);
        }
    };

    const handleCreate = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);

        try {
            await axios.post('/api/admin/institutions', formData);
            toast.success('Institution created successfully');
            setShowCreateModal(false);
            resetForm();
            fetchInstitutions();
            fetchStats();
        } catch (error: any) {
            toast.error(error.response?.data?.message || 'Failed to create institution');
        } finally {
            setLoading(false);
        }
    };

    const handleUpdate = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!selectedInstitution) return;

        setLoading(true);
        try {
            await axios.put(`/api/admin/institutions/${selectedInstitution.id}`, formData);
            toast.success('Institution updated successfully');
            setShowEditModal(false);
            setSelectedInstitution(null);
            resetForm();
            fetchInstitutions();
        } catch (error: any) {
            toast.error(error.response?.data?.message || 'Failed to update institution');
        } finally {
            setLoading(false);
        }
    };

    const handleDelete = async () => {
        if (!selectedInstitution) return;

        setLoading(true);
        try {
            await axios.delete(`/api/admin/institutions/${selectedInstitution.id}`);
            toast.success('Institution deleted successfully');
            setShowDeleteModal(false);
            setSelectedInstitution(null);
            fetchInstitutions();
            fetchStats();
        } catch (error: any) {
            toast.error(error.response?.data?.message || 'Failed to delete institution');
        } finally {
            setLoading(false);
        }
    };

    const handleUpdateStatus = async (institutionId: number, newStatus: string) => {
        try {
            await axios.put(`/api/admin/institutions/${institutionId}/status`, { status: newStatus });
            toast.success('Status updated successfully');
            fetchInstitutions();
            fetchStats();
        } catch (error: any) {
            toast.error(error.response?.data?.message || 'Failed to update status');
        }
    };

    const handleExtendExpiry = async () => {
        if (!selectedInstitution) return;

        try {
            await axios.post(`/api/admin/institutions/${selectedInstitution.id}/extend-expiry`, {
                days: extendDays,
            });
            toast.success(`Expiry extended by ${extendDays} days`);
            setShowExtendModal(false);
            setSelectedInstitution(null);
            setExtendDays(30);
            fetchInstitutions();
        } catch (error: any) {
            toast.error(error.response?.data?.message || 'Failed to extend expiry');
        }
    };

    const handleExport = async () => {
        try {
            const params: any = {};
            if (statusFilter) params.status = statusFilter;

            const response = await axios.get('/api/admin/institutions/export', {
                params,
                responseType: 'blob',
            });

            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `institutions_${new Date().toISOString().split('T')[0]}.csv`);
            document.body.appendChild(link);
            link.click();
            link.remove();

            toast.success('Data exported successfully');
        } catch (error: any) {
            toast.error('Failed to export data');
        }
    };

    const viewInstitutionDetail = (institutionId: number) => {
        router.visit(`/admin/institutions/${institutionId}/detail`);
    };

    const openEditModal = (institution: Institution) => {
        setSelectedInstitution(institution);
        setFormData({
            name: institution.nama_admin,
            email: institution.email_instansi,
            password: '',
            nama_admin: institution.nama_admin,
            nama_instansi: institution.nama_instansi,
            nomor_telepon: institution.nomor_telepon || '',
            email_instansi: institution.email_instansi || '',
            alamat_instansi: '',
            kota_instansi: institution.kota_instansi || '',
            provinsi_instansi: '',
            kode_pos: '',
            status_akun: institution.status_akun,
            tanggal_berakhir: institution.tanggal_berakhir || '',
            catatan: '',
        });
        setShowEditModal(true);
    };

    const resetForm = () => {
        setFormData({
            name: '',
            email: '',
            password: '',
            nama_admin: '',
            nama_instansi: '',
            nomor_telepon: '',
            email_instansi: '',
            alamat_instansi: '',
            kota_instansi: '',
            provinsi_instansi: '',
            kode_pos: '',
            status_akun: 'pending',
            tanggal_berakhir: '',
            catatan: '',
        });
    };

    const getStatusColor = (status: string) => {
        const colors: { [key: string]: string } = {
            aktif: 'bg-green-100 text-green-800',
            tidak_aktif: 'bg-red-100 text-red-800',
            pending: 'bg-yellow-100 text-yellow-800',
        };
        return colors[status] || 'bg-gray-100 text-gray-800';
    };

    const getStatusIcon = (status: string) => {
        const icons: { [key: string]: JSX.Element } = {
            aktif: <HiCheckCircle className="h-4 w-4" />,
            tidak_aktif: <HiXCircle className="h-4 w-4" />,
            pending: <HiClock className="h-4 w-4" />,
        };
        return icons[status];
    };

    return (
        <AdminDashboardLayout>
            <Head title="Manajemen Instansi" />

            <div className="space-y-6 font-poppins">
                {/* Statistics Cards */}
                {stats && (
                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                        <div className="rounded-2xl bg-white p-6 shadow-sm">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-sm text-gray-600">Total Instansi</p>
                                    <p className="mt-2 text-3xl font-bold text-gray-900">{stats.total_institutions}</p>
                                </div>
                                <div className="rounded-full bg-blue-100 p-4">
                                    <HiOfficeBuilding className="h-8 w-8 text-blue-600" />
                                </div>
                            </div>
                        </div>

                        <div className="rounded-2xl bg-white p-6 shadow-sm">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-sm text-gray-600">Instansi Aktif</p>
                                    <p className="mt-2 text-3xl font-bold text-green-600">{stats.active_institutions}</p>
                                </div>
                                <div className="rounded-full bg-green-100 p-4">
                                    <HiCheckCircle className="h-8 w-8 text-green-600" />
                                </div>
                            </div>
                        </div>

                        <div className="rounded-2xl bg-white p-6 shadow-sm">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-sm text-gray-600">Total Karyawan</p>
                                    <p className="mt-2 text-3xl font-bold text-gray-900">{stats.total_employees}</p>
                                </div>
                                <div className="rounded-full bg-purple-100 p-4">
                                    <HiUsers className="h-8 w-8 text-purple-600" />
                                </div>
                            </div>
                        </div>

                        <div className="rounded-2xl bg-white p-6 shadow-sm">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-sm text-gray-600">Segera Berakhir</p>
                                    <p className="mt-2 text-3xl font-bold text-orange-600">{stats.expiring_soon}</p>
                                </div>
                                <div className="rounded-full bg-orange-100 p-4">
                                    <HiClock className="h-8 w-8 text-orange-600" />
                                </div>
                            </div>
                        </div>
                    </div>
                )}

                {/* Filter and Actions */}
                <div className="rounded-3xl bg-white p-8 shadow-sm">
                    <div className="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div className="flex flex-col gap-4 md:flex-row md:items-center">
                            {/* Search */}
                            <div className="relative w-full md:w-96">
                                <input
                                    type="text"
                                    value={searchQuery}
                                    onChange={(e) => setSearchQuery(e.target.value)}
                                    placeholder="Cari instansi (nama, admin, email, kota)..."
                                    className="w-full rounded-full border-none bg-gray-100 py-3 pr-12 pl-6 text-gray-600 transition-all focus:ring-2 focus:ring-yellow-400"
                                />
                                <HiSearch className="absolute top-1/2 right-4 h-6 w-6 -translate-y-1/2 transform text-gray-400" />
                            </div>

                            {/* Status Filter */}
                            <select
                                value={statusFilter}
                                onChange={(e) => setStatusFilter(e.target.value)}
                                className="rounded-full border-none bg-gray-100 px-6 py-3 text-gray-600 focus:ring-2 focus:ring-yellow-400"
                            >
                                <option value="">Semua Status</option>
                                <option value="aktif">Aktif</option>
                                <option value="pending">Pending</option>
                                <option value="tidak_aktif">Tidak Aktif</option>
                            </select>
                        </div>

                        <div className="flex gap-2">
                            <button
                                onClick={handleExport}
                                className="flex items-center gap-2 rounded-full border-2 border-yellow-400 bg-white px-6 py-3 font-medium text-gray-900 transition-all hover:bg-yellow-50"
                            >
                                <HiDownload className="h-5 w-5" />
                                Export CSV
                            </button>

                            <button
                                onClick={() => setShowCreateModal(true)}
                                className="flex items-center gap-2 rounded-full bg-yellow-400 px-8 py-3 font-bold text-gray-900 shadow-md transition-all hover:bg-yellow-500"
                            >
                                <HiPlus className="h-5 w-5" />
                                Tambah Instansi
                            </button>
                        </div>
                    </div>

                    {/* Table */}
                    <div className="overflow-x-auto">
                        {loading ? (
                            <div className="flex items-center justify-center py-12">
                                <div className="h-12 w-12 animate-spin rounded-full border-b-2 border-yellow-400"></div>
                            </div>
                        ) : institutions.length === 0 ? (
                            <div className="py-12 text-center text-gray-500">Tidak ada data instansi</div>
                        ) : (
                            <>
                                <table className="w-full min-w-[1000px]">
                                    <thead>
                                        <tr className="bg-yellow-400 text-gray-900">
                                            <th className="rounded-tl-2xl p-4 text-left font-bold">No</th>
                                            <th className="p-4 text-left font-bold">Nama Instansi</th>
                                            <th className="p-4 text-left font-bold">Admin</th>
                                            <th className="p-4 text-left font-bold">Kota</th>
                                            <th className="p-4 text-left font-bold">Karyawan</th>
                                            <th className="p-4 text-left font-bold">Status</th>
                                            <th className="p-4 text-left font-bold">Berakhir</th>
                                            <th className="rounded-tr-2xl p-4 text-center font-bold">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {institutions.map((institution, index) => (
                                            <tr
                                                key={institution.id}
                                                className="border-b border-gray-100 transition-colors hover:bg-gray-50"
                                            >
                                                <td className="p-4 text-gray-600">
                                                    {(pagination.current_page - 1) * pagination.per_page + index + 1}
                                                </td>
                                                <td className="p-4">
                                                    <div className="font-medium text-gray-900">
                                                        {institution.nama_instansi}
                                                    </div>
                                                    <div className="text-xs text-gray-500">{institution.email_instansi}</div>
                                                </td>
                                                <td className="p-4 text-gray-600">{institution.nama_admin}</td>
                                                <td className="p-4 text-gray-600">{institution.kota_instansi || '-'}</td>
                                                <td className="p-4 text-gray-600">
                                                    <div className="flex items-center gap-1">
                                                        <HiUsers className="h-4 w-4" />
                                                        {institution.stats?.total_employees || 0}
                                                    </div>
                                                </td>
                                                <td className="p-4">
                                                    <button
                                                        onClick={() => {
                                                            const statuses = ['aktif', 'pending', 'tidak_aktif'];
                                                            const currentIndex = statuses.indexOf(institution.status_akun);
                                                            const nextStatus =
                                                                statuses[(currentIndex + 1) % statuses.length];
                                                            handleUpdateStatus(institution.id, nextStatus);
                                                        }}
                                                        className={`inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium transition-all hover:opacity-80 ${getStatusColor(institution.status_akun)}`}
                                                    >
                                                        {getStatusIcon(institution.status_akun)}
                                                        {institution.status_akun}
                                                    </button>
                                                </td>
                                                <td className="p-4 text-gray-600">
                                                    {institution.tanggal_berakhir ? (
                                                        <div>
                                                            <div className="text-sm">
                                                                {new Date(institution.tanggal_berakhir).toLocaleDateString(
                                                                    'id-ID'
                                                                )}
                                                            </div>
                                                            <button
                                                                onClick={() => {
                                                                    setSelectedInstitution(institution);
                                                                    setShowExtendModal(true);
                                                                }}
                                                                className="text-xs text-blue-600 hover:underline"
                                                            >
                                                                Perpanjang
                                                            </button>
                                                        </div>
                                                    ) : (
                                                        '-'
                                                    )}
                                                </td>
                                                <td className="p-4">
                                                    <div className="flex items-center justify-center gap-2">
                                                        <button
                                                            onClick={() => viewInstitutionDetail(institution.id)}
                                                            className="rounded-full bg-blue-500 p-2 text-white transition-all hover:bg-blue-600"
                                                            title="Lihat Detail"
                                                        >
                                                            <HiEye className="h-4 w-4" />
                                                        </button>
                                                        <button
                                                            onClick={() => openEditModal(institution)}
                                                            className="rounded-full bg-yellow-500 p-2 text-white transition-all hover:bg-yellow-600"
                                                            title="Edit"
                                                        >
                                                            <HiPencil className="h-4 w-4" />
                                                        </button>
                                                        <button
                                                            onClick={() => {
                                                                setSelectedInstitution(institution);
                                                                setShowDeleteModal(true);
                                                            }}
                                                            className="rounded-full bg-red-500 p-2 text-white transition-all hover:bg-red-600"
                                                            title="Hapus"
                                                        >
                                                            <HiTrash className="h-4 w-4" />
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>

                                {/* Pagination */}
                                <div className="mt-6 flex items-center justify-between">
                                    <div className="text-sm text-gray-600">
                                        Menampilkan {(pagination.current_page - 1) * pagination.per_page + 1} -{' '}
                                        {Math.min(pagination.current_page * pagination.per_page, pagination.total)} dari{' '}
                                        {pagination.total} instansi
                                    </div>

                                    <div className="flex items-center gap-2">
                                        <button
                                            onClick={() => fetchInstitutions(pagination.current_page - 1)}
                                            disabled={pagination.current_page === 1}
                                            className="rounded-full bg-gray-200 p-2 text-gray-700 transition-all hover:bg-gray-300 disabled:cursor-not-allowed disabled:opacity-50"
                                        >
                                            <HiChevronLeft className="h-5 w-5" />
                                        </button>

                                        <span className="text-sm text-gray-600">
                                            Halaman {pagination.current_page} dari {pagination.last_page}
                                        </span>

                                        <button
                                            onClick={() => fetchInstitutions(pagination.current_page + 1)}
                                            disabled={pagination.current_page === pagination.last_page}
                                            className="rounded-full bg-gray-200 p-2 text-gray-700 transition-all hover:bg-gray-300 disabled:cursor-not-allowed disabled:opacity-50"
                                        >
                                            <HiChevronRight className="h-5 w-5" />
                                        </button>
                                    </div>
                                </div>
                            </>
                        )}
                    </div>
                </div>
            </div>

            {/* Create Modal */}
            {showCreateModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
                    <div className="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-3xl bg-white p-8 shadow-xl">
                        <div className="mb-6 flex items-center justify-between">
                            <h2 className="text-2xl font-bold text-gray-900">Tambah Instansi Baru</h2>
                            <button
                                onClick={() => {
                                    setShowCreateModal(false);
                                    resetForm();
                                }}
                                className="rounded-full p-2 text-gray-500 hover:bg-gray-100"
                            >
                                <HiX className="h-6 w-6" />
                            </button>
                        </div>

                        <form onSubmit={handleCreate} className="space-y-6">
                            <div className="rounded-2xl bg-gray-50 p-6">
                                <h3 className="mb-4 font-semibold text-gray-900">Informasi Akun</h3>
                                <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <label className="mb-2 block text-sm font-medium text-gray-700">Nama Admin</label>
                                        <input
                                            type="text"
                                            required
                                            value={formData.nama_admin}
                                            onChange={(e) =>
                                                setFormData({ ...formData, nama_admin: e.target.value, name: e.target.value })
                                            }
                                            className="w-full rounded-full border border-gray-300 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400"
                                        />
                                    </div>

                                    <div>
                                        <label className="mb-2 block text-sm font-medium text-gray-700">Email</label>
                                        <input
                                            type="email"
                                            required
                                            value={formData.email}
                                            onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                                            className="w-full rounded-full border border-gray-300 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400"
                                        />
                                    </div>

                                    <div>
                                        <label className="mb-2 block text-sm font-medium text-gray-700">Password</label>
                                        <input
                                            type="password"
                                            required
                                            value={formData.password}
                                            onChange={(e) => setFormData({ ...formData, password: e.target.value })}
                                            className="w-full rounded-full border border-gray-300 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div className="rounded-2xl bg-gray-50 p-6">
                                <h3 className="mb-4 font-semibold text-gray-900">Informasi Instansi</h3>
                                <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div className="md:col-span-2">
                                        <label className="mb-2 block text-sm font-medium text-gray-700">Nama Instansi</label>
                                        <input
                                            type="text"
                                            required
                                            value={formData.nama_instansi}
                                            onChange={(e) => setFormData({ ...formData, nama_instansi: e.target.value })}
                                            className="w-full rounded-full border border-gray-300 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400"
                                        />
                                    </div>

                                    <div>
                                        <label className="mb-2 block text-sm font-medium text-gray-700">Nomor Telepon</label>
                                        <input
                                            type="text"
                                            value={formData.nomor_telepon}
                                            onChange={(e) => setFormData({ ...formData, nomor_telepon: e.target.value })}
                                            className="w-full rounded-full border border-gray-300 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400"
                                        />
                                    </div>

                                    <div>
                                        <label className="mb-2 block text-sm font-medium text-gray-700">Email Instansi</label>
                                        <input
                                            type="email"
                                            value={formData.email_instansi}
                                            onChange={(e) => setFormData({ ...formData, email_instansi: e.target.value })}
                                            className="w-full rounded-full border border-gray-300 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400"
                                        />
                                    </div>

                                    <div>
                                        <label className="mb-2 block text-sm font-medium text-gray-700">Kota</label>
                                        <input
                                            type="text"
                                            value={formData.kota_instansi}
                                            onChange={(e) => setFormData({ ...formData, kota_instansi: e.target.value })}
                                            className="w-full rounded-full border border-gray-300 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400"
                                        />
                                    </div>

                                    <div>
                                        <label className="mb-2 block text-sm font-medium text-gray-700">Tanggal Berakhir</label>
                                        <input
                                            type="date"
                                            value={formData.tanggal_berakhir}
                                            onChange={(e) => setFormData({ ...formData, tanggal_berakhir: e.target.value })}
                                            className="w-full rounded-full border border-gray-300 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div className="flex justify-end gap-3">
                                <button
                                    type="button"
                                    onClick={() => {
                                        setShowCreateModal(false);
                                        resetForm();
                                    }}
                                    className="rounded-full border border-gray-300 px-6 py-2 font-medium text-gray-700 hover:bg-gray-50"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    disabled={loading}
                                    className="rounded-full bg-yellow-400 px-6 py-2 font-bold text-gray-900 hover:bg-yellow-500 disabled:opacity-50"
                                >
                                    {loading ? 'Menyimpan...' : 'Simpan'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* Edit Modal - Similar structure to Create Modal */}
            {showEditModal && selectedInstitution && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
                    <div className="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-3xl bg-white p-8 shadow-xl">
                        <div className="mb-6 flex items-center justify-between">
                            <h2 className="text-2xl font-bold text-gray-900">Edit Instansi</h2>
                            <button
                                onClick={() => {
                                    setShowEditModal(false);
                                    setSelectedInstitution(null);
                                    resetForm();
                                }}
                                className="rounded-full p-2 text-gray-500 hover:bg-gray-100"
                            >
                                <HiX className="h-6 w-6" />
                            </button>
                        </div>

                        <form onSubmit={handleUpdate} className="space-y-4">
                            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div className="md:col-span-2">
                                    <label className="mb-2 block text-sm font-medium text-gray-700">Nama Instansi</label>
                                    <input
                                        type="text"
                                        required
                                        value={formData.nama_instansi}
                                        onChange={(e) => setFormData({ ...formData, nama_instansi: e.target.value })}
                                        className="w-full rounded-full border border-gray-300 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400"
                                    />
                                </div>

                                <div>
                                    <label className="mb-2 block text-sm font-medium text-gray-700">Nama Admin</label>
                                    <input
                                        type="text"
                                        required
                                        value={formData.nama_admin}
                                        onChange={(e) => setFormData({ ...formData, nama_admin: e.target.value })}
                                        className="w-full rounded-full border border-gray-300 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400"
                                    />
                                </div>

                                <div>
                                    <label className="mb-2 block text-sm font-medium text-gray-700">Nomor Telepon</label>
                                    <input
                                        type="text"
                                        value={formData.nomor_telepon}
                                        onChange={(e) => setFormData({ ...formData, nomor_telepon: e.target.value })}
                                        className="w-full rounded-full border border-gray-300 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400"
                                    />
                                </div>

                                <div>
                                    <label className="mb-2 block text-sm font-medium text-gray-700">Kota</label>
                                    <input
                                        type="text"
                                        value={formData.kota_instansi}
                                        onChange={(e) => setFormData({ ...formData, kota_instansi: e.target.value })}
                                        className="w-full rounded-full border border-gray-300 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400"
                                    />
                                </div>

                                <div>
                                    <label className="mb-2 block text-sm font-medium text-gray-700">Status</label>
                                    <select
                                        value={formData.status_akun}
                                        onChange={(e) => setFormData({ ...formData, status_akun: e.target.value })}
                                        className="w-full rounded-full border border-gray-300 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400"
                                    >
                                        <option value="aktif">Aktif</option>
                                        <option value="pending">Pending</option>
                                        <option value="tidak_aktif">Tidak Aktif</option>
                                    </select>
                                </div>
                            </div>

                            <div className="flex justify-end gap-3 pt-4">
                                <button
                                    type="button"
                                    onClick={() => {
                                        setShowEditModal(false);
                                        setSelectedInstitution(null);
                                        resetForm();
                                    }}
                                    className="rounded-full border border-gray-300 px-6 py-2 font-medium text-gray-700 hover:bg-gray-50"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    disabled={loading}
                                    className="rounded-full bg-yellow-400 px-6 py-2 font-bold text-gray-900 hover:bg-yellow-500 disabled:opacity-50"
                                >
                                    {loading ? 'Menyimpan...' : 'Simpan Perubahan'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* Delete Modal */}
            {showDeleteModal && selectedInstitution && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
                    <div className="w-full max-w-md rounded-3xl bg-white p-8 shadow-xl">
                        <h2 className="mb-4 text-2xl font-bold text-gray-900">Konfirmasi Hapus</h2>
                        <p className="mb-6 text-gray-600">
                            Apakah Anda yakin ingin menghapus instansi <strong>{selectedInstitution.nama_instansi}</strong>?
                        </p>

                        <div className="flex justify-end gap-3">
                            <button
                                onClick={() => {
                                    setShowDeleteModal(false);
                                    setSelectedInstitution(null);
                                }}
                                className="rounded-full border border-gray-300 px-6 py-2 font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Batal
                            </button>
                            <button
                                onClick={handleDelete}
                                disabled={loading}
                                className="rounded-full bg-red-500 px-6 py-2 font-bold text-white hover:bg-red-600 disabled:opacity-50"
                            >
                                {loading ? 'Menghapus...' : 'Hapus'}
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Extend Expiry Modal */}
            {showExtendModal && selectedInstitution && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
                    <div className="w-full max-w-md rounded-3xl bg-white p-8 shadow-xl">
                        <h2 className="mb-4 text-2xl font-bold text-gray-900">Perpanjang Masa Aktif</h2>
                        <p className="mb-4 text-gray-600">
                            Perpanjang masa aktif <strong>{selectedInstitution.nama_instansi}</strong>
                        </p>

                        <div className="mb-6">
                            <label className="mb-2 block text-sm font-medium text-gray-700">Jumlah Hari</label>
                            <input
                                type="number"
                                min="1"
                                value={extendDays}
                                onChange={(e) => setExtendDays(parseInt(e.target.value) || 0)}
                                className="w-full rounded-full border border-gray-300 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400"
                            />
                        </div>

                        <div className="flex justify-end gap-3">
                            <button
                                onClick={() => {
                                    setShowExtendModal(false);
                                    setSelectedInstitution(null);
                                    setExtendDays(30);
                                }}
                                className="rounded-full border border-gray-300 px-6 py-2 font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Batal
                            </button>
                            <button
                                onClick={handleExtendExpiry}
                                className="rounded-full bg-yellow-400 px-6 py-2 font-bold text-gray-900 hover:bg-yellow-500"
                            >
                                Perpanjang
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </AdminDashboardLayout>
    );
}
