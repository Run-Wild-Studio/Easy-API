<?php

namespace runwildstudio\easyapi\services;

use ArrayAccess;
use Cake\Utility\Hash;
use Craft;
use craft\base\Component;
use craft\feedme\events\FeedDataEvent;
use runwildstudio\easyapi\EasyApi;
use runwildstudio\easyapi\helpers\DataHelper;
use runwildstudio\easyapi\services\EasyApiDataTypes;
use craft\helpers\DateTimeHelper;
use DateTime;
use GuzzleHttp\Client;

class FeedMeEvents extends Component
{
    public function getDataForFeedMe(FeedDataEvent $event) {
        $api = EasyApi::$plugin->apis->getApiByFeedId($event->feedId);
        $apiUrl = $event->url;

        if ($api) {
            $responseData = null;
            try {
                if ($api->parentElementType != null && $api->parentElementType != "") {
                    $originalUrl = $apiUrl;
                    switch ($api->parentElementType) {
                        case 'craft\\elements\\Asset':
                            $assetId = $api->parentElementGroup[$api->parentElementType];
                
                            $parents = Asset::find()
                                ->siteId($api->siteId)
                                ->assetId($assetId)
                                ->all();
                            break;
        
                        case 'craft\\elements\\Category':
                            $groupId = $api->parentElementGroup[$api->parentElementType];
                            
                            $parents = Category::find()
                                ->siteId($api->siteId)
                                ->groupId($groupId)
                                ->all();
                            break;
        
                        case 'craft\\elements\\Entry':
                            $sectionId = $api->parentElementGroup[$api->parentElementType]["section"];
                            $entryTypeId = $api->parentElementGroup[$api->parentElementType]["entryType"];
                
                            $parents = Entry::find()
                                ->siteId($api->siteId)
                                ->sectionId($sectionId)
                                ->typeId($entryTypeId)
                                ->all();
                            break;
                            
                        case 'craft\\elements\\Tag':
                            $tagId = $api->parentElementGroup[$api->parentElementType];
                
                            $parents = Tag::find()
                                ->siteId($api->siteId)
                                ->tagId($tagId)
                                ->all();
                            break;
                            
                            case 'craft\\elements\\GlobalSet':
                                $globalSetId = $api->parentElementGroup[parentElementType]->globalSet;
                    
                                $parents = Glogal::find()
                                    ->siteId($api->siteId)
                                    ->globalSetId($globalSetId)
                                    ->all();
                                break;
        
                        default:
                            # shouldn't get here
                            break;
                    }
                    foreach ($parents as $parent) {
                        // Access entry fields
                        $dynamicValue = $parent->getFieldValue($api->parentElementIdField); // Replace 'yourDynamicField' with the handle of your dynamic field
    
                        // Original string with placeholder
                        $originalString = $originalUrl;
    
                        // Replace the placeholder with the dynamic value
                        $modifiedString = str_replace('{{ Id }}', $dynamicValue, $originalString);
                        
                        $apiData = EasyApiDataTypes::getRawData($modifiedString, $api->id);

                        if ($responseData != null) {
                            $array1 = json_decode($responseData['data'], true);
                            $array2 = json_decode($apiData['data'], true);
                            
                            // Merge arrays
                            $mergedArray = array_merge_recursive($array1, $array2);
                            
                            // Encode merged array back to JSON
                            $responseData['data'] = json_encode($mergedArray);
                        } else {
                            $responseData = $apiData;
                        }
                    }
                    $apiUrl = $originalUrl;
                } else {
                    $responseData = EasyApiDataTypes::getRawData($apiUrl, $api->id);
                }

                if ($responseData['success']) {
                    $tempArray = json_decode($responseData['data'], true);
                    $this->updatePaginationNode($api, $tempArray);
                    $responseData['data'] = json_encode($tempArray);
                } else {
                    throw new Exception($responseData['error'], 1);
                }

                $event->response = $responseData;
            } catch (Throwable $e) {
                // Even though we catch errors on each step of the loop, make sure to catch errors that can be anywhere
                // else in this function, just to be super-safe and not cause the queue job to die.
                EasyApi::error('`{e} - {f}: {l}`.', ['e' => $e->getMessage(), 'f' => basename($e->getFile()), 'l' => $e->getLine()]);
                Craft::$app->getErrorHandler()->logException($e);

                $event->response = [
                    'success' => false,
                    'data' => $e->getMessage(),
                ];
            }
        }
    }

    public function updatePaginationNode($api, &$data) {
        if ($api) {
            if ($api->offsetField != "") {
                DataHelper::updateOffsetValue($api, $data);
            }
        }
    }
}