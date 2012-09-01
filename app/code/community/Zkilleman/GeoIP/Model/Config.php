<?php
/**
 * Zkilleman_GeoIP
 *
 * Copyright (C) 2012 Henrik Hedelund (henke.hedelund@gmail.com)
 *
 * This file is part of Zkilleman_GeoIP.
 *
 * Zkilleman_GeoIP is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Zkilleman_GeoIP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Zkilleman_GeoIP. If not, see <http://www.gnu.org/licenses/>.
 *
 * @category  Zkilleman
 * @package   Zkilleman_GeoIP
 * @author    Henrik Hedelund <henke.hedelund@gmail.com>
 * @copyright 2012 Henrik Hedelund (henke.hedelund@gmail.com)
 * @license   http://www.gnu.org/licenses/lgpl.html GNU LGPL
 * @link      https://github.com/henkelund/magento-zkilleman-geoip
 */

class Zkilleman_GeoIP_Model_Config
{

    const XML_PATH_COUNTRY_SOURCES = 'global/geoip/country/sources';
    const XML_PATH_COUNTRY_SOURCE  = 'geoip/general/country_source';

    /**
     *
     * @var array
     */
    protected $_countrySources = null;

    /**
     *
     * @return array
     */
    public function getCountrySources()
    {
        if ($this->_countrySources == null) {
            $this->_countrySources = array();
            $node = Mage::getConfig()->getNode(self::XML_PATH_COUNTRY_SOURCES);
            /* @var $node Mage_Core_Model_Config_Element */
            foreach ($node->children() as $sourceConfig) {
                /* @var $sourceConfig Mage_Core_Model_Config_Element */
                $modelClass = Mage::getConfig()
                            ->getResourceModelClassName(
                                    (string) $sourceConfig->resourceModel);

                if (@is_subclass_of(
                        $modelClass,
                        'Zkilleman_GeoIP_Model_Resource_Indexer_Country_Abstract')) {

                    $module = $sourceConfig->getAttribute('module');
                    $helper = Mage::helper($module ? $module : 'geoip');
                    $label  = isset($sourceConfig->label) ?
                                    $helper->__((string) $sourceConfig->label) :
                                        $sourceConfig->getName();

                    $this->_countrySources[$sourceConfig->getName()] =
                            new Varien_Object(array(
                                'code'           => $sourceConfig->getName(),
                                'label'          => $label,
                                'resource_model' =>
                                        (string) $sourceConfig->resourceModel));
                }
            }
        }
        return $this->_countrySources;
    }

    /**
     *
     * @return Varien_Object
     */
    public function getCountrySource()
    {
        $code = Mage::getStoreConfig(self::XML_PATH_COUNTRY_SOURCE);
        $sources = $this->getCountrySources();
        return isset($sources[$code]) ? $sources[$code] : null;
    }
}
