<?php

namespace Database\Seeders;

use App\Models\BmiClassification;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesTableSeeder::class);

        $this->call(UsersTableSeeder::class);
        $this->call(AdminProfilesTableSeeder::class);



        $this->call(DaySeeder::class);





        $this->call(AboutUsTableSeeder::class);
        $this->call(PrivacyPoliciesTableSeeder::class);
        $this->call(TosTableSeeder::class);
        $this->call(ContactUsTableSeeder::class);
        $this->call(FAQTableSeeder::class);
        $this->call(SystemSettingsTableSeeder::class);

        // $this->call(CustomerCardsTableSeeder::class);
        

        // $this->call(NotificationsTableSeeder::class);


        $this->call(CountrySeeder::class);
        $this->call(CitiesTableSeeder::class);
        $this->call(AboutUsTableSeeder::class);
        $this->call(PrivacyPoliciesTableSeeder::class);
        $this->call(TosTableSeeder::class);
        $this->call(ContactUsTableSeeder::class);
        $this->call(FAQTableSeeder::class);
        // $this->call(LevelSeeder::class);

    }
}