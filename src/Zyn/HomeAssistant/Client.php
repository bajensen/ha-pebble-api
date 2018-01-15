<?php
namespace Zyn\HomeAssistant;

class Client {
    protected $baseUrl;
    protected $password;
    protected $entityStates;

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    /**
     * Client constructor.
     * @param string $baseUrl The URL of home assistant, such as http://localhost:8123
     * @param string $password The password to use
     */
    public function __construct ($baseUrl, $password) {
        $this->setBaseUrl($baseUrl);
        $this->setPassword($password);
    }

    public function clearStates () {
        $this->entityStates = null;
    }

    protected function processState ($entity) {
        $state = $entity['state'];
        $src = $entity;

        return [
            'state' => $state,
            'src' => $src
        ];
    }

    public function getStates () {
        if (! $this->entityStates) {
            $result = $this->get('/api/states');

            $entities = [];

            foreach ($result as $entity) {
                $entityId = $entity['entity_id'];
                $entities[$entityId] = $this->processState($entity);
            }

            $this->entityStates = $entities;
        }

        return $this->entityStates;
    }

    public function callService ($service, $action, $data) {
        $path = '/api/services/' . $service . '/' . $action;

        $result = $this->post($path, $data);

        // Use what Home Assistant has given back to update all the entity states
        foreach ($result as $entity) {
            $entityId = $entity['entity_id'];
            $this->entityStates[$entityId] = $this->processState($entity);
        }

        return $result;
    }

    public function get ($path) {
        return $this->request(self::METHOD_GET, $path);
    }

    public function post ($path, $data) {
        return $this->request(self::METHOD_POST, $path, $data);
    }

    public function request ($method, $path, $data = null) {
        $ch = curl_init();
        $url = $this->prepareUrl($path);

        curl_setopt($ch,CURLOPT_URL, $url);

        if ($method == self::METHOD_POST) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_NUMERIC_CHECK));
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHeaders());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode == 0) {
            throw new ClientException(curl_error($ch));
        }

        if ($httpCode != 200) {
            throw new ClientException('Result HTTP Status was not 200: ' . $httpCode . ' Body: ' . $result);
        }

        curl_close ($ch);

        return json_decode($result, true);
    }

    protected function prepareUrl ($path) {
        // Does baseUrl end with a slash?
        $baseUrl = $this->getBaseUrl();

        // Remove a trailing slash from the baseUrl
        if (substr($baseUrl, -1, 1) == '/') {
            $baseUrl = substr($baseUrl, 0, strlen($baseUrl) - 1);
        }

        // Prepend a slash to the path
        if (substr($path, 0, 1) != '/') {
            $path = '/' . $path;
        }

        return $baseUrl . $path;
    }

    protected function getHeaders () {
        return [
            'x-ha-access: ' . $this->getPassword(),
            'Content-Type: application/json'
        ];
    }

    /**
     * @return mixed
     */
    public function getBaseUrl () {
        return $this->baseUrl;
    }

    /**
     * @param mixed $baseUrl
     */
    public function setBaseUrl ($baseUrl) {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @return mixed
     */
    public function getPassword () {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword ($password) {
        $this->password = $password;
    }
}