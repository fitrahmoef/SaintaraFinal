import { Link } from '@inertiajs/react';
import React from 'react';
import {
    HiBell,
    HiCalendar,
    HiCog,
    HiCurrencyDollar,
    HiHome,
    HiLogout,
    HiQuestionMarkCircle,
    HiUserGroup,
    HiUsers,
} from 'react-icons/hi';

export default function DashboardLayout({
    children,
}: {
    children: React.ReactNode;
}) {
    // Data Mock User (Sementara)
    const user = { name: 'User' };
    const logout = () => console.log('Logout logic placeholder');

    // Daftar Menu Sidebar agar kodingan lebih rapi
    const menuItems = [
        {
            name: 'Dashboard',
            href: '/admin/dashboardAdmin',
            icon: HiHome,
        },
        { name: 'Agenda', href: '/admin/agendaAdmin', icon: HiCalendar },
        { name: 'Pengguna', href: '/admin/penggunaAdmin', icon: HiUsers },
        { name: 'Keuangan', href: '/admin/keuanganAdmin', icon: HiCurrencyDollar },
        { name: 'Tim', href: '/admin/teamAdmin', icon: HiUserGroup },
        {
            name: 'Bantuan & Layanan',
            href: '/admin/supportAdmin',
            icon: HiQuestionMarkCircle,
        },
        { name: 'Pengaturan', href: '/admin/settingsAdmin', icon: HiCog },
    ];

    return (
        <div className="flex min-h-screen bg-gray-50 font-poppins">
            {/* === SIDEBAR === */}
            <aside className="flex w-64 flex-shrink-0 flex-col bg-saintara-black text-white transition-all duration-300">
                {/* Logo */}
                <div className="flex h-20 items-center justify-center border-b border-gray-700">
                    <Link href="/">
                        <h1 className="cursor-pointer text-2xl font-bold tracking-wider text-white transition-colors hover:text-saintara-yellow">
                            SAINTARA
                        </h1>
                    </Link>
                </div>

                {/* Menu Items */}
                <nav className="flex-1 space-y-2 px-4 py-6">
                    {menuItems.map((item) => (
                        <Link
                            key={item.name}
                            href={item.href}
                            className="group flex items-center rounded-lg px-4 py-2.5 text-gray-300 transition-colors hover:bg-gray-700 hover:text-white"
                        >
                            <item.icon className="mr-3 h-6 w-6 text-gray-400 transition-colors group-hover:text-saintara-yellow" />
                            {item.name}
                        </Link>
                    ))}
                </nav>

                {/* Logout Button */}
                <div className="border-t border-gray-700 px-4 py-4">
                    <button
                        onClick={logout}
                        className="flex w-full items-center rounded-lg px-4 py-2.5 text-gray-300 transition-colors hover:bg-gray-700 hover:text-white"
                    >
                        <HiLogout className="mr-3 h-6 w-6 text-gray-400 group-hover:text-red-400" />
                        Logout
                    </button>
                </div>
            </aside>

            {/* === MAIN CONTENT WRAPPER === */}
            <div className="flex flex-1 flex-col overflow-hidden">
                {/* === HEADER === */}
                <header className="flex h-20 items-center justify-between border-b border-gray-100 bg-white px-8 shadow-sm">
                    <h2 className="text-2xl font-bold text-gray-800">
                        Selamat datang, {user?.name || 'User'}!
                    </h2>
                    <div className="flex items-center space-x-4">
                        <button
                            type="button"
                            className="rounded-full p-2 text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700"
                        >
                            <HiBell className="h-6 w-6" />
                        </button>
                        <div className="flex h-10 w-10 items-center justify-center rounded-full bg-saintara-yellow font-bold text-white shadow-md">
                            {user?.name?.charAt(0).toUpperCase()}
                        </div>
                    </div>
                </header>

                {/* === KONTEN HALAMAN === */}
                <main className="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-8">
                    {children}
                </main>
            </div>
        </div>
    );
}
