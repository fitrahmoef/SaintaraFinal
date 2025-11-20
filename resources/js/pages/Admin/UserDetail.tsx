import AdminDashboardLayout from '@/layouts/dashboardLayoutAdmin';
import { Head, router } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import {
    HiArrowLeft,
    HiUser,
    HiMail,
    HiPhone,
    HiCalendar,
    HiCurrencyDollar,
    HiDocumentText,
    HiChartBar,
    HiClock,
    HiPlus,
    HiMinus,
} from 'react-icons/hi';
import axios from 'axios';
import { toast } from 'react-hot-toast';
import { Line } from 'react-chartjs-2';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    Filler,
} from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend, Filler);

interface UserDetailProps {
    userId: number;
}

export default function UserDetail({ userId }: UserDetailProps) {
    const [user, setUser] = useState<any>(null);
    const [statistics, setStatistics] = useState<any>(null);
    const [recentTests, setRecentTests] = useState<any[]>([]);
    const [recentTransactions, setRecentTransactions] = useState<any[]>([]);
    const [activityTimeline, setActivityTimeline] = useState<any[]>([]);
    const [monthlyTestTrend, setMonthlyTestTrend] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);
    const [activeTab, setActiveTab] = useState<'overview' | 'tests' | 'transactions' | 'activity'>('overview');

    // Token management
    const [showTokenModal, setShowTokenModal] = useState(false);
    const [tokenAction, setTokenAction] = useState<'add' | 'deduct'>('add');
    const [tokenAmount, setTokenAmount] = useState(0);
    const [tokenReason, setTokenReason] = useState('');

    useEffect(() => {
        fetchUserDetail();
        fetchActivityTimeline();
    }, [userId]);

    const fetchUserDetail = async () => {
        setLoading(true);
        try {
            const response = await axios.get(`/api/admin/users/${userId}/monitor`);
            setUser(response.data.user);
            setStatistics(response.data.statistics);
            setRecentTests(response.data.recent_tests || []);
            setRecentTransactions(response.data.recent_transactions || []);
            setMonthlyTestTrend(response.data.monthly_test_trend || []);
        } catch (error: any) {
            toast.error(error.response?.data?.message || 'Failed to fetch user detail');
            console.error('Error fetching user detail:', error);
        } finally {
            setLoading(false);
        }
    };

    const fetchActivityTimeline = async () => {
        try {
            const response = await axios.get(`/api/admin/users/${userId}/activity-timeline`);
            setActivityTimeline(response.data.data || []);
        } catch (error: any) {
            console.error('Error fetching activity timeline:', error);
        }
    };

    const handleTokenManagement = async () => {
        if (tokenAmount <= 0) {
            toast.error('Jumlah token harus lebih dari 0');
            return;
        }

        try {
            const endpoint = tokenAction === 'add' ? 'add' : 'deduct';
            await axios.post(`/api/admin/users/${userId}/tokens/${endpoint}`, {
                amount: tokenAmount,
                reason: tokenReason,
            });

            toast.success(
                tokenAction === 'add' ? 'Token berhasil ditambahkan' : 'Token berhasil dikurangi'
            );
            setShowTokenModal(false);
            setTokenAmount(0);
            setTokenReason('');
            fetchUserDetail();
        } catch (error: any) {
            toast.error(error.response?.data?.message || 'Gagal memproses token');
        }
    };

    // Chart data
    const chartData = {
        labels: monthlyTestTrend.map((item: any) => item.month),
        datasets: [
            {
                label: 'Jumlah Test',
                data: monthlyTestTrend.map((item: any) => item.count),
                borderColor: 'rgb(250, 204, 21)',
                backgroundColor: 'rgba(250, 204, 21, 0.1)',
                fill: true,
                tension: 0.4,
            },
        ],
    };

    const chartOptions = {
        responsive: true,
        plugins: {
            legend: {
                display: false,
            },
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                },
            },
        },
    };

    if (loading) {
        return (
            <AdminDashboardLayout>
                <div className="flex h-screen items-center justify-center">
                    <div className="h-12 w-12 animate-spin rounded-full border-b-2 border-yellow-400"></div>
                </div>
            </AdminDashboardLayout>
        );
    }

    if (!user) {
        return (
            <AdminDashboardLayout>
                <div className="flex h-screen items-center justify-center">
                    <p className="text-gray-500">User tidak ditemukan</p>
                </div>
            </AdminDashboardLayout>
        );
    }

    return (
        <AdminDashboardLayout>
            <Head title={`Detail User - ${user.name}`} />

            <div className="space-y-6 font-poppins">
                {/* Back Button */}
                <button
                    onClick={() => router.visit('/admin/users')}
                    className="flex items-center gap-2 text-gray-600 transition-colors hover:text-gray-900"
                >
                    <HiArrowLeft className="h-5 w-5" />
                    <span>Kembali ke Daftar User</span>
                </button>

                {/* User Profile Card */}
                <div className="rounded-3xl bg-gradient-to-r from-yellow-400 to-orange-400 p-8 text-white shadow-lg">
                    <div className="flex flex-col items-start justify-between gap-6 md:flex-row md:items-center">
                        <div className="flex items-center gap-6">
                            <div className="flex h-20 w-20 items-center justify-center rounded-full bg-white bg-opacity-20 text-4xl font-bold">
                                {user.name.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <h1 className="text-3xl font-bold">{user.name}</h1>
                                <div className="mt-2 flex flex-wrap gap-4 text-sm">
                                    <span className="flex items-center gap-1">
                                        <HiMail className="h-4 w-4" />
                                        {user.email}
                                    </span>
                                    {user.notelp && (
                                        <span className="flex items-center gap-1">
                                            <HiPhone className="h-4 w-4" />
                                            {user.notelp}
                                        </span>
                                    )}
                                    <span className="flex items-center gap-1">
                                        <HiCalendar className="h-4 w-4" />
                                        Bergabung: {new Date(user.created_at).toLocaleDateString('id-ID')}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div className="text-right">
                            <div className="mb-2 text-sm opacity-90">Saldo Token</div>
                            <div className="text-5xl font-bold">{user.customer?.saldo_token || 0}</div>
                            <div className="mt-2 flex gap-2">
                                <button
                                    onClick={() => {
                                        setTokenAction('add');
                                        setShowTokenModal(true);
                                    }}
                                    className="rounded-full bg-white bg-opacity-20 px-4 py-2 text-sm font-medium transition hover:bg-opacity-30"
                                >
                                    <HiPlus className="inline h-4 w-4" /> Tambah
                                </button>
                                <button
                                    onClick={() => {
                                        setTokenAction('deduct');
                                        setShowTokenModal(true);
                                    }}
                                    className="rounded-full bg-white bg-opacity-20 px-4 py-2 text-sm font-medium transition hover:bg-opacity-30"
                                >
                                    <HiMinus className="inline h-4 w-4" /> Kurangi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Statistics Cards */}
                {statistics && (
                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                        <div className="rounded-2xl bg-white p-6 shadow-sm">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-sm text-gray-600">Total Test</p>
                                    <p className="mt-2 text-3xl font-bold text-gray-900">{statistics.total_tests}</p>
                                </div>
                                <div className="rounded-full bg-blue-100 p-4">
                                    <HiDocumentText className="h-8 w-8 text-blue-600" />
                                </div>
                            </div>
                        </div>

                        <div className="rounded-2xl bg-white p-6 shadow-sm">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-sm text-gray-600">Token Digunakan</p>
                                    <p className="mt-2 text-3xl font-bold text-gray-900">{statistics.total_tokens_used}</p>
                                </div>
                                <div className="rounded-full bg-green-100 p-4">
                                    <HiCurrencyDollar className="h-8 w-8 text-green-600" />
                                </div>
                            </div>
                        </div>

                        <div className="rounded-2xl bg-white p-6 shadow-sm">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-sm text-gray-600">Total Transaksi</p>
                                    <p className="mt-2 text-3xl font-bold text-gray-900">{statistics.total_transactions}</p>
                                </div>
                                <div className="rounded-full bg-purple-100 p-4">
                                    <HiChartBar className="h-8 w-8 text-purple-600" />
                                </div>
                            </div>
                        </div>

                        <div className="rounded-2xl bg-white p-6 shadow-sm">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-sm text-gray-600">Total Pengeluaran</p>
                                    <p className="mt-2 text-2xl font-bold text-gray-900">
                                        Rp {statistics.total_spent?.toLocaleString('id-ID') || 0}
                                    </p>
                                </div>
                                <div className="rounded-full bg-yellow-100 p-4">
                                    <HiCurrencyDollar className="h-8 w-8 text-yellow-600" />
                                </div>
                            </div>
                        </div>
                    </div>
                )}

                {/* Tabs */}
                <div className="flex gap-4 rounded-2xl bg-white p-4 shadow-sm">
                    <button
                        onClick={() => setActiveTab('overview')}
                        className={`rounded-full px-6 py-2 font-medium transition ${
                            activeTab === 'overview'
                                ? 'bg-yellow-400 text-gray-900'
                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                        }`}
                    >
                        Overview
                    </button>
                    <button
                        onClick={() => setActiveTab('tests')}
                        className={`rounded-full px-6 py-2 font-medium transition ${
                            activeTab === 'tests'
                                ? 'bg-yellow-400 text-gray-900'
                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                        }`}
                    >
                        Riwayat Test
                    </button>
                    <button
                        onClick={() => setActiveTab('transactions')}
                        className={`rounded-full px-6 py-2 font-medium transition ${
                            activeTab === 'transactions'
                                ? 'bg-yellow-400 text-gray-900'
                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                        }`}
                    >
                        Transaksi
                    </button>
                    <button
                        onClick={() => setActiveTab('activity')}
                        className={`rounded-full px-6 py-2 font-medium transition ${
                            activeTab === 'activity'
                                ? 'bg-yellow-400 text-gray-900'
                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                        }`}
                    >
                        Activity Log
                    </button>
                </div>

                {/* Tab Content */}
                <div className="rounded-3xl bg-white p-8 shadow-sm">
                    {activeTab === 'overview' && (
                        <div className="space-y-8">
                            {/* Chart */}
                            <div>
                                <h3 className="mb-4 text-xl font-bold text-gray-900">Tren Test 6 Bulan Terakhir</h3>
                                <div className="h-64">
                                    <Line data={chartData} options={chartOptions} />
                                </div>
                            </div>

                            {/* Recent Tests */}
                            <div>
                                <h3 className="mb-4 text-xl font-bold text-gray-900">Test Terbaru</h3>
                                {recentTests.length === 0 ? (
                                    <p className="text-gray-500">Belum ada test yang diambil</p>
                                ) : (
                                    <div className="space-y-3">
                                        {recentTests.map((test: any) => (
                                            <div
                                                key={test.id}
                                                className="flex items-center justify-between rounded-xl border border-gray-200 p-4"
                                            >
                                                <div>
                                                    <p className="font-medium text-gray-900">{test.test?.judul || 'Test'}</p>
                                                    <p className="text-sm text-gray-500">
                                                        {test.character_type?.nama_karakter || 'Belum selesai'}
                                                    </p>
                                                </div>
                                                <div className="text-right text-sm text-gray-500">
                                                    <HiClock className="inline h-4 w-4" />{' '}
                                                    {new Date(test.created_at).toLocaleDateString('id-ID')}
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </div>
                        </div>
                    )}

                    {activeTab === 'tests' && (
                        <div>
                            <h3 className="mb-4 text-xl font-bold text-gray-900">Riwayat Test Lengkap</h3>
                            {recentTests.length === 0 ? (
                                <p className="text-gray-500">Belum ada test yang diambil</p>
                            ) : (
                                <div className="overflow-x-auto">
                                    <table className="w-full">
                                        <thead>
                                            <tr className="border-b border-gray-200 bg-gray-50">
                                                <th className="p-4 text-left text-sm font-medium text-gray-700">Test</th>
                                                <th className="p-4 text-left text-sm font-medium text-gray-700">Hasil Karakter</th>
                                                <th className="p-4 text-left text-sm font-medium text-gray-700">Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {recentTests.map((test: any) => (
                                                <tr key={test.id} className="border-b border-gray-100">
                                                    <td className="p-4">{test.test?.judul || 'Test'}</td>
                                                    <td className="p-4">
                                                        {test.character_type?.nama_karakter || 'Belum selesai'}
                                                    </td>
                                                    <td className="p-4 text-gray-600">
                                                        {new Date(test.created_at).toLocaleString('id-ID')}
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            )}
                        </div>
                    )}

                    {activeTab === 'transactions' && (
                        <div>
                            <h3 className="mb-4 text-xl font-bold text-gray-900">Riwayat Transaksi</h3>
                            {recentTransactions.length === 0 ? (
                                <p className="text-gray-500">Belum ada transaksi</p>
                            ) : (
                                <div className="overflow-x-auto">
                                    <table className="w-full">
                                        <thead>
                                            <tr className="border-b border-gray-200 bg-gray-50">
                                                <th className="p-4 text-left text-sm font-medium text-gray-700">
                                                    Order ID
                                                </th>
                                                <th className="p-4 text-left text-sm font-medium text-gray-700">Jumlah</th>
                                                <th className="p-4 text-left text-sm font-medium text-gray-700">Status</th>
                                                <th className="p-4 text-left text-sm font-medium text-gray-700">Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {recentTransactions.map((transaction: any) => (
                                                <tr key={transaction.id} className="border-b border-gray-100">
                                                    <td className="p-4 font-mono text-sm">{transaction.order_id}</td>
                                                    <td className="p-4">
                                                        Rp {transaction.amount?.toLocaleString('id-ID') || 0}
                                                    </td>
                                                    <td className="p-4">
                                                        <span
                                                            className={`inline-block rounded-full px-3 py-1 text-xs font-medium ${
                                                                transaction.status === 'paid'
                                                                    ? 'bg-green-100 text-green-800'
                                                                    : transaction.status === 'pending'
                                                                      ? 'bg-yellow-100 text-yellow-800'
                                                                      : 'bg-red-100 text-red-800'
                                                            }`}
                                                        >
                                                            {transaction.status}
                                                        </span>
                                                    </td>
                                                    <td className="p-4 text-gray-600">
                                                        {new Date(transaction.created_at).toLocaleString('id-ID')}
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            )}
                        </div>
                    )}

                    {activeTab === 'activity' && (
                        <div>
                            <h3 className="mb-4 text-xl font-bold text-gray-900">Activity Timeline</h3>
                            {activityTimeline.length === 0 ? (
                                <p className="text-gray-500">Belum ada aktivitas</p>
                            ) : (
                                <div className="space-y-4">
                                    {activityTimeline.map((activity: any, index: number) => (
                                        <div key={index} className="flex gap-4">
                                            <div className="flex flex-col items-center">
                                                <div className="rounded-full bg-yellow-400 p-2">
                                                    <HiClock className="h-4 w-4 text-gray-900" />
                                                </div>
                                                {index < activityTimeline.length - 1 && (
                                                    <div className="h-full w-px bg-gray-200"></div>
                                                )}
                                            </div>
                                            <div className="flex-1 rounded-xl border border-gray-200 p-4">
                                                <div className="flex items-start justify-between">
                                                    <div>
                                                        <p className="font-medium text-gray-900">{activity.action}</p>
                                                        <p className="mt-1 text-sm text-gray-600">{activity.description}</p>
                                                    </div>
                                                    <span className="text-sm text-gray-500">
                                                        {new Date(activity.timestamp).toLocaleString('id-ID')}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    )}
                </div>
            </div>

            {/* Token Management Modal */}
            {showTokenModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
                    <div className="w-full max-w-md rounded-3xl bg-white p-8 shadow-xl">
                        <h2 className="mb-4 text-2xl font-bold text-gray-900">
                            {tokenAction === 'add' ? 'Tambah Token' : 'Kurangi Token'}
                        </h2>

                        <div className="space-y-4">
                            <div>
                                <label className="mb-2 block text-sm font-medium text-gray-700">Jumlah Token</label>
                                <input
                                    type="number"
                                    min="1"
                                    value={tokenAmount}
                                    onChange={(e) => setTokenAmount(parseInt(e.target.value) || 0)}
                                    className="w-full rounded-full border border-gray-300 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400"
                                />
                            </div>

                            <div>
                                <label className="mb-2 block text-sm font-medium text-gray-700">
                                    Alasan (opsional)
                                </label>
                                <textarea
                                    value={tokenReason}
                                    onChange={(e) => setTokenReason(e.target.value)}
                                    rows={3}
                                    className="w-full rounded-2xl border border-gray-300 px-4 py-2 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400"
                                />
                            </div>
                        </div>

                        <div className="mt-6 flex justify-end gap-3">
                            <button
                                onClick={() => {
                                    setShowTokenModal(false);
                                    setTokenAmount(0);
                                    setTokenReason('');
                                }}
                                className="rounded-full border border-gray-300 px-6 py-2 font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Batal
                            </button>
                            <button
                                onClick={handleTokenManagement}
                                className={`rounded-full px-6 py-2 font-bold text-white ${
                                    tokenAction === 'add' ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600'
                                }`}
                            >
                                {tokenAction === 'add' ? 'Tambah' : 'Kurangi'}
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </AdminDashboardLayout>
    );
}
