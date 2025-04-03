@extends('components.layouts.app')


@section('child_styles')
    <link href="{{ asset('css/org-chart.css') }}" rel="stylesheet">
    <style>
        .orgchart {
            background: rgba(248, 250, 252, 0.8) !important;
        }

        .orgchart .node {
            width: auto;
            min-width: 150px;
            max-width: 200px;
            padding: 2px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
            transition: all 0.3s cubic-bezier(.25, .8, .25, 1);
        }

        .orgchart .node:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15), 0 2px 4px rgba(0, 0, 0, 0.12);
        }

        .orgchart .node .title {
            width: 100%;
            padding: 4px 2px;
            font-size: 0.9rem;
            background-color: #334155;
            color: white;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .orgchart .node .content {
            width: 100%;
            padding: 3px;
            border: 1px solid #cbd5e1;
            text-align: center;
            font-size: 0.8rem;
        }

        .orgchart .node .employee-name {
            color: #1e40af;
            font-weight: 500;
            font-size: 0.75rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .orgchart .node .department-name {
            font-size: 0.7rem;
            font-style: italic;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .orgchart .lines .downLine {
            background-color: #334155;
        }

        .orgchart .lines .rightLine {
            border-top-color: #334155;
        }

        .orgchart .lines .leftLine {
            border-top-color: #334155;
        }

        .orgchart .node.vacant .title {
            background-color: #94a3b8;
        }

        .orgchart .node.collapsed {
            box-shadow: 0 0 0 2px #0ea5e9; 
        }

        .orgchart .node.collapsed .title {
            background-color: #0ea5e9;
        }

        .orgchart-container {
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
        }

        /* Hierarchy Level Styling */
        .orgchart .hierarchy-level-0 .title {
            background-color: #1e293b; /* CEO level */
        }

        .orgchart .hierarchy-level-1 .title {
            background-color: #334155; /* Executive level */
        }

        .orgchart .hierarchy-level-2 .title {
            background-color: #475569; /* Middle Management level */
        }

        .orgchart .hierarchy-level-3 .title {
            background-color: #64748b; /* Staff level */
        }

        .vacancy-indicator {
            color: #dc2626;
            font-size: 0.7rem;
            font-style: italic;
        }
    </style>
@endsection

@section('content')
    <div>
        <div class="flex justify-between flex-wrap items-center mb-6">
            <div class="md:mb-6 mb-4">
                <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block">
                    Organization Chart
                </h4>
            </div>
            <div class="flex flex-wrap md:mb-6 mb-4 gap-3">
                <button id="btn-toggle-employees" class="btn inline-flex justify-center btn-outline-dark">
                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="heroicons:user-group"></iconify-icon>
                    Toggle Employee Names
                </button>
            </div>
        </div>

        <!-- Loading indicator -->
        <div id="loading-indicator" class="p-6 text-center">
            <div class="animate-spin inline-block w-8 h-8 border-4 border-current border-t-transparent text-blue-600 rounded-full" role="status" aria-label="loading">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-2 text-slate-600">Loading organization chart... This may take a few moments.</p>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div id="chart-container" class="w-full h-[800px] overflow-y-auto overflow-x-auto hidden"></div>
            </div>
        </div>

        <!-- Chart Node Template (Hidden) -->
        <template id="node-template">
            <div class="node-container">
                <div class="node-details bg-white p-3 rounded-md border shadow-sm hover:shadow-md transition-all">
                    <div class="title-section border-b pb-2 mb-2">
                        <div class="position-name font-medium text-lg text-slate-900"></div>
                        <div class="department-name text-sm text-slate-500"></div>
                    </div>
                    <div class="employee-section">
                        <div class="employee-name text-sm"></div>
                    </div>
                </div>
            </div>
        </template>



    </div>
@endsection

@section('child_scripts')
    <!-- Include OrgChart Library -->
    <script src="{{ asset('js/org-chart.js') }}"></script>
    <script src="{{ asset('js/html2canvas.min.js') }}"></script>

    <script>
        $(function() {
            // Get hierarchical data from PHP
            const chartData = @json($hierarchyData);
            const $chartContainer = $('#chart-container');
            const $loadingIndicator = $('#loading-indicator');
            
            // Process the data to OrgChart format and add level info
            const processNode = (node, level = 0) => {
                return {
                    'id': node.id,
                    'name': node.name,
                    'title': node.department,
                    'employee': node.employee,
                    'className': `hierarchy-level-${level} ${node.employee ? 'filled' : 'vacant'}`,
                    'level': level,
                    'children': node.children ? node.children.map(child => processNode(child, level + 1)) : []
                };
            };
            
            const orgChartData = chartData.map(node => processNode(node));
            
            // Show loading message
            $loadingIndicator.show();
            $chartContainer.hide();
            
            // Function to collapse nodes after a certain level
            function collapseNodesAfterLevel(level) {
                console.log("Collapsing nodes after level", level);
                const $chart = $chartContainer.find('.orgchart');
                
                // Find all nodes at the specified level
                $chart.find(`.node[data-level="${level}"]`).each(function() {
                    // For each node at level 3, collapse it
                    const $node = $(this);
                    // Use the plugin's method to collapse nodes
                    $chartContainer.orgchart('collapse', $node);
                });
                
                console.log("Collapse operation completed");
            }
            
            // Initialize OrgChart with a small delay to allow loading indicator to render
            setTimeout(() => {
                const orgchart = $chartContainer.orgchart({
                    'data': orgChartData.length === 1 ? orgChartData[0] : {
                        'name': 'Organization',
                        'className': 'hierarchy-level-0',
                        'children': orgChartData
                    },
                    'nodeContent': 'title',
                    'nodeID': 'id',
                    'createNode': function($node, data) {
                        // Add hierarchy level class
                        if (data.level !== undefined) {
                            $node.addClass(`hierarchy-level-${data.level}`);
                            
                            // Add a data attribute to track the hierarchy level for collapsing
                            $node.attr('data-level', data.level);
                        }
                        
                        // Customize node appearance
                        const name = data.name;
                        // Truncate name if too long for display
                        const displayName = name.length > 20 ? name.substring(0, 18) + '...' : name;
                        $node.find('.title').text(displayName);
                        
                        // Add full name as a tooltip
                        $node.attr('title', name + (data.title ? ' (' + data.title + ')' : ''));
                        
                        const $content = $node.find('.content');
                        $content.html('');
                        
                        // Add department
                        const $dept = $('<div class="department-name"></div>');
                        const deptName = data.title || '';
                        // Truncate department name if too long
                        const displayDept = deptName.length > 25 ? deptName.substring(0, 23) + '...' : deptName;
                        $dept.text(displayDept);
                        $content.append($dept);
                        
                        // Add employee if present
                        if (data.employee) {
                            const $emp = $('<div class="employee-name"></div>');
                            $emp.text(data.employee);
                            $content.append($emp);
                        } else {
                            $node.addClass('vacant');
                            const $vacancy = $('<div class="vacancy-indicator"></div>');
                            $vacancy.text('Vacant');
                            $content.append($vacancy);
                        }
                    },
                    'direction': 't2b',
                    'toggleSiblingsResp': false,
                    'visibleLevel': 2,
                    'draggable': false,
                    'pan': false,
                    'zoom': false,
                });

                // Hide loading and show chart
                $loadingIndicator.hide();
                $chartContainer.show();
                
                // Get the actual chart element created by the plugin
                const $chart = $chartContainer.find('.orgchart');

                $('#btn-toggle-employees').on('click', function() {
                    $chart.find('.employee-name, .vacancy-indicator').toggle();
                    $(this).toggleClass('btn-primary').toggleClass('btn-outline-dark');
                });
            }, 500);
        });
    </script>
@endsection
