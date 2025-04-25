<?php

namespace App\Services;

use App\Models\KhoTrungChuyen;
use App\Models\NhaSanXuat;
use App\Models\DaiLy;

class PathFindingService
{
    // Haversine: Tính khoảng cách giữa 2 tọa độ (km)
    private function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $R = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $R * $c;
    }

    public function buildGraph($nodes)
    {
        $graph = [];
        foreach ($nodes as $id1 => $point1) {
            $graph[$id1] = [];
            foreach ($nodes as $id2 => $point2) {
                if ($id1 !== $id2) {
                    $graph[$id1][$id2] = $this->haversine(
                        $point1['lat'],
                        $point1['lng'],
                        $point2['lat'],
                        $point2['lng']
                    );
                }
            }
        }
        return $graph;
    }

    private function distancePointToSegment($px, $py, $x1, $y1, $x2, $y2)
    {
        // Chuyển thành mét
        $earthRadius = 6371; // km
        $lat1 = deg2rad($x1);
        $lng1 = deg2rad($y1);
        $lat2 = deg2rad($x2);
        $lng2 = deg2rad($y2);
        $lat = deg2rad($px);
        $lng = deg2rad($py);

        // Vector AB và AP
        $dx = $lat2 - $lat1;
        $dy = $lng2 - $lng1;

        $dot = ($lat - $lat1) * $dx + ($lng - $lng1) * $dy;
        $len_sq = $dx * $dx + $dy * $dy;
        $param = ($len_sq != 0) ? $dot / $len_sq : -1;

        if ($param < 0) {
            $closestLat = $lat1;
            $closestLng = $lng1;
        } elseif ($param > 1) {
            $closestLat = $lat2;
            $closestLng = $lng2;
        } else {
            $closestLat = $lat1 + $param * $dx;
            $closestLng = $lng1 + $param * $dy;
        }

        return $this->haversine(rad2deg($closestLat), rad2deg($closestLng), $px, $py);
    }

    public function findShortestPath(array $nhaSanXuatIds, $daiLyId)
    {
        $nhaSanXuatList = NhaSanXuat::whereIn('id', $nhaSanXuatIds)->get();
        $dl = DaiLy::findOrFail($daiLyId);
        $allKhos = KhoTrungChuyen::all();

        // Lấy danh sách tọa độ
        $startPoints = $nhaSanXuatList->map(fn($nsx) => ['lat' => $nsx->vi_do, 'lng' => $nsx->kinh_do]);
        $endPoint = ['lat' => $dl->vi_do, 'lng' => $dl->kinh_do];

        $isNorthBound = $startPoints[0]['lat'] < $endPoint['lat'];
        $cityRadius = 100;

        $validKhos = collect();
        $cityKhos = collect();

        foreach ($allKhos as $kho) {
            $lat = $kho->vi_do;
            $lng = $kho->kinh_do;

            $distanceToDl = $this->haversine($lat, $lng, $endPoint['lat'], $endPoint['lng']);
            if ($distanceToDl <= $cityRadius) {
                $cityKhos->push($kho);
                continue;
            }

            // Check nếu nằm gần tuyến đường
            $distanceToLine = $this->distancePointToSegment(
                $lat, $lng,
                $startPoints[0]['lat'], $startPoints[0]['lng'],
                $endPoint['lat'], $endPoint['lng']
            );

            if ($distanceToLine > 150) continue;

            if ($isNorthBound && ($lat < $startPoints[0]['lat'] || $lat > $endPoint['lat'])) continue;
            if (!$isNorthBound && ($lat > $startPoints[0]['lat'] || $lat < $endPoint['lat'])) continue;

            $validKhos->push($kho);
        }

        if ($validKhos->isEmpty() && $cityKhos->isEmpty()) {
            return [
                'path_ids' => [],
                'path_names' => [],
                'distance' => 0,
                'debug_kho_count' => 0,
                'error' => 'Không có kho phù hợp cho tuyến đường.'
            ];
        }

        // Danh sách kho dùng để tính toán
        $khoPoints = [];
        foreach ($validKhos as $kho) {
            $khoPoints['kho_' . $kho->id] = [
                'lat' => $kho->vi_do,
                'lng' => $kho->kinh_do,
                'ten' => $kho->ten_kho
            ];
        }

        // Ưu tiên thêm kho gần đại lý
        $cityKho = $cityKhos->sortBy(fn($kho) => $this->haversine($kho->vi_do, $kho->kinh_do, $endPoint['lat'], $endPoint['lng']))->first();

        // Quyết định số lượng kho trung chuyển cần thêm
        $minDistanceNSXToDL = min(
            $startPoints->map(fn($pt) => $this->haversine($pt['lat'], $pt['lng'], $endPoint['lat'], $endPoint['lng']))->toArray()
        );

        if ($minDistanceNSXToDL <= 100) {
            $minKho = 0;
            $maxKho = 1;
        } elseif ($minDistanceNSXToDL <= 300) {
            $minKho = 1;
            $maxKho = 2;
        } elseif ($minDistanceNSXToDL <= 600) {
            $minKho = 2;
            $maxKho = 4;
        } else {
            $minKho = 4;
            $maxKho = min(6, count($khoPoints));
        }

        // Dữ liệu lưu trữ kết quả cho từng tuyến đường
        $allResults = [];

        // Tính toán đường đi cho từng NSX
        foreach ($nhaSanXuatList as $nsx) {
            $minPath = null;
            $minDistance = INF;

            // Duyệt các tổ hợp kho trung chuyển
            for ($n = $minKho; $n <= $maxKho; $n++) {
                $combinations = $this->getCombinations(array_keys($khoPoints), $n);

                foreach ($combinations as $combo) {
                    if ($cityKho) {
                        $cityKhoId = 'kho_' . $cityKho->id;
                        $combo = array_filter($combo, fn($id) => $id !== $cityKhoId);
                        $combo = array_values($combo);
                        $combo[] = $cityKhoId;
                        $khoPoints[$cityKhoId] = [
                            'lat' => $cityKho->vi_do,
                            'lng' => $cityKho->kinh_do,
                            'ten' => $cityKho->ten_kho
                        ];
                    }

                    // Sắp xếp NSX bằng Greedy gần nhất
                    $remainingNSX = [$nsx];  // Chỉ lấy một NSX tại một thời điểm
                    $orderedNSX = [];

                    $currentLat = $combo ? $khoPoints[$combo[0]]['lat'] : $endPoint['lat'];
                    $currentLng = $combo ? $khoPoints[$combo[0]]['lng'] : $endPoint['lng'];

                    while (!empty($remainingNSX)) {
                        usort($remainingNSX, fn($a, $b) => $this->haversine($currentLat, $currentLng, $a['vi_do'], $a['kinh_do']) <=> $this->haversine($currentLat, $currentLng, $b['vi_do'], $b['kinh_do']));
                        $next = array_shift($remainingNSX);
                        $orderedNSX[] = $next;
                        $currentLat = $next['vi_do'];
                        $currentLng = $next['kinh_do'];
                    }

                    // Tạo path
                    $path = [];
                    $nodes = [];

                    foreach ($orderedNSX as $nsx) {
                        $key = 'nsx_' . $nsx['id'];
                        $path[] = $key;
                        $nodes[$key] = ['lat' => $nsx['vi_do'], 'lng' => $nsx['kinh_do']];
                    }

                    foreach ($combo as $k) {
                        $path[] = $k;
                        $nodes[$k] = $khoPoints[$k];
                    }

                    $path[] = 'dl_' . $dl->id;
                    $nodes['dl_' . $dl->id] = $endPoint;

                    // Tính tổng quãng đường
                    $totalDistance = 0;
                    for ($i = 0; $i < count($path) - 1; $i++) {
                        $totalDistance += $this->haversine(
                            $nodes[$path[$i]]['lat'], $nodes[$path[$i]]['lng'],
                            $nodes[$path[$i + 1]]['lat'], $nodes[$path[$i + 1]]['lng']
                        );
                    }

                    if ($totalDistance < $minDistance) {
                        $minDistance = $totalDistance;
                        $minPath = $path;
                    }
                }

                if ($minPath) break;
            }

            $pathNames = [];
            foreach ($minPath ?? [] as $pointId) {
                if (str_starts_with($pointId, 'nsx_')) {
                    $id = (int) str_replace('nsx_', '', $pointId);
                    $nsx = $nhaSanXuatList->firstWhere('id', $id);
                    $pathNames[] = 'Nhà sản xuất: ' . $nsx->dia_chi;
                } elseif (str_starts_with($pointId, 'dl_')) {
                    $pathNames[] = 'Đại lý: ' . $dl->dia_chi;
                } elseif (str_starts_with($pointId, 'kho_')) {
                    $khoId = (int) str_replace('kho_', '', $pointId);
                    $kho = $allKhos->firstWhere('id', $khoId);
                    $pathNames[] = $kho ? $kho->ten_kho : 'Kho không xác định';
                }
            }

            // Lưu kết quả của mỗi nhà sản xuất
            $allResults[] = [
                'path_ids' => $minPath,
                'path_names' => $pathNames,
                'distance' => round($minDistance, 2),
            ];
        }

        return $allResults;
    }

    private function getCombinations($array, $size)
    {
        if (empty($array) || $size === 0 || $size > count($array)) {
            return [[]]; // fallback an toàn
        }

        $combinations = [];
        $this->combinationHelper($array, $size, 0, [], $combinations);
        return $combinations;
    }

    private function combinationHelper($array, $size, $start, $current, &$combinations)
    {
        if (count($current) == $size) {
            $combinations[] = $current;
            return;
        }

        for ($i = $start; $i < count($array); $i++) {
            $this->combinationHelper($array, $size, $i + 1, array_merge($current, [$array[$i]]), $combinations);
        }
    }

    public function findShortestPathMultipleNSX(array $nhaSanXuatIds, $daiLyId)
    {
        $results = [];
        foreach ($nhaSanXuatIds as $nsxId) {
            $result = $this->findShortestPath([$nsxId], $daiLyId);
            // Lấy tuyến đường đầu tiên (nếu có)
            if (!empty($result) && isset($result[0]['path_ids'])) {
                $results[] = [
                    'nha_san_xuat_id' => $nsxId,
                    'path_ids' => $result[0]['path_ids'],
                    'distance' => $result[0]['distance'],
                    'path_names' => $result[0]['path_names'] ?? [],
                ];
            }
        }
        return $results;
    }
}
