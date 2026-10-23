<?php

namespace HTTPManager;

class HTTPManager {
    
    private $timeout = 30;
    private $userAgent = 'SpeXHTTPManager/2.0';
    
    /**
     * GET request
     * @param string $url - Request URL
     * @param array $headers - Additional headers
     * @return array|null
     */
    public function get($url, $headers = []) {
        return $this->makeRequest('GET', $url, null, $headers);
    }
    
    /**
     * POST request
     * @param string $url - Request URL
     * @param array $data - POST data
     * @param array $headers - Additional headers
     * @return array|null
     */
    public function post($url, $data = null, $headers = []) {
        return $this->makeRequest('POST', $url, $data, $headers);
    }
    
    /**
     * Make HTTP request
     * @param string $method - HTTP method
     * @param string $url - Request URL
     * @param mixed $data - Request data
     * @param array $headers - Headers
     * @return array|null
     */
    private function makeRequest($method, $url, $data = null, $headers = []) {
        $ch = curl_init();
        
        $defaultHeaders = [
            'Accept: application/json',
            'Content-Type: application/json'
        ];
        
        $headers = array_merge($defaultHeaders, $headers);
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        
        if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200 && $response) {
            return json_decode($response, true);
        }
        
        return null;
    }
    
    /**
     * Set timeout
     * @param int $seconds - Timeout in seconds
     */
    public function setTimeout($seconds) {
        $this->timeout = $seconds;
    }
}
?>