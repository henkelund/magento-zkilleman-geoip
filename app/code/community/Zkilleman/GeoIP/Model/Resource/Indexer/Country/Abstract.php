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

abstract class Zkilleman_GeoIP_Model_Resource_Indexer_Country_Abstract
                    extends Mage_Index_Model_Resource_Abstract
{

    /**
     * Internal constructor
     *
     */
    protected function _construct()
    {
        $this->_init('geoip/country', 'range_id');
    }

    /**
     *
     * @return bool
     */
    protected function _prepareDataSource()
    {
        return false;
    }

    /**
     *
     * @return array
     */
    protected abstract function _nextDataRow();

    /**
     *
     * @return Zkilleman_GeoIP_Model_Resource_Indexer_Country_Abstract
     */
    public function reindexAll()
    {
        $this->_getIndexAdapter()->truncateTable($this->getIdxTable());
        if (!$this->_prepareDataSource()) {
            Mage::throwException('Country index preparation failed');
        }
        $i = 0;
        while (false !== ($data = $this->_nextDataRow())) {
            $this->_getIndexAdapter()->insert($this->getIdxTable(), $data);
            ++$i;
        }
        if ($i > 0) {
            $this->syncData();
        }
        $this->_getIndexAdapter()->truncateTable($this->getIdxTable());
        if ($i == 0) {
            Mage::throwException('No IP ranges indexed');
        }
        return $this;
    }
}
