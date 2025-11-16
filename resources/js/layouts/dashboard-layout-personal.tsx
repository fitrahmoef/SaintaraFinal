import { Link } from '@inertiajs/react';
import React from 'react';
import { 
    HiBell, HiHome, HiLogout, HiUser, HiGift, HiCog, HiShoppingCart, HiCheckCircle,
    HiClipboard, HiChat 
 } from 'react-icons/hi';

export default function DashboardLayout({
    children,
}: {
    children: React.ReactNode;
}) {
    // Data Mock User (Sementara)
    const user = { name: 'Budi' };
    const logout = () => console.log('Logout logic placeholder');

    return (
        <div className="flex min-h-screen bg-gray-50 font-poppins">
            {/* === SIDEBAR === */}
            <aside className="flex w-64 flex-shrink-0 flex-col bg-saintara-black text-white">
                <div className="flex h-20 items-center justify-center border-b border-gray-700">
                    <Link href="/">
                        <h1 className="cursor-pointer text-2xl font-bold tracking-wider text-white hover:text-saintara-yellow">
                            SAINTARA
                        </h1>
                    </Link>
                </div>

                <div className="flex flex-1 flex-col overflow-y-auto">     
                    <nav className="flex-1 space-y-2 px-4 py-6">
                        <Link
                            href="/personal/dashboardPersonal" // Sesuaikan dengan route di web.php
                            className="flex items-center rounded-lg px-4 py-2.5 text-gray-300 hover:bg-gray-700"
                        >
                            <HiHome className="mr-3 h-6 w-6" /> Beranda
                        </Link>
                        <Link
                            href="/personal/profilePersonal"
                            className="flex items-center rounded-lg px-4 py-2.5 text-gray-300 hover:bg-gray-700"
                        >
                            <HiUser className="mr-3 h-6 w-6" /> Profil
                        </Link>
                        <Link
                            href="/personal/daftarTes" // Sesuaikan dengan route di web.php
                            className="flex items-center rounded-lg px-4 py-2.5 text-gray-300 hover:bg-gray-700"
                        >
                            <HiClipboard className="mr-3 h-6 w-6" /> Daftar Tes Karakter
                        </Link>
                        <Link
                            href="/personal/transaksiToken" // Sesuaikan dengan route di web.php
                            className="flex items-center rounded-lg px-4 py-2.5 text-gray-300 hover:bg-gray-700"
                        >
                            <HiShoppingCart className="mr-3 h-6 w-6" /> Transaksi dan Token
                        </Link>
                        <Link
                            href="/personal/hasilTes" // Sesuaikan dengan route di web.php
                            className="flex items-center rounded-lg px-4 py-2.5 text-gray-300 hover:bg-gray-700"
                        >
                            <HiCheckCircle className="mr-3 h-6 w-6" /> Hasil Tes
                        </Link>
                        <Link
                            href="/personal/bantuan" // Sesuaikan dengan route di web.php
                            className="flex items-center rounded-lg px-4 py-2.5 text-gray-300 hover:bg-gray-700"
                        >
                            <HiChat className="mr-3 h-6 w-6" /> Bantuan dan Layanan
                        </Link>
                        <Link
                            href="/personal/hadiahDonasi" // Sesuaikan dengan route di web.php
                            className="flex items-center rounded-lg px-4 py-2.5 text-gray-300 hover:bg-gray-700"
                        >
                            <HiGift className="mr-3 h-6 w-6" /> Hadiah dan Donasi
                        </Link>
                        <Link
                            href="/personal/setting" // Sesuaikan dengan route di web.php
                            className="flex items-center rounded-lg px-4 py-2.5 text-gray-300 hover:bg-gray-700"
                        >
                            <HiCog className="mr-3 h-6 w-6" /> Pengaturan
                        </Link>
                    </nav>          
                </div>

                <div className="border-t border-gray-700 px-4 py-4 flex-shrink-0">
                    <button
                        onClick={logout}
                        className="flex w-full items-center rounded-lg px-4 py-2.5 text-gray-300 hover:bg-gray-700 hover:text-white"
                    >
                        <HiLogout className="mr-3 h-6 w-6" /> Logout
                    </button>
                </div>
            </aside>

            {/* === MAIN CONTENT WRAPPER === */}
            <div className="flex flex-1 flex-col overflow-hidden">
                {/* === HEADER === */}
                <header className="flex h-20 items-center justify-between bg-white px-8 shadow-sm">
                    <h2 className="text-2xl font-bold text-gray-800">
                        Selamat datang, {user?.name || 'User'}!
                    </h2>
                    <div className="flex items-center space-x-4">
                        <button
                            type="button"
                            className="rounded-full p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700"
                        >
                            <HiBell className="h-6 w-6" />
                        </button>
                        <div className="flex h-10 w-10 items-center justify-center rounded-full bg-saintara-yellow font-bold text-white">
                            {user?.name?.charAt(0).toUpperCase()}
                        </div>
                    </div>
                </header>

                {/* === KONTEN HALAMAN === */}
                <main className="flex-1 overflow-x-hidden overflow-y-auto p-8">
                    {children}
                </main>
            </div>
        </div>
    );
}
