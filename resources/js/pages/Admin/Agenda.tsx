import AdminDashboardLayout from '@/layouts/dashboardLayoutAdmin'; // Pastikan path layout benar
import { Head } from '@inertiajs/react';
import { useState } from 'react';
import {
    HiChevronLeft,
    HiChevronRight,
    HiMicrophone,
    HiPlus,
    HiUserGroup,
    HiVideoCamera,
} from 'react-icons/hi';

export default function Agenda() {
    // State Mockup untuk Bulan (Hanya visual)
    const [currentMonth, setCurrentMonth] = useState('Oktober');

    // Data Dummy untuk Tanggal (Sesuai Gambar)
    const calendarDays = [
        { day: 28, type: 'prev' },
        { day: 29, type: 'prev' },
        { day: 30, type: 'prev' },
        { day: 1, type: 'curr' },
        { day: 2, type: 'curr' },
        { day: 3, type: 'curr' },
        { day: 4, type: 'curr' },
        { day: 5, type: 'curr', active: true }, // Tanggal 5 Aktif
        { day: 6, type: 'curr' },
        { day: 7, type: 'curr', dot: 'blue' }, // Ada dot biru
        { day: 8, type: 'curr' },
        { day: 9, type: 'curr' },
        { day: 10, type: 'curr', dot: 'green' }, // Ada dot hijau
        { day: 11, type: 'curr' },
        { day: 12, type: 'curr' },
        { day: 13, type: 'curr' },
        { day: 14, type: 'curr' },
        { day: 15, type: 'curr' },
        { day: 16, type: 'curr' },
        { day: 17, type: 'curr' },
        { day: 18, type: 'curr' },
        { day: 19, type: 'curr' },
        { day: 20, type: 'curr' },
        { day: 21, type: 'curr', dot: 'red' }, // Ada dot merah
        { day: 22, type: 'curr' },
        { day: 23, type: 'curr' },
        { day: 24, type: 'curr' },
        { day: 25, type: 'curr' },
        { day: 26, type: 'curr' },
        { day: 27, type: 'curr' },
        { day: 28, type: 'curr' },
        { day: 29, type: 'curr' },
        { day: 30, type: 'curr' },
        { day: 31, type: 'curr' },
    ];

    const weekDays = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

    return (
        <AdminDashboardLayout>
            <Head title="Agenda Admin" />

            <div className="space-y-6">
                {/* === HEADER PAGE === */}
                <div className="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                    <h1 className="text-3xl font-bold text-blue-900">
                        Kalender
                    </h1>

                    <button className="flex items-center justify-center gap-2 rounded-2xl bg-yellow-400 px-6 py-3 font-bold text-blue-900 shadow-md transition-all hover:bg-yellow-500">
                        <HiPlus className="h-5 w-5" />
                        Tambah Agenda
                    </button>
                </div>

                <div className="grid grid-cols-1 gap-8 lg:grid-cols-3">
                    {/* === KOLOM KIRI: KALENDER UTAMA === */}
                    <div className="rounded-[2rem] bg-white p-8 shadow-sm lg:col-span-2">
                        {/* Navigasi Bulan */}
                        <div className="mb-8 flex items-center justify-between px-4">
                            <button className="rounded-full p-2 transition hover:bg-gray-100">
                                <HiChevronLeft className="h-6 w-6 text-gray-600" />
                            </button>
                            <h2 className="text-2xl font-bold text-blue-900">
                                {currentMonth}
                            </h2>
                            <button className="rounded-full p-2 transition hover:bg-gray-100">
                                <HiChevronRight className="h-6 w-6 text-gray-600" />
                            </button>
                        </div>

                        {/* Grid Kalender */}
                        <div className="w-full">
                            {/* Nama Hari */}
                            <div className="mb-4 grid grid-cols-7">
                                {weekDays.map((day) => (
                                    <div
                                        key={day}
                                        className="text-center text-sm font-medium text-gray-400"
                                    >
                                        {day}
                                    </div>
                                ))}
                            </div>

                            {/* Angka Tanggal */}
                            <div className="grid grid-cols-7 gap-x-2 gap-y-6">
                                {calendarDays.map((item, index) => (
                                    <div
                                        key={index}
                                        className="relative flex h-10 flex-col items-center justify-center"
                                    >
                                        <div
                                            className={`flex h-10 w-10 cursor-pointer items-center justify-center rounded-2xl text-sm font-semibold transition-all md:h-10 md:w-14 ${item.type === 'prev' ? 'text-gray-300' : ''} ${item.type === 'curr' && !item.active ? 'text-gray-700 hover:bg-gray-50' : ''} ${item.active ? 'bg-yellow-500 text-white shadow-md shadow-yellow-200' : ''} `}
                                        >
                                            {item.day}
                                        </div>

                                        {/* Indikator Dot Event */}
                                        {item.dot && (
                                            <span
                                                className={`absolute -bottom-2 h-1.5 w-1.5 rounded-full ${item.dot === 'blue' ? 'bg-blue-500' : ''} ${item.dot === 'green' ? 'bg-green-500' : ''} ${item.dot === 'red' ? 'bg-red-500' : ''} `}
                                            ></span>
                                        )}
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>

                    {/* === KOLOM KANAN: LIST AGENDA === */}
                    <div className="rounded-[2rem] bg-white p-8 shadow-sm lg:col-span-1">
                        <h3 className="mb-6 text-xl font-bold text-blue-900">
                            Agenda Terdekat
                        </h3>

                        <div className="space-y-4">
                            {/* Item 1 */}
                            <div className="group flex cursor-pointer items-start gap-4 rounded-2xl bg-gray-50 p-4 transition-colors hover:bg-blue-50">
                                <div className="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 transition group-hover:bg-blue-200">
                                    <HiMicrophone className="h-6 w-6 text-blue-600" />
                                </div>
                                <div>
                                    <h4 className="text-sm font-bold text-blue-900">
                                        Talkshow Inspiratif
                                    </h4>
                                    <p className="mt-1 text-xs text-gray-500">
                                        7 Okt 2025, 10:00 - 12:00
                                    </p>
                                </div>
                            </div>

                            {/* Item 2 */}
                            <div className="group flex cursor-pointer items-start gap-4 rounded-2xl bg-gray-50 p-4 transition-colors hover:bg-green-50">
                                <div className="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-100 transition group-hover:bg-green-200">
                                    <HiVideoCamera className="h-6 w-6 text-green-600" />
                                </div>
                                <div>
                                    <h4 className="text-sm font-bold text-blue-900">
                                        Webinar Digital Marketing
                                    </h4>
                                    <p className="mt-1 text-xs text-gray-500">
                                        10 Okt 2025, 14:00 - 15:30
                                    </p>
                                </div>
                            </div>

                            {/* Item 3 */}
                            <div className="group flex cursor-pointer items-start gap-4 rounded-2xl bg-gray-50 p-4 transition-colors hover:bg-red-50">
                                <div className="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 transition group-hover:bg-red-200">
                                    <HiUserGroup className="h-6 w-6 text-red-600" />
                                </div>
                                <div>
                                    <h4 className="text-sm font-bold text-blue-900">
                                        Rapat Tim Bulanan
                                    </h4>
                                    <p className="mt-1 text-xs text-gray-500">
                                        21 Okt 2025, 09:00 - 11:00
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AdminDashboardLayout>
    );
}
