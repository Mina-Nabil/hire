<div>
    <div class="container mx-auto px-4 py-6">
        <h2 class="text-2xl font-bold mb-6">Employee Dashboard</h2>

        <!-- Document Status Table -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4">Document Status Overview</h3>
            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
                    <thead class="bg-slate-900 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-white">Document Type</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-white">Total</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-white">Valid</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-white">Near Expiry</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-white">Expired</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-white">Missing</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-white">Additional Info</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <!-- ID Card -->
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">ID Card</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-900">{{ $idCardStats['total'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-success-600 font-semibold">{{ $idCardStats['valid'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-warning-600 font-semibold">
                                @if($idCardStats['near_expiry'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') }}?nearExpiry[ID%20Card]=1" class="hover:underline">
                                        {{ $idCardStats['near_expiry'] }}
                                    </a>
                                @else
                                    {{ $idCardStats['near_expiry'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($idCardStats['expired'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') }}?expired[ID%20Card]=1" class="hover:underline">
                                        {{ $idCardStats['expired'] }}
                                    </a>
                                @else
                                    {{ $idCardStats['expired'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($idCardStats['missing'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') }}?missing[ID%20Card]=1" class="hover:underline">
                                        {{ $idCardStats['missing'] }}
                                    </a>
                                @else
                                    {{ $idCardStats['missing'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center">-</td>
                        </tr>

                        <!-- Birth Certificate -->
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">Birth Certificate</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-900">{{ $birthCertificateStats['total'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-success-600 font-semibold">{{ $birthCertificateStats['valid'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-warning-600 font-semibold">
                                @if($birthCertificateStats['near_expiry'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?nearExpiry[Birth%20Certificate]=1' }}" class="hover:underline">
                                        {{ $birthCertificateStats['near_expiry'] }}
                                    </a>
                                @else
                                    {{ $birthCertificateStats['near_expiry'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($birthCertificateStats['expired'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?expired[Birth%20Certificate]=1' }}" class="hover:underline">
                                        {{ $birthCertificateStats['expired'] }}
                                    </a>
                                @else
                                    {{ $birthCertificateStats['expired'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($birthCertificateStats['missing'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?missing[Birth%20Certificate]=1' }}" class="hover:underline">
                                        {{ $birthCertificateStats['missing'] }}
                                    </a>
                                @else
                                    {{ $birthCertificateStats['missing'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                Original: {{ $birthCertificateStats['by_type']['original'] }} | 
                                Verified: {{ $birthCertificateStats['by_type']['verified_copy'] }} | 
                                Copy: {{ $birthCertificateStats['by_type']['copy'] }}
                            </td>
                        </tr>

                        <!-- Army Service Paper -->
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">Army Service Paper</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-900">{{ $armyServicePaperStats['total'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-success-600 font-semibold">{{ $armyServicePaperStats['valid'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-warning-600 font-semibold">
                                @if($armyServicePaperStats['near_expiry'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?nearExpiry[Army%20Service%20Paper]=1' }}" class="hover:underline">
                                        {{ $armyServicePaperStats['near_expiry'] }}
                                    </a>
                                @else
                                    {{ $armyServicePaperStats['near_expiry'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($armyServicePaperStats['expired'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?expired[Army%20Service%20Paper]=1' }}" class="hover:underline">
                                        {{ $armyServicePaperStats['expired'] }}
                                    </a>
                                @else
                                    {{ $armyServicePaperStats['expired'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($armyServicePaperStats['missing'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?missing[Army%20Service%20Paper]=1' }}" class="hover:underline">
                                        {{ $armyServicePaperStats['missing'] }}
                                    </a>
                                @else
                                    {{ $armyServicePaperStats['missing'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                Males: {{ $armyServicePaperStats['males'] }} | 
                                Females: {{ $armyServicePaperStats['females'] }}
                            </td>
                        </tr>

                        <!-- Employment Contract -->
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">Employment Contract</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-900">{{ $employmentContractStats['total'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-success-600 font-semibold">{{ $employmentContractStats['valid'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-warning-600 font-semibold">
                                @if($employmentContractStats['near_expiry'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?nearExpiry[Employment%20Contract]=1' }}" class="hover:underline">
                                        {{ $employmentContractStats['near_expiry'] }}
                                    </a>
                                @else
                                    {{ $employmentContractStats['near_expiry'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($employmentContractStats['expired'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?expired[Employment%20Contract]=1' }}" class="hover:underline">
                                        {{ $employmentContractStats['expired'] }}
                                    </a>
                                @else
                                    {{ $employmentContractStats['expired'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($employmentContractStats['missing'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?missing[Employment%20Contract]=1' }}" class="hover:underline">
                                        {{ $employmentContractStats['missing'] }}
                                    </a>
                                @else
                                    {{ $employmentContractStats['missing'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center">-</td>
                        </tr>

                        <!-- Driver License -->
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">Driver License</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-900">{{ $driverLicenseStats['total'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-success-600 font-semibold">{{ $driverLicenseStats['valid'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-warning-600 font-semibold">
                                @if($driverLicenseStats['near_expiry'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') }}?nearExpiry[Driver%20License]=1" class="hover:underline">
                                        {{ $driverLicenseStats['near_expiry'] }}
                                    </a>
                                @else
                                    {{ $driverLicenseStats['near_expiry'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($driverLicenseStats['expired'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?expired[Driver%20License]=1' }}" class="hover:underline">
                                        {{ $driverLicenseStats['expired'] }}
                                    </a>
                                @else
                                    {{ $driverLicenseStats['expired'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($driverLicenseStats['missing'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?missing[Driver%20License]=1' }}" class="hover:underline">
                                        {{ $driverLicenseStats['missing'] }}
                                    </a>
                                @else
                                    {{ $driverLicenseStats['missing'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                Required: {{ $driverLicenseStats['required'] }} | 
                                Not Required: {{ $driverLicenseStats['not_required'] }}
                            </td>
                        </tr>

                        <!-- Police Record -->
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">Police Record</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-900">{{ $policeRecordStats['total'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-success-600 font-semibold">{{ $policeRecordStats['valid'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-warning-600 font-semibold">
                                @if($policeRecordStats['near_expiry'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?nearExpiry[Police%20Record]=1' }}" class="hover:underline">
                                        {{ $policeRecordStats['near_expiry'] }}
                                    </a>
                                @else
                                    {{ $policeRecordStats['near_expiry'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($policeRecordStats['expired'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?expired[Police%20Record]=1' }}" class="hover:underline">
                                        {{ $policeRecordStats['expired'] }}
                                    </a>
                                @else
                                    {{ $policeRecordStats['expired'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($policeRecordStats['missing'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?missing[Police%20Record]=1' }}" class="hover:underline">
                                        {{ $policeRecordStats['missing'] }}
                                    </a>
                                @else
                                    {{ $policeRecordStats['missing'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center">-</td>
                        </tr>

                        <!-- HR Letter -->
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">HR Letter</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-900">{{ $hrLetterStats['total'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-success-600 font-semibold">{{ $hrLetterStats['valid'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-warning-600 font-semibold">
                                @if($hrLetterStats['near_expiry'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?nearExpiry[HR%20Letter]=1' }}" class="hover:underline">
                                        {{ $hrLetterStats['near_expiry'] }}
                                    </a>
                                @else
                                    {{ $hrLetterStats['near_expiry'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($hrLetterStats['expired'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?expired[HR%20Letter]=1' }}" class="hover:underline">
                                        {{ $hrLetterStats['expired'] }}
                                    </a>
                                @else
                                    {{ $hrLetterStats['expired'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($hrLetterStats['missing'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?missing[HR%20Letter]=1' }}" class="hover:underline">
                                        {{ $hrLetterStats['missing'] }}
                                    </a>
                                @else
                                    {{ $hrLetterStats['missing'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center">-</td>
                        </tr>

                        <!-- S1 Document -->
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">S1 Document</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-900">{{ $s1DocStats['total'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-success-600 font-semibold">{{ $s1DocStats['valid'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-warning-600 font-semibold">
                                @if($s1DocStats['near_expiry'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?nearExpiry[S1%20Document]=1' }}" class="hover:underline">
                                        {{ $s1DocStats['near_expiry'] }}
                                    </a>
                                @else
                                    {{ $s1DocStats['near_expiry'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($s1DocStats['expired'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?expired[S1%20Document]=1' }}" class="hover:underline">
                                        {{ $s1DocStats['expired'] }}
                                    </a>
                                @else
                                    {{ $s1DocStats['expired'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($s1DocStats['missing'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?missing[S1%20Document]=1' }}" class="hover:underline">
                                        {{ $s1DocStats['missing'] }}
                                    </a>
                                @else
                                    {{ $s1DocStats['missing'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center">-</td>
                        </tr>

                        <!-- S2 Document -->
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">S2 Document</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-900">{{ $s2DocStats['total'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-success-600 font-semibold">{{ $s2DocStats['valid'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-warning-600 font-semibold">
                                @if($s2DocStats['near_expiry'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?nearExpiry[S2%20Document]=1' }}" class="hover:underline">
                                        {{ $s2DocStats['near_expiry'] }}
                                    </a>
                                @else
                                    {{ $s2DocStats['near_expiry'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($s2DocStats['expired'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?expired[S2%20Document]=1' }}" class="hover:underline">
                                        {{ $s2DocStats['expired'] }}
                                    </a>
                                @else
                                    {{ $s2DocStats['expired'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($s2DocStats['missing'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?missing[S2%20Document]=1' }}" class="hover:underline">
                                        {{ $s2DocStats['missing'] }}
                                    </a>
                                @else
                                    {{ $s2DocStats['missing'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center">-</td>
                        </tr>

                        <!-- S6 Document -->
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">S6 Document</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-900">{{ $s6DocStats['total'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-success-600 font-semibold">{{ $s6DocStats['valid'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-warning-600 font-semibold">
                                @if($s6DocStats['near_expiry'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?nearExpiry[S6%20Document]=1' }}" class="hover:underline">
                                        {{ $s6DocStats['near_expiry'] }}
                                    </a>
                                @else
                                    {{ $s6DocStats['near_expiry'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($s6DocStats['expired'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?expired[S6%20Document]=1' }}" class="hover:underline">
                                        {{ $s6DocStats['expired'] }}
                                    </a>
                                @else
                                    {{ $s6DocStats['expired'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($s6DocStats['missing'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?missing[S6%20Document]=1' }}" class="hover:underline">
                                        {{ $s6DocStats['missing'] }}
                                    </a>
                                @else
                                    {{ $s6DocStats['missing'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center">-</td>
                        </tr>

                        <!-- Medical Record -->
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">Medical Record</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-900">{{ $medicalRecordStats['total'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-success-600 font-semibold">{{ $medicalRecordStats['valid'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-warning-600 font-semibold">
                                @if($medicalRecordStats['near_expiry'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?nearExpiry[Medical%20Record]=1' }}" class="hover:underline">
                                        {{ $medicalRecordStats['near_expiry'] }}
                                    </a>
                                @else
                                    {{ $medicalRecordStats['near_expiry'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($medicalRecordStats['expired'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?expired[Medical%20Record]=1' }}" class="hover:underline">
                                        {{ $medicalRecordStats['expired'] }}
                                    </a>
                                @else
                                    {{ $medicalRecordStats['expired'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($medicalRecordStats['missing'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?missing[Medical%20Record]=1' }}" class="hover:underline">
                                        {{ $medicalRecordStats['missing'] }}
                                    </a>
                                @else
                                    {{ $medicalRecordStats['missing'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                Not Covered: {{ $medicalRecordStats['by_status']['Not Covered'] }} | 
                                Examination: {{ $medicalRecordStats['by_status']['Examination'] }} | 
                                Issuing: {{ $medicalRecordStats['by_status']['Issuing'] }} | 
                                Covered: {{ $medicalRecordStats['by_status']['Covered'] }} | 
                                External: {{ $medicalRecordStats['by_status']['External Cover'] }}
                            </td>
                        </tr>

                        <!-- External Medical Record -->
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">External Medical Record</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-900">{{ $externalMedicalRecordStats['total'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-success-600 font-semibold">{{ $externalMedicalRecordStats['valid'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-warning-600 font-semibold">
                                @if($externalMedicalRecordStats['near_expiry'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?nearExpiry[External%20Medical%20Record]=1' }}" class="hover:underline">
                                        {{ $externalMedicalRecordStats['near_expiry'] }}
                                    </a>
                                @else
                                    {{ $externalMedicalRecordStats['near_expiry'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($externalMedicalRecordStats['expired'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?expired[External%20Medical%20Record]=1' }}" class="hover:underline">
                                        {{ $externalMedicalRecordStats['expired'] }}
                                    </a>
                                @else
                                    {{ $externalMedicalRecordStats['expired'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($externalMedicalRecordStats['missing'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?missing[External%20Medical%20Record]=1' }}" class="hover:underline">
                                        {{ $externalMedicalRecordStats['missing'] }}
                                    </a>
                                @else
                                    {{ $externalMedicalRecordStats['missing'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center">-</td>
                        </tr>

                        <!-- Practice Card -->
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">Practice Card</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-900">{{ $practiceCardStats['total'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-success-600 font-semibold">{{ $practiceCardStats['valid'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-warning-600 font-semibold">
                                @if($practiceCardStats['near_expiry'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?nearExpiry[Practice%20Card]=1' }}" class="hover:underline">
                                        {{ $practiceCardStats['near_expiry'] }}
                                    </a>
                                @else
                                    {{ $practiceCardStats['near_expiry'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($practiceCardStats['expired'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?expired[Practice%20Card]=1' }}" class="hover:underline">
                                        {{ $practiceCardStats['expired'] }}
                                    </a>
                                @else
                                    {{ $practiceCardStats['expired'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($practiceCardStats['missing'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?missing[Practice%20Card]=1' }}" class="hover:underline">
                                        {{ $practiceCardStats['missing'] }}
                                    </a>
                                @else
                                    {{ $practiceCardStats['missing'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center">-</td>
                        </tr>

                        <!-- Skills Qualification -->
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">Skills Qualification</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-900">{{ $skillsQualificationStats['total'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-success-600 font-semibold">{{ $skillsQualificationStats['valid'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-warning-600 font-semibold">
                                @if($skillsQualificationStats['near_expiry'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?nearExpiry[Skills%20Qualification]=1' }}" class="hover:underline">
                                        {{ $skillsQualificationStats['near_expiry'] }}
                                    </a>
                                @else
                                    {{ $skillsQualificationStats['near_expiry'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($skillsQualificationStats['expired'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?expired[Skills%20Qualification]=1' }}" class="hover:underline">
                                        {{ $skillsQualificationStats['expired'] }}
                                    </a>
                                @else
                                    {{ $skillsQualificationStats['expired'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($skillsQualificationStats['missing'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?missing[Skills%20Qualification]=1' }}" class="hover:underline">
                                        {{ $skillsQualificationStats['missing'] }}
                                    </a>
                                @else
                                    {{ $skillsQualificationStats['missing'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center">-</td>
                        </tr>

                        <!-- Syndicate Card -->
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">Syndicate Card</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-900">{{ $syndicateCardStats['total'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-success-600 font-semibold">{{ $syndicateCardStats['valid'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-warning-600 font-semibold">
                                @if($syndicateCardStats['near_expiry'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?nearExpiry[Syndicate%20Card]=1' }}" class="hover:underline">
                                        {{ $syndicateCardStats['near_expiry'] }}
                                    </a>
                                @else
                                    {{ $syndicateCardStats['near_expiry'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($syndicateCardStats['expired'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?expired[Syndicate%20Card]=1' }}" class="hover:underline">
                                        {{ $syndicateCardStats['expired'] }}
                                    </a>
                                @else
                                    {{ $syndicateCardStats['expired'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($syndicateCardStats['missing'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?missing[Syndicate%20Card]=1' }}" class="hover:underline">
                                        {{ $syndicateCardStats['missing'] }}
                                    </a>
                                @else
                                    {{ $syndicateCardStats['missing'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center">-</td>
                        </tr>

                        <!-- Work Declaration -->
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">Work Declaration</td>
                            <td class="px-4 py-3 text-sm text-center text-gray-900">{{ $workDeclarationStats['total'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-success-600 font-semibold">{{ $workDeclarationStats['valid'] }}</td>
                            <td class="px-4 py-3 text-sm text-center text-warning-600 font-semibold">
                                @if($workDeclarationStats['near_expiry'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?nearExpiry[Work%20Declaration]=1' }}" class="hover:underline">
                                        {{ $workDeclarationStats['near_expiry'] }}
                                    </a>
                                @else
                                    {{ $workDeclarationStats['near_expiry'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($workDeclarationStats['expired'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?expired[Work%20Declaration]=1' }}" class="hover:underline">
                                        {{ $workDeclarationStats['expired'] }}
                                    </a>
                                @else
                                    {{ $workDeclarationStats['expired'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center text-danger-600 font-semibold">
                                @if($workDeclarationStats['missing'] > 0)
                                    <a href="{{ route('employees.reports.missing-documents') . '?missing[Work%20Declaration]=1' }}" class="hover:underline">
                                        {{ $workDeclarationStats['missing'] }}
                                    </a>
                                @else
                                    {{ $workDeclarationStats['missing'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-center">-</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4 flex justify-end">
                <div class="text-sm text-gray-600 flex items-center space-x-4">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-success-600 rounded-full mr-1"></div>
                        <span>Valid</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-warning-600 rounded-full mr-1"></div>
                        <span>Near Expiry</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-danger-600 rounded-full mr-1"></div>
                        <span>Expired/Missing</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
