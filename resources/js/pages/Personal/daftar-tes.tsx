import React, { useState, useEffect } from 'react';
import DashboardLayout from '@/layouts/dashboard-layout-personal';
import { Head } from '@inertiajs/react';
import { router } from '@inertiajs/react';
import { testApi, tokenApi } from '@/services/api';

interface Test {
  id: number;
  nama_tes: string;
  deskripsi: string;
  tipe_tes: string;
  jumlah_soal: number;
  waktu_pengerjaan: number;
  is_active: boolean;
}

export default function DaftarTesKarakter() {
  const [tests, setTests] = useState<Test[]>([]);
  const [tokenBalance, setTokenBalance] = useState(0);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      setLoading(true);
      setError(null);

      const [testsResponse, balanceResponse] = await Promise.all([
        testApi.getTests(),
        tokenApi.getBalance(),
      ]);

      if (testsResponse.data && testsResponse.data.tests) {
        setTests(testsResponse.data.tests);
      }

      if (balanceResponse.data) {
        setTokenBalance(balanceResponse.data.balance || 0);
      }
    } catch (err: any) {
      console.error('Error fetching data:', err);
      setError('Gagal memuat data. Silakan refresh halaman.');
    } finally {
      setLoading(false);
    }
  };

  const handleStartTest = (testId: number) => {
    if (tokenBalance <= 0) {
      alert('Token Anda habis! Silakan beli token terlebih dahulu.');
      router.visit('/personal/transaksiToken');
      return;
    }

    // Navigate directly to test execution
    router.visit(`/personal/test/execute?test_id=${testId}`);
  };

  const getTestTypeLabel = (type: string) => {
    const typeMap: Record<string, string> = {
      dasar: 'Dasar',
      standar: 'Standar',
      premium: 'Premium',
    };
    return typeMap[type.toLowerCase()] || type;
  };

  const getTestTypeColor = (type: string) => {
    const colorMap: Record<string, string> = {
      dasar: 'bg-yellow-300',
      standar: 'bg-yellow-400',
      premium: 'bg-yellow-500',
    };
    return colorMap[type.toLowerCase()] || 'bg-yellow-400';
  };

  if (loading) {
    return (
      <DashboardLayout>
        <Head title="Daftar Tes Karakter" />
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
      <Head title="Daftar Tes Karakter" />

      <div className="p-6">
        <div className="flex justify-between items-center mb-8">
          <h1 className="text-2xl font-bold text-[#2A2A2A]">
            Daftar Tes Karakter
          </h1>

          <div className="bg-green-50 border border-green-200 rounded-lg px-4 py-2">
            <p className="text-sm text-gray-600">Token Tersedia:</p>
            <p className="text-2xl font-bold text-green-600">{tokenBalance}</p>
          </div>
        </div>

        {error && (
          <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            {error}
          </div>
        )}

        {tokenBalance <= 0 && (
          <div className="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg mb-6">
            <p className="font-medium">Token Anda habis!</p>
            <p className="text-sm">
              Silakan beli token terlebih dahulu untuk mengikuti tes.{' '}
              <button
                onClick={() => router.visit('/personal/transaksiToken')}
                className="underline font-semibold hover:text-yellow-900"
              >
                Beli Token
              </button>
            </p>
          </div>
        )}

        <div className="bg-white rounded-2xl shadow-md p-8">
          {tests.length === 0 ? (
            <div className="text-center py-12">
              <p className="text-gray-500 text-lg">
                Belum ada tes tersedia saat ini.
              </p>
            </div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
              {tests.map((test) => (
                <div
                  key={test.id}
                  className={`${getTestTypeColor(test.tipe_tes)} p-6 rounded-xl shadow-md text-center hover:shadow-lg transition-shadow`}
                >
                  <h2 className="text-xl font-bold mb-2 text-[#2A2A2A]">
                    {test.nama_tes}
                  </h2>
                  <p className="text-sm text-gray-700 mb-2">
                    {getTestTypeLabel(test.tipe_tes)}
                  </p>
                  <p className="text-[#2A2A2A] mb-4 min-h-[48px]">
                    {test.deskripsi}
                  </p>

                  <div className="text-sm text-gray-700 mb-4 space-y-1">
                    <p>📝 {test.jumlah_soal} Pertanyaan</p>
                    <p>⏱️ {test.waktu_pengerjaan} Menit</p>
                  </div>

                  <button
                    onClick={() => handleStartTest(test.id)}
                    disabled={tokenBalance <= 0 || !test.is_active}
                    className="bg-black text-white px-6 py-2 rounded-md hover:bg-gray-800 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors w-full"
                  >
                    {tokenBalance <= 0 ? 'Beli Token Dulu' : test.is_active ? 'Mulai Tes' : 'Tidak Tersedia'}
                  </button>
                </div>
              ))}
            </div>
          )}
        </div>

        {tests.length > 0 && (
          <div className="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 className="font-semibold text-blue-900 mb-2">ℹ️ Informasi Penting:</h3>
            <ul className="text-sm text-blue-800 space-y-1 list-disc list-inside">
              <li>Setiap tes memerlukan 1 token</li>
              <li>Token akan terpotong otomatis setelah Anda menyelesaikan tes</li>
              <li>Pastikan koneksi internet stabil selama tes</li>
              <li>Tes tidak dapat diulang setelah dimulai</li>
            </ul>
          </div>
        )}
      </div>
    </DashboardLayout>
  );
}
