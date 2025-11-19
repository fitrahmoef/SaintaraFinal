import { useState, useEffect, useCallback, useRef } from 'react';
import { Head, router } from '@inertiajs/react';
import axios from 'axios';
import {
    HiClock,
    HiCheckCircle,
    HiExclamationCircle,
    HiChevronLeft,
    HiChevronRight,
    HiSave
} from 'react-icons/hi';

interface Question {
    id: number;
    nomor_soal: number;
    pertanyaan: string;
    tipe_soal: string;
    pilihan_jawaban: any;
}

interface TestData {
    id: number;
    nama: string;
    deskripsi: string;
    jumlah_soal: number;
    durasi: number;
    questions: Question[];
}

interface SessionData {
    session_token: string;
    status: string;
    current_question: number;
    jawaban: Record<number, any>;
    waktu_mulai: string;
    waktu_expired: string;
    remaining_time: number;
    progress_percentage: number;
    test: TestData;
}

export default function TestExecution() {
    const [session, setSession] = useState<SessionData | null>(null);
    const [currentQuestionIndex, setCurrentQuestionIndex] = useState(0);
    const [jawaban, setJawaban] = useState<Record<number, any>>({});
    const [remainingTime, setRemainingTime] = useState(0);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [isSaving, setIsSaving] = useState(false);
    const [lastSaved, setLastSaved] = useState<Date | null>(null);
    const [error, setError] = useState<string | null>(null);
    const [isLoading, setIsLoading] = useState(true);

    const autoSaveIntervalRef = useRef<NodeJS.Timeout | null>(null);
    const timerIntervalRef = useRef<NodeJS.Timeout | null>(null);
    const localStorageKey = useRef('test_session_backup');

    // Get test ID from URL params
    const urlParams = new URLSearchParams(window.location.search);
    const testId = urlParams.get('test_id');
    const sessionToken = urlParams.get('session_token');

    // Prevent browser back/refresh
    useEffect(() => {
        const handleBeforeUnload = (e: BeforeUnloadEvent) => {
            e.preventDefault();
            e.returnValue = 'Anda yakin ingin meninggalkan tes? Progress akan disimpan.';
            return e.returnValue;
        };

        window.addEventListener('beforeunload', handleBeforeUnload);
        return () => window.removeEventListener('beforeunload', handleBeforeUnload);
    }, []);

    // Load or start session
    useEffect(() => {
        if (sessionToken) {
            // Resume existing session
            resumeSession(sessionToken);
        } else if (testId) {
            // Start new session
            startNewSession(parseInt(testId));
        } else {
            setError('Invalid test session');
            setIsLoading(false);
        }
    }, [testId, sessionToken]);

    // Start new test session
    const startNewSession = async (testId: number) => {
        try {
            const response = await axios.post('/api/personal/tests/session/start', {
                test_id: testId
            });

            if (response.data.success) {
                const sessionData = response.data.session;
                setSession(sessionData);
                setJawaban(sessionData.jawaban || {});
                setCurrentQuestionIndex(sessionData.current_question || 0);
                setRemainingTime(sessionData.remaining_time);

                // Save to localStorage as backup
                localStorage.setItem(localStorageKey.current, JSON.stringify(sessionData));

                // Update URL with session token
                window.history.replaceState({}, '', `?session_token=${sessionData.session_token}`);

                startTimers();
                setIsLoading(false);
            }
        } catch (err: any) {
            setError(err.response?.data?.message || 'Gagal memulai tes');
            setIsLoading(false);
        }
    };

    // Resume existing session
    const resumeSession = async (token: string) => {
        try {
            const response = await axios.get('/api/personal/tests/session/status', {
                params: { session_token: token }
            });

            if (response.data.success) {
                const sessionData = response.data.session;
                setSession(sessionData);
                setJawaban(sessionData.jawaban || {});
                setCurrentQuestionIndex(sessionData.current_question || 0);
                setRemainingTime(sessionData.remaining_time);

                // Save to localStorage as backup
                localStorage.setItem(localStorageKey.current, JSON.stringify(sessionData));

                startTimers();
                setIsLoading(false);
            }
        } catch (err: any) {
            setError(err.response?.data?.message || 'Gagal melanjutkan tes');
            setIsLoading(false);
        }
    };

    // Start timer and auto-save
    const startTimers = () => {
        // Countdown timer
        timerIntervalRef.current = setInterval(() => {
            setRemainingTime(prev => {
                if (prev <= 0) {
                    handleTimeExpired();
                    return 0;
                }
                return prev - 1;
            });
        }, 1000);

        // Auto-save every 30 seconds
        autoSaveIntervalRef.current = setInterval(() => {
            saveProgress(false);
        }, 30000);
    };

    // Cleanup timers
    useEffect(() => {
        return () => {
            if (timerIntervalRef.current) clearInterval(timerIntervalRef.current);
            if (autoSaveIntervalRef.current) clearInterval(autoSaveIntervalRef.current);
        };
    }, []);

    // Save progress to server
    const saveProgress = async (showNotification = false) => {
        if (!session) return;

        setIsSaving(true);
        try {
            await axios.post('/api/personal/tests/session/save-progress', {
                session_token: session.session_token,
                current_question: currentQuestionIndex,
                jawaban: jawaban
            });

            setLastSaved(new Date());

            // Update localStorage backup
            localStorage.setItem(localStorageKey.current, JSON.stringify({
                ...session,
                current_question: currentQuestionIndex,
                jawaban: jawaban
            }));

            if (showNotification) {
                // Could add toast notification here
                console.log('Progress saved');
            }
        } catch (err) {
            console.error('Failed to save progress:', err);
        } finally {
            setIsSaving(false);
        }
    };

    // Handle answer change
    const handleAnswerChange = (questionId: number, answer: any) => {
        setJawaban(prev => ({
            ...prev,
            [questionId]: answer
        }));
    };

    // Navigate to question
    const goToQuestion = (index: number) => {
        if (index >= 0 && index < (session?.test.questions.length || 0)) {
            saveProgress(false); // Auto-save before navigating
            setCurrentQuestionIndex(index);
        }
    };

    // Handle time expired
    const handleTimeExpired = () => {
        if (timerIntervalRef.current) clearInterval(timerIntervalRef.current);
        if (autoSaveIntervalRef.current) clearInterval(autoSaveIntervalRef.current);

        alert('Waktu tes telah habis! Jawaban Anda akan otomatis disubmit.');
        submitTest();
    };

    // Submit test
    const submitTest = async () => {
        if (!session) return;

        // Confirm submission
        if (remainingTime > 0) {
            const confirmed = window.confirm(
                'Apakah Anda yakin ingin submit tes? Pastikan semua jawaban sudah benar.'
            );
            if (!confirmed) return;
        }

        setIsSubmitting(true);

        try {
            const response = await axios.post('/api/personal/tests/session/submit', {
                session_token: session.session_token,
                jawaban: jawaban
            });

            if (response.data.success) {
                // Clear timers
                if (timerIntervalRef.current) clearInterval(timerIntervalRef.current);
                if (autoSaveIntervalRef.current) clearInterval(autoSaveIntervalRef.current);

                // Clear localStorage backup
                localStorage.removeItem(localStorageKey.current);

                // Redirect to results page
                router.visit(`/personal/hasilTes`, {
                    preserveState: false
                });
            }
        } catch (err: any) {
            setError(err.response?.data?.message || 'Gagal submit tes');
            setIsSubmitting(false);
        }
    };

    // Format time display
    const formatTime = (seconds: number): string => {
        const minutes = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    };

    // Get time color based on remaining time
    const getTimeColor = (): string => {
        const percentage = (remainingTime / (session?.test.durasi ? session.test.durasi * 60 : 1)) * 100;
        if (percentage > 50) return 'text-green-600';
        if (percentage > 20) return 'text-yellow-600';
        return 'text-red-600';
    };

    // Loading state
    if (isLoading) {
        return (
            <div className="min-h-screen flex items-center justify-center bg-gray-50">
                <div className="text-center">
                    <div className="animate-spin rounded-full h-16 w-16 border-b-2 border-yellow-500 mx-auto mb-4"></div>
                    <p className="text-gray-600">Memuat tes...</p>
                </div>
            </div>
        );
    }

    // Error state
    if (error || !session) {
        return (
            <div className="min-h-screen flex items-center justify-center bg-gray-50">
                <div className="bg-white p-8 rounded-xl shadow-lg max-w-md text-center">
                    <HiExclamationCircle className="text-red-500 text-6xl mx-auto mb-4" />
                    <h2 className="text-2xl font-bold text-gray-800 mb-2">Terjadi Kesalahan</h2>
                    <p className="text-gray-600 mb-6">{error || 'Session tidak valid'}</p>
                    <button
                        onClick={() => router.visit('/personal/daftarTes')}
                        className="bg-yellow-500 hover:bg-yellow-600 text-black font-semibold py-2 px-6 rounded-lg"
                    >
                        Kembali ke Daftar Tes
                    </button>
                </div>
            </div>
        );
    }

    const currentQuestion = session.test.questions[currentQuestionIndex];
    const totalQuestions = session.test.questions.length;
    const answeredCount = Object.keys(jawaban).length;

    return (
        <div className="min-h-screen bg-gray-50">
            <Head title={`Tes: ${session.test.nama}`} />

            {/* Header with Timer */}
            <div className="bg-white shadow-md sticky top-0 z-50">
                <div className="max-w-6xl mx-auto px-4 py-4">
                    <div className="flex items-center justify-between">
                        <div>
                            <h1 className="text-xl font-bold text-gray-800">{session.test.nama}</h1>
                            <p className="text-sm text-gray-600">
                                Soal {currentQuestionIndex + 1} dari {totalQuestions}
                            </p>
                        </div>

                        <div className="flex items-center gap-6">
                            {/* Auto-save indicator */}
                            {isSaving && (
                                <div className="flex items-center gap-2 text-blue-600">
                                    <HiSave className="animate-pulse" />
                                    <span className="text-sm">Menyimpan...</span>
                                </div>
                            )}
                            {lastSaved && !isSaving && (
                                <div className="text-sm text-gray-500">
                                    Tersimpan {new Date().getTime() - lastSaved.getTime() < 60000 ? 'baru saja' : 'otomatis'}
                                </div>
                            )}

                            {/* Timer */}
                            <div className={`flex items-center gap-2 font-bold text-lg ${getTimeColor()}`}>
                                <HiClock className="text-2xl" />
                                <span>{formatTime(remainingTime)}</span>
                            </div>
                        </div>
                    </div>

                    {/* Progress bar */}
                    <div className="mt-3">
                        <div className="flex justify-between text-sm text-gray-600 mb-1">
                            <span>Progress: {answeredCount}/{totalQuestions} soal dijawab</span>
                            <span>{session.progress_percentage.toFixed(0)}%</span>
                        </div>
                        <div className="w-full bg-gray-200 rounded-full h-2">
                            <div
                                className="bg-yellow-500 h-2 rounded-full transition-all duration-300"
                                style={{ width: `${session.progress_percentage}%` }}
                            ></div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Main Content */}
            <div className="max-w-4xl mx-auto px-4 py-8">
                {/* Question Card */}
                <div className="bg-white rounded-xl shadow-lg p-8 mb-6">
                    <div className="mb-6">
                        <div className="flex items-center gap-3 mb-4">
                            <span className="bg-yellow-500 text-black font-bold px-4 py-2 rounded-lg">
                                Soal {currentQuestion.nomor_soal}
                            </span>
                            {jawaban[currentQuestion.id] && (
                                <HiCheckCircle className="text-green-500 text-2xl" />
                            )}
                        </div>
                        <p className="text-lg text-gray-800 leading-relaxed">
                            {currentQuestion.pertanyaan}
                        </p>
                    </div>

                    {/* Answer Options */}
                    <div className="space-y-3">
                        {currentQuestion.tipe_soal === 'pilihan_ganda' && currentQuestion.pilihan_jawaban.map((option: any, idx: number) => (
                            <label
                                key={idx}
                                className={`flex items-center gap-4 p-4 border-2 rounded-lg cursor-pointer transition-all ${
                                    jawaban[currentQuestion.id] === option.value
                                        ? 'border-yellow-500 bg-yellow-50'
                                        : 'border-gray-200 hover:border-yellow-300 hover:bg-gray-50'
                                }`}
                            >
                                <input
                                    type="radio"
                                    name={`question_${currentQuestion.id}`}
                                    value={option.value}
                                    checked={jawaban[currentQuestion.id] === option.value}
                                    onChange={(e) => handleAnswerChange(currentQuestion.id, e.target.value)}
                                    className="w-5 h-5 text-yellow-500 focus:ring-yellow-500"
                                />
                                <span className="text-gray-800">{option.text}</span>
                            </label>
                        ))}

                        {currentQuestion.tipe_soal === 'skala' && (
                            <div className="py-4">
                                <div className="flex justify-between items-center mb-4">
                                    {[1, 2, 3, 4, 5].map((value) => (
                                        <button
                                            key={value}
                                            onClick={() => handleAnswerChange(currentQuestion.id, value)}
                                            className={`w-16 h-16 rounded-full font-bold text-lg transition-all ${
                                                jawaban[currentQuestion.id] === value
                                                    ? 'bg-yellow-500 text-black scale-110'
                                                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                            }`}
                                        >
                                            {value}
                                        </button>
                                    ))}
                                </div>
                                <div className="flex justify-between text-sm text-gray-600">
                                    <span>Sangat Tidak Setuju</span>
                                    <span>Sangat Setuju</span>
                                </div>
                            </div>
                        )}

                        {currentQuestion.tipe_soal === 'essay' && (
                            <textarea
                                value={jawaban[currentQuestion.id] || ''}
                                onChange={(e) => handleAnswerChange(currentQuestion.id, e.target.value)}
                                className="w-full border-2 border-gray-200 rounded-lg p-4 focus:border-yellow-500 focus:ring focus:ring-yellow-200"
                                rows={6}
                                placeholder="Tulis jawaban Anda di sini..."
                            />
                        )}
                    </div>
                </div>

                {/* Navigation */}
                <div className="flex items-center justify-between mb-6">
                    <button
                        onClick={() => goToQuestion(currentQuestionIndex - 1)}
                        disabled={currentQuestionIndex === 0}
                        className={`flex items-center gap-2 px-6 py-3 rounded-lg font-semibold transition ${
                            currentQuestionIndex === 0
                                ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                                : 'bg-gray-600 text-white hover:bg-gray-700'
                        }`}
                    >
                        <HiChevronLeft />
                        Soal Sebelumnya
                    </button>

                    <button
                        onClick={() => saveProgress(true)}
                        className="flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition"
                    >
                        <HiSave />
                        Simpan Progress
                    </button>

                    {currentQuestionIndex < totalQuestions - 1 ? (
                        <button
                            onClick={() => goToQuestion(currentQuestionIndex + 1)}
                            className="flex items-center gap-2 px-6 py-3 bg-yellow-500 text-black rounded-lg font-semibold hover:bg-yellow-600 transition"
                        >
                            Soal Selanjutnya
                            <HiChevronRight />
                        </button>
                    ) : (
                        <button
                            onClick={submitTest}
                            disabled={isSubmitting}
                            className={`flex items-center gap-2 px-8 py-3 rounded-lg font-semibold transition ${
                                isSubmitting
                                    ? 'bg-gray-400 cursor-not-allowed'
                                    : 'bg-green-600 text-white hover:bg-green-700'
                            }`}
                        >
                            {isSubmitting ? 'Mengirim...' : 'Submit Tes'}
                        </button>
                    )}
                </div>

                {/* Question Navigator */}
                <div className="bg-white rounded-xl shadow-lg p-6">
                    <h3 className="font-semibold text-gray-800 mb-4">Navigator Soal</h3>
                    <div className="grid grid-cols-10 gap-2">
                        {session.test.questions.map((q, idx) => (
                            <button
                                key={q.id}
                                onClick={() => goToQuestion(idx)}
                                className={`w-10 h-10 rounded-lg font-semibold transition ${
                                    idx === currentQuestionIndex
                                        ? 'bg-yellow-500 text-black ring-2 ring-yellow-600'
                                        : jawaban[q.id]
                                        ? 'bg-green-500 text-white hover:bg-green-600'
                                        : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                }`}
                            >
                                {q.nomor_soal}
                            </button>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
}
