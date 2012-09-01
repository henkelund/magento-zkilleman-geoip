Zkilleman_GeoIP
===============

GeoIP module for Magento

```php
// Get country for current client
$countryRange = Mage::helper('geoip')->getClientCountry();
/* @var $countryRange Zkilleman_GeoIP_Model_Country */
if ($countryRange) {
    echo sprintf(
                '%s (%s)',
                $countryRange->getCountryName(),
                $countryRange->getCountryCode()
            );
}
```

```php
// Get country for specific IP
$countryRange = Mage::helper('geoip')->getCountry('207.97.227.239');
/* @var $countryRange Zkilleman_GeoIP_Model_Country */
if ($countryRange) {
    echo sprintf(
                '%s (%s)',
                $countryRange->getCountryName(),
                $countryRange->getCountryCode()
            );
}
```