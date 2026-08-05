<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Company;
use Illuminate\Support\Collection;

/**
 * BranchRoutingService
 *
 * Automatically assigns an online order to the most appropriate branch
 * of a given company based on the customer's delivery location.
 *
 * ROUTING PRIORITY (in order):
 *   1. Coverage Area Match  — customer city/area is in branch's coverage_areas JSON array
 *   2. Haversine Distance   — nearest branch by GPS coordinates (if lat/lng available)
 *   3. Default Branch       — branch flagged with is_default = true
 *   4. Fallback             — first active branch of the company (last resort)
 *
 * USAGE:
 *   $result = app(BranchRoutingService::class)->route($company, [
 *       'city'     => 'Mirpur',
 *       'district' => 'Dhaka',
 *       'lat'      => 23.8223,
 *       'lng'      => 90.3654,
 *   ]);
 *
 *   $result = [
 *       'branch'      => Branch $branch,
 *       'method'      => 'coverage_match' | 'distance' | 'default' | 'fallback',
 *       'distance_km' => 3.42 | null,
 *   ]
 */
class BranchRoutingService
{
    /**
     * Route an online order to the best branch for the given company and customer location.
     *
     * @param  Company  $company   The tenant company
     * @param  array    $location  Keys: city, district, area, lat, lng (all optional)
     * @return array{branch: Branch, method: string, distance_km: float|null}
     */
    public function route(Company $company, array $location): array
    {
        // Load all active branches that accept online orders for this company
        $branches = Branch::where('company_id', $company->id)
            ->where('status', 'active')
            ->where('accepts_online_orders', true)
            ->get();

        if ($branches->isEmpty()) {
            // Absolute last resort: any active branch, even if it doesn't accept online orders
            $fallback = Branch::where('company_id', $company->id)
                ->where('status', 'active')
                ->first();

            return [
                'branch'      => $fallback,
                'method'      => 'fallback',
                'distance_km' => null,
            ];
        }

        // ── STEP 1: Coverage Area Match ────────────────────────────────────────
        $customerCity     = strtolower(trim($location['city'] ?? ''));
        $customerDistrict = strtolower(trim($location['district'] ?? ''));
        $customerArea     = strtolower(trim($location['area'] ?? ''));

        if ($customerCity || $customerDistrict || $customerArea) {
            $matched = $this->findByCoverageArea($branches, $customerCity, $customerDistrict, $customerArea);
            if ($matched) {
                return [
                    'branch'      => $matched,
                    'method'      => 'coverage_match',
                    'distance_km' => null,
                ];
            }
        }

        // ── STEP 2: Haversine Distance (GPS) ──────────────────────────────────
        $customerLat = isset($location['lat']) ? (float) $location['lat'] : null;
        $customerLng = isset($location['lng']) ? (float) $location['lng'] : null;

        if ($customerLat !== null && $customerLng !== null) {
            $nearest = $this->findNearestByCoordinates($branches, $customerLat, $customerLng);
            if ($nearest) {
                return [
                    'branch'      => $nearest['branch'],
                    'method'      => 'distance',
                    'distance_km' => $nearest['distance_km'],
                ];
            }
        }

        // ── STEP 3: Default Branch ─────────────────────────────────────────────
        $default = $branches->firstWhere('is_default', true);
        if ($default) {
            return [
                'branch'      => $default,
                'method'      => 'default',
                'distance_km' => null,
            ];
        }

        // ── STEP 4: Fallback — first available branch ──────────────────────────
        return [
            'branch'      => $branches->first(),
            'method'      => 'fallback',
            'distance_km' => null,
        ];
    }

    /**
     * Find a branch whose coverage_areas JSON array contains the customer's location.
     * Performs case-insensitive partial matching.
     *
     * @param  Collection  $branches
     * @param  string      $city
     * @param  string      $district
     * @param  string      $area
     * @return Branch|null
     */
    protected function findByCoverageArea(
        Collection $branches,
        string $city,
        string $district,
        string $area
    ): ?Branch {
        foreach ($branches as $branch) {
            $coverageAreas = $branch->coverage_areas ?? [];

            if (empty($coverageAreas)) {
                // If no coverage areas defined, check if branch city/district matches directly
                $branchCity     = strtolower(trim($branch->city ?? ''));
                $branchDistrict = strtolower(trim($branch->district ?? ''));

                if (
                    ($city && $branchCity && str_contains($branchCity, $city)) ||
                    ($city && $branchCity && str_contains($city, $branchCity)) ||
                    ($district && $branchDistrict && str_contains($branchDistrict, $district)) ||
                    ($district && $branchDistrict && str_contains($district, $branchDistrict))
                ) {
                    return $branch;
                }
                continue;
            }

            // Normalize coverage areas to lowercase
            $normalizedAreas = array_map('strtolower', array_map('trim', $coverageAreas));

            // Check each customer location token against coverage areas
            foreach ([$city, $district, $area] as $token) {
                if (empty($token)) {
                    continue;
                }
                foreach ($normalizedAreas as $coveredArea) {
                    if (str_contains($coveredArea, $token) || str_contains($token, $coveredArea)) {
                        return $branch;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Find the nearest branch using the Haversine formula.
     * Returns the branch and the calculated distance in kilometres.
     *
     * Haversine formula:
     *   a = sin²(Δlat/2) + cos(lat1) × cos(lat2) × sin²(Δlng/2)
     *   c = 2 × atan2(√a, √(1−a))
     *   d = R × c   (R = 6371 km)
     *
     * @param  Collection  $branches
     * @param  float       $customerLat
     * @param  float       $customerLng
     * @return array{branch: Branch, distance_km: float}|null
     */
    protected function findNearestByCoordinates(
        Collection $branches,
        float $customerLat,
        float $customerLng
    ): ?array {
        $nearest     = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($branches as $branch) {
            if ($branch->latitude === null || $branch->longitude === null) {
                continue;
            }

            $distance = $this->haversine(
                $customerLat,
                $customerLng,
                (float) $branch->latitude,
                (float) $branch->longitude
            );

            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearest     = $branch;
            }
        }

        if ($nearest === null) {
            return null;
        }

        return [
            'branch'      => $nearest,
            'distance_km' => round($minDistance, 2),
        ];
    }

    /**
     * Calculate the great-circle distance between two GPS points using the Haversine formula.
     *
     * @param  float  $lat1  Latitude of point 1 (degrees)
     * @param  float  $lng1  Longitude of point 1 (degrees)
     * @param  float  $lat2  Latitude of point 2 (degrees)
     * @param  float  $lng2  Longitude of point 2 (degrees)
     * @return float  Distance in kilometres
     */
    public function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadiusKm = 6371.0;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c;
    }
}
