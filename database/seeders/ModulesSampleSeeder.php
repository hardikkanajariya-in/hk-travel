<?php

namespace Database\Seeders;

use App\Modules\Activities\Models\Activity;
use App\Modules\Buses\Models\BusRoute;
use App\Modules\Cars\Models\CarRental;
use App\Modules\Cruises\Models\Cruise;
use App\Modules\Destinations\Models\Destination;
use App\Modules\Hotels\Models\Hotel;
use App\Modules\Hotels\Models\Room;
use App\Modules\Taxi\Models\TaxiService;
use App\Modules\Tours\Models\Tour;
use App\Modules\Visa\Models\VisaService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Seeds realistic sample content for every public module:
 * destinations, tours, hotels (with rooms), activities, cruises,
 * car rentals, taxi services, bus routes and visa services.
 *
 * Each block is guarded with Schema::hasTable() so the seeder is
 * safe to run even when some modules are disabled or not migrated.
 * Existing rows are preserved — the seeder only inserts when its
 * target table is empty for that module.
 */
class ModulesSampleSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedDestinations();
        $this->seedTours();
        $this->seedHotels();
        $this->seedActivities();
        $this->seedCruises();
        $this->seedCarRentals();
        $this->seedTaxiServices();
        $this->seedBusRoutes();
        $this->seedVisaServices();
    }

    protected function seedDestinations(): void
    {
        if (! Schema::hasTable('destinations') || Destination::query()->exists()) {
            return;
        }

        $countries = [
            ['name' => 'India', 'code' => 'IN', 'description' => 'A vibrant subcontinent of contrasts — from the Himalayas to tropical beaches, ancient temples to modern megacities.'],
            ['name' => 'Thailand', 'code' => 'TH', 'description' => 'Famed for its tropical beaches, opulent royal palaces, ancient ruins and ornate temples.'],
            ['name' => 'United Arab Emirates', 'code' => 'AE', 'description' => 'A modern marvel of skyscrapers, desert adventures and luxury shopping in the Arabian Gulf.'],
            ['name' => 'Indonesia', 'code' => 'ID', 'description' => 'A sweeping archipelago of volcanic islands, surf breaks, and ancient cultures.'],
            ['name' => 'Singapore', 'code' => 'SG', 'description' => 'A clean, green city-state where futuristic skyline meets hawker food culture.'],
            ['name' => 'Maldives', 'code' => 'MV', 'description' => 'Picture-perfect atolls of turquoise lagoons, white sand and overwater villas.'],
        ];

        $countryRecords = [];
        foreach ($countries as $i => $country) {
            $countryRecords[$country['code']] = Destination::create([
                'type' => 'country',
                'name' => $country['name'],
                'slug' => Str::slug($country['name']),
                'country_code' => $country['code'],
                'description' => $country['description'],
                'cover_image' => "https://picsum.photos/seed/country-{$country['code']}/1200/700",
                'is_featured' => $i < 3,
                'is_published' => true,
            ]);
        }

        $cities = [
            ['name' => 'Mumbai', 'parent' => 'IN', 'desc' => 'Bollywood, colonial architecture, and the energy of India\'s financial capital.', 'lat' => 19.0760, 'lng' => 72.8777, 'featured' => true],
            ['name' => 'Goa', 'parent' => 'IN', 'desc' => 'Sun-drenched beaches, Portuguese heritage and lively beach shacks.', 'lat' => 15.2993, 'lng' => 74.1240, 'featured' => true],
            ['name' => 'Jaipur', 'parent' => 'IN', 'desc' => 'The Pink City — palaces, forts and the gateway to Rajasthan.', 'lat' => 26.9124, 'lng' => 75.7873, 'featured' => true],
            ['name' => 'Kerala Backwaters', 'parent' => 'IN', 'desc' => 'Cruise tranquil canals on a traditional houseboat through emerald palms.', 'lat' => 9.4981, 'lng' => 76.3388, 'featured' => false],
            ['name' => 'Bangkok', 'parent' => 'TH', 'desc' => 'Glittering temples, street food and a frenetic, friendly metropolis.', 'lat' => 13.7563, 'lng' => 100.5018, 'featured' => true],
            ['name' => 'Phuket', 'parent' => 'TH', 'desc' => 'Thailand\'s largest island — beaches, viewpoints and island-hopping.', 'lat' => 7.8804, 'lng' => 98.3923, 'featured' => false],
            ['name' => 'Chiang Mai', 'parent' => 'TH', 'desc' => 'Mountain temples, night markets and Lanna culture in the cool north.', 'lat' => 18.7883, 'lng' => 98.9853, 'featured' => false],
            ['name' => 'Dubai', 'parent' => 'AE', 'desc' => 'Record-breaking skyline, luxury malls and golden desert sunsets.', 'lat' => 25.2048, 'lng' => 55.2708, 'featured' => true],
            ['name' => 'Abu Dhabi', 'parent' => 'AE', 'desc' => 'The capital — home of the Sheikh Zayed Mosque and Yas Island circuits.', 'lat' => 24.4539, 'lng' => 54.3773, 'featured' => false],
            ['name' => 'Bali', 'parent' => 'ID', 'desc' => 'Rice terraces, surf coast, and Hindu temples on the Island of the Gods.', 'lat' => -8.4095, 'lng' => 115.1889, 'featured' => true],
            ['name' => 'Singapore City', 'parent' => 'SG', 'desc' => 'Gardens by the Bay, Marina sands and Michelin-starred hawker stalls.', 'lat' => 1.3521, 'lng' => 103.8198, 'featured' => true],
            ['name' => 'Malé', 'parent' => 'MV', 'desc' => 'The compact capital and gateway to the Maldives\' atolls.', 'lat' => 4.1755, 'lng' => 73.5093, 'featured' => false],
        ];

        foreach ($cities as $city) {
            Destination::create([
                'parent_id' => $countryRecords[$city['parent']]->id,
                'type' => 'city',
                'name' => $city['name'],
                'slug' => Str::slug($city['name']),
                'country_code' => $city['parent'],
                'description' => $city['desc'],
                'cover_image' => 'https://picsum.photos/seed/city-'.Str::slug($city['name']).'/1200/700',
                'lat' => $city['lat'],
                'lng' => $city['lng'],
                'is_featured' => $city['featured'],
                'is_published' => true,
            ]);
        }

        $this->command?->info('Destinations: '.Destination::count().' rows seeded.');
    }

    protected function seedTours(): void
    {
        if (! Schema::hasTable('tours') || Tour::query()->exists()) {
            return;
        }

        $tours = [
            ['name' => 'Golden Triangle Express', 'destination' => 'Jaipur', 'days' => 6, 'price' => 549, 'difficulty' => 'easy', 'featured' => true,
                'desc' => 'Delhi, Agra and Jaipur in six unforgettable days — Taj Mahal at sunrise, Amber Fort by elephant, and street-food walks through Old Delhi.',
                'inclusions' => ['Air-conditioned private vehicle', 'English-speaking guide', '5 nights 4-star accommodation', 'Daily breakfast', 'Monument entrance fees'],
                'exclusions' => ['International flights', 'Lunches and dinners', 'Travel insurance', 'Tips and gratuities']],
            ['name' => 'Kerala Backwaters & Beaches', 'destination' => 'Kerala Backwaters', 'days' => 8, 'price' => 729, 'difficulty' => 'easy', 'featured' => true,
                'desc' => 'Houseboat through Alleppey\'s palm-fringed canals, sip chai in Munnar\'s tea estates and finish with sunsets on Varkala\'s cliffs.',
                'inclusions' => ['1 night private houseboat with all meals', '6 nights boutique hotels', 'Airport transfers', 'Tea plantation tour', 'Ayurvedic massage session'],
                'exclusions' => ['Flights', 'Personal expenses', 'Optional excursions']],
            ['name' => 'Goa Beach Escape', 'destination' => 'Goa', 'days' => 4, 'price' => 299, 'difficulty' => 'easy', 'featured' => false,
                'desc' => 'Long lazy beach days, sunset cruises on the Mandovi and a taste of Portuguese-Indian heritage in Old Goa.',
                'inclusions' => ['3 nights beachfront resort', 'Daily breakfast', 'Sunset river cruise', 'Old Goa half-day tour'],
                'exclusions' => ['Flights', 'Lunches and dinners', 'Watersports']],
            ['name' => 'Bangkok to Phuket Discovery', 'destination' => 'Bangkok', 'days' => 9, 'price' => 999, 'difficulty' => 'easy', 'featured' => true,
                'desc' => 'Temples and tuk-tuks in Bangkok, ancient ruins in Ayutthaya, then south to Phuket for island-hopping in turquoise seas.',
                'inclusions' => ['8 nights hotels (4-star)', 'Internal flight Bangkok–Phuket', 'Phi Phi day trip with lunch', 'Floating-market tour', 'All transfers'],
                'exclusions' => ['International flights', 'Visa fees', 'Most meals']],
            ['name' => 'Chiang Mai Hill-Tribe Trek', 'destination' => 'Chiang Mai', 'days' => 5, 'price' => 459, 'difficulty' => 'moderate', 'featured' => false,
                'desc' => 'Two-day guided trek through northern Thai jungle to a Karen hill-tribe homestay, plus an ethical elephant sanctuary visit.',
                'inclusions' => ['2 nights trek with homestay', '2 nights Chiang Mai boutique hotel', 'Elephant sanctuary visit', 'All trek meals', 'Local guide'],
                'exclusions' => ['Flights to Chiang Mai', 'Travel insurance', 'Personal trekking gear']],
            ['name' => 'Dubai City & Desert', 'destination' => 'Dubai', 'days' => 4, 'price' => 689, 'difficulty' => 'easy', 'featured' => true,
                'desc' => 'Burj Khalifa observation deck, dhow dinner cruise and an evening 4x4 desert safari with dune-bashing and Bedouin barbecue.',
                'inclusions' => ['3 nights central Dubai hotel', 'Burj Khalifa “At the Top” ticket', 'Desert safari with dinner', 'Marina dhow cruise', 'Private airport transfers'],
                'exclusions' => ['Flights', 'Visa', 'Lunches']],
            ['name' => 'Bali Cultural Journey', 'destination' => 'Bali', 'days' => 7, 'price' => 819, 'difficulty' => 'easy', 'featured' => false,
                'desc' => 'Ubud rice terraces, water-temple ceremonies, a sunrise hike up Mount Batur and beach days on the Bukit peninsula.',
                'inclusions' => ['6 nights mixed villa & resort', 'Daily breakfast', 'Mount Batur sunrise trek', 'Ubud cooking class', 'All transfers'],
                'exclusions' => ['International flights', 'Lunches and dinners', 'Spa treatments']],
            ['name' => 'Maldives Honeymoon Retreat', 'destination' => 'Malé', 'days' => 5, 'price' => 1899, 'difficulty' => 'easy', 'featured' => true,
                'desc' => 'Four nights in an overwater villa, sunset dolphin cruise, couple\'s spa and a private sandbank picnic — all-inclusive.',
                'inclusions' => ['4 nights overwater villa', 'All meals and selected drinks', 'Seaplane transfers', 'Sunset cruise', 'Couple spa treatment'],
                'exclusions' => ['International flights', 'Premium spirits', 'Diving certification']],
        ];

        foreach ($tours as $t) {
            $destination = Destination::query()->where('name', $t['destination'])->first();
            $price = $t['price'];
            $discount = $t['featured'] ? round($price * 0.9, 2) : null;

            Tour::create([
                'destination_id' => $destination?->id,
                'name' => $t['name'],
                'slug' => Str::slug($t['name']),
                'description' => $t['desc'],
                'cover_image' => 'https://picsum.photos/seed/tour-'.Str::slug($t['name']).'/1200/800',
                'gallery' => [
                    'https://picsum.photos/seed/tour-'.Str::slug($t['name']).'-1/1200/800',
                    'https://picsum.photos/seed/tour-'.Str::slug($t['name']).'-2/1200/800',
                    'https://picsum.photos/seed/tour-'.Str::slug($t['name']).'-3/1200/800',
                ],
                'inclusions' => $t['inclusions'],
                'exclusions' => $t['exclusions'],
                'itinerary' => collect(range(1, $t['days']))->map(fn ($d) => [
                    'day' => $d,
                    'title' => "Day {$d}",
                    'description' => "Detailed day {$d} activities and overnight stay.",
                ])->all(),
                'difficulty' => $t['difficulty'],
                'language' => 'en',
                'price' => $price,
                'discount_price' => $discount,
                'currency' => 'USD',
                'duration_days' => $t['days'],
                'max_group_size' => 16,
                'is_published' => true,
                'is_featured' => $t['featured'],
                'published_at' => now()->subDays(rand(1, 60)),
                'rating_avg' => round(rand(40, 49) / 10, 1),
                'rating_count' => rand(8, 240),
            ]);
        }

        $this->command?->info('Tours: '.Tour::count().' rows seeded.');
    }

    protected function seedHotels(): void
    {
        if (! Schema::hasTable('hotels') || Hotel::query()->exists()) {
            return;
        }

        $hotels = [
            ['name' => 'The Taj Mahal Palace', 'city' => 'Mumbai', 'stars' => 5, 'price' => 320, 'desc' => 'Iconic Mumbai landmark facing the Gateway of India, blending heritage charm with five-star service.', 'amenities' => ['Free Wi-Fi', 'Outdoor pool', 'Spa', 'Fitness centre', 'Concierge', 'Airport shuttle'], 'check_in' => '14:00', 'check_out' => '12:00', 'featured' => true],
            ['name' => 'Leela Goa Beach Resort', 'city' => 'Goa', 'stars' => 5, 'price' => 280, 'desc' => 'A beachfront sanctuary with lagoon-view rooms, three pools and an award-winning Ayurvedic spa.', 'amenities' => ['Private beach', 'Spa', 'Three pools', 'Watersports', 'Kids club'], 'check_in' => '15:00', 'check_out' => '11:00', 'featured' => true],
            ['name' => 'Hotel Mandawa Haveli', 'city' => 'Jaipur', 'stars' => 4, 'price' => 110, 'desc' => 'A restored 19th-century haveli in the old Pink City — courtyard dining, traditional frescoes and turbaned doormen.', 'amenities' => ['Heritage building', 'Rooftop restaurant', 'Free Wi-Fi', 'Airport pickup'], 'check_in' => '14:00', 'check_out' => '12:00', 'featured' => false],
            ['name' => 'Coconut Lagoon Houseboat Resort', 'city' => 'Kerala Backwaters', 'stars' => 4, 'price' => 195, 'desc' => 'CGH Earth eco-resort accessible only by boat — palm-thatched cottages on a sleepy backwater island.', 'amenities' => ['Backwater access', 'Yoga pavilion', 'Restaurant', 'Eco-friendly'], 'check_in' => '14:00', 'check_out' => '11:00', 'featured' => false],
            ['name' => 'Mandarin Oriental Bangkok', 'city' => 'Bangkok', 'stars' => 5, 'price' => 410, 'desc' => 'Riverside legend on the Chao Phraya — destination dining, the famed Authors\' Lounge and a renowned spa.', 'amenities' => ['Riverside location', 'Spa', 'Fine dining', 'Cooking school', 'Pool'], 'check_in' => '15:00', 'check_out' => '12:00', 'featured' => true],
            ['name' => 'Patong Beach Hotel', 'city' => 'Phuket', 'stars' => 4, 'price' => 95, 'desc' => 'Energetic beachfront hotel steps from Bangla Road — two pools, swim-up bar and easy island-tour bookings.', 'amenities' => ['Pool', 'Beachfront', 'Bar', 'Tour desk'], 'check_in' => '14:00', 'check_out' => '12:00', 'featured' => false],
            ['name' => 'Anantara Chiang Mai Resort', 'city' => 'Chiang Mai', 'stars' => 5, 'price' => 240, 'desc' => 'Riverside calm on the Mae Ping with a Lanna-inspired spa and rooftop afternoon tea.', 'amenities' => ['Riverfront', 'Spa', 'Pool', 'Cooking school', 'Bicycle hire'], 'check_in' => '14:00', 'check_out' => '12:00', 'featured' => false],
            ['name' => 'Burj Al Arab Jumeirah', 'city' => 'Dubai', 'stars' => 5, 'price' => 1450, 'desc' => 'The sail-shaped icon of Dubai — duplex suites, private butlers, helipad arrivals and gold-plated everything.', 'amenities' => ['Private beach', 'Butler service', 'Helipad', 'Spa', 'Underwater restaurant'], 'check_in' => '15:00', 'check_out' => '12:00', 'featured' => true],
            ['name' => 'Rove Downtown Dubai', 'city' => 'Dubai', 'stars' => 3, 'price' => 95, 'desc' => 'Smart, design-led budget hotel a five-minute walk from Burj Khalifa and the Dubai Mall.', 'amenities' => ['Pool', 'Gym', 'Free Wi-Fi', 'Pet-friendly'], 'check_in' => '14:00', 'check_out' => '12:00', 'featured' => false],
            ['name' => 'Four Seasons Resort Bali at Sayan', 'city' => 'Bali', 'stars' => 5, 'price' => 720, 'desc' => 'Jungle-shrouded villas above the Ayung River — Ubud\'s most romantic riverside retreat.', 'amenities' => ['Private pool villas', 'Spa', 'Yoga', 'Riverside dining'], 'check_in' => '15:00', 'check_out' => '12:00', 'featured' => true],
            ['name' => 'Marina Bay Sands', 'city' => 'Singapore City', 'stars' => 5, 'price' => 480, 'desc' => 'Three towers crowned by the world-famous infinity pool overlooking the Singapore skyline.', 'amenities' => ['Infinity pool', 'Casino', 'Designer shopping', 'Spa', 'Multiple restaurants'], 'check_in' => '15:00', 'check_out' => '11:00', 'featured' => true],
            ['name' => 'Soneva Jani', 'city' => 'Malé', 'stars' => 5, 'price' => 2400, 'desc' => 'Overwater villas with retractable roofs for stargazing, water slides into the lagoon and an over-water observatory.', 'amenities' => ['Overwater villas', 'Private pool', 'Observatory', 'Cinema paradiso', 'Three restaurants'], 'check_in' => '14:00', 'check_out' => '12:00', 'featured' => true],
        ];

        foreach ($hotels as $h) {
            $destination = Destination::query()->where('name', $h['city'])->first();
            $hotel = Hotel::create([
                'destination_id' => $destination?->id,
                'name' => $h['name'],
                'slug' => Str::slug($h['name']),
                'star_rating' => $h['stars'],
                'description' => $h['desc'],
                'cover_image' => 'https://picsum.photos/seed/hotel-'.Str::slug($h['name']).'/1200/800',
                'gallery' => [
                    'https://picsum.photos/seed/hotel-'.Str::slug($h['name']).'-1/1200/800',
                    'https://picsum.photos/seed/hotel-'.Str::slug($h['name']).'-2/1200/800',
                ],
                'amenities' => $h['amenities'],
                'address' => $h['city'],
                'lat' => $destination?->lat,
                'lng' => $destination?->lng,
                'check_in' => $h['check_in'],
                'check_out' => $h['check_out'],
                'price_from' => $h['price'],
                'currency' => 'USD',
                'is_published' => true,
                'is_featured' => $h['featured'],
                'rating_avg' => round(rand(40, 49) / 10, 1),
                'rating_count' => rand(20, 800),
            ]);

            if (Schema::hasTable('hotel_rooms')) {
                Room::create([
                    'hotel_id' => $hotel->id,
                    'name' => 'Deluxe Room',
                    'description' => 'Queen bed, city view, marble bathroom and complimentary breakfast.',
                    'price_per_night' => $h['price'],
                    'capacity_adults' => 2,
                    'capacity_children' => 1,
                    'inventory' => 12,
                    'is_available' => true,
                    'amenities' => ['Air conditioning', 'TV', 'Mini bar', 'Bath tub'],
                ]);
                Room::create([
                    'hotel_id' => $hotel->id,
                    'name' => 'Executive Suite',
                    'description' => 'Separate living room, premium views and lounge access.',
                    'price_per_night' => round($h['price'] * 1.6, 2),
                    'capacity_adults' => 2,
                    'capacity_children' => 2,
                    'inventory' => 4,
                    'is_available' => true,
                    'amenities' => ['Lounge access', 'Espresso machine', 'Bath tub', 'Workspace'],
                ]);
            }
        }

        $this->command?->info('Hotels: '.Hotel::count().' rows seeded.');
    }

    protected function seedActivities(): void
    {
        if (! Schema::hasTable('activities') || Activity::query()->exists()) {
            return;
        }

        $items = [
            ['name' => 'Taj Mahal Sunrise Tour', 'city' => 'Jaipur', 'category' => 'sightseeing', 'duration' => 5, 'price' => 79, 'difficulty' => 'easy', 'featured' => true,
                'short' => 'Beat the crowds with a private sunrise visit to the Taj Mahal.',
                'long' => 'Pre-dawn pickup, fast-track entry, two-hour photo walk with an expert guide, and breakfast on a terrace overlooking the marble dome.'],
            ['name' => 'Mumbai Street-Food Walk', 'city' => 'Mumbai', 'category' => 'food', 'duration' => 3, 'price' => 45, 'difficulty' => 'easy', 'featured' => false,
                'short' => 'Eat your way through Mohammed Ali Road with a local foodie.',
                'long' => 'Eight tasting stops across pav-bhaji, vada-pav, kebabs, kulfi and chai, with story-rich commentary on Mumbai\'s food culture.'],
            ['name' => 'Backwater Houseboat Day Cruise', 'city' => 'Kerala Backwaters', 'category' => 'cruise', 'duration' => 7, 'price' => 119, 'difficulty' => 'easy', 'featured' => true,
                'short' => 'Lunch and lazy hours aboard a private rice-barge houseboat.',
                'long' => 'Glide through palm-fringed canals, swap boats for a village walk, and enjoy a fresh Keralan thali served on banana leaf onboard.'],
            ['name' => 'Bangkok Long-Tail Klong Tour', 'city' => 'Bangkok', 'category' => 'sightseeing', 'duration' => 4, 'price' => 55, 'difficulty' => 'easy', 'featured' => false,
                'short' => 'Zip through Bangkok\'s hidden canals on a private long-tail boat.',
                'long' => 'Visit Wat Arun, the flower market and stilted wooden homes most tourists never see — finish at a riverside lunch spot.'],
            ['name' => 'Phi Phi Islands Speedboat Day', 'city' => 'Phuket', 'category' => 'water', 'duration' => 9, 'price' => 89, 'difficulty' => 'easy', 'featured' => true,
                'short' => 'Snorkel Maya Bay, Bamboo Island and Monkey Beach in one day.',
                'long' => 'Six island stops, snorkel gear, buffet lunch on Phi Phi Don, and a Maya Bay visit before the day-tripper crowds arrive.'],
            ['name' => 'Elephant Nature Park Visit', 'city' => 'Chiang Mai', 'category' => 'wildlife', 'duration' => 8, 'price' => 99, 'difficulty' => 'easy', 'featured' => false,
                'short' => 'A full day at an ethical elephant rescue sanctuary.',
                'long' => 'Feed and walk alongside rescued elephants, learn their stories, enjoy a vegetarian Thai lunch, and finish with a river bath (in the dry season).'],
            ['name' => 'Desert Safari with BBQ Dinner', 'city' => 'Dubai', 'category' => 'adventure', 'duration' => 6, 'price' => 75, 'difficulty' => 'moderate', 'featured' => true,
                'short' => 'Dune-bashing, sandboarding and a Bedouin-style dinner under the stars.',
                'long' => '4x4 ride into the Lahbab desert, camel ride, henna painting, belly-dancing show and an open-fire BBQ with vegetarian options.'],
            ['name' => 'Burj Khalifa “At The Top” Skip-the-Line', 'city' => 'Dubai', 'category' => 'sightseeing', 'duration' => 2, 'price' => 65, 'difficulty' => 'easy', 'featured' => false,
                'short' => 'Sunset visit to levels 124 and 125 of the world\'s tallest building.',
                'long' => 'Pre-booked sunset slot, fast-track entry, outdoor observation terrace and panoramic views of Dubai Marina.'],
            ['name' => 'Ubud Rice Terraces & Temples Tour', 'city' => 'Bali', 'category' => 'culture', 'duration' => 8, 'price' => 69, 'difficulty' => 'easy', 'featured' => true,
                'short' => 'A full day exploring Tegallalang, Tirta Empul and the Sacred Monkey Forest.',
                'long' => 'Air-conditioned vehicle, English-speaking guide, sarong rental, traditional Balinese lunch and a stop at a coffee plantation.'],
            ['name' => 'Mount Batur Sunrise Trek', 'city' => 'Bali', 'category' => 'adventure', 'duration' => 9, 'price' => 95, 'difficulty' => 'challenging', 'featured' => false,
                'short' => 'Pre-dawn climb of an active volcano for an unforgettable sunrise.',
                'long' => 'Hotel pickup at 02:00, two-hour ascent with a local guide, breakfast at the summit, descent through lava fields and optional hot-spring stop.'],
            ['name' => 'Singapore Night Safari', 'city' => 'Singapore City', 'category' => 'wildlife', 'duration' => 4, 'price' => 58, 'difficulty' => 'easy', 'featured' => false,
                'short' => 'See nocturnal wildlife on a tram ride through the world\'s first night zoo.',
                'long' => 'Tram tour with live commentary, four walking trails, the Creatures of the Night show and complimentary hotel transfers from selected hotels.'],
            ['name' => 'Maldives Sunset Dolphin Cruise', 'city' => 'Malé', 'category' => 'water', 'duration' => 2, 'price' => 89, 'difficulty' => 'easy', 'featured' => true,
                'short' => 'Sunset dhoni cruise with near-guaranteed dolphin sightings.',
                'long' => 'Two-hour traditional dhoni boat trip into the atoll, complimentary refreshments, and a 95% dolphin-sighting record.'],
        ];

        foreach ($items as $a) {
            $destination = Destination::query()->where('name', $a['city'])->first();
            Activity::create([
                'destination_id' => $destination?->id,
                'name' => $a['name'],
                'slug' => Str::slug($a['name']),
                'category' => $a['category'],
                'short_description' => $a['short'],
                'description' => $a['long'],
                'cover_image' => 'https://picsum.photos/seed/act-'.Str::slug($a['name']).'/1200/800',
                'gallery' => [
                    'https://picsum.photos/seed/act-'.Str::slug($a['name']).'-1/1200/800',
                    'https://picsum.photos/seed/act-'.Str::slug($a['name']).'-2/1200/800',
                ],
                'highlights' => ['Small group', 'Hotel pickup', 'Free cancellation', 'Instant confirmation'],
                'included' => ['Bottled water', 'Hotel transfers', 'Professional guide'],
                'duration_hours' => $a['duration'],
                'price' => $a['price'],
                'currency' => 'USD',
                'min_age' => $a['difficulty'] === 'challenging' ? 14 : 4,
                'max_group_size' => 12,
                'difficulty' => $a['difficulty'],
                'is_published' => true,
                'is_featured' => $a['featured'],
                'rating_avg' => round(rand(42, 50) / 10, 1),
                'rating_count' => rand(10, 500),
            ]);
        }

        $this->command?->info('Activities: '.Activity::count().' rows seeded.');
    }

    protected function seedCruises(): void
    {
        if (! Schema::hasTable('cruises') || Cruise::query()->exists()) {
            return;
        }

        $cruises = [
            ['line' => 'Royal Caribbean', 'ship' => 'Symphony of the Seas', 'title' => 'Western Mediterranean Highlights', 'from' => 'Barcelona', 'to' => 'Rome (Civitavecchia)', 'nights' => 7, 'price' => 1199, 'featured' => true,
                'desc' => 'Sail from Barcelona to Marseille, Florence, Rome and the French Riviera aboard one of the world\'s largest cruise ships.'],
            ['line' => 'Norwegian Cruise Line', 'ship' => 'Norwegian Bliss', 'title' => 'Alaska Glacier Voyage', 'from' => 'Seattle', 'to' => 'Seattle', 'nights' => 7, 'price' => 1399, 'featured' => true,
                'desc' => 'Round-trip from Seattle through the Inside Passage with calls at Juneau, Skagway, Ketchikan and a day cruising Glacier Bay.'],
            ['line' => 'MSC Cruises', 'ship' => 'MSC Bellissima', 'title' => 'Arabian Gulf Discovery', 'from' => 'Dubai', 'to' => 'Dubai', 'nights' => 7, 'price' => 749, 'featured' => false,
                'desc' => 'Round-trip from Dubai with overnight stays in Abu Dhabi and Doha, plus calls at Sir Bani Yas and Muscat.'],
            ['line' => 'Costa Cruises', 'ship' => 'Costa Smeralda', 'title' => 'Greek Islands & Turkey', 'from' => 'Athens (Piraeus)', 'to' => 'Athens (Piraeus)', 'nights' => 7, 'price' => 899, 'featured' => false,
                'desc' => 'Mykonos, Santorini, Heraklion and Kuşadası — the classic Greek Isles loop from Athens.'],
            ['line' => 'Cunard', 'ship' => 'Queen Mary 2', 'title' => 'Transatlantic Crossing', 'from' => 'Southampton', 'to' => 'New York', 'nights' => 7, 'price' => 1599, 'featured' => true,
                'desc' => 'The classic ocean liner crossing — black-tie balls, planetarium shows and seven days of sea-day glamour.'],
            ['line' => 'Carnival Cruise Line', 'ship' => 'Carnival Magic', 'title' => 'Eastern Caribbean Sun', 'from' => 'Port Canaveral', 'to' => 'Port Canaveral', 'nights' => 6, 'price' => 549, 'featured' => false,
                'desc' => 'Round-trip from Florida with calls at Nassau, Amber Cove, and Grand Turk for a relaxed beach week.'],
            ['line' => 'Holland America', 'ship' => 'Koningsdam', 'title' => 'Norwegian Fjords', 'from' => 'Amsterdam', 'to' => 'Amsterdam', 'nights' => 11, 'price' => 1799, 'featured' => true,
                'desc' => 'Scenic fjord cruising with calls at Bergen, Geiranger, Flåm, Stavanger and Eidfjord — long Nordic days at sea.'],
            ['line' => 'Avalon Waterways', 'ship' => 'Tranquility II', 'title' => 'Mekong River — Cambodia & Vietnam', 'from' => 'Siem Reap', 'to' => 'Ho Chi Minh City', 'nights' => 8, 'price' => 2499, 'featured' => false,
                'desc' => 'Boutique river cruise from Angkor Wat through floating villages on the Mekong, ending in vibrant Saigon.'],
        ];

        foreach ($cruises as $c) {
            Cruise::create([
                'title' => $c['title'],
                'slug' => Str::slug($c['title']),
                'cruise_line' => $c['line'],
                'ship_name' => $c['ship'],
                'departure_port' => $c['from'],
                'arrival_port' => $c['to'],
                'departure_date' => now()->addMonths(rand(1, 9))->startOfDay(),
                'return_date' => now()->addMonths(rand(1, 9))->addDays($c['nights'])->startOfDay(),
                'duration_nights' => $c['nights'],
                'description' => $c['desc'],
                'highlights' => 'Multiple ports of call · All-inclusive dining onboard · Award-winning entertainment · Pools and spa',
                'cover_image' => 'https://picsum.photos/seed/cruise-'.Str::slug($c['title']).'/1200/800',
                'gallery' => [
                    'https://picsum.photos/seed/cruise-'.Str::slug($c['title']).'-1/1200/800',
                    'https://picsum.photos/seed/cruise-'.Str::slug($c['title']).'-2/1200/800',
                ],
                'cabin_types' => [
                    ['name' => 'Interior', 'price_from' => $c['price']],
                    ['name' => 'Ocean view', 'price_from' => round($c['price'] * 1.25, 2)],
                    ['name' => 'Balcony', 'price_from' => round($c['price'] * 1.55, 2)],
                    ['name' => 'Suite', 'price_from' => round($c['price'] * 2.4, 2)],
                ],
                'inclusions' => ['Onboard accommodation', 'All main meals', 'Entertainment', 'Use of pools and gym', 'Port taxes'],
                'exclusions' => ['Flights', 'Shore excursions', 'Premium drinks', 'Specialty dining', 'Gratuities'],
                'price_from' => $c['price'],
                'currency' => 'USD',
                'is_published' => true,
                'is_featured' => $c['featured'],
            ]);
        }

        $this->command?->info('Cruises: '.Cruise::count().' rows seeded.');
    }

    protected function seedCarRentals(): void
    {
        if (! Schema::hasTable('car_rentals') || CarRental::query()->exists()) {
            return;
        }

        $cars = [
            ['name' => 'Suzuki Swift Hatchback', 'class' => 'compact', 'make' => 'Suzuki', 'model' => 'Swift', 'seats' => 5, 'doors' => 5, 'luggage' => 2, 'transmission' => 'manual', 'fuel' => 'petrol', 'daily' => 22, 'weekly' => 130, 'desc' => 'Nimble and economical hatchback — perfect for city driving and twisty coastal roads.', 'featured' => false, 'pickups' => ['Mumbai Airport', 'Goa Airport']],
            ['name' => 'Toyota Innova Crysta', 'class' => 'suv', 'make' => 'Toyota', 'model' => 'Innova', 'seats' => 7, 'doors' => 5, 'luggage' => 4, 'transmission' => 'automatic', 'fuel' => 'diesel', 'daily' => 55, 'weekly' => 340, 'desc' => 'India\'s favourite people-mover — comfortable for long highway drives with the family.', 'featured' => true, 'pickups' => ['Delhi Airport', 'Jaipur Airport', 'Mumbai Airport']],
            ['name' => 'Honda City Sedan', 'class' => 'sedan', 'make' => 'Honda', 'model' => 'City', 'seats' => 5, 'doors' => 4, 'luggage' => 3, 'transmission' => 'automatic', 'fuel' => 'petrol', 'daily' => 38, 'weekly' => 230, 'desc' => 'Refined mid-size sedan with a roomy boot — ideal for couples and small families.', 'featured' => false, 'pickups' => ['Mumbai Airport', 'Bangalore Airport']],
            ['name' => 'Toyota Fortuner 4x4', 'class' => 'suv', 'make' => 'Toyota', 'model' => 'Fortuner', 'seats' => 7, 'doors' => 5, 'luggage' => 5, 'transmission' => 'automatic', 'fuel' => 'diesel', 'daily' => 95, 'weekly' => 590, 'desc' => 'Big, capable 4x4 for adventures into the Himalayas, deserts or off-road tracks.', 'featured' => true, 'pickups' => ['Delhi Airport', 'Leh Airport']],
            ['name' => 'BMW 5 Series', 'class' => 'luxury', 'make' => 'BMW', 'model' => '5 Series', 'seats' => 5, 'doors' => 4, 'luggage' => 3, 'transmission' => 'automatic', 'fuel' => 'petrol', 'daily' => 175, 'weekly' => 1090, 'desc' => 'Executive sedan with leather interior and chauffeur option — corporate-trip ready.', 'featured' => true, 'pickups' => ['Mumbai Airport', 'Delhi Airport', 'Bangalore Airport']],
            ['name' => 'Mercedes-Benz V-Class', 'class' => 'van', 'make' => 'Mercedes-Benz', 'model' => 'V-Class', 'seats' => 8, 'doors' => 5, 'luggage' => 6, 'transmission' => 'automatic', 'fuel' => 'diesel', 'daily' => 220, 'weekly' => 1370, 'desc' => 'Luxury minivan with captain chairs — ideal for groups, weddings and airport transfers.', 'featured' => false, 'pickups' => ['Mumbai Airport', 'Goa Airport']],
            ['name' => 'Toyota Camry Hybrid', 'class' => 'sedan', 'make' => 'Toyota', 'model' => 'Camry', 'seats' => 5, 'doors' => 4, 'luggage' => 3, 'transmission' => 'automatic', 'fuel' => 'hybrid', 'daily' => 79, 'weekly' => 490, 'desc' => 'Refined hybrid sedan — quiet, comfortable and remarkably fuel efficient.', 'featured' => false, 'pickups' => ['Bangalore Airport', 'Hyderabad Airport']],
            ['name' => 'Mahindra Thar 4x4 Convertible', 'class' => 'suv', 'make' => 'Mahindra', 'model' => 'Thar', 'seats' => 4, 'doors' => 3, 'luggage' => 2, 'transmission' => 'manual', 'fuel' => 'diesel', 'daily' => 65, 'weekly' => 410, 'desc' => 'Roof-off, doors-off off-road icon — the fun choice for Goa or Rajasthan adventures.', 'featured' => true, 'pickups' => ['Goa Airport', 'Jaipur Airport']],
        ];

        foreach ($cars as $c) {
            CarRental::create([
                'name' => $c['name'],
                'slug' => Str::slug($c['name']),
                'vehicle_class' => $c['class'],
                'make' => $c['make'],
                'model' => $c['model'],
                'description' => $c['desc'],
                'cover_image' => 'https://picsum.photos/seed/car-'.Str::slug($c['name']).'/1200/800',
                'gallery' => [
                    'https://picsum.photos/seed/car-'.Str::slug($c['name']).'-1/1200/800',
                    'https://picsum.photos/seed/car-'.Str::slug($c['name']).'-2/1200/800',
                ],
                'features' => ['Bluetooth audio', 'USB charging', 'Reverse camera', $c['transmission'] === 'automatic' ? 'Cruise control' : 'Sport mode'],
                'pickup_locations' => $c['pickups'],
                'seats' => $c['seats'],
                'doors' => $c['doors'],
                'luggage' => $c['luggage'],
                'transmission' => $c['transmission'],
                'fuel_type' => $c['fuel'],
                'has_ac' => true,
                'daily_rate' => $c['daily'],
                'weekly_rate' => $c['weekly'],
                'currency' => 'USD',
                'is_published' => true,
                'is_featured' => $c['featured'],
            ]);
        }

        $this->command?->info('Car rentals: '.CarRental::count().' rows seeded.');
    }

    protected function seedTaxiServices(): void
    {
        if (! Schema::hasTable('taxi_services') || TaxiService::query()->exists()) {
            return;
        }

        $services = [
            ['title' => 'Mumbai Airport Sedan Transfer', 'type' => 'airport_transfer', 'vehicle' => 'Sedan', 'capacity' => 3, 'luggage' => 3, 'flat' => 22, 'base' => 0, 'km' => 0, 'hour' => 0, 'desc' => 'Pre-booked door-to-door transfer between Chhatrapati Shivaji International Airport and any Mumbai address.', 'areas' => ['Mumbai', 'Navi Mumbai'], 'featured' => true],
            ['title' => 'Mumbai Airport SUV Transfer', 'type' => 'airport_transfer', 'vehicle' => 'SUV', 'capacity' => 6, 'luggage' => 5, 'flat' => 35, 'base' => 0, 'km' => 0, 'hour' => 0, 'desc' => 'Comfortable Toyota Innova or similar — ideal for families and groups with luggage.', 'areas' => ['Mumbai'], 'featured' => false],
            ['title' => 'Goa Airport Beach Transfer', 'type' => 'airport_transfer', 'vehicle' => 'Sedan', 'capacity' => 3, 'luggage' => 3, 'flat' => 28, 'base' => 0, 'km' => 0, 'hour' => 0, 'desc' => 'Fixed-price transfer from Dabolim or Mopa airport to any North or South Goa beach resort.', 'areas' => ['North Goa', 'South Goa'], 'featured' => true],
            ['title' => 'Delhi 8-Hour Sightseeing Hire', 'type' => 'hourly', 'vehicle' => 'Sedan', 'capacity' => 3, 'luggage' => 2, 'flat' => 0, 'base' => 12, 'km' => 0.6, 'hour' => 12, 'desc' => 'Hourly chauffeur hire for a self-guided day around Old Delhi, Lutyens Delhi and key monuments.', 'areas' => ['Delhi NCR'], 'featured' => false],
            ['title' => 'Jaipur Local Sightseeing (Full Day)', 'type' => 'hourly', 'vehicle' => 'SUV', 'capacity' => 6, 'luggage' => 4, 'flat' => 0, 'base' => 14, 'km' => 0.8, 'hour' => 14, 'desc' => 'A full day with private vehicle and driver to cover Amber Fort, Hawa Mahal, City Palace and Jal Mahal.', 'areas' => ['Jaipur'], 'featured' => false],
            ['title' => 'Bangkok Airport Express Transfer', 'type' => 'airport_transfer', 'vehicle' => 'Sedan', 'capacity' => 3, 'luggage' => 3, 'flat' => 26, 'base' => 0, 'km' => 0, 'hour' => 0, 'desc' => 'Pre-booked private transfer between Suvarnabhumi (BKK) or Don Mueang (DMK) and central Bangkok.', 'areas' => ['Bangkok'], 'featured' => true],
            ['title' => 'Phuket Patong–Airport Transfer', 'type' => 'airport_transfer', 'vehicle' => 'Minivan', 'capacity' => 8, 'luggage' => 8, 'flat' => 32, 'base' => 0, 'km' => 0, 'hour' => 0, 'desc' => 'Spacious minivan transfer between Phuket Airport and any Patong, Karon or Kata hotel.', 'areas' => ['Phuket'], 'featured' => false],
            ['title' => 'Dubai Premium Airport Transfer', 'type' => 'airport_transfer', 'vehicle' => 'Luxury Sedan', 'capacity' => 3, 'luggage' => 3, 'flat' => 65, 'base' => 0, 'km' => 0, 'hour' => 0, 'desc' => 'Mercedes E-Class or BMW 5 Series transfer between DXB or DWC and any Dubai address.', 'areas' => ['Dubai'], 'featured' => true],
            ['title' => 'Bali Point-to-Point Driver', 'type' => 'point_to_point', 'vehicle' => 'SUV', 'capacity' => 6, 'luggage' => 5, 'flat' => 0, 'base' => 8, 'km' => 0.5, 'hour' => 0, 'desc' => 'On-demand pickup anywhere in South Bali — fixed quotes before you confirm.', 'areas' => ['Kuta', 'Seminyak', 'Ubud', 'Canggu'], 'featured' => false],
            ['title' => 'Singapore Marina Transfer', 'type' => 'airport_transfer', 'vehicle' => 'Sedan', 'capacity' => 3, 'luggage' => 3, 'flat' => 38, 'base' => 0, 'km' => 0, 'hour' => 0, 'desc' => 'Changi Airport (SIN) to Marina Bay, Orchard or any central Singapore hotel.', 'areas' => ['Singapore'], 'featured' => false],
        ];

        foreach ($services as $s) {
            TaxiService::create([
                'title' => $s['title'],
                'slug' => Str::slug($s['title']),
                'service_type' => $s['type'],
                'vehicle_type' => $s['vehicle'],
                'description' => $s['desc'],
                'cover_image' => 'https://picsum.photos/seed/taxi-'.Str::slug($s['title']).'/1200/800',
                'features' => ['Meet & greet at arrivals', 'Free 60-minute waiting time', 'Free cancellation up to 24h', 'Bottled water'],
                'service_areas' => $s['areas'],
                'capacity' => $s['capacity'],
                'luggage' => $s['luggage'],
                'base_fare' => $s['base'],
                'per_km_rate' => $s['km'],
                'per_hour_rate' => $s['hour'],
                'flat_rate' => $s['flat'],
                'currency' => 'USD',
                'is_published' => true,
                'is_featured' => $s['featured'],
            ]);
        }

        $this->command?->info('Taxi services: '.TaxiService::count().' rows seeded.');
    }

    protected function seedBusRoutes(): void
    {
        if (! Schema::hasTable('bus_routes') || BusRoute::query()->exists()) {
            return;
        }

        $routes = [
            ['operator' => 'VRL Travels', 'origin' => 'Mumbai', 'destination' => 'Goa', 'type' => 'sleeper', 'depart' => '21:00', 'arrive' => '08:30', 'minutes' => 690, 'km' => 600, 'fare' => 28, 'featured' => true],
            ['operator' => 'Neeta Travels', 'origin' => 'Mumbai', 'destination' => 'Pune', 'type' => 'ac', 'depart' => '07:30', 'arrive' => '11:00', 'minutes' => 210, 'km' => 150, 'fare' => 12, 'featured' => false],
            ['operator' => 'Rajasthan State RTC', 'origin' => 'Delhi', 'destination' => 'Jaipur', 'type' => 'luxury', 'depart' => '06:00', 'arrive' => '11:00', 'minutes' => 300, 'km' => 280, 'fare' => 18, 'featured' => true],
            ['operator' => 'KSRTC Volvo', 'origin' => 'Bangalore', 'destination' => 'Mysore', 'type' => 'ac', 'depart' => '08:00', 'arrive' => '11:00', 'minutes' => 180, 'km' => 150, 'fare' => 10, 'featured' => false],
            ['operator' => 'Transport Bangkok', 'origin' => 'Bangkok', 'destination' => 'Pattaya', 'type' => 'ac', 'depart' => '09:00', 'arrive' => '11:30', 'minutes' => 150, 'km' => 150, 'fare' => 14, 'featured' => false],
            ['operator' => 'Sombat Tour', 'origin' => 'Bangkok', 'destination' => 'Chiang Mai', 'type' => 'sleeper', 'depart' => '19:30', 'arrive' => '07:00', 'minutes' => 690, 'km' => 700, 'fare' => 35, 'featured' => true],
            ['operator' => 'Phuket Express', 'origin' => 'Phuket', 'destination' => 'Krabi', 'type' => 'standard', 'depart' => '08:30', 'arrive' => '11:30', 'minutes' => 180, 'km' => 165, 'fare' => 9, 'featured' => false],
            ['operator' => 'Perama', 'origin' => 'Bali (Ubud)', 'destination' => 'Lovina', 'type' => 'standard', 'depart' => '10:00', 'arrive' => '13:00', 'minutes' => 180, 'km' => 95, 'fare' => 11, 'featured' => false],
            ['operator' => 'RTA Express', 'origin' => 'Dubai', 'destination' => 'Abu Dhabi', 'type' => 'luxury', 'depart' => '07:00', 'arrive' => '08:45', 'minutes' => 105, 'km' => 140, 'fare' => 9, 'featured' => false],
        ];

        foreach ($routes as $r) {
            $title = "{$r['operator']} · {$r['origin']} to {$r['destination']}";
            BusRoute::create([
                'title' => $title,
                'slug' => Str::slug($title),
                'operator' => $r['operator'],
                'bus_type' => $r['type'],
                'origin' => $r['origin'],
                'destination' => $r['destination'],
                'stops' => [],
                'departure_time' => $r['depart'],
                'arrival_time' => $r['arrive'],
                'duration_minutes' => $r['minutes'],
                'distance_km' => $r['km'],
                'schedule_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
                'amenities' => $r['type'] === 'sleeper'
                    ? ['Reclining berths', 'Charging point', 'Reading light', 'Air conditioning', 'Onboard toilet']
                    : ['Air conditioning', 'Charging point', 'Reclining seats', 'Bottled water'],
                'description' => "Daily service operated by {$r['operator']} between {$r['origin']} and {$r['destination']}.",
                'fare' => $r['fare'],
                'currency' => 'USD',
                'is_published' => true,
                'is_featured' => $r['featured'],
            ]);
        }

        $this->command?->info('Bus routes: '.BusRoute::count().' rows seeded.');
    }

    protected function seedVisaServices(): void
    {
        if (! Schema::hasTable('visa_services') || VisaService::query()->exists()) {
            return;
        }

        $visas = [
            ['country' => 'India', 'code' => 'IN', 'type' => 'Tourist e-Visa', 'title' => 'India 30-Day Tourist e-Visa', 'fee' => 25, 'service' => 18, 'min' => 3, 'max' => 7, 'stay' => 30, 'validity' => 30, 'featured' => true,
                'desc' => 'Single-entry electronic tourist visa valid for 30 days from arrival. Apply online at least 4 days before travel.',
                'requirements' => ['Passport valid 6+ months from arrival', 'Recent passport-style photo', 'Confirmed return ticket', 'Valid email address'],
                'documents' => ['Scanned passport bio-page (PDF/JPEG)', 'Recent passport photo (JPEG)', 'Return flight booking']],
            ['country' => 'India', 'code' => 'IN', 'type' => 'Tourist e-Visa', 'title' => 'India 1-Year Multiple-Entry Tourist Visa', 'fee' => 40, 'service' => 25, 'min' => 5, 'max' => 10, 'stay' => 90, 'validity' => 365, 'featured' => false,
                'desc' => 'Multiple-entry tourist visa valid for one year — each stay capped at 90 days. Ideal for repeat visitors.',
                'requirements' => ['Passport valid 12+ months from arrival', 'Recent passport-style photo', 'Travel itinerary'],
                'documents' => ['Scanned passport bio-page', 'Recent passport photo', 'Itinerary or hotel bookings']],
            ['country' => 'Thailand', 'code' => 'TH', 'type' => 'Visa on Arrival', 'title' => 'Thailand 15-Day Visa on Arrival', 'fee' => 60, 'service' => 15, 'min' => 0, 'max' => 1, 'stay' => 15, 'validity' => 15, 'featured' => false,
                'desc' => 'Issued on arrival at major Thai airports for eligible nationalities. We pre-fill the application to save you queue time.',
                'requirements' => ['Passport valid 6+ months', 'Confirmed onward ticket within 15 days', 'Proof of funds (THB 10,000)'],
                'documents' => ['Scanned passport bio-page', 'Onward flight booking', 'Hotel booking']],
            ['country' => 'Thailand', 'code' => 'TH', 'type' => 'Tourist e-Visa', 'title' => 'Thailand 60-Day Tourist e-Visa', 'fee' => 40, 'service' => 22, 'min' => 5, 'max' => 14, 'stay' => 60, 'validity' => 90, 'featured' => true,
                'desc' => 'Pre-arrival electronic tourist visa allowing a 60-day stay, with the option to extend by 30 days in-country.',
                'requirements' => ['Passport valid 6+ months', 'Bank statement (last 3 months)', 'Confirmed accommodation'],
                'documents' => ['Scanned passport bio-page', 'Bank statement', 'Hotel/accommodation booking', 'Recent photo']],
            ['country' => 'United Arab Emirates', 'code' => 'AE', 'type' => 'Tourist e-Visa', 'title' => 'UAE 30-Day Tourist Visa', 'fee' => 90, 'service' => 25, 'min' => 3, 'max' => 5, 'stay' => 30, 'validity' => 60, 'featured' => true,
                'desc' => 'Single-entry tourist visa for the UAE valid for 60 days from issue, allowing a 30-day stay.',
                'requirements' => ['Passport valid 6+ months', 'Recent colour photo (white background)', 'Confirmed return ticket'],
                'documents' => ['Passport bio-page', 'Coloured passport photo', 'Return flight booking', 'Hotel booking']],
            ['country' => 'United Arab Emirates', 'code' => 'AE', 'type' => 'Tourist e-Visa', 'title' => 'UAE 90-Day Multi-Entry Tourist Visa', 'fee' => 175, 'service' => 35, 'min' => 5, 'max' => 7, 'stay' => 90, 'validity' => 180, 'featured' => false,
                'desc' => 'Long-stay multi-entry tourist visa valid for six months from issue with a 90-day stay per entry.',
                'requirements' => ['Passport valid 6+ months', 'Bank statement showing AED 4,000+', 'Travel itinerary'],
                'documents' => ['Passport bio-page', 'Coloured passport photo', 'Bank statement', 'Itinerary']],
            ['country' => 'Singapore', 'code' => 'SG', 'type' => 'Tourist e-Visa', 'title' => 'Singapore 30-Day Tourist Visa', 'fee' => 30, 'service' => 18, 'min' => 3, 'max' => 5, 'stay' => 30, 'validity' => 60, 'featured' => false,
                'desc' => 'Required for select nationalities. Single or double-entry tourist visa for short visits to Singapore.',
                'requirements' => ['Passport valid 6+ months', 'Confirmed return ticket', 'Hotel booking'],
                'documents' => ['Passport bio-page', 'Recent photo', 'Flight & hotel bookings']],
            ['country' => 'Indonesia', 'code' => 'ID', 'type' => 'Visa on Arrival', 'title' => 'Indonesia 30-Day Visa on Arrival', 'fee' => 35, 'service' => 12, 'min' => 0, 'max' => 1, 'stay' => 30, 'validity' => 30, 'featured' => false,
                'desc' => 'Issued at Bali, Jakarta and other major airports for eligible nationalities. Extendable once for a further 30 days.',
                'requirements' => ['Passport valid 6+ months', 'Onward or return flight'],
                'documents' => ['Passport bio-page', 'Onward flight booking']],
        ];

        foreach ($visas as $v) {
            VisaService::create([
                'country' => $v['country'],
                'country_code' => $v['code'],
                'visa_type' => $v['type'],
                'title' => $v['title'],
                'slug' => Str::slug($v['title']),
                'description' => $v['desc'],
                'eligibility' => 'Most international passport holders. Eligibility varies by nationality — please check with our team if unsure.',
                'notes' => 'Processing times exclude weekends and public holidays. We will email you at every stage of the application.',
                'requirements' => $v['requirements'],
                'documents' => $v['documents'],
                'processing_days_min' => $v['min'],
                'processing_days_max' => $v['max'],
                'allowed_stay_days' => $v['stay'],
                'validity_days' => $v['validity'],
                'fee' => $v['fee'],
                'service_fee' => $v['service'],
                'currency' => 'USD',
                'is_published' => true,
                'is_featured' => $v['featured'],
            ]);
        }

        $this->command?->info('Visa services: '.VisaService::count().' rows seeded.');
    }
}
