<?php
/**
 * Copyright © 2018 Studio Raz. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace SR\ThemeConfigurator\Model\Theme\FileContentInjector;

use Magento\Framework\Exception\FileSystemException;

interface FileContentInjectorInterface
{
    /**
     * Injects Content into Recipient File (or into specific file)
     *
     * @param string $content File's Content
     * @param string|null $destinationFile Injection recipient
     * @return bool
     * @throws FileSystemException
     */
    public function inject($content = '', $destinationFile = null);

    /**
     * Sets config for FileGenerator
     *
     * @param array $config ex: ['scope' => {string}, 'area' => {string}, 'theme_code' => {string}, etc]
     * @return $this
     */
    public function setConfig(array $config = []);

    /**
     * Returns config for FileGenerator
     * for ex: ['scope' => {string}, 'area' => {string}, 'theme_code' => {string}, etc]
     *
     * @return array
     */
    public function getConfig();
}
