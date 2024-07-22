<?php

namespace App\Helpers;

use GuzzleHttp\Client;

class SupabaseHelper
{
    protected $client;
    protected $storageUrl;
    protected $apiKey;
    protected $bucket;

    public function __construct()
    {
        $this->client = new Client();
        $this->storageUrl = env('SUPABASE_URL') . '/storage/v1/object';
        $this->apiKey = env('SUPABASE_API_KEY');
        $this->bucket = env('SUPABASE_STORAGE_BUCKET');
    }

    public function uploadFile($filePath, $fileName)
    {
        $fileContent = file_get_contents($filePath);

        $response = $this->client->request('POST', $this->storageUrl . '/' . $this->bucket . '/' . $fileName, [
            'headers' => [
                'apikey' => $this->apiKey,
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/octet-stream'
            ],
            'body' => $fileContent
        ]);

        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody(), true);
        }

        return null;
    }

    public function deleteFile($fileName)
    {
        $response = $this->client->request('DELETE', $this->storageUrl . '/' . $this->bucket . '/' . $fileName, [
            'headers' => [
                'apikey' => $this->apiKey,
                'Authorization' => 'Bearer ' . $this->apiKey,
            ],
        ]);

        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody(), true);
        }

        return null;
    }
}
