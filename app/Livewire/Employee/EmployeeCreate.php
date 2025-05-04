<?php

namespace App\Livewire\Employee;

use App\Exceptions\AppException;
use App\Models\Base\City;
use App\Models\Base\InsuranceOffice;
use App\Models\Personel\Employee;
use App\Models\Recruitment\Applicants\Applicant;
use App\Models\Users\User;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class EmployeeCreate extends Component
{
    use AlertFrontEnd;
    
    // Applicant ID for pre-filling data
    public $applicant_id;
    
    // Basic Employee Info
    public $user_id;
    public $name;
    public $email;
    public $phone;
    public $address;
    public $nationality = 'Egyptian'; // Default value
    public $gender;
    public $birth_date;
    public $birth_place_id;
    public $license_required = false;
    public $employment_date;
    
    // Employee Additional Info
    public $insurance_office_id;
    public $insurance_number;
    public $insurance_amount;
    public $academic_qualification;
    public $university;
    public $graduation_year;
    public $military_status;
    public $marital_status;
    
    // Data for selects
    public $users = [];
    public $cities = [];
    public $insuranceOffices = [];
    public $genders = [];
    public $militaryStatuses = [];
    public $maritalStatuses = [];

    // Applicant Selection Modal
    public $showApplicantModal = false;
    public $applicantSearch = '';
    public $applicantsWithOffers = [];

    protected $rules = [
        // Basic employee info validation
        'user_id' => 'required|exists:users,id',
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'required|string|max:255',
        'nationality' => 'required|string|max:50',
        'gender' => 'required|in:Male,Female',
        'birth_date' => 'required|date',
        'birth_place_id' => 'required|exists:cities,id',
        'license_required' => 'boolean',
        'employment_date' => 'required|date',
        
        // Employee additional info validation
        'insurance_office_id' => 'required|exists:insurance_offices,id',
        'insurance_number' => 'nullable|string|max:50',
        'insurance_amount' => 'nullable|numeric',
        'academic_qualification' => 'nullable|string|max:255',
        'university' => 'nullable|string|max:255',
        'graduation_year' => 'nullable|integer|min:1900|max:2100',
        'military_status' => 'nullable|required_if:gender,Male|in:Exempted,Drafted,Completed',
        'marital_status' => 'required|string|in:Single,Married,Divorced,Widowed',
    ];

    public function mount($applicant_id = null)
    {
        // Load data for select inputs
        $this->users = User::whereNull('employee_id')->get();
        $this->cities = City::orderBy('name')->get();
        $this->insuranceOffices = InsuranceOffice::orderBy('name')->get();
        $this->genders = Applicant::GENDER;
        $this->militaryStatuses = Applicant::MILITARY_STATUS;
        $this->maritalStatuses = Applicant::MARITAL_STATUS;
        
        // Set default values
        $this->employment_date = now()->format('Y-m-d');
        $this->birth_date = now()->subYears(25)->format('Y-m-d'); // Default to 25 years ago
        
        if (count($this->cities) > 0) {
            $this->birth_place_id = $this->cities[0]->id;
        }
        
        if (count($this->insuranceOffices) > 0) {
            $this->insurance_office_id = $this->insuranceOffices[0]->id;
        }
        
        if (count($this->users) > 0) {
            $this->user_id = $this->users[0]->id;
        }
        
        // If applicant_id is provided, load applicant data
        if ($applicant_id) {
            $this->applicant_id = $applicant_id;
            $this->loadApplicantData();
        }
    }
    
    /**
     * Load data from applicant to pre-fill the form
     */
    protected function loadApplicantData()
    {
        $applicant = Applicant::find($this->applicant_id);
        
        if (!$applicant) {
            $this->alert('failed', 'Applicant not found');
            return redirect()->route('employees.create');
        }
        
        // Fill in the form with applicant data
        $this->name = $applicant->full_name;
        $this->email = $applicant->email;
        $this->phone = $applicant->phone;
        $this->address = $applicant->address;
        $this->nationality = $applicant->nationality ?? 'Egyptian';
        $this->gender = $applicant->gender;
        $this->birth_date = $applicant->birth_date?->format('Y-m-d');
        
        // Try to find a matching city for birth place
        if ($applicant->city_id) {
            $this->birth_place_id = $applicant->city_id;
        }
        
        // Fill in educational information if available
        if ($applicant->educations && $applicant->educations->isNotEmpty()) {
            $highestEducation = $applicant->educations->sortByDesc('end_date')->first();
            if ($highestEducation) {
                $this->academic_qualification = $highestEducation->degree;
                $this->university = $highestEducation->school_name;
                $this->graduation_year = $highestEducation->end_date?->year;
            }
        }
        
        // Set military and marital status
        $this->military_status = $applicant->military_status;
        $this->marital_status = $applicant->marital_status ?? 'Single';
        
        // Check if applicant has relevant skill/experience for driver license
        if ($applicant->skills) {
            foreach ($applicant->skills as $skill) {
                if (stripos($skill->skill, 'driv') !== false || stripos($skill->skill, 'license') !== false) {
                    $this->license_required = true;
                    break;
                }
            }
        }
        
        $this->alert('success', 'Applicant data loaded successfully');
    }

    public function createEmployee()
    {
        $this->validate();
        
        // dd($this->applicant_id);
        try {
            DB::beginTransaction();
            
            // Create employee info data array
            $employeeInfoData = [
                'insurance_office_id' => $this->insurance_office_id,
                'insurance_number' => $this->insurance_number,
                'insurance_amount' => $this->insurance_amount,
                'academic_qualification' => $this->academic_qualification,
                'university' => $this->university,
                'graduation_year' => $this->graduation_year,
                'military_status' => $this->military_status,
                'marital_status' => $this->marital_status,
            ];
            
            // Create employee with info
            $employee = Employee::createEmployee(
                $this->user_id,
                $this->name,
                $this->email,
                $this->phone,
                $this->address,
                $this->nationality,
                $this->gender,
                $this->birth_date,
                $this->birth_place_id,
                $this->license_required,
                $this->employment_date,
                $employeeInfoData,
                $this->applicant_id
            );
            
            // If this employee was created from an applicant, mark the applicant as hired
            if ($this->applicant_id) {
                $applicant = Applicant::find($this->applicant_id);
                if ($applicant) {
                    // Mark the applicant as hired
                    $applicant->hire();
                }
            }
            
            DB::commit();
            
            $this->alert('success', 'Employee created successfully!');
            return redirect()->route('employees.show', $employee->id);
            
        } catch (AppException $e) {
            DB::rollBack();
            $this->alert('failed', $e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            $this->alertError();
        }
    }

    /**
     * Open the applicant selection modal and load applicants with accepted offers
     */
    public function openApplicantModal()
    {
        $this->applicantSearch = '';
        $this->loadApplicantsWithOffers();
        $this->showApplicantModal = true;
    }
    
    /**
     * Close the applicant selection modal
     */
    public function closeApplicantModal()
    {
        $this->showApplicantModal = false;
    }
    
    /**
     * Load applicants with accepted offers who are not yet hired
     */
    public function loadApplicantsWithOffers()
    {
        
        $query = Applicant::withAcceptedOffersNotHired();

        if (!empty($this->applicantSearch)) {
            $search = '%' . $this->applicantSearch . '%';
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', $search)
                  ->orWhere('last_name', 'like', $search)
                  ->orWhere('email', 'like', $search)
                  ->orWhere('phone', 'like', $search);
            });
        }
        
        $this->applicantsWithOffers = $query->get();
    }
    
    /**
     * Select an applicant from the modal and load their data
     */
    public function selectApplicant($applicantId)
    {
        $this->applicant_id = $applicantId;
        $this->loadApplicantData();
        $this->showApplicantModal = false;
    }

    /**
     * Reset the form data to default values
     */
    public function resetForm()
    {
        $this->applicant_id = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->address = '';
        $this->nationality = 'Egyptian';
        $this->gender = null;
        $this->birth_date = now()->subYears(25)->format('Y-m-d');
        $this->license_required = false;
        $this->employment_date = now()->format('Y-m-d');
        
        // Reset additional info
        $this->insurance_number = null;
        $this->insurance_amount = null;
        $this->academic_qualification = null;
        $this->university = null;
        $this->graduation_year = null;
        $this->military_status = null;
        $this->marital_status = null;
        
        // Reset selects to defaults
        if (count($this->cities) > 0) {
            $this->birth_place_id = $this->cities[0]->id;
        }
        
        if (count($this->insuranceOffices) > 0) {
            $this->insurance_office_id = $this->insuranceOffices[0]->id;
        }
        
        if (count($this->users) > 0) {
            $this->user_id = $this->users[0]->id;
        }
        
        $this->alert('info', 'Form has been reset');
    }

    public function render()
    {
        return view('livewire.employee.employee-create', [
            'title' => 'Create New Employee'
        ]);
    }
} 