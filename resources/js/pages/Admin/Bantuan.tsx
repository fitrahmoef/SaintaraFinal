import AdminDashboardLayout from '@/layouts/dashboardLayoutAdmin'; // Pastikan path layout benard
import { Head } from '@inertiajs/react';
import { useState } from 'react';
import { HiLocationMarker } from 'react-icons/hi'; // Ikon untuk lokasi

export default function Support() {
    const [activeTab, setActiveTab] = useState('Komplain');

    // ==========================================
    // 1. KONTEN: PERSETUJUAN
    // ==========================================
    const renderPersetujuan = () => (
        <div className="animate-fade-in space-y-6">
            <h3 className="text-lg font-bold text-blue-900">
                Permohonan Persetujuan (1)
            </h3>

            {/* Card Permohonan */}
            <div className="flex flex-col items-start justify-between gap-4 rounded-2xl border border-gray-100 bg-gray-50 p-6 shadow-sm md:flex-row md:items-center">
                <div className="flex items-start gap-4">
                    <div className="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100">
                        <HiLocationMarker className="h-6 w-6 text-blue-600" />
                    </div>
                    <div>
                        <h4 className="text-lg font-bold text-blue-900">
                            Penawaran Kerja Sama - PT Maju Jaya
                        </h4>
                        <p className="mt-1 text-sm text-gray-500">
                            Diajukan oleh: Tim Marketing | 5 Oktober 2025
                        </p>
                    </div>
                </div>
                <div className="flex gap-3">
                    <button className="rounded-lg border border-gray-300 px-6 py-2 text-sm font-bold text-gray-600 transition-colors hover:bg-gray-100">
                        Tolak
                    </button>
                    <button className="rounded-lg bg-green-500 px-6 py-2 text-sm font-bold text-white shadow-md transition-colors hover:bg-green-600">
                        Setuju
                    </button>
                </div>
            </div>
        </div>
    );

    // ==========================================
    // 2. KONTEN: KOMPLAIN (DEFAULT TAB)
    // ==========================================
    const renderKomplain = () => (
        <div className="animate-fade-in space-y-8">
            {/* Summary Cards */}
            <div className="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div className="rounded-2xl border border-orange-100 bg-orange-50 p-6">
                    <p className="mb-2 text-sm font-bold text-orange-800">
                        Tiket Baru
                    </p>
                    <h3 className="text-3xl font-extrabold text-orange-900">
                        5
                    </h3>
                </div>
                <div className="rounded-2xl border border-blue-100 bg-blue-50 p-6">
                    <p className="mb-2 text-sm font-bold text-blue-800">
                        Sedang Diproses
                    </p>
                    <h3 className="text-3xl font-extrabold text-blue-900">
                        12
                    </h3>
                </div>
                <div className="rounded-2xl border border-green-100 bg-green-50 p-6">
                    <p className="mb-2 text-sm font-bold text-green-800">
                        Selesai
                    </p>
                    <h3 className="text-3xl font-extrabold text-green-900">
                        90
                    </h3>
                </div>
            </div>

            {/* Table Komplain */}
            <div className="overflow-x-auto rounded-xl border border-gray-200">
                <table className="w-full min-w-[700px]">
                    <thead className="bg-gray-100 text-xs font-extrabold tracking-wider text-blue-900 uppercase">
                        <tr>
                            <th className="px-6 py-4 text-left">ID Tiket</th>
                            <th className="px-6 py-4 text-left">Pelapor</th>
                            <th className="px-6 py-4 text-left">Subjek</th>
                            <th className="px-6 py-4 text-center">Prioritas</th>
                            <th className="px-6 py-4 text-center">Status</th>
                            <th className="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-100 bg-white text-sm font-medium text-gray-700">
                        <tr>
                            <td className="px-6 py-4">#T-00125</td>
                            <td className="px-6 py-4">
                                user@gmail.com (Personal)
                            </td>
                            <td className="px-6 py-4 font-bold text-gray-900">
                                Tidak bisa download hasil tes
                            </td>
                            <td className="px-6 py-4 text-center">
                                <span className="rounded-md bg-red-100 px-3 py-1 text-xs font-bold text-red-600">
                                    Tinggi
                                </span>
                            </td>
                            <td className="px-6 py-4 text-center">
                                <span className="rounded-md bg-blue-100 px-3 py-1 text-xs font-bold text-blue-600">
                                    Diproses
                                </span>
                            </td>
                            <td className="px-6 py-4 text-center">
                                <button className="text-xs font-bold text-yellow-500 hover:text-yellow-600">
                                    Cek Detail
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    );

    // ==========================================
    // 3. KONTEN: PERBAIKAN
    // ==========================================
    const renderPerbaikan = () => (
        <div className="animate-fade-in space-y-6">
            <h3 className="text-lg font-bold text-blue-900">
                Status Perbaikan Komplain
            </h3>
            <div className="overflow-x-auto rounded-xl border border-gray-200">
                <table className="w-full min-w-[700px]">
                    <thead className="bg-gray-100 text-xs font-extrabold tracking-wider text-blue-900 uppercase">
                        <tr>
                            <th className="px-6 py-4 text-left">ID Tiket</th>
                            <th className="px-6 py-4 text-left">Subjek</th>
                            <th className="px-6 py-4 text-left">
                                Penanggung Jawab
                            </th>
                            <th className="px-6 py-4 text-left">
                                Status Perbaikan
                            </th>
                            <th className="px-6 py-4 text-left">
                                Tanggal Selesai
                            </th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-100 bg-white text-sm font-medium text-gray-700">
                        <tr>
                            <td className="px-6 py-4">#T-00125</td>
                            <td className="px-6 py-4">
                                Tidak bisa download hasil tes
                            </td>
                            <td className="px-6 py-4">Bayu Subaya</td>
                            <td className="px-6 py-4">
                                <span className="rounded bg-yellow-50 px-2 py-1 font-bold text-yellow-600">
                                    Investigasi
                                </span>
                            </td>
                            <td className="px-6 py-4">-</td>
                        </tr>
                        <tr>
                            <td className="px-6 py-4">#T-00124</td>
                            <td className="px-6 py-4">
                                Voucher tidak bisa dipakai
                            </td>
                            <td className="px-6 py-4">Tim Marketing</td>
                            <td className="px-6 py-4">
                                <span className="rounded bg-green-50 px-2 py-1 font-bold text-green-600">
                                    Selesai
                                </span>
                            </td>
                            <td className="px-6 py-4">4 Okt 2025</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    );

    // Daftar Tab
    const tabs = ['Persetujuan', 'Komplain', 'Perbaikan'];

    return (
        <AdminDashboardLayout>
            <Head title="Bantuan & Layanan" />

            <div className="space-y-8 font-poppins">
                <h2 className="text-3xl font-bold text-blue-900">
                    Bantuan & Layanan
                </h2>

                {/* Tab Container (Card Putih Besar) */}
                <div className="min-h-[600px] rounded-[2.5rem] bg-white p-8 shadow-sm">
                    {/* Tab Navigasi (Tengah) */}
                    <div className="mb-10 flex justify-center">
                        <div className="flex gap-6 md:gap-12">
                            {tabs.map((tab) => (
                                <button
                                    key={tab}
                                    onClick={() => setActiveTab(tab)}
                                    className={`rounded-2xl border-2 px-8 py-3 text-sm font-bold transition-all ${
                                        activeTab === tab
                                            ? 'scale-105 transform border-yellow-400 bg-yellow-400 text-gray-900 shadow-md'
                                            : 'border-yellow-400 bg-white text-gray-900 hover:bg-yellow-50'
                                    } `}
                                >
                                    {tab}
                                </button>
                            ))}
                        </div>
                    </div>

                    {/* Render Konten */}
                    <div className="mt-8">
                        {activeTab === 'Persetujuan' && renderPersetujuan()}
                        {activeTab === 'Komplain' && renderKomplain()}
                        {activeTab === 'Perbaikan' && renderPerbaikan()}
                    </div>
                </div>
            </div>
        </AdminDashboardLayout>
    );
}
