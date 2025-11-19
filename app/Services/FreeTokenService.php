<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class FreeTokenService
{
    /**
     * Number of free tokens to grant to new users
     */
    const FREE_TOKENS_COUNT = 1;

    /**
     * Grant free tokens to a new customer/user
     *
     * @param Customer $customer
     * @return bool
     */
    public function grantFreeTokens(Customer $customer): bool
    {
        // Check if tokens have already been granted
        if ($customer->free_tokens_granted) {
            Log::info('Free tokens already granted', ['customer_id' => $customer->id]);
            return false;
        }

        // Grant free tokens
        $customer->free_tokens_granted = true;
        $customer->free_token_count = self::FREE_TOKENS_COUNT;
        $customer->free_tokens_granted_at = now();
        $customer->save();

        Log::info('Free tokens granted successfully', [
            'customer_id' => $customer->id,
            'token_count' => self::FREE_TOKENS_COUNT,
        ]);

        return true;
    }

    /**
     * Check if customer has free tokens available
     *
     * @param Customer $customer
     * @return bool
     */
    public function hasFreeTokens(Customer $customer): bool
    {
        return $customer->free_token_count > 0;
    }

    /**
     * Get available free token count
     *
     * @param Customer $customer
     * @return int
     */
    public function getFreeTokenCount(Customer $customer): int
    {
        return $customer->free_token_count ?? 0;
    }

    /**
     * Use free tokens for a test
     *
     * @param Customer $customer
     * @param int $tokenRequired
     * @return bool
     */
    public function useFreeTokens(Customer $customer, int $tokenRequired = 1): bool
    {
        if ($customer->free_token_count < $tokenRequired) {
            return false;
        }

        $customer->free_token_count -= $tokenRequired;
        $customer->save();

        Log::info('Free tokens used', [
            'customer_id' => $customer->id,
            'tokens_used' => $tokenRequired,
            'remaining' => $customer->free_token_count,
        ]);

        return true;
    }

    /**
     * Get total available tokens (free + purchased)
     *
     * @param Customer $customer
     * @return array
     */
    public function getTotalTokenBalance(Customer $customer): array
    {
        // Get free tokens
        $freeTokens = $this->getFreeTokenCount($customer);

        // Get purchased tokens
        $tokenPurchases = $customer->tokenPurchases()->active()->get();
        $purchasedTotal = $tokenPurchases->sum('jumlah_token');
        $purchasedUsed = $tokenPurchases->sum('jumlah_terpakai');
        $purchasedAvailable = $purchasedTotal - $purchasedUsed;

        // Total available
        $totalAvailable = $freeTokens + $purchasedAvailable;

        return [
            'free_tokens' => $freeTokens,
            'purchased_total' => $purchasedTotal,
            'purchased_used' => $purchasedUsed,
            'purchased_available' => $purchasedAvailable,
            'total_available' => $totalAvailable,
            'total_used' => $purchasedUsed, // Free tokens don't count as "used" in purchased
        ];
    }

    /**
     * Create customer profile with free tokens for a user
     *
     * @param User $user
     * @return Customer
     */
    public function createCustomerWithFreeTokens(User $user): Customer
    {
        $customer = Customer::create([
            'user_id' => $user->id,
            'nama_lengkap' => $user->name,
            'nama_panggilan' => $user->namapanggilan,
            'nomor_telepon' => $user->notelp,
            'negara' => $user->negara,
            'kota' => $user->kota,
            'free_tokens_granted' => true,
            'free_token_count' => self::FREE_TOKENS_COUNT,
            'free_tokens_granted_at' => now(),
        ]);

        Log::info('Customer created with free tokens', [
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'free_tokens' => self::FREE_TOKENS_COUNT,
        ]);

        return $customer;
    }
}
