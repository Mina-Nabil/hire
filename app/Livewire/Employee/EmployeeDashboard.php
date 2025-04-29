<?php

namespace App\Livewire\Employee;

use App\Models\Personel\Employee;
use Livewire\Component;

class EmployeeDashboard extends Component
{
    public $idCardStats;
    public $birthCertificateStats;
    public $armyServicePaperStats;
    public $employmentContractStats;
    public $driverLicenseStats;
    public $policeRecordStats;
    public $hrLetterStats;
    public $s1DocStats;
    public $s2DocStats;
    public $s6DocStats;

    public function mount()
    {
        $this->loadIdCardStats();
        $this->loadBirthCertificateStats();
        $this->loadArmyServicePaperStats();
        $this->loadEmploymentContractStats();
        $this->loadDriverLicenseStats();
        $this->loadPoliceRecordStats();
        $this->loadHrLetterStats();
        $this->loadS1DocStats();
        $this->loadS2DocStats();
        $this->loadS6DocStats();
    }

    public function loadIdCardStats()
    {
        $this->idCardStats = Employee::getIdCardStatistics();
    }

    public function loadBirthCertificateStats()
    {
        $this->birthCertificateStats = Employee::getBirthCertificateStatistics();
    }

    public function loadArmyServicePaperStats()
    {
        $this->armyServicePaperStats = Employee::getArmyServicePaperStatistics();
    }

    public function loadEmploymentContractStats()
    {
        $this->employmentContractStats = Employee::getEmploymentContractStatistics();
    }

    public function loadDriverLicenseStats()
    {
        $this->driverLicenseStats = Employee::getDriverLicenseStatistics();
    }

    public function loadPoliceRecordStats()
    {
        $this->policeRecordStats = Employee::getPoliceRecordStatistics();
    }

    public function loadHrLetterStats()
    {
        $this->hrLetterStats = Employee::getHrLetterStatistics();
    }

    public function loadS1DocStats()
    {
        $this->s1DocStats = Employee::getS1DocStatistics();
    }

    public function loadS2DocStats()
    {
        $this->s2DocStats = Employee::getS2DocStatistics();
    }

    public function loadS6DocStats()
    {
        $this->s6DocStats = Employee::getS6DocStatistics();
    }

    public function render()
    {
        return view('livewire.employee.employee-dashboard');
    }
}
