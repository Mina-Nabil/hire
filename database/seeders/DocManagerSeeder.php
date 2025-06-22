<?php

namespace Database\Seeders;

use App\Models\Base\DocManager;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentTypes = [
            [
                'doc_type' => 'armyServicePaper',
                'name' => 'Army Service Paper',
                'description' => 'Military service documentation or exemption certificate',
                'is_required' => true,
                'is_active' => true
            ],
            [
                'doc_type' => 'birthCertificate',
                'name' => 'Birth Certificate',
                'description' => 'Official birth certificate document',
                'is_required' => true,
                'is_active' => true
            ],
            [
                'doc_type' => 'idCard',
                'name' => 'ID Card',
                'description' => 'National identification card',
                'is_required' => true,
                'is_active' => true
            ],
            [
                'doc_type' => 'driverLicense',
                'name' => 'Driver License',
                'description' => 'Valid driver license if applicable',
                'is_required' => true,
                'is_active' => true
            ],
            [
                'doc_type' => 'employeeContract',
                'name' => 'Employee Contract',
                'description' => 'Employment contract and terms',
                'is_required' => true,
                'is_active' => true
            ],
            [
                'doc_type' => 'employeeS1Doc',
                'name' => 'Employee S1 Document',
                'description' => 'S1 form or equivalent employment document',
                'is_required' => true,
                'is_active' => true
            ],
            [
                'doc_type' => 'employeeS2Doc',
                'name' => 'Employee S2 Document',
                'description' => 'S2 form or equivalent employment document',
                'is_required' => true,
                'is_active' => true
            ],
            [
                'doc_type' => 'employeeS6Doc',
                'name' => 'Employee S6 Document',
                'description' => 'S6 form or termination document',
                'is_required' => true,
                'is_active' => true
            ],
            [
                'doc_type' => 'policeRecord',
                'name' => 'Police Record',
                'description' => 'Criminal background check or police clearance',
                'is_required' => true,
                'is_active' => true
            ],
            [
                'doc_type' => 'medicalRecord',
                'name' => 'Medical Record',
                'description' => 'Medical examination and health records',
                'is_required' => true,
                'is_active' => true
            ],
            [
                'doc_type' => 'externalMedicalRecord',
                'name' => 'External Medical Record',
                'description' => 'External medical examination records',
                'is_required' => true,
                'is_active' => true
            ],
            [
                'doc_type' => 'practiceCard',
                'name' => 'Practice Card',
                'description' => 'Professional practice license or card',
                'is_required' => true,
                'is_active' => true
            ],
            [
                'doc_type' => 'skillsQualification',
                'name' => 'Skills Qualification',
                'description' => 'Skills assessment or qualification certificates',
                'is_required' => true,
                'is_active' => true
            ],
            [
                'doc_type' => 'syndicateCard',
                'name' => 'Syndicate Card',
                'description' => 'Professional syndicate membership card',
                'is_required' => true,
                'is_active' => true
            ],
            [
                'doc_type' => 'workDeclaration',
                'name' => 'Work Declaration',
                'description' => 'Work declaration or employment statement',
                'is_required' => true,
                'is_active' => true
            ],
            [
                'doc_type' => 'labourDocument',
                'name' => 'Labour Document',
                'description' => 'Labour office related documents',
                'is_required' => true,
                'is_active' => true
            ],
            [
                'doc_type' => 'collegeCertificate',
                'name' => 'College Certificate',
                'description' => 'Educational certificates and qualifications',
                'is_required' => true,
                'is_active' => true
            ],
            [
                'doc_type' => 'socialPrint',
                'name' => 'Social Print',
                'description' => 'Social security or insurance documents',
                'is_required' => true,
                'is_active' => true
            ]
        ];

        foreach ($documentTypes as $docType) {
            DocManager::updateOrCreate(
                ['doc_type' => $docType['doc_type']],
                $docType
            );
        }
    }
}
