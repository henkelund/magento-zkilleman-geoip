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

class Zkilleman_GeoIP_Helper_Import extends Mage_Core_Helper_Abstract
{
    const IO_CHUNK_SIZE = 8192;

    /**
     *
     * @return string
     * @throws Exception
     */
    public function getTmpDir()
    {
        $tmpDir = Mage::getBaseDir('var') . DS . 'tmp';
        if (!is_dir($tmpDir)) {
            if (@mkdir($tmpDir, 0777, true)) {
                return $tmpDir;
            } else {
                throw new Exception(sprintf('Unable to create dir "%s"', $tmpDir));
            }
        }
        return $tmpDir;
    }

    /**
     *
     * @param  string $basename
     * @return string
     */
    public function getNewFilename($basename)
    {
        $dir = $this->getTmpDir() . DS;
        $segments = explode('.', $basename);
        for ($i = 2; file_exists($dir . implode('.', $segments)); ++$i) {
            $segments = explode('.', $basename);
            $segments[0] .= '_' . $i;
        }
        return $dir . implode('.', $segments);
    }

    /**
     *
     * @param  string $url
     * @return mixed string|false
     */
    public function download($url)
    {
        $file = '';
        if (strlen($url = trim($url)) == 0 ||
                strlen($file = basename($url)) == 0) {
            return false;
        }

        $file = $this->getNewFilename($file);

        $remoteResource = fopen($url, 'rb');
        if (is_resource($remoteResource)) {
            $localResource = fopen($file, 'wb');
            if (is_resource($localResource)) {
                while(!feof($remoteResource)) {
                    fwrite(
                            $localResource,
                            fread(
                                    $remoteResource,
                                    self::IO_CHUNK_SIZE
                            ),
                            self::IO_CHUNK_SIZE
                    );
                }
                fclose($localResource);
            } else {
                fclose($remoteResource);
                return false;
            }
            fclose($remoteResource);
        } else {
            return false;
        }

        return $file;
    }

    /**
     *
     * @param  string $file
     * @return array
     */
    public function unzip($file)
    {
        $files = array();
        $zip = zip_open($file);
        if (is_resource($zip)) {
            while (false !== ($entry = zip_read($zip))) {
                $newfile = $this->getNewFilename(zip_entry_name($entry));
                $out = fopen($newfile, 'w');
                if (is_resource($out)) {
                    while ($data = zip_entry_read($entry, self::IO_CHUNK_SIZE)) {
                        fwrite($out, $data);
                    }
                    $files[] = $newfile;
                    fclose($out);
                }
            }
            zip_close($zip);
        }
        return $files;
    }
}
