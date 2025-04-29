<div>
    <div class="container mx-auto px-4 py-6">
        <h2 class="text-2xl font-bold mb-6">Employee Dashboard</h2>

        <!-- ID Card Statistics Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4">ID Card Status</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Total Employees Card -->
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Total Employees</div>
                    <div class="text-2xl font-bold">{{ $idCardStats['total'] }}</div>
                </div>

                <!-- Valid ID Cards Card -->
                <div class="bg-success-100 border border-success-500 rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Valid ID Cards</div>
                    <div class="text-2xl font-bold text-success-600">{{ $idCardStats['valid'] }}</div>
                </div>

                <!-- Near Expiry Card -->
                <div class="bg-warning-50 border border-warning-500 rounded-lg shadow p-4">
                    <div class="text-warning-100 text-sm">Near Expiry</div>
                    <div class="text-2xl font-bold text-warning-600">{{ $idCardStats['near_expiry'] }}</div>
                </div>

                <!-- Expired Card -->
                <div class="bg-danger-100 border border-danger-500 rounded-lg shadow p-4">
                    <div class="text-danger-100 text-sm">Expired</div>
                    <div class="text-2xl font-bold text-danger-600">{{ $idCardStats['expired'] }}</div>
                </div>

                <!-- Missing Card -->
                <div class="bg-danger-100 border border-danger-500 rounded-lg shadow p-4">
                    <div class="text-danger-100 text-sm">Missing</div>
                    <div class="text-2xl font-bold text-danger-600">{{ $idCardStats['missing'] }}</div>
                </div>
            </div>
        </div>

        <!-- Birth Certificate Statistics Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4">Birth Certificate Status</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
                <!-- Total Employees Card -->
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Total Employees</div>
                    <div class="text-2xl font-bold">{{ $birthCertificateStats['total'] }}</div>
                </div>
                
                <!-- Valid Birth Certificates Card -->
                <div class="bg-success-100 border border-success-500 rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Valid Birth Certificates</div>
                    <div class="text-2xl font-bold text-success-600">{{ $birthCertificateStats['valid'] }}</div>
                </div>

                <!-- Near Expiry Card -->
                <div class="bg-warning-50 border border-warning-500 rounded-lg shadow p-4">
                    <div class="text-warning-100 text-sm">Near Expiry</div>
                    <div class="text-2xl font-bold text-warning-600">{{ $birthCertificateStats['near_expiry'] }}</div>
                </div>
                
                <!-- Expired Card -->
                <div class="bg-danger-100 border border-danger-500 rounded-lg shadow p-4">
                    <div class="text-danger-100 text-sm">Expired</div>
                    <div class="text-2xl font-bold text-danger-600">{{ $birthCertificateStats['expired'] }}</div>
                </div>

                <!-- Missing Card -->
                <div class="bg-danger-100 border border-danger-500 rounded-lg shadow p-4">
                    <div class="text-danger-100 text-sm">Missing</div>
                    <div class="text-2xl font-bold text-danger-600">{{ $birthCertificateStats['missing'] }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Original Card -->
                <div class="bg-blue-100 border border-info-500 rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Original</div>
                    <div class="text-2xl font-bold text-info-600">{{ $birthCertificateStats['by_type']['original'] }}</div>
                </div>
                
                <!-- Verified Copy Card -->
                <div class="bg-purple-100 border border-info-500 rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Verified Copy</div>
                    <div class="text-2xl font-bold text-info-600">{{ $birthCertificateStats['by_type']['verified_copy'] }}</div>
                </div>

                <!-- Copy Card -->
                <div class="bg-indigo-100 border border-info-500 rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Copy</div>
                    <div class="text-2xl font-bold text-info-600">{{ $birthCertificateStats['by_type']['copy'] }}</div>
                </div>
            </div>
        </div>

        <!-- Army Service Paper Statistics Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4">Army Service Paper Status</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
                <!-- Total Employees Card -->
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Total Employees</div>
                    <div class="text-2xl font-bold">{{ $armyServicePaperStats['total'] }}</div>
                </div>
                
                <!-- Female Employees Card (Not Required) -->
                <div class="bg-gray-100 border border-gray-500 rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Female Employees (Not Required)</div>
                    <div class="text-2xl font-bold text-gray-600">{{ $armyServicePaperStats['females'] }}</div>
                </div>
                
                <!-- Male Employees Card (Required) -->
                <div class="bg-primary-100 border border-primary-500 rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Male Employees (Required)</div>
                    <div class="text-2xl font-bold text-primary-600">{{ $armyServicePaperStats['males'] }}</div>
                </div>
                
                <!-- Valid Army Service Papers Card -->
                <div class="bg-success-100 border border-success-500 rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Valid Army Service Papers</div>
                    <div class="text-2xl font-bold text-success-600">{{ $armyServicePaperStats['valid'] }}</div>
                </div>
                
                <!-- Near Expiry Card -->
                <div class="bg-warning-50 border border-warning-500 rounded-lg shadow p-4">
                    <div class="text-warning-100 text-sm">Near Expiry</div>
                    <div class="text-2xl font-bold text-warning-600">{{ $armyServicePaperStats['near_expiry'] }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                <!-- Expired Card -->
                <div class="bg-danger-100 border border-danger-500 rounded-lg shadow p-4">
                    <div class="text-danger-100 text-sm">Expired</div>
                    <div class="text-2xl font-bold text-danger-600">{{ $armyServicePaperStats['expired'] }}</div>
                </div>
                
                <!-- Missing Card -->
                <div class="bg-danger-100 border border-danger-500 rounded-lg shadow p-4">
                    <div class="text-danger-100 text-sm">Missing (Male Only)</div>
                    <div class="text-2xl font-bold text-danger-600">{{ $armyServicePaperStats['missing'] }}</div>
                </div>

                <div class="bg-blue-100 border border-info-500 rounded-lg shadow p-4 grid grid-cols-3 divide-x divide-info-500">
                    <div class="pr-2">
                        <div class="text-gray-500 text-sm">Original</div>
                        <div class="text-2xl font-bold text-info-600">{{ $armyServicePaperStats['by_type']['original'] }}</div>
                    </div>
                    <div class="px-2 border-l border-info-500">
                        <div class="text-gray-500 text-sm">Verified Copy</div>
                        <div class="text-2xl font-bold text-info-600">{{ $armyServicePaperStats['by_type']['verified_copy'] }}</div>
                    </div>
                    <div class="pl-2 border-l border-info-500">
                        <div class="text-gray-500 text-sm">Copy</div>
                        <div class="text-2xl font-bold text-info-600">{{ $armyServicePaperStats['by_type']['copy'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employment Contract Statistics Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4">Employment Contract Status</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Total Employees Card -->
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Total Employees</div>
                    <div class="text-2xl font-bold">{{ $employmentContractStats['total'] }}</div>
                </div>

                <!-- Valid Contracts Card -->
                <div class="bg-success-100 border border-success-500 rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Valid Contracts</div>
                    <div class="text-2xl font-bold text-success-600">{{ $employmentContractStats['valid'] }}</div>
                </div>

                <!-- Near Expiry Card -->
                <div class="bg-warning-50 border border-warning-500 rounded-lg shadow p-4">
                    <div class="text-warning-100 text-sm">Near Expiry</div>
                    <div class="text-2xl font-bold text-warning-600">{{ $employmentContractStats['near_expiry'] }}</div>
                </div>

                <!-- Expired Card -->
                <div class="bg-danger-100 border border-danger-500 rounded-lg shadow p-4">
                    <div class="text-danger-100 text-sm">Expired</div>
                    <div class="text-2xl font-bold text-danger-600">{{ $employmentContractStats['expired'] }}</div>
                </div>

                <!-- Missing Card -->
                <div class="bg-danger-100 border border-danger-500 rounded-lg shadow p-4">
                    <div class="text-danger-100 text-sm">Missing</div>
                    <div class="text-2xl font-bold text-danger-600">{{ $employmentContractStats['missing'] }}</div>
                </div>
            </div>
        </div>

        <!-- Driver License Statistics Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4">Driver License Status</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
                <!-- Total Employees Card -->
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Total Employees</div>
                    <div class="text-2xl font-bold">{{ $driverLicenseStats['total'] }}</div>
                </div>
                
                <!-- Required Card -->
                <div class="bg-primary-100 border border-primary-500 rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Required</div>
                    <div class="text-2xl font-bold text-primary-600">{{ $driverLicenseStats['required'] }}</div>
                </div>
                
                <!-- Not Required Card -->
                <div class="bg-gray-100 border border-gray-500 rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Not Required</div>
                    <div class="text-2xl font-bold text-gray-600">{{ $driverLicenseStats['not_required'] }}</div>
                </div>
                
                <!-- Valid Driver Licenses Card -->
                <div class="bg-success-100 border border-success-500 rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Valid Driver Licenses</div>
                    <div class="text-2xl font-bold text-success-600">{{ $driverLicenseStats['valid'] }}</div>
                </div>
                
                <!-- Near Expiry Card -->
                <div class="bg-warning-50 border border-warning-500 rounded-lg shadow p-4">
                    <div class="text-warning-100 text-sm">Near Expiry</div>
                    <div class="text-2xl font-bold text-warning-600">{{ $driverLicenseStats['near_expiry'] }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Expired Card -->
                <div class="bg-danger-100 border border-danger-500 rounded-lg shadow p-4">
                    <div class="text-danger-100 text-sm">Expired</div>
                    <div class="text-2xl font-bold text-danger-600">{{ $driverLicenseStats['expired'] }}</div>
                </div>
                
                <!-- Missing Card -->
                <div class="bg-danger-100 border border-danger-500 rounded-lg shadow p-4">
                    <div class="text-danger-100 text-sm">Missing (Required Only)</div>
                    <div class="text-2xl font-bold text-danger-600">{{ $driverLicenseStats['missing'] }}</div>
                </div>
            </div>
        </div>

        <!-- Police Record Statistics Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4">Police Record Status</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Total Employees Card -->
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Total Employees</div>
                    <div class="text-2xl font-bold">{{ $policeRecordStats['total'] }}</div>
                </div>

                <!-- Valid Police Records Card -->
                <div class="bg-success-100 border border-success-500 rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Valid Police Records</div>
                    <div class="text-2xl font-bold text-success-600">{{ $policeRecordStats['valid'] }}</div>
                </div>

                <!-- Near Expiry Card -->
                <div class="bg-warning-50 border border-warning-500 rounded-lg shadow p-4">
                    <div class="text-warning-100 text-sm">Near Expiry</div>
                    <div class="text-2xl font-bold text-warning-600">{{ $policeRecordStats['near_expiry'] }}</div>
                </div>

                <!-- Expired Card -->
                <div class="bg-danger-100 border border-danger-500 rounded-lg shadow p-4">
                    <div class="text-danger-100 text-sm">Expired</div>
                    <div class="text-2xl font-bold text-danger-600">{{ $policeRecordStats['expired'] }}</div>
                </div>

                <!-- Missing Card -->
                <div class="bg-danger-100 border border-danger-500 rounded-lg shadow p-4">
                    <div class="text-danger-100 text-sm">Missing</div>
                    <div class="text-2xl font-bold text-danger-600">{{ $policeRecordStats['missing'] }}</div>
                </div>
            </div>
        </div>

        <!-- HR Letter Statistics Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4">HR Letter Status</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Total Employees Card -->
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Total Employees</div>
                    <div class="text-2xl font-bold">{{ $hrLetterStats['total'] }}</div>
                </div>

                <!-- Valid HR Letters Card -->
                <div class="bg-success-100 border border-success-500 rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Valid HR Letters</div>
                    <div class="text-2xl font-bold text-success-600">{{ $hrLetterStats['valid'] }}</div>
                </div>

                <!-- Near Expiry Card -->
                <div class="bg-warning-50 border border-warning-500 rounded-lg shadow p-4">
                    <div class="text-warning-100 text-sm">Near Expiry</div>
                    <div class="text-2xl font-bold text-warning-600">{{ $hrLetterStats['near_expiry'] }}</div>
                </div>

                <!-- Expired Card -->
                <div class="bg-danger-100 border border-danger-500 rounded-lg shadow p-4">
                    <div class="text-danger-100 text-sm">Expired</div>
                    <div class="text-2xl font-bold text-danger-600">{{ $hrLetterStats['expired'] }}</div>
                </div>

                <!-- Missing Card -->
                <div class="bg-danger-100 border border-danger-500 rounded-lg shadow p-4">
                    <div class="text-danger-100 text-sm">Missing</div>
                    <div class="text-2xl font-bold text-danger-600">{{ $hrLetterStats['missing'] }}</div>
                </div>
            </div>
        </div>

        <!-- S1 Document Statistics Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4">S1 Document Status</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Total Employees Card -->
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Total Employees</div>
                    <div class="text-2xl font-bold">{{ $s1DocStats['total'] }}</div>
                </div>

                <!-- Valid S1 Documents Card -->
                <div class="bg-success-100 border border-success-500 rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Valid S1 Documents</div>
                    <div class="text-2xl font-bold text-success-600">{{ $s1DocStats['valid'] }}</div>
                </div>

                <!-- Near Expiry Card -->
                <div class="bg-warning-50 border border-warning-500 rounded-lg shadow p-4">
                    <div class="text-warning-100 text-sm">Near Expiry</div>
                    <div class="text-2xl font-bold text-warning-600">{{ $s1DocStats['near_expiry'] }}</div>
                </div>

                <!-- Expired Card -->
                <div class="bg-danger-100 border border-danger-500 rounded-lg shadow p-4">
                    <div class="text-danger-100 text-sm">Expired</div>
                    <div class="text-2xl font-bold text-danger-600">{{ $s1DocStats['expired'] }}</div>
                </div>

                <!-- Missing Card -->
                <div class="bg-danger-100 border border-danger-500 rounded-lg shadow p-4">
                    <div class="text-danger-100 text-sm">Missing</div>
                    <div class="text-2xl font-bold text-danger-600">{{ $s1DocStats['missing'] }}</div>
                </div>
            </div>
        </div>

        <!-- S2 Document Statistics Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4">S2 Document Status</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Total Employees Card -->
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Total Employees</div>
                    <div class="text-2xl font-bold">{{ $s2DocStats['total'] }}</div>
                </div>

                <!-- Valid S2 Documents Card -->
                <div class="bg-success-100 border border-success-500 rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Valid S2 Documents</div>
                    <div class="text-2xl font-bold text-success-600">{{ $s2DocStats['valid'] }}</div>
                </div>

                <!-- Near Expiry Card -->
                <div class="bg-warning-50 border border-warning-500 rounded-lg shadow p-4">
                    <div class="text-warning-100 text-sm">Near Expiry</div>
                    <div class="text-2xl font-bold text-warning-600">{{ $s2DocStats['near_expiry'] }}</div>
                </div>

                <!-- Expired Card -->
                <div class="bg-danger-100 border border-danger-500 rounded-lg shadow p-4">
                    <div class="text-danger-100 text-sm">Expired</div>
                    <div class="text-2xl font-bold text-danger-600">{{ $s2DocStats['expired'] }}</div>
                </div>

                <!-- Missing Card -->
                <div class="bg-danger-100 border border-danger-500 rounded-lg shadow p-4">
                    <div class="text-danger-100 text-sm">Missing</div>
                    <div class="text-2xl font-bold text-danger-600">{{ $s2DocStats['missing'] }}</div>
                </div>
            </div>
        </div>

        <!-- S6 Document Statistics Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4">S6 Document Status</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Total Employees Card -->
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Total Employees</div>
                    <div class="text-2xl font-bold">{{ $s6DocStats['total'] }}</div>
                </div>

                <!-- Valid S6 Documents Card -->
                <div class="bg-success-100 border border-success-500 rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Valid S6 Documents</div>
                    <div class="text-2xl font-bold text-success-600">{{ $s6DocStats['valid'] }}</div>
                </div>

                <!-- Near Expiry Card -->
                <div class="bg-warning-50 border border-warning-500 rounded-lg shadow p-4">
                    <div class="text-warning-100 text-sm">Near Expiry</div>
                    <div class="text-2xl font-bold text-warning-600">{{ $s6DocStats['near_expiry'] }}</div>
                </div>

                <!-- Expired Card -->
                <div class="bg-danger-100 border border-danger-500 rounded-lg shadow p-4">
                    <div class="text-danger-100 text-sm">Expired</div>
                    <div class="text-2xl font-bold text-danger-600">{{ $s6DocStats['expired'] }}</div>
                </div>

                <!-- Missing Card -->
                <div class="bg-danger-100 border border-danger-500 rounded-lg shadow p-4">
                    <div class="text-danger-100 text-sm">Missing</div>
                    <div class="text-2xl font-bold text-danger-600">{{ $s6DocStats['missing'] }}</div>
                </div>
            </div>
        </div>

        <!-- Medical Record Statistics Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4">Medical Record Status</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
                <!-- Total Employees Card -->
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Total Employees</div>
                    <div class="text-2xl font-bold">{{ $medicalRecordStats['total'] }}</div>
                </div>

                <!-- Valid Medical Records Card -->
                <div class="bg-success-100 border border-success-500 rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Valid Medical Records</div>
                    <div class="text-2xl font-bold text-success-600">{{ $medicalRecordStats['valid'] }}</div>
                </div>

                <!-- Near Expiry Card -->
                <div class="bg-warning-50 border border-warning-500 rounded-lg shadow p-4">
                    <div class="text-warning-100 text-sm">Near Expiry</div>
                    <div class="text-2xl font-bold text-warning-600">{{ $medicalRecordStats['near_expiry'] }}</div>
                </div>

                <!-- Expired Card -->
                <div class="bg-danger-100 border border-danger-500 rounded-lg shadow p-4">
                    <div class="text-danger-100 text-sm">Expired</div>
                    <div class="text-2xl font-bold text-danger-600">{{ $medicalRecordStats['expired'] }}</div>
                </div>

                <!-- Missing Card -->
                <div class="bg-danger-100 border border-danger-500 rounded-lg shadow p-4">
                    <div class="text-danger-100 text-sm">Missing</div>
                    <div class="text-2xl font-bold text-danger-600">{{ $medicalRecordStats['missing'] }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Not Covered Card -->
                <div class="bg-gray-100 border border-gray-500 rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Not Covered</div>
                    <div class="text-2xl font-bold text-gray-600">{{ $medicalRecordStats['by_status']['Not Covered'] }}</div>
                </div>

                <!-- Examination Card -->
                <div class="bg-blue-100 border border-blue-500 rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Examination</div>
                    <div class="text-2xl font-bold text-blue-600">{{ $medicalRecordStats['by_status']['Examination'] }}</div>
                </div>

                <!-- Issuing Card -->
                <div class="bg-yellow-100 border border-yellow-500 rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Issuing</div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $medicalRecordStats['by_status']['Issuing'] }}</div>
                </div>

                <!-- Covered Card -->
                <div class="bg-green-100 border border-green-500 rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Covered</div>
                    <div class="text-2xl font-bold text-green-600">{{ $medicalRecordStats['by_status']['Covered'] }}</div>
                </div>

                <!-- External Cover Card -->
                <div class="bg-purple-100 border border-purple-500 rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">External Cover</div>
                    <div class="text-2xl font-bold text-purple-600">{{ $medicalRecordStats['by_status']['External Cover'] }}</div>
                </div>
            </div>
        </div>

        <!-- External Medical Record Statistics Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4">External Medical Record Status</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Total Employees Card -->
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Total Employees</div>
                    <div class="text-2xl font-bold">{{ $externalMedicalRecordStats['total'] }}</div>
                </div>

                <!-- Valid External Medical Records Card -->
                <div class="bg-success-100 border border-success-500 rounded-lg shadow p-4">
                    <div class="text-gray-500 text-sm">Valid Records</div>
                    <div class="text-2xl font-bold text-success-600">{{ $externalMedicalRecordStats['valid'] }}</div>
                </div>

                <!-- Near Expiry Card -->
                <div class="bg-warning-50 border border-warning-500 rounded-lg shadow p-4">
                    <div class="text-warning-100 text-sm">Near Expiry</div>
                    <div class="text-2xl font-bold text-warning-600">{{ $externalMedicalRecordStats['near_expiry'] }}</div>
                </div>

                <!-- Expired Card -->
                <div class="bg-danger-100 border border-danger-500 rounded-lg shadow p-4">
                    <div class="text-danger-100 text-sm">Expired</div>
                    <div class="text-2xl font-bold text-danger-600">{{ $externalMedicalRecordStats['expired'] }}</div>
                </div>

                <!-- Missing Card -->
                <div class="bg-danger-100 border border-danger-500 rounded-lg shadow p-4">
                    <div class="text-danger-100 text-sm">Missing</div>
                    <div class="text-2xl font-bold text-danger-600">{{ $externalMedicalRecordStats['missing'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
