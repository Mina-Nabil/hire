<?php

namespace App\Livewire\Recruitment;

use App\Exceptions\AppException;
use App\Models\Base\Area;
use App\Models\Base\City;
use App\Models\Personel\Employee;
use App\Models\Recruitment\Applicants\Applicant;
use App\Models\Recruitment\Applicants\Application;
use App\Models\Recruitment\Applicants\Channel;
use App\Models\Recruitment\Vacancies\Vacancy;
use App\Traits\AlertFrontEnd;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;


class ApplicantsCreateMin extends Component
{
    use AlertFrontEnd, WithFileUploads;

    //Page settings
    public $pageTitle = 'Create New Applicant';
    public $pageDescription = 'Create a new applicant for the selected vacancy';
    public $pageLayout = 'components.layouts.guest';
    // Page data
    public $cities = [];
    public $areas = [];
    public $channels = [];
    public $employees = [];
    public $vacancies = [];
    public $baseQuestions = [];
    public $vacancyQuestions = [];
    // Current step
    public $currentStep = 1;
    public $totalSteps = 2;
    // Current language
    public $locale;

    // Step 1: Personal Information
    public $areaId;
    public $cityId;
    public $firstName;
    public $lastName;
    public $email;
    public $phone;
    public $socialNumber = null;
    public $homePhone = null;
    public $channelId = null;
    public $cv = null;


    // Step 2: Vacancy & Application
    public $selectedVacancy = null;
    // public $allVacancyQuestions = [];
    // public $questionAnswers = [];
    public $selectedReferral = null;
    public $vacancyId = null;
    public $coverLetter = null;
    public $referredById = null;
    public $slotId = null;

    public function mount($vacancyID, $referralID = null)
    {
        // Set the locale from session or default to English
        $this->locale = Session::get('locale', 'en');
        App::setLocale($this->locale);

        try {
            $vacancyID = decrypt($vacancyID);
        } catch (Exception $e) {
            abort(404);
        }

        $this->vacancyId = $vacancyID;
        $this->selectedVacancy = Vacancy::findOrFail($vacancyID);
        $this->updatedVacancyId($vacancyID);

        if ($referralID) {
            $referralID = decrypt($referralID);
            $this->referredById = $referralID;
            $this->selectedReferral = Employee::findOrFail($referralID);
        }

        // $this->areas = Area::all();
        $this->cities = City::all();
        $this->channels = Channel::shown()->get();
        $this->employees = Employee::all();
        $this->vacancies = Vacancy::where('status', 'open')->with('position')->get();
    }

