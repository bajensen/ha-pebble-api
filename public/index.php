<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/../vendor/autoload.php';

///////////////////////////////////////////////
// Load application configuration
///////////////////////////////////////////////
$appConfig = include __DIR__ . '/../config/config.default.php';

$env = getenv('ENVIRONMENT') ?: 'local';
$envConfigFileName = __DIR__ . '/../config/config.' . $env . '.php';

if (file_exists($envConfigFileName)) {
    $envConfig = include $envConfigFileName;

    $appConfig = array_replace_recursive($appConfig, $envConfig);
}

///////////////////////////////////////////////
// Create api client and entity service
///////////////////////////////////////////////
$apiClient = new \Zyn\HomeAssistant\Client($appConfig['base_url'], $appConfig['password']);
$entityService = new \Zyn\HomeAssistant\EntityService($appConfig['entities'], $apiClient);

///////////////////////////////////////////////
// Slim Framework Configuration
///////////////////////////////////////////////
$config = [];
$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

///////////////////////////////////////////////
// Slim Framework App
///////////////////////////////////////////////
$app = new \Slim\App(['settings' => $config]);

///////////////////////////////////////////////
// Authentication Middleware
///////////////////////////////////////////////
$app->add(function (Request $request, Response $response, $next) use ($appConfig) {
    $apiKeyHeader = $request->getHeader('X-API-Key');

    $params = $request->getQueryParams();
    $apiKeyParam = array_key_exists('key', $params) ? $params['key'] : null;

    $apiKey = $apiKeyHeader ? $apiKeyHeader : [$apiKeyParam];

    if (! array_intersect($apiKey, $appConfig['api_keys'])) {
        $response->getBody()->write(json_encode([ 'status' => 'error', 'message' => 'Access Denied'], JSON_NUMERIC_CHECK));

        return $response
            ->withAddedHeader('Content-Type', 'application/json')
            ->withStatus(401);
    }

    $response = $next($request, $response);

    return $response;
});

$container = $app->getContainer();

///////////////////////////////////////////////
// Attach Logging
///////////////////////////////////////////////
$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler(__DIR__. '/../logs/app.log');
    $logger->pushHandler($file_handler);
    return $logger;
};

///////////////////////////////////////////////
// URL: /
///////////////////////////////////////////////
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write('Home Assistant Pebble API');

    return $response->withStatus(200);
});

///////////////////////////////////////////////
// URL: /entities
///////////////////////////////////////////////
$app->get('/entities', function (Request $request, Response $response) use ($entityService, $apiClient) {
    $response->getBody()->write(json_encode($entityService->getEntities(), JSON_NUMERIC_CHECK));

    return $response
        ->withAddedHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

///////////////////////////////////////////////
// URL: /entities/{entity_id}
///////////////////////////////////////////////
$app->get('/entities/{entity_id}', function (Request $request, Response $response) use ($entityService, $apiClient)  {
    $entityId = $request->getAttribute('entity_id');

    $actions = $entityService->getEntityActions($entityId);

    if (count($actions)) {
        $response->getBody()->write(json_encode($actions, JSON_NUMERIC_CHECK));

        return $response
            ->withAddedHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
    else {
        $response->getBody()->write(json_encode(['status' => 'error', 'message' => 'Not found.'], JSON_NUMERIC_CHECK));

        return $response
            ->withAddedHeader('Content-Type', 'application/json')
            ->withStatus(404);
    }
});

///////////////////////////////////////////////
// URL: /entities/{entity_id}/{action_id}
///////////////////////////////////////////////
$app->any('/entities/{entity_id}/{action_id}', function (Request $request, Response $response) use ($entityService, $apiClient)  {
    $entityId = $request->getAttribute('entity_id');
    $actionId = $request->getAttribute('action_id');

    $params = $request->getQueryParams();
    $pretty = array_key_exists('pretty', $params);

    $action = $entityService->getEntityAction($entityId, $actionId);

    if ($action) {
        try {
            $result = $apiClient->callService($action['ha_service'], $action['ha_action'], $action['ha_data']);

            $response->getBody()->write(json_encode(['status' => 'success', 'message' => 'Success!'], JSON_NUMERIC_CHECK));
//            $response->getBody()->write(json_encode($result, JSON_NUMERIC_CHECK | ($pretty ? JSON_PRETTY_PRINT : 0)));

            return $response
                ->withAddedHeader('Content-Type', 'application/json')
                ->withStatus(200);
        }
        catch (\Zyn\HomeAssistant\ClientException $e) {
            $response->getBody()->write(json_encode(['status' => 'error', 'message' => $e->getMessage()], JSON_NUMERIC_CHECK));

            return $response
                ->withAddedHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
    else {
        $response->getBody()->write(json_encode(['status' => 'error', 'message' => 'Not found.'], JSON_NUMERIC_CHECK));

        return $response
            ->withAddedHeader('Content-Type', 'application/json')
            ->withStatus(404);
    }
});

$app->run();
