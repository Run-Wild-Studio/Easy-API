<?php

namespace runwildstudio\easyapi\models;

use ArrayAccess;
use Cake\Utility\Hash;
use Craft;
use craft\base\Model;
use craft\elements\Asset;
use craft\elements\Category;
use craft\elements\Entry;
use craft\elements\GlobalSet;
use craft\elements\Tag;
use craft\feedme\models\FeedModel;
use runwildstudio\easyapi\base\Element;
use runwildstudio\easyapi\base\ElementInterface;
use runwildstudio\easyapi\helpers\DuplicateHelper;
use runwildstudio\easyapi\EasyApi;
use DateTime;

/**
 * Class ApiModel
 *
 * @property-read mixed $duplicateHandleFriendly
 * @property-read mixed $dataType
 * @property-read bool $nextPagination
 * @property-read ElementInterface|Element|null $element
 */
class ApiModel extends FeedModel
{
    // Properties
    // =========================================================================

    /**
     * @var string|null
     */
    public ?string $apiUrl = null;

    /**
     * @var string|null
     */
    public ?string $contentType = null;

    /**
     * @var string|null
     */
    public ?string $authorization = null;

    /**
     * @var string|null
     */
    public ?string $httpAction = null;

    /**
     * @var
     */
    public mixed $updateElementIdField = null;

    /**
     * @var string|null
     */
    public ?string $direction = null;

    /**
     * @var string|null
     */
    public ?string $requestHeader = null;

    /**
     * @var string|null
     */
    public ?string $requestBody = null;

    /**
     * @var string|null
     */
    public ?string $parentElement = null;

    /**
     * @var string|null
     */
    public ?string $parentElementType = null;

    /**
     * @var array|null
     */
    public ?array $parentElementGroup = null;

    /**
     * @var string|null
     */
    public ?string $parentElementIdField = null;

    /**
     * TODO: Add functionality to filter parent entries when fetching related API entries.
     * @var string|null
     */
    public ?string $parentFilter = null;

    /**
     * @var bool
     * @since 4.3.0
     */
    public ?bool $queueRequest = false;
    
    /**
     * @var int|null
     */
    public ?int $queueOrder = null;

    /**
     * @var bool
     * @since 4.3.0
     */
    public ?bool $useLive = false;

    // Model-only properties

    // Public Methods
    // =========================================================================

    /**
     * @return string
     */
    public function __toString()
    {
        return Craft::t('easyapi', $this->name);
    }

    /**
     * @return string
     */
    public function getDuplicateHandleFriendly(): string
    {
        return DuplicateHelper::getFriendly($this->duplicateHandle);
    }

    /**
     * @return mixed|null
     */
    public function getDataType(): mixed
    {
        return EasyApi::$plugin->data->getRegisteredApiDataType($this->contentType);
    }

    /**
     * @return ElementInterface|Element|null
     */
    public function getElement(): ElementInterface|Element|null
    {
        $element = EasyApi::$plugin->elements->getRegisteredElement($this->elementType);

        if ($element) {
            /** @var Element $element */
            $element->api = $this;
        }

        return $element;
    }

    /**
     * @return ElementInterface|Element|null
     */
    public function getParentElement(): ElementInterface|Element|null
    {
        if ($this->parentElementType == null) {
            return null;
        }
        
        $element = EasyApi::$plugin->elements->getRegisteredElement($this->parentElementType);

        if ($element) {
            /** @var Element $element */
            $element->api = $this;
        }

        return $element;
    }

    /**
     * @param bool $usePrimaryElement
     * @return array|ArrayAccess|mixed|null
     */
    public function getApiData(bool $usePrimaryElement = true): mixed
    {
        $apiDataResponse = EasyApi::$plugin->data->getApiData($this, $usePrimaryElement);

        return Hash::get($apiDataResponse, 'data');
    }

    /**
     * @param false $usePrimaryElement
     * @return mixed
     */
    public function getApiNodes(bool $usePrimaryElement = false): mixed
    {
        if ($this->parentElementType != null && $this->parentElementType != "") {
            $this->updateURLFromParent();
        }

        $apiDataResponse = EasyApi::$plugin->data->getApiData($this, $usePrimaryElement);

        $apiData = Hash::get($apiDataResponse, 'data');

        $apiDataResponse['data'] = EasyApi::$plugin->data->getApiNodes($apiData);

        return $apiDataResponse;
    }

    /**
     * @param bool $usePrimaryElement
     * @return mixed
     */
    public function getApiMapping(bool $usePrimaryElement = true): mixed
    {
        if ($this->parentElementType != null && $this->parentElementType != "") {
            $this->updateURLFromParent();
        }

        $apiDataResponse = EasyApi::$plugin->data->getApiData($this, $usePrimaryElement);

        $apiData = Hash::get($apiDataResponse, 'data');

        $apiDataResponse['data'] = EasyApi::$plugin->data->getApiMapping($apiData);

        return $apiDataResponse;
    }

    private function updateURLFromParent()
    {

        switch ($this->parentElementType) {
            case 'craft\\elements\\Asset':
                $assetId = $this->parentElementGroup[$this->parentElementType];
    
                $parent = Asset::find()
                    ->assetId($assetId)
                    ->one();
                break;

            case 'craft\\elements\\Category':
                $groupId = $this->parentElementGroup[$this->parentElementType];
                
                $parent = Category::find()
                    ->groupId($groupId)
                    ->one();
                break;

            case 'craft\\elements\\Entry':
                $sectionId = $this->parentElementGroup[$this->parentElementType]["section"];
                $entryTypeId = $this->parentElementGroup[$this->parentElementType]["entryType"];
    
                $parent = Entry::find()
                    ->sectionId($sectionId)
                    ->typeId($entryTypeId)
                    ->one();
                break;
                
            case 'craft\\elements\\Tag':
                $tagId = $this->parentElementGroup[$this->parentElementType];
    
                $parent = Tag::find()
                    ->tagId($tagId)
                    ->one();
                break;
                
                case 'craft\\elements\\GlobalSet':
                    $globalSetId = $this->parentElementGroup[parentElementType]->globalSet;
        
                    $parent = Glogal::find()
                        ->globalSetId($globalSetId)
                        ->one();
                    break;

            default:
                # Should never get to this
                break;
        }

        $originalUrl = $this->apiUrl;
        $originalString = $originalUrl;
        $dynamicValue = $parent->getFieldValue($this->parentElementIdField);
        $modifiedString = str_replace('{{ Id }}', $dynamicValue, $originalString);
        $this->apiUrl = $modifiedString;
    }

    /**
     * @return bool
     */
    public function getNextPagination(): bool
    {
        if (!$this->paginationUrl || !filter_var($this->paginationUrl, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Set the URL dynamically on the api, then kick off processing again
        $this->apiUrl = $this->paginationUrl;

        return true;
    }

    /**
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            [['name', 'apiUrl', 'contentType', 'elementType', 'duplicateHandle', 'authorization'], 'required'],
        ];
    }
}
