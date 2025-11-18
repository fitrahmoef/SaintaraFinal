import DashboardLayoutInstansi from '@/layouts/dashboard-layout-instansi';
import { Head } from '@inertiajs/react';
import { HiChartPie, HiClipboardList, HiUserGroup } from 'react-icons/hi';

interface CharacterDistribution {
    character_name: string;
    count: number;
}

interface TestResult {
    id: number;
    test_date: string;
    characterType: {
        name: string;
        code: string;
    };
}

interface Props {
    institutionName: string;
    totalTests: number;
    testResults: {
        data: TestResult[];
    };
    characterDistribution: CharacterDistribution[];
}

export default function InstansiDashboard({
    institutionName,
    totalTests,
    testResults,
    characterDistribution,
}: Props) {
    return (
        <DashboardLayoutInstansi>
            <Head title="Dashboard Instansi" />

            <div className="space-y-6">
                {/* Institution Name */}
                <div className="rounded-3xl bg-gradient-to-r from-saintara-yellow to-yellow-400 p-6 shadow-lg">
                    <h1 className="text-3xl font-bold text-white">
                        {institutionName}
                    </h1>
                    <p className="mt-2 text-yellow-50">
                        Dashboard Manajemen Tes Karakter
                    </p>
                </div>

                {/* Statistics Cards */}
                <div className="grid grid-cols-1 gap-6 md:grid-cols-3">
                    {/* Total Tests */}
                    <div className="flex items-center gap-4 rounded-3xl bg-white p-6 shadow-sm">
                        <div className="flex h-16 w-16 items-center justify-center rounded-full bg-blue-100">
                            <HiClipboardList className="h-8 w-8 text-blue-600" />
                        </div>
                        <div>
                            <p className="text-xs text-gray-400">
                                Total Tes
                            </p>
                            <h3 className="text-3xl font-bold text-gray-800">
                                {totalTests}
                            </h3>
                        </div>
                    </div>

                    {/* Total Participants */}
                    <div className="flex items-center gap-4 rounded-3xl bg-white p-6 shadow-sm">
                        <div className="flex h-16 w-16 items-center justify-center rounded-full bg-green-100">
                            <HiUserGroup className="h-8 w-8 text-green-600" />
                        </div>
                        <div>
                            <p className="text-xs text-gray-400">
                                Total Peserta
                            </p>
                            <h3 className="text-3xl font-bold text-gray-800">
                                {totalTests}
                            </h3>
                        </div>
                    </div>

                    {/* Character Types */}
                    <div className="flex items-center gap-4 rounded-3xl bg-white p-6 shadow-sm">
                        <div className="flex h-16 w-16 items-center justify-center rounded-full bg-purple-100">
                            <HiChartPie className="h-8 w-8 text-purple-600" />
                        </div>
                        <div>
                            <p className="text-xs text-gray-400">
                                Tipe Karakter
                            </p>
                            <h3 className="text-3xl font-bold text-gray-800">
                                {characterDistribution.length}
                            </h3>
                        </div>
                    </div>
                </div>

                {/* Character Distribution */}
                <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    {/* Distribution Chart */}
                    <div className="rounded-3xl bg-white p-8 shadow-sm">
                        <h3 className="mb-6 text-xl font-bold text-gray-800">
                            Distribusi Tipe Karakter
                        </h3>
                        <div className="space-y-4">
                            {characterDistribution.length > 0 ? (
                                characterDistribution.map((item, index) => (
                                    <div key={index}>
                                        <div className="mb-2 flex justify-between text-sm">
                                            <span className="font-medium text-gray-700">
                                                {item.character_name}
                                            </span>
                                            <span className="font-bold text-saintara-yellow">
                                                {item.count} orang
                                            </span>
                                        </div>
                                        <div className="h-3 overflow-hidden rounded-full bg-gray-100">
                                            <div
                                                className="h-full rounded-full bg-saintara-yellow transition-all"
                                                style={{
                                                    width: `${(item.count / totalTests) * 100}%`,
                                                }}
                                            ></div>
                                        </div>
                                    </div>
                                ))
                            ) : (
                                <p className="text-center text-gray-400">
                                    Belum ada data distribusi
                                </p>
                            )}
                        </div>
                    </div>

                    {/* Recent Test Results */}
                    <div className="rounded-3xl bg-white p-8 shadow-sm">
                        <h3 className="mb-6 text-xl font-bold text-gray-800">
                            Hasil Tes Terbaru
                        </h3>
                        <div className="space-y-4">
                            {testResults.data.length > 0 ? (
                                testResults.data.slice(0, 5).map((result) => (
                                    <div
                                        key={result.id}
                                        className="flex items-center justify-between rounded-lg border border-gray-100 p-4"
                                    >
                                        <div className="flex items-center gap-4">
                                            <div className="flex h-12 w-12 items-center justify-center rounded-full bg-yellow-100">
                                                <span className="text-sm font-bold text-saintara-yellow">
                                                    {result.characterType.code}
                                                </span>
                                            </div>
                                            <div>
                                                <h4 className="font-semibold text-gray-800">
                                                    {result.characterType.name}
                                                </h4>
                                                <p className="text-xs text-gray-400">
                                                    {new Date(
                                                        result.test_date,
                                                    ).toLocaleDateString(
                                                        'id-ID',
                                                    )}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                ))
                            ) : (
                                <p className="text-center text-gray-400">
                                    Belum ada hasil tes
                                </p>
                            )}
                        </div>
                    </div>
                </div>

                {/* Quick Actions */}
                <div className="rounded-3xl bg-white p-8 shadow-sm">
                    <h3 className="mb-6 text-xl font-bold text-gray-800">
                        Aksi Cepat
                    </h3>
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <a
                            href="/instansi/formTesInstansi"
                            className="flex items-center justify-between rounded-lg border-2 border-saintara-yellow bg-yellow-50 p-4 transition-all hover:bg-saintara-yellow hover:text-white"
                        >
                            <span className="font-semibold">
                                Tambah Peserta Tes Baru
                            </span>
                            <HiUserGroup className="h-6 w-6" />
                        </a>
                        <a
                            href="/instansi/laporan"
                            className="flex items-center justify-between rounded-lg border-2 border-gray-200 bg-gray-50 p-4 transition-all hover:border-saintara-yellow hover:bg-yellow-50"
                        >
                            <span className="font-semibold">
                                Lihat Laporan Lengkap
                            </span>
                            <HiChartPie className="h-6 w-6" />
                        </a>
                    </div>
                </div>
            </div>
        </DashboardLayoutInstansi>
    );
}
