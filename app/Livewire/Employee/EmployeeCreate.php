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
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;   

class EmployeeCreate extends Component
{
    use AlertFrontEnd, WithFileUploads;
    
    // Applicant ID for pre-filling data
    public $applicant_id;
    
    // Basic Employee Info
    public $name;
    public $name_ar;
    public $email;
    public $phone;
    public $address;
    public $nationality = 'Egyptian'; // Default value
    public $gender;
    public $birth_date;
    public $birth_place_id;
    public $license_required = false;
    public $employment_date;
    public $mother_name;

    public $status;
    public $statuses;

    // Employee Additional Info
    public $insurance_office_id;
    public $insurance_number;
    public $insurance_amount;
    public $academic_qualification;
    public $university;
    public $graduation_year;
    public $military_status;
    public $marital_status;
    
    // Username preview
    public $previewedUsername = '';
    public $usernameHasSuffix = false;
    public $baseUsername = '';
    
    // Data for selects
    public $cities = [];
    public $insuranceOffices = [];
    public $genders = [];
    public $militaryStatuses = [];
    public $maritalStatuses = [];

    public $id_card_file;
    public $id_number;
    public $id_issue_date;
    public $id_expiry_date;

    // Applicant Selection Modal
    public $showApplicantModal = false;
    public $applicantSearch = '';
    public $applicantsWithOffers = [];
    public $password;
    protected $rules = [
        // Basic employee info validation
        'name' => 'required|string|max:255',
        'name_ar' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'required|string|max:255',
        'nationality' => 'required|string|max:50',
        'gender' => 'required|in:Male,Female',
        'birth_date' => 'required|date',
        'birth_place_id' => 'required|exists:cities,id',
        'license_required' => 'boolean',
        'employment_date' => 'required|date',
        'mother_name' => 'nullable|string|max:255',
        // Employee additional info validation
        'insurance_office_id' => 'required|exists:insurance_offices,id',
        'insurance_number' => 'nullable|string|max:50',
        'insurance_amount' => 'nullable|numeric',
        'academic_qualification' => 'nullable|string|max:255',
        'university' => 'nullable|string|max:255',
        'graduation_year' => 'nullable|integer|min:1900|max:2100',
        'military_status' => 'nullable|required_if:gender,Male|in:Exempted,Drafted,Completed',
        'marital_status' => 'required|string|in:Single,Married,Divorced,Widowed',

        'id_card_file' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,bmp,gif',
        'id_number' => 'required|string|max:50',
        'id_issue_date' => 'required|date',
        'id_expiry_date' => 'required|date|after:id_issue_date',
    ];

    public function mount($applicant_id = null)
    {
        // Load data for select inputs
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
        
        // If applicant_id is provided, load applicant data
        if ($applicant_id) {
            $this->applicant_id = $applicant_id;
            $this->loadApplicantData();
        }

        $this->password = $this->generatePassword();
        $this->statuses = Employee::STATUS_LIST;
        $this->status = Employee::STATUS_ACTIVE;
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
        
        // Generate username preview
        $this->previewUsername();
        
        $this->alert('success', 'Applicant data loaded successfully');
    }

    /**
     * Generate a standardized username based on the employee's name
     * 
     * @param string $name Full name of the employee
     * @return string Generated username
     */
    public function generateUsername($name)
    {
        // Extract first and last name from full name
        $nameParts = explode(' ', $name);
        $firstName = isset($nameParts[0]) ? $nameParts[0] : '';
        $lastName = isset($nameParts[count($nameParts) - 1]) ? $nameParts[count($nameParts) - 1] : '';
        
        // Convert to lowercase and remove non-alphanumeric characters
        $firstName = preg_replace('/[^a-z0-9]/', '', strtolower($firstName));
        $lastName = preg_replace('/[^a-z0-9]/', '', strtolower($lastName));
        
        // Generate base username
        $baseUsername = $firstName . $lastName;
        
        // Ensure username is not empty
        if (empty($baseUsername)) {
            $baseUsername = 'employee' . rand(100, 999);
        }
        
        // Check if username exists and append numbers if needed
        $username = $baseUsername;
        $count = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $count;
            $count++;
        }
        
