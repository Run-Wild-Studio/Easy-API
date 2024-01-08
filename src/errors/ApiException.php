<?php
/**
 * @link https://runwildstudio.co.nz/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://runwildstudio.github.io/license/
 */

namespace runwildstudio\easyapi\errors;

use yii\base\UserException;

/**
 * Class ApiException
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 4.4.3
 */
class ApiException extends UserException
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Api Error';
    }
}
