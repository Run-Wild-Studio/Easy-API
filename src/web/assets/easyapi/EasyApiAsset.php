<?php

namespace runwildstudio\easyapi\web\assets\easyapi;

use runwildstudio\easyapi\models\ElementGroup;
use runwildstudio\easyapi\EasyApi;
use craft\helpers\Json;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;
use craft\web\View;

class EasyApiAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function init(): void
    {
        $this->sourcePath = "@runwildstudio/easyapi/web/assets/easyapi";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'src/js/easyapi.js',
            'src/lib/selectize.js',
        ];

        $this->css = [
            'src/scss/easyapi.css',
        ];

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function registerAssetFiles($view): void
    {
        parent::registerAssetFiles($view);

        $elementTypeInfo = [];
        foreach (EasyApi::getInstance()->getElements()->getRegisteredElements() as $elementClass => $element) {
            $groups = [];
            $elementGroups = $element->getGroups();
            foreach ($elementGroups as $group) {
                if ($group instanceof ElementGroup) {
                    $groups[$group->id] = [
                    ];
                }
            }
            $elementTypeInfo[$elementClass] = [
                'groups' => $groups,
            ];
        }

        $json = Json::encode($elementTypeInfo, JSON_UNESCAPED_UNICODE);
        $js = <<<JS
if (typeof Craft.EasyApi === typeof undefined) {
    Craft.EasyApi = {};
}
Craft.EasyApi.elementTypes = {$json};
JS;
        $view->registerJs($js, View::POS_HEAD);
    }
}
