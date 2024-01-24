<?php

namespace runwildstudio\easyapi\migrations;

use craft\db\Migration;

class Install extends Migration
{
    // Public Methods
    // =========================================================================

    public function safeUp(): bool
    {
        $this->createTables();

        return true;
    }

    public function safeDown(): bool
    {
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    protected function createTables(): void
    {
        $this->createTable('{{%easyapi_apis}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'apiUrl' => $this->text()->notNull(),
            'contentType' => $this->string(),
            'authorization' => $this->string(),
            'httpAction' => $this->string(),
            'updateElementIdField' => $this->text(),
            'direction' => $this->string(),
            'primaryElement' => $this->string(),
            'elementType' => $this->string()->notNull(),
            'elementGroup' => $this->text(),
            'requestHeader' => $this->string(),
            'requestBody' => $this->string(),
            'siteId' => $this->string(),
            'sortOrder' => $this->smallInteger()->unsigned(),
            'fieldMapping' => $this->text(),
            'fieldUnique' => $this->text(),
            'parentElement' => $this->string(),
            'parentElementType' => $this->string(),
            'parentElementGroup' => $this->text(),
            'parentElementIdField' => $this->text(),
            'parentFilter' => $this->text(),
            'queueRequest' => $this->boolean()->notNull()->defaultValue(false),
            'queueOrder' => $this->text(),
            'useLive' => $this->boolean()->notNull()->defaultValue(false),

            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),

            'duplicateHandle' => $this->text(),
            'updateSearchIndexes' => $this->boolean()->notNull()->defaultValue(true),
            'paginationNode' => $this->text(),
            'paginationUrl' => $this->text(),
            'passkey' => $this->text(),
        ]);
    }

    protected function removeTables(): void
    {
        $this->dropTableIfExists('{{%easyapi_apis}}');
    }
}
