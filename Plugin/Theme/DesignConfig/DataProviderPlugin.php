<?php
/**
 * Copyright © 2018 Studio Raz. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace SR\ThemeConfigurator\Plugin\Theme\DesignConfig;

use Magento\Theme\Model\Design\Config\DataProvider as ThemeDesignConfigDataProvider;

class DataProviderPlugin
{
    /**
     * After Plugin
     * @origin ThemeDesignConfigDataProvider::getMeta
     *
     * @param ThemeDesignConfigDataProvider $subject
     * @param array $result
     * @return array
     */
    public function afterGetMeta(ThemeDesignConfigDataProvider $subject, array $result = [])
    {
         if (!isset($result['other_settings']['children']['sr_theme_configurator'])) {
            return $result;
        }

        $this->prepareLessVariablesData($result);

        return $result;
    }

    /**
     * Prepares meta  data about Less Variables parameter
     * Modifies $metaData
     *
     * @param array $metaData [By Reference]
     * @return $this
     */
    private function prepareLessVariablesData(array &$metaData = [])
    {
        if (!isset($metaData['other_settings']['children']['sr_theme_configurator']['children']['less_variables'])) {
            return $this;
        }

        $lessVariablesData = $metaData['other_settings']['children']['sr_theme_configurator']['children']['less_variables'] ?? [];
        $lessVariablesSubsets = $lessVariablesData['arguments']['data']['config']['default'];

        if (empty($lessVariablesSubsets)) {
            unset($metaData['other_settings']['children']['sr_theme_configurator']['children']['less_variables']);
            return $this;
        }

        $showFallbackReset = (bool)($lessVariablesData['arguments']['data']['config']['showFallbackReset'] ?? false);
        $tmpMetaData = [];
        foreach ($lessVariablesSubsets as $subFieldsetCode => $fields) {
            foreach ($fields as $fieldCode => $fieldValue) {
                if (!isset($tmpMetaData[$subFieldsetCode])) {
                    $tmpMetaData[$subFieldsetCode] = [
                        'children' => [],
                    ];
                }

                $tmpMetaData[$subFieldsetCode]['children'][$fieldCode] = [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'default' => $fieldValue,
                                'showFallbackReset' => $showFallbackReset,
                            ],
                        ],
                    ],
                ];
            }
        }

        $metaData['other_settings']['children']['sr_theme_configurator']['children']['less_variables'] = [
            'children' => $tmpMetaData,
        ];

        return $this;
    }
}
