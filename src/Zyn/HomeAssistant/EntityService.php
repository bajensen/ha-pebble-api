<?php
namespace Zyn\HomeAssistant;

class EntityService {
    protected $config;

    /** @var Client */
    protected $apiClient;

    /**
     * EntityService constructor.
     * @param array $config
     * @param Client $apiClient
     */
    public function __construct ($config, $apiClient) {
        $this->config = $config;
        $this->apiClient = $apiClient;
    }

    public function getEntityState ($entityId) {
        $entities = $this->config;

        if (! array_key_exists($entityId, $entities)) {
            return null;
        }

        $entityProperties = $entities[$entityId];

        if (! array_key_exists('state_entity', $entityProperties)) {
            return null;
        }

        $stateEntity = $entityProperties['state_entity'];

        if (! $stateEntity) {
            return null;
        }

        $states = $this->apiClient->getStates();

        if (! array_key_exists($stateEntity, $states) || ! array_key_exists('state', $states[$stateEntity])) {
            return null;
        }

        return $states[$stateEntity]['state'];
    }

    public function getEntities () {
        $entities = $this->config;

        $output = [];

        foreach ($entities as $entityId => $entityProperties) {
            $output[] = $this->getEntityRow($entityId, $entityProperties);
        }

        return $output;
    }

    public function getEntityNextActionId ($entityId) {
        $actions = $this->getEntityActions($entityId);
        $state = $this->getEntityState($entityId);

        unset($actions[$state]);

        $nextAction = $this->getFirstElementOfArray($actions);

        return $nextAction['action_id'];
    }

    public function getEntityActions ($entityId) {
        $entities = $this->config;

        if (! array_key_exists($entityId, $entities)) {
            return [];
        }

        $entity = $entities[$entityId];

        $output = [];

        $state = $this->getEntityState($entityId);
        $stateMap = $entity['state_map'];

        if ($state !== null && array_key_exists($state, $stateMap)) {
            foreach ($stateMap[$state]['action_list'] as $actionId) {
                $actionProperties = $entity['actions'][$actionId];
                $output[] = $this->getActionRow($entityId, $actionId, $actionProperties);
            }
        }
        else {
            foreach ($entity['actions'] as $actionId => $actionProperties) {
                $output[] = $this->getActionRow($entityId, $actionId, $actionProperties);
            }
        }

        return $output;
    }

    public function getEntityRow ($entityId, $entityProperties = null) {
        $entities = $this->config;

        $state = $this->getEntityState($entityId);

        $entityProperties = $entityProperties ?: $entities[$entityId];

        $stateMap = array_key_exists('state_map', $entityProperties) ? $entityProperties['state_map'] : null;
        $stateText = (array_key_exists($state, $stateMap) && array_key_exists('name', $stateMap[$state])) ? $stateMap[$state]['name'] : null;

        return [
            'title' => $entityProperties['name'],
            'subtitle' => ($stateText ? '(' . $stateText . ') ' : '') . $entityProperties['desc'],
            'entity_id' => $entityId,
        ];
    }

    public function getActionRow ($entityId, $actionId, $actionProperties) {
        $entities = $this->config;

        $actionProperties = $actionProperties ?: $entities[$entityId]['actions'][$actionId];

        return [
            'title' => $actionProperties['name'],
            'subtitle' => $actionProperties['desc'],
            'action_id' => $actionId,
        ];
    }

    /**
     * @param string $entityId
     * @param string $actionId
     * @return array|null null if not found, array of action config otherwise
     */
    public function getEntityAction ($entityId, $actionId) {
        $entities = $this->config;

        if (! array_key_exists($entityId, $entities)) {
            return null;
        }

        $entity = $entities[$entityId];
        $actions = $entity['actions'];

        if (! array_key_exists($actionId, $actions)) {
            return null;
        }

        return $actions[$actionId];
    }

    protected function getFirstElementOfArray ($array) {
        return array_shift(array_slice($array, 0, 1));
    }
}