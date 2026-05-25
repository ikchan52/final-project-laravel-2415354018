<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Menampilkan daftar layanan dengan filter status.
     */
    public function index(Request $request): JsonResponse
    {
        $status = $request->query("status");
        $query = Service::query();

        if ($status !== null) {
            // Validasi input status
            if (!in_array($status, ["active", "inactive"], true)) {
                return response()->json([
                    "success" => false,
                    "message" => "Validation failed",
                    "errors" => ["status" => ["The selected status is invalid."]],
                ], 422);
            }
            $query->where("status", $status);
        }

        return response()->json([
            "success" => true,
            "message" => "Services retrieved successfully",
            "data" => $query->latest()->get(),
        ]);
    }

    /**
     * Menyimpan layanan baru.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            "name" => ["required", "string", "max:255"],
            "price" => ["required", "integer", "min:0"],
            "description" => ["nullable", "string"],
            "status" => ["required", "in:active,inactive"], // Pakai Enum/String
        ]);

        $service = Service::create($data);

        return response()->json([
            "success" => true,
            "message" => "Service created successfully",
            "data" => $service,
        ], 201); // 201 Created untuk data baru
    }

    public function show(Service $service): JsonResponse
    {
        return response()->json([
            "success" => true,
            "message" => "Service retrieved successfully",
            "data" => $service,
        ]);
    }

    public function update(Request $request, Service $service): JsonResponse
    {
        $data = $request->validate([
            "name" => ["sometimes", "string", "max:255"],
            "price" => ["sometimes", "integer", "min:0"],
            "description" => ["nullable", "string"],
            "status" => ["sometimes", "in:active,inactive"],
        ]);

        $service->update($data);

        return response()->json([
            "success" => true,
            "message" => "Service updated successfully",
            "data" => $service,
        ]);
    }

    public function destroy(Service $service): JsonResponse
    {
        // Cegah hapus jika sudah ada pelanggan yang pakai
        if ($service->subscriptions()->exists()) {
            return response()->json([
                "success" => false,
                "message" => "Layanan tidak bisa dihapus karena masih memiliki data langganan.",
            ], 422);
        }

        $service->delete();

        return response()->json([
            "success" => true,
            "message" => "Service deleted successfully",
            "data" => null,
        ]);
    }

    /**
     * Custom Actions untuk Aktivasi
     */
    public function activate(Service $service): JsonResponse
    {
        $service->update(["status" => "active"]);

        return response()->json([
            "success" => true,
            "message" => "Service activated successfully",
            "data" => $service,
        ]);
    }

    public function deactivate(Service $service): JsonResponse
    {
        $service->update(["status" => "inactive"]);

        return response()->json([
            "success" => true,
            "message" => "Service deactivated successfully",
            "data" => $service,
        ]);
    }
}
