import AdminDashboardLayout from '@/layouts/dashboardLayoutAdmin'; // Pastikan path layout benar
import { Head, Link } from '@inertiajs/react';
import {
    HiAdjustments,
    HiArrowSmRight,
    HiBell,
    HiShieldCheck,
    HiTicket,
    HiUsers,
    HiViewList,
} from 'react-icons/hi';

export default function Settings() {
    // Data Menu Pengaturan
    const settingsMenu = [
        {
            title: 'Pengaturan Umum',
            desc: 'Kelola nama aplikasi, logo, informasi kontak, dan mode pemeliharaan.',
            icon: HiAdjustments,
            color: 'blue', // Biru
            href: '#',
        },
        {
            title: 'Manajemen Tim & Akses',
            desc: 'Tambah atau hapus anggota tim dan atur peran serta hak akses mereka.',
            icon: HiUsers,
            color: 'green', // Hijau
            href: '#',
        },
        {
            title: 'Bonus & Promo',
            desc: 'Buat dan kelola kode promo, diskon, dan penetapan bonus untuk agen.',
            icon: HiTicket,
            color: 'red', // Merah
            href: '#',
        },
        {
            title: 'Keamanan & Hak Akses',
            desc: 'Atur kebijakan kata sandi dan hak akses khusus. Akses level Owner diperlukan.',
            icon: HiShieldCheck,
            color: 'orange', // Kuning/Oranye
            href: '#',
        },
        {
            title: 'Notifikasi',
            desc: 'Kelola template dan trigger notifikasi email untuk pengguna dan admin.',
            icon: HiBell,
            color: 'purple', // Ungu
            href: '#',
        },
        {
            title: 'Jenis Tes & Paket',
            desc: 'Edit, tambah, atau hapus jenis tes dan paket yang ditawarkan kepada pengguna.',
            icon: HiViewList,
            color: 'indigo', // Biru Tua/Indigo
            href: '#',
        },
    ];

    // Helper function untuk mendapatkan class warna
    const getColorClass = (color: string) => {
        switch (color) {
            case 'blue':
                return 'bg-blue-100 text-blue-600';
            case 'green':
                return 'bg-green-100 text-green-600';
            case 'red':
                return 'bg-red-100 text-red-600';
            case 'orange':
                return 'bg-orange-100 text-orange-600';
            case 'purple':
                return 'bg-purple-100 text-purple-600';
            case 'indigo':
                return 'bg-indigo-100 text-indigo-600';
            default:
                return 'bg-gray-100 text-gray-600';
        }
    };

    return (
        <AdminDashboardLayout>
            <Head title="Pengaturan Sistem" />

            <div className="space-y-8 font-poppins">
                <h2 className="text-3xl font-bold text-blue-900">
                    Pengaturan Sistem
                </h2>

                {/* GRID CARD PENGATURAN */}
                <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    {settingsMenu.map((item, index) => (
                        <div
                            key={index}
                            className="group rounded-[2rem] border border-transparent bg-white p-8 shadow-sm transition-all hover:border-gray-200 hover:shadow-md"
                        >
                            <div className="flex items-start gap-4">
                                {/* Icon Circle */}
                                <div
                                    className={`flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full ${getColorClass(item.color)}`}
                                >
                                    <item.icon className="h-6 w-6" />
                                </div>

                                {/* Content */}
                                <div className="space-y-3">
                                    <h3 className="text-lg font-bold text-gray-900">
                                        {item.title}
                                    </h3>
                                    <p className="min-h-[40px] text-sm leading-relaxed text-gray-500">
                                        {item.desc}
                                    </p>

                                    {/* Link Button */}
                                    <Link
                                        href={item.href}
                                        className="mt-2 inline-flex items-center text-sm font-bold text-yellow-600 transition-transform group-hover:translate-x-1 hover:text-yellow-700"
                                    >
                                        Kelola Pengaturan
                                        <HiArrowSmRight className="ml-1 h-5 w-5" />
                                    </Link>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </AdminDashboardLayout>
    );
}
