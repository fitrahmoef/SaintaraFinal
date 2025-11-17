import AdminDashboardLayout from '@/layouts/dashboardLayoutAdmin'; // Pastikan path layout benar
import { Head } from '@inertiajs/react';
import {
    HiArrowSmRight,
    HiChatAlt2,
    HiCurrencyDollar,
    HiDesktopComputer,
    HiMicrophone,
    HiUserGroup,
    HiUsers,
} from 'react-icons/hi';
import { HiWrench } from 'react-icons/hi2';

// ...rest of code...

export default function Dashboard() {
    return (
        <AdminDashboardLayout>
            <Head title="Dashboard Admin" />

            <div className="space-y-6">
                {/* ======================= */}
                {/* BAGIAN 1: KARTU ATAS */}
                {/* ======================= */}
                <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                    {/* Card 1: Total Tes */}
                    <div className="flex items-center justify-between rounded-3xl bg-white p-6 shadow-sm">
                        <div>
                            <p className="mb-1 text-xs text-gray-400">
                                Total Tes Bulan ini
                            </p>
                            <h3 className="text-3xl font-bold text-gray-800">
                                500
                            </h3>
                        </div>
                        {/* Mini Bar Chart Visual (CSS Only) */}
                        <div className="flex h-10 items-end gap-1">
                            <div className="h-4 w-1.5 rounded-full bg-yellow-100"></div>
                            <div className="h-6 w-1.5 rounded-full bg-yellow-200"></div>
                            <div className="h-8 w-1.5 rounded-full bg-yellow-300"></div>
                            <div className="h-5 w-1.5 rounded-full bg-yellow-400"></div>
                            <div className="h-10 w-1.5 rounded-full bg-yellow-500"></div>
                        </div>
                    </div>

                    {/* Card 2: Agen Aktif */}
                    <div className="flex items-center gap-4 rounded-3xl bg-white p-6 shadow-sm">
                        <div className="flex h-12 w-12 items-center justify-center rounded-full bg-yellow-400 text-white">
                            <HiUserGroup className="h-6 w-6" />
                        </div>
                        <div>
                            <p className="text-xs text-gray-400">Agen Aktif</p>
                            <h3 className="text-2xl font-bold text-gray-800">
                                321
                            </h3>
                        </div>
                    </div>

                    {/* Card 3: Agenda Talkshow */}
                    <div className="flex items-center gap-4 rounded-3xl bg-white p-6 shadow-sm">
                        <div className="flex h-12 w-12 items-center justify-center rounded-full bg-yellow-400 text-white">
                            <HiMicrophone className="h-6 w-6" />
                        </div>
                        <div>
                            <p className="text-xs text-gray-400">
                                Agenda Talkshow
                            </p>
                            <h3 className="text-xl font-bold text-gray-800">
                                12 Talkshow
                            </h3>
                        </div>
                    </div>

                    {/* Card 4: Agenda Webinar */}
                    <div className="flex items-center gap-4 rounded-3xl bg-white p-6 shadow-sm">
                        <div className="flex h-12 w-12 items-center justify-center rounded-full bg-yellow-400 text-white">
                            <HiChatAlt2 className="h-6 w-6" />
                        </div>
                        <div>
                            <p className="text-xs text-gray-400">
                                Agenda Webinar
                            </p>
                            <h3 className="text-xl font-bold text-gray-800">
                                1 Webinar
                            </h3>
                        </div>
                    </div>
                </div>

                {/* ======================= */}
                {/* BAGIAN 2: TENGAH (CHART & LIST) */}
                {/* ======================= */}
                <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    {/* KOLOM KIRI: Distribusi Tes (Donut Chart) */}
                    <div className="rounded-3xl bg-white p-8 shadow-sm lg:col-span-2">
                        <h3 className="mb-6 text-xl font-bold text-gray-800">
                            Distribusi Tes
                        </h3>
                        <div className="flex flex-col items-center justify-center gap-10 md:flex-row">
                            {/* Legend */}
                            <div className="space-y-4">
                                <div className="flex items-center gap-3">
                                    <span className="h-4 w-4 rounded-full bg-[#FCD34D]"></span>{' '}
                                    {/* Kuning Muda */}
                                    <span className="text-sm font-medium text-gray-600">
                                        Personal
                                    </span>
                                </div>
                                <div className="flex items-center gap-3">
                                    <span className="h-4 w-4 rounded-full bg-[#A16207]"></span>{' '}
                                    {/* Coklat/Emas Tua */}
                                    <span className="text-sm font-medium text-gray-600">
                                        Institution
                                    </span>
                                </div>
                                <div className="flex items-center gap-3">
                                    <span className="h-4 w-4 rounded-full bg-[#FFFF00]"></span>{' '}
                                    {/* Kuning Terang */}
                                    <span className="text-sm font-medium text-gray-600">
                                        Gift
                                    </span>
                                </div>
                            </div>

                            {/* CSS Donut Chart (Tanpa Library Berat) */}
                            <div
                                className="relative h-48 w-48 rounded-full"
                                style={{
                                    background:
                                        'conic-gradient(#FCD34D 0% 35%, #FFFF00 35% 45%, #A16207 45% 100%)',
                                }}
                            >
                                {/* Lubang Tengah Donut */}
                                <div className="absolute inset-0 m-auto h-24 w-24 rounded-full bg-white"></div>
                            </div>
                        </div>
                    </div>

                    {/* KOLOM KANAN: Permohonan Persetujuan */}
                    <div className="flex flex-col justify-between rounded-3xl bg-white p-8 shadow-sm lg:col-span-1">
                        <div>
                            <h3 className="mb-6 text-lg font-bold text-gray-800">
                                Permohonan Persetujuan
                            </h3>
                            <div className="relative ml-2 space-y-6 border-l-2 border-gray-100 pl-6">
                                {/* Item 1 */}
                                <div className="relative">
                                    <span className="absolute top-1 -left-[31px] h-3 w-3 rounded-full bg-yellow-400"></span>
                                    <h4 className="font-bold text-gray-800">
                                        Komisi Agen
                                    </h4>
                                    <p className="mt-1 text-xs text-gray-400">
                                        Today at 10:00 AM
                                    </p>
                                </div>
                                {/* Item 2 */}
                                <div className="relative">
                                    <span className="absolute top-1 -left-[31px] h-3 w-3 rounded-full bg-yellow-400"></span>
                                    <h4 className="font-bold text-gray-800">
                                        Penawaran Kerja sama
                                    </h4>
                                    <p className="mt-1 text-xs text-gray-400">
                                        Today at 07:00 AM
                                    </p>
                                </div>
                                {/* Item 3 */}
                                <div className="relative">
                                    <span className="absolute top-1 -left-[31px] h-3 w-3 rounded-full bg-gray-300"></span>
                                    <h4 className="font-bold text-gray-800">
                                        Undangan Seminar
                                    </h4>
                                    <p className="mt-1 text-xs text-gray-400">
                                        Yesterday at 05:00 PM
                                    </p>
                                </div>
                            </div>
                        </div>
                        <button className="mt-4 flex items-center justify-end text-sm font-bold text-yellow-500 hover:underline">
                            View all <HiArrowSmRight className="ml-1 h-5 w-5" />
                        </button>
                    </div>
                </div>

                {/* ======================= */}
                {/* BAGIAN 3: BAWAH (3 KOLOM) */}
                {/* ======================= */}
                <div className="grid grid-cols-1 gap-6 md:grid-cols-3">
                    {/* 1. Penjualan Token */}
                    <div className="rounded-3xl bg-white p-6 shadow-sm">
                        <div className="mb-4 flex items-start justify-between">
                            <div>
                                <p className="text-xs text-gray-400">
                                    Penjualan Token Bulan ini
                                </p>
                                <h3 className="mt-1 text-2xl font-extrabold text-gray-900">
                                    Rp9.600.000,-
                                </h3>
                                <span className="mt-1 inline-flex items-center text-xs font-bold text-green-500">
                                    <span className="mr-2 h-2 w-2 rounded-full bg-green-500"></span>{' '}
                                    On track
                                </span>
                            </div>
                            <span className="rounded-lg bg-green-100 px-2 py-1 text-xs font-bold text-green-500">
                                +2.45%
                            </span>
                        </div>
                        {/* CSS Bar Chart */}
                        <div className="mt-6 flex h-32 items-end justify-between gap-2">
                            <div className="group relative h-[40%] w-3 rounded-t-md bg-gray-100">
                                <div className="absolute bottom-0 h-[60%] w-full rounded-t-md bg-yellow-400"></div>
                            </div>
                            <div className="relative h-[60%] w-3 rounded-t-md bg-gray-100">
                                <div className="absolute bottom-0 h-[40%] w-full rounded-t-md bg-yellow-400"></div>
                            </div>
                            <div className="relative h-[80%] w-3 rounded-t-md bg-gray-100">
                                <div className="absolute bottom-0 h-[70%] w-full rounded-t-md bg-yellow-400"></div>
                            </div>
                            <div className="relative h-[50%] w-3 rounded-t-md bg-gray-100">
                                <div className="absolute bottom-0 h-[30%] w-full rounded-t-md bg-yellow-400"></div>
                            </div>
                            <div className="relative h-[70%] w-3 rounded-t-md bg-gray-100">
                                <div className="absolute bottom-0 h-[80%] w-full rounded-t-md bg-yellow-400"></div>
                            </div>
                            <div className="relative h-[90%] w-3 rounded-t-md bg-gray-100">
                                <div className="absolute bottom-0 h-[90%] w-full rounded-t-md bg-yellow-400"></div>
                            </div>
                            <div className="relative h-[60%] w-3 rounded-t-md bg-gray-100">
                                <div className="absolute bottom-0 h-[20%] w-full rounded-t-md bg-yellow-400"></div>
                            </div>
                        </div>
                    </div>

                    {/* 2. Laporan Harian Tim */}
                    <div className="flex flex-col justify-between rounded-3xl bg-white p-6 shadow-sm">
                        <h3 className="mb-4 text-lg font-bold text-gray-800">
                            Laporan Harian Tim
                        </h3>
                        <div className="space-y-4">
                            {/* User 1 */}
                            <div className="flex items-center gap-3">
                                <img
                                    src="https://i.pravatar.cc/150?u=1"
                                    alt="User"
                                    className="h-10 w-10 rounded-full object-cover"
                                />
                                <div>
                                    <h5 className="text-sm font-bold text-gray-800">
                                        Lia Kaula
                                    </h5>
                                    <p className="text-xs text-gray-400">
                                        Keuangan
                                    </p>
                                </div>
                            </div>
                            {/* User 2 */}
                            <div className="flex items-center gap-3">
                                <img
                                    src="https://i.pravatar.cc/150?u=2"
                                    alt="User"
                                    className="h-10 w-10 rounded-full object-cover"
                                />
                                <div>
                                    <h5 className="text-sm font-bold text-gray-800">
                                        Andi Kindy
                                    </h5>
                                    <p className="text-xs text-gray-400">
                                        Marketing
                                    </p>
                                </div>
                            </div>
                            {/* User 3 */}
                            <div className="flex items-center gap-3">
                                <img
                                    src="https://i.pravatar.cc/150?u=3"
                                    alt="User"
                                    className="h-10 w-10 rounded-full object-cover"
                                />
                                <div>
                                    <h5 className="text-sm font-bold text-gray-800">
                                        Bayu Subaya
                                    </h5>
                                    <p className="text-xs text-gray-400">SDM</p>
                                </div>
                            </div>
                        </div>
                        <button className="mt-2 flex items-center justify-end text-sm font-bold text-yellow-500 hover:underline">
                            View all <HiArrowSmRight className="ml-1 h-5 w-5" />
                        </button>
                    </div>

                    {/* 3. Absensi */}
                    <div className="rounded-3xl bg-white p-6 shadow-sm">
                        <h3 className="mb-6 text-lg font-bold text-gray-800">
                            Absensi
                        </h3>
                        <div className="space-y-6">
                            <div className="flex items-center gap-4">
                                <HiDesktopComputer className="h-6 w-6 text-blue-500" />
                                <span className="font-bold text-gray-700">
                                    Tim IT
                                </span>
                            </div>
                            <div className="flex items-center gap-4">
                                <HiCurrencyDollar className="h-6 w-6 text-green-500" />
                                <span className="font-bold text-gray-700">
                                    Tim Keuangan
                                </span>
                            </div>
                            <div className="flex items-center gap-4">
                                <HiUsers className="h-6 w-6 text-pink-500" />
                                <span className="font-bold text-gray-700">
                                    Tim Marketing
                                </span>
                            </div>
                            <div className="flex items-center gap-4">
                                <HiWrench className="h-6 w-6 text-yellow-600" />
                                <span className="font-bold text-gray-700">
                                    Tim Operasional
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AdminDashboardLayout>
    );
}
