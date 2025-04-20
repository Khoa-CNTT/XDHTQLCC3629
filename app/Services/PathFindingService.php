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

    public function findShortestPath($nhaSanXuatId, $daiLyId)
    {
        $nsx = NhaSanXuat::findOrFail($nhaSanXuatId);
        $dl = DaiLy::findOrFail($daiLyId);
        $allKhos = KhoTrungChuyen::all();

        $startPoint = ['lat' => $nsx->vi_do, 'lng' => $nsx->kinh_do];
        $endPoint = ['lat' => $dl->vi_do, 'lng' => $dl->kinh_do];

        $isNorthBound = $startPoint['lat'] < $endPoint['lat'];
        $cityRadius = 100;

        $validKhos = collect();
        $cityKhos = collect();

        foreach ($allKhos as $kho) {
            $lat = $kho->vi_do;
            $lng = $kho->kinh_do;

            // Luôn check cityKhos trước (ưu tiên)
            $distanceToDl = $this->haversine($lat, $lng, $dl->vi_do, $dl->kinh_do);
            if ($distanceToDl <= $cityRadius) {
                $cityKhos->push($kho);
                continue; // Đã là kho trong thành phố, khỏi check tiếp
            }

            // Các điều kiện lọc theo tuyến
            $distanceToLine = $this->distancePointToSegment(
                $lat, $lng,
                $startPoint['lat'], $startPoint['lng'],
                $endPoint['lat'], $endPoint['lng']
            );

            if ($distanceToLine > 150) continue;

            if ($isNorthBound && ($lat < $startPoint['lat'] || $lat > $endPoint['lat'])) continue;
            if (!$isNorthBound && ($lat > $startPoint['lat'] || $lat < $endPoint['lat'])) continue;

            $validKhos->push($kho);
        }

        if ($validKhos->isEmpty() && $cityKhos->isEmpty()) {
            $directDistance = $this->haversine(
                $startPoint['lat'], $startPoint['lng'],
                $endPoint['lat'], $endPoint['lng']
            );

            if ($directDistance <= 100) {
                return [
                    'path_ids' => ['nsx_' . $nsx->id, 'dl_' . $dl->id],
                    'path_names' => [
                        'Nhà sản xuất: ' . $nsx->ten_cong_ty,
                        'Đại lý: ' . $dl->ten_cong_ty,
                    ],
                    'distance' => round($directDistance, 2),
                    'debug_kho_count' => 0,
                ];
            } else {
                return [
                    'path_ids' => [],
                    'path_names' => [],
                    'distance' => 0,
                    'debug_kho_count' => 0,
                    'error' => 'Không có kho phù hợp cho tuyến đường dài (' . round($directDistance, 2) . ' km)'
                ];
            }
        }

        $khoPoints = [];
        foreach ($validKhos as $kho) {
            $khoPoints['kho_' . $kho->id] = [
                'lat' => $kho->vi_do,
                'lng' => $kho->kinh_do,
                'ten' => $kho->ten_kho
            ];
        }

        $cityKho = $cityKhos->sortBy(function ($kho) use ($dl) {
            return $this->haversine($kho->vi_do, $kho->kinh_do, $dl->vi_do, $dl->kinh_do);
        })->first();

        $minPath = null;
        $minDistance = INF;

        $directDistance = $this->haversine(
            $startPoint['lat'], $startPoint['lng'],
            $endPoint['lat'], $endPoint['lng']
        );

        if ($directDistance <= 100) {
            $minKho = 0;
            $maxKho = 1;
        } elseif ($directDistance <= 300) {
            $minKho = 1;
            $maxKho = 2;
        } elseif ($directDistance <= 600) {
            $minKho = 2;
            $maxKho = 4;
        } else {
            $minKho = 4;
            $maxKho = min(6, count($khoPoints));
        }

        for ($n = $minKho; $n <= $maxKho; $n++) {
            $combinations = $this->getCombinations(array_keys($khoPoints), $n);
            foreach ($combinations as $combo) {
                // Sắp xếp theo khoảng cách từ NSX
                usort($combo, function ($a, $b) use ($startPoint, $khoPoints) {
                    $distA = $this->haversine($startPoint['lat'], $startPoint['lng'], $khoPoints[$a]['lat'], $khoPoints[$a]['lng']);
                    $distB = $this->haversine($startPoint['lat'], $startPoint['lng'], $khoPoints[$b]['lat'], $khoPoints[$b]['lng']);
                    return $distA <=> $distB;
                });

                // Thêm kho trong thành phố làm điểm cuối nếu có
                // Ép cityKho luôn là chặng cuối trước khi tới đại lý
                if ($cityKho) {
                    $cityKhoId = 'kho_' . $cityKho->id;

                    // Xóa cityKho khỏi combo nếu có
                    $combo = array_filter($combo, fn($id) => $id !== $cityKhoId);

                    // Thêm lại vào cuối
                    $combo = array_values($combo); // đảm bảo re-index
                    $combo[] = $cityKhoId;

                    // Đảm bảo khoPoints có cityKho
                    $khoPoints[$cityKhoId] = [
                        'lat' => $cityKho->vi_do,
                        'lng' => $cityKho->kinh_do,
                        'ten' => $cityKho->ten_kho
                    ];
                }

                $path = ['nsx_' . $nsx->id, ...$combo, 'dl_' . $dl->id];
                $nodes = ['nsx_' . $nsx->id => $startPoint];
                foreach ($combo as $k) $nodes[$k] = $khoPoints[$k];
                $nodes['dl_' . $dl->id] = $endPoint;

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
                $pathNames[] = 'Nhà sản xuất: ' . $nsx->dia_chi;
            } elseif (str_starts_with($pointId, 'dl_')) {
                $pathNames[] = 'Đại lý: ' . $dl->dia_chi;
            } elseif (str_starts_with($pointId, 'kho_')) {
                $khoId = (int) str_replace('kho_', '', $pointId);
                $kho = $allKhos->firstWhere('id', $khoId);
                $pathNames[] = ($kho ? $kho->ten_kho : 'Không rõ');
            }
        }

        return [
            'path_ids' => $minPath ?? [],
            'path_names' => $pathNames,
            'distance' => round($minDistance, 2),
            'debug_kho_count' => $validKhos->count() + $cityKhos->count(),
        ];
    }

    private function getCombinations($array, $size)
    {
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
}
