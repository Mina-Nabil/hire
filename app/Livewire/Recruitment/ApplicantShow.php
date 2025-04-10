<?php

namespace App\Livewire\Recruitment;

use App\Exceptions\AppException;
use App\Models\Base\Area;
use App\Models\Personel\Employee;
use App\Models\Recruitment\Applicants\Applicant;
use App\Models\Recruitment\Applicants\ApplicantSkill;
use App\Models\Recruitment\Applicants\Application;
use App\Models\Recruitment\Applicants\Channel;
use App\Models\Users\Document;
use App\Models\Recruitment\Applicants\Language;
use App\Models\Recruitment\Interviews\Interview;
use App\Models\Recruitment\JobOffers\JobOffer;
use App\Models\Recruitment\Vacancies\Vacancy;
use App\Traits\AlertFrontEnd;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Users\User;

class ApplicantShow extends Component
{
    use AlertFrontEnd, WithFileUploads;

    //////page controls
    public $section = 'info';

    public function changeSection($section)
    {
        $this->section = $section;
    }

    //////model functions
    protected $listeners = [
        'deleteApplicant',
        'deleteDocument',
        'deleteEducation',
        'deleteTraining',
        'deleteExperience',
        'deleteLanguage',
        'deleteSkill',
        'deleteReference'
    ];

    public $applicant;
    public $interviews;
    public $offers;

    // New Application Modal
    public $showNewApplicationModal = false;
    public $vacancyId;
    public $applicationNotes;
    public $referedBy;
    public $referedByOptions;
    public $availableVacancies = [];

    // New Interview Modal
    public $interviewTypes = Interview::INTERVIEW_TYPES;
    public $showNewInterviewModal = false;
    public $selectedApplicationId;
    public $selectedApplication;
    public $interviewDate;
    public $interviewTime;
    public $interviewType;
    public $interviewLocation;
    public $interviewNotes;

    // Interview Feedback Modal
    public $showFeedbackModal = false;
    public $showFeedbacksHistoryModal = false;
    public $selectedInterview;
    public $interviewResult;
    public $rating;
    public $strengths;
    public $weaknesses;
    public $feedbackNotes;
    public $nextStep;
    public $newApplicationStatus;


    // Interview Management Modals
    public $showSetInterviewersModal = false;
    public $interviewers = [];
    public $selectedInterviewers = [];
    
    public $showRescheduleModal = false;
    public $newInterviewDate;
    public $newInterviewTime;
    public $newInterviewLocation;
    public $newInterviewType;
    public $newInterviewZoomLink;

    public $showCancelModal = false;
    public $cancelReason;
    
    public $showCompleteModal = false;
    
    public $showAddNoteModal = false;
    public $noteTitle;
    public $noteContent;
    
    public $showUpdateStatusModal = false;
    public $newInterviewStatus;
    public $interviewStatuses = Interview::INTERVIEW_STATUSES;
    public $applicationStatuses = Application::APPLICATION_STATUSES;

    // Offer related properties
    public $showNewOfferModal = false;
    public $showViewOfferModal = false;
    public $applicationId;
    public $offeredSalary;
    public $proposedStartDate;
    public $expiryDate;
    public $benefits;
    public $specialTerms;
    public $offerNotes;
    public $selectedOffer;
    public $eligibleApplications;
    public $canCreateOffer = false;

    // Document upload
    public $showDocumentUploadModal = false;
    public $documentName;
    public $documentFile;
    public $documentNotes;

    // Edit Main Information Modal
    public $showMainInfoModal = false;
    public $editMainInfo = [];
    public $cvResume;
    public $channels;
    public $areas;

    // Edit Education Modal
    public $showEducationModal = false;
    public $educations = [];

    // Edit Training Modal
    public $showTrainingModal = false;
    public $trainings = [];

    // Edit Experience Modal
    public $showExperienceModal = false;
    public $experiences = [];

    // Edit Language Modal
    public $showLanguageModal = false;
    public $languageLevels = Language::PROFICIENCY_LEVELS;

    public $languages = [];

    // Edit Reference Modal
    public $showReferenceModal = false;
    public $references = [];

    // Edit Skills Modal
    public $showSkillModal = false;
    public $skillLevels = ApplicantSkill::SKILL_LEVELS;
    public $allSkillsString = "";
    public $skillsLevels = ApplicantSkill::SKILL_LEVELS;
    public $skills = [];

    // Edit Health Modal
    public $showHealthModal = false;
    public $hasHealthIssues = false;
    public $healthIssues;

    // Image Upload
    public $showImageUploadModal = false;
    public $newImage;

    //delete function
    public function deleteApplicant()
    {
        try {
            $this->applicant->delete();
            $this->alert('success', 'Applicant deleted successfully');
        } catch (AppException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'Failed to delete applicant');
        }

