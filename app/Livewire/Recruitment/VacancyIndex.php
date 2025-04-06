<?php

namespace App\Livewire\Recruitment;

use App\Exceptions\AppException;
use App\Models\Hierarchy\Position;
use App\Models\Recruitment\Vacancies\BaseQuestion;
use App\Models\Recruitment\Vacancies\Vacancy;
use App\Models\Recruitment\Vacancies\VacancyQuestion;
use App\Models\Recruitment\Vacancies\VacancySlot;
use App\Models\Users\User;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Exception;
use Livewire\Component;
use Livewire\WithPagination;

class VacancyIndex extends Component
{
    use WithPagination, AlertFrontEnd;
    
    //page data
    public $positions;
    public $users;

    // Search
    public $search = '';

    // Vacancy section
    public $newVacancyModal = false;
    public $editVacancyModal = false;
    public $viewVacancyModal = false;
    public $vacancyId;
    public $positionId;
    public $assignedTo;
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

    // Confirmation modal
    public $deleteConfirmationModal = false;
    public $itemToDelete;
    public $itemTypeToDelete;

    protected $listeners = ['dateSelected' => 'onDateSelected'];


    //reset pagination
    public function updatedSearch()
    {
        $this->resetPage();
    }

    // Vacancy functions
    public function openNewVacancySec()
    {
        $this->newVacancyModal = true;
        $this->resetVacancyFields();
        $this->addQuestion();  // Start with one empty question
        $this->addSlot();      // Start with one empty slot
    }

    public function closeNewVacancySec()
    {
        $this->newVacancyModal = false;
    }

