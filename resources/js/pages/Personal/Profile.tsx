import DashboardLayout from '@/layouts/dashboard-layout-personal';
import { Head } from '@inertiajs/react';
import { useState } from 'react';

export default function Profile() {
    // State untuk mengelola input form
    const [formData, setFormData] = useState({
        fullName: 'Budi Santoso',
        nickname: '',
        gender: '',
        phone: '',
        bloodType: '',
        email: 'budisantoso@gmail.com',
        country: '',
        city: '',
    });

    // Handler untuk memperbarui state saat input berubah
    const handleChange = (
        e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>,
    ) => {
        const { id, value } = e.target;
        setFormData((prevData) => ({
            ...prevData,
            [id]: value,
        }));
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        alert('Simulasi: Data berhasil disimpan!');
        console.log('Data yang disubmit:', formData);
    };

    return (
        <DashboardLayout>
            <Head title="Edit Profil" />

            <div className="grid grid-cols-1 gap-8 lg:grid-cols-3">
                {/* ============================ */}
                {/* === KOLOM KIRI (KARTU) === */}
                {/* ============================ */}
                <div className="space-y-8 lg:col-span-1">
                    {/* --- Kartu Profil --- */}
                    <div className="rounded-2xl border border-gray-100 bg-white p-6 text-center shadow-sm">
                        <div className="flex flex-col items-center">
                            <img
                                src="/memoji-placeholder.png" // Pastikan file ada di folder public, atau ganti URL gambar lain
                                alt="Foto Profil"
                                className="mb-4 h-32 w-32 rounded-full border-4 border-yellow-50 object-cover shadow-lg"
                            />
                            <h5 className="mb-1 text-xl font-bold text-gray-900">
                                {formData.fullName || 'Nama Pengguna'}
                            </h5>
                            <span className="text-sm text-gray-500">
                                {formData.email}
                            </span>

                            <button
                                type="button"
                                className="mt-6 w-full rounded-lg bg-saintara-yellow py-2.5 text-sm font-bold text-saintara-black transition-colors hover:bg-yellow-400"
                            >
                                Ubah Foto
                            </button>
                        </div>
                    </div>

                    {/* --- Kartu Status Akun --- */}
                    <div className="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                        <h5 className="mb-4 border-b border-gray-100 pb-2 text-lg font-bold text-gray-900">
                            Status Akun
                        </h5>
                        <div className="flex items-center justify-between">
                            <span className="text-sm font-medium text-gray-600">
                                Tipe Akun :
                            </span>
                            <span className="rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700">
                                Personal Premium
                            </span>
                        </div>
                    </div>
                </div>

                {/* ============================ */}
                {/* === KOLOM KANAN (FORM) === */}
                {/* ============================ */}
                <div className="lg:col-span-2">
                    <div className="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm md:p-8">
                        <h5 className="mb-1 text-2xl font-bold text-gray-800">
                            Edit Data Diri
                        </h5>
                        <p className="mb-6 text-sm text-gray-500">
                            Perbarui informasi profil Anda di sini.
                        </p>

                        <hr className="mb-6 border-gray-100" />

                        <form className="space-y-6" onSubmit={handleSubmit}>
                            {/* Baris 1: Nama Lengkap & Panggilan */}
                            <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <label
                                        htmlFor="fullName"
                                        className="mb-2 block text-sm font-medium text-gray-700"
                                    >
                                        Nama Lengkap
                                    </label>
                                    <input
                                        id="fullName"
                                        type="text"
                                        className="w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-saintara-yellow focus:ring-saintara-yellow"
                                        value={formData.fullName}
                                        onChange={handleChange}
                                        required
                                    />
                                </div>
                                <div>
                                    <label
                                        htmlFor="nickname"
                                        className="mb-2 block text-sm font-medium text-gray-700"
                                    >
                                        Nama Panggilan
                                    </label>
                                    <input
                                        id="nickname"
                                        type="text"
                                        placeholder="Masukkan nama panggilan"
                                        className="w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-saintara-yellow focus:ring-saintara-yellow"
                                        value={formData.nickname}
                                        onChange={handleChange}
                                    />
                                </div>
                            </div>

                            {/* Baris 2: Jenis Kelamin & No Telepon */}
                            <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <label
                                        htmlFor="gender"
                                        className="mb-2 block text-sm font-medium text-gray-700"
                                    >
                                        Jenis Kelamin
                                    </label>
                                    <select
                                        id="gender"
                                        className="w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-saintara-yellow focus:ring-saintara-yellow"
                                        value={formData.gender}
                                        onChange={handleChange}
                                    >
                                        <option value="">
                                            Pilih Jenis Kelamin
                                        </option>
                                        <option value="pria">Pria</option>
                                        <option value="wanita">Wanita</option>
                                    </select>
                                </div>
                                <div>
                                    <label
                                        htmlFor="phone"
                                        className="mb-2 block text-sm font-medium text-gray-700"
                                    >
                                        No Telephone
                                    </label>
                                    <input
                                        id="phone"
                                        type="tel"
                                        placeholder="0812..."
                                        className="w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-saintara-yellow focus:ring-saintara-yellow"
                                        value={formData.phone}
                                        onChange={handleChange}
                                    />
                                </div>
                            </div>

                            {/* Baris 3: Golongan Darah & Email */}
                            <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <label
                                        htmlFor="bloodType"
                                        className="mb-2 block text-sm font-medium text-gray-700"
                                    >
                                        Golongan Darah
                                    </label>
                                    <select
                                        id="bloodType"
                                        className="w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-saintara-yellow focus:ring-saintara-yellow"
                                        value={formData.bloodType}
                                        onChange={handleChange}
                                    >
                                        <option value="">
                                            Pilih Gol. Darah
                                        </option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="AB">AB</option>
                                        <option value="O">O</option>
                                    </select>
                                </div>
                                <div>
                                    <label
                                        htmlFor="email"
                                        className="mb-2 block text-sm font-medium text-gray-700"
                                    >
                                        Email
                                    </label>
                                    <input
                                        id="email"
                                        type="email"
                                        className="w-full cursor-not-allowed rounded-lg border border-gray-300 bg-gray-100 p-2.5 text-sm text-gray-500"
                                        value={formData.email}
                                        onChange={handleChange}
                                        readOnly
                                        title="Email tidak dapat diubah"
                                    />
                                </div>
                            </div>

                            {/* Baris 4: Negara & Kota */}
                            <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <label
                                        htmlFor="country"
                                        className="mb-2 block text-sm font-medium text-gray-700"
                                    >
                                        Negara
                                    </label>
                                    <input
                                        id="country"
                                        type="text"
                                        placeholder="Indonesia"
                                        className="w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-saintara-yellow focus:ring-saintara-yellow"
                                        value={formData.country}
                                        onChange={handleChange}
                                    />
                                </div>
                                <div>
                                    <label
                                        htmlFor="city"
                                        className="mb-2 block text-sm font-medium text-gray-700"
                                    >
                                        Kota
                                    </label>
                                    <input
                                        id="city"
                                        type="text"
                                        placeholder="Jakarta"
                                        className="w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-saintara-yellow focus:ring-saintara-yellow"
                                        value={formData.city}
                                        onChange={handleChange}
                                    />
                                </div>
                            </div>

                            {/* Tombol Simpan */}
                            <div className="flex justify-end pt-6">
                                <button
                                    type="submit"
                                    className="rounded-lg bg-saintara-black px-8 py-3 text-sm font-bold text-white transition-all hover:bg-gray-800 focus:ring-4 focus:ring-gray-300 focus:outline-none"
                                >
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
