import AdminDashboardLayout from '@/layouts/dashboardLayoutAdmin'; // Pastikan path layout benar
import { Head } from '@inertiajs/react';
import { useState } from 'react';
import { HiPlus, HiSearch } from 'react-icons/hi';

export default function Finance() {
    const [activeTab, setActiveTab] = useState('Pemasukan');

    // ==========================================
    // 1. RENDER KONTEN: PEMASUKAN
    // ==========================================
    const renderPemasukan = () => (
        <div className="animate-fade-in space-y-6">
            {/* Summary Cards */}
            <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div className="rounded-2xl border border-green-200 bg-green-50 p-6">
                    <p className="text-sm font-medium text-green-600">
                        Total Pemasukan:
                    </p>
                    <h3 className="mt-1 text-3xl font-bold text-green-600">
                        Rp125.600.000
                    </h3>
                </div>
                <div className="rounded-2xl border border-blue-200 bg-blue-50 p-6">
                    <p className="text-sm font-medium text-blue-600">
                        Transaksi Bulan ini:
                    </p>
                    <h3 className="mt-1 text-3xl font-bold text-blue-600">
                        320
                    </h3>
                </div>
            </div>

            {/* Filters */}
            <div className="flex flex-col items-center gap-4 rounded-2xl bg-gray-50 p-4 md:flex-row">
                <div className="relative w-full md:w-1/2">
                    <input
                        type="text"
                        placeholder="Cari..."
                        className="w-full rounded-full border-none bg-gray-100 py-2 pr-4 pl-10 focus:ring-2 focus:ring-yellow-400"
                    />
                    <HiSearch className="absolute top-1/2 left-4 h-5 w-5 -translate-y-1/2 text-gray-400" />
                </div>
                <div className="flex w-full items-center gap-2 md:w-auto">
                    <span className="font-bold whitespace-nowrap text-gray-700">
                        Filter Tanggal
                    </span>
                    <input
                        type="date"
                        className="w-full rounded-lg border-none bg-gray-200 px-4 py-2 text-gray-600"
                    />
                    <input
                        type="date"
                        className="w-full rounded-lg border-none bg-gray-200 px-4 py-2 text-gray-600"
                    />
                </div>
            </div>

            {/* Table */}
            <div className="overflow-x-auto">
                <table className="w-full min-w-[800px]">
                    <thead>
                        <tr className="bg-yellow-400 text-gray-900">
                            <th className="rounded-l-xl px-6 py-4 text-left font-bold">
                                ID TRANSAKSI
                            </th>
                            <th className="px-6 py-4 text-left font-bold">
                                TANGGAL
                            </th>
                            <th className="px-6 py-4 text-left font-bold">
                                SUMBER DANA
                            </th>
                            <th className="px-6 py-4 text-left font-bold">
                                KETERANGAN
                            </th>
                            <th className="px-6 py-4 text-left font-bold">
                                JUMLAH
                            </th>
                            <th className="rounded-r-xl px-6 py-4 text-center font-bold">
                                AKSI
                            </th>
                        </tr>
                    </thead>
                    <tbody className="bg-white font-semibold text-gray-800">
                        <tr className="border-b border-gray-100">
                            <td className="px-6 py-5">INV251005001</td>
                            <td className="px-6 py-5">5 Oktober 2025</td>
                            <td className="px-6 py-5">QRIS</td>
                            <td className="px-6 py-5">
                                Pembelian token personal
                            </td>
                            <td className="px-6 py-5">Rp150.000</td>
                            <td className="px-6 py-5 text-center">
                                <button className="font-bold text-gray-900 hover:text-yellow-600">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    );

    // ==========================================
    // 2. RENDER KONTEN: PENGELUARAN
    // ==========================================
    const renderPengeluaran = () => (
        <div className="animate-fade-in space-y-6">
            {/* Summary Cards */}
            <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div className="rounded-2xl border border-red-200 bg-red-50 p-6">
                    <p className="text-sm font-medium text-red-600">
                        Total Pengeluaran:
                    </p>
                    <h3 className="mt-1 text-3xl font-bold text-red-600">
                        Rp30.540.000
                    </h3>
                </div>
                <div className="rounded-2xl border border-green-200 bg-green-50 p-6">
                    <p className="text-sm font-medium text-green-600">
                        Saldo Akhir:
                    </p>
                    <h3 className="mt-1 text-3xl font-bold text-green-600">
                        Rp95.060.000
                    </h3>
                </div>
            </div>

            {/* Actions */}
            <div className="flex flex-col items-center justify-between gap-4 rounded-2xl bg-gray-50 p-2 md:flex-row">
                <div className="relative ml-2 w-full md:w-1/2">
                    <input
                        type="text"
                        placeholder="Cari..."
                        className="w-full rounded-full border-none bg-gray-100 py-3 pr-4 pl-10 focus:ring-2 focus:ring-yellow-400"
                    />
                    <HiSearch className="absolute top-1/2 left-4 h-5 w-5 -translate-y-1/2 text-gray-400" />
                </div>
                <button className="flex items-center gap-2 rounded-full bg-yellow-400 px-6 py-3 font-bold text-gray-900 shadow-md transition-all hover:bg-yellow-500">
                    <HiPlus className="h-5 w-5 rounded-full border-2 border-gray-900 p-[1px]" />
                    Tambah Pengeluaran
                </button>
            </div>

            {/* Table */}
            <div className="overflow-x-auto">
                <table className="w-full min-w-[800px]">
                    <thead>
                        <tr className="bg-yellow-400 text-gray-900">
                            <th className="rounded-l-xl px-6 py-4 text-left font-bold">
                                ID TRANSAKSI
                            </th>
                            <th className="px-6 py-4 text-left font-bold">
                                TANGGAL
                            </th>
                            <th className="px-6 py-4 text-left font-bold">
                                JENIS
                            </th>
                            <th className="px-6 py-4 text-left font-bold">
                                KETERANGAN
                            </th>
                            <th className="px-6 py-4 text-left font-bold">
                                JUMLAH
                            </th>
                            <th className="rounded-r-xl px-6 py-4 text-center font-bold">
                                AKSI
                            </th>
                        </tr>
                    </thead>
                    <tbody className="bg-white font-semibold text-gray-800">
                        <tr className="border-b border-gray-100">
                            <td className="px-6 py-5">EXP251010001</td>
                            <td className="px-6 py-5">10 Oktober 2025</td>
                            <td className="px-6 py-5">Gaji Karyawan</td>
                            <td className="px-6 py-5">
                                Pembayaran gaji karyawan bulan Oktober
                            </td>
                            <td className="px-6 py-5">Rp10.000.000</td>
                            <td className="px-6 py-5 text-center">
                                <button className="font-bold text-gray-900 hover:text-yellow-600">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    );

    // ==========================================
    // 3. RENDER KONTEN: TRANSAKSI AGEN
    // ==========================================
    const renderTransaksiAgen = () => (
        <div className="animate-fade-in space-y-6">
            {/* Summary Cards */}
            <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div className="rounded-2xl border border-green-200 bg-green-50 p-6">
                    <p className="text-xs font-medium text-green-600">
                        Total Komisi Bulan ini:
                    </p>
                    <h3 className="mt-1 text-2xl font-bold text-green-600">
                        Rp4.500.000
                    </h3>
                </div>
                <div className="rounded-2xl border border-blue-200 bg-blue-50 p-6">
                    <p className="text-xs font-medium text-blue-600">
                        Komisi Belum dibayar:
                    </p>
                    <h3 className="mt-1 text-2xl font-bold text-blue-600">
                        Rp1.200.000
                    </h3>
                </div>
                <div className="rounded-2xl border border-red-200 bg-red-50 p-6">
                    <p className="text-xs font-medium text-red-600">
                        Pembatalan Komisi:
                    </p>
                    <h3 className="mt-1 text-2xl font-bold text-red-600">
                        Rp150.000
                    </h3>
                </div>
            </div>

            {/* Filters */}
            <div className="flex flex-col items-center gap-4 rounded-2xl bg-gray-50 p-4 md:flex-row">
                <div className="relative w-full md:w-1/2">
                    <input
                        type="text"
                        placeholder="Cari..."
                        className="w-full rounded-full border-none bg-gray-100 py-2 pr-4 pl-10 focus:ring-2 focus:ring-yellow-400"
                    />
                    <HiSearch className="absolute top-1/2 left-4 h-5 w-5 -translate-y-1/2 text-gray-400" />
                </div>
                <div className="flex w-full items-center gap-2 md:w-auto">
                    <span className="text-sm font-bold whitespace-nowrap text-gray-700">
                        Filter Tanggal
                    </span>
                    <div className="h-10 w-32 rounded-lg bg-gray-200"></div>{' '}
                    {/* Dummy Date Picker style */}
                    <div className="h-10 w-32 rounded-lg bg-gray-200"></div>
                </div>
            </div>

            {/* Table */}
            <div className="overflow-x-auto">
                <table className="w-full min-w-[800px]">
                    <thead>
                        <tr className="bg-yellow-400 text-gray-900">
                            <th className="rounded-l-xl px-6 py-4 text-left font-bold">
                                NAMA AGEN
                            </th>
                            <th className="px-6 py-4 text-left font-bold">
                                TANGGAL
                            </th>
                            <th className="px-6 py-4 text-left font-bold">
                                KETERANGAN
                            </th>
                            <th className="px-6 py-4 text-left font-bold">
                                JUMLAH
                            </th>
                            <th className="px-6 py-4 text-left font-bold">
                                STATUS
                            </th>
                            <th className="rounded-r-xl px-6 py-4 text-center font-bold">
                                AKSI
                            </th>
                        </tr>
                    </thead>
                    <tbody className="bg-white font-semibold text-gray-800">
                        <tr className="border-b border-gray-100">
                            <td className="px-6 py-5">Aqtar Sumaktar</td>
                            <td className="px-6 py-5">5 Oktober 2025</td>
                            <td className="px-6 py-5">
                                Penjualan 20 token (Paket Sekolah)
                            </td>
                            <td className="px-6 py-5 text-green-600">
                                + Rp500.000,-
                            </td>
                            <td className="px-6 py-5">Sudah dibayar</td>
                            <td className="px-6 py-5 text-center">
                                <button className="text-sm font-bold text-gray-900 hover:text-yellow-600">
                                    Lihat Detail
                                </button>
                            </td>
                        </tr>
                        <tr className="border-b border-gray-100">
                            <td className="px-6 py-5">Ahmad Sahroni</td>
                            <td className="px-6 py-5">4 Oktober 2025</td>
                            <td className="px-6 py-5">
                                Penjualan 5 token (Paket Personal)
                            </td>
                            <td className="px-6 py-5 text-green-600">
                                + Rp125.000,-
                            </td>
                            <td className="px-6 py-5">Belum dibayar</td>
                            <td className="px-6 py-5 text-center">
                                <button className="text-sm font-bold text-gray-900 hover:text-yellow-600">
                                    Lihat Detail
                                </button>
                            </td>
                        </tr>
                        <tr className="border-b border-gray-100">
                            <td className="px-6 py-5">Rina Ilastina</td>
                            <td className="px-6 py-5">3 Oktober 2025</td>
                            <td className="px-6 py-5">
                                Pembatalan INV251001005
                            </td>
                            <td className="px-6 py-5 text-red-500">
                                - Rp500.000,-
                            </td>
                            <td className="px-6 py-5">Dibatalkan</td>
                            <td className="px-6 py-5 text-center">
                                <button className="text-sm font-bold text-gray-900 hover:text-yellow-600">
                                    Lihat Detail
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    );

    // ==========================================
    // 4. RENDER KONTEN: LAPORAN
    // ==========================================
    const renderLaporan = () => {
        const months = [
            { name: 'September 2025' },
            { name: 'Agustus 2025' },
            { name: 'Juli 2025' },
            { name: 'Juni 2025' },
            { name: 'Mei 2025' },
            { name: 'April 2025' },
        ];

        return (
            <div className="animate-fade-in">
                <h3 className="mb-6 text-2xl font-bold text-gray-900">
                    Laporan Keuangan
                </h3>
                <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    {months.map((m, i) => (
                        <div
                            key={i}
                            className="flex flex-col items-center rounded-[2.5rem] border border-gray-100 bg-white p-8 text-center shadow-sm transition-all hover:shadow-md"
                        >
                            <h4 className="mb-8 text-2xl leading-tight font-bold text-gray-900">
                                Bulan <br /> {m.name}
                            </h4>
                            <div className="w-full space-y-3">
                                <button className="w-full rounded-full border-2 border-yellow-400 py-2 font-bold text-gray-900 transition-colors hover:bg-yellow-50">
                                    Lihat
                                </button>
                                <button className="w-full rounded-full border-2 border-yellow-400 py-2 font-bold text-gray-900 transition-colors hover:bg-yellow-50">
                                    Download
                                </button>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        );
    };

    // ==========================================
    // RENDER UTAMA
    // ==========================================
    const tabs = ['Pemasukan', 'Pengeluaran', 'Transaksi Agen', 'Laporan'];

    return (
        <AdminDashboardLayout>
            <Head title="Manajemen Keuangan" />

            <div className="space-y-6 font-poppins">
                <h2 className="text-3xl font-bold text-gray-900">
                    Manajemen Keuangan
                </h2>

                {/* Tabs Navigasi */}
                <div className="flex w-fit flex-wrap gap-4 rounded-[2rem] bg-white p-4 shadow-sm">
                    {tabs.map((tab) => (
                        <button
                            key={tab}
                            onClick={() => setActiveTab(tab)}
                            className={`rounded-full border px-8 py-3 text-sm font-bold transition-all ${
                                activeTab === tab
                                    ? 'border-yellow-400 bg-yellow-400 text-gray-900 shadow-md'
                                    : 'border-yellow-400 bg-white text-gray-900 hover:bg-yellow-50'
                            } `}
                        >
                            {tab}
                        </button>
                    ))}
                </div>

                {/* Kontainer Utama */}
                <div className="min-h-[600px] rounded-[2.5rem] bg-white p-8 shadow-sm">
                    {activeTab === 'Pemasukan' && renderPemasukan()}
                    {activeTab === 'Pengeluaran' && renderPengeluaran()}
                    {activeTab === 'Transaksi Agen' && renderTransaksiAgen()}
                    {activeTab === 'Laporan' && renderLaporan()}

                    {/* Pagination (Hanya muncul di tab tabel) */}
                    {activeTab !== 'Laporan' && (
                        <div className="mt-8 flex flex-col items-center justify-between gap-4 border-t border-yellow-200 pt-6 md:flex-row">
                            <p className="text-sm font-bold text-gray-900">
                                Showing 1-1 of 1000
                            </p>
                            <div className="flex gap-3">
                                <button className="rounded-full bg-yellow-400 px-6 py-2 font-bold text-gray-900 shadow-sm transition-colors hover:bg-yellow-500">
                                    Previous
                                </button>
                                <button className="rounded-full bg-yellow-400 px-6 py-2 font-bold text-gray-900 shadow-sm transition-colors hover:bg-yellow-500">
                                    Next
                                </button>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AdminDashboardLayout>
    );
}
