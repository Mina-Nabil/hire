<?php

namespace App\Livewire\Employee;

use App\Exceptions\AppException;
use App\Models\Base\InsuranceOffice;
use App\Models\Personel\Docs\ArmyServicePaper;
use App\Models\Personel\Docs\BirthCertificate;
use App\Models\Personel\Docs\EmployeeS6Doc;
use App\Models\Personel\Docs\PoliceRecord;
use App\Models\Personel\Employee;
use App\Models\Personel\EmployeeInfo;
use App\Models\Recruitment\Applicants\Applicant;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class EmployeeShow extends Component
{
    use AlertFrontEnd, WithFileUploads;
    //////page controls
    public $section = 'info';

    public function changeSection($section)
    {
        $this->section = $section;
    }

    protected $queryString = ['section'];

    public $employee;
    public $insuranceOffices;
    public $militaryStatuses;

    // Base Info Edit Modal
    public $editBaseInfoModal = false;
    public $name;
    public $email;
    public $phone;
    public $address;
    public $nationality;
    public $gender;
    public $birth_date;
    public $employment_date;

    // Employee Info Edit Modal
    public $editEmployeeInfoModal = false;
    public $insurance_office_id;
    public $insurance_number;
    public $insurance_amount;
    public $academic_qualification;
    public $university;
    public $graduation_year;
    public $military_status;
    public $marital_status;
    
    // ID Card Modal
    public $editIdCardModal = false;
    public $id_card_file;
    public $id_number;
    public $id_issue_date;
    public $id_expiry_date;
    public $keep_existing_file = false;

    // Birth Certificate Modal
    public $editBirthCertificateModal = false;
    public $birth_certificate_file;
    public $birth_certificate_issue_date;
    public $birth_certificate_expiry_date;
    public $birth_certificate_type;
    public $keep_existing_birth_certificate = false;
    public $birthCertificateTypes;
    
    // Army Service Paper Modal
    public $editArmyServicePaperModal = false;
    public $army_service_paper_file;
    public $army_service_paper_issue_date;
    public $army_service_paper_expiry_date;
    public $army_service_paper_type;
    public $keep_existing_army_service_paper = false;
    public $armyServicePaperTypes;

    // Employee S1 Doc Modal
    public $editEmployeeS1DocModal = false;
    public $employee_s1_doc_file;
    public $employee_s1_doc_issue_date;
    public $employee_s1_doc_expiry_date;
    public $s1_number;
    public $keep_existing_employee_s1_doc = false;
    
    // Employee S2 Doc Modal
    public $editEmployeeS2DocModal = false;
    public $employee_s2_doc_file;
    public $employee_s2_doc_issue_date;
    public $employee_s2_doc_expiry_date;
    public $s2_amount;
    public $s2_year;
    public $keep_existing_employee_s2_doc = false;
    
    // Employee S6 Doc Modal
    public $editEmployeeS6DocModal = false;
    public $employee_s6_doc_file;
    public $employee_s6_doc_issue_date;
    public $employee_s6_doc_expiry_date;
    public $s6_number;
    public $leaving_reason;
    public $keep_existing_employee_s6_doc = false;
    public $employeeS6DocLeavingReasons;
    
    // Employee Contract Modal
    public $editEmployeeContractModal = false;
    public $employee_contract_file;
    public $employee_contract_issue_date;
    public $employee_contract_expiry_date;
    public $keep_existing_employee_contract = false;
    public $editing_contract_id = null;
    
    // Driver License Properties
    public $editDriverLicenseModal = false;
    public $keep_existing_driver_license = false;
    public $driver_license_file;
    public $driver_license_issue_date;
    public $driver_license_expiry_date;

    // Police Record Properties
    public $editPoliceRecordModal = false;
    public $keep_existing_police_record = false;
    public $police_record_file;
    public $police_record_issue_date;
    public $police_record_expiry_date;

    // HR Letter Properties
    public $editHrLetterModal = false;
    public $keep_existing_hr_letter = false;
    public $hr_letter_file;
    public $hr_letter_issue_date;
    public $hr_letter_expiry_date;

    // Common properties
    public $editing_record_id = null;

    protected $rules = [
        // Base Info validation rules
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'required|string|max:255',
        'nationality' => 'required|string|max:50',
        'gender' => 'required|in:Male,Female',
        'birth_date' => 'required|date',
        'employment_date' => 'required|date',

        // Employee Info validation rules
        'insurance_office_id' => 'required|exists:insurance_offices,id',
        'insurance_number' => 'nullable|string|max:50',
        'insurance_amount' => 'nullable|string|max:50',
        'academic_qualification' => 'nullable|string|max:255',
        'university' => 'nullable|string|max:255',
        'graduation_year' => 'nullable|integer',
        'military_status' => 'nullable|string|max:50',
        'marital_status' => 'nullable|string|max:50',
        
        // ID Card validation rules
        'id_card_file' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,bmp,gif',
        'id_number' => 'required|string|max:50',
        'id_issue_date' => 'required|date',
        'id_expiry_date' => 'required|date|after:id_issue_date',
        
        // Birth Certificate validation rules
        'birth_certificate_file' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,bmp,gif',
        'birth_certificate_issue_date' => 'required|date',
        'birth_certificate_expiry_date' => 'nullable|date|after:birth_certificate_issue_date',
        'birth_certificate_type' => 'required|in:Original,Copy,Verified Copy',
        
        // Army Service Paper validation rules
        'army_service_paper_file' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,bmp,gif',
        'army_service_paper_issue_date' => 'required|date',
        'army_service_paper_expiry_date' => 'nullable|date|after:army_service_paper_issue_date',
        'army_service_paper_type' => 'required|in:Original,Exemption,Postponed',

        // Employee S1 Doc validation rules
        'employee_s1_doc_file' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,bmp,gif',
        's1_number' => 'required|string|max:50',
        'employee_s1_doc_issue_date' => 'required|date',
        'employee_s1_doc_expiry_date' => 'nullable|date|after:employee_s1_doc_issue_date',

        // Employee S2 Doc validation rules
        'employee_s2_doc_file' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,bmp,gif',
        's2_amount' => 'required|numeric|min:0',
        's2_year' => 'required|integer|min:1900|max:2040',
        'employee_s2_doc_issue_date' => 'required|date',
        'employee_s2_doc_expiry_date' => 'nullable|date|after:employee_s2_doc_issue_date',

        // Employee S6 Doc validation rules
        'employee_s6_doc_file' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,bmp,gif',
        's6_number' => 'required|string|max:50',
        'leaving_reason' => 'required|string',
        'employee_s6_doc_issue_date' => 'required|date',
        'employee_s6_doc_expiry_date' => 'nullable|date|after:employee_s6_doc_issue_date',

        // Employee Contract validation rules
        'employee_contract_file' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,bmp,gif',
        'employee_contract_issue_date' => 'required|date',
        'employee_contract_expiry_date' => 'nullable|date|after:employee_contract_issue_date',

        // Driver License Rules
        'driver_license_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        'driver_license_issue_date' => 'required|date',
        'driver_license_expiry_date' => 'required|date',

        // Police Record Rules
        'police_record_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        'police_record_issue_date' => 'required|date',
        'police_record_expiry_date' => 'nullable|date',

        // HR Letter Rules
        'hr_letter_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        'hr_letter_issue_date' => 'required|date',
        'hr_letter_expiry_date' => 'nullable|date',
    ];

    public function mount($id)
    {
        $this->employee = Employee::with(['info', 'idCard', 'birthCertificate', 'armyServicePaper', 'employeeS1Doc', 'employeeS2Doc', 'employeeS6Doc', 'policeRecords', 'hrLetters', 'driverLicense', 'contracts'])->findorFail($id);
        $this->insuranceOffices = InsuranceOffice::all();
        $this->militaryStatuses = Applicant::MILITARY_STATUS;
        $this->birthCertificateTypes = BirthCertificate::TYPES;
        $this->armyServicePaperTypes = ArmyServicePaper::TYPES;
        $this->employeeS6DocLeavingReasons = EmployeeS6Doc::LEAVING_REASONS;
    }
    
    public function openEditIdCardModal()
    {
        $this->resetValidation();
        if ($this->employee->idCard) {
            $this->id_number = $this->employee->idCard->id_number;
            $this->id_issue_date = $this->employee->idCard->issue_date ?? null;
            $this->id_expiry_date = $this->employee->idCard->expiry_date ?? null;
        }
        
        $this->editIdCardModal = true;
    }
    
    public function closeEditIdCardModal()
    {
        $this->editIdCardModal = false;
        $this->id_card_file = null;
        $this->keep_existing_file = false;
        $this->resetValidation();
    }
    
    public function updateIdCard()
    {
        // Validation rules change when keeping existing file
        if ($this->keep_existing_file && $this->employee->idCard) {
            $this->validate([
                'id_number' => 'required|string|max:50',
                'id_issue_date' => 'required|date',
                'id_expiry_date' => 'required|date|after:id_issue_date',
            ]);
        } else {
            $this->validate([
                'id_card_file' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,bmp,gif',
                'id_number' => 'required|string|max:50',
                'id_issue_date' => 'required|date',
                'id_expiry_date' => 'required|date|after:id_issue_date',
            ]);
        }
        
        try {
            // Get existing file path
            $path = null;
            
            if ($this->keep_existing_file && $this->employee->idCard) {
                // Keep existing file path
                $path = $this->employee->idCard->getRawOriginal('file_path');
            } else {
                // Delete existing ID card file if it exists
                if ($this->employee->idCard && $this->employee->idCard->file_path) {
                    $existingFilePath = str_replace('storage/', '', $this->employee->idCard->getRawOriginal('file_path'));
                    if (Storage::disk('s3')->exists($existingFilePath)) {
                        Storage::disk('s3')->delete($existingFilePath);
                    }
                }
                // Upload file to S3
                $path = $this->id_card_file->store(Employee::FILES_DIRECTORY.'/id_cards', 's3');
            }
            
            // Update employee ID card
            $res = $this->employee->setIDCard(
                $path,
                Carbon::parse($this->id_issue_date),
                Carbon::parse($this->id_expiry_date),
                $this->id_number
            );

            if ($res) {
                $this->closeEditIdCardModal();
                $this->alertSuccess('ID card updated successfully!');
                
                // Refresh employee data
                $this->employee = Employee::with(['info', 'idCard'])->findOrFail($this->employee->id);
            } else {
                $this->alertError();
            }
            
            // Refresh employee data
            $this->employee = Employee::with(['info', 'idCard'])->findOrFail($this->employee->id);
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }
    
    public function getFileExtension($path)
    {
        if (!$path) return null;
        
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    public function downloadIdCard()
    {
        return $this->employee->idCard->downloadFile();
    }

    public function openEditBaseInfoModal()
    {
        $this->resetValidation();
        $this->name = $this->employee->name;
        $this->email = $this->employee->email;
        $this->phone = $this->employee->phone;
        $this->address = $this->employee->address;
        $this->nationality = $this->employee->nationality;
        $this->gender = $this->employee->gender;
        $this->birth_date = $this->employee->birth_date ? $this->employee->birth_date->format('Y-m-d') : null;
        $this->employment_date = $this->employee->employment_date ? $this->employee->employment_date->format('Y-m-d') : null;
        
        $this->editBaseInfoModal = true;
    }

    public function closeEditBaseInfoModal()
    {
        $this->editBaseInfoModal = false;
    }

    public function updateBaseInfo()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'nationality' => 'required|string|max:50',
            'gender' => 'required|in:Male,Female',
            'birth_date' => 'required|date',
            'employment_date' => 'required|date',
        ]);


            $res = $this->employee->updateBaseInfo(
                $this->name,
                $this->email,
                $this->phone,
                $this->address,
                $this->nationality,
                $this->gender,
                $this->birth_date,
                $this->employment_date
            );

            if ($res) {
                $this->closeEditBaseInfoModal();
                $this->alert('success', 'Employee updated successfully!');
            } else {
                $this->alertError();
            }

    }

    public function openEditEmployeeInfoModal()
    {
        $this->resetValidation();
        if ($this->employee->info) {
            $this->insurance_office_id = $this->employee->info->insurance_office_id;
            $this->insurance_number = $this->employee->info->insurance_number;
            $this->insurance_amount = $this->employee->info->insurance_amount;
            $this->academic_qualification = $this->employee->info->academic_qualification;
            $this->university = $this->employee->info->university;
            $this->graduation_year = $this->employee->info->graduation_year;
            $this->military_status = $this->employee->info->military_status;
            $this->marital_status = $this->employee->info->marital_status;
        } else {
            // Default values for new employee info
            $this->insurance_office_id = $this->insuranceOffices->first()->id ?? null;
        }
        
        $this->editEmployeeInfoModal = true;
    }

    public function closeEditEmployeeInfoModal()
    {
        $this->editEmployeeInfoModal = false;
    }

    public function updateEmployeeInfo()
    {
        $this->validate([
            'insurance_office_id' => 'required|exists:insurance_offices,id',
            'insurance_number' => 'nullable|string|max:50',
            'insurance_amount' => 'nullable|string|max:50',
            'academic_qualification' => 'nullable|string|max:255',
            'university' => 'nullable|string|max:255',
            'graduation_year' => 'nullable|integer',
            'military_status' => 'nullable|string|max:50',
            'marital_status' => 'nullable|string|max:50',
        ]);

            $res = $this->employee->updateEmployeeInfo(
                $this->insurance_office_id,
                $this->insurance_number,
                $this->insurance_amount,
                $this->academic_qualification,
                $this->university,
                $this->graduation_year,
                $this->military_status,
                $this->marital_status
            );
            if ($res) {
                $this->closeEditEmployeeInfoModal();
                $this->alert('success', 'Employee information updated successfully!');
            } else {
                $this->alertError();
            }
            
            
    }

    public function openEditBirthCertificateModal()
    {
        $this->resetValidation();
        if ($this->employee->birthCertificate) {
            $this->birth_certificate_issue_date = $this->employee->birthCertificate->issue_date ?? null;
            $this->birth_certificate_expiry_date = $this->employee->birthCertificate->expiry_date ?? null;
            $this->birth_certificate_type = $this->employee->birthCertificate->type ?? BirthCertificate::TYPE_ORIGINAL;
        } else {
            $this->birth_certificate_type = BirthCertificate::TYPE_ORIGINAL;
        }
        
        $this->editBirthCertificateModal = true;
    }
    
    public function closeEditBirthCertificateModal()
    {
        $this->editBirthCertificateModal = false;
        $this->birth_certificate_file = null;
        $this->keep_existing_birth_certificate = false;
        $this->resetValidation();
    }
    
    public function updateBirthCertificate()
    {
        // Validation rules change when keeping existing file
        if ($this->keep_existing_birth_certificate && $this->employee->birthCertificate) {
            $this->validate([
                'birth_certificate_issue_date' => 'required|date',
                'birth_certificate_expiry_date' => 'nullable|date|after:birth_certificate_issue_date',
                'birth_certificate_type' => 'required|in:'.implode(',', BirthCertificate::TYPES),
            ]);
        } else {
            $this->validate([
                'birth_certificate_file' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,bmp,gif',
                'birth_certificate_issue_date' => 'required|date',
                'birth_certificate_expiry_date' => 'nullable|date|after:birth_certificate_issue_date',
                'birth_certificate_type' => 'required|in:'.implode(',', BirthCertificate::TYPES),
            ]);
        }
        // dd($this->birth_certificate_expiry_date ? Carbon::parse($this->birth_certificate_expiry_date) : null);
        try {
            // Get existing file path
            $path = null;
            
            if ($this->keep_existing_birth_certificate && $this->employee->birthCertificate) {
                // Keep existing file path
                $path = $this->employee->birthCertificate->getRawOriginal('file_path');
            } else {
                // Delete existing birth certificate file if it exists
                if ($this->employee->birthCertificate && $this->employee->birthCertificate->file_path) {
                    $existingFilePath = str_replace('storage/', '', $this->employee->birthCertificate->getRawOriginal('file_path'));
                    if (Storage::disk('s3')->exists($existingFilePath)) {
                        Storage::disk('s3')->delete($existingFilePath);
                    }
                }
                // Upload file to S3
                $path = $this->birth_certificate_file->store(Employee::FILES_DIRECTORY.'/birth_certificates', 's3');
            }

            // dd($path);
            
            // Update employee birth certificate
            $res = $this->employee->setBirthCertificate(
                $path,
                Carbon::parse($this->birth_certificate_issue_date),
                $this->birth_certificate_type,
                $this->birth_certificate_expiry_date ? Carbon::parse($this->birth_certificate_expiry_date) : null
            );
            if ($res) {
                $this->closeEditBirthCertificateModal();
                $this->alertSuccess('Birth certificate updated successfully!');
                
                // Refresh employee data
                $this->employee = Employee::with(['info', 'idCard', 'birthCertificate'])->findOrFail($this->employee->id);
            } else {
                $this->alertError();
            }
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }

        // dd($this->employee->birthCertificate->expiry_date);

    }

    public function downloadBirthCertificate()
    {
        return $this->employee->birthCertificate->downloadFile();
    }

    // Army Service Paper Methods
    public function openEditArmyServicePaperModal()
    {
        $this->resetValidation();
        $this->editArmyServicePaperModal = true;

        if ($this->employee->armyServicePaper) {
            $this->army_service_paper_issue_date = $this->employee->armyServicePaper->issue_date;
            $this->army_service_paper_expiry_date = $this->employee->armyServicePaper->expiry_date;
        }
    }
    
    public function closeEditArmyServicePaperModal()
    {
        $this->editArmyServicePaperModal = false;
        $this->army_service_paper_file = null;
        $this->keep_existing_army_service_paper = false;
        $this->resetValidation();
    }
    
    public function updateArmyServicePaper()
    {
        // Validation rules change when keeping existing file
        if ($this->keep_existing_army_service_paper && $this->employee->armyServicePaper) {
            $this->validate([
                'army_service_paper_issue_date' => 'required|date',
                'army_service_paper_expiry_date' => 'nullable|date|after:army_service_paper_issue_date',
                'army_service_paper_type' => 'required|in:'.implode(',', \App\Models\Personel\Docs\ArmyServicePaper::TYPES),
            ]);
        } else {
            $this->validate([
                'army_service_paper_file' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,bmp,gif',
                'army_service_paper_issue_date' => 'required|date',
                'army_service_paper_expiry_date' => 'nullable|date|after:army_service_paper_issue_date',
                'army_service_paper_type' => 'required|in:'.implode(',', \App\Models\Personel\Docs\ArmyServicePaper::TYPES),
            ]);
        }

        try {
            // Get existing file path
            $path = null;
            
            if ($this->keep_existing_army_service_paper && $this->employee->armyServicePaper) {
                // Keep existing file path
                $path = $this->employee->armyServicePaper->getRawOriginal('file_path');
            } else {
                // Delete existing army service paper file if it exists
                if ($this->employee->armyServicePaper && $this->employee->armyServicePaper->file_path) {
                    $existingFilePath = str_replace('storage/', '', $this->employee->armyServicePaper->getRawOriginal('file_path'));
                    if (Storage::disk('s3')->exists($existingFilePath)) {
                        Storage::disk('s3')->delete($existingFilePath);
                    }
                }
                // Upload file to S3
                $path = $this->army_service_paper_file->store(Employee::FILES_DIRECTORY.'/army_service_papers', 's3');
            }
            
            // Update employee army service paper
            $res = $this->employee->setArmyServicePaper(
                $path,
                Carbon::parse($this->army_service_paper_issue_date),
                $this->army_service_paper_type,
                $this->army_service_paper_expiry_date ? Carbon::parse($this->army_service_paper_expiry_date) : null
            );

            if ($res) {
                $this->closeEditArmyServicePaperModal();
                $this->alertSuccess('Army service paper updated successfully!');
                
                // Refresh employee data
                $this->employee = Employee::with(['info', 'idCard', 'birthCertificate', 'armyServicePaper'])->findOrFail($this->employee->id);
            } else {
                $this->alertError();
            }
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function downloadArmyServicePaper()
    {
        if (!$this->employee->armyServicePaper) {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'No army service paper found.'
            ]);
            return;
        }

        try {
            return response()->download(
                storage_path('app/public/' . $this->employee->armyServicePaper->file_path),
                'army_service_paper.' . pathinfo($this->employee->armyServicePaper->file_path, PATHINFO_EXTENSION)
            );
        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Failed to download army service paper. ' . $e->getMessage()
            ]);
        }
    }

    // Employee S1 Doc Methods
    public function openEditEmployeeS1DocModal()
    {
        $this->resetValidation();
        $this->editEmployeeS1DocModal = true;

        if ($this->employee->employeeS1Doc) {
            $this->s1_number = $this->employee->employeeS1Doc->s1_number;
            $this->employee_s1_doc_issue_date = $this->employee->employeeS1Doc->issue_date;
            if ($this->employee->employeeS1Doc->expiry_date) {
                $this->employee_s1_doc_expiry_date = $this->employee->employeeS1Doc->expiry_date;
            }
        }
    }
    
    public function closeEditEmployeeS1DocModal()
    {
        $this->editEmployeeS1DocModal = false;
        $this->employee_s1_doc_file = null;
        $this->keep_existing_employee_s1_doc = false;
        $this->resetValidation();
    }
    
    public function updateEmployeeS1Doc()
    {
        // Validation rules change when keeping existing file
        if ($this->keep_existing_employee_s1_doc && $this->employee->employeeS1Doc) {
            $this->validate([
                's1_number' => 'required|string|max:50',
                'employee_s1_doc_issue_date' => 'required|date',
                'employee_s1_doc_expiry_date' => 'nullable|date|after:employee_s1_doc_issue_date',
            ]);
        } else {
            $this->validate([
                'employee_s1_doc_file' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,bmp,gif',
                's1_number' => 'required|string|max:50',
                'employee_s1_doc_issue_date' => 'required|date',
                'employee_s1_doc_expiry_date' => 'nullable|date|after:employee_s1_doc_issue_date',
            ]);
        }

        try {
            // Get existing file path
            $path = null;
            
            if ($this->keep_existing_employee_s1_doc && $this->employee->employeeS1Doc) {
                // Keep existing file path
                $path = $this->employee->employeeS1Doc->getRawOriginal('file_path');
            } else {
                // Delete existing employee S1 doc file if it exists
                if ($this->employee->employeeS1Doc && $this->employee->employeeS1Doc->file_path) {
                    $existingFilePath = str_replace('storage/', '', $this->employee->employeeS1Doc->getRawOriginal('file_path'));
                    if (Storage::disk('s3')->exists($existingFilePath)) {
                        Storage::disk('s3')->delete($existingFilePath);
                    }
                }
                // Upload file to S3
                $path = $this->employee_s1_doc_file->store(Employee::FILES_DIRECTORY.'/employee_s1_docs', 's3');
            }
            
            // Update employee S1 doc
            $res = $this->employee->setEmployeeS1Doc(
                $path,
                Carbon::parse($this->employee_s1_doc_issue_date),
                $this->employee_s1_doc_expiry_date ? Carbon::parse($this->employee_s1_doc_expiry_date) : null,
                $this->s1_number
            );

            if ($res) {
                $this->closeEditEmployeeS1DocModal();
                $this->alertSuccess('Employee S1 doc updated successfully!');
                
                // Refresh employee data
                $this->employee = Employee::with(['info', 'idCard', 'birthCertificate', 'armyServicePaper', 'employeeS1Doc'])->findOrFail($this->employee->id);
            } else {
                $this->alertError();
            }
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function downloadEmployeeS1Doc()
    {
        return $this->employee->employeeS1Doc->downloadFile();
    }

    // Employee S2 Doc Methods
    public function openEditEmployeeS2DocModal()
    {
        $this->resetValidation();
        $this->editEmployeeS2DocModal = true;
        $this->editing_record_id = null;
        
        // Reset form fields for new record
        $this->s2_amount = null;
        $this->s2_year = date('Y');
        $this->employee_s2_doc_issue_date = date('Y-m-d');
        $this->employee_s2_doc_expiry_date = null;
    }
    
    public function openEditSpecificS2DocModal($recordId)
    {
        $this->resetValidation();
        $s2Doc = \App\Models\Personel\Docs\EmployeeS2Doc::findOrFail($recordId);
        
        if ($s2Doc->employee_id != $this->employee->id) {
            $this->alertError('Invalid record');
            return;
        }
        
        $this->editing_record_id = $recordId;
        $this->s2_amount = $s2Doc->s2_amount;
        $this->s2_year = $s2Doc->year;
        $this->employee_s2_doc_issue_date = $s2Doc->issue_date ? $s2Doc->issue_date : null;
        $this->employee_s2_doc_expiry_date = $s2Doc->expiry_date ? $s2Doc->expiry_date : null;
        $this->keep_existing_employee_s2_doc = true;
        $this->editEmployeeS2DocModal = true;
    }
    
    public function closeEditEmployeeS2DocModal()
    {
        $this->editEmployeeS2DocModal = false;
        $this->employee_s2_doc_file = null;
        $this->keep_existing_employee_s2_doc = false;
        $this->editing_record_id = null;
        $this->resetValidation();
    }
    
    public function updateEmployeeS2Doc()
    {
        // Different validation rules for updating vs creating
        if ($this->editing_record_id && $this->keep_existing_employee_s2_doc) {
            $validationRules = [
                's2_amount' => 'required|numeric|min:0',
                's2_year' => 'required|integer|min:1900|max:2040',
                'employee_s2_doc_issue_date' => 'required|date',
                'employee_s2_doc_expiry_date' => 'nullable|date|after:employee_s2_doc_issue_date',
            ];
        } else {
            $validationRules = [
                'employee_s2_doc_file' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,bmp,gif',
                's2_amount' => 'required|numeric|min:0',
                's2_year' => 'required|integer|min:1900|max:2040',
                'employee_s2_doc_issue_date' => 'required|date',
                'employee_s2_doc_expiry_date' => 'nullable|date|after:employee_s2_doc_issue_date',
            ];
        }

        // Validation rules
        $this->validate($validationRules);

        try {
            if ($this->editing_record_id) {
                // Editing existing record
                $s2Doc = \App\Models\Personel\Docs\EmployeeS2Doc::findOrFail($this->editing_record_id);
                
                $path = null;
                if ($this->keep_existing_employee_s2_doc) {
                    // Keep existing file path
                    $path = $s2Doc->getRawOriginal('file_path');
                } else {
                    // Delete existing file if it exists
                    $existingFilePath = str_replace('storage/', '', $s2Doc->getRawOriginal('file_path'));
                    if (Storage::disk('s3')->exists($existingFilePath)) {
                        Storage::disk('s3')->delete($existingFilePath);
                    }
                    // Upload file to S3
                    $path = $this->employee_s2_doc_file->store(Employee::FILES_DIRECTORY.'/employee_s2_docs', 's3');
                }
                
                // Update existing record using the updateRecord method
                $res = $s2Doc->updateRecord(
                    $path,
                    Carbon::parse($this->employee_s2_doc_issue_date),
                    $this->employee_s2_doc_expiry_date ? Carbon::parse($this->employee_s2_doc_expiry_date) : null,
                    $this->s2_amount,
                    $this->s2_year
                );
                
                $this->closeEditEmployeeS2DocModal();
                $this->alertSuccess('Employee S2 doc updated successfully!');
            } else {
                // Creating a new record
                $path = $this->employee_s2_doc_file->store(Employee::FILES_DIRECTORY.'/employee_s2_docs', 's3');
                
                // Update employee S2 doc - always create a new record for S2
                $res = $this->employee->setEmployeeS2Doc(
                    $path,
                    Carbon::parse($this->employee_s2_doc_issue_date),
                    $this->employee_s2_doc_expiry_date ? Carbon::parse($this->employee_s2_doc_expiry_date) : null,
                    $this->s2_amount,
                    $this->s2_year
                );

                if ($res) {
                    $this->closeEditEmployeeS2DocModal();
                    $this->alertSuccess('Employee S2 doc added successfully!');
                } else {
                    $this->alertError();
                }
            }
            
            // Refresh employee data
            $this->employee = Employee::with(['info', 'idCard', 'birthCertificate', 'armyServicePaper', 'employeeS1Doc', 'employeeS2Doc', 'employeeS6Doc', 'contracts'])->findOrFail($this->employee->id);
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function downloadEmployeeS2Doc($docId = null)
    {
        try {
            if ($docId) {
                // Find the specific S2 doc by ID
                $s2Doc = \App\Models\Personel\Docs\EmployeeS2Doc::findOrFail($docId);
                if ($s2Doc->employee_id != $this->employee->id) {
                    throw new \Exception('Document not associated with this employee');
                }
                return $s2Doc->downloadFile();
            } else {
                // Fallback to download the first doc if no ID provided
                if (count($this->employee->employeeS2Doc) > 0) {
                    return $this->employee->employeeS2Doc[0]->downloadFile();
                }
            }
            
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'No employee S2 doc found.'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Error downloading document: ' . $e->getMessage()
            ]);
        }
    }

    // Employee S6 Doc Methods
    public function openEditEmployeeS6DocModal()
    {
        $this->resetValidation();
        $s6Doc = $this->employee->employeeS6Doc()->first();
        
        if ($s6Doc->employee_id != $this->employee->id) {
            $this->alertError('Invalid record');
            return;
        }
        
        if ($s6Doc) {
        $this->s6_number = $s6Doc->s6_number;
        $this->leaving_reason = $s6Doc->leaving_reason;
        $this->employee_s6_doc_issue_date = $s6Doc->issue_date ? $s6Doc->issue_date : null;
        $this->employee_s6_doc_expiry_date = $s6Doc->expiry_date ? $s6Doc->expiry_date : null;
        $this->keep_existing_employee_s6_doc = true;
        }
        $this->editEmployeeS6DocModal = true;
    }
    
    public function closeEditEmployeeS6DocModal()
    {
        $this->editEmployeeS6DocModal = false;
        $this->employee_s6_doc_file = null;
        $this->keep_existing_employee_s6_doc = false;
        $this->editing_record_id = null;
        $this->resetValidation();
    }
    
    public function updateEmployeeS6Doc()
    {
        // Validation rules change when keeping existing file
        if ($this->keep_existing_employee_s6_doc && $this->employee->employeeS6Doc()->first()) {
            $this->validate([
                's6_number' => 'required|string|max:50',
                'leaving_reason' => 'required|string|in:' . implode(',', \App\Models\Personel\Docs\EmployeeS6Doc::LEAVING_REASONS),
                'employee_s6_doc_issue_date' => 'required|date',
                'employee_s6_doc_expiry_date' => 'nullable|date|after:employee_s6_doc_issue_date',
            ]);
        } else {
            $this->validate([
                'employee_s6_doc_file' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,bmp,gif',
                's6_number' => 'required|string|max:50',
                'leaving_reason' => 'required|string|in:' . implode(',', \App\Models\Personel\Docs\EmployeeS6Doc::LEAVING_REASONS),
                'employee_s6_doc_issue_date' => 'required|date',
                'employee_s6_doc_expiry_date' => 'nullable|date|after:employee_s6_doc_issue_date',
            ]);
        }

        try {
            // Get existing file path
            $path = null;
            
            if ($this->keep_existing_employee_s6_doc && $this->employee->employeeS6Doc()->first()) {
                // Keep existing file path
                $path = $this->employee->employeeS6Doc()->first()->getRawOriginal('file_path');
            } else {
                // Delete existing S6 doc file if it exists
                if ($this->employee->employeeS6Doc()->first() && $this->employee->employeeS6Doc()->first()->file_path) {
                    $existingFilePath = str_replace('storage/', '', $this->employee->employeeS6Doc()->first()->getRawOriginal('file_path'));
                    if (Storage::disk('s3')->exists($existingFilePath)) {
                        Storage::disk('s3')->delete($existingFilePath);
                    }
                }
                // Upload file to S3
                $path = $this->employee_s6_doc_file->store(Employee::FILES_DIRECTORY.'/employee_s6_docs', 's3');
            }
            
            // Update employee S6 doc
            $res = $this->employee->setEmployeeS6Doc(
                $path,
                Carbon::parse($this->employee_s6_doc_issue_date),
                $this->employee_s6_doc_expiry_date ? Carbon::parse($this->employee_s6_doc_expiry_date) : null,
                $this->s6_number,
                $this->leaving_reason
            );

            if ($res) {
                $this->closeEditEmployeeS6DocModal();
                $this->alertSuccess('Employee S6 doc updated successfully!');
                
                // Refresh employee data
                $this->employee = Employee::with(['info', 'idCard', 'birthCertificate', 'armyServicePaper', 'employeeS1Doc', 'employeeS2Doc', 'employeeS6Doc'])->findOrFail($this->employee->id);
            } else {
                $this->alertError();
            }
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function downloadEmployeeS6Doc($docId = null)
    {
        try {
            if ($docId) {
                // Find the specific S6 doc by ID
                $s6Doc = \App\Models\Personel\Docs\EmployeeS6Doc::findOrFail($docId);
                if ($s6Doc->employee_id != $this->employee->id) {
                    throw new \Exception('Document not associated with this employee');
                }
                return $s6Doc->downloadFile();
            } else {
                // Fallback to download the first doc if no ID provided
                if (count($this->employee->employeeS6Doc) > 0) {
                    return $this->employee->employeeS6Doc[0]->downloadFile();
                }
            }
            
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'No employee S6 doc found.'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Error downloading document: ' . $e->getMessage()
            ]);
        }
    }

    public function openEditEmployeeContractModal()
    {
        $this->resetValidation();
        $this->editEmployeeContractModal = true;
        $this->editing_contract_id = null;

        if ($this->employee->contracts && $this->employee->contracts->count() > 0) {
            $this->employee_contract_issue_date = date('Y-m-d');
            $this->employee_contract_expiry_date = null;
        }
    }

    public function openEditSpecificContractModal($contractId)
    {
        $this->resetValidation();
        $contract = \App\Models\Personel\Docs\EmployeeContract::findOrFail($contractId);
        
        if ($contract->employee_id != $this->employee->id) {
            $this->alertError('Invalid record');
            return;
        }
        
        $this->editing_contract_id = $contractId;
        $this->employee_contract_issue_date = $contract->issue_date ? $contract->issue_date : null;
        $this->employee_contract_expiry_date = $contract->expiry_date ? $contract->expiry_date : null;
        $this->keep_existing_employee_contract = true;
        $this->editEmployeeContractModal = true;
    }
    
    public function closeEditEmployeeContractModal()
    {
        $this->editEmployeeContractModal = false;
        $this->resetEmployeeContractFields();
    }

    private function resetEmployeeContractFields()
    {
        $this->reset([
            'keep_existing_employee_contract',
            'employee_contract_file',
            'employee_contract_issue_date',
            'employee_contract_expiry_date',
            'editing_contract_id'
        ]);
    }

    public function updateEmployeeContract()
    {
        $validationRules = [
            'employee_contract_issue_date' => 'required|date',
            'employee_contract_expiry_date' => 'nullable|date',
        ];

        if (!$this->keep_existing_employee_contract) {
            $validationRules['employee_contract_file'] = 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,bmp,gif';
        }

        $this->validate($validationRules);

        try {
            $path = null;
            if (!$this->keep_existing_employee_contract && $this->employee_contract_file) {
                $path = $this->employee_contract_file->store(Employee::FILES_DIRECTORY.'/employee_contracts', 's3');
            } else if ($this->keep_existing_employee_contract && $this->editing_contract_id) {
                $existingRecord = \App\Models\Personel\Docs\EmployeeContract::findOrFail($this->editing_contract_id);
                $path = $existingRecord->getRawOriginal('file_path');
            } else if ($this->keep_existing_employee_contract && $this->employee->contracts->count() > 0) {
                $path = $this->employee->contracts->last()->getRawOriginal('file_path');
            }

            // If editing an existing record
            if ($this->editing_contract_id) {
                $employeeContract = \App\Models\Personel\Docs\EmployeeContract::findOrFail($this->editing_contract_id);
                $res = $employeeContract->updateRecord(
                    $path,
                    Carbon::parse($this->employee_contract_issue_date),
                    $this->employee_contract_expiry_date ? Carbon::parse($this->employee_contract_expiry_date) : null
                );
            } else {
                // Creating a new record
                $res = $this->employee->setEmployeeContract(
                    $path,
                    Carbon::parse($this->employee_contract_issue_date),
                    $this->employee_contract_expiry_date ? Carbon::parse($this->employee_contract_expiry_date) : null
                );
            }

            if ($res) {
                $this->alertSuccess('Employee contract updated successfully!');
                $this->closeEditEmployeeContractModal();
                
                // Refresh employee data
                $this->employee = Employee::with(['info', 'idCard', 'birthCertificate', 'armyServicePaper', 'employeeS1Doc', 'employeeS2Doc', 'employeeS6Doc', 'policeRecords', 'hrLetters', 'driverLicense', 'contracts'])->findOrFail($this->employee->id);
            } else {
                $this->alertError();
            }
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function downloadEmployeeContract($docId = null)
    {
        try {
            if ($docId) {
                // Find the specific Employee Contract by ID
                $employeeContract = \App\Models\Personel\Docs\EmployeeContract::findOrFail($docId);
                if ($employeeContract->employee_id != $this->employee->id) {
                    throw new \Exception('Document not associated with this employee');
                }
                return $employeeContract->downloadFile();
            } else {
                // Fallback to download the first doc if no ID provided
                if ($this->employee->contracts->count() > 0) {
                    return $this->employee->contracts->first()->downloadFile();
                }
            }
            
            $this->alertError('No employee contract found.');
        } catch (\Exception $e) {
            $this->alertError('Error downloading document: ' . $e->getMessage());
        }
    }

    public function deleteEmployeeContractModal($recordId)
    {
        $employeeContract = \App\Models\Personel\Docs\EmployeeContract::findOrFail($recordId);
        $employeeContract->deleteRecord();
        $this->alertSuccess('Employee contract deleted successfully!');
        
        // Refresh employee data
        $this->employee = Employee::with(['info', 'idCard', 'birthCertificate', 'armyServicePaper', 'employeeS1Doc', 'employeeS2Doc', 'employeeS6Doc', 'policeRecords', 'hrLetters', 'driverLicense', 'contracts'])->findOrFail($this->employee->id);
    }

    public function openEditDriverLicenseModal()
    {
        $this->resetValidation();
        $this->editDriverLicenseModal = true;

        if ($this->employee->driverLicense) {
            $this->driver_license_issue_date = $this->employee->driverLicense->issue_date;
            $this->driver_license_expiry_date = $this->employee->driverLicense->expiry_date;
        }
    }

    public function closeEditDriverLicenseModal()
    {
        $this->editDriverLicenseModal = false;
        $this->resetDriverLicenseFields();
    }

    private function resetDriverLicenseFields()
    {
        $this->reset([
            'keep_existing_driver_license',
            'driver_license_file',
            'driver_license_issue_date',
            'driver_license_expiry_date'
        ]);
    }

    public function updateDriverLicense()
    {
        $validationRules = [
            'driver_license_issue_date' => 'required|date',
            'driver_license_expiry_date' => 'required|date',
        ];

        if (!$this->keep_existing_driver_license) {
            $validationRules['driver_license_file'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:2048';
        }

        $this->validate($validationRules);

        try {
            if (!$this->keep_existing_driver_license && $this->driver_license_file) {
                $path = $this->driver_license_file->store(Employee::FILES_DIRECTORY.'/driver_licenses', 's3');
            }

            $res = $this->employee->setDriverLicense(
                $path ?? ($this->employee->driverLicense?->file_path ?? null),
                Carbon::parse($this->driver_license_issue_date),
                Carbon::parse($this->driver_license_expiry_date)
            );

            if ($res) {
                $this->alertSuccess('Driver license updated successfully!');
                $this->closeEditDriverLicenseModal();
            } else {
                $this->alertError();
            }
        } catch (\Exception $e) {
            $this->alertError();
        }
    }

    public function downloadDriverLicense()
    {
        return $this->employee->driverLicense->downloadFile();
    }

    // Police Record Edit Methods

    protected $listeners = ['deletePoliceRecordModal', 'deleteHrLetterModal', 'deleteEmployeeS2DocModal', 'deleteEmployeeS6DocModal', 'deleteEmployeeContractModal'];
    public function openEditPoliceRecordModal()
    {
        $this->resetValidation();
        $this->editPoliceRecordModal = true;
        $this->police_record_issue_date = date('Y-m-d');
        $this->police_record_expiry_date = null;
    }

    public function openEditSpecificPoliceRecordModal($recordId)
    {
        $this->resetValidation();
        $policeRecord = \App\Models\Personel\Docs\PoliceRecord::findOrFail($recordId);
        
        if ($policeRecord->employee_id != $this->employee->id) {
            $this->alertError('Invalid record');
            return;
        }
        
        $this->editing_record_id = $recordId;
        $this->police_record_issue_date = $policeRecord->issue_date;
        $this->police_record_expiry_date = $policeRecord->expiry_date ? $policeRecord->expiry_date : null;
        $this->editPoliceRecordModal = true;
    }

    public function closeEditPoliceRecordModal()
    {
        $this->editPoliceRecordModal = false;
        $this->resetPoliceRecordFields();
    }

    private function resetPoliceRecordFields()
    {
        $this->reset([
            'keep_existing_police_record',
            'police_record_file',
            'police_record_issue_date',
            'police_record_expiry_date',
            'editing_record_id'
        ]);
    }

    public function updatePoliceRecord()
    {
        $validationRules = [
            'police_record_issue_date' => 'required|date',
            'police_record_expiry_date' => 'nullable|date|after:police_record_issue_date',
        ];

        if (!$this->keep_existing_police_record) {
            $validationRules['police_record_file'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:2048';
        }

        $this->validate($validationRules);

        try {
            $path = null;
            if (!$this->keep_existing_police_record && $this->police_record_file) {
                $path = $this->police_record_file->store(Employee::FILES_DIRECTORY.'/police_records', 's3');
            } else if ($this->keep_existing_police_record && $this->editing_record_id) {
                $existingRecord = \App\Models\Personel\Docs\PoliceRecord::findOrFail($this->editing_record_id);
                $path = $existingRecord->getRawOriginal('file_path');
            } else if ($this->keep_existing_police_record && $this->employee->policeRecords->count() > 0) {
                $path = $this->employee->policeRecords->last()->getRawOriginal('file_path');
            }

            // If editing an existing record
            if ($this->editing_record_id) {
                $policeRecord = \App\Models\Personel\Docs\PoliceRecord::findOrFail($this->editing_record_id);
                $res = $policeRecord->updateRecord(
                    $path,
                    Carbon::parse($this->police_record_issue_date),
                    $this->police_record_expiry_date ? Carbon::parse($this->police_record_expiry_date) : null
                );
            } else {
                // Creating a new record
                $res = $this->employee->setPoliceRecord(
                    $path,
                    Carbon::parse($this->police_record_issue_date),
                    $this->police_record_expiry_date ? Carbon::parse($this->police_record_expiry_date) : null
                );
            }

            if ($res) {
                $this->alertSuccess('Police record updated successfully!');
                $this->closeEditPoliceRecordModal();
                
                // Refresh employee data
                $this->employee = Employee::with(['info', 'idCard', 'birthCertificate', 'armyServicePaper', 'employeeS1Doc', 'employeeS2Doc', 'employeeS6Doc', 'policeRecords', 'hrLetters', 'driverLicense'])->findOrFail($this->employee->id);
            } else {
                $this->alertError();
            }
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function deletePoliceRecordModal($recordId)
    {
        $policeRecord = PoliceRecord::findOrFail($recordId);
        $policeRecord->deleteRecord();
        $this->alertSuccess('Police record deleted successfully!');
        
        // Refresh employee data
        $this->employee = Employee::with(['info', 'idCard', 'birthCertificate', 'armyServicePaper', 'employeeS1Doc', 'employeeS2Doc', 'employeeS6Doc', 'policeRecords', 'hrLetters', 'driverLicense'])->findOrFail($this->employee->id);
    }

    public function downloadPoliceRecord($docId = null)
    {
        try {
            if ($docId) {
                // Find the specific Police Record by ID
                $policeRecord = \App\Models\Personel\Docs\PoliceRecord::findOrFail($docId);
                if ($policeRecord->employee_id != $this->employee->id) {
                    throw new \Exception('Document not associated with this employee');
                }
                return $policeRecord->downloadFile();
            } else {
                // Fallback to download the first doc if no ID provided
                if ($this->employee->policeRecords->count() > 0) {
                    return $this->employee->policeRecords->first()->downloadFile();
                }
            }
            
            $this->alertError('No police record found.');
        } catch (\Exception $e) {
            $this->alertError('Error downloading document: ' . $e->getMessage());
        }
    }

    // HR Letter Edit Methods
    public function openEditHrLetterModal()
    {
        $this->resetValidation();
        $this->editHrLetterModal = true;
        $this->hr_letter_issue_date = date('Y-m-d');
        $this->hr_letter_expiry_date = null;
    }

    public function openEditSpecificHrLetterModal($recordId)
    {
        $this->resetValidation();
        $hrLetter = \App\Models\Personel\Docs\HrLetter::findOrFail($recordId);
        
        if ($hrLetter->employee_id != $this->employee->id) {
            $this->alertError('Invalid record');
            return;
        }
        
        $this->editing_record_id = $recordId;
        $this->hr_letter_issue_date = $hrLetter->issue_date;
        $this->hr_letter_expiry_date = $hrLetter->expiry_date ? $hrLetter->expiry_date : null;
        $this->editHrLetterModal = true;
    }

    public function closeEditHrLetterModal()
    {
        $this->editHrLetterModal = false;
        $this->resetHrLetterFields();
    }

    private function resetHrLetterFields()
    {
        $this->reset([
            'keep_existing_hr_letter',
            'hr_letter_file',
            'hr_letter_issue_date',
            'hr_letter_expiry_date',
            'editing_record_id'
        ]);
    }

    public function updateHrLetter()
    {
        $validationRules = [
            'hr_letter_issue_date' => 'required|date',
            'hr_letter_expiry_date' => 'nullable|date',
        ];

        if (!$this->keep_existing_hr_letter) {
            $validationRules['hr_letter_file'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:2048';
        }

        $this->validate($validationRules);

        try {
            $path = null;
            if (!$this->keep_existing_hr_letter && $this->hr_letter_file) {
                $path = $this->hr_letter_file->store(Employee::FILES_DIRECTORY.'/hr_letters', 's3');
            } else if ($this->keep_existing_hr_letter && $this->editing_record_id) {
                $existingRecord = \App\Models\Personel\Docs\HrLetter::findOrFail($this->editing_record_id);
                $path = $existingRecord->getRawOriginal('file_path');
            } else if ($this->keep_existing_hr_letter && $this->employee->hrLetters->count() > 0) {
                $path = $this->employee->hrLetters->last()->getRawOriginal('file_path');
            }

            // If editing an existing record
            if ($this->editing_record_id) {
                $hrLetter = \App\Models\Personel\Docs\HrLetter::findOrFail($this->editing_record_id);
                $res = $hrLetter->updateRecord(
                    $path,
                    Carbon::parse($this->hr_letter_issue_date),
                    $this->hr_letter_expiry_date ? Carbon::parse($this->hr_letter_expiry_date) : null
                );
            } else {
                // Creating a new record
                $res = $this->employee->setHrLetter(
                    $path,
                    Carbon::parse($this->hr_letter_issue_date),
                    $this->hr_letter_expiry_date ? Carbon::parse($this->hr_letter_expiry_date) : null
                );
            }

            if ($res) {
                $this->alertSuccess('HR letter updated successfully!');
                $this->closeEditHrLetterModal();
                
                // Refresh employee data
                $this->employee = Employee::with(['info', 'idCard', 'birthCertificate', 'armyServicePaper', 'employeeS1Doc', 'employeeS2Doc', 'employeeS6Doc', 'policeRecords', 'hrLetters', 'driverLicense'])->findOrFail($this->employee->id);
            } else {
                $this->alertError();
            }
        } catch (\Exception $e) {
            $this->alertError($e->getMessage());
        }
    }

    public function downloadHrLetter($docId = null)
    {
        try {
            if ($docId) {
                // Find the specific HR Letter by ID
                $hrLetter = \App\Models\Personel\Docs\HrLetter::findOrFail($docId);
                if ($hrLetter->employee_id != $this->employee->id) {
                    throw new \Exception('Document not associated with this employee');
                }
                return $hrLetter->downloadFile();
            } else {
                // Fallback to download the first doc if no ID provided
                if ($this->employee->hrLetters->count() > 0) {
                    return $this->employee->hrLetters->first()->downloadFile();
                }
            }
            
            $this->alertError('No HR letter found.');
        } catch (\Exception $e) {
            $this->alertError('Error downloading document: ' . $e->getMessage());
        }
    }

    public function deleteHrLetterModal($recordId)
    {
        $hrLetter = \App\Models\Personel\Docs\HrLetter::findOrFail($recordId);
        $hrLetter->deleteRecord();
        $this->alertSuccess('HR letter deleted successfully!');
        
        // Refresh employee data
        $this->employee = Employee::with(['info', 'idCard', 'birthCertificate', 'armyServicePaper', 'employeeS1Doc', 'employeeS2Doc', 'employeeS6Doc', 'policeRecords', 'hrLetters', 'driverLicense'])->findOrFail($this->employee->id);
    }

    public function deleteEmployeeS2DocModal($recordId)
    {
        $s2Doc = \App\Models\Personel\Docs\EmployeeS2Doc::findOrFail($recordId);
        
        try {
            // Delete the S2 document
            $s2Doc->delete();
            $this->alertSuccess('Employee S2 document deleted successfully!');
            
            // Refresh employee data
            $this->employee = Employee::with(['info', 'idCard', 'birthCertificate', 'armyServicePaper', 'employeeS1Doc', 'employeeS2Doc', 'employeeS6Doc', 'policeRecords', 'hrLetters', 'driverLicense'])->findOrFail($this->employee->id);
        } catch (\Exception $e) {
            $this->alertError('Error deleting Employee S2 document: ' . $e->getMessage());
        }
    }

    public function deleteEmployeeS6DocModal($recordId)
    {
        $s6Doc = \App\Models\Personel\Docs\EmployeeS6Doc::findOrFail($recordId);
        
        try {
            // Delete the S6 document
            $s6Doc->delete();
            $this->alertSuccess('Employee S6 document deleted successfully!');
            
            // Refresh employee data
            $this->employee = Employee::with(['info', 'idCard', 'birthCertificate', 'armyServicePaper', 'employeeS1Doc', 'employeeS2Doc', 'employeeS6Doc', 'policeRecords', 'hrLetters', 'driverLicense'])->findOrFail($this->employee->id);
        } catch (\Exception $e) {
            $this->alertError('Error deleting Employee S6 document: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.employee.employee-show');
    }
}
