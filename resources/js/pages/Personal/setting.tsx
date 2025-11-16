import DashboardLayout from '@/layouts/dashboard-layout-personal';
import React, { use, useState } from 'react';
import { usePage } from '@inertiajs/react';

export default function Settings() {
  const [loading, setLoading] = useState(false);
  const [passwordData, setPasswordData] = useState({
    current_password: '',
    new_password: '',
    confirm_password: '',
  });

  const [language, setLanguage] = useState('id');
  const [theme, setTheme] = useState<'light' | 'dark'>('light');
  const [notifications, setNotifications] = useState({
    articles: true,
    promos: false,
  });

  const handleChangePassword = (e: React.FormEvent) => {
    e.preventDefault();

    if (passwordData.new_password !== passwordData.confirm_password) {
      alert('Password baru tidak cocok!');
      return;
    }

    if (passwordData.new_password.length < 6) {
      alert('Password minimal 6 karakter.');
      return;
    }

    setLoading(true);
    setTimeout(() => {
      alert('Password berhasil diubah (dummy).');
      setPasswordData({
        current_password: '',
        new_password: '',
        confirm_password: '',
      });
      setLoading(false);
    }, 1000);
  };

  // Data user dummy
  const user = {
    email: 'budi@example.com',
    role: 'personal',
    created_at: '2024-05-10T00:00:00Z',
  };


  return (
    <DashboardLayout>
      <div className="p-8 bg-gray-50 min-h-screen text-black">
        <h1 className="text-3xl font-bold mb-8 text-blue-900">Pengaturan</h1>

        <div className="space-y-6 max-w-3xl">
          {/* === Pengaturan Akun === */}
          <div className="bg-white rounded-xl shadow p-6">
            <h2 className="text-lg font-bold mb-4">Pengaturan Akun</h2>
            <div className="space-y-4">
              {/* Email */}
              <div>
                <label className="block text-sm text-gray-600 mb-1">Email saat ini</label>
                <div className="flex items-center space-x-2">
                  <input
                    type="email"
                    disabled
                    value={user.email}
                    className="w-full px-4 py-2 border rounded-lg bg-gray-100 text-gray-700"
                  />
                  <button className="px-4 py-2 bg-yellow-400 hover:bg-yellow-500 text-white rounded-lg font-medium">
                    Ubah Email
                  </button>
                </div>
              </div>

              {/* Password */}
              <div>
                <label className="block text-sm text-gray-600 mb-1">Kata Sandi</label>
                <button
                  onClick={() => document.getElementById('password-section')?.scrollIntoView({ behavior: 'smooth' })}
                  className="px-4 py-2 bg-yellow-400 hover:bg-yellow-500 text-white rounded-lg font-medium"
                >
                  Ubah Kata Sandi
                </button>
                <p className="text-sm text-gray-500 mt-1">
                  Anda akan diminta memasukkan kata sandi saat ini.
                </p>
              </div>
            </div>
          </div>

          {/* === Ganti Password === */}
          <div id="password-section" className="bg-white rounded-xl shadow p-6">
            <h2 className="text-lg font-bold mb-4">Ganti Password</h2>
            <form onSubmit={handleChangePassword} className="space-y-4">
              <div>
                <label className="block text-sm font-medium mb-1">Password Sekarang</label>
                <input
                  type="password"
                  value={passwordData.current_password}
                  onChange={(e) =>
                    setPasswordData({ ...passwordData, current_password: e.target.value })
                  }
                  className="w-full px-4 py-2 border border-gray-300 rounded-lg text-black"
                  required
                />
              </div>

              <div>
                <label className="block text-sm font-medium mb-1">Password Baru</label>
                <input
                  type="password"
                  value={passwordData.new_password}
                  onChange={(e) =>
                    setPasswordData({ ...passwordData, new_password: e.target.value })
                  }
                  className="w-full px-4 py-2 border border-gray-300 rounded-lg text-black"
                  required
                />
              </div>

              <div>
                <label className="block text-sm font-medium mb-1">Konfirmasi Password Baru</label>
                <input
                  type="password"
                  value={passwordData.confirm_password}
                  onChange={(e) =>
                    setPasswordData({ ...passwordData, confirm_password: e.target.value })
                  }
                  className="w-full px-4 py-2 border border-gray-300 rounded-lg text-black"
                  required
                />
              </div>

              <button
                type="submit"
                disabled={loading}
                className="px-6 py-2 bg-yellow-400 text-white rounded-lg hover:bg-yellow-500 disabled:opacity-50"
              >
                {loading ? 'Mengubah...' : 'Ubah Password'}
              </button>
            </form>
          </div>

          {/* === Preferensi Tampilan === */}
          <div className="bg-white rounded-xl shadow p-6">
            <h2 className="text-lg font-bold mb-4">Preferensi Tampilan</h2>
            <div className="space-y-4">
              <div>
                <label className="block text-sm text-gray-600 mb-1">Bahasa Tampilan</label>
                <select
                  value={language}
                  onChange={(e) => setLanguage(e.target.value)}
                  className="w-full px-4 py-2 border rounded-lg bg-gray-50"
                >
                  <option value="id">Bahasa Indonesia</option>
                  <option value="en">English</option>
                </select>
              </div>

              <div>
                <label className="block text-sm text-gray-600 mb-2">Pilihan Tema</label>
                <div className="flex space-x-4">
                  <button
                    onClick={() => setTheme('light')}
                    type="button"
                    className={`px-6 py-2 border rounded-lg ${
                      theme === 'light'
                        ? 'bg-gray-800 text-white border-gray-800'
                        : 'bg-white text-gray-800 border-gray-400'
                    }`}
                  >
                    Light
                  </button>
                  <button
                    onClick={() => setTheme('dark')}
                    type="button"
                    className={`px-6 py-2 border rounded-lg ${
                      theme === 'dark'
                        ? 'bg-gray-800 text-white border-gray-800'
                        : 'bg-white text-gray-800 border-gray-400'
                    }`}
                  >
                    Dark
                  </button>
                </div>
              </div>
            </div>
          </div>

          {/* === Kelola Notifikasi === */}
          <div className="bg-white rounded-xl shadow p-6">
            <h2 className="text-lg font-bold mb-4">Kelola Notifikasi</h2>
            <div className="flex flex-col space-y-3">
              <label className="flex items-center justify-between">
                <span>Notifikasi Email untuk Artikel Terbaru</span>
                <input
                  type="checkbox"
                  checked={notifications.articles}
                  onChange={() =>
                    setNotifications({ ...notifications, articles: !notifications.articles })
                  }
                  className="w-5 h-5 accent-yellow-400"
                />
              </label>
              <label className="flex items-center justify-between">
                <span>Notifikasi Promo dan Penawaran</span>
                <input
                  type="checkbox"
                  checked={notifications.promos}
                  onChange={() =>
                    setNotifications({ ...notifications, promos: !notifications.promos })
                  }
                  className="w-5 h-5 accent-yellow-400"
                />
              </label>
            </div>
          </div>

          {/* === Zona Berbahaya === */}
          <div className="bg-red-100 rounded-xl shadow p-6 border border-red-300">
            <h2 className="text-lg font-bold text-red-700 mb-2">Zona Berbahaya</h2>
            <p className="text-sm text-red-600 mb-4">
              Menghapus akun Anda bersifat permanen dan tidak dapat dibatalkan. Semua data dan
              riwayat Anda akan hilang selamanya.
            </p>
            <button className="px-6 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium">
              Hapus Akun Saya
            </button>
          </div>
        </div>
      </div>
    </DashboardLayout>
  );
}
