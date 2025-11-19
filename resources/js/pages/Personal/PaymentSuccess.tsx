import DashboardLayout from '@/layouts/dashboard-layout-personal';
import { Head, Link } from '@inertiajs/react';
import { HiCheckCircle, HiHome, HiCreditCard, HiClipboardList } from 'react-icons/hi';

interface Transaction {
    id: number;
    kode_transaksi: string;
    jumlah: number;
    status_pembayaran: string;
    metode_pembayaran: string;
    waktu_dibayar: string;
    customer: {
        nama_lengkap: string;
        email: string;
    };
    package: {
        nama_paket: string;
        jumlah_token: number;
    };
    tokenPurchase?: {
        kode_token: string;
        jumlah_token: number;
        tanggal_kadaluarsa: string | null;
    };
}

interface PaymentSuccessProps {
    transaction: Transaction | null;
}

export default function PaymentSuccess({ transaction }: PaymentSuccessProps) {
    return (
        <DashboardLayout>
            <Head title="Pembayaran Berhasil" />

            <div className="min-h-[70vh] flex items-center justify-center px-4">
                <div className="w-full max-w-2xl">
                    {/* Success Icon & Message */}
                    <div className="text-center mb-8">
                        <div className="flex justify-center mb-4">
                            <HiCheckCircle className="text-green-500 text-8xl animate-bounce" />
                        </div>
                        <h1 className="text-3xl font-bold text-gray-800 mb-2">
                            Pembayaran Berhasil! 🎉
                        </h1>
                        <p className="text-gray-600">
                            Terima kasih! Pembayaran Anda telah dikonfirmasi.
                        </p>
                    </div>

                    {/* Transaction Details */}
                    {transaction ? (
                        <div className="bg-white rounded-xl shadow-lg p-8 space-y-6">
                            {/* Transaction Info */}
                            <div className="border-b pb-4">
                                <h2 className="text-lg font-semibold text-gray-800 mb-4">
                                    Detail Transaksi
                                </h2>
                                <div className="space-y-3">
                                    <div className="flex justify-between">
                                        <span className="text-gray-600">Kode Transaksi</span>
                                        <span className="font-semibold text-gray-800">
                                            {transaction.kode_transaksi}
                                        </span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-gray-600">Paket</span>
                                        <span className="font-semibold text-gray-800">
                                            {transaction.package.nama_paket}
                                        </span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-gray-600">Metode Pembayaran</span>
                                        <span className="font-semibold text-gray-800 capitalize">
                                            {transaction.metode_pembayaran}
                                        </span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-gray-600">Total Pembayaran</span>
                                        <span className="font-bold text-green-600 text-xl">
                                            Rp {transaction.jumlah.toLocaleString('id-ID')}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {/* Token Info */}
                            {transaction.tokenPurchase && (
                                <div className="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg p-6">
                                    <h3 className="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                        <HiCreditCard className="text-yellow-600" />
                                        Token Anda
                                    </h3>
                                    <div className="space-y-3">
                                        <div className="flex justify-between">
                                            <span className="text-gray-700">Kode Token</span>
                                            <span className="font-mono font-bold text-yellow-700">
                                                {transaction.tokenPurchase.kode_token}
                                            </span>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-gray-700">Jumlah Token</span>
                                            <span className="font-bold text-yellow-700 text-2xl">
                                                {transaction.tokenPurchase.jumlah_token} Token
                                            </span>
                                        </div>
                                        {transaction.tokenPurchase.tanggal_kadaluarsa && (
                                            <div className="flex justify-between">
                                                <span className="text-gray-700">Berlaku Hingga</span>
                                                <span className="font-semibold text-gray-800">
                                                    {new Date(transaction.tokenPurchase.tanggal_kadaluarsa).toLocaleDateString('id-ID', {
                                                        day: '2-digit',
                                                        month: 'long',
                                                        year: 'numeric'
                                                    })}
                                                </span>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            )}

                            {/* Next Steps */}
                            <div className="border-t pt-4">
                                <h3 className="font-semibold text-gray-800 mb-3">
                                    Langkah Selanjutnya
                                </h3>
                                <ul className="space-y-2 text-gray-600">
                                    <li className="flex items-start gap-2">
                                        <span className="text-green-500 mt-1">✓</span>
                                        <span>Token telah ditambahkan ke akun Anda</span>
                                    </li>
                                    <li className="flex items-start gap-2">
                                        <span className="text-green-500 mt-1">✓</span>
                                        <span>Anda dapat langsung mulai mengerjakan tes</span>
                                    </li>
                                    <li className="flex items-start gap-2">
                                        <span className="text-green-500 mt-1">✓</span>
                                        <span>Bukti pembayaran telah dikirim ke email Anda</span>
                                    </li>
                                </ul>
                            </div>

                            {/* Action Buttons */}
                            <div className="flex flex-col sm:flex-row gap-4 pt-4">
                                <Link
                                    href={route('personal.dashboard')}
                                    className="flex-1 flex items-center justify-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-black font-semibold py-3 px-6 rounded-lg transition"
                                >
                                    <HiHome className="text-xl" />
                                    Kembali ke Dashboard
                                </Link>
                                <Link
                                    href={route('personal.daftar-tes')}
                                    className="flex-1 flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition"
                                >
                                    <HiClipboardList className="text-xl" />
                                    Mulai Tes Sekarang
                                </Link>
                            </div>
                        </div>
                    ) : (
                        /* No Transaction Found */
                        <div className="bg-white rounded-xl shadow-lg p-8 text-center">
                            <p className="text-gray-600 mb-6">
                                Informasi transaksi tidak ditemukan.
                            </p>
                            <Link
                                href={route('personal.dashboard')}
                                className="inline-flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-black font-semibold py-3 px-6 rounded-lg transition"
                            >
                                <HiHome className="text-xl" />
                                Kembali ke Dashboard
                            </Link>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
