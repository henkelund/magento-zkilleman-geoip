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

abstract class Zkilleman_GeoIP_Model_System_Config_Backend_Cron_Abstract
                        extends Mage_Core_Model_Config_Data
{

    protected $_enabledValuePath   = null;
    protected $_timeValuePath      = null;
    protected $_frequencyValuePath = null;
    protected $_cronExpressionPath = null;
    protected $_cronModelPath      = null;

    /**
     *
     */
    protected abstract function _preparePaths();

    /**
     *
     * @return boolean
     */
    public function preparePaths()
    {
        $this->_preparePaths();
        if (!$this->_enabledValuePath       ||
                !$this->_timeValuePath      ||
                !$this->_frequencyValuePath ||
                !$this->_cronExpressionPath ||
                !$this->_cronModelPath) {
            return false;
        }
        return true;
    }

    /**
     *
     */
    protected function _afterSave()
    {
        if (!$this->preparePaths()) {
            return;
        }
        $enabled   = $this->getData($this->_enabledValuePath);
        $time      = $this->getData($this->_timeValuePath);
        $frequency = $this->getData($this->_frequencyValuePath);

        $frequencyWeekly  = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_WEEKLY;
        $frequencyMonthly = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_MONTHLY;

        $cronExprString = '';
        if ($enabled) {
            $cronExprArray = array(
                intval($time[1]),                                   # Minute
                intval($time[0]),                                   # Hour
                ($frequency == $frequencyMonthly) ? '1' : '*',      # Day of Month
                '*',                                                # Month of Year
                ($frequency == $frequencyWeekly) ? '1' : '*',       # Day of Week
            );
            $cronExprString = join(' ', $cronExprArray);
        }

        try {
            Mage::getModel('core/config_data')
                ->load($this->_cronExpressionPath, 'path')
                ->setValue($cronExprString)
                ->setPath($this->_cronExpressionPath)
                ->save();

            Mage::getModel('core/config_data')
                ->load($this->_cronModelPath, 'path')
                ->setValue((string) Mage::getConfig()->getNode($this->_cronModelPath))
                ->setPath($this->_cronModelPath)
                ->save();
        }
        catch (Exception $e) {
            Mage::throwException(
                    Mage::helper('geoip')->__('Unable to save import schedule.'));
        }
    }
}
