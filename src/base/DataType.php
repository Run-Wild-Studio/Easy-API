<?php

namespace runwildstudio\easyapi\base;

use Cake\Utility\Hash;
use Craft;
use craft\base\Component;
use craft\helpers\UrlHelper;

/**
 *
 * @property-read mixed $name
 * @property-read mixed $class
 */
abstract class DataType extends Component
{
    // Public
    // =========================================================================

    /**
     * @return mixed
     */
    public function getName(): string
    {
        /** @phpstan-ignore-next-line */
        return static::$name;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return get_class($this);
    }

    /**
     * @param $array
     * @param $api
     */
    public function setupPaginationUrl($array, $api): void
    {
        if (!$api->paginationNode) {
            return;
        }

        // Find the URL value in the api
        $flatten = Hash::flatten($array, '/');
        $url = Hash::get($flatten, $api->paginationNode);

        // resolve any aliases in the pagination URL
        $url = Craft::getAlias($url);

        // if the api provides a root relative URL, make it whole again based on the api.
        if ($url && UrlHelper::isRootRelativeUrl($url)) {
            $url = UrlHelper::hostInfo($api->apiUrl) . $url;
        }

        // Replace the mapping value with the actual URL
        $api->paginationUrl = $url;
    }
}
