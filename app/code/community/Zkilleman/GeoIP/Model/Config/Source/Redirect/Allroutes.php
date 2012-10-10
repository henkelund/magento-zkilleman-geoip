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

class Zkilleman_GeoIP_Model_Config_Source_Redirect_Allroutes
{

    /**
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        $routers = array();
        $routersConfigNode = Mage::getConfig()->getNode('frontend/routers');
        if($routersConfigNode) {
            $routers = $routersConfigNode->children();
        }
        foreach ($routers as $routerConfig) {
            if ($routerConfig->args && $routerConfig->args->frontName) {
                $frontName = (string) $routerConfig->args->frontName;
                $options[] = array('value' => $frontName, 'label' => $frontName);
            }
        }
        return $options;
    }
}
