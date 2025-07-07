<?php

namespace App\Livewire\Employee;

use App\Models\Base\DocManager;
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
    public $medicalRecordStats;
    public $externalMedicalRecordStats;
    public $practiceCardStats;
    public $skillsQualificationStats;
    public $syndicateCardStats;
    public $workDeclarationStats;

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
        $this->loadMedicalRecordStats();
        $this->loadExternalMedicalRecordStats();
        $this->loadPracticeCardStats();
        $this->loadSkillsQualificationStats();
        $this->loadSyndicateCardStats();
        $this->loadWorkDeclarationStats();
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

    public function loadMedicalRecordStats()
    {
        $this->medicalRecordStats = Employee::getMedicalRecordStatistics();
    }

    public function loadExternalMedicalRecordStats()
    {
        $this->externalMedicalRecordStats = Employee::getExternalMedicalRecordStatistics();
    }

    public function loadPracticeCardStats()
    {
        $this->practiceCardStats = Employee::getPracticeCardStatistics();
    }

    public function loadSkillsQualificationStats()
    {
        $this->skillsQualificationStats = Employee::getSkillsQualificationStatistics();
    }

    public function loadSyndicateCardStats()
    {
        $this->syndicateCardStats = Employee::getSyndicateCardStatistics();
    }

    public function loadWorkDeclarationStats()
    {
        $this->workDeclarationStats = Employee::getWorkDeclarationStatistics();
    }

    /**
     * Get document name from DocManager
     * 
     * @param string $docType
     * @param string $fallback
     * @return string
     */
    public function getDocumentName($docType, $fallback = null)
    {
        $docManager = DocManager::where('doc_type', $docType)->first();
        return $docManager ? $docManager->name : ($fallback ?: $docType);
    }

    public function render()
    {
        $totalLeft = Employee::left()->count();
        return view('livewire.employee.employee-dashboard', [
            'totalLeft' => $totalLeft
        ]);
    }
}
