<?php

namespace App\Livewire\Employee;

use App\Models\Personel\Employee;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Missing Documents Report')]
class MissingDocReport extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $showMissingDocModal = false;
    public $showExpiredDocModal = false;
    public $showNearExpiryDocModal = false;
    public $showFilterMissingModal = false;
    public $showFilterExpiredModal = false;
    public $showFilterNearExpiryModal = false;
    public $selectedEmployee = null;
    public $missingDocuments = [];
    public $expiredDocuments = [];
    public $nearExpiryDocuments = [];
    
    // Document types for filtering
    public $documentTypes = [
        'ID Card' => false,
        'Birth Certificate' => false,
        'Employment Contract' => false,
        'Army Service Paper' => false,
        'Driver License' => false,
        'Police Record' => false,
        'HR Letter' => false,
        'S1 Document' => false,
        'S2 Document' => false,
        'S6 Document' => false,
        'Medical Record' => false,
        'External Medical Record' => false,
        'Practice Card' => false,
        'Skills Qualification' => false,
        'Syndicate Card' => false,
        'Work Declaration' => false,
    ];
    
    // Separate filters for missing, expired, and near expiry documents
    public $missingDocFilters = [];
    public $expiredDocFilters = [];
    public $nearExpiryDocFilters = [];
    
    // Filter active states
    public $missingFilterActive = false;
    public $expiredFilterActive = false;
    public $nearExpiryFilterActive = false;
    
    public function mount()
    {
        // Initialize filters with all document types
        $this->resetFilters();
    }
    
    /**
     * Reset all filters
     */
    public function resetFilters()
    {
        $this->missingDocFilters = array_fill_keys(array_keys($this->documentTypes), false);
        $this->expiredDocFilters = array_fill_keys(array_keys($this->documentTypes), false);
        $this->nearExpiryDocFilters = array_fill_keys(array_keys($this->documentTypes), false);
        $this->missingFilterActive = false;
        $this->expiredFilterActive = false;
        $this->nearExpiryFilterActive = false;
    }
    
    /**
     * Show modal to select missing document filters
     */
    public function showMissingFilters()
    {
        $this->showFilterMissingModal = true;
    }
    
    /**
     * Show modal to select expired document filters
     */
    public function showExpiredFilters()
    {
        $this->showFilterExpiredModal = true;
    }
    
    /**
     * Show modal to select near expiry document filters
     */
    public function showNearExpiryFilters()
    {
        $this->showFilterNearExpiryModal = true;
    }
    
    /**
     * Apply missing document filters
     */
    public function applyMissingFilters()
    {
        $this->missingFilterActive = count(array_filter($this->missingDocFilters)) > 0;
        $this->showFilterMissingModal = false;
    }
    
    /**
     * Apply expired document filters
     */
    public function applyExpiredFilters()
    {
        $this->expiredFilterActive = count(array_filter($this->expiredDocFilters)) > 0;
        $this->showFilterExpiredModal = false;
    }
    
    /**
     * Apply near expiry document filters
     */
    public function applyNearExpiryFilters()
    {
        $this->nearExpiryFilterActive = count(array_filter($this->nearExpiryDocFilters)) > 0;
        $this->showFilterNearExpiryModal = false;
    }
    
    /**
     * Clear all filters
     */
    public function clearAllFilters()
    {
        $this->resetFilters();
    }
    
    public function showMissingDocuments($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);
        $this->selectedEmployee = $employee;
        $this->missingDocuments = $employee->getMissingDocuments();
        $this->showMissingDocModal = true;
    }
    
    public function showExpiredDocuments($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);
        $this->selectedEmployee = $employee;
        $this->expiredDocuments = $employee->getExpiredDocuments();
        $this->showExpiredDocModal = true;
    }
    
    public function showNearExpiryDocuments($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);
        $this->selectedEmployee = $employee;
        $this->nearExpiryDocuments = $employee->getNearExpiryDocuments();
        $this->showNearExpiryDocModal = true;
    }
    
    public function closeModal()
    {
        $this->showMissingDocModal = false;
        $this->showExpiredDocModal = false;
        $this->showNearExpiryDocModal = false;
        $this->showFilterMissingModal = false;
        $this->showFilterExpiredModal = false;
        $this->showFilterNearExpiryModal = false;
        $this->selectedEmployee = null;
        $this->missingDocuments = [];
        $this->expiredDocuments = [];
        $this->nearExpiryDocuments = [];
    }
    
    public function render(): View
    {
        $query = Employee::query()->where(function ($query) {
            $query->withMissingDocuments()->orWhere(function ($q) {
                $q->withExpiredDocuments();
            })->orWhere(function ($q) {
                $q->withNearExpiryDocuments();
            });
        });
            
        if ($this->searchTerm) {
            $query->where('name', 'like', '%' . $this->searchTerm . '%')->orWhere('email', 'like', '%' . $this->searchTerm . '%');
        }
        
        $employees = $query->with(['idCard', 'birthCertificate', 'contracts', 'armyServicePaper', 'driverLicense', 'policeRecords', 'hrLetters', 'employeeS1Doc', 'employeeS2Doc', 'employeeS6Doc', 'medicalRecord', 'externalMedicalRecord', 'practiceCard', 'skillsQualifications', 'syndicateCard', 'info'])->paginate(10);
            
        // Pre-calculate missing, expired, and near expiry document counts for each employee
        foreach ($employees as $key => $employee) {
            $missingDocs = $employee->getMissingDocuments();
            $expiredDocs = $employee->getExpiredDocuments();
            $nearExpiryDocs = $employee->getNearExpiryDocuments();
            
            $employee->missing_docs_count = count($missingDocs);
            $employee->expired_docs_count = count($expiredDocs);
            $employee->near_expiry_docs_count = count($nearExpiryDocs);
            
            // Apply filters if active
            if ($this->missingFilterActive || $this->expiredFilterActive || $this->nearExpiryFilterActive) {
                $shouldRemove = true;
                
                // Check if employee has any of the filtered missing documents
                if ($this->missingFilterActive) {
                    foreach ($this->missingDocFilters as $docType => $isFiltered) {
                        if ($isFiltered && in_array($docType, $missingDocs)) {
                            $shouldRemove = false;
                            break;
                        }
                    }
                } else {
                    $shouldRemove = false; // No missing filters, don't remove based on missing docs
                }
                
                // If not already marked for removal, check expired documents
                if (!$shouldRemove && $this->expiredFilterActive) {
                    $shouldRemove = true;
                    foreach ($this->expiredDocFilters as $docType => $isFiltered) {
                        if ($isFiltered && in_array($docType, $expiredDocs)) {
                            $shouldRemove = false;
                            break;
                        }
                    }
                }

                // If not already marked for removal, check near expiry documents
                if (!$shouldRemove && $this->nearExpiryFilterActive) {
                    $shouldRemove = true;
                    foreach ($this->nearExpiryDocFilters as $docType => $isFiltered) {
                        if ($isFiltered && in_array($docType, $nearExpiryDocs)) {
                            $shouldRemove = false;
                            break;
                        }
                    }
                }
                
                // If no filters match, mark this employee to be hidden
                $employee->hidden = $shouldRemove;
            } else {
                $employee->hidden = false;
            }
        }
            
        return view('livewire.employee.missing-doc-report', [
            'employees' => $employees,
            'anyFiltersActive' => $this->missingFilterActive || $this->expiredFilterActive || $this->nearExpiryFilterActive,
        ]);
    }
}
