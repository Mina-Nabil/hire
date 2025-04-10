<?php

namespace App\Livewire\Recruitment;

use App\Exceptions\AppException;
use App\Models\Base\Area;
use App\Models\Base\City;
use App\Models\Personel\Employee;
use App\Models\Recruitment\Applicants\Applicant;
use App\Models\Recruitment\Applicants\ApplicantHealth;
use App\Models\Recruitment\Applicants\Application;
use App\Models\Recruitment\Applicants\Channel;
use App\Models\Recruitment\Applicants\Language;
use App\Models\Recruitment\Vacancies\Vacancy;
use App\Traits\AlertFrontEnd;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Recruitment\Applicants\Education as ApplicantEducation;
use App\Models\Recruitment\Applicants\Experience as ApplicantExperience;
use App\Models\Recruitment\Applicants\Language as ApplicantLanguage;
use App\Models\Recruitment\Applicants\Reference as ApplicantReference;
use App\Models\Recruitment\Applicants\Training as ApplicantTraining;
use App\Models\Recruitment\Applicants\ApplicationAnswer;
use App\Models\Recruitment\Vacancies\BaseQuestion;
use Illuminate\Validation\Rule;
use App\Models\Recruitment\Applicants\ApplicantSkill;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ApplicantsCreate extends Component
{
    use AlertFrontEnd, WithFileUploads;

    //Page settings
    public $pageTitle = 'Create New Applicant';
    public $pageDescription = 'Create a new applicant for the selected vacancy';
    public $pageLayout = 'components.layouts.app';
    // Page data
    public $cities = [];
    public $areas = [];
    public $channels = [];
    public $employees = [];
    public $vacancies = [];
    public $baseQuestions = [];

    // Current step
    public $currentStep = 1;
    public $totalSteps = 8;

    // Step 1: Personal Information
    public $areaId;
    public $cityId;
    public $firstName;
    public $middleName = null;
    public $lastName;
    public $nationality = null;
    public $email;
    public $phone;
    public $address = null;
    public $socialNumber = null;
    public $homePhone = null;
    public $birthDate = null;
    public $channelId = null;
    public $militaryStatus = null;
    public $gender = null;
    public $maritalStatus = null;
    public $profileImage = null;
    public $cv = null;

    // Step 2: Education
    public $educations = [];

    // Step 3: Training
    public $trainings = [];

    // Step 4: Experience
    public $experiences = [];

    // Step 5: Languages
    public $languages = [];

    // Step 6: References
    public $references = [];

    // Step 7: Skills & Health
    public $skills = [];
    public $hasHealthIssues = false;
    public $healthIssues = null;

    // Skill lists for datalists
    public $computerSkillsList = [];
    public $technicalSkillsList = [];
    public $softSkillsList = [];

    // Step 8: Vacancy & Application
    public $selectedVacancy = null;
    public $selectedReferral = null;
    public $vacancyId = null;
    public $coverLetter = null;
    public $referredById = null;

    public function mount($hashedVacancyId=null, $hashedReferralId=null)
    {
        if ($hashedVacancyId) {
            $vacancyID = Hash::decode($hashedVacancyId);
            $this->selectedVacancy = Vacancy::findOrFail($vacancyID);
        }

        if ($hashedReferralId) {
            $referralID = Hash::decode($hashedReferralId);
            $this->selectedReferral = Employee::findOrFail($referralID);
        }

        if (!$this->selectedVacancy) {
            $this->authorize('viewAny', Vacancy::class);
        }

        // $this->areas = Area::all();
        $this->cities = City::all();
        $this->channels = Channel::all();
        $this->employees = Employee::all();
        $this->vacancies = Vacancy::where('status', 'open')->with('position')->get();
        $this->baseQuestions = BaseQuestion::all();
        
        // Initialize with one empty record for each collection
        $this->addEducation();
        $this->addTraining();
        $this->addExperience();
        $this->addLanguage();
        $this->addReference();
        $this->addSkill();

        // Initialize skill lists from the ApplicantSkill model
        $this->computerSkillsList = ApplicantSkill::COMPUTER_SKILLS;
        $this->technicalSkillsList = ApplicantSkill::TECHNICAL_SKILLS;
        $this->softSkillsList = ApplicantSkill::SOFT_SKILLS;
    }

    public function updatedCityId($value)
    {
        if ($value) {
            $this->areas = Area::where('city_id', $value)->get();
        } else {
            $this->areas = [];
        }
    }

    // Navigation functions
    public function nextStep()
    {
        if ($this->currentStep === 1) {
            $this->validatePersonalInfo();
        } elseif ($this->currentStep === 2) {
            $this->validateEducations();
        } elseif ($this->currentStep === 3) {
            $this->validateTrainings();
        } elseif ($this->currentStep === 4) {
            $this->validateExperiences();
        } elseif ($this->currentStep === 5) {
            $this->validateLanguages();
        } elseif ($this->currentStep === 6) {
            $this->validateReferences();
        } elseif ($this->currentStep === 7) {
            $this->validateSkillsAndHealth();
        }

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    // Step 1: Personal Info
    public function validatePersonalInfo()
    {
        $this->validate([
            'areaId' => 'required|exists:areas,id',
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:applicants,email',
            'phone' => 'required|string|max:255|unique:applicants,phone',
            'homePhone' => 'required|string|max:255|unique:applicants,home_phone',
            'birthDate' => 'nullable|date',
            'gender' => 'nullable|in:' . implode(',', Applicant::GENDER),
            'maritalStatus' => 'nullable|in:' . implode(',', Applicant::MARITAL_STATUS),
            'militaryStatus' => 'nullable|in:' . implode(',', Applicant::MILITARY_STATUS),
            'channelId' => 'nullable|exists:channels,id',
            'profileImage' => 'nullable|image|max:1024',
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);
    }

    // Step 2: Education
    public function addEducation()
    {
        $this->educations[] = [
            'school_name' => '',
            'degree' => '',
            'field_of_study' => '',
            'start_date' => null,
            'end_date' => null
        ];
    }

    public function removeEducation($index)
    {
        if (isset($this->educations[$index])) {
            array_splice($this->educations, $index, 1);
        }

        if (count($this->educations) === 0) {
            $this->addEducation();
        }
    }

    public function validateEducations()
    {
        foreach ($this->educations as $index => $education) {
            if (!empty($education['school_name'])) {
                $this->validate([
                    "educations.{$index}.school_name" => 'required|string|max:255',
                    "educations.{$index}.degree" => 'required|string|max:255',
                    "educations.{$index}.field_of_study" => 'required|string|max:255',
                    "educations.{$index}.start_date" => 'required|date',
                    "educations.{$index}.end_date" => 'nullable|date|after_or_equal:educations.' . $index . '.start_date',
                ], [
                    'educations.{$index}.school_name.required' => 'The school name is required',
                    'educations.{$index}.degree.required' => 'The degree is required',
                    'educations.{$index}.field_of_study.required' => 'The field of study is required',
                    'educations.{$index}.start_date.required' => 'The start date is required',
                    'educations.{$index}.end_date.after_or_equal' => 'The end date must be after or equal to the start date',
                ]);
            }
        }
    }

    // Step 3: Training
    public function addTraining()
    {
        $this->trainings[] = [
            'name' => '',
            'sponsor' => '',
            'duration' => '',
            'start_date' => null
        ];
    }

    public function removeTraining($index)
    {
        if (isset($this->trainings[$index])) {
            array_splice($this->trainings, $index, 1);
        }

        if (count($this->trainings) === 0) {
            $this->addTraining();
        }
    }

    public function validateTrainings()
    {
        foreach ($this->trainings as $index => $training) {
            if (!empty($training['name'])) {
                $this->validate([
                    "trainings.{$index}.name" => 'required|string|max:255',
                    "trainings.{$index}.sponsor" => 'required|string|max:255',
                    "trainings.{$index}.duration" => 'required|string|max:255',
                    "trainings.{$index}.start_date" => 'required|date',
                ], [
                    "trainings.{$index}.name.required" => 'The training name is required',
                    "trainings.{$index}.sponsor.required" => 'The training sponsor is required',
                    "trainings.{$index}.duration.required" => 'The training duration is required',
                    "trainings.{$index}.start_date.required" => 'The training start date is required',
                ]);
            }
        }
    }

    // Step 4: Experience
    public function addExperience()
    {
        $this->experiences[] = [
            'company_name' => '',
            'position' => '',
            'start_date' => null,
            'end_date' => null,
            'salary' => '',
            'reason_for_leaving' => ''
        ];
    }

    public function removeExperience($index)
    {
        if (isset($this->experiences[$index])) {
            array_splice($this->experiences, $index, 1);
        }

        if (count($this->experiences) === 0) {
            $this->addExperience();
        }
    }

    public function validateExperiences()
    {
        foreach ($this->experiences as $index => $experience) {
            if (!empty($experience['company_name'])) {
                $this->validate([
                    "experiences.{$index}.company_name" => 'required|string|max:255',
                    "experiences.{$index}.position" => 'required|string|max:255',
                    "experiences.{$index}.start_date" => 'required|date',
                    "experiences.{$index}.end_date" => 'nullable|date|after_or_equal:experiences.' . $index . '.start_date',
                    "experiences.{$index}.salary" => 'nullable|string|max:255',
                    "experiences.{$index}.reason_for_leaving" => 'nullable|string|max:255',
                ], [
                    "experiences.{$index}.company_name.required" => 'The company name is required',
                    "experiences.{$index}.position.required" => 'The position is required',
                    "experiences.{$index}.start_date.required" => 'The start date is required',
                    "experiences.{$index}.end_date.after_or_equal" => 'The end date must be after or equal to the start date',
                ]);
            }
        }
    }

    // Step 5: Languages
    public function addLanguage()
    {
        $this->languages[] = [
            'language' => '',
            'speaking_level' => null,
            'writing_level' => null,
            'reading_level' => null
        ];
    }

    public function removeLanguage($index)
    {
        if (isset($this->languages[$index])) {
            array_splice($this->languages, $index, 1);
        }

        if (count($this->languages) === 0) {
            $this->addLanguage();
        }
    }

    public function validateLanguages()
    {
        foreach ($this->languages as $index => $language) {
            if (!empty($language['language'])) {
                $this->validate([
                    "languages.{$index}.language" => 'required|string|max:255',
                    "languages.{$index}.speaking_level" => 'nullable|in:' . implode(',', Language::PROFICIENCY_LEVELS),
                    "languages.{$index}.writing_level" => 'nullable|in:' . implode(',', Language::PROFICIENCY_LEVELS),
                    "languages.{$index}.reading_level" => 'nullable|in:' . implode(',', Language::PROFICIENCY_LEVELS),
                ], [
                    "languages.{$index}.language.required" => 'The language is required',
                ]);
            }
        }
    }

    // Step 6: References
    public function addReference()
    {
        $this->references[] = [
            'name' => '',
            'phone' => '',
            'email' => '',
            'address' => '',
            'relationship' => ''
        ];
    }

    public function removeReference($index)
    {
        if (isset($this->references[$index])) {
            array_splice($this->references, $index, 1);
        }

        if (count($this->references) === 0) {
            $this->addReference();
        }
    }

    public function validateReferences()
    {
        foreach ($this->references as $index => $reference) {
            if (!empty($reference['name'])) {
                $this->validate([
                    "references.{$index}.name" => 'required|string|max:255',
                    "references.{$index}.phone" => 'required|string|max:255',
                    "references.{$index}.email" => 'nullable|email|max:255',
                    "references.{$index}.address" => 'nullable|string|max:255',
                    "references.{$index}.relationship" => 'nullable|string|max:255',
                ], [
                    "references.{$index}.name.required" => 'The name is required',
                    "references.{$index}.phone.required" => 'The phone is required',
                ]);
            }
        }
    }

    // Step 7: Skills & Health
    public function addSkill()
    {
        $this->skills[] = [
            'skill' => '',
            'level' => null,
            'type' => '' // 'computer', 'technical', or 'soft'
        ];
    }

    public function removeSkill($index)
    {
        if (isset($this->skills[$index])) {
            array_splice($this->skills, $index, 1);
        }

        if (count($this->skills) === 0) {
            $this->addSkill();
        }
    }

    public function validateSkillsAndHealth()
    {
        foreach ($this->skills as $index => $skill) {
            if (!empty($skill['skill'])) {
                $this->validate([
                    "skills.{$index}.skill" => 'required|string|max:255',
                    "skills.{$index}.level" => 'required|in:' . implode(',', ApplicantSkill::SKILL_LEVELS),
                    "skills.{$index}.type" => 'required|in:computer,technical,soft',
                ], [
                    "skills.{$index}.skill.required" => 'The skill is required',
                    "skills.{$index}.level.required" => 'The skill level is required',
                    "skills.{$index}.type.required" => 'The skill type is required',
                ]);
            }
        }

        $this->validate([
            'hasHealthIssues' => 'boolean',
            'healthIssues' => 'required_if:hasHealthIssues,1|nullable|string|max:2000',
        ]);
    }


    // Step 8: Vacancy & Application
    public function updatedVacancyId($value)
    {
        if ($value) {
            $this->selectedVacancy = Vacancy::findOrFail($value);
        }
    }

    public function validateVacancyAndApplication()
    {
        $this->validate([
            'vacancyId' => 'required|exists:vacancies,id',
            'coverLetter' => 'nullable|string|max:2000',
            'referredById' => 'nullable|exists:employees,id',
        ], [
            "vacancyId.required" => 'The vacancy is required',
            "coverLetter.max" => 'The cover letter must be less than 2000 characters',
            "referredById.exists" => 'The referred by employee is invalid',
        ]);
    }

    // Create applicant
    public function createApplicant()
    {
        $this->validateVacancyAndApplication();

        try {
            DB::transaction(function () {
                // 1. Create the applicant
                $applicantData = [
                    'area_id' => $this->areaId,
                    'first_name' => $this->firstName,
                    'middle_name' => $this->middleName,
                    'last_name' => $this->lastName,
                    'nationality' => $this->nationality,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'address' => $this->address,
                    'social_number' => $this->socialNumber,
                    'home_phone' => $this->homePhone,
                    'birth_date' => $this->birthDate,
                    'channel_id' => $this->channelId,
                    'military_status' => $this->militaryStatus,
                    'gender' => $this->gender,
                    'marital_status' => $this->maritalStatus,
                ];

                $applicant = Applicant::createApplicant(
                    $this->areaId,
                    $this->firstName,
                    $this->lastName,
                    $this->email,
                    $this->phone,
                    $applicantData
                );

                // 2. Handle file uploads
                if ($this->profileImage) {
                    $imagePath = $this->profileImage->store('applicant-images', 'public');
                    $applicant->setImage($imagePath);
                }

                if ($this->cv) {
                    $cvPath = $this->cv->store('applicant-cvs', 'public');
                    $applicant->setCv($cvPath);
                }

                // 3. Set educations
                $validEducations = array_filter($this->educations, function ($education) {
                    return !empty($education['school_name']);
                });
                if (!empty($validEducations)) {
                    $applicant->setEducations($validEducations);
                }

                // 4. Set trainings
                $validTrainings = array_filter($this->trainings, function ($training) {
                    return !empty($training['name']);
                });
                if (!empty($validTrainings)) {
                    $applicant->setTrainings($validTrainings);
                }

                // 5. Set experiences
                $validExperiences = array_filter($this->experiences, function ($experience) {
                    return !empty($experience['company_name']);
                });
                if (!empty($validExperiences)) {
                    $applicant->setExperiences($validExperiences);
                }

                // 6. Set languages
                $validLanguages = array_filter($this->languages, function ($language) {
                    return !empty($language['language']);
                });
                if (!empty($validLanguages)) {
                    $applicant->setLanguages($validLanguages);
                }

                // 7. Set references
                $validReferences = array_filter($this->references, function ($reference) {
                    return !empty($reference['name']);
                });
                if (!empty($validReferences)) {
                    $applicant->setReferences($validReferences);
                }

                // 8. Set skills
                $validSkills = array_filter($this->skills, function ($skill) {
                    return !empty($skill['skill']);
                });
                if (!empty($validSkills)) {
                    $applicant->setSkills($validSkills);
                }

                // 9. Set health
                $applicant->setHealth($this->hasHealthIssues, $this->hasHealthIssues ? $this->healthIssues : null);

                // 10. Create application
                Application::createApplication(
                    $applicant->id,
                    $this->vacancyId,
                    $this->coverLetter,
                    $this->referredById
                );
            });

            $this->alertSuccess('Applicant created successfully!');
            return redirect()->to('/recruitment/applicants');
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alertError('Failed to create applicant. Please try again.');
        }
    }

    public function render()
    {
        $isLoggedIn = Auth::check();
        
        if ($isLoggedIn) {
            $this->pageTitle = 'Create New Applicant';
            $this->pageDescription = 'Create a new applicant for the selected vacancy';
            $this->pageLayout = 'components.layouts.app';
        } else {
            $this->pageTitle = 'Apply for ' . env('COMPANY_NAME', 'Our Company');
            $this->pageDescription = $this->selectedVacancy 
                ? 'Apply for the ' . $this->selectedVacancy->position->name . ' position' 
                : 'Apply for one of our open positions';
            $this->pageLayout = 'components.layouts.guest';
        }
        
        $view = view('livewire.recruitment.applicants-create', [
            'areas' => $this->areas,
            'channels' => $this->channels,
            'employees' => $this->employees,
            'vacancies' => $this->vacancies,
            'genderOptions' => Applicant::GENDER,
            'maritalStatusOptions' => Applicant::MARITAL_STATUS,
            'militaryStatusOptions' => Applicant::MILITARY_STATUS,
            'proficiencyLevels' => Language::PROFICIENCY_LEVELS,
            'skillLevels' => ApplicantSkill::SKILL_LEVELS,
            'computerSkillsList' => $this->computerSkillsList,
            'technicalSkillsList' => $this->technicalSkillsList,
            'softSkillsList' => $this->softSkillsList,
        ])->layout($this->pageLayout, [
            'title' => $this->pageTitle,
            'description' => $this->pageDescription,
            'applicantsCreate' => 'active',
        ]);
        
        return $view;
    }
}
