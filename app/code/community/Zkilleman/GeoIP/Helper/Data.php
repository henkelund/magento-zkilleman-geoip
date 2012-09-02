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
     * @param  string $ipv6
     * @return string
     */
    public function ipv6ToInt($ipv6)
    {
        if (!@class_exists('Math_BigInteger')) {
            require_once(implode(DS,
                            array('lib', 'phpseclib', 'Math', 'BigInteger.php')));
        }

        $segs = explode(':', $ipv6);
        $hex  = '';
        for ($i = 0; $i < 8; ++$i) {
            $hex .= str_pad(isset($segs[$i]) ? $segs[$i] : '', 4, '0', STR_PAD_LEFT);
        }

        $bigint = new Math_BigInteger($hex, 16);
        return $bigint->toString();
    }

    /**
     *
     * @param  string $int
     * @return string
     */
    public function intToIpv6($int)
    {
        if (!@class_exists('Math_BigInteger')) {
            require_once(implode(DS,
                            array('lib', 'phpseclib', 'Math', 'BigInteger.php')));
        }

        $bigint = new Math_BigInteger($int);
        $hex    = str_pad($bigint->toHex(), 32, '0', STR_PAD_LEFT);
        $segs   = str_split($hex, 4);
        for ($i = 0; $i < 8; ++$i) {
            $segs[$i] = ltrim($segs[$i], '0');
        }
        return preg_replace('/\:{3,}$/', '::', implode(':', $segs));
    }

    /**
     *
     * @param  string $ip
     * @return Zkilleman_GeoIP_Model_Country|Zkilleman_GeoIP_Model_Country_Ipv6
     */
    public function getCountry($ip)
    {
        $resourceModel = 'geoip/country_collection';
        if (false !== strpos($ip, '.')) {
            $ip = $this->ipToInt($ip);
        } else if (false !== strpos($ip, ':')) {
            $ip = $this->ipv6ToInt($ip);
            $resourceModel = 'geoip/country_ipv6_collection';
        }

        // Can't do it like this cause MySQL string to decimal cast loses precision
        // $ranges = Mage::getResourceModel($resourceModel)
        //                ->addFieldToFilter('start_ip_int', array('lteq' => $ip))
        //                    ->addFieldToFilter('end_ip_int', array('gteq' => $ip))
        //                        ->setPageSize(1);

        if (!preg_match('/^\d+$/', $ip)) {
            return false;
        }
        $ranges = Mage::getResourceModel($resourceModel)->setPageSize(1);
        $ranges->getSelect()->where(
                    sprintf('start_ip_int <= %s AND end_ip_int >= %s', $ip, $ip));

        return $ranges->getSize() > 0 ? $ranges->getFirstItem() : null;
    }

    /**
     *
     * @return Zkilleman_GeoIP_Model_Country|Zkilleman_GeoIP_Model_Country_Ipv6
     */
    public function getClientCountry()
    {
        return $this->getCountry(Mage::app()->getRequest()->getClientIp());
    }

    /**
     *
     * @return string
     */
    public function getClientCountryCode()
    {
        $countryCode = 'XX';

        if ($range = $this->getClientCountry()) {
            $countryCode = $range->getCountryCode();
        } else {
            // IP lookup missed, fall back on http headers & store default
            $browser = array_keys(
                            Mage::app()->getLocale()->getLocale()->getBrowser());
            $browser[] = Mage::app()->getLocale()->getDefaultLocale();

            foreach ($browser as $locale) {
                $matches = array();
                if (preg_match('/^[a-z]{2}_([A-Z]{2})$/', $locale, $matches)) {
                    $countryCode = $matches[1];
                    break;
                }
            }
        }

        return $countryCode;
    }
}
