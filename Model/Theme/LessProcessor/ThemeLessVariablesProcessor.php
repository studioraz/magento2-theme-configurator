<?php
/**
 * Copyright © 2018 Studio Raz. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace SR\ThemeConfigurator\Model\Theme\LessProcessor;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ValueInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use SR\ThemeConfigurator\Model\Theme\FileContentInjector\FileContentInjectorInterface;
use SR\ThemeConfigurator\Model\Theme\FileContentInjector\LessFileContentInjectorFactory;
use SR\ThemeConfigurator\Model\Theme\FileGenerator\FileGeneratorInterface;
use SR\ThemeConfigurator\Model\Theme\FileGenerator\LessFileGeneratorFactory;

class ThemeLessVariablesProcessor
{
    /**
     * Default theme
     *
     * @see src/vendor/magento/module-theme/etc/di.xml:65
     *
     * <type name="Magento\Theme\Model\View\Design">
     *     <arguments>
     *         <argument name="themes" xsi:type="array">
     *             <item name="frontend" xsi:type="string">Magento/luma</item>
     *             <item name="adminhtml" xsi:type="string">Magento/backend</item>
     *         </argument>
     *     </arguments>
     * </type>
     */
    const THEME_CODE_DEFAULT = 'Magento/luma';

    /**
     * Default design area
     * @var string
     */
    private $designArea = Area::AREA_FRONTEND;

    /**
     * @var ValueInterface
     */
    private $configValue;

    /**
     * @var ThemeProviderInterface
     */
    private $themeProvider;

    /**
     * @var LessFileGeneratorFactory
     */
    private $fileGeneratorFactory;

    /**
     * @var LessFileContentInjectorFactory
     */
    private $fileContentInjectorFactory;

    /**
     * ThemeLessVariablesProcessor constructor.
     * @param ThemeProviderInterface $themeProvider
     * @param LessFileGeneratorFactory $fileGeneratorFactory
     * @param LessFileContentInjectorFactory $fileContentInjectorFactory
     */
    public function __construct(
        ThemeProviderInterface $themeProvider,
        LessFileGeneratorFactory $fileGeneratorFactory,
        LessFileContentInjectorFactory $fileContentInjectorFactory
    ) {
        $this->themeProvider = $themeProvider;
        $this->fileGeneratorFactory = $fileGeneratorFactory;
        $this->fileContentInjectorFactory = $fileContentInjectorFactory;
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    public function process()
    {
        /** @var ValueInterface $configValue */
        $configValue = $this->getConfigValue();

        /** @var ThemeInterface $theme */
        $theme = $this->getTheme($configValue);

        $config = [
            'scope' => $configValue->getScope(),
            'scope_id' => $configValue->getScopeId(),
            'theme_code' => $theme->getCode(),//ex: Magento/luma [{Vendor}/{theme-name}]
            'area' => $this->getDesignArea(),
        ];

        try {
            /** @var FileGeneratorInterface $fileGenerator */
            $fileGenerator = $this->fileGeneratorFactory->create();
            $fileGenerator->setConfig($config);

            $fileGenerator->generate($this->buildContent());

            /** @var FileContentInjectorInterface $fileContentInjector */
            $fileContentInjector = $this->fileContentInjectorFactory->create();
            $fileContentInjector->setConfig($config);

            $injectionContent = "@import '" . pathinfo($fileGenerator->getGeneratedFile(), PATHINFO_BASENAME) . "';";
            $fileContentInjector->inject($injectionContent);
        } catch(FileSystemException $e) {
            throw new LocalizedException(__($e->getMessage()));
        }

        return $this;
    }

    /**
     * @return string
     */
    private function buildContent()
    {
        $list = [];
        foreach ($this->getConfigValue()->getFlattenVarsList() as $varName => $varValue) {
            if ($varValue) {
                $list[] = $this->getFormattedLessVarDeclaration($varName, $varValue);
            }
        }

        return implode("\n", $list);
    }

    /**
     * @param ValueInterface $configValue
     * @return $this
     */
    public function setConfigValue(ValueInterface $configValue)
    {
        $this->configValue = $configValue;
        return $this;
    }

    /**
     * @return ValueInterface
     */
    public function getConfigValue()
    {
        return $this->configValue;
    }

    /**
     * @return string
     */
    public function getDesignArea()
    {
        return $this->designArea;
    }

    /**
     * @param string $name
     * @param string $value
     * @return string
     */
    private function getFormattedLessVarDeclaration($name, $value)
    {
        return '@' . $name . ': ' . $value . ';';
    }

    /**
     * Returns Theme by ConfigValue
     *
     * @param ValueInterface $configValue
     * @return ThemeInterface
     */
    private function getTheme(ValueInterface $configValue)
    {
        // Note: theme_id key has been added as a trick
        // see: \SR\ThemeConfigurator\Observer\ThemeDesignConfig\GenerateLessFileObserver::execute
        $themeId = (int)$configValue->getData('theme_id');

        /** @var ThemeInterface $theme */
        $theme = !empty($themeId)
            ? $this->themeProvider->getThemeById($themeId)
            : $this->themeProvider->getThemeByFullPath($this->getDesignArea() . '/' . self::THEME_CODE_DEFAULT);

        return $theme;
    }
}
