<?php
namespace Lightmedia\Googleprint\Builders;

use Carbon\Carbon;
use GuzzleHttp\Exception\BadResponseException;
use Lightmedia\Googleprint\Cache\GooglePrintCache;
use Lightmedia\Googleprint\Exceptions\GooglePrintException;

class QueryBuilder {

    protected $accessToken;
    protected $values;

    const BASE_URL = 'https://www.google.com/cloudprint';

    protected function getAccessToken() {

        if(false === GooglePrintCache::has('accessToken')) {

            $this->refreshAccessToken();
        }

        return GooglePrintCache::get('accessToken');
    }

    protected function refreshAccessToken() {

        $params = [
            'client_id'     => config('print.oauth.clientId'),
            'client_secret' => config('print.oauth.secret'),
            'refresh_token' => config('print.oauth.refreshToken'),
            'grant_type'    => 'refresh_token',
        ];

        try {
            $client = new \GuzzleHttp\Client();
            $result = $client->request('POST', 'https://www.googleapis.com/oauth2/v4/token', [ 'form_params' => $params, ]);
        } catch(BadResponseException $e) {
            throw new GooglePrintException('Google oauth error: ' . $e->getResponse()->getReasonPhrase());
        }

        if($result->getStatusCode() == 200) {

            $response = json_decode((string)$result->getBody()->getContents(), true);

            $this->setAccessToken($response);

        } else {
            throw new \Exception('Unable to get access token!');
        }
    }

    protected function setAccessToken($response) {

        $expiresAt = Carbon::now()->addSeconds((int)$response['expires_in'] - 60);
        GooglePrintCache::put('accessToken', $response['access_token'], $expiresAt);

    }

    public function search(array $array) {

        $this->values = $array;

        $result = $this->request(self::BASE_URL . '/search');

        return $result;
    }

    public function submit(array $array) {

        $this->values = $array;

        $result = $this->request(self::BASE_URL . '/submit');

        return $result;
    }

    protected function removeEmptyValues() {

        foreach($this->values as $key => $value) {
            if(empty( $value )) {
                unset( $this->values[$key] );
            }
        }

        return $this->values;
    }

    protected function request($url) {

        $params = array_merge_recursive(
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getAccessToken(),
                ],
            ],
            [
                'form_params' => $this->removeEmptyValues(),
            ]
        );

        try {
            $client = new \GuzzleHttp\Client();
            $result = $client->request('POST', $url, $params);
        } catch(BadResponseException $e) {
            throw new GooglePrintException('Google API error: ' . $e->getResponse()->getReasonPhrase());
        }

        $response = json_decode($result->getBody()->getContents(), true);

        if(false === isset( $response['success'] )) {

            throw new GooglePrintException('Error in Google API connection');
        }

        if(true !== $response['success']){

            throw new GooglePrintException('Error in Google API request: ' . $response['message']);
        }
        
        return $response;
    }
}