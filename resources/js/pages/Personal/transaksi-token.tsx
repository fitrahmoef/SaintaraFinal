import React, { useState, useEffect } from 'react';
import DashboardLayout from '@/layouts/dashboard-layout-personal';
import { Head } from '@inertiajs/react';
import { tokenApi } from '@/services/api';

// Declare Midtrans Snap on window object
declare global {
  interface Window {
    snap?: {
      pay: (token: string, options?: any) => void;
    };
  }
}

interface Package {
  id: number;
  nama: string;
  harga: number;
  harga_formatted: string;
  deskripsi: string;
  jumlah_token: number;
  masa_aktif: string;
}

interface Transaction {
  id: number;
  kode: string;
  tanggal: string;
  paket: string;
  jumlah: string;
  status: string;
  metode: string;
}

export default function TransaksiDanToken() {
  const [tokenBalance, setTokenBalance] = useState(0);
  const [totalTokens, setTotalTokens] = useState(0);
  const [usedTokens, setUsedTokens] = useState(0);
  const [packages, setPackages] = useState<Package[]>([]);
  const [transactions, setTransactions] = useState<Transaction[]>([]);
  const [loading, setLoading] = useState(true);
  const [purchasing, setPurchasing] = useState<number | null>(null);
  const [error, setError] = useState<string | null>(null);

  // Load Midtrans Snap script
  useEffect(() => {
    const midtransClientKey = import.meta.env.VITE_MIDTRANS_CLIENT_KEY || 'your-client-key-here';
    const isProduction = import.meta.env.VITE_MIDTRANS_IS_PRODUCTION === 'true';
    const snapUrl = isProduction
      ? 'https://app.midtrans.com/snap/snap.js'
      : 'https://app.sandbox.midtrans.com/snap/snap.js';

    const script = document.createElement('script');
    script.src = snapUrl;
    script.setAttribute('data-client-key', midtransClientKey);
    script.async = true;
    document.body.appendChild(script);

    return () => {
      document.body.removeChild(script);
    };
  }, []);

  // Fetch data on component mount
  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      setLoading(true);
      setError(null);

      // Fetch token balance and transactions
      const [tokenResponse, packagesResponse] = await Promise.all([
        tokenApi.getTokens(),
        tokenApi.getPackages(),
      ]);

      if (tokenResponse.data) {
        setTokenBalance(tokenResponse.data.tokenBalance || 0);
        setTotalTokens(tokenResponse.data.totalTokens || 0);
        setUsedTokens(tokenResponse.data.usedTokens || 0);
        setTransactions(tokenResponse.data.transactions || []);
      }

      if (packagesResponse.data && packagesResponse.data.packages) {
        setPackages(packagesResponse.data.packages);
      }
    } catch (err: any) {
      console.error('Error fetching data:', err);
      setError('Gagal memuat data. Silakan refresh halaman.');
    } finally {
      setLoading(false);
    }
  };

  const handlePurchase = async (packageId: number) => {
    try {
      setPurchasing(packageId);
      setError(null);

      // Call purchase API
      const response = await tokenApi.purchase({ package_id: packageId });

      if (response.data.success && response.data.payment?.snap_token) {
        const snapToken = response.data.payment.snap_token;

        // Open Midtrans Snap popup
        if (window.snap) {
          window.snap.pay(snapToken, {
            onSuccess: function (result: any) {
              console.log('Payment success:', result);
              alert('Pembayaran berhasil! Token Anda akan segera ditambahkan.');
              // Refresh data
              fetchData();
            },
            onPending: function (result: any) {
              console.log('Payment pending:', result);
              alert('Pembayaran sedang diproses. Silakan selesaikan pembayaran Anda.');
              fetchData();
            },
            onError: function (result: any) {
              console.error('Payment error:', result);
              alert('Pembayaran gagal. Silakan coba lagi.');
            },
            onClose: function () {
              console.log('Payment popup closed');
              // Refresh data in case payment was completed but popup closed
              fetchData();
            },
          });
        } else {
          throw new Error('Midtrans Snap not loaded');
        }
      } else {
        throw new Error(response.data.message || 'Gagal membuat transaksi');
      }
    } catch (err: any) {
      console.error('Error purchasing package:', err);
      const errorMessage =
        err.response?.data?.message || err.message || 'Gagal melakukan pembelian';
      setError(errorMessage);
      alert(errorMessage);
    } finally {
      setPurchasing(null);
    }
  };

  const getStatusColor = (status: string) => {
    switch (status.toLowerCase()) {
      case 'dibayar':
      case 'selesai':
        return 'text-green-600';
      case 'pending':
      case 'menunggu_pembayaran':
        return 'text-yellow-500';
      case 'gagal':
      case 'dibatalkan':
        return 'text-red-600';
      default:
        return 'text-gray-600';
    }
  };

  const getStatusText = (status: string) => {
    const statusMap: Record<string, string> = {
      dibayar: 'Selesai',
      pending: 'Pending',
      menunggu_pembayaran: 'Menunggu Pembayaran',
      gagal: 'Gagal',
      dibatalkan: 'Dibatalkan',
    };
    return statusMap[status.toLowerCase()] || status;
  };

  if (loading) {
    return (
      <DashboardLayout>
        <Head title="Transaksi dan Token" />
        <div className="flex items-center justify-center h-96">
          <div className="text-center">
            <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-yellow-400 mx-auto mb-4"></div>
            <p className="text-gray-600">Memuat data...</p>
          </div>
        </div>
      </DashboardLayout>
    );
  }

  return (
    <DashboardLayout>
      <Head title="Transaksi dan Token" />

      <div className="p-6">
        <h1 className="text-2xl font-bold text-[#2A2A2A] mb-8">
          Transaksi & Token
        </h1>

        {error && (
          <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            {error}
          </div>
        )}

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {/* === Status Token === */}
          <div className="bg-white rounded-2xl shadow-md p-6 col-span-2">
            <h2 className="text-lg font-semibold mb-3 text-[#2A2A2A]">
              Status Token Anda
            </h2>
            <div className="border border-green-200 bg-green-50 rounded-xl p-6">
              <div className="grid grid-cols-3 gap-4 text-center">
                <div>
                  <p className="text-sm text-gray-600">Token Tersedia</p>
                  <p className="text-3xl font-bold text-green-600 mt-2">
                    {tokenBalance}
                  </p>
                </div>
                <div>
                  <p className="text-sm text-gray-600">Total Token</p>
                  <p className="text-3xl font-bold text-blue-600 mt-2">
                    {totalTokens}
                  </p>
                </div>
                <div>
                  <p className="text-sm text-gray-600">Token Terpakai</p>
                  <p className="text-3xl font-bold text-gray-600 mt-2">
                    {usedTokens}
                  </p>
                </div>
              </div>
            </div>
          </div>

          {/* === Riwayat Transaksi === */}
          <div className="bg-white rounded-2xl shadow-md p-6">
            <h2 className="text-lg font-bold mb-4 text-[#2A2A2A]">
              Riwayat Transaksi
            </h2>
            <div className="space-y-4 max-h-96 overflow-y-auto">
              {transactions.length === 0 ? (
                <p className="text-gray-500 text-center py-8">
                  Belum ada transaksi
                </p>
              ) : (
                transactions.map((trx) => (
                  <div
                    key={trx.id}
                    className="flex justify-between items-center border-b border-gray-200 pb-3"
                  >
                    <div>
                      <p className="font-medium text-[#2A2A2A]">{trx.paket}</p>
                      <p className="text-sm text-gray-500">{trx.tanggal}</p>
                      <p className="text-xs text-gray-400">
                        {trx.kode}
                      </p>
                    </div>
                    <div className="text-right">
                      <p className="font-semibold text-[#2A2A2A]">
                        {trx.jumlah}
                      </p>
                      <p className={`text-sm ${getStatusColor(trx.status)}`}>
                        {getStatusText(trx.status)}
                      </p>
                    </div>
                  </div>
                ))
              )}
            </div>
          </div>
        </div>

        {/* === Pembelian Token === */}
        <div className="bg-white rounded-2xl shadow-md p-8 mt-8">
          <h2 className="text-lg font-semibold mb-6 text-[#2A2A2A]">
            Beli Token Tes
          </h2>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {packages.length === 0 ? (
              <p className="text-gray-500 col-span-full text-center py-8">
                Tidak ada paket tersedia
              </p>
            ) : (
              packages.map((pkg) => (
                <div
                  key={pkg.id}
                  className="bg-yellow-400 rounded-xl p-6 shadow-md text-center hover:shadow-lg transition-shadow"
                >
                  <h3 className="text-lg font-bold mb-2 text-[#2A2A2A]">
                    {pkg.nama}
                  </h3>
                  <p className="text-[#2A2A2A] mb-3 min-h-[48px]">
                    {pkg.deskripsi}
                  </p>
                  <div className="mb-3">
                    <p className="text-sm text-gray-700">
                      {pkg.jumlah_token}x Tes Premium
                    </p>
                    <p className="text-xs text-gray-600">
                      Berlaku {pkg.masa_aktif}
                    </p>
                  </div>
                  <p className="text-2xl font-bold text-[#2A2A2A] mb-4">
                    {pkg.harga_formatted}
                  </p>
                  <button
                    onClick={() => handlePurchase(pkg.id)}
                    disabled={purchasing === pkg.id}
                    className="bg-black text-white px-6 py-2 rounded-md hover:bg-gray-800 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors w-full"
                  >
                    {purchasing === pkg.id ? (
                      <span className="flex items-center justify-center">
                        <svg
                          className="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                          xmlns="http://www.w3.org/2000/svg"
                          fill="none"
                          viewBox="0 0 24 24"
                        >
                          <circle
                            className="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            strokeWidth="4"
                          ></circle>
                          <path
                            className="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                          ></path>
                        </svg>
                        Memproses...
                      </span>
                    ) : (
                      'Beli Paket'
                    )}
                  </button>
                </div>
              ))
            )}
          </div>
        </div>
      </div>
    </DashboardLayout>
  );
}
