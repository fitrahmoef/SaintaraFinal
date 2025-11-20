import AdminDashboardLayout from '@/layouts/dashboardLayoutAdmin';
import { Head, router } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { HiPlus, HiSearch, HiPencil, HiTrash, HiEye, HiX, HiChevronLeft, HiChevronRight } from 'react-icons/hi';
import axios from 'axios';
import { toast } from 'react-hot-toast';

interface User {
    id: number;
    name: string;
    email: string;
    notelp?: string;
    user_type: string;
    status?: string;
    created_at: string;
    customer?: {
        saldo_token: number;
        nama: string;
    };
    admin_instansi?: {
        nama_instansi: string;
        status_akun: string;
    };
}

interface PaginationMeta {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

export default function UserManagement() {
    const [activeTab, setActiveTab] = useState<string>('all');
    const [users, setUsers] = useState<User[]>([]);
    const [loading, setLoading] = useState(false);
    const [searchQuery, setSearchQuery] = useState('');
    const [pagination, setPagination] = useState<PaginationMeta>({
        current_page: 1,
        last_page: 1,
        per_page: 15,
        total: 0,
    });

    // Modal states
    const [showCreateModal, setShowCreateModal] = useState(false);
    const [showEditModal, setShowEditModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [selectedUser, setSelectedUser] = useState<User | null>(null);

    // Form state
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        notelp: '',
        user_type: 'personal',
        negara: 'Indonesia',
        kota: '',
    });

    // Fetch users
    const fetchUsers = async (page = 1, type = activeTab, search = searchQuery) => {
        setLoading(true);
        try {
            const params: any = {
                page,
                per_page: pagination.per_page,
            };

            if (type !== 'all') {
                params.type = type;
            }

            if (search) {
                params.search = search;
            }

            const response = await axios.get('/api/admin/users', { params });
            setUsers(response.data.data);
            setPagination({
                current_page: response.data.current_page,
                last_page: response.data.last_page,
                per_page: response.data.per_page,
                total: response.data.total,
            });
        } catch (error: any) {
            toast.error(error.response?.data?.message || 'Failed to fetch users');
            console.error('Error fetching users:', error);
        } finally {
            setLoading(false);
        }
    };

    // Load users when tab or search changes
    useEffect(() => {
        fetchUsers(1, activeTab, searchQuery);
    }, [activeTab]);

    // Handle search with debounce
    useEffect(() => {
        const timer = setTimeout(() => {
            if (searchQuery !== '') {
                fetchUsers(1, activeTab, searchQuery);
            }
        }, 500);

        return () => clearTimeout(timer);
    }, [searchQuery]);

