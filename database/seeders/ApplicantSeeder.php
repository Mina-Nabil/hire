<?php

namespace Database\Seeders;

use App\Models\Base\Area;
use App\Models\Recruitment\Applicants\Applicant;
use App\Models\Recruitment\Applicants\ApplicantSkill;
use App\Models\Recruitment\Applicants\Channel;
use App\Models\Recruitment\Applicants\Education;
use App\Models\Recruitment\Applicants\Experience;
use App\Models\Recruitment\Applicants\Language;
use App\Models\Recruitment\Applicants\Reference;
use App\Models\Recruitment\Vacancies\Vacancy;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class ApplicantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();
        $areas = Area::all();
        $channels = Channel::all();
        $vacancies = Vacancy::with('position.department')->get();
        
        // If no channels exist, create some
        if ($channels->isEmpty()) {
            $this->command->info('No channels found. Creating default channels...');
            $channelNames = [
                'LinkedIn',
                'CFA Society',
                'AMCham',
                'Recruitment Agency',
                'Consultants',
                'Others',
            ];
            
            foreach ($channelNames as $name) {
                Channel::create(['name' => $name]);
            }
            $channels = Channel::all();
        }

        $this->command->info('Creating 100 applicants...');
        
        // Generate 100 applicants
        for ($i = 0; $i < 100; $i++) {
            $gender = $faker->randomElement(Applicant::GENDER);
            $maritalStatus = $faker->randomElement(Applicant::MARITAL_STATUS);
            $militaryStatus = $gender === 'Male' ? $faker->randomElement(Applicant::MILITARY_STATUS) : null;
            
            // Create applicant
            $applicant = Applicant::create([
                'area_id' => $areas->random()->id,
                'channel_id' => $channels->random()->id,
                'first_name' => $faker->firstName($gender === 'Male' ? 'male' : 'female'),
                'middle_name' => $faker->optional(0.7)->firstName,
                'last_name' => $faker->lastName,
                'nationality' => $faker->country,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->unique()->numerify('+20 10########'),
                'home_phone' => $faker->optional(0.5)->numerify('+20 2########'),
                'address' => $faker->address,
                'social_number' => $faker->numerify('##############'),
                'birth_date' => $faker->dateTimeBetween('-45 years', '-20 years')->format('Y-m-d'),
                'gender' => $gender,
                'marital_status' => $maritalStatus,
                'military_status' => $militaryStatus,
            ]);

            // Add education records (1-3)
            $educationCount = rand(1, 3);
            for ($j = 0; $j < $educationCount; $j++) {
                $startDate = $faker->dateTimeBetween('-15 years', '-5 years');
                $endDate = (clone $startDate)->modify('+' . rand(2, 6) . ' years');
                
                Education::create([
                    'applicant_id' => $applicant->id,
                    'school_name' => $faker->randomElement(['Cairo University', 'Ain Shams University', 'Alexandria University', 'American University in Cairo', 'German University in Cairo', 'British University in Egypt']),
                    'degree' => $faker->randomElement(['Bachelor', 'Master', 'Ph.D', 'Diploma']),
                    'field_of_study' => $faker->randomElement(['Computer Science', 'Business Administration', 'Engineering', 'Medicine', 'Law', 'Arts', 'Science']),
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                ]);
            }

            // Add experience records (0-4)
            $experienceCount = rand(0, 4);
            $lastEndDate = null;
            
            for ($j = 0; $j < $experienceCount; $j++) {
                $startDate = $lastEndDate ? (clone $lastEndDate)->modify('+' . rand(1, 12) . ' months') : $faker->dateTimeBetween('-10 years', '-1 year');
                $endDate = (clone $startDate)->modify('+' . rand(1, 4) . ' years');
                $lastEndDate = $endDate;
                
                Experience::create([
                    'applicant_id' => $applicant->id,
                    'company_name' => $faker->company,
                    'position' => $faker->jobTitle,
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $j === $experienceCount - 1 ? ($faker->boolean(30) ? null : $endDate->format('Y-m-d')) : $endDate->format('Y-m-d'),
                    'salary' => $faker->numberBetween(5000, 30000),
                    'reason_for_leaving' => $faker->randomElement(['Better opportunity', 'Career growth', 'Relocation', 'Company restructuring', 'Contract ended', null]),
                ]);
            }

            // Add skills (2-6)
            $skillCount = rand(2, 6);
            $skillsPool = array_merge(ApplicantSkill::COMPUTER_SKILLS, ApplicantSkill::SOFT_SKILLS);
            $selectedSkills = Arr::random($skillsPool, $skillCount);
            
            foreach ($selectedSkills as $skill) {
                ApplicantSkill::create([
                    'applicant_id' => $applicant->id,
                    'skill' => $skill,
                    'level' => $faker->randomElement(ApplicantSkill::SKILL_LEVELS),
                ]);
            }

            // Add languages (1-3)
            $languageCount = rand(1, 3);
            $languages = ['Arabic', 'English', 'French', 'German', 'Spanish', 'Italian'];
            $selectedLanguages = Arr::random($languages, $languageCount);
            
            foreach ($selectedLanguages as $language) {
                Language::create([
                    'applicant_id' => $applicant->id,
                    'language' => $language,
                    'speaking_level' => $faker->randomElement(Language::PROFICIENCY_LEVELS),
                    'writing_level' => $faker->randomElement(Language::PROFICIENCY_LEVELS),
                    'reading_level' => $faker->randomElement(Language::PROFICIENCY_LEVELS),
                ]);
            }

            // Add references (0-2)
            $referenceCount = rand(0, 2);
            for ($j = 0; $j < $referenceCount; $j++) {
                Reference::create([
                    'applicant_id' => $applicant->id,
                    'name' => $faker->name,
                    'phone' => $faker->phoneNumber,
                    'email' => $faker->optional(0.8)->safeEmail,
                    'address' => $faker->optional(0.5)->address,
                    'relationship' => $faker->randomElement(['Former Manager', 'Colleague', 'Professor', 'Client']),
                ]);
            }

            // Apply for positions
            $applyCount = rand(1, 3);
            $availableVacancies = $vacancies->where('status', 'open')->random($applyCount);
            
            foreach ($availableVacancies as $vacancy) {
                $applicant->applyForVacancy(
                    $vacancy->id,
                    $faker->optional(0.7)->paragraph,
                    null
                );
            }
            
            if ($i > 0 && $i % 20 === 0) {
                $this->command->info("Created {$i} applicants...");
            }
        }
        
        $this->command->info('Finished creating 100 applicants with their applications and related records.');
    }
} 