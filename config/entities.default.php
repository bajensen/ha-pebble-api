<?php
return [
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
    ],
    'accent_lights' => [
        'name' => 'Accent Lights',
        'desc' => 'Living Room',
        'state_entity' => 'switch.living_accent',
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
                'desc' => 'Turn on lights',
                'ha_service' => 'switch',
                'ha_action' => 'turn_on',
                'ha_data' => ['entity_id' => 'switch.living_accent']
            ],
            'off' => [
                'name' => 'Off',
                'desc' => 'Turn off lights',
                'ha_service' => 'switch',
                'ha_action' => 'turn_off',
                'ha_data' => ['entity_id' => 'switch.living_accent']
            ],
        ]
    ],
    'white_led' => [
        'name' => 'White LED',
        'desc' => 'Living Room',
        'state_entity' => 'switch.living_white',
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
                'desc' => 'Turn on lights',
                'ha_service' => 'switch',
                'ha_action' => 'turn_on',
                'ha_data' => ['entity_id' => 'switch.living_white']
            ],
            'off' => [
                'name' => 'Off',
                'desc' => 'Turn off lights',
                'ha_service' => 'switch',
                'ha_action' => 'turn_off',
                'ha_data' => ['entity_id' => 'switch.living_white']
            ],
        ]
    ],
];