        return redirect()->route('recruitment.applicants');
    }

    // Document Management
    public function openDocumentUploadModal()
    {
        $this->showDocumentUploadModal = true;
        $this->reset(['documentName', 'documentFile', 'documentNotes']);
    }

    public function closeDocumentUploadModal()
    {
        $this->showDocumentUploadModal = false;
        $this->reset(['documentName', 'documentFile', 'documentNotes']);
    }

    public function uploadDocument()
    {
        $this->validate([
            'documentName' => 'required|string|max:255',
            'documentFile' => 'required|file|max:10240', // 10MB max
            'documentNotes' => 'nullable|string|max:500',
        ]);

        try {
            $filePath = $this->documentFile->store(Document::APPLICANT_DOCUMENTS, 's3');

            Document::createDocument($this->applicant, $this->documentName, $filePath, $this->documentNotes);

            $this->alert('success', 'Document uploaded successfully');
            $this->closeDocumentUploadModal();

            // Refresh applicant data
            $this->applicant->refresh();
        } catch (Exception $e) {
            $this->alert('error', 'Failed to upload document: ' . $e->getMessage());
        }
    }

    public function confirmDeleteDocument($documentId)
    {
        $this->dispatch(
            'showConfirmation',
            'Are you sure you want to delete this document?',
            'danger',
            'deleteDocument',
            $documentId
        );
    }

    public function deleteDocument($documentId)
    {
        try {
            $document = Document::findOrFail($documentId);

            // Delete the file from storage
            if ($document->file_path) {
                Storage::disk('s3')->delete($document->file_path);
            }

            $document->delete();
            $this->alert('success', 'Document deleted successfully');

            // Refresh applicant data
            $this->applicant->refresh();
        } catch (Exception $e) {
            $this->alert('error', 'Failed to delete document: ' . $e->getMessage());
        }
    }


    //mount and render
    /**
     * @param Applicant $applicant
     */
    public function mount($applicant)
    {
        $this->allSkillsString = implode(",", ApplicantSkill::ALL_SKILLS);
        $this->applicant = Applicant::findOrFail($applicant);
        $this->applicant->load([
            'area',
            'applications.vacancy.position.department',
            'educations',
            'trainings',
            'experiences',
            'languages',
            'references',
            'skills',
            'health',
            'documents'
        ]);

        // Load interviews related to this applicant
        $this->interviews = Interview::whereIn('application_id', $this->applicant->applications->pluck('id')->toArray())->get();

        // Load job offers related to this applicant
        $this->offers = JobOffer::whereIn('application_id', $this->applicant->applications->pluck('id')->toArray())->get();

        // Check if applicant has eligible applications for job offers
        $this->checkEligibleApplications();

        $this->channels = Channel::all();
        $this->areas = Area::all();
    }

    // Check for applications eligible for job offers
    private function checkEligibleApplications()
    {
        $eligibleStatuses = ['Shortlisted', 'Interview', 'Interview Passed'];

        $this->eligibleApplications = $this->applicant->applications
            ->filter(function ($application) use ($eligibleStatuses) {
                return in_array($application->status, $eligibleStatuses);
            });

        $this->canCreateOffer = $this->eligibleApplications->count() > 0;
    }

    // Application management
    public function openNewApplicationModal()
    {
        // Get vacancies that are open and that the applicant hasn't applied to yet
        $appliedVacancyIds = $this->applicant->applications->pluck('vacancy_id')->toArray();
        $this->referedByOptions = Employee::current()->get();
        $this->availableVacancies = Vacancy::where('status', 'open')
            ->whereNotIn('id', $appliedVacancyIds)
            ->with('position.department')
            ->get();

        $this->showNewApplicationModal = true;
    }

    public function closeNewApplicationModal()
    {
        $this->showNewApplicationModal = false;
        $this->vacancyId = null;
        $this->applicationNotes = null;
        $this->referedBy = null;
        $this->resetValidation();
    }

    public function createApplication()
    {
        $this->validate([
            'vacancyId' => 'required|exists:vacancies,id',
            'referedBy' => 'nullable|exists:employees,id',
        ]);

        try {
            // Use the applyForVacancy method from the Applicant model
            $this->applicant->applyForVacancy(
                $this->vacancyId,
                $this->applicationNotes,
                $this->referedBy
            );

            $this->alert('success', 'Application submitted successfully');
            $this->closeNewApplicationModal();

            // Refresh applicant data
            $this->applicant->refresh();
            $this->checkEligibleApplications();
        } catch (Exception $e) {
            $this->alert('error', 'Failed to submit application: ' . $e->getMessage());
        }
    }

    // Interview management
    public function openNewInterviewModal($applicationId)
    {
        $this->selectedApplicationId = $applicationId;
        $this->selectedApplication = Application::with('vacancy.position.department')->find($applicationId);

        // Set default values
        $this->interviewDate = now()->addDays(3)->format('Y-m-d');
        $this->interviewTime = now()->format('H:i');
        $this->interviewType = Interview::TYPE_IN_PERSON;
        $this->interviewLocation = null;
        $this->interviewNotes = null;

        $this->showNewInterviewModal = true;
    }

    public function closeNewInterviewModal()
    {
        $this->showNewInterviewModal = false;
        $this->selectedApplicationId = null;
        $this->selectedApplication = null;
        $this->interviewDate = null;
        $this->interviewTime = null;
        $this->interviewType = null;
        $this->interviewLocation = null;
        $this->interviewNotes = null;
        $this->resetValidation();
    }

    public function scheduleInterview()
    {
        $this->validate([
            'interviewDate' => 'required|date|after_or_equal:today',
            'interviewTime' => 'required',
            'interviewType' => 'required|string|max:255',
            'interviewLocation' => 'nullable|string|max:255',
            'interviewNotes' => 'nullable|string|max:500',
        ]);

        try {
            // Create DateTime object from the date and time
            $interviewDateTime = new \DateTime($this->interviewDate . ' ' . $this->interviewTime);
            
            // Use the application's createInterview method
            $this->selectedApplication->createInterview(
                Auth::id(),
                $interviewDateTime,
                $this->interviewType,
                $this->interviewLocation,
                $this->interviewNotes
            );

            $this->alert('success', 'Interview scheduled successfully');
            $this->closeNewInterviewModal();

            // Refresh data
            $this->applicant->refresh();
            $this->interviews = Interview::whereIn('application_id', $this->applicant->applications->pluck('id')->toArray())->get();
        } catch (Exception $e) {
            $this->alert('error', 'Failed to schedule interview: ' . $e->getMessage());
        }
    }

    // Interview Feedback
    public function openFeedbackModal($interviewId)
    {
        $this->selectedInterview = Interview::with('application.vacancy.position.department')->find($interviewId);

        // Pre-populate fields if feedback exists
        if ($this->selectedInterview->result) {
            $this->interviewResult = $this->selectedInterview->result;
            $this->rating = $this->selectedInterview->rating;
            $this->strengths = $this->selectedInterview->strengths;
            $this->weaknesses = $this->selectedInterview->weaknesses;
            $this->feedbackNotes = $this->selectedInterview->feedback;
            $this->nextStep = $this->selectedInterview->next_step;
        } else {
            $this->reset(['interviewResult', 'rating', 'strengths', 'weaknesses', 'feedbackNotes', 'nextStep']);
        }

        $this->showFeedbackModal = true;
    }

    public function closeFeedbackModal()
    {
        $this->showFeedbackModal = false;
        $this->selectedInterview = null;
        $this->reset(['interviewResult', 'rating', 'strengths', 'weaknesses', 'feedbackNotes', 'nextStep']);
        $this->resetValidation();
    }

    public function saveInterviewFeedback()
    {
        $this->validate([
            'interviewResult' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:10',
            'strengths' => 'nullable|string|max:500',
            'weaknesses' => 'nullable|string|max:500',
            'feedbackNotes' => 'nullable|string|max:1000',
            'nextStep' => 'nullable|string|max:255',
            'newApplicationStatus' => 'nullable|string|max:255',
        ]);

        try {
            $this->selectedInterview->addFeedback(
                Auth::id(),
                $this->interviewResult,
                $this->rating,
                $this->strengths,
                $this->weaknesses,
                $this->feedbackNotes
            ); 
            
            $this->selectedInterview->complete();

            if ($this->newApplicationStatus) {
                $this->selectedInterview->application->updateStatus($this->newApplicationStatus);
            }

            $this->alert('success', 'Interview feedback saved successfully');
            $this->closeFeedbackModal();

            // Refresh data
            $this->applicant->refresh();
            $this->interviews = Interview::whereIn('application_id', $this->applicant->applications->pluck('id')->toArray())->get();
            $this->checkEligibleApplications();
        } catch (AppException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'Internal server error');
        }
    }

    // Job Offer Management
    public function openNewOfferModal()
    {
        $this->reset(['applicationId', 'offeredSalary', 'proposedStartDate', 'expiryDate', 'benefits', 'specialTerms', 'offerNotes']);

        // Pre-populate dates
        $this->proposedStartDate = now()->addDays(14)->format('Y-m-d');
        $this->expiryDate = now()->addDays(7)->format('Y-m-d');

        $this->showNewOfferModal = true;
    }

    public function closeNewOfferModal()
    {
        $this->showNewOfferModal = false;
        $this->reset(['applicationId', 'offeredSalary', 'proposedStartDate', 'expiryDate', 'benefits', 'specialTerms', 'offerNotes']);
        $this->resetValidation();
    }

    public function createOffer()
    {
        $this->validate([
            'applicationId' => 'required|exists:applications,id',
            'offeredSalary' => 'required|numeric|min:0',
            'proposedStartDate' => 'required|date|after:today',
            'expiryDate' => 'required|date|after:today|before:proposedStartDate',
            'benefits' => 'required|string|max:1000',
            'specialTerms' => 'nullable|string|max:1000',
            'offerNotes' => 'nullable|string|max:500',
        ]);

        try {
            $offer = new JobOffer([
                'application_id' => $this->applicationId,
                'offered_salary' => $this->offeredSalary,
                'proposed_start_date' => $this->proposedStartDate,
                'expiry_date' => $this->expiryDate,
                'benefits' => $this->benefits,
                'special_terms' => $this->specialTerms,
                'notes' => $this->offerNotes,
                'status' => 'Draft',
            ]);

            $offer->save();

            $this->alert('success', 'Job offer created successfully');
            $this->closeNewOfferModal();

            // Refresh data
            $this->offers = JobOffer::whereIn('application_id', $this->applicant->applications->pluck('id')->toArray())->get();
        } catch (Exception $e) {
            $this->alert('error', 'Failed to create job offer: ' . $e->getMessage());
        }
    }

    public function viewOffer($offerId)
    {
        $this->selectedOffer = JobOffer::with('application.vacancy.position.department')->find($offerId);
        $this->showViewOfferModal = true;
    }

    public function closeViewOfferModal()
    {
        $this->showViewOfferModal = false;
        $this->selectedOffer = null;
    }

    public function sendOffer($offerId)
    {
        try {
            $offer = JobOffer::find($offerId);
            $offer->status = 'Sent';
            $offer->offer_date = now();
            $offer->save();

            // Update application status
            $application = $offer->application;
            $application->status = 'Offer';
            $application->save();

            $this->alert('success', 'Job offer sent successfully');
            $this->closeViewOfferModal();

            // Refresh data
            $this->offers = JobOffer::whereIn('application_id', $this->applicant->applications->pluck('id')->toArray())->get();
            $this->applicant->refresh();
        } catch (Exception $e) {
            $this->alert('error', 'Failed to send job offer: ' . $e->getMessage());
        }
    }

    public function acceptOffer($offerId)
    {
        try {
            $offer = JobOffer::find($offerId);
            $offer->status = 'Accepted';
            $offer->response_date = now();
            $offer->save();

            // Update application status
            $application = $offer->application;
            $application->status = 'Offer Accepted';
            $application->save();

            $this->alert('success', 'Job offer marked as accepted');

            // Refresh data
            $this->offers = JobOffer::whereIn('application_id', $this->applicant->applications->pluck('id')->toArray())->get();
            $this->applicant->refresh();
        } catch (Exception $e) {
            $this->alert('error', 'Failed to process offer acceptance: ' . $e->getMessage());
        }
    }

    public function rejectOffer($offerId)
    {
        try {
            $offer = JobOffer::find($offerId);
            $offer->status = 'Rejected';
            $offer->response_date = now();
            $offer->save();

            // Update application status
            $application = $offer->application;
            $application->status = 'Offer Rejected';
            $application->save();

            $this->alert('success', 'Job offer marked as rejected');

            // Refresh data
            $this->offers = JobOffer::whereIn('application_id', $this->applicant->applications->pluck('id')->toArray())->get();
            $this->applicant->refresh();
        } catch (Exception $e) {
            $this->alert('error', 'Failed to process offer rejection: ' . $e->getMessage());
        }
    }

    // Toggle functions for modals
    public function toggleMainInfo()
    {
        $this->showMainInfoModal = !$this->showMainInfoModal;
        if ($this->showMainInfoModal) {
            $this->editMainInfo = $this->applicant->only([
                'first_name',
                'middle_name',
                'last_name',
                'nationality',
                'email',
                'phone',
                'home_phone',
                'area_id',
                'channel_id',
                'address',
                'social_number',
                'birth_date',
                'gender',
                'marital_status',
                'military_status',
            ]);
            $this->cvResume = $this->applicant->cv_url;
        }
    }

    //Education modal controls
    public function toggleEducation()
    {
        $this->showEducationModal = !$this->showEducationModal;
        if ($this->showEducationModal) {
            foreach ($this->applicant->educations as $education) {
                $this->educations[] = [
                    'school_name' => $education->school_name,
                    'degree' => $education->degree,
                    'field_of_study' => $education->field_of_study,
                    'start_date' => $education->start_date->format('Y-m-d'),
                    'end_date' => $education->end_date->format('Y-m-d'),
                ];
            }
        }
    }

    public function addEducation()
    {
        $this->educations[] = [
            'school_name' => '',
            'degree' => '',
        ];
    }

    public function removeEducation($index)
    {
        unset($this->educations[$index]);
    }

    //Training modal controls
    public function toggleTraining()
    {
        $this->showTrainingModal = !$this->showTrainingModal;
        if ($this->showTrainingModal) {
            foreach ($this->applicant->trainings as $training) {
                $this->trainings[] = [
                    'name' => $training->name,
                    'sponsor' => $training->sponsor,
                    'duration' => $training->duration,
                    'start_date' => $training->start_date->format('Y-m-d'),
                    'end_date' => $training->end_date->format('Y-m-d'),
                ];
            }
        }
    }

    public function addTraining()
    {
        $this->trainings[] = [
            'name' => '',
            'sponsor' => '',
            'duration' => '',
            'start_date' => '',
        ];
    }

    public function removeTraining($index)
    {
        unset($this->trainings[$index]);
    }


    //Experience modal controls
    public function toggleExperience()
    {
        $this->showExperienceModal = !$this->showExperienceModal;
        if ($this->showExperienceModal) {
            foreach ($this->applicant->experiences as $experience) {
                $this->experiences[] = [
                    'company_name' => $experience->company_name,
                    'position' => $experience->position,
                    'start_date' => $experience->start_date->format('Y-m-d'),
                    'end_date' => $experience->end_date->format('Y-m-d'),
                    'salary' => $experience->salary,
                    'reason_for_leaving' => $experience->reason_for_leaving,
                ];
            }
        }
    }

    public function addExperience()
    {
        $this->experiences[] = [
            'company_name' => '',
            'position' => '',
            'start_date' => '',
            'salary' => '',
        ];
    }

    public function removeExperience($index)
    {
        unset($this->experiences[$index]);
    }

    //Language modal controls
    public function toggleLanguages()
    {
        $this->showLanguageModal = !$this->showLanguageModal;
        if ($this->showLanguageModal) {
            $this->languages = $this->applicant->languages->toArray();
        }
    }

    public function addLanguage()
    {
        $this->languages[] = [
            'language' => '',
            'speaking_level' => '',
            'writing_level' => '',
            'reading_level' => '',
        ];
    }

    public function removeLanguage($index)
    {
        unset($this->languages[$index]);
    }

    //Reference modal controls
    public function toggleReferences()
    {
        $this->showReferenceModal = !$this->showReferenceModal;
        if ($this->showReferenceModal) {
            $this->references = $this->applicant->references->toArray();
        }
    }

    public function addReference()
    {
        $this->references[] = [
            'name' => '',
            'phone' => '',
            'email' => '',
            'address' => '',
            'relationship' => '',
        ];
    }

    public function removeReference($index)
    {
        unset($this->references[$index]);
    }

    //Skills modal controls
    public function toggleSkills()
    {
        $this->showSkillModal = !$this->showSkillModal;
        if ($this->showSkillModal) {
            $this->skills = $this->applicant->skills->toArray();
        }
    }

    public function addSkill()
    {
        $this->skills[] = [
            'skill' => '',
            'level' => '',
        ];
    }

    public function removeSkill($index)
    {
        unset($this->skills[$index]);
    }

    //Health modal controls
    public function toggleHealth()
    {
        $this->showHealthModal = !$this->showHealthModal;
        if ($this->showHealthModal) {
            $this->hasHealthIssues = $this->applicant->health?->has_health_issues ?? false;
            $this->healthIssues = $this->applicant->health?->health_issues;
        }
    }

    // Save functions
    public function saveMainInfo()
    {
        $this->validate([
            'editMainInfo.first_name' => 'required|string|max:255',
            'editMainInfo.last_name' => 'required|string|max:255',
            'editMainInfo.email' => 'required|email|max:255',
            'editMainInfo.phone' => 'required|string|max:20',
            'editMainInfo.home_phone' => 'nullable|string|max:20',
            'editMainInfo.nationality' => 'nullable|string|max:100',
            'editMainInfo.address' => 'nullable|string|max:500',
            'editMainInfo.social_number' => 'nullable|string|max:50',
            'editMainInfo.birth_date' => 'nullable|date',
            'editMainInfo.gender' => 'nullable|in:Male,Female',
            'editMainInfo.marital_status' => 'nullable|in:Single,Married,Divorced,Widowed',
            'editMainInfo.military_status' => 'nullable|in:Exempted,Drafted,Completed',
            'cvResume' => 'nullable|file|mimes:pdf|max:10048',
        ], [
            'editMainInfo.first_name.required' => 'First name is required',
            'editMainInfo.last_name.required' => 'Last name is required',
            'editMainInfo.email.required' => 'Email is required',
            'editMainInfo.phone.required' => 'Phone is required',
            'editMainInfo.home_phone.required' => 'Home phone is required',
            'editMainInfo.nationality.required' => 'Nationality is required',
            'editMainInfo.address.required' => 'Address is required',
            'editMainInfo.social_number.required' => 'Social number is required',
            'editMainInfo.birth_date.required' => 'Birth date is required',
            'editMainInfo.gender.required' => 'Gender is required',
            'editMainInfo.marital_status.required' => 'Marital status is required',
            'editMainInfo.military_status.required' => 'Military status is required',
            'cvResume.mimes' => 'CV/Resume must be a PDF file',
            'cvResume.max' => 'CV/Resume must be less than 10MB',
        ]);

        try {
            $this->applicant->updatePersonalInfo($this->editMainInfo);
            if ($this->cvResume) {
                $filePath = $this->cvResume->store(Document::APPLICANT_DOCUMENTS, 's3');
                $this->applicant->updateCv($filePath);
            }
            $this->alert('success', 'Personal information updated successfully');
            $this->toggleMainInfo();
            $this->applicant->refresh();
        } catch (Exception $e) {
            $this->alert('error', 'Failed to update personal information: ' . $e->getMessage());
        }
    }

    public function saveEducations()
    {
        $this->validate([
            'educations.*.school_name' => 'required|string|max:255',
            'educations.*.degree' => 'required|string|max:255',
            'educations.*.field_of_study' => 'required|string|max:255',
            'educations.*.start_date' => 'required|date',
            'educations.*.end_date' => 'required|date|after:educations.*.start_date',
        ], [
            'educations.*.school_name.required' => 'School name is required',
            'educations.*.degree.required' => 'Degree is required',
            'educations.*.field_of_study.required' => 'Field of study is required',
            'educations.*.start_date.required' => 'Start date is required',
            'educations.*.end_date.required' => 'End date is required',
            'educations.*.end_date.after' => 'End date must be after start date',
        ]);

        try {
            $this->applicant->setEducations($this->educations);
            $this->alert('success', 'Education records updated successfully');
            $this->toggleEducation();
            $this->applicant->refresh();
        } catch (Exception $e) {
            $this->alert('error', 'Failed to update education records: ' . $e->getMessage());
        }
    }

    public function saveTrainings()
    {
        $this->validate([
            'trainings.*.name' => 'required|string|max:255',
            'trainings.*.sponsor' => 'required|string|max:255',
            'trainings.*.duration' => 'required|string|max:50',
            'trainings.*.start_date' => 'required|date',
        ], [
            'trainings.*.name.required' => 'Name is required',
            'trainings.*.sponsor.required' => 'sponsor is required',
            'trainings.*.duration.required' => 'Duration is required',
            'trainings.*.start_date.required' => 'Start date is required',
        ]);

        try {
            $this->applicant->setTrainings($this->trainings);
            $this->alert('success', 'Training records updated successfully');
            $this->toggleTraining();
            $this->applicant->refresh();
        } catch (Exception $e) {
            $this->alert('error', 'Failed to update training records: ' . $e->getMessage());
        }
    }

    public function saveExperiences()
    {
        $this->validate([
            'experiences.*.company_name' => 'required|string|max:255',
            'experiences.*.position' => 'required|string|max:255',
            'experiences.*.start_date' => 'required|date',
            'experiences.*.end_date' => 'nullable|date|after:experiences.*.start_date',
            'experiences.*.salary' => 'required|numeric|min:0',
            'experiences.*.reason_for_leaving' => 'nullable|string|max:500',
        ], [
            'experiences.*.company_name.required' => 'Company name is required',
            'experiences.*.position.required' => 'Position is required',
            'experiences.*.start_date.required' => 'Start date is required',
            'experiences.*.end_date.required' => 'End date is required',
            'experiences.*.end_date.after' => 'End date must be after start date',
            'experiences.*.salary.required' => 'Salary is required',
            'experiences.*.salary.min' => 'Salary must be at least 0',
            'experiences.*.reason_for_leaving.required' => 'Reason for leaving is required',
        ]);

        try {
            $this->applicant->setExperiences($this->experiences);
            $this->alert('success', 'Experience records updated successfully');
            $this->toggleExperience();
            $this->applicant->refresh();
        } catch (Exception $e) {
            $this->alert('error', 'Failed to update experience records: ' . $e->getMessage());
        }
    }

    public function saveLanguages()
    {
        $this->validate([
            'languages.*.language' => 'required|string|max:100',
            'languages.*.speaking_level' => 'required|string|max:50',
            'languages.*.writing_level' => 'nullable|string|max:50',
            'languages.*.reading_level' => 'nullable|string|max:50',
        ], [
            'languages.*.language.required' => 'Language Name is required',
            'languages.*.speaking_level.required' => 'Speaking Level is required',
            'languages.*.writing_level.required' => 'Writing Level is required',
            'languages.*.reading_level.required' => 'Reading Level is required',
        ]);

        try {
            $this->applicant->setLanguages($this->languages);
            $this->alert('success', 'Language records updated successfully');
            $this->toggleLanguages();
            $this->applicant->refresh();
        } catch (Exception $e) {
            $this->alert('error', 'Failed to update language records: ' . $e->getMessage());
        }
    }

    public function saveReferences()
    {
        $this->validate([
            'references.*.name' => 'required|string|max:255',
            'references.*.phone' => 'required|string|max:30',
            'references.*.email' => 'nullable|email|max:255',
            'references.*.address' => 'nullable|string|max:500',
            'references.*.relationship' => 'nullable|string|max:100',
        ], [
            'references.*.name.required' => 'Name is required',
            'references.*.phone.required' => 'Phone is required',
            'references.*.email.required' => 'Email is required',
            'references.*.address.required' => 'Address is required',
            'references.*.relationship.required' => 'Relationship is required',
        ]);

        try {
            $this->applicant->setReferences($this->references);
            $this->alert('success', 'Reference records updated successfully');
            $this->toggleReferences();
            $this->applicant->refresh();
        } catch (Exception $e) {
            $this->alert('error', 'Failed to update reference records: ' . $e->getMessage());
        }
    }

    public function saveSkills()
    {
        $this->validate([
            'skills.*.skill' => 'required|string|max:255',
            'skills.*.level' => 'required|string|max:50',
        ], [
            'skills.*.skill.required' => 'Name is required',
            'skills.*.level.required' => 'Proficiency is required',
        ]);

        try {
            $this->applicant->setSkills($this->skills);
            $this->alert('success', 'Skills updated successfully');
            $this->toggleSkills();
            $this->applicant->refresh();
        } catch (Exception $e) {
            $this->alert('error', 'Failed to update skills: ' . $e->getMessage());
        }
    }

    public function saveHealth()
    {
        $this->validate([
            'hasHealthIssues' => 'required|boolean',
            'healthIssues' => 'nullable|string|max:500',
        ], [
            'hasHealthIssues.required' => 'Health issues are required',
            'healthIssues.string' => 'Health issues must be a string',
            'healthIssues.max' => 'Health issues must be less than 500 characters',
        ]);

        try {
            $this->applicant->setHealth($this->hasHealthIssues, $this->healthIssues);
            $this->alert('success', 'Health information updated successfully');
            $this->toggleHealth();
            $this->applicant->refresh();
        } catch (Exception $e) {
            $this->alert('error', 'Failed to update health information: ' . $e->getMessage());
        }
    }

    public function uploadImage()
    {
        $this->validate([
            'newImage' => 'required|image|max:1024', // 1MB Max
        ], [
            'newImage.required' => 'Image is required',
            'newImage.image' => 'Image must be an image',
            'newImage.max' => 'Image must be less than 1MB',
        ]);

        try {
            $path = $this->newImage->store('applicant-images', 'public');
            $this->applicant->setImage($path);
            $this->alert('success', 'Profile image updated successfully');
            $this->newImage = null;
            $this->applicant->refresh();
        } catch (Exception $e) {
            $this->alert('error', 'Failed to update profile image: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.recruitment.applicant-show');
    }

    // Interview Management - Set Interviewers
    public function openSetInterviewersModal($interviewId)
    {
        $this->selectedInterview = Interview::find($interviewId);
        $this->interviewers = User::hrOrAdmin()->get();
        $this->selectedInterviewers = $this->selectedInterview->interviewers->pluck('id')->toArray();
        $this->showSetInterviewersModal = true;
    }

    public function closeSetInterviewersModal()
    {
        $this->showSetInterviewersModal = false;
        $this->selectedInterview = null;
        $this->selectedInterviewers = [];
        $this->resetValidation();
    }

    public function saveInterviewers()
    {
        $this->validate([
            'selectedInterviewers' => 'required|array|min:1',
        ]);

        try {
            $this->selectedInterview->setInterviewers($this->selectedInterviewers);
            $this->alert('success', 'Interviewers assigned successfully');
            $this->closeSetInterviewersModal();
            
            // Refresh interviews data
            $this->interviews = Interview::whereIn('application_id', $this->applicant->applications->pluck('id')->toArray())->get();
        } catch (Exception $e) {
            $this->alert('error', 'Failed to assign interviewers: ' . $e->getMessage());
        }
    }

    // Interview Management - Reschedule
    public function openRescheduleModal($interviewId)
    {
        $this->selectedInterview = Interview::find($interviewId);
        
        // Pre-populate with current values
        $this->newInterviewDate = $this->selectedInterview->date->format('Y-m-d');
        $this->newInterviewTime = $this->selectedInterview->date->format('H:i');
        $this->newInterviewType = $this->selectedInterview->type;
        $this->newInterviewLocation = $this->selectedInterview->location;
        $this->newInterviewZoomLink = $this->selectedInterview->zoom_link;
        
        $this->showRescheduleModal = true;
    }

    public function closeRescheduleModal()
    {
        $this->showRescheduleModal = false;
        $this->selectedInterview = null;
        $this->newInterviewDate = null;
        $this->newInterviewTime = null;
        $this->newInterviewType = null;
        $this->newInterviewLocation = null;
        $this->newInterviewZoomLink = null;
        $this->resetValidation();
    }

    public function rescheduleInterview()
    {
        $this->validate([
            'newInterviewDate' => 'required|date|after_or_equal:today',
            'newInterviewTime' => 'required',
            'newInterviewType' => 'required|in:' . implode(',', Interview::INTERVIEW_TYPES),
            'newInterviewLocation' => 'nullable|string|max:255',
            'newInterviewZoomLink' => 'nullable|url|max:255',
        ]);

        try {
            // Create DateTime object from the date and time
            $newDateTime = new \DateTime($this->newInterviewDate . ' ' . $this->newInterviewTime);
            
            $this->selectedInterview->reschedule(
                $newDateTime,
                $this->newInterviewType,
                $this->newInterviewLocation,
                $this->newInterviewZoomLink
            );
            
            $this->alert('success', 'Interview rescheduled successfully');
            $this->closeRescheduleModal();
            
            // Refresh interviews data
            $this->interviews = Interview::whereIn('application_id', $this->applicant->applications->pluck('id')->toArray())->get();
        } catch (Exception $e) {
            $this->alert('error', 'Failed to reschedule interview: ' . $e->getMessage());
        }
    }

    // Interview Management - Cancel
    public function openCancelModal($interviewId)
    {
        $this->selectedInterview = Interview::find($interviewId);
        $this->cancelReason = null;
        $this->showCancelModal = true;
    }

    public function closeCancelModal()
    {
        $this->showCancelModal = false;
        $this->selectedInterview = null;
        $this->cancelReason = null;
        $this->resetValidation();
    }

    public function cancelInterview()
    {
        $this->validate([
            'cancelReason' => 'nullable|string|max:500',
        ]);

        try {
            $this->selectedInterview->cancel();
            
            // Add a note with the cancellation reason if provided
            if ($this->cancelReason) {
                $this->selectedInterview->addNote('Cancellation Reason', $this->cancelReason);
            }
            
            $this->alert('success', 'Interview cancelled successfully');
            $this->closeCancelModal();
            
            // Refresh interviews data
            $this->interviews = Interview::whereIn('application_id', $this->applicant->applications->pluck('id')->toArray())->get();
        } catch (Exception $e) {
            $this->alert('error', 'Failed to cancel interview: ' . $e->getMessage());
        }
    }

    // Interview Management - Complete
    public function openCompleteModal($interviewId)
    {
        $this->selectedInterview = Interview::find($interviewId);
        $this->showCompleteModal = true;
    }

    public function closeCompleteModal()
    {
        $this->showCompleteModal = false;
        $this->selectedInterview = null;
        $this->resetValidation();
    }

    public function completeInterview()
    {
        try {
            $this->selectedInterview->complete();
            $this->alert('success', 'Interview marked as completed');
            $this->closeCompleteModal();
            
            // Optionally redirect to feedback form
            $this->openFeedbackModal($this->selectedInterview->id);
            
            // Refresh interviews data
            $this->interviews = Interview::whereIn('application_id', $this->applicant->applications->pluck('id')->toArray())->get();
        } catch (Exception $e) {
            $this->alert('error', 'Failed to complete interview: ' . $e->getMessage());
        }
    }

    // Interview Management - Add Note
    public function openAddNoteModal($interviewId)
    {
        $this->selectedInterview = Interview::find($interviewId);
        $this->noteTitle = null;
        $this->noteContent = null;
        $this->showAddNoteModal = true;
    }

    public function closeAddNoteModal()
    {
        $this->showAddNoteModal = false;
        $this->selectedInterview = null;
        $this->noteTitle = null;
        $this->noteContent = null;
        $this->resetValidation();
    }

    public function addInterviewNote()
    {
        $this->validate([
            'noteTitle' => 'required|string|max:255',
            'noteContent' => 'nullable|string|max:1000',
        ]);

        try {
            $this->selectedInterview->addNote($this->noteTitle, $this->noteContent);
            $this->alert('success', 'Note added successfully');
            $this->closeAddNoteModal();
            
            // Refresh interviews data
            $this->interviews = Interview::whereIn('application_id', $this->applicant->applications->pluck('id')->toArray())->get();
        } catch (Exception $e) {
            $this->alert('error', 'Failed to add note: ' . $e->getMessage());
        }
    }

    // Interview Management - Update Status
    public function openUpdateStatusModal($interviewId)
    {
        $this->selectedInterview = Interview::find($interviewId);
        $this->newInterviewStatus = $this->selectedInterview->status;
        $this->showUpdateStatusModal = true;
    }

    public function closeUpdateStatusModal()
    {
        $this->showUpdateStatusModal = false;
        $this->selectedInterview = null;
        $this->newInterviewStatus = null;
        $this->resetValidation();
    }

    public function updateInterviewStatus()
    {
        $this->validate([
            'newInterviewStatus' => 'required|string|in:' . implode(',', Interview::INTERVIEW_STATUSES),
        ]);

        try {
            $this->selectedInterview->updateStatus($this->newInterviewStatus);
            $this->alert('success', 'Interview status updated successfully');
            $this->closeUpdateStatusModal();
            
            // Refresh interviews data
            $this->interviews = Interview::whereIn('application_id', $this->applicant->applications->pluck('id')->toArray())->get();
        } catch (Exception $e) {
            $this->alert('error', 'Failed to update interview status: ' . $e->getMessage());
        }
    }

    /**
     * Open the show feedbacks modal
     */
    public function openShowFeedbacksModal($interviewId)
    {
        $this->resetValidation();
        $this->selectedInterview = Interview::with(['feedbacks.user'])->find($interviewId);
        $this->showFeedbacksHistoryModal = true;
    }

    /**
     * Close the show feedbacks modal
     */
    public function closeShowFeedbacksModal()
    {
        $this->showFeedbacksHistoryModal = false;
    }
}
