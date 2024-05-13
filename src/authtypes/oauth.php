<?php

namespace runwildstudio\easyapi\authtypes;

use Craft;
use runwildstudio\easyapi\base\AuthType;
use runwildstudio\easyapi\base\AuthTypeInterface;
use runwildstudio\easyapi\EasyApi;
use Exception;

class oauth extends AuthType implements AuthTypeInterface
{
    // Properties
    // =========================================================================

    /**
     * @var string
     */
    public static string $name = 'OAuth';

    private function getAuthToken($api): array
    {
        try {
            $curl = curl_init();
            // POST data
            $postData = [
                'client_id' => $api->authorizationAppId,
                'client_secret' => $api->authorizationAppSecret,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $api->authorizationRedirect
            ];

            if (!empty($api->authorizationCode)) {
                $postData['code'] = $api->authorizationCode;
            }

            curl_setopt_array($curl, array(
                CURLOPT_URL => $api->authorizationUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_CUSTOMREQUEST => 'POST'
            ));

            $data = curl_exec($curl);
            curl_close($curl);

            $response = ['success' => true, 'value' => $data];
        } catch (Exception $e) {
            $response = ['success' => false, 'error' => $e->getMessage()];
            Craft::$app->getErrorHandler()->logException($e);
        }

        return $response;
    }


    // Public Methods
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function getAuthValue($api): array
    {
        // Make sure auth has been populated!
        if ($api->authorizationUrl === undefined || $api->authorizationUrl === '') {
            return ['success' => false, 'error' => 'Authorization URL not specified'];
        }
        // Make sure auth has been populated!
        if ($api->authorizationAppId === undefined || $api->authorizationAppId === '') {
            return ['success' => false, 'error' => 'Authorization App Id not specified'];
        }
        // Make sure auth has been populated!
        if ($api->authorizationAppSecret === undefined || $api->authorizationAppSecret === '') {
            return ['success' => false, 'error' => 'Authorization App Secret not specified'];
        }
        // Make sure auth has been populated!
        if ($api->authorizationRedirect === undefined || $api->authorizationRedirect === '') {
            return ['success' => false, 'error' => 'Authorization Redirect URL not specified'];
        }

        return getAuthToken($api);
    }
}
