<?php

namespace runwildstudio\easyapi\helpers;

class DuplicateHelper
{
    public const Add = 'add';
    public const Update = 'update';
    public const Disable = 'disable';
    public const DisableForSite = 'disableForSite';
    public const Delete = 'delete';

    // Public Methods
    // =========================================================================

    /**
     * @param $handles
     * @return string
     */
    public static function getFriendly($handles): string
    {
        $array = [];

        foreach ($handles as $handle) {
            $array[] = ucfirst($handle);
        }

        return implode(' & ', $array);
    }

    /**
     * @param $handles
     * @param $handle
     * @param false $only
     * @return bool
     */
    public static function contains($handles, $handle, bool $only = false): bool
    {
        if (in_array($handle, $handles, true)) {
            if ($only) {
                if (count($handles) == 1) {
                    return true;
                }
            } else {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $apiData
     * @param false $only
     * @return bool
     */
    public static function isAdd($apiData, bool $only = false): bool
    {
        return self::contains($apiData['duplicateHandle'], self::Add, $only);
    }

    /**
     * @param $apiData
     * @param false $only
     * @return bool
     */
    public static function isUpdate($apiData, bool $only = false): bool
    {
        return self::contains($apiData['duplicateHandle'], self::Update, $only);
    }

    /**
     * @param $apiData
     * @param false $only
     * @return bool
     */
    public static function isDisable($apiData, bool $only = false): bool
    {
        return self::contains($apiData['duplicateHandle'], self::Disable, $only);
    }

    /**
     * @param $apiData
     * @param false $only
     * @return bool
     */
    public static function isDisableForSite($apiData, bool $only = false): bool
    {
        return self::contains($apiData['duplicateHandle'], self::DisableForSite, $only);
    }

    /**
     * @param $apiData
     * @param false $only
     * @return bool
     */
    public static function isDelete($apiData, bool $only = false): bool
    {
        return self::contains($apiData['duplicateHandle'], self::Delete, $only);
    }
}
