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

class Zkilleman_GeoIP_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     *
     * @param  string $ip
     * @return int
     */
    public function ipToInt($ip)
    {
        $result = 0;
        $parts = explode('.', $ip);
        for ($i = 0; $i < 4; ++$i) {
            $result += isset($parts[$i]) ? $parts[$i]*pow(256, (3 - $i)) : 0;
        }
        return $result;
    }

    /**
     *
     * @param  int $int
     * @return string
     */
    public function intToIp($int)
    {
        return implode('.', array(
                                    ($int/16777216)%256,
                                    ($int/65536)%256,
                                    ($int/256)%256,
                                    ($int)%256
                                ));
    }

    /**
     *
     * @param  string $ip
     * @return Zkilleman_GeoIP_Model_Country
     */
    public function getCountry($ip)
    {
        if (!is_scalar($ip)) {
            return null;
        } else if (!is_numeric($ip)) {
            $ip = $this->ipToInt($ip);
        }
        $ranges = Mage::getResourceModel('geoip/country_collection');
        /* @var $ranges Zkilleman_GeoIP_Model_Resource_Country_Collection */
        $ranges->addFieldToFilter('start_ip_int', array('lteq' => $ip))
                    ->addFieldToFilter('end_ip_int', array('gteq' => $ip))
                        ->setPageSize(1);

        return $ranges->getSize() > 0 ? $ranges->getFirstItem() : null;
    }

    /**
     *
     * @return Zkilleman_GeoIP_Model_Country
     */
    public function getClientCountry()
    {
        return $this->getCountry(Mage::app()->getRequest()->getClientIp());
    }
}
