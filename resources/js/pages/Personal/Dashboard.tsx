import DashboardLayout from '@/layouts/dashboard-layout-personal'; // Import Layout
import { Head, Link } from '@inertiajs/react'; // GANTI INI
import { useState } from 'react';
import {
    HiCreditCard,
    HiDownload,
    HiFire,
    HiQuestionMarkCircle,
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

export default function Dashboard() {
    // Mock Data State
    const [isLoading, setIsLoading] = useState(false);
    const [latestResult, setLatestResult] = useState<LatestResult | null>({
        id: 1,
        character_type_name: 'Sang Analis',
        character_type_code: 'SA',
        description:
            'Ini adalah deskripsi palsu dari mock data untuk kebutuhan development.',
        strengths: ['Logis', 'Detail', 'Terstruktur'],
        challenges: ['Terlalu kaku', 'Sulit bersosialisasi'],
        communication_style: 'To the point',
    });

    return (
        <DashboardLayout>
            <Head title="Dashboard Personal" />

            <div className="grid grid-cols-1 gap-8 lg:grid-cols-3">
                {/* Left Column */}
                <div className="space-y-8 lg:col-span-2">
                    {/* Character Profile Card */}
                    {isLoading ? (
                        <div className="animate-pulse rounded-lg bg-white p-6 shadow-md">
                            Loading...
                        </div>
                    ) : latestResult ? (
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
                            <p>Belum ada hasil tes.</p>
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
                    {/* Download */}
                    <div className="rounded-lg bg-white p-6 text-center shadow-md">
                        <button className="flex w-full items-center justify-center rounded-lg bg-saintara-black px-4 py-2.5 text-sm font-medium text-white hover:bg-gray-800">
                            <HiDownload className="mr-2 h-5 w-5" /> Unduh Hasil
                            Tes
                        </button>
                    </div>

                    {/* Strengths */}
                    {latestResult && (
                        <div className="rounded-lg bg-white p-6 shadow-md">
                            <h4 className="mb-2 font-semibold text-gray-800">
                                Kekuatan
                            </h4>
                            <ul className="list-inside list-disc text-sm text-gray-600">
                                {latestResult.strengths.map((s, i) => (
                                    <li key={i}>{s}</li>
                                ))}
                            </ul>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
