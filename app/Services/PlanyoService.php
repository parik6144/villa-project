<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\PropertySites;

class PlanyoService
{
    protected ?string $apiUrl;
    protected ?string $apiKey;
    protected ?string $siteId;

    public function __construct()
    {
        $propertySite = PropertySites::where('site', 'Planyo')->first();

        if ($propertySite) {
            $this->apiUrl = $propertySite->api_url;
            $this->apiKey = $propertySite->api_key;
            $this->siteId = $propertySite->account_id;
        } else {
            $this->apiUrl = null;
            $this->apiKey = null;
            $this->siteId = null;
        }
    }

    public function addResource(array $formData)
    {
        $defaultParams = [
            'method' => 'add_resource',
        ];

        $args = array_merge($defaultParams, $formData);

        return $this->doRequest($args);
    }

    public function modifyResource(array $formData)
    {
        $defaultParams = [
            'method' => 'modify_resource'
        ];

        $args = array_merge($defaultParams, $formData);

        return $this->doRequest($args);
    }


    public function getListResources(array $params = [])
    {
        $defaultParams = [
            'method' => 'list_resources',
            'detail_level' => 15,
            'list_published_only' => true,
            'sort' => 'resname',
            'page' => 0,
        ];

        $args = array_merge($defaultParams, $params);

        return $this->doRequest($args);
    }

    public function getIcal(array $params = [])
    {
        $defaultParams = [
            'method' => 'get_ical_feed_url'
        ];

        $args = array_merge($defaultParams, $params);

        return $this->doRequest($args);
    }

    public function getSeasons($resource_id = 0)
    {
        $args = [
            'resource_id' => $resource_id,
            'method' => 'get_resource_seasons'
        ];

        return $this->doRequest($args);
    }

    public function getResourceInfo($planyoResourceId, array $params = [])
    {
        $defaultParams = [
            'method' => 'get_resource_info',
            'resource_id' => $planyoResourceId
        ];

        $args = array_merge($defaultParams, $params);

        return $this->doRequest($args);
    }

    public function addResourceImage($resource_id, $image_url)
    {
        $args = [
            'resource_id' => $resource_id,
            'image_url' => $image_url,
            'method' => 'add_resource_image'
        ];

        return $this->doRequest($args);
    }

    public function deleteResourceImage($image_id)
    {
        $args = [
            'id' => $image_id,
            'method' => 'remove_resource_image'
        ];

        return $this->doRequest($args);
    }

    public function getReservationData(array $params = [])
    {
        // Обязательные параметры: start_time и end_time
        if (!isset($params['reservation_id'])) {
            throw new \InvalidArgumentException('Параметр reservation_id обязателен');
        }

        $defaultParams = [
            'method'      => 'get_reservation_data',
        ];

        $args = array_merge($defaultParams, $params);

        return $this->doRequest($args);
    }

    public function getListReservations(array $params = [])
    {
        // Обязательные параметры: start_time и end_time
        if (!isset($params['start_time']) || !isset($params['end_time'])) {
            throw new \InvalidArgumentException('Параметры start_time и end_time обязательны и должны быть в формате "YYYY-MM-DD HH:MM"');
        }

        $defaultParams = [
            'method'                => 'list_reservations',
            'list_by_creation_date' => true,
            'detail_level'          => 1, // уровень детализации
        ];

        $args = array_merge($defaultParams, $params);

        return $this->doRequest($args);
    }

    public function getListReservationPayments(array $params = [])
    {
        // Обязательные параметры: start_time и end_time
        if (!isset($params['reservation_id'])) {
            throw new \InvalidArgumentException('Параметр reservation_id обязателен');
        }

        $defaultParams = [
            'method'      => 'list_reservation_payments',
        ];

        $args = array_merge($defaultParams, $params);

        return $this->doRequest($args);
    }


    public function getListUsers(array $params = [])
    {
        $defaultParams = [
            'page'       => 0,
            'page_size'  => 1000,
            'method'         => 'list_users',
        ];

        $args = array_merge($defaultParams, $params);

        return $this->doRequest($args);
    }

    public function getSiteInfo(array $params = [])
    {

        $defaultParams = [
            'method'      => 'get_site_info',
        ];

        $args = array_merge($defaultParams, $params);

        return $this->doRequest($args);
    }


    private function doRequest(array $customerParams)
    {
        $result = null;
        $params = [
            'api_key' => $this->apiKey,
            'site_id' => $this->siteId,
        ];
        $args = array_merge($customerParams, array_diff_key($params, $customerParams));


        $url = $this->apiUrl;

        // dd($url, $args);

        $response = Http::timeout(60)
            ->retry(3, 1000) // 3 попытки с интервалом 1 секунда
            ->get($url, $args);
        $result = $response->json();

        return $result;
    }
}
