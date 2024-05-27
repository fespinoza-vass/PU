<?php

namespace WolfSellers\Checkout\Setup\Patch\Data;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class UpdateData implements DataPatchInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(
        ResourceConnection $resourceConnection,
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    public function apply()
    {
        $conn  = $this->resourceConnection->getConnection();

        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15123 and city="Ancon" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15047 and city="Barranco" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15082 and city="Breña" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15121 and city="Carabayllo" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15472 and city="Chaclacayo" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15054 and city="Chorrillos" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15593 and city="Cieneguilla" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15003 and city="El Agustino" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15311 and city="Independencia" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15046 and city="Jesus Maria" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15012 and city="La Molina" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15018 and city="La Victoria" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15001 and city="Lima" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15046 and city="Lince" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15720 and city="Los Olivos" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15822 and city="Lurin" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15076 and city="Magdalena del Mar" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15046 and city="Miraflores" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15593 and city="Pachacamac" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15866 and city="Pucusana" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15083 and city="Pueblo Libre" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15113 and city="Puente Piedra" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15846 and city="Punta Hermosa" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15851 and city="Punta Negra" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15093 and city="Rimac" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15856 and city="San Bartolo" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15240 and city="San Borja" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15036 and city="San Isidro" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15700 and city="San Juan" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15401 and city="San Juan de Lurigancho" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15004 and city="San Luis" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15202 and city="San Martin de Porres" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15084 and city="San Miguel" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15861 and city="Santa Maria del Mar" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15123 and city="Santa Rosa" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15023 and city="Santiago de Surco" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15036 and city="Surquillo" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15816 and city="Villa el Salvador" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15803 and city="Villa Maria del Triunfo" and region="Lima"');
        $conn->query('UPDATE inventory_geoname set is_district_lima=1 where postcode = 15004 and city="Vitarte" and region="Lima"');

    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

}
