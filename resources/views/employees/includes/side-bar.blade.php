<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('admin.index.get') }}" class="app-brand-link">
            {{-- <img src="{{ asset('logos/opslines-txt.png') }}" alt="OPS" style="width: 200px;"> --}}
            <h3>HR</h3>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{ request()->routeIs('admin.index.get') ? 'active' : '' }}">
            <a href="{{ route('admin.index.get') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>Dashboard</div>
            </a>
        </li>

        <!-- ADMIN SETUP -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Admin Setup</span>
        </li>

        @php
            $companySetupRoutes = [
                'admin.company.edit',
                'admin.locations.index',
                'admin.departments.index',
                'admin.designations.index',
                'admin.grades.index',
                'admin.cost_centers.index',
            ];
        @endphp

        <li class="menu-item {{ in_array(request()->route()->getName(), $companySetupRoutes) ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-buildings"></i>
                <div>Company Setup</div>
            </a>

            <ul class="menu-sub">

                <li class="menu-item {{ request()->routeIs('admin.company.edit') ? 'active' : '' }}">
                    <a href="{{ route('admin.company.edit') }}" class="menu-link">
                        <div>Company Profile</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.locations.index') }}" class="menu-link">
                        <div>Branches / Locations</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.departments.index') }}" class="menu-link">
                        <div>Departments</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('admin.designations.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.designations.index') }}" class="menu-link">
                        <div>Designations</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('admin.grades.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.grades.index') }}" class="menu-link">
                        <div>Grades</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('admin.cost_centers.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.cost_centers.index') }}" class="menu-link">
                        <div>Cost Centers</div>
                    </a>
                </li>

            </ul>
        </li>


        @php
            $usersRoutes = ['admin.users.get', 'admin.roles.get', 'admin.permissions.get', 'admin.role_assignment.get'];
        @endphp

        <li class="menu-item {{ in_array(request()->route()->getName(), $usersRoutes) ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-shield"></i>
                <div>Users & Access</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.users.get') ? 'active' : '' }}">
                    <a href="{{ route('admin.users.get') }}" class="menu-link">
                        <div>Users</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.roles.get') ? 'active' : '' }}">
                    <a href="{{ route('admin.roles.get') }}" class="menu-link">
                        <div>Roles</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.permissions.get') ? 'active' : '' }}">
                    <a href="{{ route('admin.permissions.get') }}" class="menu-link">
                        <div>Permissions</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.role_assignment.get') ? 'active' : '' }}">
                    <a href="{{ route('admin.role_assignment.get') }}" class="menu-link">
                        <div>Role Assignment</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- HR CORE -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">HR Core</span>
        </li>

        <li
            class="menu-item {{ request()->routeIs(['admin.employees.*', 'admin.transfers.*', 'admin.exits.*']) ? 'active open' : '' }}">

            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-id-card"></i>
                <div>Employees</div>
            </a>

            <ul class="menu-sub">

                <li class="menu-item {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.employees.index') }}" class="menu-link">
                        <div>Employee List</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('admin.transfers.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.transfers.index') }}" class="menu-link">
                        <div>Employee Transfers</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('admin.exits.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.exits.index') }}" class="menu-link">
                        <div>Employee Exits</div>
                    </a>
                </li>

            </ul>
        </li>

        <li class="menu-item {{ request()->routeIs(['admin.attendance.*']) ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-time"></i>
                <div>Attendance</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.attendance.daily') ? 'active' : '' }}">
                    <a href="{{ route('admin.attendance.daily') }}" class="menu-link">
                        <div>Daily Attendance</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('admin.attendance.logs') ? 'active' : '' }}">
                    <a href="{{ route('admin.attendance.logs') }}" class="menu-link">
                        <div>Attendance Logs</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('admin.attendance.corrections') ? 'active' : '' }}">
                    <a href="{{ route('admin.attendance.corrections') }}" class="menu-link">
                        <div>Corrections / Regularization</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('admin.attendance.overtime') ? 'active' : '' }}">
                    <a href="{{ route('admin.attendance.overtime') }}" class="menu-link">
                        <div>Overtime</div>
                    </a>
                </li>
            </ul>
        </li>

        <li
            class="menu-item {{ request()->routeIs([
                'admin.shift_types.*',
                'admin.shift_groups.*',
                'admin.shift_assignments.*',
                'admin.holiday_calendars.*',
                'admin.holidays.*',
                'admin.work_week_profiles.*',
            ])
                ? 'active open'
                : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-calendar"></i>
                <div>Shifts & Calendar</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.shift_types.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.shift_types.index') }}" class="menu-link">
                        <div>Shift Types</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.shift_groups.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.shift_groups.index') }}" class="menu-link">
                        <div>Shift Groups</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.shift_assignments.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.shift_assignments.index') }}" class="menu-link">
                        <div>Shift Assignments</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.holiday_calendars.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.holiday_calendars.index') }}" class="menu-link">
                        <div>Holiday Calendars</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.holidays.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.holidays.index') }}" class="menu-link">
                        <div>Holidays</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.work_week_profiles.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.work_week_profiles.index') }}" class="menu-link">
                        <div>Work Week Profiles</div>
                    </a>
                </li>
            </ul>
        </li>

        <li
            class="menu-item {{ request()->routeIs([
                'admin.leave_types.*',
                'admin.leave_policies.*',
                'admin.employee_leave_policies.*',
                'admin.leave_requests.*',
                'admin.leave_balances.*',
            ])
                ? 'active open'
                : '' }}">

            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-log-out-circle"></i>
                <div>Leave Management</div>
            </a>

            <ul class="menu-sub">

                <li class="menu-item {{ request()->routeIs('admin.leave_types.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.leave_types.index') }}" class="menu-link">
                        <div>Leave Types</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('admin.leave_policies.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.leave_policies.index') }}" class="menu-link">
                        <div>Leave Policies</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('admin.employee_leave_policies.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.employee_leave_policies.index') }}" class="menu-link">
                        <div>Employee Leave Policies</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('admin.leave_requests.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.leave_requests.index') }}" class="menu-link">
                        <div>Leave Requests</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('admin.leave_balances.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.leave_balances.index') }}" class="menu-link">
                        <div>Leave Balances</div>
                    </a>
                </li>

            </ul>
        </li>

        <!-- PAYROLL -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Payroll</span>
        </li>

        <li
            class="menu-item {{ request()->routeIs([
                'admin.pay_schedules.*',
                'admin.salary_components.*',
                'admin.salary_structures.*',
                'admin.employee_salary.*',
            ])
                ? 'active open'
                : '' }}">

            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-money"></i>
                <div>Payroll Setup</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.pay_schedules.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.pay_schedules.index') }}" class="menu-link">
                        <div>Pay Schedules</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('admin.salary_components.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.salary_components.index') }}" class="menu-link">
                        <div>Salary Components</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('admin.salary_structures.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.salary_structures.index') }}" class="menu-link">
                        <div>Salary Structures</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('admin.employee_salary.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.employee_salary.index') }}" class="menu-link">
                        <div>Employee Salary Assignment</div>
                    </a>
                </li>
            </ul>
        </li>

        <li
            class="menu-item {{ request()->routeIs(['admin.payroll.run.*', 'admin.payroll_runs.*', 'admin.payslips.*', 'admin.loans.*'])
                ? 'active open'
                : '' }}">

            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-receipt"></i>
                <div>Payroll Processing</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.payroll.run.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.payroll.run.create') }}" class="menu-link">
                        <div>Run Payroll</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('admin.payroll_runs.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.payroll_runs.index') }}" class="menu-link">
                        <div>Payroll Runs</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('admin.payslips.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.payslips.index') }}" class="menu-link">
                        <div>Payslips</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('admin.loans.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.loans.index') }}" class="menu-link">
                        <div>Loans & Advances</div>
                    </a>
                </li>
            </ul>
        </li>

        @php
            $workflowRoutes = [
                'admin.workflows.index',
                'admin.workflows.create',
                'admin.workflows.edit',
                'admin.approval_requests.index',
                'admin.approval_history.index',
            ];
        @endphp

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Approvals & Automation</span>
        </li>

        <li class="menu-item {{ in_array(request()->route()->getName(), $workflowRoutes) ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-git-branch"></i>
                <div>Workflows</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.workflows.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.workflows.index') }}" class="menu-link">
                        <div>Workflow Builder</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.approval_requests.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.approval_requests.index') }}" class="menu-link">
                        <div>Approval Requests</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.approval_history.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.approval_history.index') }}" class="menu-link">
                        <div>Approval History</div>
                    </a>
                </li>
            </ul>
        </li>

        <li
            class="menu-item {{ request()->routeIs(['admin.asset_categories.*', 'admin.assets.*', 'admin.asset_assignments.*'])
                ? 'active open'
                : '' }}">

            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-box"></i>
                <div>Assets</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.asset_categories.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.asset_categories.index') }}" class="menu-link">
                        <div>Asset Categories</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('admin.assets.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.assets.index') }}" class="menu-link">
                        <div>Assets List</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('admin.asset_assignments.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.asset_assignments.index') }}" class="menu-link">
                        <div>Asset Assignments</div>
                    </a>
                </li>
            </ul>
        </li>

        <li
            class="menu-item {{ request()->routeIs(['admin.request_types.*', 'admin.requests.*']) ? 'active open' : '' }}">

            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-message-square-detail"></i>
                <div>HR Requests</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.request_types.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.request_types.index') }}" class="menu-link">
                        <div>Request Types</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('admin.requests.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.requests.index') }}" class="menu-link">
                        <div>Requests</div>
                    </a>
                </li>
            </ul>
        </li>

        <li
            class="menu-item {{ request()->routeIs([
                'admin.templates.index',
                'admin.templates.offer_letters',
                'admin.templates.experience_letters',
                'admin.templates.policies',
            ])
                ? 'active open'
                : '' }}">

            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div>Letters & Templates</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.templates.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.templates.index') }}" class="menu-link">
                        <div>Template Library</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('admin.templates.offer_letters') ? 'active' : '' }}">
                    <a href="{{ route('admin.templates.offer_letters') }}" class="menu-link">
                        <div>Offer Letter Templates</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('admin.templates.experience_letters') ? 'active' : '' }}">
                    <a href="{{ route('admin.templates.experience_letters') }}" class="menu-link">
                        <div>Experience Letter Templates</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->routeIs('admin.templates.policies') ? 'active' : '' }}">
                    <a href="{{ route('admin.templates.policies') }}" class="menu-link">
                        <div>Policy Templates</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- REPORTS -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Reports</span>
        </li>

        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-bar-chart"></i>
                <div>Reports</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item"><a href="javascript:void(0);" class="menu-link">
                        <div>Employee Reports</div>
                    </a></li>
                <li class="menu-item"><a href="javascript:void(0);" class="menu-link">
                        <div>Attendance Reports</div>
                    </a></li>
                <li class="menu-item"><a href="javascript:void(0);" class="menu-link">
                        <div>Leave Reports</div>
                    </a></li>
                <li class="menu-item"><a href="javascript:void(0);" class="menu-link">
                        <div>Payroll Reports</div>
                    </a></li>
                <li class="menu-item"><a href="javascript:void(0);" class="menu-link">
                        <div>Audit Logs</div>
                    </a></li>
            </ul>
        </li>

        <!-- SYSTEM -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">System</span>
        </li>

        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-cog"></i>
                <div>System Settings</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item"><a href="javascript:void(0);" class="menu-link">
                        <div>General Settings</div>
                    </a></li>
                <li class="menu-item"><a href="javascript:void(0);" class="menu-link">
                        <div>Notifications</div>
                    </a></li>
                <li class="menu-item"><a href="javascript:void(0);" class="menu-link">
                        <div>Custom Fields</div>
                    </a></li>
                <li class="menu-item"><a href="javascript:void(0);" class="menu-link">
                        <div>Backups / Exports</div>
                    </a></li>
            </ul>
        </li>
    </ul>
</aside>
<!-- / Menu -->
