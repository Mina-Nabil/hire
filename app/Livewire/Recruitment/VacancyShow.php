<?php

namespace App\Livewire\Recruitment;

use App\Exceptions\AppException;
use App\Models\Hierarchy\Position;
use App\Models\Recruitment\Applicants\Applicant;
use App\Models\Recruitment\Applicants\Application;
use App\Models\Recruitment\Interviews\Interview;
use App\Models\Recruitment\Interviews\InterviewFeedback;
use App\Models\Recruitment\JobOffers\JobOffer;
use App\Models\Recruitment\Vacancies\BaseQuestion;
use App\Models\Recruitment\Vacancies\Vacancy;
use App\Models\Recruitment\Vacancies\VacancyQuestion;
use App\Models\Recruitment\Vacancies\VacancySlot;
use App\Models\Users\User;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class VacancyShow extends Component
{
    use WithPagination, AlertFrontEnd;

    //page data
    public $vacancyId;
    public $section = 'info';
    public $positions;
    public $users;
    public $search = '';

    // Vacancy section
    public $editVacancyModal = false;
    public $positionId;
    public $assignedTo;
    public $hiringManagerId;
    public $hrManagerId;
    public $vacancyType;
    public $vacancyStatus;
    public $closingDate;
    public $jobResponsibilities;
    public $arabicJobResponsibilities;
    public $jobQualifications;
    public $arabicJobQualifications;
    public $jobBenefits;
    public $arabicJobBenefits;
    public $jobSalary;

    // Questions section
    public $questions = [];
    public $questionTypes = [];

    // Slots section
    public $slots = [];
    
    // Interview Management Properties (similar to ApplicantShow)
    public $interviews;
    public $offers;
    
    // Selected Interview
    public $selectedInterview;
    public $showFeedbackModal = false;
    public $showFeedbacksHistoryModal = false;
    public $interviewResult;
    public $rating;
    public $strengths;
    public $weaknesses;
    public $feedbackNotes;
    public $nextStep;
    public $newApplicationStatus;

    // View Application Modal
    public $showApplicationModal = false;
    
    // Interview Management Modals
    public $showNewInterviewModal = false;
    public $selectedApplicationId;
    public $selectedApplication;
    public $interviewDate;
    public $interviewTime;
    public $interviewType;
    public $interviewLocation;
    public $interviewNotes;
    public $showSetInterviewersModal = false;
    public $interviewers = [];
    public $selectedInterviewers = [];
    public $interviewResults = InterviewFeedback::RESULTS;
    
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
    public $interviewTypes = Interview::INTERVIEW_TYPES;
    public $interviewStatuses = Interview::INTERVIEW_STATUSES;
    public $applicationStatuses = Application::APPLICATION_STATUSES;

    // Add these properties after other modal-related properties
    public $showNewOfferModal = false;
    public $offeredSalary;
    public $proposedStartDate;
    public $expiryDate;
    public $benefits;
    public $offerNotes;
    public $hrApproved = false;
    public $hiringManagerApproved = false;

    // Add these properties after other modal-related properties
    public $showEditOfferModal = false;
    public $editOfferedSalary;
    public $editProposedStartDate;
    public $editExpiryDate;
    public $editBenefits;
    public $editOfferNotes;
    public $selectedOffer;

    // Add these properties after other modal-related properties
    public $showAcceptOfferModal = false;
    public $showRejectOfferModal = false;
    public $offerResponseNotes;

    protected $listeners = ['dateSelected' => 'onDateSelected'];

    public function changeSection($section)
    {
        $this->section = $section;
        if($section === 'manage'){
            $this->loadVacancyControls();
        }
    }

    public function mount($id)
    {
        $this->vacancyId = $id;
        $this->questionTypes = BaseQuestion::TYPES;
        $this->positions = Position::all();
        $this->users = User::hrOrAdmin()->get();
        $this->interviewers = User::hrOrAdmin()->get();
        
        // Get interviews for this vacancy
        $this->refreshInterviews();
    }
    
    private function refreshInterviews()
    {
        $this->interviews = Interview::byVacancyId($this->vacancyId)->get();
        $this->offers = JobOffer::whereHas('application', function($q) {
            $q->where('vacancy_id', $this->vacancyId);
        })->get();
    }
    
    public function loadVacancyControls()
    {
        $vacancy = Vacancy::with(['vacancy_questions', 'vacancy_slots'])->find($this->vacancyId);
        $this->positionId = $vacancy->position_id;
        $this->assignedTo = $vacancy->assigned_to;
        $this->hiringManagerId = $vacancy->hiring_manager_id;
        $this->hrManagerId = $vacancy->hr_manager_id;
        $this->vacancyType = $vacancy->type;
        $this->vacancyStatus = $vacancy->status;
        $this->closingDate = $vacancy->closing_date ? $vacancy->closing_date->format('Y-m-d') : null;
        $this->jobResponsibilities = $vacancy->job_responsibilities;
        $this->arabicJobResponsibilities = $vacancy->arabic_job_responsibilities;
        $this->jobQualifications = $vacancy->job_qualifications;
        $this->arabicJobQualifications = $vacancy->arabic_job_qualifications;
        $this->jobBenefits = $vacancy->job_benefits;
        $this->arabicJobBenefits = $vacancy->arabic_job_benefits;
        $this->jobSalary = $vacancy->job_salary;

        // Load questions
        $this->questions = [];
        foreach ($vacancy->vacancy_questions as $question) {
            $this->questions[] = [
                'id' => $question->id,
                'question' => $question->question,
                'arabic_question' => $question->arabic_question,
                'type' => $question->type,
                'required' => $question->required,
                'options' => $question->options_array,
            ];
        }

        // If no questions, add an empty one
        if (empty($this->questions)) {
            $this->addQuestion();
        }

        // Load slots
        $this->slots = [];
        foreach ($vacancy->vacancy_slots as $slot) {
            $this->slots[] = [
                'id' => $slot->id,
                'date' => $slot->date->format('Y-m-d'),
                'start_time' => $slot->start_time->format('H:i'),
                'end_time' => $slot->end_time->format('H:i'),
            ];
        }

        // If no slots, add an empty one
        if (empty($this->slots)) {
            $this->addSlot();
        }
    }
    
    public function addQuestion()
    {
        $this->questions[] = [
            'question' => '',
            'arabic_question' => '',
            'type' => 'text',
            'required' => false,
            'options' => '',
        ];
    }

    public function removeQuestion($index)
    {
        if (isset($this->questions[$index])) {
            array_splice($this->questions, $index, 1);
        }

        if (count($this->questions) == 0) {
            $this->addQuestion();
        }
    }

    // Slot functions
    public function addSlot()
    {
        $this->slots[] = [
            'date' => Carbon::now()->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '17:00',
        ];
    }

    public function removeSlot($index)
    {
        if (isset($this->slots[$index])) {
            array_splice($this->slots, $index, 1);
        }

        if (count($this->slots) == 0) {
            $this->addSlot();
        }
    }
    
    public function updateVacancy()
    {
        
        $this->validate([
            'positionId' => 'required|exists:positions,id',
            'assignedTo' => 'required|exists:users,id',
            'hiringManagerId' => 'required|exists:users,id',
            'hrManagerId' => 'required|exists:users,id',
            'vacancyType' => 'required|in:full_time,part_time,temporary',
            'vacancyStatus' => 'required|in:open,closed',
            'jobResponsibilities' => 'nullable|string',
            'arabicJobResponsibilities' => 'nullable|string',
            'jobQualifications' => 'nullable|string',
            'arabicJobQualifications' => 'nullable|string',
            'jobBenefits' => 'nullable|string',
            'arabicJobBenefits' => 'nullable|string',
            'jobSalary' => 'nullable|string',
            'questions.*.question' => 'nullable|string',
            'questions.*.type' => 'required_if:questions.*.question,true|string',
            'questions.*.options' => 'nullable|string',
        ]);

        try {
            $vacancy = Vacancy::find($this->vacancyId);

            // Prepare vacancy data
            $data = [
                'position_id' => $this->positionId,
                'assigned_to' => $this->assignedTo,
                'hiring_manager_id' => $this->hiringManagerId,
                'hr_manager_id' => $this->hrManagerId,
                'type' => $this->vacancyType,
                'status' => $this->vacancyStatus,
                'job_responsibilities' => $this->jobResponsibilities,
                'arabic_job_responsibilities' => $this->arabicJobResponsibilities,
                'job_qualifications' => $this->jobQualifications,
                'arabic_job_qualifications' => $this->arabicJobQualifications,
                'job_benefits' => $this->jobBenefits,
                'arabic_job_benefits' => $this->arabicJobBenefits,
                'job_salary' => $this->jobSalary,
            ];

            // Process questions
            $questionsData = [];
            foreach ($this->questions as $question) {
                if (!empty($question['question'])) {
                    $questionData = [
                        'question' => $question['question'],
                        'arabic_question' => $question['arabic_question'] ?? null,
                        'type' => $question['type'],
                        'required' => isset($question['required']) ? true : false,
                        'options' => isset($question['options']) ? $question['options'] : null,
                    ];

                    if (isset($question['id'])) {
                        $questionData['id'] = $question['id'];
                    }

                    $questionsData[] = $questionData;
                }
            }

            // Process slots
            $slotsData = [];
            foreach ($this->slots as $slot) {
                if (!empty($slot['date']) && !empty($slot['start_time']) && !empty($slot['end_time'])) {
                    $slotData = [
                        'date' => $slot['date'],
                        'start_time' => $slot['start_time'],
                        'end_time' => $slot['end_time'],
                    ];

                    if (isset($slot['id'])) {
                        $slotData['id'] = $slot['id'];
                    }

                    $slotsData[] = $slotData;
                }
            }

            // Update vacancy with questions and slots
            $data['questions'] = $questionsData;
            $data['slots'] = $slotsData;
            $data['reset_questions'] = true;
            $data['reset_slots'] = true;

            $vacancy->updateVacancy($data);
            $this->alert('error', 'Failed to update vacancy. Please try again.');
            $this->alert('success', 'Vacancy updated successfully!');
        } catch (AppException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'Failed to update vacancy. Please try again.');
        }
    }
    
    // Date selected event handler
    public function onDateSelected($value, $name)
    {
        if ($name === 'closingDate') {
            $this->closingDate = $value;
        } else if (strpos($name, 'slots.') === 0 && strpos($name, '.date') !== false) {
            $parts = explode('.', $name);
            $index = $parts[1];
            $this->slots[$index]['date'] = $value;
        } else if ($name === 'newInterviewDate') {
            $this->newInterviewDate = $value;
        }
    }

    //show application modal
    public function openApplicationModal($applicantId)
    {
        $this->showApplicationModal = true;
        $this->selectedApplication = Application::where('applicant_id', $applicantId)->where('vacancy_id', $this->vacancyId)->first();
    }

    public function closeApplicationModal()
    {
        $this->showApplicationModal = false;
        $this->selectedApplication = null;
    }

    
    
    // Interview Management Functions

    // Interview management
      public function openNewInterviewModal($applicantId)
      {
        $application = Application::with('vacancy.position.department')->where('applicant_id', $applicantId)->where('vacancy_id', $this->vacancyId)->first();
          $this->selectedApplicationId = $application->id;
          $this->selectedApplication = $application;
  
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
              $this->mount($this->vacancyId);
  
          } catch (AppException $e) {
              $this->alert('error', $e->getMessage());
          } catch (Exception $e) {
              report($e);
              $this->alert('error', 'Failed to schedule interview: ' . $e->getMessage());
          }
      }
    
    // Interview Feedback
    public function openFeedbackModal($interviewId)
    {
        $this->selectedInterview = Interview::with('application.vacancy.position.department')->find($interviewId);

        // Pre-populate fields if feedback exists
        $userFeedback = $this->selectedInterview->feedbacks()->where('user_id', Auth::id())->first();
        if ($userFeedback) {
            $this->interviewResult = $userFeedback->result;
            $this->rating = $userFeedback->rating;
            $this->strengths = $userFeedback->strengths;
            $this->weaknesses = $userFeedback->weaknesses;
            $this->feedbackNotes = $userFeedback->feedback;
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
            
            if ($this->selectedInterview->status !== Interview::STATUS_COMPLETED) {
                $this->selectedInterview->complete();
            }

            if ($this->newApplicationStatus) {
                $this->selectedInterview->application->updateStatus($this->newApplicationStatus);
            }

            $this->alert('success', 'Interview feedback saved successfully');
            $this->closeFeedbackModal();

            // Refresh data
            $this->refreshInterviews();
        } catch (AppException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'Internal server error');
        }
    }

    public function openShowFeedbacksModal($interviewId)
    {
        $this->resetValidation();
        $this->selectedInterview = Interview::with(['feedbacks.user'])->find($interviewId);
        $this->showFeedbacksHistoryModal = true;
    }

    public function closeShowFeedbacksModal()
    {
        $this->showFeedbacksHistoryModal = false;
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
            $this->refreshInterviews();
        } catch (AppException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
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
            $this->refreshInterviews();
        } catch (AppException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
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
            $this->refreshInterviews();
        } catch (AppException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
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
            $this->refreshInterviews();
        } catch (AppException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
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
            $this->refreshInterviews();
        } catch (AppException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
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
            $this->refreshInterviews();
        } catch (AppException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'Failed to update interview status: ' . $e->getMessage());
        }
    }

    public function showApplicant($applicantId)
    {
        return $this->dispatch('openNewTab', route('recruitment.applicants.show', $applicantId));
    }

    // Add these functions after other modal-related functions
    public function openNewOfferModal($applicantId)
    {
        $this->selectedApplication = Application::with(['vacancy.position.department', 'applicant', 'feedbacks.user'])->where('applicant_id', $applicantId)->where('vacancy_id', $this->vacancyId)->first();

        if(!$this->selectedApplication) {
            $this->alert('error', 'Application not found');
            return;
        }
        
        // Check approvals
        $hiringManager = $this->selectedApplication->vacancy->hiring_manager;
        $hr = $this->selectedApplication->vacancy->hr_manager;
        
        foreach ($this->selectedApplication->feedbacks as $feedback) {
            if ($feedback->result == InterviewFeedback::RESULT_PASSED) {
                if ($feedback->user_id == $hiringManager->id) {
                    $this->hiringManagerApproved = true;
                } 
                if ($feedback->user_id == $hr->id) {
                    $this->hrApproved = true;
                }
            }
        }

        // Set default dates
        $this->proposedStartDate = now()->addMonth()->startOfMonth()->format('Y-m-d');
        $this->expiryDate = now()->addDays(14)->format('Y-m-d');
        
        $this->showNewOfferModal = true;
    }

    public function closeNewOfferModal()
    {
        $this->showNewOfferModal = false;
        $this->selectedApplication = null;
        $this->reset(['offeredSalary', 'proposedStartDate', 'expiryDate', 'benefits', 'offerNotes', 'hrApproved', 'hiringManagerApproved']);
        $this->resetValidation();
    }

    public function createOffer()
    {
        $this->validate([
            'offeredSalary' => 'required|numeric|min:0',
            'proposedStartDate' => 'required|date|after:today',
            'expiryDate' => 'required|date|after:today|before:proposedStartDate',
            'benefits' => 'required|string',
            'offerNotes' => 'nullable|string',
        ]);

        try {
            $this->selectedApplication->offer(
                $this->offeredSalary,
                new \DateTime($this->proposedStartDate),
                new \DateTime($this->expiryDate),
                $this->benefits,
                $this->offerNotes
            );

            $this->alert('success', 'Job offer created successfully');
            $this->closeNewOfferModal();

            // Refresh data
            $this->refreshInterviews();
        } catch (AppException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'Failed to create job offer: ' . $e->getMessage());
        }
    }

    public function openEditOfferModal($offerId)
    {
        $this->selectedOffer = JobOffer::find($offerId);
        
        if(!$this->selectedOffer) {
            $this->alert('error', 'Offer not found');
            return;
        }

        // Pre-populate the form
        $this->editOfferedSalary = $this->selectedOffer->offered_salary;
        $this->editProposedStartDate = $this->selectedOffer->proposed_start_date->format('Y-m-d');
        $this->editExpiryDate = $this->selectedOffer->expiry_date->format('Y-m-d');
        $this->editBenefits = $this->selectedOffer->benefits;
        $this->editOfferNotes = $this->selectedOffer->notes;
        
        $this->showEditOfferModal = true;
    }

    public function closeEditOfferModal()
    {
        $this->showEditOfferModal = false;
        $this->selectedOffer = null;
        $this->reset(['editOfferedSalary', 'editProposedStartDate', 'editExpiryDate', 'editBenefits', 'editOfferNotes']);
        $this->resetValidation();
    }

    public function updateOffer()
    {
        $this->validate([
            'editOfferedSalary' => 'required|numeric|min:0',
            'editProposedStartDate' => 'required|date|after:today',
            'editExpiryDate' => 'required|date|after:today|before:editProposedStartDate',
            'editBenefits' => 'required|string',
            'editOfferNotes' => 'nullable|string',
        ]);

        try {
            $this->selectedOffer->editOffer(
                $this->editOfferedSalary,
                new Carbon($this->editProposedStartDate),
                new Carbon($this->editExpiryDate),
                $this->editBenefits,
                $this->editOfferNotes
            );

            $this->alert('success', 'Job offer updated successfully');
            $this->closeEditOfferModal();

            // Refresh data
            $this->refreshInterviews();
        } catch (AppException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'Failed to update job offer: ' . $e->getMessage());
        }
    }

    public function openAcceptOfferModal($offerId)
    {
        $this->selectedOffer = JobOffer::with('application.vacancy.position.department')->find($offerId);
        if (!$this->selectedOffer) {
            $this->alert('error', 'Offer not found');
            return;
        }
        $this->offerResponseNotes = null;
        $this->showAcceptOfferModal = true;
    }

    public function closeAcceptOfferModal()
    {
        $this->showAcceptOfferModal = false;
        $this->selectedOffer = null;
        $this->offerResponseNotes = null;
        $this->resetValidation();
    }

    public function confirmAcceptOffer()
    {
        try {
            $this->selectedOffer->accept($this->offerResponseNotes);
            $this->alert('success', 'Job offer accepted successfully');
            $this->closeAcceptOfferModal();
            $this->refreshInterviews();
        } catch (AppException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'Failed to accept job offer');
        }
    }

    public function openRejectOfferModal($offerId)
    {
        $this->selectedOffer = JobOffer::with('application.vacancy.position.department')->find($offerId);
        if (!$this->selectedOffer) {
            $this->alert('error', 'Offer not found');
            return;
        }
        $this->offerResponseNotes = null;
        $this->showRejectOfferModal = true;
    }

    public function closeRejectOfferModal()
    {
        $this->showRejectOfferModal = false;
        $this->selectedOffer = null;
        $this->offerResponseNotes = null;
        $this->resetValidation();
    }

    public function confirmRejectOffer()
    {
        $this->validate([
            'offerResponseNotes' => 'required|string|max:1000',
        ]);

        try {
            $this->selectedOffer->reject($this->offerResponseNotes);
            $this->alert('success', 'Job offer rejected successfully');
            $this->closeRejectOfferModal();
            $this->refreshInterviews();
        } catch (AppException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'Failed to reject job offer');
        }
    }

    public function render()
    {
        $vacancy = Vacancy::with(['vacancy_questions', 'vacancy_slots', 'position', 'assigned_to_user', 'hiring_manager', 'hr_manager'])->find($this->vacancyId);
        $applicants = Applicant::byVacancyId($this->vacancyId)->when($this->search, function ($query) {
            $query->search($this->search);
        })->with('applications')->paginate(10);
        
        return view('livewire.recruitment.vacancy-show', [
            'vacancy' => $vacancy,
            'applicants' => $applicants,
            'vacancyTypes' => Vacancy::TYPE_OPTIONS,
            'vacancyStatuses' => Vacancy::STATUS_OPTIONS,
        ]);
    }
}
