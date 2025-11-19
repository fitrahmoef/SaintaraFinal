import AdminDashboardLayout from '@/layouts/dashboardLayoutAdmin';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import {
    HiCreditCard,
    HiDownload,
    HiEye,
    HiFilter,
    HiSearch,
    HiCheckCircle,
    HiClock,
    HiXCircle,
} from 'react-icons/hi';

interface Transaction {
    id: number;
    kode_transaksi: string;
    customer: {
        user: {
            nama_lengkap: string;
            email: string;
        };
    };
    package: {
        nama_paket: string;
    };
    jumlah_bayar: number;
    status_pembayaran: string;
    metode_pembayaran: string;
    waktu_dibuat: string;
    waktu_dibayar: string | null;
}

interface TransactionStats {
    total_revenue: number;
    pending_transactions: number;
    paid_transactions: number;
    failed_transactions: number;
}

interface TransactionsProps {
    transactions: {
        data: Transaction[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    stats: TransactionStats;
}

export default function Transactions({ transactions, stats }: TransactionsProps) {
    const [search, setSearch] = useState('');
    const [status, setStatus] = useState('all');
    const [startDate, setStartDate] = useState('');
    const [endDate, setEndDate] = useState('');

    const handleFilter = () => {
        router.get('/admin/transactions', {
            search,
            status,
            start_date: startDate,
            end_date: endDate,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleExport = () => {
        window.location.href = `/api/admin/transactions/export?status=${status}&start_date=${startDate}&end_date=${endDate}`;
    };

    const getStatusBadge = (status: string) => {
        const badges: Record<string, { bg: string; text: string; icon: any }> = {
            dibayar: { bg: 'bg-green-100', text: 'text-green-700', icon: HiCheckCircle },
            pending: { bg: 'bg-yellow-100', text: 'text-yellow-700', icon: HiClock },
            gagal: { bg: 'bg-red-100', text: 'text-red-700', icon: HiXCircle },
            kadaluarsa: { bg: 'bg-gray-100', text: 'text-gray-700', icon: HiXCircle },
        };

        const badge = badges[status] || badges.pending;
        const Icon = badge.icon;

        return (
            <span className={`inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium ${badge.bg} ${badge.text}`}>
                <Icon className="h-3 w-3" />
                {status.charAt(0).toUpperCase() + status.slice(1)}
            </span>
        );
    };

    return (
        <AdminDashboardLayout>
            <Head title="Manajemen Transaksi" />

            <div className="space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-800">Manajemen Transaksi</h1>
                        <p className="text-sm text-gray-500">Kelola semua transaksi pembayaran</p>
                    </div>
                    <button
                        onClick={handleExport}
                        className="flex items-center gap-2 rounded-lg bg-green-500 px-4 py-2 text-white hover:bg-green-600"
                    >
                        <HiDownload className="h-5 w-5" />
                        Export CSV
                    </button>
                </div>

                {/* Stats Cards */}
                <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <div className="rounded-3xl bg-white p-6 shadow-sm">
                        <div className="flex items-center gap-4">
                            <div className="flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                                <HiCreditCard className="h-6 w-6 text-green-600" />
                            </div>
                            <div>
                                <p className="text-xs text-gray-400">Total Revenue</p>
                                <h3 className="text-xl font-bold text-gray-800">
                                    Rp {stats.total_revenue.toLocaleString('id-ID')}
                                </h3>
                            </div>
                        </div>
                    </div>

                    <div className="rounded-3xl bg-white p-6 shadow-sm">
                        <div className="flex items-center gap-4">
                            <div className="flex h-12 w-12 items-center justify-center rounded-full bg-yellow-100">
                                <HiClock className="h-6 w-6 text-yellow-600" />
                            </div>
                            <div>
                                <p className="text-xs text-gray-400">Pending</p>
                                <h3 className="text-xl font-bold text-gray-800">{stats.pending_transactions}</h3>
                            </div>
                        </div>
                    </div>

                    <div className="rounded-3xl bg-white p-6 shadow-sm">
                        <div className="flex items-center gap-4">
                            <div className="flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                                <HiCheckCircle className="h-6 w-6 text-green-600" />
                            </div>
                            <div>
                                <p className="text-xs text-gray-400">Berhasil</p>
                                <h3 className="text-xl font-bold text-gray-800">{stats.paid_transactions}</h3>
                            </div>
                        </div>
                    </div>

                    <div className="rounded-3xl bg-white p-6 shadow-sm">
                        <div className="flex items-center gap-4">
                            <div className="flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                                <HiXCircle className="h-6 w-6 text-red-600" />
                            </div>
                            <div>
                                <p className="text-xs text-gray-400">Gagal</p>
                                <h3 className="text-xl font-bold text-gray-800">{stats.failed_transactions}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Filters */}
                <div className="rounded-3xl bg-white p-6 shadow-sm">
                    <div className="mb-4 flex items-center gap-2 text-sm font-medium text-gray-700">
                        <HiFilter className="h-5 w-5" />
                        Filter Transaksi
                    </div>
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-5">
                        <div className="md:col-span-2">
                            <input
                                type="text"
                                placeholder="Cari kode transaksi, nama, email..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="w-full rounded-lg border border-gray-300 px-4 py-2"
                            />
                        </div>
                        <select
                            value={status}
                            onChange={(e) => setStatus(e.target.value)}
                            className="rounded-lg border border-gray-300 px-4 py-2"
                        >
                            <option value="all">Semua Status</option>
                            <option value="pending">Pending</option>
                            <option value="dibayar">Dibayar</option>
                            <option value="gagal">Gagal</option>
                            <option value="kadaluarsa">Kadaluarsa</option>
                        </select>
                        <input
                            type="date"
                            value={startDate}
                            onChange={(e) => setStartDate(e.target.value)}
                            className="rounded-lg border border-gray-300 px-4 py-2"
                            placeholder="Dari tanggal"
                        />
                        <input
                            type="date"
                            value={endDate}
                            onChange={(e) => setEndDate(e.target.value)}
                            className="rounded-lg border border-gray-300 px-4 py-2"
                            placeholder="Sampai tanggal"
                        />
                    </div>
                    <div className="mt-4">
                        <button
                            onClick={handleFilter}
                            className="flex items-center gap-2 rounded-lg bg-yellow-400 px-6 py-2 text-white hover:bg-yellow-500"
                        >
                            <HiSearch className="h-5 w-5" />
                            Cari
                        </button>
                    </div>
                </div>

                {/* Transactions Table */}
                <div className="rounded-3xl bg-white shadow-sm overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Kode Transaksi
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Customer
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Paket
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Jumlah
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Status
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Tanggal
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-200 bg-white">
                                {transactions.data.map((transaction) => (
                                    <tr key={transaction.id} className="hover:bg-gray-50">
                                        <td className="whitespace-nowrap px-6 py-4">
                                            <span className="font-medium text-gray-900">
                                                {transaction.kode_transaksi}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4">
                                            <div>
                                                <div className="text-sm font-medium text-gray-900">
                                                    {transaction.customer.user.nama_lengkap}
                                                </div>
                                                <div className="text-sm text-gray-500">
                                                    {transaction.customer.user.email}
                                                </div>
                                            </div>
                                        </td>
                                        <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                            {transaction.package.nama_paket}
                                        </td>
                                        <td className="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                            Rp {transaction.jumlah_bayar.toLocaleString('id-ID')}
                                        </td>
                                        <td className="whitespace-nowrap px-6 py-4">
                                            {getStatusBadge(transaction.status_pembayaran)}
                                        </td>
                                        <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                            {new Date(transaction.waktu_dibuat).toLocaleDateString('id-ID')}
                                        </td>
                                        <td className="whitespace-nowrap px-6 py-4 text-sm">
                                            <button
                                                onClick={() => router.get(`/admin/transactions/${transaction.id}`)}
                                                className="text-yellow-600 hover:text-yellow-900"
                                            >
                                                <HiEye className="h-5 w-5" />
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {/* Pagination */}
                    {transactions.last_page > 1 && (
                        <div className="border-t border-gray-200 bg-white px-6 py-4">
                            <div className="flex items-center justify-between">
                                <div className="text-sm text-gray-700">
                                    Menampilkan <span className="font-medium">{transactions.data.length}</span> dari{' '}
                                    <span className="font-medium">{transactions.total}</span> transaksi
                                </div>
                                <div className="flex gap-2">
                                    {/* Add pagination buttons here */}
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AdminDashboardLayout>
    );
}
