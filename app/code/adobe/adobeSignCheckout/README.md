# Magento 2 Adobe Sign Checkout Module 


**Magento 2 Adobe Sign Checkout Module**, a Magento 2.0 module for adding a custom checkout page with Adobe Sign

Prerequisite:-
3rd party lib (DOMPDF) is used to convert html to pdf (https://github.com/dompdf/dompdf)

To install dompdf to Magento Instance, 
> composer require dompdf/dompdf
> bin/magento setup:upgrade

To install this module to Magento Instance, 
copy this to <Magento dir>/app/code, then run 
> bin/magento setup:upgrade
> bin/magento cache:clean
> bin/magento cache:flush
