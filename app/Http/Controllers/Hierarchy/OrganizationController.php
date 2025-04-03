<?php

namespace App\Http\Controllers\Hierarchy;

use App\Http\Controllers\Controller;
use App\Models\Hierarchy\OrganizationalChart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrganizationController extends Controller
{
    /**
     * Display the organization chart
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get the hierarchical data using the OrganizationalChart model
        $hierarchyData = OrganizationalChart::getHierarchy();
        
        return view('static.organization.tree', [
            'title' => 'Organization Chart',
            'organizationIndex' => 'active',
            'hierarchyData' => $hierarchyData
        ]);
    }
}
