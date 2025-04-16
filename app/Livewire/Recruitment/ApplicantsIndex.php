<?php

namespace App\Livewire\Recruitment;

use App\Models\Base\Area;
use App\Models\Base\City;
use App\Models\Recruitment\Applicants\Applicant;
use Livewire\Component;
use Livewire\WithPagination;

class ApplicantsIndex extends Component
{
    use WithPagination;

    // Search & Filter Properties
    public $search = '';
    public $startDate = null;
    public $endDate = null;
    public $militaryStatus = null;
    public $maritalStatus = null;
    public $cityId = null;
    public $areaId = null;
    public $minAge = null;
    public $maxAge = null;
    public $showFilters = false;

    // Data for Filters
    public $areas = [];
    public $cities = [];
    public $militaryStatusOptions = [];
    public $maritalStatusOptions = [];

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
        $this->militaryStatusOptions = Applicant::MILITARY_STATUS;
        $this->maritalStatusOptions = Applicant::MARITAL_STATUS;
    }

    public function updatedCityId()
    {
        if($this->cityId) {
            $this->areas = City::find($this->cityId)->areas;
        } else {
            $this->areas = [];
        }
    }



    public function render()
    {
        $applicants = Applicant::query()
            ->when($this->search, function ($query) {
                $query->search($this->search);
            })
            ->when($this->startDate, function ($query) {
                $query->createdFrom($this->startDate);
            })
            ->when($this->endDate, function ($query) {
                $query->createdTo($this->endDate);
            })
            ->when($this->militaryStatus, function ($query) {
                $query->withMilitaryStatus($this->militaryStatus);
            })
            ->when($this->maritalStatus, function ($query) {
                $query->withMaritalStatus($this->maritalStatus);
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
            ->with(['area', 'applications'])
            ->paginate(30);

        return view('livewire.recruitment.applicants-index', [
            'applicants' => $applicants
        ])->layout('components.layouts.app', [
            'title' => 'Applicants',
            'description' => 'Applicants found on the system, please use the filters to find the applicant you are looking for',
            'applicantsIndex' => 'active',
        ]);
    }

    public function resetFilters()
    {
        $this->reset([
            'search',
            'startDate',
            'endDate',
            'militaryStatus',
            'maritalStatus',
            'cityId',
            'areaId',
            'minAge',
            'maxAge'
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

    public function updatingMilitaryStatus()
    {
        $this->resetPage();
    }

    public function updatingMaritalStatus()
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
} 