        return $username;
    }
    
    /**
     * Preview the username that will be generated
     */
    public function previewUsername()
    {
        if (!empty($this->name)) {
            // Extract first and last name from full name
            $nameParts = explode(' ', $this->name);
            $firstName = isset($nameParts[0]) ? $nameParts[0] : '';
            $lastName = isset($nameParts[count($nameParts) - 1]) ? $nameParts[count($nameParts) - 1] : '';
            
            // Convert to lowercase and remove non-alphanumeric characters
            $firstName = preg_replace('/[^a-z0-9]/', '', strtolower($firstName));
            $lastName = preg_replace('/[^a-z0-9]/', '', strtolower($lastName));
            
            // Generate base username
            $this->baseUsername = $firstName . $lastName;
            
            // Ensure username is not empty
            if (empty($this->baseUsername)) {
                $this->baseUsername = 'employee' . rand(100, 999);
            }
            
            // Check if username exists and append numbers if needed
            $username = $this->baseUsername;
            $count = 1;
            $this->usernameHasSuffix = false;
            
            while (User::where('username', $username)->exists()) {
                $username = $this->baseUsername . $count;
                $this->usernameHasSuffix = true;
                $count++;
            }
            
            $this->previewedUsername = $username;
        } else {
            $this->previewedUsername = '';
            $this->usernameHasSuffix = false;
            $this->baseUsername = '';
        }
    }
    
    /**
     * Generate a standard password
     * 
     * @return string Generated password
     */
    protected function generatePassword()
    {
        $password = '12345678';
        return $password;
    }

    public function createEmployee()
    {
        $this->validate();
        
        try {
            DB::beginTransaction();
            
            // Make sure we have the latest username preview
            $this->previewUsername();
            $username = $this->previewedUsername;
            
            // Create a new user with type 'employee'
            $user = User::createUser(
                $this->name,
                $username,
                $this->password,
                User::TYPE_EMPLOYEE
            );

            $path = $this->id_card_file->store(Employee::FILES_DIRECTORY.'/id_cards', 's3');
            
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
            
            // Create employee with info using the newly created user
            $employee = Employee::createEmployee(
                $user->id,
                $this->name,
                $this->name_ar,
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
                $this->applicant_id,
                $path,
                $this->id_number,
                $this->id_issue_date,
                $this->id_expiry_date,
                $this->mother_name,
                $this->status
            );

            // Update the user with employee_id
            $user->employee_id = $employee->id;
            $user->save();
            
            // If this employee was created from an applicant, mark the applicant as hired
            if ($this->applicant_id) {
                $applicant = Applicant::find($this->applicant_id);
                if ($applicant) {
                    // Mark the applicant as hired
                    $applicant->hire();
                }
            }
        
            DB::commit();
            
            $this->alert('success', 'Employee created successfully! Login credentials have been generated.');
            return redirect()->route('employees.show', $employee->id);
            
        } catch (AppException $e) {
            DB::rollBack();
            // If there was an uploaded file, attempt to delete it from storage
            if (isset($path) && Storage::disk('s3')->exists($path)) {
                try {
                    Storage::disk('s3')->delete($path);
                } catch (Exception $deleteException) {
                    report($deleteException);
                    // Continue with the error handling even if file deletion fails
                }
            }
            $this->alert('failed', $e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();
            if (isset($path) && Storage::disk('s3')->exists($path)) {
                try {
                    Storage::disk('s3')->delete($path);
                } catch (Exception $deleteException) {
                    report($deleteException);
                    // Continue with the error handling even if file deletion fails
                }
            }
            report($e);
            $this->alertError($e->getMessage());
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
     * Select an applicant to fill the form with their data
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
        
        $this->alert('info', 'Form has been reset');
    }

    public function render()
    {
        return view('livewire.employee.employee-create', [
            'title' => 'Create New Employee'
        ]);
    }
} 