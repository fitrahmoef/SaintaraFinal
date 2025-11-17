import AdminDashboardLayout from '@/layouts/dashboardLayoutAdmin'; // Pastikan path layout benar
import { Head } from '@inertiajs/react';
import { useState } from 'react';

export default function Team() {
    const [activeTab, setActiveTab] = useState('Data Absensi');

    // ==========================================
    // 1. KONTEN: DATA ABSENSI
    // ==========================================
    const renderAbsensi = () => (
        <div className="animate-fade-in space-y-6">
            <h3 className="text-lg font-bold text-gray-900">
                Absensi Bulan Oktober 2025
            </h3>
            <div className="overflow-x-auto rounded-xl border border-gray-200">
                <table className="w-full min-w-[600px]">
                    <thead className="bg-gray-100 text-sm text-gray-900 uppercase">
                        <tr>
                            <th className="px-4 py-3 text-left font-bold">
                                Nama Anggota
                            </th>
                            <th className="px-4 py-3 text-left font-bold">
                                Tanggal
                            </th>
                            <th className="px-4 py-3 text-left font-bold">
                                Jam Masuk
                            </th>
                            <th className="px-4 py-3 text-left font-bold">
                                Jam Pulang
                            </th>
                            <th className="px-4 py-3 text-center font-bold">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-100 bg-white text-sm text-gray-800">
                        <tr>
                            <td className="px-4 py-3">Budi Santoso</td>
                            <td className="px-4 py-3">5 Oktober 2025</td>
                            <td className="px-4 py-3">08.55</td>
                            <td className="px-4 py-3">17.00</td>
                            <td className="px-4 py-3 text-center">
                                <span className="rounded-full border border-green-200 bg-green-100 px-3 py-1 text-xs font-bold text-green-700">
                                    Hadir
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td className="px-4 py-3">WR Supratman</td>
                            <td className="px-4 py-3">5 Oktober 2025</td>
                            <td className="px-4 py-3">-</td>
                            <td className="px-4 py-3">-</td>
                            <td className="px-4 py-3 text-center">
                                <span className="rounded-full border border-red-200 bg-red-100 px-3 py-1 text-xs font-bold text-red-700">
                                    Absen
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    );

    // ==========================================
    // 2. KONTEN: LAPORAN KERJA
    // ==========================================
    const renderLaporanKerja = () => (
        <div className="animate-fade-in space-y-6">
            <div className="flex items-center justify-between">
                <h3 className="text-lg font-bold text-gray-900">
                    Daftar Laporan Kerja Harian
                </h3>
                <button className="rounded-full bg-yellow-400 px-4 py-2 text-sm font-bold text-gray-900 shadow-md transition-colors hover:bg-yellow-500">
                    Buat Laporan Baru
                </button>
            </div>

            {/* Card Laporan */}
            <div className="flex items-center justify-between rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-shadow hover:shadow-md">
                <div>
                    <h4 className="text-lg font-bold text-gray-900">
                        Laporan - Budi Santoso
                    </h4>
                    <p className="mt-1 text-sm text-gray-500">
                        Tanggal: 5 Oktober 2025
                    </p>
                </div>
                <button className="text-sm font-bold text-yellow-600 hover:text-yellow-700">
                    Lihat Laporan
                </button>
            </div>
        </div>
    );

    // ==========================================
    // 3. KONTEN: GAJI
    // ==========================================
    const renderGaji = () => (
        <div className="animate-fade-in space-y-6">
            <h3 className="text-lg font-bold text-gray-900">
                Penggajian Periode Oktober 2025
            </h3>
            <div className="overflow-x-auto rounded-xl border border-gray-200">
                <table className="w-full min-w-[600px]">
                    <thead className="bg-gray-100 text-sm text-gray-900 uppercase">
                        <tr>
                            <th className="px-4 py-3 text-left font-bold">
                                Nama Anggota
                            </th>
                            <th className="px-4 py-3 text-left font-bold">
                                Jabatan
                            </th>
                            <th className="px-4 py-3 text-left font-bold">
                                Gaji Bersih
                            </th>
                            <th className="px-4 py-3 text-center font-bold">
                                Status
                            </th>
                            <th className="px-4 py-3 text-center font-bold">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-100 bg-white text-sm text-gray-800">
                        <tr>
                            <td className="px-4 py-3">Budi Santoso</td>
                            <td className="px-4 py-3">IT Support</td>
                            <td className="px-4 py-3 font-semibold">
                                Rp10.000.000
                            </td>
                            <td className="px-4 py-3 text-center">
                                <span className="rounded-full border border-green-200 bg-green-100 px-3 py-1 text-xs font-bold text-green-700">
                                    Sudah Dibayar
                                </span>
                            </td>
                            <td className="px-4 py-3 text-center">
                                <button className="text-xs font-bold text-yellow-600 hover:underline">
                                    Lihat Rincian
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td className="px-4 py-3">WR Supratman</td>
                            <td className="px-4 py-3">Admin</td>
                            <td className="px-4 py-3 font-semibold">
                                Rp5.000.000
                            </td>
                            <td className="px-4 py-3 text-center">
                                <span className="rounded-full border border-red-200 bg-red-100 px-3 py-1 text-xs font-bold text-red-700">
                                    Belum Dibayar
                                </span>
                            </td>
                            <td className="px-4 py-3 text-center">
                                <button className="text-xs font-bold text-yellow-600 hover:underline">
                                    Lihat Rincian
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    );

    // ==========================================
    // 4. KONTEN: KOMISI (Placeholder)
    // ==========================================
    const renderKomisi = () => (
        <div className="animate-fade-in space-y-6">
            <h3 className="text-lg font-bold text-gray-900">
                Komisi Agen Periode Oktober 2025
            </h3>
            <div className="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center">
                <p className="text-gray-500">
                    Belum ada data komisi untuk periode ini.
                </p>
            </div>
        </div>
    );

    // ==========================================
    // 5. KONTEN: PERFORMA
    // ==========================================
    const renderPerforma = () => (
        <div className="animate-fade-in space-y-6">
            <h3 className="text-lg font-bold text-gray-900">
                Performa Tim & Agen
            </h3>

            {/* Grafik Placeholder */}
            <div className="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h4 className="mb-4 font-bold text-gray-800">
                    Pencapaian Target Tim Marketing
                </h4>
                <div className="flex h-48 w-full items-center justify-center rounded-lg bg-gray-200">
                    <span className="text-sm text-gray-500">
                        Grafik Performa Tim akan ditampilkan disini
                    </span>
                </div>
            </div>

            {/* Progress Bar Individu */}
            <div className="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div className="mb-2 flex items-end justify-between">
                    <div>
                        <p className="mb-1 text-xs text-gray-500">
                            Pencapaian Individu (Agen)
                        </p>
                        <h4 className="font-bold text-gray-800">
                            Agen A - Rina Wulandari
                        </h4>
                    </div>
                    <span className="text-sm font-bold text-gray-600">
                        85 dari 100 Tes
                    </span>
                </div>
                <div className="h-2.5 w-full rounded-full bg-gray-100">
                    <div
                        className="h-2.5 rounded-full bg-yellow-400"
                        style={{ width: '85%' }}
                    ></div>
                </div>
            </div>
        </div>
    );

    // Tabs List
    const tabs = [
        'Data Absensi',
        'Laporan Kerja',
        'Gaji',
        'Komisi',
        'Performa',
    ];

    return (
        <AdminDashboardLayout>
            <Head title="Manajemen Tim" />

            <div className="space-y-6 font-poppins">
                <h2 className="text-3xl font-bold text-gray-900">
                    Manajemen Tim
                </h2>

                {/* Tabs Navigasi */}
                <div className="flex w-fit flex-wrap gap-4 rounded-[2rem] bg-white p-4 shadow-sm">
                    {tabs.map((tab) => (
                        <button
                            key={tab}
                            onClick={() => setActiveTab(tab)}
                            className={`rounded-full border px-6 py-2 text-sm font-bold transition-all ${
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
                <div className="min-h-[500px] rounded-[2.5rem] bg-white p-8 shadow-sm">
                    {activeTab === 'Data Absensi' && renderAbsensi()}
                    {activeTab === 'Laporan Kerja' && renderLaporanKerja()}
                    {activeTab === 'Gaji' && renderGaji()}
                    {activeTab === 'Komisi' && renderKomisi()}
                    {activeTab === 'Performa' && renderPerforma()}
                </div>
            </div>
        </AdminDashboardLayout>
    );
}
