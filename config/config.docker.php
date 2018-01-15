<?php
return [
    'base_url' => getenv('HOME_ASSISTANT_URL'),
    'password' => getenv('HOME_ASSISTANT_PASSWORD'),
    'api_keys' => [getenv('API_KEY')],
    'entities' => @include getenv('ENTITIES_FILE_PATH')
];