    public function switchLocale($locale)
    {
        $this->locale = $locale;
        Session::put('locale', $locale);
        App::setLocale($locale);
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
            'email' => 'required|email|max:255',
            'socialNumber' => 'required',
            'phone' => 'required|string|max:255',
            'homePhone' => 'nullable|string|max:255',
            'channelId' => 'nullable|exists:channels,id',
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:4096',
        ], [
            'areaId.required' => 'The area is required',
            'firstName.required' => 'The first name is required',
            'lastName.required' => 'The last name is required',
            'email.required' => 'The email is required',
            'phone.required' => 'The phone is required',
        ]);

        $user = Auth::user();
        if (!$user) {
            $this->validate([
                'cv' => 'required|file|mimes:pdf,doc,docx|max:4096',
            ], [
                'cv.required' => 'The CV is required',
                'cv.mimes' => 'The CV must be a PDF, DOC or DOCX file',
                'cv.max' => 'The CV must be less than 4MB',
            ]);
        }
    }

    // Step 2: Vacancy & Application
    public function updatedVacancyId($value)
    {
        if ($value) {
            $this->selectedVacancy = Vacancy::with('vacancy_questions', 'vacancy_slots')->findOrFail($value);

            // foreach ($this->baseQuestions as $question) {
            //     $this->allVacancyQuestions[] = [
            //         'id' => $question->id,
            //         'origin' => "base",
            //         'required' => $question->required,
            //         'question' => $question->question,
            //         'type' => $question->type,
            //         'options_array' => $question->options_array
            //     ];
            //     $this->questionAnswers[] = [
            //         'id' => $question->id,
            //         'origin' => "base",
            //         'answer' => '',
            //     ];
            // }

            // foreach ($this->selectedVacancy->vacancy_questions as $question) {
            //     $this->allVacancyQuestions[] = [
            //         'id' => $question->id,
            //         'origin' => "vacancy",
            //         'required' => $question->required,
            //         'question' => $question->question,
            //         'type' => $question->type,
            //         'options_array' => $question->options_array
            //     ];
            //     $this->questionAnswers[] = [
            //         'id' => $question->id,
            //         'origin' => "vacancy",
            //         'answer' => '',
            //     ];
            // }
        }
    }

    public function clearSelectedVacancy()
    {
        $this->selectedVacancy = null;
        // $this->allVacancyQuestions = [];
        // $this->questionAnswers = [];
    }

    // public function validateAnsweredQuestions()
    // {
    //     $validationRules = [];
    //     $messages = [];
    //     foreach ($this->allVacancyQuestions as $index => &$question) {
    //         if ($question['origin'] == "vacancy") {
    //             $questionObject = $this->selectedVacancy->vacancy_questions->where('id', $question['id'])->first();
    //             if ($questionObject->required) {
    //                 $validationRules["questionAnswers.{$index}.answer"] = 'required';
    //                 $messages["questionAnswers.{$index}.answer.required"] = 'The question is required';
    //             }
    //         } else {
    //             $questionObject = BaseQuestion::where('id', $question['id'])->first();
    //             if ($questionObject->required) {
    //                 $validationRules["questionAnswers.{$index}.answer"] = 'required';
    //                 $messages["questionAnswers.{$index}.answer.required"] = 'The question is required';
    //             }
    //         }
    //         $question["object"] = $questionObject;
    //     }

    //     if (count($validationRules) > 0) {
    //         $this->validate($validationRules, $messages);
    //     } else {
    //         return true;
    //     }
    // }




    public function validateVacancyAndApplication()
    {
        $this->validate([
            'vacancyId' => 'required|exists:vacancies,id',
            'coverLetter' => 'nullable|string|max:6000',
            'referredById' => 'nullable|exists:employees,id',
        ], [
            "vacancyId.required" => 'The vacancy is required',
            "coverLetter.max" => 'The cover letter must be less than 2000 characters',
            "referredById.exists" => 'The referred by employee is invalid',
        ]);

        $user = Auth::user();
        if (!$user) {
            $this->validate([
                'slotId' => 'required|exists:vacancy_slots,id',
            ], [
                'slotId.required' => 'The slot is required',
                'slotId.exists' => 'The slot is invalid',
            ]);
        }
    }

    // Create applicant
    public function createApplicant()
    {
        $this->validateVacancyAndApplication();
        // $this->validateAnsweredQuestions();

        try {
            DB::transaction(function () {
                // 1. Create the applicant
                $applicantData = [
                    'area_id' => $this->areaId,
                    'first_name' => $this->firstName,
                    'last_name' => $this->lastName,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'home_phone' => $this->homePhone,
                    'channel_id' => $this->channelId,
                ];

                $applicant = Applicant::createApplicant(
                    $this->areaId,
                    $this->firstName,
                    $this->lastName,
                    $this->email,
                    $this->phone,
                    $this->socialNumber,
                    $applicantData
                );

                // 2. Handle file uploads
                if ($this->cv) {
                    $cvPath = $this->cv->store(Applicant::CV_PATH, 's3');
                    $applicant->updateCv($cvPath, true);
                }




                // 10. Create application
                $application = Application::createApplication(
                    $applicant->id,
                    $this->vacancyId,
                    $this->coverLetter,
                    $this->referredById
                );

                // 11. Create application answers
                if ($this->slotId) {
                    $application->bookSlot($this->slotId);
                }
                // foreach ($this->questionAnswers as $i => $qa) {
                //     if (array_key_exists($i, $this->allVacancyQuestions)) {
                //         $application->addAnswer($qa['answer'], $this->allVacancyQuestions[$i]['object']);
                //     }
                // }
            });

            $this->alertSuccess('Applicant created successfully!');
            $user = Auth::user();
            if (!$user) {
                return redirect()->to('/thank-you');
            }
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
                ? env('COMPANY_NAME', 'Our Company') . ' Job Application'
                : 'Apply for one of our open positions';
            $this->pageLayout = 'components.layouts.guest';
        }

        return view('livewire.recruitment.applicants-create-min', [
            'areas' => $this->areas,
            'channels' => $this->channels,
            'employees' => $this->employees,
            'vacancies' => $this->vacancies,

        ])->layout($this->pageLayout, [
            'title' => $this->pageTitle,
            'description' => $this->pageDescription,
            'applicantsCreate' => 'active',
        ]);
    }
}
