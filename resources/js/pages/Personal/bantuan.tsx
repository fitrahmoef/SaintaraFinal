import React, { useState } from "react";
import DashboardLayout from "@/layouts/dashboard-layout-personal";
import { Head } from "@inertiajs/react";

export default function Bantuan() {
    const [faqOpen, setFaqOpen] = useState<number | null>(0);

    const toggleFAQ = (index: number) => {
        setFaqOpen(faqOpen === index ? null : index);
    };

    const faqItems = [
        {
            q: "Apa itu Token dan untuk apa kegunaannya?",
            a: "Token adalah alat tukar yang Anda gunakan untuk mengakses tes berbayar di platform Saintara, seperti Saintara Premium atau Saintara Langge. Satu token biasanya berlaku untuk satu kali penggunaan tes.",
        },
        {
            q: "Bagaimana cara membaca hasil tes saya?",
            a: "Setelah tes selesai, hasil akan otomatis tersedia di menu Hasil Tes di dashboard Anda.",
        },
        {
            q: "Apa data pribadi saya aman?",
            a: "Kami menjaga privasi Anda dan tidak membagikan data pribadi apa pun kepada pihak ketiga tanpa izin.",
        },
    ];

    return (
        <DashboardLayout>
            <Head title="Bantuan & Layanan" />

            <div className="p-6">
                {/* Page title */}
                <h1 className="text-2xl font-bold text-[#2A2A2A] mb-8">
                    Bantuan & Layanan
                </h1>

                {/* ================= FORM ================= */}
                <div className="bg-white rounded-2xl shadow-md p-6 mb-8">
                    <h2 className="text-xl font-semibold text-[#2A2A2A] mb-2">
                        Form Pengaduan Kendala Teknis
                    </h2>
                    <p className="text-gray-600 mb-6">
                        Mengalami masalah saat menggunakan platform? Isi formulir di bawah ini dan tim kami akan segera membantu Anda.
                    </p>

                    <form className="space-y-4">
                        <div>
                            <label className="block font-medium mb-1">Subjek Kendala</label>
                            <input
                                type="text"
                                placeholder="Contoh: Tidak bisa mendownload hasil tes"
                                className="w-full border border-gray-300 rounded-lg p-3"
                            />
                        </div>

                        <div>
                            <label className="block font-medium mb-1">Kategori Kendala</label>
                            <select className="w-full border border-gray-300 rounded-lg p-3">
                                <option>Pilih Kategori</option>
                                <option>Masalah teknis</option>
                                <option>Kesalahan pembayaran</option>
                                <option>Akses atau login</option>
                            </select>
                        </div>

                        <div>
                            <label className="block font-medium mb-1">Jelaskan Kendala Anda</label>
                            <textarea
                                rows={4}
                                className="w-full border border-gray-300 rounded-lg p-3"
                                placeholder="Tuliskan detail kendala yang Anda alami"
                            />
                        </div>

                        <button
                            type="submit"
                            className="bg-black text-white px-6 py-3 rounded-lg hover:bg-gray-800"
                        >
                            Kirim Laporan
                        </button>
                    </form>
                </div>

                {/* ================= FAQ ================= */}
                <div className="bg-white rounded-2xl shadow-md p-6">
                    <h2 className="text-xl font-bold text-[#2A2A2A] mb-4">
                        FAQ (Pertanyaan Umum)
                    </h2>

                    <div className="divide-y border border-gray-200 rounded-xl overflow-hidden">
                        {faqItems.map((item, index) => (
                            <div key={index}>
                                <button
                                    className={`w-full text-left p-4 font-semibold flex justify-between items-center
                                        ${faqOpen === index ? "bg-yellow-300" : "bg-gray-100"}`}
                                    onClick={() => toggleFAQ(index)}
                                >
                                    {item.q}
                                    <span className="text-lg">
                                        {faqOpen === index ? "▾" : "▸"}
                                    </span>
                                </button>

                                {faqOpen === index && (
                                    <div className="p-4 bg-white text-gray-700">
                                        {item.a}
                                    </div>
                                )}
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
