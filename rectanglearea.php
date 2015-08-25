<?php

/*
 * Copyright 2013 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 *
 * PHP code by Todd Trann
 *
 * Adapted from SphericalUtil.java in Google maps source code
 * https://github.com/googlemaps/android-maps-utils/blob/master/library/src/com/google/maps/android/SphericalUtil.java
 *
 */

/*
 * Returns the area of a closed path on Earth in sq km
 * Path is an array of arrays
 * Earth radius is fixed at 6371 km
 */
function computeAreaKm2($path) {
    return abs(computeSignedArea($path,6371));
}

/*
 * Returns the area of a closed path on Earth in acres
 * Path is an array of arrays
 * Earth radius is fixed at 6371 km
 */
function computeAreaAcres($path) {
    return abs(computeSignedArea($path,6371)) * 247.105;
}

/*
 * Returns the signed area of a closed path on a sphere of given radius
 * The computed area uses the same units as the radius squared
 */
function computeSignedArea($path, $radius) {
    $size = count($path);
    if ($size < 3) { return 0; }
    $total = 0;
    $prev = $path[$size-1];
    $prevTanLat = tan((M_PI / 2 - deg2rad($prev['latitude'])) / 2);
    $prevLng = deg2rad($prev['longitude']);
    // For each edge, accumulate the signed area of the polar triangle
    foreach ($path as $point) {
        $tanLat = tan((M_PI / 2 - deg2rad($point['latitude'])) / 2);
        $lng = deg2rad($point['longitude']);
        $total += polarTriangleArea($tanLat, $lng, $prevTanLat, $prevLng);
        $prevTanLat = $tanLat;
        $prevLng = $lng;
    }
    return $total * ($radius * $radius);
}

/*
 * Returns the signed area of a triangle which has North Pole as a vertex
 */
function polarTriangleArea($tan1, $lng1, $tan2, $lng2) {
    $deltaLng = $lng1 - $lng2;
    $t = $tan1 * $tan2;
    return (2 * atan2($t * sin($deltaLng), 1 + $t * cos($deltaLng)));
}


/* ****************************************************
 * CODE BELOW HERE IS NOT NEEDED
 * It is included only to demonstrate use
 */

function makePoint($lat,$lng) {
    $p = array();
    $p['latitude'] = $lat;
    $p['longitude'] = $lng;
    return $p;
}

$p = array(
    makePoint(52.129246,-106.599083),
    makePoint(52.129265,-106.610786),
    makePoint(52.136501,-106.610802),
    makePoint(52.136486,-106.599091)
);

echo "Rectangle: " . print_r($p, true) . "\n";
echo "The area is: " . computeAreaAcres($p) . " acres\n";

?>