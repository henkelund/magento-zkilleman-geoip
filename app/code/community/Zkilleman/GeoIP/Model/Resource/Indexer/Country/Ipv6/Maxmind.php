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

class Zkilleman_GeoIP_Model_Resource_Indexer_Country_Ipv6_Maxmind
            extends Zkilleman_GeoIP_Model_Resource_Indexer_Country_Ipv6_Abstract
{

    const XML_PATH_COUNTRY_IPV6_SOURCE_URL =
                                    'global/geoip/maxmind/country_ipv6/gzip_url';

    /**
     *
     * @var resource
     */
    protected $_csv     = null;

    /**
     *
     * @var string
     */
    protected $_csvFile = null;

    /**
     *
     * @var array
     */
    protected $_columns = array(
                                    'start_ip',     'end_ip',
                                    'start_ip_int', 'end_ip_int',
                                    'country_code', 'country_name'
                                );

    /**
     *
     * @return string
     */
    protected function _getUrl()
    {
        $config = Mage::getConfig()->getNode(self::XML_PATH_COUNTRY_IPV6_SOURCE_URL);
        /* @var $config Mage_Core_Model_Config_Element */
        return (string) $config;
    }

    /**
     *
     * @return bool
     */
    protected function _prepareDataSource()
    {
        $helper = Mage::helper('geoip/import');
        $gzip   = $helper->download($this->_getUrl());
        if ($gzip) {
            $file = Mage::helper('geoip/import')->gunzip($gzip);
            if ($file) {
                $this->_csv = fopen($this->_csvFile = $file, 'r');
            }
            @unlink($gzip);
        }
        return is_resource($this->_csv);
    }

    /**
     *
     */
    protected function _cleanup()
    {
        if (is_resource($this->_csv)) {
            fclose($this->_csv);
        }
        if ($this->_csvFile) {
            @unlink($this->_csvFile);
        }
    }

    /**
     *
     * @return mixed array|false
     */
    protected function _nextDataRow()
    {
        if (is_resource($this->_csv)) {
            $row = fgetcsv($this->_csv);
            if ($row && count($row) == count($this->_columns)) {
                return array_combine($this->_columns, $row);
            }
            $this->_cleanup();
        }
        return false;
    }
}
