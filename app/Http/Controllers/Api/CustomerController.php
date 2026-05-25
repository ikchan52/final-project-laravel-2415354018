<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use App\Http\Resources\CustomerResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        // Fitur Search: Cari berdasarkan Nama atau Email
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        // Pagination: 10 data per halaman dengan urutan terbaru
        return CustomerResource::collection($query->latest()->paginate(10));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email|unique:customers,email',
            'phone'   => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        return DB::transaction(function () use ($data) {
            // 1. Buat Akun Login (User)
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make('password123'),
                'role'     => 'customer',
                'status'   => 'active',
            ]);

            // 2. Buat Profil Pelanggan
            $customer = Customer::create([
                'user_id' => $user->id,
                'name'    => $data['name'],
                'email'   => $data['email'],
                'address' => $data['address'],
                'phone'   => $data['phone'],
            ]);

            return (new CustomerResource($customer))
                ->additional(['message' => 'Customer dan Akun User berhasil dibuat'])
                ->response()
                ->setStatusCode(201);
        });
    }

    public function show(Customer $customer)
    {
        return new CustomerResource($customer);
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name'    => 'sometimes|required|string|max:255',
            'email'   => 'sometimes|required|email|unique:customers,email,' . $customer->id,
            'phone'   => 'sometimes|required|string|max:20',
            'address' => 'sometimes|required|string',
        ]);

        $customer->update($data);

        return (new CustomerResource($customer))
            ->additional(['message' => 'Data pelanggan berhasil diperbarui']);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(['message' => 'Pelanggan berhasil dihapus']);
    }
}
