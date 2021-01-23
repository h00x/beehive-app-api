<?php

namespace App\Http\Resources;

use App\Models\Hive;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\ResourceCollection;

class HiveCollection extends ResourceCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'hives';

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }

    public function withResponse($request, $response)
    {
        $jsonResponse = json_decode($response->getContent(), true);

        unset($jsonResponse['links'], $jsonResponse['meta']['links'], $jsonResponse['meta']['path']);

        $response->setContent(json_encode($jsonResponse));
    }
}
