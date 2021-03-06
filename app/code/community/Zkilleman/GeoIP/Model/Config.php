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

    const XML_PATH_ENABLED               = 'geoip/general/enabled';
    const XML_PATH_SET_ADDRESSES_COUNTRY = 'geoip/general/set_addresses_country';
    const XML_PATH_STORE_REDIRECT        = 'geoip/redirect/enabled';
    const XML_PATH_REDIRECT_WEBSITES     = 'geoip/redirect/allowed_websites';
    const XML_PATH_REDIRECT_LOGGING      = 'geoip/redirect/logging_enabled';
    const XML_PATH_REDIRECT_ROUTES       = 'geoip/redirect/allowed_routes';
    const XML_PATH_ROUTES_WHITELIST      = 'geoip/redirect/routes_whitelist';
    const XML_PATH_ROUTES_BLACKLIST      = 'geoip/redirect/routes_blacklist';
    const XML_PATH_COUNTRY_SOURCES       = 'global/geoip/country/sources';
    const XML_PATH_COUNTRY_SOURCE        = 'geoip/import/country_source';
    const XML_PATH_COUNTRY_IPV6_SOURCES  = 'global/geoip/country_ipv6/sources';
    const XML_PATH_COUNTRY_IPV6_SOURCE   = 'geoip/import/country_ipv6_source';

    /**
     *
     * @var array
     */
    protected $_countrySources = null;

    /**
     *
     * @var array
     */
    protected $_countryIpv6Sources = null;

    /**
     *
     * @param  string $path
     * @return array
     */
    protected function _getCountrySources($path)
    {
        $sources = array();
        $node = Mage::getConfig()->getNode($path);
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

                $sources[$sourceConfig->getName()] =
                        new Varien_Object(array(
                            'code'           => $sourceConfig->getName(),
                            'label'          => $label,
                            'resource_model' =>
                                    (string) $sourceConfig->resourceModel));
            }
        }
        return $sources;
    }

    /**
     *
     * @return array
     */
    public function getCountrySources()
    {
        if ($this->_countrySources == null) {
            $this->_countrySources =
                        $this->_getCountrySources(self::XML_PATH_COUNTRY_SOURCES);
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

    /**
     *
     * @return array
     */
    public function getCountryIpv6Sources()
    {
        if ($this->_countryIpv6Sources == null) {
            $this->_countryIpv6Sources =
                    $this->_getCountrySources(self::XML_PATH_COUNTRY_IPV6_SOURCES);
        }
        return $this->_countryIpv6Sources;
    }

    /**
     *
     * @return Varien_Object
     */
    public function getCountryIpv6Source()
    {
        $code = Mage::getStoreConfig(self::XML_PATH_COUNTRY_IPV6_SOURCE);
        $sources = $this->getCountryIpv6Sources();
        return isset($sources[$code]) ? $sources[$code] : null;
    }

    /**
     *
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::isInstalled() &&
                Mage::getStoreConfigFlag(self::XML_PATH_ENABLED);
    }

    /**
     *
     * @return bool
     */
    public function isSetAddressesCountryEnabled()
    {
        return $this->isEnabled() &&
                    Mage::getStoreConfigFlag(self::XML_PATH_SET_ADDRESSES_COUNTRY);
    }

    /**
     *
     * @return bool
     */
    public function isStoreRedirectEnabled()
    {
        return $this->isEnabled() &&
                    Mage::getStoreConfigFlag(self::XML_PATH_STORE_REDIRECT);
    }

    /**
     *
     * @return array
     */
    public function getRedirectWebsiteIds($store = null)
    {
        $ids = $this->_explode(
                    Mage::getStoreConfig(self::XML_PATH_REDIRECT_WEBSITES, $store));
        $current = Mage::app()->getWebsite()->getId();
        if (!in_array($current, $ids)) {
            // Current website should always be allowed, right?
            $ids[] = $current;
        }
        return $ids;
    }

    /**
     *
     * @return bool
     */
    public function isCountryAllowed($countryCode, $store = null)
    {
        $allowed = $this->_explode(
                        Mage::getStoreConfig('general/country/allow', $store));
        return in_array(strtoupper($countryCode), $allowed);
    }

    /**
     *
     * @return boolean
     */
    public function isRedirectLoggingEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_REDIRECT_LOGGING);
    }

    /**
     *
     * @param  Mage_Core_Controller_Varien_Action $action
     * @return boolean
     */
    public function isStoreRedirectAllowed($action)
    {
        $result;
        switch (Mage::getStoreConfig(self::XML_PATH_REDIRECT_ROUTES)) {
            case Zkilleman_GeoIP_Model_Config_Source_Redirect_Routerestrictions::WHITELIST:
                $routeName = $action->getRequest()->getRequestedRouteName();
                $whitelist = $this->_explode(Mage::getStoreConfig(
                                                self::XML_PATH_ROUTES_WHITELIST));
                $result    = in_array($routeName, $whitelist);
                break;
            case Zkilleman_GeoIP_Model_Config_Source_Redirect_Routerestrictions::BLACKLIST:
                $routeName = $action->getRequest()->getRequestedRouteName();
                $blacklist = $this->_explode(Mage::getStoreConfig(
                                                self::XML_PATH_ROUTES_BLACKLIST));
                $result    = !in_array($routeName, $blacklist);
                break;
            default:
            //case Zkilleman_GeoIP_Model_Config_Source_Redirect_Routerestrictions::ALLOW_ALL:
                $result = true;
                break;
        }
        return $result;
    }

    /**
     *
     * @return array
     */
    protected function _explode($string)
    {
        return preg_split('/\s*,\s*/', (string) $string, -1, PREG_SPLIT_NO_EMPTY);
    }
}
