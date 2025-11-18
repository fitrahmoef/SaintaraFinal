import React, { useState, useEffect } from "react";
import DashboardLayout from "@/layouts/dashboard-layout-personal";
import { Head } from "@inertiajs/react";
import axios from "axios";
import { HiDownload, HiEye, HiChartBar, HiCheckCircle } from "react-icons/hi";

interface TestResult {
    id: number;
    test_name: string;
    karakter: string;
    deskripsi: string;
    skor: number;
    tanggal: string;
    durasi: string;
    certificate: {
        nomor: string;
        url: string;
    } | null;
}

interface ResultDetail {
    id: number;
    test: {
        nama: string;
        jenis: string;
    };
    karakter: string;
    deskripsi: string;
    skor: number;
    analisis: any;
    tanggal: string;
    durasi: string;
    certificate: {
        nomor: string;
        tanggal_terbit: string;
        url: string;
    } | null;
}

export default function HasilTes() {
    const [results, setResults] = useState<TestResult[]>([]);
    const [selectedResult, setSelectedResult] = useState<ResultDetail | null>(null);
    const [loading, setLoading] = useState(true);
    const [detailLoading, setDetailLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        fetchResults();
    }, []);

    const fetchResults = async () => {
        try {
            setLoading(true);
            setError(null);

            const response = await axios.get('/api/personal/results');

            if (response.data && response.data.results) {
                setResults(response.data.results);

                // Auto-select latest result if available
                if (response.data.results.length > 0) {
                    fetchResultDetail(response.data.results[0].id);
                }
            }
        } catch (err: any) {
            console.error('Error fetching results:', err);
            setError('Gagal memuat hasil tes. Silakan refresh halaman.');
        } finally {
            setLoading(false);
        }
    };

    const fetchResultDetail = async (id: number) => {
        try {
            setDetailLoading(true);
            const response = await axios.get(`/api/personal/results/${id}`);

            if (response.data && response.data.result) {
                setSelectedResult(response.data.result);
            }
        } catch (err: any) {
            console.error('Error fetching result detail:', err);
        } finally {
            setDetailLoading(false);
        }
    };

    const handleDownloadCertificate = (certificateUrl: string) => {
        window.open(certificateUrl, '_blank');
    };

    const getSkorColor = (skor: number): string => {
        if (skor >= 80) return 'text-green-600';
        if (skor >= 60) return 'text-yellow-600';
        return 'text-red-600';
    };

    const getSkorLabel = (skor: number): string => {
        if (skor >= 80) return 'Sangat Baik';
        if (skor >= 60) return 'Baik';
        if (skor >= 40) return 'Cukup';
        return 'Perlu Ditingkatkan';
    };

    if (loading) {
        return (
            <DashboardLayout>
                <Head title="Hasil Tes" />
                <div className="flex items-center justify-center h-96">
                    <div className="text-center">
                        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-yellow-400 mx-auto mb-4"></div>
                        <p className="text-gray-600">Memuat hasil tes...</p>
                    </div>
                </div>
            </DashboardLayout>
        );
    }

    if (error) {
        return (
            <DashboardLayout>
                <Head title="Hasil Tes" />
                <div className="p-6">
                    <div className="bg-red-50 border border-red-200 rounded-lg p-4 text-red-700">
                        {error}
                    </div>
                </div>
            </DashboardLayout>
        );
    }

    if (results.length === 0) {
        return (
            <DashboardLayout>
                <Head title="Hasil Tes" />
                <div className="p-6">
                    <div className="bg-white rounded-2xl shadow-md p-12 text-center">
                        <HiChartBar className="text-gray-300 text-6xl mx-auto mb-4" />
                        <h2 className="text-2xl font-bold text-gray-800 mb-2">
                            Belum Ada Hasil Tes
                        </h2>
                        <p className="text-gray-600 mb-6">
                            Anda belum menyelesaikan tes apapun. Mulai tes sekarang untuk melihat hasil analisis karakter Anda!
                        </p>
                        <a
                            href="/personal/daftarTes"
                            className="inline-block bg-yellow-400 hover:bg-yellow-500 text-black font-semibold py-3 px-8 rounded-lg transition"
                        >
                            Mulai Tes Sekarang
                        </a>
                    </div>
                </div>
            </DashboardLayout>
        );
    }

    return (
        <DashboardLayout>
            <Head title="Hasil Tes" />

            <div className="p-6">
                {/* Title */}
                <div className="flex justify-between items-center mb-8">
                    <h1 className="text-2xl font-bold text-[#2A2A2A]">
                        Hasil Tes Anda
                    </h1>
                    <span className="text-gray-600">
                        Total: {results.length} tes
                    </span>
                </div>

                <div className="grid grid-cols-12 gap-6">
                    {/* LEFT SECTION - Detail Hasil */}
                    <div className="col-span-8 space-y-6">
                        {detailLoading ? (
                            <div className="bg-white rounded-2xl shadow-md p-12 text-center">
                                <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-yellow-400 mx-auto mb-4"></div>
                                <p className="text-gray-600">Memuat detail...</p>
                            </div>
                        ) : selectedResult ? (
                            <>
                                {/* CARD: Keterangan Hasil */}
                                <div className="bg-white rounded-2xl shadow-md p-6">
                                    <div className="flex gap-6">
                                        {/* Icon/Badge */}
                                        <div className="flex-shrink-0">
                                            <div className="bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-lg w-24 h-24 flex items-center justify-center">
                                                <HiCheckCircle className="text-white text-5xl" />
                                            </div>
                                        </div>

                                        <div className="flex-1">
                                            <div className="flex justify-between items-start mb-2">
                                                <div>
                                                    <p className="text-gray-500 text-xs mb-1">
                                                        {selectedResult.test.nama} • {selectedResult.tanggal}
                                                    </p>
                                                    <h2 className="text-lg font-semibold text-gray-700">
                                                        Hasil Analisis Karakter
                                                    </h2>
                                                </div>
                                                <div className="text-right">
                                                    <p className="text-xs text-gray-500">Skor Anda</p>
                                                    <p className={`text-3xl font-bold ${getSkorColor(selectedResult.skor)}`}>
                                                        {selectedResult.skor}
                                                    </p>
                                                    <p className={`text-xs ${getSkorColor(selectedResult.skor)}`}>
                                                        {getSkorLabel(selectedResult.skor)}
                                                    </p>
                                                </div>
                                            </div>

                                            <p className="text-gray-500 text-xs mb-2">KARAKTER DOMINAN ANDA</p>

                                            <h3 className="text-2xl font-bold text-gray-900 mb-3">
                                                {selectedResult.karakter}
                                            </h3>

                                            <p className="text-gray-600 text-sm leading-relaxed">
                                                {selectedResult.deskripsi}
                                            </p>

                                            {selectedResult.analisis && (
                                                <div className="mt-4 pt-4 border-t border-gray-200">
                                                    <p className="text-xs text-gray-500 mb-2">
                                                        Durasi Pengerjaan: {selectedResult.durasi}
                                                    </p>
                                                    {selectedResult.certificate && (
                                                        <p className="text-xs text-gray-500">
                                                            No. Sertifikat: {selectedResult.certificate.nomor}
                                                        </p>
                                                    )}
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                </div>

                                {/* CARD: Strengths & Challenges */}
                                {selectedResult.analisis && (
                                    <div className="grid grid-cols-2 gap-6">
                                        {/* Strengths */}
                                        <div className="bg-white rounded-2xl shadow-md p-6">
                                            <h3 className="text-lg font-semibold text-gray-700 mb-4 flex items-center gap-2">
                                                <span className="text-green-500">✓</span>
                                                Kekuatan Anda
                                            </h3>
                                            <ul className="space-y-3 text-gray-700 text-sm">
                                                {selectedResult.analisis.strengths?.map((strength: string, idx: number) => (
                                                    <li key={idx} className="flex gap-2">
                                                        <span className="text-green-500 flex-shrink-0">✔️</span>
                                                        <span>{strength}</span>
                                                    </li>
                                                )) || (
                                                    <li className="text-gray-500 italic">Data kekuatan belum tersedia</li>
                                                )}
                                            </ul>
                                        </div>

                                        {/* Challenges */}
                                        <div className="bg-white rounded-2xl shadow-md p-6">
                                            <h3 className="text-lg font-semibold text-gray-700 mb-4 flex items-center gap-2">
                                                <span className="text-orange-500">!</span>
                                                Tantangan untuk Berkembang
                                            </h3>
                                            <ul className="space-y-3 text-gray-700 text-sm">
                                                {selectedResult.analisis.challenges?.map((challenge: string, idx: number) => (
                                                    <li key={idx} className="flex gap-2">
                                                        <span className="text-orange-500 flex-shrink-0">🔸</span>
                                                        <span>{challenge}</span>
                                                    </li>
                                                )) || (
                                                    <li className="text-gray-500 italic">Data tantangan belum tersedia</li>
                                                )}
                                            </ul>
                                        </div>
                                    </div>
                                )}

                                {/* Additional Analysis */}
                                {selectedResult.analisis?.communication_style && (
                                    <div className="bg-white rounded-2xl shadow-md p-6">
                                        <h3 className="text-lg font-semibold text-gray-700 mb-3">
                                            Gaya Komunikasi
                                        </h3>
                                        <p className="text-gray-600 text-sm leading-relaxed">
                                            {selectedResult.analisis.communication_style}
                                        </p>
                                    </div>
                                )}
                            </>
                        ) : (
                            <div className="bg-white rounded-2xl shadow-md p-12 text-center">
                                <p className="text-gray-600">Pilih hasil tes untuk melihat detail</p>
                            </div>
                        )}
                    </div>

                    {/* RIGHT SECTION - List & Actions */}
                    <div className="col-span-4 space-y-6">
                        {/* Test Results List */}
                        <div className="bg-white rounded-2xl shadow-md p-6">
                            <h3 className="font-semibold text-gray-800 mb-4">Riwayat Tes</h3>
                            <div className="space-y-3 max-h-96 overflow-y-auto">
                                {results.map((result) => (
                                    <button
                                        key={result.id}
                                        onClick={() => fetchResultDetail(result.id)}
                                        className={`w-full text-left p-4 rounded-lg transition border-2 ${
                                            selectedResult?.id === result.id
                                                ? 'border-yellow-400 bg-yellow-50'
                                                : 'border-gray-200 hover:border-yellow-300 hover:bg-gray-50'
                                        }`}
                                    >
                                        <div className="flex items-start justify-between gap-2">
                                            <div className="flex-1 min-w-0">
                                                <h4 className="font-semibold text-gray-800 text-sm truncate">
                                                    {result.test_name}
                                                </h4>
                                                <p className="text-xs text-gray-600 mt-1">
                                                    {result.karakter}
                                                </p>
                                                <p className="text-xs text-gray-500 mt-1">
                                                    {result.tanggal}
                                                </p>
                                            </div>
                                            <div className={`text-xl font-bold ${getSkorColor(result.skor)}`}>
                                                {result.skor}
                                            </div>
                                        </div>
                                    </button>
                                ))}
                            </div>
                        </div>

                        {/* Download Actions */}
                        {selectedResult?.certificate && (
                            <div className="space-y-3">
                                <button
                                    onClick={() => handleDownloadCertificate(selectedResult.certificate!.url)}
                                    className="w-full bg-yellow-400 hover:bg-yellow-500 py-3 rounded-lg font-semibold transition flex items-center justify-center gap-2"
                                >
                                    <HiDownload />
                                    Unduh Sertifikat
                                </button>
                                <p className="text-xs text-center text-gray-500">
                                    Terbit: {selectedResult.certificate.tanggal_terbit}
                                </p>
                            </div>
                        )}

                        {/* Additional Info */}
                        <div className="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6">
                            <h4 className="font-semibold text-gray-800 mb-2 text-sm">
                                💡 Saran Pengembangan
                            </h4>
                            <p className="text-xs text-gray-700 leading-relaxed">
                                Berdasarkan hasil tes, Anda dapat mengembangkan diri dengan mengikuti
                                program pelatihan yang sesuai dengan karakter dominan Anda.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
