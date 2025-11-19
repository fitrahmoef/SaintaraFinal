import DashboardLayout from '@/layouts/dashboard-layout-personal';
import { Head, Link } from '@inertiajs/react';
import {
    HiCreditCard,
    HiDownload,
    HiFire,
    HiQuestionMarkCircle,
    HiGift,
} from 'react-icons/hi';

// Tipe Data
interface LatestResult {
    id: number;
    character_type_name: string;
    character_type_code: string;
    description: string;
    strengths: string[];
    challenges: string[];
    communication_style: string;
}

interface TokenBalance {
    total: number;
    used: number;
    available: number;
    free_tokens: number;
    purchased_available: number;
}

interface DashboardProps {
    latestResult: LatestResult | null;
    tokenBalance: TokenBalance;
}

export default function Dashboard({ latestResult, tokenBalance }: DashboardProps) {

    return (
        <DashboardLayout>
            <Head title="Dashboard Personal" />

            <div className="grid grid-cols-1 gap-8 lg:grid-cols-3">
                {/* Left Column */}
                <div className="space-y-8 lg:col-span-2">
                    {/* Character Profile Card */}
                    {latestResult ? (
                        <div className="flex flex-col items-center rounded-lg bg-white p-6 shadow-md md:flex-row">
                            <div className="mb-4 flex-shrink-0 text-center md:mr-6 md:mb-0 md:text-left">
                                <div className="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-br from-saintara-yellow to-yellow-200">
                                    <span className="text-2xl font-bold">
                                        {latestResult.character_type_code}
                                    </span>
                                </div>
                                <h4 className="mt-2 text-xs text-gray-500">
                                    {latestResult.character_type_name.toUpperCase()}
                                </h4>
                                <h3 className="text-xl font-bold text-saintara-black">
                                    Karakter Alami Anda
                                </h3>
                            </div>
                            <div className="border-t border-gray-200 pt-4 pl-6 md:border-t-0 md:border-l md:pt-0">
                                <div className="mb-2 flex items-center">
                                    <HiFire className="mr-2 h-6 w-6 text-saintara-yellow" />
                                    <h4 className="font-semibold text-gray-700">
                                        Sekilas Tentang Anda
                                    </h4>
                                </div>
                                <p className="text-sm text-gray-600">
                                    {latestResult.description}
                                </p>
                            </div>
                        </div>
                    ) : (
                        <div className="rounded-lg bg-white p-6 text-center shadow-md">
                            <p className="text-gray-500">Belum ada hasil tes.</p>
                            <Link
                                href="/personal/daftarTes"
                                className="mt-4 inline-block rounded-lg bg-saintara-yellow px-6 py-2 text-sm font-semibold text-white hover:bg-yellow-500"
                            >
                                Mulai Tes Sekarang
                            </Link>
                        </div>
                    )}

                    {/* Free Token Welcome Card */}
                    {tokenBalance.free_tokens > 0 && !latestResult && (
                        <div className="rounded-lg bg-gradient-to-r from-green-50 to-blue-50 p-6 shadow-md border-2 border-green-200">
                            <div className="flex items-start">
                                <HiGift className="h-8 w-8 text-green-600 mr-3 mt-1 flex-shrink-0" />
                                <div>
                                    <h3 className="text-lg font-bold text-gray-800 mb-2">
                                        Selamat Datang! Anda Mendapat Token Gratis!
                                    </h3>
                                    <p className="text-sm text-gray-700 mb-3">
                                        Sebagai pengguna baru, Anda mendapat {tokenBalance.free_tokens} token gratis untuk mencoba tes karakter pertama Anda.
                                        Jangan lewatkan kesempatan untuk mengenal diri Anda lebih dalam!
                                    </p>
                                    <Link
                                        href="/personal/daftarTes"
                                        className="inline-block rounded-lg bg-green-600 px-6 py-2 text-sm font-semibold text-white hover:bg-green-700"
                                    >
                                        Gunakan Token Gratis Sekarang →
                                    </Link>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Action Cards */}
                    <div className="rounded-lg bg-white p-6 shadow-md">
                        <h3 className="mb-4 text-xl font-bold text-saintara-black">
                            Laporan Saintara
                        </h3>
                        <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div className="flex items-center rounded-lg bg-yellow-50 p-4">
                                <div className="mr-4 flex h-16 w-16 items-center justify-center rounded-lg bg-saintara-yellow">
                                    <HiCreditCard className="h-8 w-8 text-white" />
                                </div>
                                <div>
                                    <h4 className="font-semibold text-gray-800">
                                        Beli Token
                                    </h4>
                                    <Link
                                        href="/personal/transaksi-token"
                                        className="mt-1 text-sm font-semibold text-saintara-yellow hover:underline"
                                    >
                                        Beli Sekarang →
                                    </Link>
                                </div>
                            </div>
                            {/* Card kedua */}
                            <div className="flex items-center rounded-lg bg-yellow-50 p-4">
                                <div className="mr-4 flex h-16 w-16 items-center justify-center rounded-lg bg-saintara-black">
                                    <HiQuestionMarkCircle className="h-8 w-8 text-saintara-yellow" />
                                </div>
                                <div>
                                    <h4 className="font-semibold text-gray-800">
                                        Tanya AI
                                    </h4>
                                    <Link
                                        href="/personal/ai-chat"
                                        className="mt-1 text-sm font-semibold text-saintara-yellow hover:underline"
                                    >
                                        Mulai Bertanya →
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Right Column */}
                <div className="space-y-8 lg:col-span-1">
                    {/* Token Balance Card */}
                    <div className="rounded-lg bg-gradient-to-br from-saintara-yellow to-yellow-400 p-6 shadow-lg">
                        <h4 className="mb-4 text-sm font-semibold text-white">
                            Saldo Token Anda
                        </h4>
                        <div className="mb-4">
                            <div className="mb-1 flex items-baseline">
                                <span className="text-4xl font-bold text-white">
                                    {tokenBalance.available}
                                </span>
                                <span className="ml-2 text-sm text-yellow-100">
                                    / {tokenBalance.total} Token
                                </span>
                            </div>
                            {tokenBalance.free_tokens > 0 && (
                                <div className="mt-2 flex items-center gap-2">
                                    <HiGift className="h-4 w-4 text-white" />
                                    <span className="text-xs font-semibold text-white">
                                        {tokenBalance.free_tokens} Token Gratis!
                                    </span>
                                </div>
                            )}
                            <p className="mt-2 text-xs text-yellow-100">
                                {tokenBalance.used} token telah digunakan
                            </p>
                        </div>
                        <div className="h-2 overflow-hidden rounded-full bg-yellow-200">
                            <div
                                className="h-full rounded-full bg-white transition-all"
                                style={{
                                    width: `${tokenBalance.total > 0 ? (tokenBalance.available / tokenBalance.total) * 100 : 0}%`,
                                }}
                            ></div>
                        </div>
                    </div>

                    {/* Download */}
                    {latestResult && (
                        <div className="rounded-lg bg-white p-6 text-center shadow-md">
                            <button className="flex w-full items-center justify-center rounded-lg bg-saintara-black px-4 py-2.5 text-sm font-medium text-white hover:bg-gray-800">
                                <HiDownload className="mr-2 h-5 w-5" /> Unduh Hasil
                                Tes
                            </button>
                        </div>
                    )}

                    {/* Strengths */}
                    {latestResult && (
                        <div className="rounded-lg bg-white p-6 shadow-md">
                            <h4 className="mb-2 font-semibold text-gray-800">
                                Kekuatan Anda
                            </h4>
                            <ul className="list-inside list-disc text-sm text-gray-600">
                                {latestResult.strengths.map((s, i) => (
                                    <li key={i}>{s}</li>
                                ))}
                            </ul>
                        </div>
                    )}

                    {/* Challenges */}
                    {latestResult && latestResult.challenges && latestResult.challenges.length > 0 && (
                        <div className="rounded-lg bg-white p-6 shadow-md">
                            <h4 className="mb-2 font-semibold text-gray-800">
                                Tantangan
                            </h4>
                            <ul className="list-inside list-disc text-sm text-gray-600">
                                {latestResult.challenges.map((c, i) => (
                                    <li key={i}>{c}</li>
                                ))}
                            </ul>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
