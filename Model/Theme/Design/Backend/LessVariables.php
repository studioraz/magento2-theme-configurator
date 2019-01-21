<?php
/**
 * Copyright © 2018 Studio Raz. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace SR\ThemeConfigurator\Model\Theme\Design\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value as AppConfigValue;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;

class LessVariables extends AppConfigValue
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'srthemeconfig_lessvariables_config_data';

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * LessVariables constructor.
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param SerializerInterface $serializer
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,

        SerializerInterface $serializer,

        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->serializer = $serializer;

        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @inheritdoc
     */
    public function afterLoad()
    {
        $value = $this->getUnserializedValue();

        //@fixme: tmp key=>value is added in order to parse Empty Array as JSON (not an Array Object)
        // during form elements processing and flattening the fields
        $value['tmp'] = [];

        $this->setValue($value);
        return parent::afterLoad();
    }

    /**
     * @inheritdoc
     */
    public function beforeSave()
    {
        parent::beforeSave();

        $this->setValue($this->getSerializedValue());
        return $this;
    }

    /**
     * Unserializes value if it is applicable
     * Returns an Array
     *
     * @return array
     */
    public function getUnserializedValue()
    {
        $value = $this->getValue();

        if (!is_array($value)) {
            $value = (string)($value ?: '{}');
            $value = $this->serializer->unserialize($value);
        }

        return (array)$value;
    }

    /**
     * Serializes value if it is applicable
     * Returns an Array
     *
     * @return string
     */
    public function getSerializedValue()
    {
        $value = $this->getValue();

        if (is_array($value)) {
            $value = $this->serializer->serialize($value);
        }

        return (string)$value;
    }

    /**
     * Return flat array of the Value
     *
     * @return array
     */
    public function getFlattenVarsList()
    {
        $list = [];
        foreach ($this->getUnserializedValue() as $group => $fields) {
            $list = array_replace($list, $fields);
        }
        return $list;
    }
}
