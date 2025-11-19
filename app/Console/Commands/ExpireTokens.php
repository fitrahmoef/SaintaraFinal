<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TokenPurchase;
use App\Models\Transaction;
use App\Models\TestSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpireTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tokens:expire
                            {--dry-run : Run without making changes}
                            {--verbose : Show detailed output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire tokens, transactions, and test sessions that have passed their expiry date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $isVerbose = $this->option('verbose');

        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
        }

        $this->info('🚀 Starting expiration check...');
        $this->newLine();

        // 1. Expire Tokens
        $this->info('1️⃣  Checking expired tokens...');
        $expiredTokensCount = $this->expireTokens($isDryRun, $isVerbose);
        $this->info("   ✓ {$expiredTokensCount} tokens expired");
        $this->newLine();

        // 2. Expire Pending Transactions
        $this->info('2️⃣  Checking expired pending transactions...');
        $expiredTransactionsCount = $this->expireTransactions($isDryRun, $isVerbose);
        $this->info("   ✓ {$expiredTransactionsCount} transactions expired");
        $this->newLine();

        // 3. Expire Test Sessions
        $this->info('3️⃣  Checking expired test sessions...');
        $expiredSessionsCount = $this->expireSessions($isDryRun, $isVerbose);
        $this->info("   ✓ {$expiredSessionsCount} sessions expired");
        $this->newLine();

        $this->info('✅ Expiration check completed successfully!');
        $this->newLine();

        if ($isDryRun) {
            $this->warn('⚠️  This was a dry run - no actual changes were made');
        } else {
            $this->info('📝 Summary:');
            $this->table(
                ['Type', 'Count'],
                [
                    ['Expired Tokens', $expiredTokensCount],
                    ['Expired Transactions', $expiredTransactionsCount],
                    ['Expired Sessions', $expiredSessionsCount],
                    ['Total', $expiredTokensCount + $expiredTransactionsCount + $expiredSessionsCount],
                ]
            );
        }

        Log::info('Expiration command completed', [
            'expired_tokens' => $expiredTokensCount,
            'expired_transactions' => $expiredTransactionsCount,
            'expired_sessions' => $expiredSessionsCount,
            'dry_run' => $isDryRun,
        ]);

        return Command::SUCCESS;
    }

    /**
     * Expire tokens that have passed their expiry date
     */
    private function expireTokens(bool $isDryRun, bool $isVerbose): int
    {
        $query = TokenPurchase::where('status', 'aktif')
            ->whereNotNull('tanggal_kadaluarsa')
            ->where('tanggal_kadaluarsa', '<', now());

        if ($isVerbose) {
            $tokens = $query->get();
            foreach ($tokens as $token) {
                $this->line("   • Token {$token->kode_token} (Customer ID: {$token->customer_id}) expired on {$token->tanggal_kadaluarsa}");
            }
        }

        $count = $query->count();

        if (!$isDryRun && $count > 0) {
            $query->update([
                'status' => 'kadaluarsa',
                'updated_at' => now(),
            ]);
        }

        return $count;
    }

    /**
     * Expire pending transactions that have passed their expiry date
     */
    private function expireTransactions(bool $isDryRun, bool $isVerbose): int
    {
        $query = Transaction::where('status_pembayaran', 'pending')
            ->whereNotNull('waktu_kadaluarsa')
            ->where('waktu_kadaluarsa', '<', now());

        if ($isVerbose) {
            $transactions = $query->get();
            foreach ($transactions as $transaction) {
                $this->line("   • Transaction {$transaction->kode_transaksi} expired on {$transaction->waktu_kadaluarsa}");
            }
        }

        $count = $query->count();

        if (!$isDryRun && $count > 0) {
            $query->update([
                'status_pembayaran' => 'kadaluarsa',
                'updated_at' => now(),
            ]);

            // Log expired transactions
            if ($isVerbose) {
                $this->warn("   ⚠️  Expired transactions will not generate tokens");
            }
        }

        return $count;
    }

    /**
     * Expire test sessions that have passed their expiry time
     */
    private function expireSessions(bool $isDryRun, bool $isVerbose): int
    {
        $query = TestSession::where('status', 'in_progress')
            ->whereNotNull('waktu_expired')
            ->where('waktu_expired', '<', now());

        if ($isVerbose) {
            $sessions = $query->get();
            foreach ($sessions as $session) {
                $this->line("   • Session {$session->session_token} (Customer ID: {$session->customer_id}) expired on {$session->waktu_expired}");
            }
        }

        $count = $query->count();

        if (!$isDryRun && $count > 0) {
            DB::beginTransaction();
            try {
                $sessions = $query->get();

                foreach ($sessions as $session) {
                    $session->update([
                        'status' => 'expired',
                        'token_locked' => false,
                        'updated_at' => now(),
                    ]);
                }

                DB::commit();

                if ($isVerbose) {
                    $this->warn("   ⚠️  Expired sessions unlocked tokens (tokens not consumed)");
                }
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("   ❌ Error expiring sessions: " . $e->getMessage());
                Log::error('Failed to expire sessions', ['error' => $e->getMessage()]);
                return 0;
            }
        }

        return $count;
    }
}
