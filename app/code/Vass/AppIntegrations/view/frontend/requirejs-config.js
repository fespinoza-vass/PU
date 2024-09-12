/**
 * @copyright Copyright (c) 2024 Vass
 * @package Vass_AppIntegrations
 * @author Vass Team
 */

var config = {
    map: {
        '*' :{
            'events-app': 'Vass_AppIntegrations/js/events-app'
        }
    },
    shim:{
        'slickCarousel':{
            deps:['jquery']
        }
    }
};
