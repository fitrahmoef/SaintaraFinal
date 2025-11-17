import AdminDashboardLayout from '@/layouts/dashboardLayoutAdmin'; // Pastikan path layout benar
import { Head } from '@inertiajs/react';
import { useState } from 'react';
import { HiPlus, HiSearch } from 'react-icons/hi';

export default function Users() {
    // ==========================================
    // 1. STATE: Menentukan Tab mana yang Aktif
    // ==========================================
    const [activeTab, setActiveTab] = useState('Personal');

    // ==========================================
    // 2. DATA DUMMY (Beda-beda tiap kategori)
    // ==========================================
    const dataPersonal = [
        {
            id: 1,
            name: 'Budi Santoso',
            email: 'BudiSantoso@gmail.com',
            lastTest: '2025-10-01',
        },
        {
            id: 2,
            name: 'Siti Aminah',
            email: 'siti.aminah@yahoo.com',
            lastTest: '2025-09-28',
        },
    ];

    const dataSekolah = [
        {
            id: 1,
            name: 'SMAN 1 Jakarta',
            email: 'admin@sman1jkt.sch.id',
            lastTest: '2025-09-15',
        },
        {
            id: 2,
            name: 'SMP Taruna Bangsa',
            email: 'info@taruna.sch.id',
            lastTest: '2025-08-20',
        },
        {
            id: 3,
            name: 'SD Pelita Harapan',
            email: 'contact@pelita.sch.id',
            lastTest: '2025-10-05',
        },
    ];

    const dataInstansi = [
        {
            id: 1,
            name: 'PT Telkom Indonesia',
            email: 'hrd@telkom.co.id',
            lastTest: '2025-10-02',
        },
        {
            id: 2,
            name: 'Kementerian Kesehatan',
            email: 'pusdatin@kemkes.go.id',
            lastTest: '2025-09-30',
        },
    ];

    const dataGift = [
        {
            id: 1,
            name: 'Voucher Anniversary',
            email: 'gift@user.com',
            lastTest: '2025-10-10',
        },
    ];

    // ==========================================
    // 3. LOGIKA: Pilih data berdasarkan Tab Aktif
    // ==========================================
    const getCurrentData = () => {
        switch (activeTab) {
            case 'Personal':
                return dataPersonal;
            case 'Sekolah':
                return dataSekolah;
            case 'Instansi/Organisasi':
                return dataInstansi;
            case 'Hadiah/Gift':
                return dataGift;
            default:
                return [];
        }
    };

    const currentUsers = getCurrentData(); // Data yang akan ditampilkan di tabel

    // Daftar Nama Menu
    const tabs = [
        'Personal',
        'Sekolah',
        'Instansi/Organisasi',
        'Hadiah/Gift',
        'Loyalitas&Performa',
    ];

    return (
        <AdminDashboardLayout>
            <Head title="Manajemen Pengguna" />

            <div className="space-y-6 font-poppins">
                {/* === TAB NAVIGASI === */}
                <div className="flex w-fit flex-wrap gap-4 rounded-[2rem] bg-white p-4 shadow-sm">
                    {tabs.map((tabName) => (
                        <button
                            key={tabName}
                            // SAAT DIKLIK: Ubah state activeTab
                            onClick={() => setActiveTab(tabName)}
                            className={`rounded-full border px-6 py-2 text-sm font-bold transition-all ${
                                activeTab === tabName
                                    ? 'border-yellow-400 bg-yellow-400 text-gray-900 shadow-md' // Style Aktif
                                    : 'border-yellow-400 bg-white text-gray-900 hover:bg-yellow-50'
                            } // Style Tidak Aktif`}
                        >
                            {tabName}
                        </button>
                    ))}
                </div>

                {/* === CONTENT AREA === */}
                <div className="min-h-[600px] rounded-[2.5rem] bg-white p-8 shadow-sm">
                    {/* Header Table */}
                    <div className="mb-8 flex flex-col items-center justify-between gap-4 md:flex-row">
                        <div className="relative w-full md:w-1/2">
                            <input
                                type="text"
                                placeholder={`Cari pengguna ${activeTab}...`}
                                className="w-full rounded-full border-none bg-gray-100 py-3 pr-12 pl-6 text-gray-600 transition-all focus:ring-2 focus:ring-yellow-400"
                            />
                            <HiSearch className="absolute top-1/2 right-4 h-6 w-6 -translate-y-1/2 transform text-gray-400" />
                        </div>

                        <button className="flex transform items-center gap-2 rounded-full bg-yellow-400 px-8 py-3 font-bold text-gray-900 shadow-md transition-all hover:scale-105 hover:bg-yellow-500">
                            <HiPlus className="h-5 w-5 rounded-full border-2 border-gray-900 p-[1px]" />
                            Tambah Pengguna
                        </button>
                    </div>

                    {/* === TABEL DATA === */}
                    <div className="overflow-x-auto">
                        <table className="w-full min-w-[800px]">
                            <thead>
                                <tr className="bg-yellow-400 text-gray-900">
                                    <th className="rounded-l-full px-6 py-4 text-left font-bold">
                                        Nama
                                    </th>
                                    <th className="px-6 py-4 text-left font-bold">
                                        Email
                                    </th>
                                    <th className="px-6 py-4 text-left font-bold">
                                        Tanggal Tes Terakhir
                                    </th>
                                    <th className="rounded-r-full px-6 py-4 text-center font-bold">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>

                            <tbody className="font-semibold text-gray-800">
                                {currentUsers.length > 0 ? (
                                    currentUsers.map((user, index) => (
                                        <tr
                                            key={index}
                                            className="border-b border-yellow-200 transition-colors hover:bg-gray-50"
                                        >
                                            <td className="px-6 py-5">
                                                {user.name}
                                            </td>
                                            <td className="px-6 py-5">
                                                {user.email}
                                            </td>
                                            <td className="px-6 py-5">
                                                {user.lastTest}
                                            </td>
                                            <td className="px-6 py-5 text-center">
                                                <button className="inline-flex items-center gap-1 font-bold text-gray-900 hover:text-yellow-600">
                                                    Edit
                                                </button>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    // Tampilan jika data kosong
                                    <tr>
                                        <td
                                            colSpan={4}
                                            className="py-10 text-center text-gray-400"
                                        >
                                            Belum ada data untuk kategori{' '}
                                            {activeTab}
                                        </td>
                                    </tr>
                                )}
                                <tr className="border-b border-yellow-200">
                                    <td colSpan={4} className="py-2"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {/* Pagination */}
                    <div className="mt-8 flex flex-col items-center justify-between gap-4 md:flex-row">
                        <p className="text-sm font-bold text-gray-900">
                            Showing {currentUsers.length > 0 ? 1 : 0}-
                            {currentUsers.length} of {currentUsers.length}
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
                </div>
            </div>
        </AdminDashboardLayout>
    );
}
