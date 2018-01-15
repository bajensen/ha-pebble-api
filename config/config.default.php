<?php
return [
    'base_url' => 'http://localhost:8123',
    'password' => 'hackme',
    'api_keys' => ['hackmemore'],
    'entities' => [
        'bedroom_2_light' => [
            'name' => 'Lamp',
            'desc' => '2nd Bedroom',
            'state_entity' => 'switch.bedroom_2_light',
            'state_map' => [
                'on' => [
                    'name' => 'On',
                    'action_list' => ['off', 'on']
                ],
                'off' => [
                    'name' => 'Off',
                    'action_list' => ['on', 'off']
                ],
            ],
            'actions' => [
                'on' => [
                    'name' => 'On',
                    'desc' => 'Turn on lamp',
                    'ha_service' => 'switch',
                    'ha_action' => 'turn_on',
                    'ha_data' => ['entity_id' => 'switch.bedroom_2_light']
                ],
                'off' => [
                    'name' => 'Off',
                    'desc' => 'Turn off lamp',
                    'ha_service' => 'switch',
                    'ha_action' => 'turn_off',
                    'ha_data' => ['entity_id' => 'switch.bedroom_2_light']
                ],
            ]
        ],
        'bedroom_2_fan' => [
            'name' => 'Fan',
            'desc' => '2nd Bedroom',
            'state_entity' => 'switch.bedroom_2_fan',
            'state_map' => [
                'on' => [
                    'name' => 'On',
                    'action_list' => ['off', 'on']
                ],
                'off' => [
                    'name' => 'Off',
                    'action_list' => ['on', 'off']
                ],
            ],
            'actions' => [
                'on' => [
                    'name' => 'On',
                    'desc' => 'Turn on fan',
                    'ha_service' => 'switch',
                    'ha_action' => 'turn_on',
                    'ha_data' => ['entity_id' => 'switch.bedroom_2_fan']
                ],
                'off' => [
                    'name' => 'Off',
                    'desc' => 'Turn off fan',
                    'ha_service' => 'switch',
                    'ha_action' => 'turn_off',
                    'ha_data' => ['entity_id' => 'switch.bedroom_2_fan']
                ],
            ]
        ]
    ]
];
