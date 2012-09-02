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

class Zkilleman_GeoIP_Model_Observer
{

    /**
     *
     * @return Zkilleman_GeoIP_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('geoip/config');
    }

    /**
     *
     * @param Varien_Event_Observer $observer
     */
    public function predispatch($observer)
    {
        $session = Mage::getSingleton('customer/session');
        /* @var $session Mage_Customer_Model_Session */

        if (!$session->hasGeoipCountryCode()) {

            $countryCode = Mage::helper('geoip')->getClientCountryCode();
            $session->setGeoipCountryCode($countryCode);
        }

        Mage::register('client_country_id', $session->getGeoipCountryCode(), true);
    }

    /**
     *
     * @param Varien_Event_Observer $observer
     */
    public function checkoutPrerender($observer)
    {
        if (!$this->_getConfig()->isSetAddressesCountryEnabled()) {
            return;
        }

        $countryId = Mage::registry('client_country_id');
        if (!$countryId) {
            return;
        }

        $layout = Mage::getSingleton('core/layout');
        /* @var $layout Mage_Core_Model_Layout */
        foreach (array(
                    'checkout.onepage.billing',
                    'checkout.onepage.shipping') as $name) {

            $block   = $layout ? $layout->getBlock($name) : null;
            $address = $block ? $block->getAddress() : null;
            if (is_object($address) &&
                    $address instanceof Varien_Object &&
                    !$address->hasCountryId()) {

                $address->setCountryId($countryId);
            }
        }
    }
}
