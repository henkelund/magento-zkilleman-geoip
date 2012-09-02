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

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

/**
 * Create table 'geoip/country'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('geoip/country_ipv6'))
    ->addColumn('range_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Range Id')
    ->addColumn('start_ip', Varien_Db_Ddl_Table::TYPE_TEXT, 39, array(
        'nullable'  => false
        ), 'Start IP')
    ->addColumn('end_ip', Varien_Db_Ddl_Table::TYPE_TEXT, 39, array(
        'nullable'  => false
        ), 'End IP')
    ->addColumn('start_ip_int', Varien_Db_Ddl_Table::TYPE_DECIMAL, '39,0', array(
        'unsigned'  => true
        ), 'Start IP Integer')
    ->addColumn('end_ip_int', Varien_Db_Ddl_Table::TYPE_DECIMAL, '39,0', array(
        'unsigned'  => true
        ), 'End IP Integer')
    ->addColumn('country_code', Varien_Db_Ddl_Table::TYPE_CHAR, 2, array(
        'nullable'  => false
        ), 'Country Code')
    ->addColumn('country_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false
        ), 'Country Name')
    ->addIndex($installer->getIdxName(
            'geoip/country', array('start_ip_int', 'end_ip_int')),
            array('start_ip_int', 'end_ip_int'))
    ->addIndex($installer->getIdxName(
            'geoip/country', array('country_code')),
            array('country_code'))
    ->setComment('GeoIP Country IPv6 Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'geoip/country_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('geoip/country_ipv6_tmp'))
    ->addColumn('range_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Range Id')
    ->addColumn('start_ip', Varien_Db_Ddl_Table::TYPE_TEXT, 39, array(
        'nullable'  => false
        ), 'Start IP')
    ->addColumn('end_ip', Varien_Db_Ddl_Table::TYPE_TEXT, 39, array(
        'nullable'  => false
        ), 'End IP')
    ->addColumn('start_ip_int', Varien_Db_Ddl_Table::TYPE_DECIMAL, '39,0', array(
        'unsigned'  => true
        ), 'Start IP Integer')
    ->addColumn('end_ip_int', Varien_Db_Ddl_Table::TYPE_DECIMAL, '39,0', array(
        'unsigned'  => true
        ), 'End IP Integer')
    ->addColumn('country_code', Varien_Db_Ddl_Table::TYPE_CHAR, 2, array(
        'nullable'  => false
        ), 'Country Code')
    ->addColumn('country_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false
        ), 'Country Name')
    ->setComment('GeoIP Country IPv6 Tmp Table');
$installer->getConnection()->createTable($table);

$installer->endSetup();
