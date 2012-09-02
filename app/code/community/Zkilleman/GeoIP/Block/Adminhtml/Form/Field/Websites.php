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

class Zkilleman_GeoIP_Block_Adminhtml_Form_Field_Websites
            extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    /**
     *
     * @var Mage_Core_Model_Resource_Website_Collection
     */
    protected $_websites = null;

    /**
     *
     * @return Mage_Core_Model_Resource_Website_Collection
     */
    protected function _getWebsites()
    {
        if (!$this->_websites) {
            $this->_websites = Mage::getResourceModel('core/website_collection');
            if ($current = $this->getRequest()->getParam('website')) {
                $this->_websites->addFieldToFilter('code', array('neq' => $current));
            }
            $this->_websites->load();
        }
        return $this->_websites;
    }

    /**
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $element->setValues($this->_getWebsites()->toOptionArray());
        return $element->getElementHtml();
    }

    /**
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        if ($this->_getWebsites()->getSize() == 0) {
            return $this->_decorateRowHtml(
                        $element,
                        sprintf(
                                '<td><span id="%s"></span></td>',
                                $element->getHtmlId()
                                )
                        );
        }
        return parent::render($element);
    }
}
