import React from 'react';
import DashboardLayout from '@/layouts/dashboard-layout-personal';
import { Head } from '@inertiajs/react';

export default function TransaksiDanToken() {
  const tokenTersedia = 5;

  const riwayatTransaksi = [
    {
      nama: 'Beli Paket 5 Token',
      tanggal: '1 Okt 2025',
      jumlah: 'Rp 200.000',
      status: 'Selesai',
    },
    {
      nama: 'Beli Paket 1 Token',
      tanggal: '18 Okt 2025',
      jumlah: 'Rp 50.000',
      status: 'Pending',
    },
  ];

  return (
    <DashboardLayout>
      <Head title="Transaksi dan Token" />

      <div className="p-6">
        <h1 className="text-2xl font-bold text-[#2A2A2A] mb-8">
          Transaksi & Token
        </h1>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {/* === Status Token === */}
          <div className="bg-white rounded-2xl shadow-md p-6 col-span-2">
            <h2 className="text-lg font-semibold mb-3 text-[#2A2A2A]">
              Status Token Anda
            </h2>
            <div className="border border-green-200 bg-green-50 rounded-xl p-6 text-center">
              <p className="text-[#2A2A2A]">Token Aktif Tersedia:</p>
              <p className="text-3xl font-bold text-green-600 mt-2">
                {tokenTersedia} Token
              </p>
            </div>
          </div>

          {/* === Riwayat Transaksi === */}
          <div className="bg-white rounded-2xl shadow-md p-6">
            <h2 className="text-lg font-bold mb-4 text-[#2A2A2A]">
              Riwayat Transaksi
            </h2>
            <div className="space-y-4">
              {riwayatTransaksi.map((trx, index) => (
                <div
                  key={index}
                  className="flex justify-between items-center border-b border-gray-200 pb-3"
                >
                  <div>
                    <p className="font-medium text-[#2A2A2A]">{trx.nama}</p>
                    <p className="text-sm text-gray-500">{trx.tanggal}</p>
                  </div>
                  <div className="text-right">
                    <p className="font-semibold text-[#2A2A2A]">{trx.jumlah}</p>
                    <p
                      className={`text-sm ${
                        trx.status === 'Selesai'
                          ? 'text-green-600'
                          : 'text-yellow-500'
                      }`}
                    >
                      {trx.status}
                    </p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* === Pembelian Token === */}
        <div className="bg-white rounded-2xl shadow-md p-8 mt-8">
          <h2 className="text-lg font-semibold mb-6 text-[#2A2A2A]">
            Beli Token Tes
          </h2>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {/* Paket 1 Token */}
            <div className="bg-yellow-400 rounded-xl p-6 shadow-md text-center">
              <h3 className="text-lg font-bold mb-2 text-[#2A2A2A]">
                Paket 1 Token
              </h3>
              <p className="text-[#2A2A2A] mb-3">
                Untuk 1x Tes Saintara Premium
              </p>
              <p className="text-2xl font-bold text-[#2A2A2A] mb-4">
                Rp 50.000
              </p>
              <button className="bg-black text-white px-6 py-2 rounded-md hover:bg-gray-800">
                Beli Paket
              </button>
            </div>

            {/* Paket 5 Token */}
            <div className="bg-yellow-400 rounded-xl p-6 shadow-md text-center">
              <h3 className="text-lg font-bold mb-2 text-[#2A2A2A]">
                Paket 5 Token
              </h3>
              <p className="text-[#2A2A2A] mb-3">
                Untuk 5x Tes Saintara Premium
              </p>
              <p className="text-2xl font-bold text-[#2A2A2A] mb-4">
                Rp 200.000
              </p>
              <button className="bg-black text-white px-6 py-2 rounded-md hover:bg-gray-800">
                Beli Paket
              </button>
            </div>
          </div>
        </div>
      </div>
    </DashboardLayout>
  );
}
