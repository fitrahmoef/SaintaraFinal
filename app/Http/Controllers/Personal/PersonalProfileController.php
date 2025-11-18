<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PersonalProfileController extends Controller
{
    /**
     * Get current user's profile
     */
    public function show()
    {
        $user = auth()->user();
        $customer = $user->customer;

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'profile' => [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'nama_lengkap' => $customer->nama_lengkap,
                'tanggal_lahir' => $customer->tanggal_lahir,
                'tempat_lahir' => $customer->tempat_lahir,
                'jenis_kelamin' => $customer->jenis_kelamin,
                'golongan_darah' => $customer->golongan_darah,
                'no_telepon' => $customer->no_telepon,
                'alamat' => $customer->alamat,
                'kota' => $customer->kota,
                'provinsi' => $customer->provinsi,
                'kode_pos' => $customer->kode_pos,
                'pekerjaan' => $customer->pekerjaan,
                'pendidikan_terakhir' => $customer->pendidikan_terakhir,
                'foto_profil' => $customer->foto_profil,
            ]
        ]);
    }

    /**
     * Update user's profile
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        $customer = $user->customer;

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found'
            ], 404);
        }

        $validated = $request->validate([
            // User fields
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],

            // Customer fields
            'nama_lengkap' => 'sometimes|string|max:255',
            'tanggal_lahir' => 'sometimes|date|before:today',
            'tempat_lahir' => 'sometimes|nullable|string|max:255',
            'jenis_kelamin' => ['sometimes', Rule::in(['L', 'P'])],
            'golongan_darah' => ['sometimes', Rule::in(['A', 'B', 'AB', 'O'])],
            'no_telepon' => 'sometimes|nullable|string|max:20',
            'alamat' => 'sometimes|nullable|string',
            'kota' => 'sometimes|nullable|string|max:100',
            'provinsi' => 'sometimes|nullable|string|max:100',
            'kode_pos' => 'sometimes|nullable|string|max:10',
            'pekerjaan' => 'sometimes|nullable|string|max:100',
            'pendidikan_terakhir' => ['sometimes', 'nullable', Rule::in([
                'SD', 'SMP', 'SMA', 'D3', 'S1', 'S2', 'S3'
            ])],
        ]);

        DB::beginTransaction();
        try {
            // Update User fields if provided
            $userFields = [];
            if (isset($validated['name'])) {
                $userFields['name'] = $validated['name'];
            }
            if (isset($validated['email'])) {
                $userFields['email'] = $validated['email'];

                // If email changed, mark as unverified
                if ($user->email !== $validated['email']) {
                    $userFields['email_verified_at'] = null;
                }
            }

            if (!empty($userFields)) {
                $user->update($userFields);
            }

            // Update Customer fields
            $customerFields = array_diff_key($validated, array_flip(['name', 'email']));
            if (!empty($customerFields)) {
                $customer->update($customerFields);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'profile' => [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'nama_lengkap' => $customer->nama_lengkap,
                    'tanggal_lahir' => $customer->tanggal_lahir,
                    'tempat_lahir' => $customer->tempat_lahir,
                    'jenis_kelamin' => $customer->jenis_kelamin,
                    'golongan_darah' => $customer->golongan_darah,
                    'no_telepon' => $customer->no_telepon,
                    'alamat' => $customer->alamat,
                    'kota' => $customer->kota,
                    'provinsi' => $customer->provinsi,
                    'kode_pos' => $customer->kode_pos,
                    'pekerjaan' => $customer->pekerjaan,
                    'pendidikan_terakhir' => $customer->pendidikan_terakhir,
                    'foto_profil' => $customer->foto_profil,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload profile photo
     */
    public function uploadPhoto(Request $request)
    {
        $user = auth()->user();
        $customer = $user->customer;

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found'
            ], 404);
        }

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048', // 2MB max
        ]);

        try {
            // Store photo
            $path = $request->file('photo')->store('profile_photos', 'public');

            // Delete old photo if exists
            if ($customer->foto_profil && \Storage::disk('public')->exists($customer->foto_profil)) {
                \Storage::disk('public')->delete($customer->foto_profil);
            }

            // Update customer record
            $customer->update(['foto_profil' => $path]);

            return response()->json([
                'success' => true,
                'message' => 'Photo uploaded successfully',
                'photo_url' => \Storage::url($path),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload photo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete profile photo
     */
    public function deletePhoto()
    {
        $user = auth()->user();
        $customer = $user->customer;

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found'
            ], 404);
        }

        try {
            // Delete photo file if exists
            if ($customer->foto_profil && \Storage::disk('public')->exists($customer->foto_profil)) {
                \Storage::disk('public')->delete($customer->foto_profil);
            }

            // Update customer record
            $customer->update(['foto_profil' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Photo deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete photo: ' . $e->getMessage()
            ], 500);
        }
    }
}