    // Create user
    const handleCreateUser = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);

        try {
            await axios.post('/api/admin/users', formData);
            toast.success('User created successfully');
            setShowCreateModal(false);
            resetForm();
            fetchUsers();
        } catch (error: any) {
            toast.error(error.response?.data?.message || 'Failed to create user');
            console.error('Error creating user:', error);
        } finally {
            setLoading(false);
        }
    };

    // Update user
    const handleUpdateUser = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!selectedUser) return;

        setLoading(true);
        try {
            const updateData = { ...formData };
            // Remove password fields if empty
            if (!updateData.password) {
                delete updateData.password;
                delete updateData.password_confirmation;
            }

            await axios.put(`/api/admin/users/${selectedUser.id}`, updateData);
            toast.success('User updated successfully');
            setShowEditModal(false);
            setSelectedUser(null);
            resetForm();
            fetchUsers();
        } catch (error: any) {
            toast.error(error.response?.data?.message || 'Failed to update user');
            console.error('Error updating user:', error);
        } finally {
            setLoading(false);
        }
    };

    // Delete user
    const handleDeleteUser = async () => {
        if (!selectedUser) return;

        setLoading(true);
        try {
            await axios.delete(`/api/admin/users/${selectedUser.id}`);
            toast.success('User deleted successfully');
            setShowDeleteModal(false);
            setSelectedUser(null);
            fetchUsers();
        } catch (error: any) {
            toast.error(error.response?.data?.message || 'Failed to delete user');
            console.error('Error deleting user:', error);
        } finally {
            setLoading(false);
        }
    };

    // Reset form
    const resetForm = () => {
        setFormData({
            name: '',
            email: '',
            password: '',
            password_confirmation: '',
            notelp: '',
            user_type: 'personal',
            negara: 'Indonesia',
            kota: '',
        });
    };

    // Open edit modal
    const openEditModal = (user: User) => {
        setSelectedUser(user);
        setFormData({
            name: user.name,
            email: user.email,
            password: '',
            password_confirmation: '',
            notelp: user.notelp || '',
            user_type: user.user_type,
            negara: 'Indonesia',
            kota: '',
        });
        setShowEditModal(true);
    };

    // View user detail
    const viewUserDetail = (userId: number) => {
        router.visit(`/admin/users/${userId}/detail`);
    };

    const tabs = [
        { key: 'all', label: 'Semua' },
        { key: 'personal', label: 'Personal' },
        { key: 'instansi', label: 'Instansi' },
        { key: 'admin', label: 'Admin' },
        { key: 'gift', label: 'Gift' },
    ];

    const getUserTypeLabel = (type: string) => {
        const labels: { [key: string]: string } = {
            personal: 'Personal',
            instansi: 'Instansi',
            admin: 'Admin',
            superadmin: 'Super Admin',
            gift: 'Gift',
        };
        return labels[type] || type;
    };

    const getUserTypeColor = (type: string) => {
        const colors: { [key: string]: string } = {
            personal: 'bg-blue-100 text-blue-800',
            instansi: 'bg-purple-100 text-purple-800',
            admin: 'bg-red-100 text-red-800',
            superadmin: 'bg-gray-800 text-white',
            gift: 'bg-green-100 text-green-800',
        };
        return colors[type] || 'bg-gray-100 text-gray-800';
    };

    return (
        <AdminDashboardLayout>
            <Head title="Manajemen Pengguna" />

            <div className="space-y-6 font-poppins">
                {/* Tab Navigation */}
                <div className="flex w-fit flex-wrap gap-4 rounded-[2rem] bg-white p-4 shadow-sm">
                    {tabs.map((tab) => (
                        <button
                            key={tab.key}
                            onClick={() => setActiveTab(tab.key)}
                            className={`rounded-full border px-6 py-2 text-sm font-bold transition-all ${
                                activeTab === tab.key
                                    ? 'border-yellow-400 bg-yellow-400 text-gray-900 shadow-md'
                                    : 'border-yellow-400 bg-white text-gray-900 hover:bg-yellow-50'
                            }`}
                        >
                            {tab.label}
                        </button>
                    ))}
                </div>

                {/* Content Area */}
                <div className="min-h-[600px] rounded-[2.5rem] bg-white p-8 shadow-sm">
                    {/* Header */}
                    <div className="mb-8 flex flex-col items-center justify-between gap-4 md:flex-row">
                        <div className="relative w-full md:w-1/2">
                            <input
                                type="text"
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                                placeholder="Cari pengguna (nama, email, telepon)..."
                                className="w-full rounded-full border-none bg-gray-100 py-3 pr-12 pl-6 text-gray-600 transition-all focus:ring-2 focus:ring-yellow-400"
                            />
                            <HiSearch className="absolute top-1/2 right-4 h-6 w-6 -translate-y-1/2 transform text-gray-400" />
                        </div>

                        <button
                            onClick={() => setShowCreateModal(true)}
                            className="flex transform items-center gap-2 rounded-full bg-yellow-400 px-8 py-3 font-bold text-gray-900 shadow-md transition-all hover:scale-105 hover:bg-yellow-500"
                        >
                            <HiPlus className="h-5 w-5" />
                            Tambah Pengguna
                        </button>
                    </div>

                    {/* Table */}
                    <div className="overflow-x-auto">
                        {loading ? (
                            <div className="flex items-center justify-center py-12">
                                <div className="h-12 w-12 animate-spin rounded-full border-b-2 border-yellow-400"></div>
                            </div>
                        ) : users.length === 0 ? (
                            <div className="py-12 text-center text-gray-500">
                                Tidak ada data pengguna
                            </div>
                        ) : (
                            <>
                                <table className="w-full min-w-[800px]">
                                    <thead>
                                        <tr className="bg-yellow-400 text-gray-900">
                                            <th className="rounded-tl-2xl p-4 text-left font-bold">No</th>
                                            <th className="p-4 text-left font-bold">Nama</th>
                                            <th className="p-4 text-left font-bold">Email</th>
                                            <th className="p-4 text-left font-bold">Telepon</th>
                                            <th className="p-4 text-left font-bold">Tipe</th>
                                            <th className="p-4 text-left font-bold">Token</th>
                                            <th className="rounded-tr-2xl p-4 text-center font-bold">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {users.map((user, index) => (
                                            <tr
                                                key={user.id}
                                                className="border-b border-gray-100 transition-colors hover:bg-gray-50"
                                            >
                                                <td className="p-4 text-gray-600">
                                                    {(pagination.current_page - 1) * pagination.per_page + index + 1}
                                                </td>
                                                <td className="p-4">
                                                    <div className="font-medium text-gray-900">{user.name}</div>
                                                    {user.admin_instansi && (
                                                        <div className="text-xs text-gray-500">
                                                            {user.admin_instansi.nama_instansi}
                                                        </div>
                                                    )}
                                                </td>
                                                <td className="p-4 text-gray-600">{user.email}</td>
                                                <td className="p-4 text-gray-600">{user.notelp || '-'}</td>
                                                <td className="p-4">
                                                    <span
                                                        className={`inline-block rounded-full px-3 py-1 text-xs font-medium ${getUserTypeColor(user.user_type)}`}
                                                    >
                                                        {getUserTypeLabel(user.user_type)}
                                                    </span>
                                                </td>
                                                <td className="p-4 text-gray-600">
                                                    {user.customer?.saldo_token ?? '-'}
                                                </td>
                                                <td className="p-4">
                                                    <div className="flex items-center justify-center gap-2">
                                                        <button
                                                            onClick={() => viewUserDetail(user.id)}
                                                            className="rounded-full bg-blue-500 p-2 text-white transition-all hover:bg-blue-600"
                                                            title="Lihat Detail"
                                                        >
                                                            <HiEye className="h-4 w-4" />
                                                        </button>
                                                        <button
                                                            onClick={() => openEditModal(user)}
                                                            className="rounded-full bg-yellow-500 p-2 text-white transition-all hover:bg-yellow-600"
                                                            title="Edit"
                                                        >
                                                            <HiPencil className="h-4 w-4" />
                                                        </button>
                                                        <button
                                                            onClick={() => {
                                                                setSelectedUser(user);
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
                                        {pagination.total} pengguna
                                    </div>

                                    <div className="flex items-center gap-2">
                                        <button
                                            onClick={() => fetchUsers(pagination.current_page - 1)}
                                            disabled={pagination.current_page === 1}
                                            className="rounded-full bg-gray-200 p-2 text-gray-700 transition-all hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            <HiChevronLeft className="h-5 w-5" />
                                        </button>

                                        <span className="text-sm text-gray-600">
                                            Halaman {pagination.current_page} dari {pagination.last_page}
                                        </span>

                                        <button
                                            onClick={() => fetchUsers(pagination.current_page + 1)}
                                            disabled={pagination.current_page === pagination.last_page}
                                            className="rounded-full bg-gray-200 p-2 text-gray-700 transition-all hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed"
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

            {/* Create User Modal */}
            {showCreateModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
                    <div className="w-full max-w-2xl rounded-3xl bg-white p-8 shadow-xl">
                        <div className="mb-6 flex items-center justify-between">
                            <h2 className="text-2xl font-bold text-gray-900">Tambah Pengguna Baru</h2>
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

                        <form onSubmit={handleCreateUser} className="space-y-4">
                            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label className="mb-2 block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                    <input
                                        type="text"
                                        required
                                        value={formData.name}
                                        onChange={(e) => setFormData({ ...formData, name: e.target.value })}
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
                                    <label className="mb-2 block text-sm font-medium text-gray-700">Nomor Telepon</label>
                                    <input
                                        type="text"
                                        value={formData.notelp}
                                        onChange={(e) => setFormData({ ...formData, notelp: e.target.value })}
                                        className="w-full rounded-full border border-gray-300 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400"
                                    />
                                </div>

                                <div>
                                    <label className="mb-2 block text-sm font-medium text-gray-700">Tipe Pengguna</label>
                                    <select
                                        value={formData.user_type}
                                        onChange={(e) => setFormData({ ...formData, user_type: e.target.value })}
                                        className="w-full rounded-full border border-gray-300 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400"
                                    >
                                        <option value="personal">Personal</option>
                                        <option value="instansi">Instansi</option>
                                        <option value="admin">Admin</option>
                                        <option value="gift">Gift</option>
                                    </select>
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

                                <div>
                                    <label className="mb-2 block text-sm font-medium text-gray-700">
                                        Konfirmasi Password
                                    </label>
                                    <input
                                        type="password"
                                        required
                                        value={formData.password_confirmation}
                                        onChange={(e) =>
                                            setFormData({ ...formData, password_confirmation: e.target.value })
                                        }
                                        className="w-full rounded-full border border-gray-300 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400"
                                    />
                                </div>
                            </div>

                            <div className="flex justify-end gap-3 pt-4">
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

            {/* Edit User Modal */}
            {showEditModal && selectedUser && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
                    <div className="w-full max-w-2xl rounded-3xl bg-white p-8 shadow-xl">
                        <div className="mb-6 flex items-center justify-between">
                            <h2 className="text-2xl font-bold text-gray-900">Edit Pengguna</h2>
                            <button
                                onClick={() => {
                                    setShowEditModal(false);
                                    setSelectedUser(null);
                                    resetForm();
                                }}
                                className="rounded-full p-2 text-gray-500 hover:bg-gray-100"
                            >
                                <HiX className="h-6 w-6" />
                            </button>
                        </div>

                        <form onSubmit={handleUpdateUser} className="space-y-4">
                            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label className="mb-2 block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                    <input
                                        type="text"
                                        required
                                        value={formData.name}
                                        onChange={(e) => setFormData({ ...formData, name: e.target.value })}
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
                                    <label className="mb-2 block text-sm font-medium text-gray-700">Nomor Telepon</label>
                                    <input
                                        type="text"
                                        value={formData.notelp}
                                        onChange={(e) => setFormData({ ...formData, notelp: e.target.value })}
                                        className="w-full rounded-full border border-gray-300 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400"
                                    />
                                </div>

                                <div>
                                    <label className="mb-2 block text-sm font-medium text-gray-700">Tipe Pengguna</label>
                                    <select
                                        value={formData.user_type}
                                        onChange={(e) => setFormData({ ...formData, user_type: e.target.value })}
                                        className="w-full rounded-full border border-gray-300 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400"
                                    >
                                        <option value="personal">Personal</option>
                                        <option value="instansi">Instansi</option>
                                        <option value="admin">Admin</option>
                                        <option value="gift">Gift</option>
                                    </select>
                                </div>

                                <div>
                                    <label className="mb-2 block text-sm font-medium text-gray-700">
                                        Password Baru (kosongkan jika tidak ingin mengubah)
                                    </label>
                                    <input
                                        type="password"
                                        value={formData.password}
                                        onChange={(e) => setFormData({ ...formData, password: e.target.value })}
                                        className="w-full rounded-full border border-gray-300 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400"
                                    />
                                </div>

                                <div>
                                    <label className="mb-2 block text-sm font-medium text-gray-700">
                                        Konfirmasi Password
                                    </label>
                                    <input
                                        type="password"
                                        value={formData.password_confirmation}
                                        onChange={(e) =>
                                            setFormData({ ...formData, password_confirmation: e.target.value })
                                        }
                                        className="w-full rounded-full border border-gray-300 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400"
                                    />
                                </div>
                            </div>

                            <div className="flex justify-end gap-3 pt-4">
                                <button
                                    type="button"
                                    onClick={() => {
                                        setShowEditModal(false);
                                        setSelectedUser(null);
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

            {/* Delete Confirmation Modal */}
            {showDeleteModal && selectedUser && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
                    <div className="w-full max-w-md rounded-3xl bg-white p-8 shadow-xl">
                        <h2 className="mb-4 text-2xl font-bold text-gray-900">Konfirmasi Hapus</h2>
                        <p className="mb-6 text-gray-600">
                            Apakah Anda yakin ingin menghapus pengguna <strong>{selectedUser.name}</strong>? Tindakan
                            ini tidak dapat dibatalkan.
                        </p>

                        <div className="flex justify-end gap-3">
                            <button
                                onClick={() => {
                                    setShowDeleteModal(false);
                                    setSelectedUser(null);
                                }}
                                className="rounded-full border border-gray-300 px-6 py-2 font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Batal
                            </button>
                            <button
                                onClick={handleDeleteUser}
                                disabled={loading}
                                className="rounded-full bg-red-500 px-6 py-2 font-bold text-white hover:bg-red-600 disabled:opacity-50"
                            >
                                {loading ? 'Menghapus...' : 'Hapus'}
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </AdminDashboardLayout>
    );
}
