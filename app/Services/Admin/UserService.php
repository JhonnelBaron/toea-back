<?php

namespace App\Services\Admin;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function get()
    {
        $users = User::with('nominee')->get();

        return [
            'status' => 200,
            'message' => 'Users retrieved successfully.',
            'data' => UserResource::collection($users)
        ];

    }

    public function add($data)
    {
        $data['password'] = bcrypt($data['password']);
        $data['email_verified_at'] = now(); // mark as verified

        $user = User::create($data);

        if ($user->user_type === 'nominee'){
            $user->nominee()->create([
                'nominee_type' => $data['nominee_type'],
                'nominee_category' => $data['nominee_category'],
                'region' => $data['region'],
                'province' => $data['province'],
                'nominee_name' => $data['nominee_name'],
                'status' => $data['status'] ?? 'pending',
            ]);
        }

        return [
            'status' => 201,
            'message' => 'User created successfully.',
            'data' => $user->load('nominee')
        ];
    }

    public function show($id)
    {
        $user = User::with('nominee')->find($id);
        if (!$user) {
            return [
                'status' => 404,
                'message' => 'User not found.',
            ];
        }

        return [
            'status' => 200,
            'message' => 'User retrieved successfully.',
            'data' => new UserResource($user)
        ];
    }

    public function update($id, $data)
    {
        $user = User::find($id);
        if (!$user) {
            return [
                'status' => 404,
                'message' => 'User not found.',
            ];
        }

      
        // Hash password if provided
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

            if ($user->user_type === 'nominee') {
                $user->nominee()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nominee_type'     => $data['nominee_type'] ?? $user->nominee?->nominee_type,
                        'nominee_category' => $data['nominee_category'] ?? $user->nominee?->nominee_category,
                        'region'           => $data['region'] ?? $user->nominee?->region,
                        'province'         => $data['province'] ?? $user->nominee?->province,
                        'nominee_name'     => $data['nominee_name'] ?? $user->nominee?->nominee_name,
                        'status'           => $data['status'] ?? $user->nominee?->status,
                    ]
                );
            }

        return [
            'status' => 200,
            'message' => 'User updated successfully.',
            'data' => $user->load('nominee'),
        ];
    }

    public function delete($id)
    {
        $user = User::find($id);
        if (!$user) {
            return [
                'status' => 404,
                'message' => 'User not found.',
            ];
        }

        $user->delete();

        return [
            'status' => 200,
            'message' => 'User deleted successfully.',
        ];
    }
}