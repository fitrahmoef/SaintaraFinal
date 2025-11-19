import React, { useState, useEffect } from 'react';
import { Head, router } from '@inertiajs/react';
import DashboardLayout from '@/layouts/dashboardLayoutAdmin';
import axios from 'axios';
import { toast } from 'react-hot-toast';

interface User {
    id: number;
    name: string;
    email: string;
    user_type: string;
    role_label: string;
    notelp: string;
    negara: string;
    kota: string;
    created_at: string;
    customer?: {
        nama_lengkap: string;
        tanggal_lahir: string;
        jenis_kelamin: string;
    };
}

interface PaginatedResponse {
    data: User[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface Stats {
    total: number;
    personal: number;
    instansi: number;
    gift: number;
    admin: number;
    superadmin: number;
}

export default function Pengguna() {
    const [users, setUsers] = useState<User[]>([]);
    const [stats, setStats] = useState<Stats | null>(null);
    const [loading, setLoading] = useState(true);
    const [currentPage, setCurrentPage] = useState(1);
    const [lastPage, setLastPage] = useState(1);
    const [total, setTotal] = useState(0);
    const [search, setSearch] = useState('');
    const [userType, setUserType] = useState('personal');
    const [showCreateModal, setShowCreateModal] = useState(false);
    const [showEditModal, setShowEditModal] = useState(false);
    const [showDetailsModal, setShowDetailsModal] = useState(false);
    const [selectedUser, setSelectedUser] = useState<any>(null);
    const [selectedUsers, setSelectedUsers] = useState<number[]>([]);
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        password: '',
        user_type: 'personal',
        notelp: '',
        negara: '',
        kota: '',
        nama_lengkap: '',
        tanggal_lahir: '',
        jenis_kelamin: '',
        golongan_darah: '',
    });

    useEffect(() => {
        fetchUsers();
        fetchStats();
    }, [currentPage, search, userType]);

    const fetchUsers = async () => {
        try {
            setLoading(true);
            const response = await axios.get<PaginatedResponse>('/api/admin/users', {
                params: {
                    page: currentPage,
                    search,
                    type: userType,
                },
            });
            setUsers(response.data.data);
            setLastPage(response.data.last_page);
            setTotal(response.data.total);
        } catch (error: any) {
            toast.error(error.response?.data?.message || 'Gagal memuat data pengguna');
        } finally {
            setLoading(false);
        }
    };

    const fetchStats = async () => {
        try {
            const response = await axios.get<Stats>('/api/admin/users/stats');
            setStats(response.data);
        } catch (error) {
            console.error('Failed to fetch stats:', error);
        }
    };

    const fetchUserDetails = async (userId: number) => {
        try {
            const response = await axios.get(`/api/admin/users/${userId}/details`);
            setSelectedUser(response.data);
            setShowDetailsModal(true);
        } catch (error: any) {
            toast.error(error.response?.data?.message || 'Gagal memuat detail pengguna');
        }
    };

    const handleCreate = async (e: React.FormEvent) => {
        e.preventDefault();
        try {
            await axios.post('/api/admin/users', formData);
            toast.success('Pengguna berhasil dibuat');
            setShowCreateModal(false);
            resetForm();
            fetchUsers();
            fetchStats();
        } catch (error: any) {
            toast.error(error.response?.data?.message || 'Gagal membuat pengguna');
        }
    };

