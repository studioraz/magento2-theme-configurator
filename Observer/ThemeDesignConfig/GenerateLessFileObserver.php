<?php
/**
 * Copyright © 2018 Studio Raz. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace SR\ThemeConfigurator\Observer\ThemeDesignConfig;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use SR\ThemeConfigurator\Model\Theme\Design\Backend\LessVariables;
use SR\ThemeConfigurator\Model\Theme\LessProcessor\ThemeLessVariablesProcessor;

class GenerateLessFileObserver implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ThemeLessVariablesProcessor
     */
    private $themeLessVariablesProcessor;

    /**
     * GenerateLessFileObserver constructor.
     * @param RequestInterface $request
     * @param ThemeLessVariablesProcessor $themeLessVariablesProcessor
     */
    public function __construct(
        RequestInterface $request,
        ThemeLessVariablesProcessor $themeLessVariablesProcessor
    ) {
        $this->request = $request;
        $this->themeLessVariablesProcessor = $themeLessVariablesProcessor;
    }

    /**
     * @event srthemeconfig_lessvariables_config_data_save_after
     *
     * @param Observer $observer
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var LessVariables $lessVariablesObject */
        $lessVariablesObject = $observer->getDataObject();

        $themeId = $this->request->getParam('theme_theme_id', '');
        $lessVariablesObject->setData('theme_id', $themeId);// trick: to get actual theme id during processing

        $this->themeLessVariablesProcessor->setConfigValue($lessVariablesObject);

        $this->themeLessVariablesProcessor->process();
    }
}
