<?php
/**
 * Copyright © 2018 Studio Raz. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace SR\ThemeConfigurator\Model\Theme\FileGenerator;

use Magento\Framework\Exception\FileSystemException;

interface FileGeneratorInterface
{
    /**
     * Generates File with passed Content
     *
     * @param string $content File's Content
     * @return bool
     * @throws FileSystemException
     */
    public function generate($content = '');

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

    /**
     * Sets full path of Generated File
     *
     * @param string|null $generatedFile
     * @return $this
     */
    public function setGeneratedFile($generatedFile = null);

    /**
     * Returns full path of Generated File
     *
     * @return string|null
     */
    public function getGeneratedFile();
}
