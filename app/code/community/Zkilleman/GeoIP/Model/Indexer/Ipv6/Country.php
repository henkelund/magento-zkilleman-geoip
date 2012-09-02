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

class Zkilleman_GeoIP_Model_Indexer_Ipv6_Country
                extends Mage_Index_Model_Indexer_Abstract
{

    /**
     * Internal constructor
     *
     */
    protected function _construct()
    {
        $config = Mage::getSingleton('geoip/config');
        if ($source = $config->getCountryIpv6Source()) {
            $this->_init($source->getResourceModel());
        }
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('geoip')->__('GeoIP IPv6 Countries');
    }

    /**
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('geoip')->__('IPv6 range to country index');
    }

    /**
     * Register data required by process in event object
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        // No relevant events
        return $this;
    }

    /**
     * Process event
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        // No relevant events
    }
}
