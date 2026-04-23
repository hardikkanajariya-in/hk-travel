<?php

use App\Modules\Activities\ActivityModule;
use App\Modules\Blog\BlogModule;
use App\Modules\Buses\BusModule;
use App\Modules\Cars\CarModule;
use App\Modules\Comments\CommentModule;
use App\Modules\Crm\CrmModule;
use App\Modules\Cruises\CruiseModule;
use App\Modules\Destinations\DestinationModule;
use App\Modules\Flights\FlightModule;
use App\Modules\Hotels\HotelModule;
use App\Modules\Reviews\ReviewModule;
use App\Modules\Taxi\TaxiModule;
use App\Modules\Tours\TourModule;
use App\Modules\Trains\TrainModule;
use App\Modules\Visa\VisaModule;

/*
 * HK Travel module registry.
 *
 * Each module declares an enabled flag, its manifest class, and an optional
 * provider list. Disabled modules are skipped by ModuleManager and are
 * therefore invisible across routes, menus, search, and sitemap.
 *
 * To toggle a module, prefer the admin UI; this file holds defaults for
 * a fresh install and acts as the discovery list.
 */

return [

    'modules' => [

        'tours' => [
            'enabled' => true,
            'manifest' => TourModule::class,
            'label' => 'Tours & Itineraries',
        ],

        'hotels' => [
            'enabled' => true,
            'manifest' => HotelModule::class,
            'label' => 'Hotels & Rooms',
        ],

        'flights' => [
            'enabled' => true,
            'manifest' => FlightModule::class,
            'label' => 'Flights',
            'provider' => 'stub', // stub | amadeus | duffel
            'amadeus' => [
                'api_key' => env('AMADEUS_API_KEY'),
                'api_secret' => env('AMADEUS_API_SECRET'),
                'base_url' => env('AMADEUS_BASE_URL', 'https://test.api.amadeus.com'),
            ],
            'duffel' => [
                'access_token' => env('DUFFEL_ACCESS_TOKEN'),
                'base_url' => env('DUFFEL_BASE_URL', 'https://api.duffel.com'),
            ],
        ],

        'trains' => [
            'enabled' => true,
            'manifest' => TrainModule::class,
            'label' => 'Trains',
            'provider' => 'stub',
            'sabre' => [
                'client_id' => env('SABRE_RAIL_CLIENT_ID'),
                'client_secret' => env('SABRE_RAIL_CLIENT_SECRET'),
                'base_url' => env('SABRE_RAIL_BASE_URL', 'https://api.cert.platform.sabre.com'),
            ],
            'trainline' => [
                'api_key' => env('TRAINLINE_API_KEY'),
                'base_url' => env('TRAINLINE_BASE_URL', 'https://partner-api.thetrainline.com'),
            ],
        ],

        'buses' => [
            'enabled' => true,
            'manifest' => BusModule::class,
            'label' => 'Buses',
        ],

        'taxi' => [
            'enabled' => true,
            'manifest' => TaxiModule::class,
            'label' => 'Taxi & Transfers',
        ],

        'cars' => [
            'enabled' => true,
            'manifest' => CarModule::class,
            'label' => 'Car Rentals',
        ],

        'cruises' => [
            'enabled' => true,
            'manifest' => CruiseModule::class,
            'label' => 'Cruises',
        ],

        'activities' => [
            'enabled' => true,
            'manifest' => ActivityModule::class,
            'label' => 'Activities & Experiences',
        ],

        'visa' => [
            'enabled' => true,
            'manifest' => VisaModule::class,
            'label' => 'Visa Services',
        ],

        'destinations' => [
            'enabled' => true,
            'manifest' => DestinationModule::class,
            'label' => 'Destinations',
        ],

        'blog' => [
            'enabled' => true,
            'manifest' => BlogModule::class,
            'label' => 'Blog & Travel Guides',
        ],

        'crm' => [
            'enabled' => true,
            'manifest' => CrmModule::class,
            'label' => 'CRM & Leads',
        ],

        'reviews' => [
            'enabled' => true,
            'manifest' => ReviewModule::class,
            'label' => 'Reviews & Ratings',
        ],

        'comments' => [
            'enabled' => true,
            'manifest' => CommentModule::class,
            'label' => 'Comments',
        ],

    ],

];