    public function addNewVacancy()
    {
        $this->validate([
            'positionId' => 'required|exists:positions,id',
            'assignedTo' => 'required|exists:users,id',
            'vacancyType' => 'required|in:full_time,part_time,temporary',
            'jobResponsibilities' => 'nullable|string',
            'arabicJobResponsibilities' => 'nullable|string',
            'jobQualifications' => 'nullable|string',
            'arabicJobQualifications' => 'nullable|string',
            'jobBenefits' => 'nullable|string',
            'arabicJobBenefits' => 'nullable|string',
            'jobSalary' => 'nullable|string',
        ]);

        try {
            // Prepare vacancy data
            $data = [
                'position_id' => $this->positionId,
                'assigned_to' => $this->assignedTo,
                'type' => $this->vacancyType,
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
                    $questionsData[] = [
                        'question' => $question['question'],
                        'arabic_question' => $question['arabic_question'] ?? null,
                        'type' => $question['type'],
                        'required' => isset($question['required']) ? true : false,
                        'options' => !empty($question['options']) ? explode(',', $question['options']) : null,
                    ];
                }
            }

            // Process slots
            $slotsData = [];
            foreach ($this->slots as $slot) {
                if (!empty($slot['date']) && !empty($slot['start_time']) && !empty($slot['end_time'])) {
                    $slotsData[] = [
                        'date' => $slot['date'],
                        'start_time' => $slot['start_time'],
                        'end_time' => $slot['end_time'],
                    ];
                }
            }

            // Create vacancy with questions and slots
            $data['questions'] = $questionsData;
            $data['slots'] = $slotsData;

            Vacancy::newVacancy($data);
            $this->closeNewVacancySec();
            $this->alertSuccess('Vacancy added successfully!');
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alertError('Failed to add vacancy. Please try again.');
        }
    }

    public function openEditVacancySec($id)
    {
        $vacancy = Vacancy::with(['vacancy_questions', 'vacancy_slots'])->find($id);
        $this->vacancyId = $vacancy->id;
        $this->positionId = $vacancy->position_id;
        $this->assignedTo = $vacancy->assigned_to;
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
                'options' => $question->options ? implode(',', $question->options) : '',
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
        
        $this->editVacancyModal = true;
    }

    public function viewVacancy($id)
    {
        $vacancy = Vacancy::with(['vacancy_questions', 'vacancy_slots', 'position', 'assigned_to_user'])->find($id);
        $this->vacancyId = $vacancy->id;
        $this->positionId = $vacancy->position_id;
        $this->assignedTo = $vacancy->assigned_to;
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
                'options' => $question->options ? implode(',', $question->options) : '',
            ];
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
        
        $this->viewVacancyModal = true;
    }

    public function closeEditVacancySec()
    {
        $this->editVacancyModal = false;
    }

    public function closeViewVacancySec()
    {
        $this->viewVacancyModal = false;
    }

    public function updateVacancy()
    {
        $this->validate([
            'positionId' => 'required|exists:positions,id',
            'assignedTo' => 'required|exists:users,id',
            'vacancyType' => 'required|in:full_time,part_time,temporary',
            'vacancyStatus' => 'required|in:open,closed',
            'jobResponsibilities' => 'nullable|string',
            'arabicJobResponsibilities' => 'nullable|string',
            'jobQualifications' => 'nullable|string',
            'arabicJobQualifications' => 'nullable|string',
            'jobBenefits' => 'nullable|string',
            'arabicJobBenefits' => 'nullable|string',
            'jobSalary' => 'nullable|string',
        ]);

        try {
            $vacancy = Vacancy::find($this->vacancyId);

            // Prepare vacancy data
            $data = [
                'position_id' => $this->positionId,
                'assigned_to' => $this->assignedTo,
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
                        'options' => !empty($question['options']) ? explode(',', $question['options']) : null,
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

            // Create vacancy with questions and slots
            $data['questions'] = $questionsData;
            $data['slots'] = $slotsData;
            $data['reset_questions'] = true;
            $data['reset_slots'] = true;

            $vacancy->updateVacancy($data);
            $this->closeEditVacancySec();
            $this->alertSuccess('Vacancy updated successfully!');
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alertError('Failed to update vacancy. Please try again.');
        }
    }

    public function confirmDeleteVacancy($id)
    {
        $this->itemToDelete = $id;
        $this->itemTypeToDelete = 'vacancy';
        $this->deleteConfirmationModal = true;
    }

    public function deleteVacancy()
    {
        try {
            $vacancy = Vacancy::find($this->itemToDelete);
            $vacancy->deleteVacancy();
            $this->closeDeleteConfirmationModal();
            $this->alertSuccess('Vacancy deleted successfully!');
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alertError('Failed to delete vacancy. Please try again.');
        }
    }

    // Question functions
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

    // Date selected event handler
    public function onDateSelected($value, $name)
    {
        if ($name === 'closingDate') {
            $this->closingDate = $value;
        } else if (strpos($name, 'slots.') === 0 && strpos($name, '.date') !== false) {
            $parts = explode('.', $name);
            $index = $parts[1];
            $this->slots[$index]['date'] = $value;
        }
    }

    // Helper functions
    public function closeDeleteConfirmationModal()
    {
        $this->deleteConfirmationModal = false;
        $this->itemToDelete = null;
        $this->itemTypeToDelete = null;
    }

    public function confirmDelete()
    {
        if ($this->itemTypeToDelete === 'vacancy') {
            $this->deleteVacancy();
        }
    }

    private function resetVacancyFields()
    {
        $this->vacancyId = null;
        $this->positionId = null;
        $this->assignedTo = null;
        $this->vacancyType = 'full_time';
        $this->vacancyStatus = 'open';
        $this->closingDate = Carbon::now()->addDays(30)->format('Y-m-d');
        $this->jobResponsibilities = null;
        $this->arabicJobResponsibilities = null;
        $this->jobQualifications = null;
        $this->arabicJobQualifications = null;
        $this->jobBenefits = null;
        $this->arabicJobBenefits = null;
        $this->jobSalary = null;
        $this->questions = [];
        $this->slots = [];
    }


    public function mount()
    {
        $this->questionTypes = BaseQuestion::TYPES;
        $this->positions = Position::availableForRecruitment()->get();
        $this->users = User::hrOrAdmin()->get();
    }

    public function render()
    {
        $vacancies = Vacancy::with(['position', 'assigned_to_user', 'vacancy_questions', 'vacancy_slots'])
            ->when($this->search, function ($query) {
                $query->search($this->search);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);


        $vacancyTypes = Vacancy::TYPE_OPTIONS;
        $vacancyStatuses = Vacancy::STATUS_OPTIONS;

        return view('livewire.recruitment.vacancy-index', [
            'vacancies' => $vacancies,
            'vacancyTypes' => $vacancyTypes,
            'vacancyStatuses' => $vacancyStatuses
        ])->layout('components.layouts.app', [
            'title' => 'Vacancies',
            'vacanciesIndex' => 'active'
        ]);
    }
} 