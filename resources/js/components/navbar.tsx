import { Link, usePage } from '@inertiajs/react'; // Gunakan Link dan usePage dari Inertia
import { useState } from 'react';
import { HiLogout, HiMenu, HiUser, HiX } from 'react-icons/hi';

export default function Navbar() {
    const [isMenuOpen, setIsMenuOpen] = useState(false);
    const [isUserMenuOpen, setIsUserMenuOpen] = useState(false);

    // Mengambil data user dari props Laravel (Inertia)
    // 'auth' adalah prop standar yang dikirim Laravel
    const { auth } = usePage().props as any;
    const user = auth.user;

    return (
        <nav className="fixed top-4 z-50 w-full px-4 transition-all duration-300 md:top-10">
            <div className="mx-auto flex max-w-screen-xl flex-wrap items-center justify-between rounded-full border border-gray-100 bg-white/90 p-4 shadow-xl backdrop-blur-md">
                {/* Logo */}
                <Link href="/" className="flex items-center space-x-3">
                    <img
                        src="/assets/logo/4.png" // Pastikan file ada di folder public/assets/logo
                        alt="Logo Saintara"
                        className="h-8 w-8 object-contain"
                    />
                    <span className="font-poppins self-center text-xl font-extrabold tracking-wide whitespace-nowrap text-gray-900 uppercase md:text-2xl">
                        SAINTARA
                    </span>
                </Link>

                {/* Tombol Aksi (Kanan) */}
                <div className="flex items-center space-x-3 md:order-2">
                    {user ? (
                        <div className="relative">
                            <button
                                onClick={() =>
                                    setIsUserMenuOpen(!isUserMenuOpen)
                                }
                                className="flex items-center space-x-2 text-sm font-medium text-gray-700 transition hover:text-yellow-500"
                            >
                                <div className="flex h-9 w-9 items-center justify-center rounded-full bg-yellow-400 shadow-sm">
                                    <HiUser className="h-5 w-5 text-white" />
                                </div>
                                <span className="hidden font-semibold md:block">
                                    {user.name}
                                </span>
                            </button>

                            {/* Dropdown Menu User */}
                            {isUserMenuOpen && (
                                <div className="absolute right-0 mt-3 w-48 overflow-hidden rounded-xl border border-gray-100 bg-white py-2 shadow-2xl">
                                    <Link
                                        href="/dashboard"
                                        className="block px-4 py-2 text-sm text-gray-700 transition hover:bg-gray-50"
                                        onClick={() => setIsUserMenuOpen(false)}
                                    >
                                        Dashboard
                                    </Link>
                                    <Link
                                        href="/logout"
                                        method="post" // Penting: Logout di Laravel harus method POST
                                        as="button"
                                        className="block w-full px-4 py-2 text-left text-sm text-red-600 transition hover:bg-red-50"
                                    >
                                        <HiLogout className="mr-2 inline h-4 w-4" />
                                        Logout
                                    </Link>
                                </div>
                            )}
                        </div>
                    ) : (
                        <>
                            <Link
                                href="/register"
                                className="transform rounded-full bg-yellow-500 px-6 py-2.5 text-center text-sm font-medium text-white shadow-lg transition-all duration-300 hover:-translate-y-0.5 hover:bg-yellow-600 hover:shadow-xl focus:ring-4 focus:ring-yellow-300"
                            >
                                DAFTAR
                            </Link>

                            <Link
                                href="/login"
                                className="transform rounded-full bg-gray-900 px-6 py-2.5 text-center text-sm font-medium text-white shadow-lg transition-all duration-300 hover:-translate-y-0.5 hover:bg-black hover:shadow-xl focus:ring-4 focus:ring-gray-300"
                            >
                                MASUK
                            </Link>
                        </>
                    )}

                    {/* Mobile Menu Button */}
                    <button
                        onClick={() => setIsMenuOpen(!isMenuOpen)}
                        type="button"
                        className="inline-flex h-10 w-10 items-center justify-center rounded-lg p-2 text-gray-500 hover:bg-gray-100 focus:outline-none md:hidden"
                    >
                        <span className="sr-only">Open main menu</span>
                        {isMenuOpen ? (
                            <HiX className="h-6 w-6" />
                        ) : (
                            <HiMenu className="h-6 w-6" />
                        )}
                    </button>
                </div>

                {/* Link Navigasi */}
                <div
                    className={`${isMenuOpen ? 'mt-4 block rounded-2xl border border-gray-100 bg-white p-4 shadow-lg' : 'hidden'} w-full md:order-1 md:mt-0 md:flex md:w-auto md:border-0 md:bg-transparent md:shadow-none`}
                >
                    <ul className="flex flex-col font-medium md:flex-row md:space-x-8">
                        {[
                            'Tentang',
                            'Produk',
                            'Pelatihan & Acara',
                            'Rahasia Karakter Alami',
                        ].map((item, idx) => {
                            // Mapping link hash manual sederhana
                            const href =
                                item === 'Pelatihan & Acara'
                                    ? '/calendar'
                                    : `/#${item.toLowerCase().replace(/ /g, '-')}`;
                            return (
                                <li key={idx}>
                                    <Link
                                        href={href}
                                        className="block rounded px-3 py-2 text-gray-700 transition-colors hover:bg-gray-100 md:p-0 md:hover:bg-transparent md:hover:text-yellow-500"
                                    >
                                        {item}
                                    </Link>
                                </li>
                            );
                        })}
                    </ul>
                </div>
            </div>
        </nav>
    );
}
