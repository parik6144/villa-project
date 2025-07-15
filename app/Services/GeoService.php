<?php
// app/Services/GeoService.php
namespace App\Services;

use Geocoder\Provider\GoogleMaps\GoogleMaps;
use Geocoder\StatefulGeocoder;
use GuzzleHttp\Client;

class GeoService
{
    protected static $geocoder;

    public static function init()
    {
        if (!self::$geocoder) {
            $httpClient = new Client(); 
            $googleMaps = new GoogleMaps($httpClient, null, env('GOOGLE_MAPS_API_KEY'));
            self::$geocoder = new StatefulGeocoder($googleMaps, 'en');
        }
    }

    public static function geocode(string $address)
    {
        if (self::$geocoder === null) {
            self::init();
        }

        $result = self::$geocoder->geocode($address);

        if ($result->count() > 0) {
            return $result->first()->getCoordinates();
        }

        return null;
    }

    public static function reverseGeocode(float $latitude, float $longitude)
    {
        if (self::$geocoder === null) {
            self::init();
        }

        $result = self::$geocoder->reverse($latitude, $longitude, 1);

        if ($result->count() > 0) {
            $location = $result->first();

            return [
                'country' => $location->getCountry() ? $location->getCountry()->getCode() : null,
                'city' => $location->getLocality(),
                'state_or_region' => $location->getSubLocality(),
                'street' => $location->getStreetName(),
                'address' => $location->getStreetNumber(),
                'postal_code' => $location->getPostalCode(),
            ];;
        }

        return null;
    }
}
