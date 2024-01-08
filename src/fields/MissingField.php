<?php
/**
 * @link https://runwildstudio.co.nz/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://runwildstudio.github.io/license/
 */

namespace runwildstudio\easyapi\fields;

use craft\base\MissingComponentInterface;
use craft\base\MissingComponentTrait;
use runwildstudio\easyapi\base\Field;

/**
 * MissingField represents a field with an invalid class.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 4.4.3
 */
class MissingField extends Field implements MissingComponentInterface
{
    use MissingComponentTrait;
}
