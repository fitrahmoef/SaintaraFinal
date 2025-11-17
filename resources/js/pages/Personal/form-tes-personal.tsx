import React from "react";
import { useForm } from "@inertiajs/react";

export default function FormKepribadian() {
  const { data, setData, post, processing } = useForm({
    full_name: "",
    nickname: "",
    email: "",
    phone: "",
    blood_type: "",
    country: "",
    city: "",
    gender: "",
  });

  const submit = (e: React.FormEvent) => {
    e.preventDefault();
    post("/tes/kepribadian/start");
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-yellow-50 to-yellow-100 px-4">
            <div className="w-full max-w-2xl bg-white rounded-2xl shadow-lg p-8 border text-black">
                
                {/* Header */}
                <div className="text-center mb-6">
                    <h1 className="text-2xl font-bold text-yellow-600">Saintara</h1>
                    <h2 className="text-xl font-semibold mt-2">Formulir Tes Kepribadian</h2>
                    <p className="text-gray-600 text-sm mt-1">
                        Silakan isi data diri Anda sebelum memulai tes.
                    </p>
                </div>

                {/* Form */}
                <form className="grid grid-cols-1 gap-4 md:grid-cols-2">

                    <div>
                        <label className="block text-sm font-medium">Nama Lengkap</label>
                        <input
                            type="text"
                            className="w-full border rounded-md px-3 py-2 bg-gray-50 focus:ring focus:ring-yellow-300"
                            placeholder="Budi Santoso"
                        />
                    </div>

                    <div>
                        <label className="block text-sm font-medium">Nama Panggilan</label>
                        <input
                            type="text"
                            className="w-full border rounded-md px-3 py-2 bg-gray-50 focus:ring focus:ring-yellow-300"
                            placeholder="Budi"
                        />
                    </div>

                    <div className="col-span-2">
                        <label className="block text-sm font-medium">E-mail</label>
                        <input
                            type="email"
                            className="w-full border rounded-md px-3 py-2 bg-gray-50 focus:ring focus:ring-yellow-300"
                            placeholder="budisantoso@gmail.com"
                        />
                    </div>

                    <div>
                        <label className="block text-sm font-medium">Nomor Telepon</label>
                        <input
                            type="text"
                            className="w-full border rounded-md px-3 py-2 bg-gray-50 focus:ring focus:ring-yellow-300"
                            placeholder="08xxxxxxxxxx"
                        />
                    </div>

                    <div>
                        <label className="block text-sm font-medium">Golongan Darah</label>
                        <select className="w-full border rounded-md px-3 py-2 bg-gray-50">
                            <option>Pilih Golongan Darah</option>
                            <option>A</option>
                            <option>B</option>
                            <option>AB</option>
                            <option>O</option>
                        </select>
                    </div>

                    <div>
                        <label className="block text-sm font-medium">Negara</label>
                        <input
                            type="text"
                            className="w-full border rounded-md px-3 py-2 bg-gray-50"
                            placeholder="Indonesia"
                        />
                    </div>

                    <div>
                        <label className="block text-sm font-medium">Kota</label>
                        <input
                            type="text"
                            className="w-full border rounded-md px-3 py-2 bg-gray-50"
                            placeholder="Jakarta"
                        />
                    </div>

                    <div className="col-span-2">
                        <label className="block text-sm font-medium mb-1">Jenis Kelamin</label>
                        <div className="flex gap-6">
                            <label className="flex items-center gap-2">
                                <input type="radio" name="gender" className="accent-yellow-500" />
                                Laki-laki
                            </label>
                            <label className="flex items-center gap-2">
                                <input type="radio" name="gender" className="accent-yellow-500" />
                                Perempuan
                            </label>
                        </div>
                    </div>
                </form>

                {/* Submit Button */}
                <div className="mt-6 text-center">
                    <button className="w-full bg-yellow-500 hover:bg-yellow-600 text-black font-semibold py-3 rounded-lg transition">
                        Lanjutkan ke Tes
                    </button>
                </div>
            </div>
        </div>
  );
}
