import React, { useState } from "react";
import { useForm } from "@inertiajs/react";

export default function FormInstansi() {
  const [participants, setParticipants] = useState([
    {
      full_name: "",
      nickname: "",
      email: "",
      phone: "",
      blood_type: "",
      country: "",
      city: "",
      gender: "",
    },
  ]);

  const [excelFile, setExcelFile] = useState<File | null>(null);

  const addParticipant = () => {
    setParticipants([
      ...participants,
      {
        full_name: "",
        nickname: "",
        email: "",
        phone: "",
        blood_type: "",
        country: "",
        city: "",
        gender: "",
      },
    ]);
  };

  const removeParticipant = (index: number) => {
    if (participants.length > 1) {
      setParticipants(participants.filter((_, i) => i !== index));
    }
  };

  const updateField = (index: number, field: keyof typeof participants[number], value: string) => {
    const updated = [...participants];
    updated[index][field] = value;
    setParticipants(updated);
  };

  const submitForm = () => {
    console.log("Data dikirim:", participants);
  };

  return (
    <div className="min-h-screen px-6 py-10 bg-gradient-to-br from-yellow-50 to-yellow-100 text-black">
      <div className="max-w-4xl mx-auto bg-white shadow-lg rounded-2xl p-8 border">
        <h1 className="text-center text-2xl font-bold text-yellow-600">Tes Kepribadian Instansi</h1>
        <p className="text-center text-gray-600 mb-6">
          Tambahkan peserta atau unggah Excel (pilih salah satu atau keduanya).
        </p>

        {/* UPLOAD EXCEL */}
        <div className="mb-8">
          <label className="font-semibold block mb-2">Upload Excel (.xlsx)</label>
          <input
            type="file"
            accept=".xlsx"
            onChange={(e) => setExcelFile(e.target.files?.[0] || null)}
            className="w-full border p-2 rounded-md"
          />

          {excelFile && (
            <div className="mt-4 p-4 bg-green-100 rounded-lg border border-green-400">
              📄 File terpilih: <b>{excelFile.name}</b>
            </div>
          )}
        </div>

        {/* FORM PESERTA DINAMIS */}
        <h2 className="text-lg font-bold mb-3">Daftar Peserta (Input Manual)</h2>

        {participants.map((p, index) => (
          <div key={index} className="border p-4 rounded-xl mb-4 bg-gray-50">
            <div className="flex justify-between mb-2">
              <h3 className="font-semibold">Peserta #{index + 1}</h3>

              {participants.length > 1 && (
                <button
                  className="text-red-600 text-sm underline"
                  onClick={() => removeParticipant(index)}
                >
                  Hapus
                </button>
              )}
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <input placeholder="Nama Lengkap" className="input" onChange={(e) => updateField(index, "full_name", e.target.value)} />
              <input placeholder="Nama Panggilan" className="input" onChange={(e) => updateField(index, "nickname", e.target.value)} />
              <input placeholder="E-mail" className="input" onChange={(e) => updateField(index, "email", e.target.value)} />
              <input placeholder="Nomor Telepon" className="input" onChange={(e) => updateField(index, "phone", e.target.value)} />

              <select className="input" onChange={(e) => updateField(index, "blood_type", e.target.value)}>
                <option value="">Golongan Darah</option>
                <option>A</option><option>B</option><option>AB</option><option>O</option>
              </select>

              <input placeholder="Negara" className="input" onChange={(e) => updateField(index, "country", e.target.value)} />
              <input placeholder="Kota" className="input" onChange={(e) => updateField(index, "city", e.target.value)} />

              <select
                className="input"
                onChange={(e) => updateField(index, "gender", e.target.value)}
              >
                <option value="">Jenis Kelamin</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
              </select>
            </div>
          </div>
        ))}

        <button
          onClick={addParticipant}
          className="w-full bg-yellow-500 py-2 rounded-lg font-semibold hover:bg-yellow-600 transition mb-6"
        >
          + Tambah Peserta
        </button>

        {/* TOMBOL SUBMIT */}
        <button
          onClick={submitForm}
          className="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700"
        >
          Simpan dan Lanjutkan Tes
        </button>

        {/* PREVIEW TABEL (placeholder) */}
        <h2 className="text-lg font-bold mt-10 mb-3">Preview Data</h2>
        <table className="w-full text-sm border">
          <thead className="bg-blue-100">
            <tr>
              <th className="border p-2">Nama</th>
              <th className="border p-2">Email</th>
              <th className="border p-2">Kota</th>
              <th className="border p-2">Gender</th>
            </tr>
          </thead>
          <tbody>
            {participants.map((p, i) => (
              <tr key={i}>
                <td className="border p-2">{p.full_name}</td>
                <td className="border p-2">{p.email}</td>
                <td className="border p-2">{p.city}</td>
                <td className="border p-2">{p.gender}</td>
              </tr>
            ))}
          </tbody>
        </table>

      </div>
    </div>
  );
}