    const handleUpdate = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!selectedUser) return;

        try {
            await axios.put(`/api/admin/users/${selectedUser.user.id}`, formData);
            toast.success('Pengguna berhasil diupdate');
            setShowEditModal(false);
            resetForm();
            fetchUsers();
        } catch (error: any) {
            toast.error(error.response?.data?.message || 'Gagal mengupdate pengguna');
        }
    };

    const handleDelete = async (userId: number) => {
        if (!confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) return;

        try {
            await axios.delete(`/api/admin/users/${userId}`);
            toast.success('Pengguna berhasil dihapus');
            fetchUsers();
            fetchStats();
        } catch (error: any) {
            toast.error(error.response?.data?.message || 'Gagal menghapus pengguna');
        }
    };

    const handleBulkDelete = async () => {
        if (selectedUsers.length === 0) {
            toast.error('Pilih pengguna yang ingin dihapus');
            return;
        }

        if (!confirm(`Apakah Anda yakin ingin menghapus ${selectedUsers.length} pengguna?`)) return;

        try {
            await axios.post('/api/admin/users/bulk-delete', {
                user_ids: selectedUsers,
            });
            toast.success('Pengguna berhasil dihapus');
            setSelectedUsers([]);
            fetchUsers();
            fetchStats();
        } catch (error: any) {
            toast.error(error.response?.data?.message || 'Gagal menghapus pengguna');
        }
    };

    const handleExport = async () => {
        try {
            const response = await axios.get('/api/admin/users/export', {
                params: { type: userType, search },
                responseType: 'blob',
            });
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `users_${userType}_${new Date().getTime()}.csv`);
            document.body.appendChild(link);
            link.click();
            link.remove();
            toast.success('Data berhasil diexport');
        } catch (error) {
            toast.error('Gagal export data');
        }
    };

    const openEditModal = (user: User) => {
        setFormData({
            name: user.name,
            email: user.email,
            password: '',
            user_type: user.user_type,
            notelp: user.notelp || '',
            negara: user.negara || '',
            kota: user.kota || '',
            nama_lengkap: user.customer?.nama_lengkap || '',
            tanggal_lahir: user.customer?.tanggal_lahir || '',
            jenis_kelamin: user.customer?.jenis_kelamin || '',
            golongan_darah: '',
        });
        setSelectedUser({ user });
        setShowEditModal(true);
    };

    const resetForm = () => {
        setFormData({
            name: '',
            email: '',
            password: '',
            user_type: 'personal',
            notelp: '',
            negara: '',
            kota: '',
            nama_lengkap: '',
            tanggal_lahir: '',
            jenis_kelamin: '',
            golongan_darah: '',
        });
        setSelectedUser(null);
    };

    const toggleUserSelection = (userId: number) => {
        setSelectedUsers((prev) =>
            prev.includes(userId) ? prev.filter((id) => id !== userId) : [...prev, userId]
        );
    };

    const toggleSelectAll = () => {
        if (selectedUsers.length === users.length) {
            setSelectedUsers([]);
        } else {
            setSelectedUsers(users.map((u) => u.id));
        }
    };

    return (
        <DashboardLayout>
            <Head title="Manajemen Pengguna" />

            <div className="p-6">
                {/* Header */}
                <div className="mb-6">
                    <h1 className="text-3xl font-bold text-gray-800">Manajemen Pengguna</h1>
                    <p className="text-gray-600 mt-2">Kelola semua pengguna sistem</p>
                </div>

                {/* Stats Cards */}
                {stats && (
                    <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
                        <StatCard label="Total" value={stats.total} color="bg-blue-500" onClick={() => setUserType('all')} />
                        <StatCard label="Personal" value={stats.personal} color="bg-green-500" onClick={() => setUserType('personal')} />
                        <StatCard label="Instansi" value={stats.instansi} color="bg-purple-500" onClick={() => setUserType('instansi')} />
                        <StatCard label="Gift" value={stats.gift} color="bg-yellow-500" onClick={() => setUserType('gift')} />
                        <StatCard label="Admin" value={stats.admin} color="bg-red-500" onClick={() => setUserType('admin')} />
                        <StatCard label="Super Admin" value={stats.superadmin} color="bg-gray-800" onClick={() => setUserType('superadmin')} />
                    </div>
                )}

                {/* Actions Bar */}
                <div className="bg-white rounded-lg shadow p-4 mb-6">
                    <div className="flex flex-col md:flex-row gap-4 justify-between items-center">
                        <div className="flex gap-2 w-full md:w-auto">
                            <input
                                type="text"
                                placeholder="Cari nama atau email..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="px-4 py-2 border rounded-lg flex-1 md:w-64"
                            />
                            <select
                                value={userType}
                                onChange={(e) => setUserType(e.target.value)}
                                className="px-4 py-2 border rounded-lg"
                            >
                                <option value="personal">Personal</option>
                                <option value="instansi">Instansi</option>
                                <option value="gift">Gift</option>
                                <option value="admin">Admin</option>
                                <option value="superadmin">Super Admin</option>
                            </select>
                        </div>
                        <div className="flex gap-2 w-full md:w-auto">
                            {selectedUsers.length > 0 && (
                                <button
                                    onClick={handleBulkDelete}
                                    className="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                                >
                                    Hapus ({selectedUsers.length})
                                </button>
                            )}
                            <button
                                onClick={handleExport}
                                className="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
                            >
                                Export CSV
                            </button>
                            <button
                                onClick={() => setShowCreateModal(true)}
                                className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                            >
                                + Tambah Pengguna
                            </button>
                        </div>
                    </div>
                </div>

                {/* Users Table */}
                <div className="bg-white rounded-lg shadow overflow-hidden">
                    {loading ? (
                        <div className="p-8 text-center">
                            <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                            <p className="mt-4 text-gray-600">Memuat data...</p>
                        </div>
                    ) : users.length === 0 ? (
                        <div className="p-8 text-center text-gray-500">
                            Tidak ada data pengguna
                        </div>
                    ) : (
                        <>
                            <div className="overflow-x-auto">
                                <table className="w-full">
                                    <thead className="bg-gray-50 border-b">
                                        <tr>
                                            <th className="px-6 py-3 text-left">
                                                <input
                                                    type="checkbox"
                                                    checked={selectedUsers.length === users.length}
                                                    onChange={toggleSelectAll}
                                                    className="rounded"
                                                />
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Nama
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Email
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tipe
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                No. Telp
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Lokasi
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Dibuat
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Aksi
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody className="bg-white divide-y divide-gray-200">
                                        {users.map((user) => (
                                            <tr key={user.id} className="hover:bg-gray-50">
                                                <td className="px-6 py-4">
                                                    <input
                                                        type="checkbox"
                                                        checked={selectedUsers.includes(user.id)}
                                                        onChange={() => toggleUserSelection(user.id)}
                                                        className="rounded"
                                                    />
                                                </td>
                                                <td className="px-6 py-4">
                                                    <div className="text-sm font-medium text-gray-900">{user.name}</div>
                                                    {user.customer?.nama_lengkap && (
                                                        <div className="text-sm text-gray-500">{user.customer.nama_lengkap}</div>
                                                    )}
                                                </td>
                                                <td className="px-6 py-4 text-sm text-gray-500">{user.email}</td>
                                                <td className="px-6 py-4">
                                                    <span
                                                        className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                                                            user.user_type === 'superadmin'
                                                                ? 'bg-gray-800 text-white'
                                                                : user.user_type === 'admin'
                                                                ? 'bg-red-100 text-red-800'
                                                                : user.user_type === 'instansi'
                                                                ? 'bg-purple-100 text-purple-800'
                                                                : user.user_type === 'gift'
                                                                ? 'bg-yellow-100 text-yellow-800'
                                                                : 'bg-green-100 text-green-800'
                                                        }`}
                                                    >
                                                        {user.role_label}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 text-sm text-gray-500">{user.notelp || '-'}</td>
                                                <td className="px-6 py-4 text-sm text-gray-500">
                                                    {user.kota && user.negara ? `${user.kota}, ${user.negara}` : '-'}
                                                </td>
                                                <td className="px-6 py-4 text-sm text-gray-500">{user.created_at}</td>
                                                <td className="px-6 py-4 text-sm font-medium space-x-2">
                                                    <button
                                                        onClick={() => fetchUserDetails(user.id)}
                                                        className="text-blue-600 hover:text-blue-900"
                                                    >
                                                        Detail
                                                    </button>
                                                    <button
                                                        onClick={() => openEditModal(user)}
                                                        className="text-indigo-600 hover:text-indigo-900"
                                                    >
                                                        Edit
                                                    </button>
                                                    <button
                                                        onClick={() => handleDelete(user.id)}
                                                        className="text-red-600 hover:text-red-900"
                                                    >
                                                        Hapus
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
                                                {Array.from({ length: lastPage }, (_, i) => i + 1).map((page) => (
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
                                                ))}
                                            </nav>
                                        </div>
                                    </div>
                                </div>
                            )}
                        </>
                    )}
                </div>
            </div>

            {/* Create Modal */}
            {showCreateModal && (
                <Modal title="Tambah Pengguna Baru" onClose={() => setShowCreateModal(false)}>
                    <form onSubmit={handleCreate} className="space-y-4">
                        <UserForm formData={formData} setFormData={setFormData} isCreate />
                        <div className="flex gap-2 justify-end">
                            <button
                                type="button"
                                onClick={() => setShowCreateModal(false)}
                                className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                            >
                                Simpan
                            </button>
                        </div>
                    </form>
                </Modal>
            )}

            {/* Edit Modal */}
            {showEditModal && (
                <Modal title="Edit Pengguna" onClose={() => setShowEditModal(false)}>
                    <form onSubmit={handleUpdate} className="space-y-4">
                        <UserForm formData={formData} setFormData={setFormData} isCreate={false} />
                        <div className="flex gap-2 justify-end">
                            <button
                                type="button"
                                onClick={() => setShowEditModal(false)}
                                className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                            >
                                Update
                            </button>
                        </div>
                    </form>
                </Modal>
            )}

            {/* Details Modal */}
            {showDetailsModal && selectedUser && (
                <Modal title="Detail Pengguna" onClose={() => setShowDetailsModal(false)} size="large">
                    <UserDetailsView user={selectedUser} />
                </Modal>
            )}
        </DashboardLayout>
    );
}

// Components
function StatCard({ label, value, color, onClick }: { label: string; value: number; color: string; onClick: () => void }) {
    return (
        <div
            onClick={onClick}
            className={`${color} text-white p-4 rounded-lg shadow cursor-pointer hover:opacity-90 transition`}
        >
            <div className="text-2xl font-bold">{value}</div>
            <div className="text-sm opacity-90">{label}</div>
        </div>
    );
}

function Modal({
    title,
    children,
    onClose,
    size = 'medium',
}: {
    title: string;
    children: React.ReactNode;
    onClose: () => void;
    size?: 'medium' | 'large';
}) {
    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div className={`bg-white rounded-lg shadow-xl ${size === 'large' ? 'max-w-4xl' : 'max-w-2xl'} w-full max-h-[90vh] overflow-y-auto`}>
                <div className="flex justify-between items-center p-6 border-b">
                    <h2 className="text-xl font-bold">{title}</h2>
                    <button onClick={onClose} className="text-gray-400 hover:text-gray-600">
                        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div className="p-6">{children}</div>
            </div>
        </div>
    );
}

function UserForm({
    formData,
    setFormData,
    isCreate,
}: {
    formData: any;
    setFormData: React.Dispatch<React.SetStateAction<any>>;
    isCreate: boolean;
}) {
    return (
        <div className="space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                    <input
                        type="text"
                        required
                        value={formData.name}
                        onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                        className="w-full px-3 py-2 border rounded-lg"
                    />
                </div>
                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input
                        type="email"
                        required
                        value={formData.email}
                        onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                        className="w-full px-3 py-2 border rounded-lg"
                    />
                </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                        Password {!isCreate && '(kosongkan jika tidak ingin mengubah)'}
                    </label>
                    <input
                        type="password"
                        required={isCreate}
                        value={formData.password}
                        onChange={(e) => setFormData({ ...formData, password: e.target.value })}
                        className="w-full px-3 py-2 border rounded-lg"
                    />
                </div>
                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Tipe Pengguna</label>
                    <select
                        required
                        value={formData.user_type}
                        onChange={(e) => setFormData({ ...formData, user_type: e.target.value })}
                        className="w-full px-3 py-2 border rounded-lg"
                    >
                        <option value="personal">Personal</option>
                        <option value="instansi">Instansi</option>
                        <option value="gift">Gift</option>
                        <option value="admin">Admin</option>
                        <option value="superadmin">Super Admin</option>
                    </select>
                </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
                    <input
                        type="text"
                        value={formData.notelp}
                        onChange={(e) => setFormData({ ...formData, notelp: e.target.value })}
                        className="w-full px-3 py-2 border rounded-lg"
                    />
                </div>
                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Negara</label>
                    <input
                        type="text"
                        value={formData.negara}
                        onChange={(e) => setFormData({ ...formData, negara: e.target.value })}
                        className="w-full px-3 py-2 border rounded-lg"
                    />
                </div>
                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Kota</label>
                    <input
                        type="text"
                        value={formData.kota}
                        onChange={(e) => setFormData({ ...formData, kota: e.target.value })}
                        className="w-full px-3 py-2 border rounded-lg"
                    />
                </div>
            </div>

            {(formData.user_type === 'personal' || formData.user_type === 'gift') && (
                <>
                    <h3 className="text-lg font-semibold mt-4">Informasi Customer</h3>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                            <input
                                type="text"
                                value={formData.nama_lengkap}
                                onChange={(e) => setFormData({ ...formData, nama_lengkap: e.target.value })}
                                className="w-full px-3 py-2 border rounded-lg"
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                            <input
                                type="date"
                                value={formData.tanggal_lahir}
                                onChange={(e) => setFormData({ ...formData, tanggal_lahir: e.target.value })}
                                className="w-full px-3 py-2 border rounded-lg"
                            />
                        </div>
                    </div>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
                            <select
                                value={formData.jenis_kelamin}
                                onChange={(e) => setFormData({ ...formData, jenis_kelamin: e.target.value })}
                                className="w-full px-3 py-2 border rounded-lg"
                            >
                                <option value="">Pilih</option>
                                <option value="pria">Pria</option>
                                <option value="wanita">Wanita</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">Golongan Darah</label>
                            <select
                                value={formData.golongan_darah}
                                onChange={(e) => setFormData({ ...formData, golongan_darah: e.target.value })}
                                className="w-full px-3 py-2 border rounded-lg"
                            >
                                <option value="">Pilih</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="AB">AB</option>
                                <option value="O">O</option>
                            </select>
                        </div>
                    </div>
                </>
            )}
        </div>
    );
}

function UserDetailsView({ user }: { user: any }) {
    return (
        <div className="space-y-6">
            {/* User Info */}
            <div>
                <h3 className="text-lg font-semibold mb-4 border-b pb-2">Informasi Pengguna</h3>
                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <label className="text-sm text-gray-600">Nama</label>
                        <p className="font-medium">{user.user.name}</p>
                    </div>
                    <div>
                        <label className="text-sm text-gray-600">Email</label>
                        <p className="font-medium">{user.user.email}</p>
                    </div>
                    <div>
                        <label className="text-sm text-gray-600">Tipe</label>
                        <p className="font-medium">{user.user.role_label}</p>
                    </div>
                    <div>
                        <label className="text-sm text-gray-600">No. Telepon</label>
                        <p className="font-medium">{user.user.notelp || '-'}</p>
                    </div>
                    <div>
                        <label className="text-sm text-gray-600">Lokasi</label>
                        <p className="font-medium">
                            {user.user.kota && user.user.negara ? `${user.user.kota}, ${user.user.negara}` : '-'}
                        </p>
                    </div>
                    <div>
                        <label className="text-sm text-gray-600">Terdaftar</label>
                        <p className="font-medium">{new Date(user.user.created_at).toLocaleString('id-ID')}</p>
                    </div>
                </div>
            </div>

            {/* Token Stats */}
            {user.tokens && (
                <div>
                    <h3 className="text-lg font-semibold mb-4 border-b pb-2">Statistik Token</h3>
                    <div className="grid grid-cols-4 gap-4">
                        <div className="bg-blue-50 p-4 rounded-lg">
                            <div className="text-2xl font-bold text-blue-600">{user.tokens.total_purchased}</div>
                            <div className="text-sm text-gray-600">Token Dibeli</div>
                        </div>
                        <div className="bg-red-50 p-4 rounded-lg">
                            <div className="text-2xl font-bold text-red-600">{user.tokens.total_used}</div>
                            <div className="text-sm text-gray-600">Token Digunakan</div>
                        </div>
                        <div className="bg-green-50 p-4 rounded-lg">
                            <div className="text-2xl font-bold text-green-600">{user.tokens.remaining}</div>
                            <div className="text-sm text-gray-600">Token Tersisa</div>
                        </div>
                        <div className="bg-purple-50 p-4 rounded-lg">
                            <div className="text-2xl font-bold text-purple-600">{user.tokens.purchase_count}</div>
                            <div className="text-sm text-gray-600">Transaksi</div>
                        </div>
                    </div>
                </div>
            )}

            {/* Activity Stats */}
            {user.activity && (
                <div>
                    <h3 className="text-lg font-semibold mb-4 border-b pb-2">Aktivitas</h3>
                    <div className="grid grid-cols-3 gap-4">
                        <div className="bg-gray-50 p-4 rounded-lg">
                            <div className="text-2xl font-bold text-gray-600">{user.activity.total_activities}</div>
                            <div className="text-sm text-gray-600">Total Aktivitas</div>
                        </div>
                        <div className="bg-gray-50 p-4 rounded-lg">
                            <div className="text-2xl font-bold text-gray-600">{user.activity.active_days}</div>
                            <div className="text-sm text-gray-600">Hari Aktif</div>
                        </div>
                        <div className="bg-gray-50 p-4 rounded-lg">
                            <div className="text-sm text-gray-600">Aktivitas Terakhir</div>
                            <div className="text-sm font-medium">
                                {user.activity.last_activity
                                    ? new Date(user.activity.last_activity).toLocaleString('id-ID')
                                    : 'Belum ada'}
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* Recent Transactions */}
            {user.transactions && user.transactions.length > 0 && (
                <div>
                    <h3 className="text-lg font-semibold mb-4 border-b pb-2">Transaksi Terakhir</h3>
                    <div className="space-y-2">
                        {user.transactions.map((transaction: any) => (
                            <div key={transaction.id} className="flex justify-between items-center p-3 bg-gray-50 rounded">
                                <div>
                                    <p className="font-medium">{transaction.transaction_code}</p>
                                    <p className="text-sm text-gray-600">{transaction.created_at}</p>
                                </div>
                                <div className="text-right">
                                    <p className="font-bold">Rp {transaction.amount.toLocaleString('id-ID')}</p>
                                    <span
                                        className={`text-xs px-2 py-1 rounded ${
                                            transaction.status === 'paid'
                                                ? 'bg-green-100 text-green-800'
                                                : transaction.status === 'pending'
                                                ? 'bg-yellow-100 text-yellow-800'
                                                : 'bg-red-100 text-red-800'
                                        }`}
                                    >
                                        {transaction.status}
                                    </span>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            )}

            {/* Test Results */}
            {user.test_results && user.test_results.length > 0 && (
                <div>
                    <h3 className="text-lg font-semibold mb-4 border-b pb-2">Hasil Tes Terakhir</h3>
                    <div className="space-y-2">
                        {user.test_results.map((result: any) => (
                            <div key={result.id} className="p-3 bg-gray-50 rounded">
                                <p className="font-medium">{result.test_name}</p>
                                <p className="text-sm text-gray-600">Score: {result.score}</p>
                                <p className="text-sm text-gray-600">{result.completed_at}</p>
                            </div>
                        ))}
                    </div>
                </div>
            )}
        </div>
    );
}
