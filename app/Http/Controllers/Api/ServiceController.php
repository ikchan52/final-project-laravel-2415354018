<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $status = $request->query("status");
        $query = Service::query();

        if ($status !== null) {
            // Validasi input status secara manual
            if (!in_array($status, ["active", "inactive"], true)) {
                return response()->json([
                    "success" => false,
                    "message" => "Validation failed",
                    "errors" => ["status" => ["The selected status is invalid."]],
                ], 422);
            }
            $query->where("status", $status === "active");
        }

        return response()->json([
            "success" => true,
            "message" => "Services retrieved successfully",
            "data" => $query->latest()->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            "name" => ["required", "string"],
            "price" => ["required", "integer", "min:0"],
            "description" => ["nullable", "string"],
            "status" => ["nullable", "boolean"],
        ]);

        $service = Service::create($data);

        return response()->json([
            "success" => true,
            "message" => "Service created successfully",
            "data" => $service,
        ], 201);
    }

    // Menggunakan Route Model Binding (Service $service)
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
            "name" => ["sometimes", "string"],
            "price" => ["sometimes", "integer", "min:0"],
            "description" => ["nullable", "string"],
            "status" => ["nullable", "boolean"],
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
        // Cek apakah ada langganan yang masih aktif
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

    public function activate(Service $service): JsonResponse
    {
        $service->update(["status" => true]);

        return response()->json([
            "success" => true,
            "message" => "Service activated successfully",
            "data" => $service,
        ]);
    }

    public function deactivate(Service $service): JsonResponse
    {
        $service->update(["status" => false]);

        return response()->json([
            "success" => true,
            "message" => "Service deactivated successfully",
            "data" => $service,
        ]);
    }
}
