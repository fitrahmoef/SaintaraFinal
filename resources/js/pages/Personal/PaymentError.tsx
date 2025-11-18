import DashboardLayout from '@/layouts/dashboard-layout-personal';
import { Head, Link } from '@inertiajs/react';
import { HiXCircle, HiHome, HiRefresh, HiSupport } from 'react-icons/hi';

interface Transaction {
    id: number;
    kode_transaksi: string;
    jumlah: number;
    status_pembayaran: string;
    metode_pembayaran: string;
    customer: {
        nama_lengkap: string;
        email: string;
    };
    package: {
        nama_paket: string;
        jumlah_token: number;
    };
    payment_metadata?: {
        transaction_status?: string;
        fraud_status?: string;
    };
}

interface PaymentErrorProps {
    transaction: Transaction | null;
}

export default function PaymentError({ transaction }: PaymentErrorProps) {
    // Determine error message based on status
    const getErrorMessage = () => {
        if (!transaction) {
            return {
                title: 'Pembayaran Tidak Ditemukan',
                description: 'Informasi transaksi tidak dapat ditemukan. Silakan hubungi customer support jika Anda memerlukan bantuan.'
            };
        }

        const status = transaction.payment_metadata?.transaction_status;
        const fraudStatus = transaction.payment_metadata?.fraud_status;

        if (fraudStatus === 'deny' || fraudStatus === 'challenge') {
            return {
                title: 'Transaksi Ditolak',
                description: 'Maaf, transaksi Anda ditolak oleh sistem keamanan. Silakan gunakan metode pembayaran lain atau hubungi bank Anda.'
            };
        }

        if (status === 'deny') {
            return {
                title: 'Pembayaran Ditolak',
                description: 'Pembayaran Anda ditolak oleh bank. Silakan periksa saldo atau limit kartu Anda dan coba lagi.'
            };
        }

        if (status === 'expire') {
            return {
                title: 'Pembayaran Kadaluarsa',
                description: 'Waktu pembayaran telah habis. Silakan buat transaksi baru untuk melanjutkan.'
            };
        }

        if (status === 'cancel') {
            return {
                title: 'Pembayaran Dibatalkan',
                description: 'Transaksi telah dibatalkan. Anda dapat membuat transaksi baru jika masih ingin membeli.'
            };
        }

        return {
            title: 'Pembayaran Gagal',
            description: 'Terjadi kesalahan saat memproses pembayaran Anda. Silakan coba lagi atau hubungi customer support.'
        };
    };

    const errorInfo = getErrorMessage();

    return (
        <DashboardLayout>
            <Head title="Pembayaran Gagal" />

            <div className="min-h-[70vh] flex items-center justify-center px-4">
                <div className="w-full max-w-2xl">
                    {/* Error Icon & Message */}
                    <div className="text-center mb-8">
                        <div className="flex justify-center mb-4">
                            <HiXCircle className="text-red-500 text-8xl animate-pulse" />
                        </div>
                        <h1 className="text-3xl font-bold text-gray-800 mb-2">
                            {errorInfo.title}
                        </h1>
                        <p className="text-gray-600">
                            {errorInfo.description}
                        </p>
                    </div>

                    {/* Transaction Details */}
                    {transaction && (
                        <div className="bg-white rounded-xl shadow-lg p-8 space-y-6 mb-6">
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
                                        <span className="text-gray-600">Total Pembayaran</span>
                                        <span className="font-bold text-gray-800 text-xl">
                                            Rp {transaction.jumlah.toLocaleString('id-ID')}
                                        </span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-gray-600">Status</span>
                                        <span className="font-semibold text-red-600 capitalize">
                                            {transaction.status_pembayaran}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Help Section */}
                    <div className="bg-blue-50 rounded-xl p-6 mb-6">
                        <h3 className="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <HiSupport className="text-blue-600 text-xl" />
                            Butuh Bantuan?
                        </h3>
                        <ul className="space-y-2 text-gray-700 text-sm">
                            <li className="flex items-start gap-2">
                                <span className="text-blue-500 mt-1">•</span>
                                <span>Pastikan saldo atau limit kartu Anda mencukupi</span>
                            </li>
                            <li className="flex items-start gap-2">
                                <span className="text-blue-500 mt-1">•</span>
                                <span>Coba gunakan metode pembayaran lain (e-wallet, transfer bank, dll)</span>
                            </li>
                            <li className="flex items-start gap-2">
                                <span className="text-blue-500 mt-1">•</span>
                                <span>Hubungi customer support jika masalah berlanjut</span>
                            </li>
                            <li className="flex items-start gap-2">
                                <span className="text-blue-500 mt-1">•</span>
                                <span>Email: support@saintara.com | WhatsApp: +62 812-3456-7890</span>
                            </li>
                        </ul>
                    </div>

                    {/* Action Buttons */}
                    <div className="flex flex-col sm:flex-row gap-4">
                        <Link
                            href={route('personal.transaksi-token')}
                            className="flex-1 flex items-center justify-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-black font-semibold py-3 px-6 rounded-lg transition"
                        >
                            <HiRefresh className="text-xl" />
                            Coba Lagi
                        </Link>
                        <Link
                            href={route('personal.dashboard')}
                            className="flex-1 flex items-center justify-center gap-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg transition"
                        >
                            <HiHome className="text-xl" />
                            Kembali ke Dashboard
                        </Link>
                        <Link
                            href={route('personal.bantuan')}
                            className="flex-1 flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition"
                        >
                            <HiSupport className="text-xl" />
                            Hubungi Support
                        </Link>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
