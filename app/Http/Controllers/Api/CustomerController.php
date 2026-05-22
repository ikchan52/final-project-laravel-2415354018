<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Display a listing of the customers.
     */
    public function index(Request $request)
    {
        $status = $request->query('status');

        $customers = Customer::when($status, function ($query, $status) {
            return $query->where('status', $status === 'active');
        })->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'List data pelanggan',
            'data'    => $customers
        ], 200);
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:customers,email',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        // customer_id tidak perlu di-input, karena sudah otomatis dari Model boot
        $customer = Customer::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Pelanggan berhasil ditambahkan',
            'data'    => $customer
        ], 201);
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer)
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail data pelanggan',
            'data'    => $customer
        ], 200);
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validator = Validator::make($request->all(), [
            'name'    => 'sometimes|required|string|max:255',
            'email'   => 'sometimes|required|email|unique:customers,email,' . $customer->id,
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'status'  => 'sometimes|required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Update gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $customer->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data pelanggan berhasil diperbarui',
            'data'    => $customer
        ], 200);
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pelanggan berhasil dihapus'
        ], 200);
    }
}
