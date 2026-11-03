<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WilayahService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function __construct(
        protected WilayahService $wilayahService
    ) {}

    public function provinces(): JsonResponse
    {
        return response()->json($this->wilayahService->getProvinces());
    }

    public function regencies(Request $request): JsonResponse
    {
        $provinceId = (string) $request->query('province_id', $request->query('province_code', ''));
        if (! $provinceId) {
            return response()->json([]);
        }

        return response()->json($this->wilayahService->getRegencies($provinceId));
    }

    public function districts(Request $request): JsonResponse
    {
        $regencyId = (string) $request->query('regency_id', $request->query('regency_code', ''));
        if (! $regencyId) {
            return response()->json([]);
        }

        return response()->json($this->wilayahService->getDistricts($regencyId));
    }

    public function villages(Request $request): JsonResponse
    {
        $districtId = (string) $request->query('district_id', $request->query('district_code', ''));
        if (! $districtId) {
            return response()->json([]);
        }

        return response()->json($this->wilayahService->getVillages($districtId));
    }

    public function findRegency(Request $request): JsonResponse
    {
        $name = (string) $request->query('name', $request->query('city', ''));
        if (! $name) {
            return response()->json(null);
        }

        return response()->json($this->wilayahService->findRegencyByName($name));
    }

    public function all(): JsonResponse
    {
        return response()->json($this->wilayahService->getAllWilayahMap());
    }
}
