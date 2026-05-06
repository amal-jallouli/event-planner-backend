<?php

namespace Database\Seeders;

use App\Models\{Event, User, Category};
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $admin      = User::where('role', 'admin')->first();
        $categories = Category::all()->keyBy('name');

        $events = [
            [
                'title'       => 'Conférence Tech Tunisia 2024',
                'description' => 'Découvrez les dernières tendances tech : IA, blockchain et développement web. Conférenciers de haut niveau et networking.',
                'start_date'  => now()->addDays(10),
                'end_date'    => now()->addDays(10)->addHours(8),
                'place'       => 'Palais des Congrès, Tunis',
                'price'       => 50, 'is_free' => false, 'capacity' => 200,
                'category_id' => $categories['Technologie']->id, 'status' => 'actif',
            ],
            [
                'title'       => 'Festival Jazz de Carthage',
                'description' => 'Soirée inoubliable avec les meilleurs artistes jazz de la région. Entrée libre pour tous les amateurs de musique.',
                'start_date'  => now()->addDays(15),
                'end_date'    => now()->addDays(15)->addHours(5),
                'place'       => 'Amphithéâtre de Carthage',
                'price'       => 0, 'is_free' => true, 'capacity' => 500,
                'category_id' => $categories['Musique']->id, 'status' => 'actif',
            ],
            [
                'title'       => 'Marathon de Tunis 2024',
                'description' => 'Marathon annuel de Tunis. Distances disponibles : 5km, 10km, 21km et 42km. Ouvert à tous les niveaux.',
                'start_date'  => now()->addDays(20),
                'end_date'    => now()->addDays(20)->addHours(6),
                'place'       => 'Avenue Habib Bourguiba, Tunis',
                'price'       => 30, 'is_free' => false, 'capacity' => 1000,
                'category_id' => $categories['Sport']->id, 'status' => 'actif',
            ],
            [
                'title'       => 'Workshop Angular Avancé',
                'description' => 'Formation intensive Angular 17 : signals, standalone components, architecture avancée. Places limitées.',
                'start_date'  => now()->addDays(5),
                'end_date'    => now()->addDays(5)->addHours(7),
                'place'       => 'Technopark El Ghazala, Ariana',
                'price'       => 80, 'is_free' => false, 'capacity' => 30,
                'category_id' => $categories['Formation']->id, 'status' => 'actif',
            ],
            [
                'title'       => 'Exposition d\'Art Contemporain',
                'description' => 'Œuvres de jeunes artistes tunisiens dans cette exposition unique. Entrée gratuite.',
                'start_date'  => now()->addDays(3),
                'end_date'    => now()->addDays(3)->addHours(4),
                'place'       => 'Galerie d\'Art El Foundouk, Tunis',
                'price'       => 0, 'is_free' => true, 'capacity' => 150,
                'category_id' => $categories['Art & Culture']->id, 'status' => 'actif',
            ],
            [
                'title'       => 'Startup Weekend Tunis',
                'description' => '54h pour créer votre startup. Entrepreneurs, designers et développeurs bienvenus. Mentors et investisseurs présents.',
                'start_date'  => now()->addDays(30),
                'end_date'    => now()->addDays(32),
                'place'       => 'Hub Startup, El Manar',
                'price'       => 100, 'is_free' => false, 'capacity' => 80,
                'category_id' => $categories['Business']->id, 'status' => 'actif',
            ],
        ];

        foreach ($events as $data) {
            Event::create(array_merge($data, ['created_by' => $admin->id]));
        }
    }
}
