import AdminDashboardLayout from '@/layouts/dashboardLayoutAdmin';
import { Head, router } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import {
    HiArrowLeft,
    HiOfficeBuilding,
    HiMail,
    HiPhone,
    HiLocationMarker,
    HiCalendar,
    HiUsers,
    HiDocumentText,
    HiCurrencyDollar,
    HiClock,
} from 'react-icons/hi';
import axios from 'axios';
import { toast } from 'react-hot-toast';
import { Doughnut, Line } from 'react-chartjs-2';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    ArcElement,
    Title,
    Tooltip,
    Legend,
    Filler,
} from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, ArcElement, Title, Tooltip, Legend, Filler);

interface InstitutionDetailProps {
    institutionId: number;
}

export default function InstitutionDetail({ institutionId }: InstitutionDetailProps) {
    const [institution, setInstitution] = useState<any>(null);
    const [statistics, setStatistics] = useState<any>(null);
    const [employees, setEmployees] = useState<any[]>([]);
    const [recentTests, setRecentTests] = useState<any[]>([]);
    const [recentTransactions, setRecentTransactions] = useState<any[]>([]);
    const [monthlyTestTrend, setMonthlyTestTrend] = useState<any[]>([]);
    const [characterDistribution, setCharacterDistribution] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);
    const [activeTab, setActiveTab] = useState<'overview' | 'employees' | 'tests' | 'transactions'>('overview');

    useEffect(() => {
        fetchInstitutionDetail();
        fetchEmployees();
    }, [institutionId]);

    const fetchInstitutionDetail = async () => {
        setLoading(true);
        try {
            const response = await axios.get(`/api/admin/institutions/${institutionId}`);
            setInstitution(response.data.institution);
            setStatistics(response.data.statistics);
            setRecentTests(response.data.recent_tests || []);
            setRecentTransactions(response.data.recent_transactions || []);
            setMonthlyTestTrend(response.data.monthly_test_trend || []);
            setCharacterDistribution(response.data.character_distribution || []);
        } catch (error: any) {
            toast.error(error.response?.data?.message || 'Failed to fetch institution detail');
            console.error('Error fetching institution detail:', error);
        } finally {
            setLoading(false);
        }
    };

    const fetchEmployees = async () => {
        try {
            const response = await axios.get(`/api/admin/institutions/${institutionId}/employees`);
            setEmployees(response.data.employees || []);
        } catch (error: any) {
            console.error('Error fetching employees:', error);
        }
    };

    // Chart data for monthly tests
    const monthlyTestChartData = {
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

    // Chart data for character distribution
    const characterChartData = {
        labels: characterDistribution.map((item: any) => item.character_type?.nama_karakter || 'Unknown'),
        datasets: [
            {
                data: characterDistribution.map((item: any) => item.count),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 159, 64, 0.8)',
                ],
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

    const doughnutOptions = {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom' as const,
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

    if (!institution) {
        return (
            <AdminDashboardLayout>
                <div className="flex h-screen items-center justify-center">
                    <p className="text-gray-500">Instansi tidak ditemukan</p>
                </div>
            </AdminDashboardLayout>
        );
    }

    const getStatusColor = (status: string) => {
        const colors: { [key: string]: string } = {
            aktif: 'bg-green-100 text-green-800',
            tidak_aktif: 'bg-red-100 text-red-800',
            pending: 'bg-yellow-100 text-yellow-800',
        };
        return colors[status] || 'bg-gray-100 text-gray-800';
    };

    return (
        <AdminDashboardLayout>
            <Head title={`Detail Instansi - ${institution.nama_instansi}`} />

            <div className="space-y-6 font-poppins">
                {/* Back Button */}
                <button
                    onClick={() => router.visit('/admin/institutions')}
                    className="flex items-center gap-2 text-gray-600 transition-colors hover:text-gray-900"
                >
                    <HiArrowLeft className="h-5 w-5" />
                    <span>Kembali ke Daftar Instansi</span>
                </button>

                {/* Institution Profile Card */}
                <div className="rounded-3xl bg-gradient-to-r from-purple-400 to-blue-400 p-8 text-white shadow-lg">
                    <div className="flex flex-col items-start justify-between gap-6 md:flex-row md:items-center">
                        <div className="flex items-center gap-6">
                            <div className="flex h-20 w-20 items-center justify-center rounded-full bg-white bg-opacity-20 text-4xl font-bold">
                                <HiOfficeBuilding className="h-12 w-12" />
                            </div>
                            <div>
                                <h1 className="text-3xl font-bold">{institution.nama_instansi}</h1>
                                <div className="mt-2 flex flex-wrap gap-4 text-sm">
                                    <span className="flex items-center gap-1">
                                        <HiMail className="h-4 w-4" />
                                        {institution.email_instansi}
                                    </span>
                                    {institution.nomor_telepon && (
                                        <span className="flex items-center gap-1">
                                            <HiPhone className="h-4 w-4" />
                                            {institution.nomor_telepon}
                                        </span>
                                    )}
                                    {institution.kota_instansi && (
                                        <span className="flex items-center gap-1">
                                            <HiLocationMarker className="h-4 w-4" />
                                            {institution.kota_instansi}
                                        </span>
                                    )}
                                </div>
                                <div className="mt-2 flex items-center gap-4">
                                    <span className="flex items-center gap-1">
                                        <HiCalendar className="h-4 w-4" />
                                        Bergabung: {new Date(institution.tanggal_bergabung).toLocaleDateString('id-ID')}
                                    </span>
                                    {institution.tanggal_berakhir && (
                                        <span className="flex items-center gap-1">
                                            <HiClock className="h-4 w-4" />
                                            Berakhir: {new Date(institution.tanggal_berakhir).toLocaleDateString('id-ID')}
                                        </span>
                                    )}
                                </div>
                            </div>
                        </div>

                        <div className="text-right">
                            <div className="mb-2 text-sm opacity-90">Status</div>
                            <div
                                className={`inline-block rounded-full px-4 py-2 text-base font-bold ${getStatusColor(institution.status_akun)}`}
                            >
                                {institution.status_akun.toUpperCase()}
                            </div>
                            <div className="mt-4 text-sm opacity-90">Admin</div>
                            <div className="text-lg font-bold">{institution.nama_admin}</div>
                        </div>
                    </div>
                </div>

                {/* Statistics Cards */}
                {statistics && (
                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                        <div className="rounded-2xl bg-white p-6 shadow-sm">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-sm text-gray-600">Total Karyawan</p>
                                    <p className="mt-2 text-3xl font-bold text-gray-900">{statistics.total_employees}</p>
                                </div>
                                <div className="rounded-full bg-purple-100 p-4">
                                    <HiUsers className="h-8 w-8 text-purple-600" />
                                </div>
                            </div>
                        </div>

                        <div className="rounded-2xl bg-white p-6 shadow-sm">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-sm text-gray-600">Total Test</p>
                                    <p className="mt-2 text-3xl font-bold text-gray-900">{statistics.total_tests_completed}</p>
                                </div>
                                <div className="rounded-full bg-blue-100 p-4">
                                    <HiDocumentText className="h-8 w-8 text-blue-600" />
                                </div>
                            </div>
                        </div>

                        <div className="rounded-2xl bg-white p-6 shadow-sm">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-sm text-gray-600">Saldo Token</p>
                                    <p className="mt-2 text-3xl font-bold text-gray-900">{statistics.current_token_balance}</p>
                                </div>
                                <div className="rounded-full bg-green-100 p-4">
                                    <HiCurrencyDollar className="h-8 w-8 text-green-600" />
                                </div>
                            </div>
                        </div>

                        <div className="rounded-2xl bg-white p-6 shadow-sm">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-sm text-gray-600">Total Pendapatan</p>
                                    <p className="mt-2 text-2xl font-bold text-gray-900">
                                        Rp {statistics.total_revenue?.toLocaleString('id-ID') || 0}
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
                        onClick={() => setActiveTab('employees')}
                        className={`rounded-full px-6 py-2 font-medium transition ${
                            activeTab === 'employees'
                                ? 'bg-yellow-400 text-gray-900'
                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                        }`}
                    >
                        Karyawan ({employees.length})
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
                </div>

                {/* Tab Content */}
                <div className="rounded-3xl bg-white p-8 shadow-sm">
                    {activeTab === 'overview' && (
                        <div className="space-y-8">
                            <div className="grid grid-cols-1 gap-8 lg:grid-cols-2">
                                {/* Monthly Test Trend */}
                                <div>
                                    <h3 className="mb-4 text-xl font-bold text-gray-900">Tren Test 6 Bulan Terakhir</h3>
                                    <div className="h-64">
                                        <Line data={monthlyTestChartData} options={chartOptions} />
                                    </div>
                                </div>

                                {/* Character Distribution */}
                                <div>
                                    <h3 className="mb-4 text-xl font-bold text-gray-900">Distribusi Tipe Karakter</h3>
                                    <div className="h-64">
                                        <Doughnut data={characterChartData} options={doughnutOptions} />
                                    </div>
                                </div>
                            </div>

                            {/* Recent Tests */}
                            <div>
                                <h3 className="mb-4 text-xl font-bold text-gray-900">Test Terbaru</h3>
                                {recentTests.length === 0 ? (
                                    <p className="text-gray-500">Belum ada test yang diambil</p>
                                ) : (
                                    <div className="space-y-3">
                                        {recentTests.slice(0, 5).map((test: any) => (
                                            <div
                                                key={test.id}
                                                className="flex items-center justify-between rounded-xl border border-gray-200 p-4"
                                            >
                                                <div>
                                                    <p className="font-medium text-gray-900">
                                                        {test.user?.name} - {test.test?.judul || 'Test'}
                                                    </p>
                                                    <p className="text-sm text-gray-500">
                                                        {test.character_type?.nama_karakter || 'Belum selesai'}
                                                    </p>
                                                </div>
                                                <div className="text-right text-sm text-gray-500">
                                                    {new Date(test.created_at).toLocaleDateString('id-ID')}
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </div>
                        </div>
                    )}

                    {activeTab === 'employees' && (
                        <div>
                            <h3 className="mb-4 text-xl font-bold text-gray-900">Daftar Karyawan</h3>
                            {employees.length === 0 ? (
                                <p className="text-gray-500">Belum ada karyawan</p>
                            ) : (
                                <div className="overflow-x-auto">
                                    <table className="w-full">
                                        <thead>
                                            <tr className="border-b border-gray-200 bg-gray-50">
                                                <th className="p-4 text-left text-sm font-medium text-gray-700">Nama</th>
                                                <th className="p-4 text-left text-sm font-medium text-gray-700">Email</th>
                                                <th className="p-4 text-left text-sm font-medium text-gray-700">Telepon</th>
                                                <th className="p-4 text-left text-sm font-medium text-gray-700">Token</th>
                                                <th className="p-4 text-left text-sm font-medium text-gray-700">Test Selesai</th>
                                                <th className="p-4 text-left text-sm font-medium text-gray-700">Bergabung</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {employees.map((employee: any) => (
                                                <tr key={employee.id} className="border-b border-gray-100">
                                                    <td className="p-4">{employee.name}</td>
                                                    <td className="p-4 text-gray-600">{employee.email}</td>
                                                    <td className="p-4 text-gray-600">{employee.notelp || '-'}</td>
                                                    <td className="p-4 text-gray-600">{employee.token_balance}</td>
                                                    <td className="p-4 text-gray-600">{employee.tests_completed}</td>
                                                    <td className="p-4 text-gray-600">
                                                        {new Date(employee.created_at).toLocaleDateString('id-ID')}
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            )}
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
                                                <th className="p-4 text-left text-sm font-medium text-gray-700">Karyawan</th>
                                                <th className="p-4 text-left text-sm font-medium text-gray-700">Test</th>
                                                <th className="p-4 text-left text-sm font-medium text-gray-700">
                                                    Hasil Karakter
                                                </th>
                                                <th className="p-4 text-left text-sm font-medium text-gray-700">Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {recentTests.map((test: any) => (
                                                <tr key={test.id} className="border-b border-gray-100">
                                                    <td className="p-4">{test.user?.name}</td>
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
                                                <th className="p-4 text-left text-sm font-medium text-gray-700">Order ID</th>
                                                <th className="p-4 text-left text-sm font-medium text-gray-700">User</th>
                                                <th className="p-4 text-left text-sm font-medium text-gray-700">Jumlah</th>
                                                <th className="p-4 text-left text-sm font-medium text-gray-700">Status</th>
                                                <th className="p-4 text-left text-sm font-medium text-gray-700">Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {recentTransactions.map((transaction: any) => (
                                                <tr key={transaction.id} className="border-b border-gray-100">
                                                    <td className="p-4 font-mono text-sm">{transaction.order_id}</td>
                                                    <td className="p-4">{transaction.user?.name}</td>
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
                </div>
            </div>
        </AdminDashboardLayout>
    );
}
