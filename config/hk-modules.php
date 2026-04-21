<?php

use App\Modules\Activities\ActivityModule;
use App\Modules\Blog\BlogModule;
use App\Modules\Bookings\BookingModule;
use App\Modules\Buses\BusModule;
use App\Modules\Cars\CarModule;
use App\Modules\Comments\CommentModule;
use App\Modules\Crm\CrmModule;
use App\Modules\Cruises\CruiseModule;
use App\Modules\Destinations\DestinationModule;
use App\Modules\Flights\FlightModule;
use App\Modules\Hotels\HotelModule;
use App\Modules\Packages\PackageModule;
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
            'enabled' => false,
            'manifest' => TourModule::class,
            'label' => 'Tours & Itineraries',
        ],

        'hotels' => [
            'enabled' => false,
            'manifest' => HotelModule::class,
            'label' => 'Hotels & Rooms',
        ],

        'flights' => [
            'enabled' => false,
            'manifest' => FlightModule::class,
            'label' => 'Flights',
            'provider' => 'stub', // stub | amadeus | duffel
        ],

        'trains' => [
            'enabled' => false,
            'manifest' => TrainModule::class,
            'label' => 'Trains',
            'provider' => 'stub',
        ],

        'buses' => [
            'enabled' => false,
            'manifest' => BusModule::class,
            'label' => 'Buses',
        ],

        'taxi' => [
            'enabled' => false,
            'manifest' => TaxiModule::class,
            'label' => 'Taxi & Transfers',
        ],

        'cars' => [
            'enabled' => false,
            'manifest' => CarModule::class,
            'label' => 'Car Rentals',
        ],

        'cruises' => [
            'enabled' => false,
            'manifest' => CruiseModule::class,
            'label' => 'Cruises',
        ],

        'activities' => [
            'enabled' => false,
            'manifest' => ActivityModule::class,
            'label' => 'Activities & Experiences',
        ],

        'visa' => [
            'enabled' => false,
            'manifest' => VisaModule::class,
            'label' => 'Visa Services',
        ],

        'destinations' => [
            'enabled' => false,
            'manifest' => DestinationModule::class,
            'label' => 'Destinations',
        ],

        'blog' => [
            'enabled' => false,
            'manifest' => BlogModule::class,
            'label' => 'Blog & Travel Guides',
        ],

        'crm' => [
            'enabled' => false,
            'manifest' => CrmModule::class,
            'label' => 'CRM & Leads',
        ],

        'bookings' => [
            'enabled' => false,
            'manifest' => BookingModule::class,
            'label' => 'Bookings',
        ],

        'packages' => [
            'enabled' => false,
            'manifest' => PackageModule::class,
            'label' => 'Travel Packages',
        ],

        'reviews' => [
            'enabled' => false,
            'manifest' => ReviewModule::class,
            'label' => 'Reviews & Ratings',
        ],

        'comments' => [
            'enabled' => false,
            'manifest' => CommentModule::class,
            'label' => 'Comments',
        ],

    ],

];
