import React from 'react'
import DashboardLayout from '@/layouts/dashboard-layout-personal'
import { Head } from '@inertiajs/react'

export default function DaftarTesKarakter() {
  return (
    <DashboardLayout>
      <Head title="Daftar Tes Karakter" />

      <div className="p-6">
        <h1 className="text-2xl font-bold text-[#2A2A2A] mb-8">
          Daftar Tes Karakter
        </h1>

        <div className="bg-white rounded-2xl shadow-md p-8">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {/* Dasar */}
            <div className="bg-yellow-400 p-6 rounded-xl shadow-md text-center">
              <h2 className="text-xl font-bold mb-2 text-[#2A2A2A]">Dasar</h2>
              <p className="text-[#2A2A2A] mb-4">
                Untuk individu yang ingin mengenal diri
              </p>
              <p className="text-2xl font-bold text-[#2A2A2A] mb-4">
                Rp150.000
              </p>
              <div className="text-left text-[#2A2A2A] space-y-1 mb-4">
                <p>✔ Laporan 10 Karakter</p>
                <p>✔ Analisis Karakter Alami</p>
              </div>
              <a href="formTes">
                <button className="bg-black text-white px-6 py-2 rounded-md hover:bg-gray-800">
                  Mulai Tes
                </button>
              </a>
            </div>

            {/* Standar */}
            <div className="bg-yellow-400 p-6 rounded-xl shadow-md text-center">
              <h2 className="text-xl font-bold mb-2 text-[#2A2A2A]">Standar</h2>
              <p className="text-[#2A2A2A] mb-4">
                Untuk individu yang ingin mengenal diri lebih lanjut
              </p>
              <p className="text-2xl font-bold text-[#2A2A2A] mb-4">
                Rp150.000
              </p>
              <div className="text-left text-[#2A2A2A] space-y-1 mb-4">
                <p>✔ Laporan 20 Karakter</p>
                <p>✔ Analisis Karakter Alami</p>
              </div>
              <button className="bg-black text-white px-6 py-2 rounded-md hover:bg-gray-800">
                Mulai Tes
              </button>
            </div>

            {/* Premium */}
            <div className="bg-yellow-400 p-6 rounded-xl shadow-md text-center">
              <h2 className="text-xl font-bold mb-2 text-[#2A2A2A]">Premium</h2>
              <p className="text-[#2A2A2A] mb-4">
                Untuk individu yang ingin tahu lebih dalam tentang diri sendiri
              </p>
              <p className="text-2xl font-bold text-[#2A2A2A] mb-4">
                Rp150.000
              </p>
              <div className="text-left text-[#2A2A2A] space-y-1 mb-4">
                <p>✔ Laporan 35+ Karakter</p>
                <p>✔ Analisis Karakter Alami</p>
              </div>
              <button className="bg-black text-white px-6 py-2 rounded-md hover:bg-gray-800">
                Mulai Tes
              </button>
            </div>
          </div>
        </div>
      </div>
    </DashboardLayout>
  )
}
