<?php

namespace WolfSellers\Bopis\Block\Address;

use Magento\Directory\Block\Data;

class Grid extends Data {
    /**
     * @param $address
     * @return string
     */
    public function getStreetAddress($address): string {
        $street = $address->getStreet();

        if (is_array($street)) {
            $street = implode(', ', $street);
        }

        return $street;
    }
}
