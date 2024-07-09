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
use runwildstudio\easyapi\base\Element;
use runwildstudio\easyapi\base\ElementInterface;
use runwildstudio\easyapi\helpers\DuplicateHelper;
use runwildstudio\easyapi\EasyApi;
use DateTime;

/**
 * Class ApiModel
 *
 * @property-read mixed $dataType
 * @property-read bool $nextPagination
 * @property-read ElementInterface|Element|null $element
 */
class ApiModel extends Model
{
    // Properties
    // =========================================================================

    /**
     * @var int|null
     */
    public ?int $id = null;

    /**
     * @var string
     */
    public string $name = '';

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
    public ?string $authorizationType = null;

    /**
     * @var string|null
     */
    public ?string $authorizationUrl = null;

    /**
     * @var string|null
     */
    public ?string $authorizationAppId = null;

    /**
     * @var string|null
     */
    public ?string $authorizationAppSecret = null;

    /**
     * @var string|null
     */
    public ?string $authorizationGrantType = null;

    /**
     * @var string|null
     */
    public ?string $authorizationUsername = null;

    /**
     * @var string|null
     */
    public ?string $authorizationPassword = null;

    /**
     * @var string|null
     */
    public ?string $authorizationRedirect = null;

    /**
     * @var string|null
     */
    public ?string $authorizationCode = null;

    /**
     * @var string|null
     */
    public ?string $authorizationRefreshToken = null;

    /**
     * @var string|null
     */
    public ?string $authorizationCustomParameters = null;

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
     * @var string|null
     */
    public ?string $offsetField = null;

    /**
     * @var string|null
     */
    public ?string $offsetUpateURL = null;

    /**
     * @var string|null
     */
    public ?string $offsetTermination = null;

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
    
    /**
     * @var int|null
     */
    public ?int $feedId = null;

    /**
     * @var int|null
     */
    public ?int $siteId = null;

    /**
     * @var int|null
     */
    public ?int $sortOrder = null;

    /**
     * @var DateTime|null
     */
    public ?DateTime $dateCreated = null;

    /**
     * @var DateTime|null
     */
    public ?DateTime $dateUpdated = null;

    /**
     * @var string|null
     */
    public ?string $uid = null;

    // Feed fields required to create a new feed
    // =========================================================================

    /**
     * @var string|null
     */
    public ?string $elementType = null;

    /**
     * @var array|null
     */
    public ?array $elementGroup = null;

    /**
     * @var array|null
     */
    public ?array $duplicateHandle = null;

    /**
     * @var string|null
     */
    public ?string $passkey = null;

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
     * @return mixed|null
     */
    public function getAuthType(): mixed
    {
        return EasyApi::$plugin->auth->getRegisteredApiAuthType($this->authorizationType);
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
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            [['name', 'apiUrl', 'contentType', 'authorizationType'], 'required'],
        ];
    }
}
