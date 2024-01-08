<?php

namespace runwildstudio\easyapi\records;

use craft\db\ActiveRecord;

class ApiRecord extends ActiveRecord
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritDoc
     */
    public static function tableName(): string
    {
        return '{{%easyapi_apis}}';
    }
}
