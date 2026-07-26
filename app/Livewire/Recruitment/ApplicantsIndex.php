<?php

namespace App\Livewire\Recruitment;

use App\Models\Base\Area;
use App\Models\Base\City;
use App\Models\Recruitment\Applicants\Applicant;
use App\Models\Recruitment\Interviews\InterviewFeedback;
use Livewire\Component;
use Livewire\WithPagination;

class ApplicantsIndex extends Component
{
    use WithPagination;

    // Search & Filter Properties
    public $search = '';
    public $startDate = null;
    public $endDate = null;
    public $cityId = null;
    public $areaId = null;
    public $minAge = null;
    public $maxAge = null;
    public $name = null;
    public $jobTitle = null;
    public $phone = null;
    public $gender = null;
    public $education = null;
    public $interviewFrom = null;
    public $interviewTo = null;
    public $interviewResult = null;
    public $showFilters = false;

    // Data for Filters
    public $areas = [];
    public $cities = [];
    public $genderOptions = [];
    public $interviewResultOptions = [];

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function showApplicant($applicantId)
    {
        return $this->dispatch('openNewTab', route('recruitment.applicants.show', $applicantId));
    }

    public function mount()
    {
        $this->cities = City::all();
        // $this->areas = Area::all();
        $this->genderOptions = Applicant::GENDER;
        $this->interviewResultOptions = InterviewFeedback::RESULTS;
    }

    public function updatedCityId()
    {
        if ($this->cityId) {
            $this->areas = City::find($this->cityId)->areas;
        } else {
            $this->areas = [];
        }
    }



    public function render()
    {
        $applicants = Applicant::query()
            ->latest()
            ->when($this->search, function ($query) {
                $query->search($this->search);
            })
            ->when($this->startDate, function ($query) {
                $query->createdFrom($this->startDate);
            })
            ->when($this->endDate, function ($query) {
                $query->createdTo($this->endDate);
            })
            ->when($this->areaId, function ($query) {
                $query->fromArea($this->areaId);
            })
            ->when($this->cityId, function ($query) {
                $query->fromCity($this->cityId);
            })
            ->when($this->minAge, function ($query) {
                $query->olderThan($this->minAge);
            })
            ->when($this->maxAge, function ($query) {
                $query->youngerThan($this->maxAge);
            })
            ->when($this->name, function ($query) {
                $query->withName($this->name);
            })
            ->when($this->jobTitle, function ($query) {
                $query->withJobTitle($this->jobTitle);
            })
            ->when($this->phone, function ($query) {
                $query->withPhone($this->phone);
            })
            ->when($this->gender, function ($query) {
                $query->withGender($this->gender);
            })
            ->when($this->education, function ($query) {
                $query->withEducation($this->education);
            })
            ->when($this->interviewFrom, function ($query) {
                $query->interviewFrom($this->interviewFrom);
            })
            ->when($this->interviewTo, function ($query) {
                $query->interviewTo($this->interviewTo);
            })
            ->when($this->interviewResult, function ($query) {
                $query->withInterviewResult($this->interviewResult);
            })
            ->with([
                'applications.vacancy.position',
                'educations',
                'interviews.feedbacks',
            ])
            ->simplePaginate(30);

        return view('livewire.recruitment.applicants-index', [
            'applicants' => $applicants
        ]);
    }

    public function resetFilters()
    {
        $this->reset([
            'search',
            'startDate',
            'endDate',
            'cityId',
            'areaId',
            'minAge',
            'maxAge',
            'name',
            'jobTitle',
            'phone',
            'gender',
            'education',
            'interviewFrom',
            'interviewTo',
            'interviewResult',
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStartDate()
    {
        $this->resetPage();
    }

    public function updatingEndDate()
    {
        $this->resetPage();
    }

    public function updatingAreaId()
    {
        $this->resetPage();
    }

    public function updatingMinAge()
    {
        $this->resetPage();
    }

    public function updatingMaxAge()
    {
        $this->resetPage();
    }

    public function updatingName()
    {
        $this->resetPage();
    }

    public function updatingJobTitle()
    {
        $this->resetPage();
    }

    public function updatingPhone()
    {
        $this->resetPage();
    }

    public function updatingGender()
    {
        $this->resetPage();
    }

    public function updatingEducation()
    {
        $this->resetPage();
    }

    public function updatingInterviewFrom()
    {
        $this->resetPage();
    }

    public function updatingInterviewTo()
    {
        $this->resetPage();
    }

    public function updatingInterviewResult()
    {
        $this->resetPage();
    }
}
