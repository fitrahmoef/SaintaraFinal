import React from "react";
import DashboardLayout from "@/layouts/dashboard-layout-personal";
import { Head } from "@inertiajs/react";

export default function HasilTes() {
    return (
        <DashboardLayout>
            <Head title="Hasil Tes" />

            <div className="p-6">

                {/* Title */}
                <h1 className="text-2xl font-bold text-[#2A2A2A] mb-8">
                    Hasil Tes Anda
                </h1>

                <div className="grid grid-cols-12 gap-6">

                    {/* LEFT SECTION */}
                    <div className="col-span-8 space-y-6">

                        {/* CARD: Keterangan Hasil */}
                        <div className="bg-white rounded-2xl shadow-md p-6 flex gap-6">
                            {/* Placeholder gambar */}
                            <div className="bg-yellow-400 rounded-lg w-24 h-24"></div>

                            <div>
                                <h2 className="text-lg font-semibold text-gray-700 mb-2">
                                    Ringkasan Hasil Tes Terbaru
                                </h2>

                                <p className="text-gray-500 text-xs mb-1">KARAKTER DOMINAN ANDA</p>

                                <h3 className="text-2xl font-bold text-gray-900 mb-1">
                                    Pemikir Introvert
                                </h3>

                                <p className="text-gray-600 text-sm leading-relaxed">
                                    Anda adalah sosok yang cenderung berpikir mendalam, logis, dan analitis.
                                    Karakter ini sering menunjukkan kecenderungan pemikiran internal dan kemampuan
                                    membuat solusi secara terstruktur pada banyak bidang. Anda juga membutuhkan waktu
                                    lebih untuk refleksi dan evaluasi.
                                </p>

                                <p className="text-gray-500 text-xs mt-3">
                                    Dominan OteK Kiri Atas
                                </p>
                            </div>
                        </div>

                        {/* CARD: Ringkasan Hasil & Tantangan */}
                        <div className="grid grid-cols-2 gap-6">

                            <div className="bg-white rounded-2xl shadow-md p-6">
                                <h3 className="text-lg font-semibold text-gray-700 mb-3">
                                    Ringkasan Hasil Tes Terbaru
                                </h3>

                                <ul className="space-y-2 text-gray-700">
                                    <li className="flex gap-2">
                                        <span>✔️</span> <span>Analisis Tajam: Mampu membedah masalah dengan cepat.</span>
                                    </li>
                                    <li className="flex gap-2">
                                        <span>✔️</span> <span>Mandiri: Dapat bekerja dengan baik tanpa pengawasan.</span>
                                    </li>
                                </ul>
                            </div>

                            <div className="bg-white rounded-2xl shadow-md p-6">
                                <h3 className="text-lg font-semibold text-gray-700 mb-3">
                                    Tantangan untuk Berkembang
                                </h3>

                                <ul className="space-y-2 text-gray-700">
                                    <li className="flex gap-2">
                                        🔸 <span>Cenderung Overthinking: Terlalu banyak menganalisis dapat memperlambat keputusan.</span>
                                    </li>
                                    <li className="flex gap-2">
                                        🔸 <span>Sulit Beradaptasi Cepat: Membutuhkan waktu lebih lama dalam perubahan mendadak.</span>
                                    </li>
                                </ul>
                            </div>
                            
                        </div>

                    </div>

                    {/* RIGHT SECTION */}
                    <div className="col-span-4 space-y-6">

                        {/* BOX 1 */}
                        <div className="bg-white rounded-2xl shadow-md p-6 h-36"></div>
                        <button className="w-full bg-yellow-400 py-3 rounded-lg font-semibold hover:bg-yellow-500">
                            Unduh Hasil Sertifikat
                        </button>

                        {/* BOX 2 */}
                        <div className="bg-white rounded-2xl shadow-md p-6 h-36"></div>
                        <button className="w-full bg-yellow-400 py-3 rounded-lg font-semibold hover:bg-yellow-500">
                            Unduh Hasil Tes
                        </button>
                    </div>

                </div>

            </div>
        </DashboardLayout>
    );
}
