<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\ApprovalHistoryController;
use App\Http\Controllers\Admin\ApprovalRequestController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\GradeController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\ViewsController;
use App\Http\Controllers\Admin\WorkflowController;
use App\Http\Controllers\HR\AssetAssignmentController;
use App\Http\Controllers\HR\AssetCategoryController;
use App\Http\Controllers\HR\AssetController;
use App\Http\Controllers\HR\AttendanceController;
use App\Http\Controllers\HR\CompanyController;
use App\Http\Controllers\HR\CostCenterController;
use App\Http\Controllers\HR\DepartmentController;
use App\Http\Controllers\HR\DesignationController;
use App\Http\Controllers\HR\DocumentsController;
use App\Http\Controllers\HR\EmployeeController;
use App\Http\Controllers\HR\EmployeeExitController;
use App\Http\Controllers\HR\EmployeeLeavePolicyController;
use App\Http\Controllers\HR\EmployeeTransferController;
use App\Http\Controllers\HR\HolidayCalendarController;
use App\Http\Controllers\HR\HolidayController;
use App\Http\Controllers\HR\HRRequestController;
use App\Http\Controllers\HR\LeaveBalanceController;
use App\Http\Controllers\HR\LeavePolicyController;
use App\Http\Controllers\HR\LeaveRequestController;
use App\Http\Controllers\HR\LeaveTypeController;
use App\Http\Controllers\HR\ReportController;
use App\Http\Controllers\HR\RequestTypeController;
use App\Http\Controllers\HR\ShiftAssignmentController;
use App\Http\Controllers\HR\ShiftGroupController;
use App\Http\Controllers\HR\ShiftTypeController;
use App\Http\Controllers\HR\SystemSettingController;
use App\Http\Controllers\HR\TemplateController;
use App\Http\Controllers\HR\WorkWeekProfileController;
use App\Http\Controllers\Payroll\EmployeeSalaryController;
use App\Http\Controllers\Payroll\LoanController;
use App\Http\Controllers\Payroll\PayrollProcessingController;
use App\Http\Controllers\Payroll\PayrollRunController;
use App\Http\Controllers\Payroll\PayScheduleController;
use App\Http\Controllers\Payroll\PayslipController;
use App\Http\Controllers\Payroll\SalaryComponentController;
use App\Http\Controllers\Payroll\SalaryStructureController;
use Illuminate\Support\Facades\Route;


Route::get('/', [AuthController::class, 'adminloginPage'])->name('login.get');

Route::prefix('admin')->name('admin.')->group(function () {

    // admin auth
    Route::get('/login', [AuthController::class, 'adminloginPage'])->name('login.get');
    Route::post('/login', [AuthController::class, 'adminloginPost'])->name('login.post');
    Route::get('/forgot-password', [AuthController::class, 'adminforgotPage'])->name('forgot.get');
    Route::get('/reset/{token?}/password', [AuthController::class, 'adminResetPage'])->name('reset.get');
    Route::post('/forgot-password', [AuthController::class, 'adminForgotPost'])->name('forgot.post');
    Route::post('/reset-password', [AuthController::class, 'adminResetPost'])->name('reset.post');
    Route::get('/logout', [AuthController::class, 'adminlogout'])->name('logout');



    Route::middleware(['auth', 'role:admin'])->group(function () {

        Route::get('/dashboard', [ViewsController::class, 'indexPage'])->name('index.get');
        Route::get('/tables', [ViewsController::class, 'tablesPage'])->name('tables.get');

        // users
        Route::get('/users', [AccountController::class, 'usersPage'])->name('users.get');
        Route::post('/store-user', [AccountController::class, 'storeUser'])->name('users.store');
        Route::get('/roles', [AccountController::class, 'rolesPage'])->name('roles.get');
        Route::post('/roles', [AccountController::class, 'rolesStore'])->name('roles.store');
        Route::put('/users/{user}/roles', [AccountController::class, 'updateRoles'])->name('uers.roles.update');
        Route::delete('/users/{user}', [AccountController::class, 'destroyUser'])->name('users.destroy');

        Route::get('/permissions', [AccountController::class, 'permissionsPage'])->name('permissions.get');
        Route::post('/permissions', [AccountController::class, 'permissionsStore'])->name('permissions.store');

        // Role Assignment page
        Route::get('/role-assignment', [AccountController::class, 'roleAssignmentPage'])->name('role_assignment.get');
        Route::post('/role-assignment/user-roles', [AccountController::class, 'assignRolesToUser'])->name('user_roles.assign');
        Route::post('/role-assignment/role-permissions', [AccountController::class, 'assignPermissionsToRole'])->name('role_permissions.assign');

        // employees data
        Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
        // employee profile
        Route::get('/profile/{emp?}/details', [EmployeeController::class, 'profilePage'])->name('profile.get');
        Route::put('/employee/{employee?}/profile', [EmployeeController::class, 'updateProfile'])->name('employees.profile.update');
        Route::post('/employee/{employee?}/photo', [EmployeeController::class, 'updatePhoto'])->name('employees.photo.update');

        // Employee Documents
        Route::get('/employees/{employee?}/documents', [DocumentsController::class, 'index'])->name('employees.documents');
        Route::post('/employees/{employee}/documents', [DocumentsController::class, 'store'])->name('employees.documents.store');
        Route::get('/documents/{document}/download', [DocumentsController::class, 'download'])->name('documents.download');
        Route::delete('/documents/{document}', [DocumentsController::class, 'destroy'])->name('documents.destroy');

        // employee transfers
        Route::get('/employee-transfers', [EmployeeTransferController::class, 'index'])->name('transfers.index');
        Route::post('/employee-transfers', [EmployeeTransferController::class, 'store'])->name('transfers.store');
        Route::put('/employee-transfers/{transfer?}/status', [EmployeeTransferController::class, 'updateStatus'])->name('transfers.status');

        // employee exits
        Route::get('/employee-exits', [EmployeeExitController::class, 'index'])->name('exits.index');
        Route::post('/employee-exits', [EmployeeExitController::class, 'store'])->name('exits.store');
        Route::put('/employee-exits/{resignation?}/status', [EmployeeExitController::class, 'updateStatus'])->name('exits.status');

        // company profile
        Route::get('/company', [CompanyController::class, 'edit'])->name('company.edit');
        Route::put('/company', [CompanyController::class, 'update'])->name('company.update');
        Route::post('/company/logo', [CompanyController::class, 'updateLogo'])->name('company.logo.update');

        // Departments
        Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
        Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
        Route::put('/departments/{department?}', [DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('/departments/{department?}', [DepartmentController::class, 'destroy'])->name('departments.destroy');

        // Designations
        Route::get('/designations', [DesignationController::class, 'index'])->name('designations.index');
        Route::post('/designations', [DesignationController::class, 'store'])->name('designations.store');
        Route::put('/designations/{designation?}', [DesignationController::class, 'update'])->name('designations.update');
        Route::delete('/designations/{designation?}', [DesignationController::class, 'destroy'])->name('designations.destroy');

        // Locations
        Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
        Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');
        Route::put('/locations/{location?}', [LocationController::class, 'update'])->name('locations.update');
        Route::delete('/locations/{location?}', [LocationController::class, 'destroy'])->name('locations.destroy');

        // Grades
        Route::get('/grades', [GradeController::class, 'index'])->name('grades.index');
        Route::post('/grades', [GradeController::class, 'store'])->name('grades.store');
        Route::put('/grades/{grade}', [GradeController::class, 'update'])->name('grades.update');
        Route::delete('/grades/{grade}', [GradeController::class, 'destroy'])->name('grades.destroy');

        // cost center
        Route::get('/cost-centers', [CostCenterController::class, 'index'])->name('cost_centers.index');
        Route::post('/cost-centers', [CostCenterController::class, 'store'])->name('cost_centers.store');
        Route::put('/cost-centers/{costCenter}', [CostCenterController::class, 'update'])->name('cost_centers.update');
        Route::delete('/cost-centers/{costCenter}', [CostCenterController::class, 'destroy'])->name('cost_centers.destroy');

        // employee exiot clearance
        Route::get('/exits/{resignation?}/clearance', [EmployeeExitController::class, 'showExitClearance'])
            ->name('exits.clearance.show');
        Route::post('/exits/{resignation?}/final-settlement', [EmployeeExitController::class, 'saveFinalSettlement'])
            ->name('exits.final_settlement.save');
        Route::post('/exit-clearance/tasks', [EmployeeExitController::class, 'addTask'])
            ->name('exits.clearance.task.add');
        Route::put('/exit-clearance/tasks/{task}', [EmployeeExitController::class, 'updateTaskStatus'])
            ->name('exits.clearance.task.status');
        Route::put('/exits/{resignation?}/clearance/status', [EmployeeExitController::class, 'updateClearanceStatus'])
            ->name('exits.clearance.status');


        // DAILY ATTENDANCE (records)
        Route::get('/attendance/daily', [AttendanceController::class, 'daily'])->name('attendance.daily');
        Route::post('/attendance/daily/mark', [AttendanceController::class, 'markDaily'])->name('attendance.daily.mark');
        Route::put('/attendance/daily/{record?}', [AttendanceController::class, 'updateDaily'])->name('attendance.daily.update');
        Route::post('/attendance/daily/process', [AttendanceController::class, 'processDate'])->name('attendance.daily.process');
        Route::get('/attendance/logs', [AttendanceController::class, 'logs'])->name('attendance.logs');
        Route::post('/attendance/logs', [AttendanceController::class, 'storeLog'])->name('attendance.logs.store');
        Route::delete('/attendance/logs/{log?}', [AttendanceController::class, 'deleteLog'])->name('attendance.logs.delete');
        Route::get('/attendance/corrections', [AttendanceController::class, 'corrections'])->name('attendance.corrections');
        Route::put('/attendance/corrections/{request?}/status', [AttendanceController::class, 'updateCorrectionStatus'])
            ->name('attendance.corrections.status');
        Route::get('/attendance/overtime', [AttendanceController::class, 'overtime'])->name('attendance.overtime');



        // SHIFT TYPES
        Route::get('/shift-types', [ShiftTypeController::class, 'index'])->name('shift_types.index');
        Route::post('/shift-types', [ShiftTypeController::class, 'store'])->name('shift_types.store');
        Route::put('/shift-types/{shiftType}', [ShiftTypeController::class, 'update'])->name('shift_types.update');
        Route::delete('/shift-types/{shiftType}', [ShiftTypeController::class, 'destroy'])->name('shift_types.destroy');

        // SHIFT GROUPS
        Route::get('/shift-groups', [ShiftGroupController::class, 'index'])->name('shift_groups.index');
        Route::post('/shift-groups', [ShiftGroupController::class, 'store'])->name('shift_groups.store');
        Route::put('/shift-groups/{shiftGroup}', [ShiftGroupController::class, 'update'])->name('shift_groups.update');
        Route::delete('/shift-groups/{shiftGroup}', [ShiftGroupController::class, 'destroy'])->name('shift_groups.destroy');

        // SHIFT ASSIGNMENTS
        Route::get('/shift-assignments', [ShiftAssignmentController::class, 'index'])->name('shift_assignments.index');
        Route::post('/shift-assignments', [ShiftAssignmentController::class, 'store'])->name('shift_assignments.store');
        Route::put('/shift-assignments/{assignment}', [ShiftAssignmentController::class, 'update'])->name('shift_assignments.update');
        Route::delete('/shift-assignments/{assignment}', [ShiftAssignmentController::class, 'destroy'])->name('shift_assignments.destroy');

        // HOLIDAY CALENDARS
        Route::get('/holiday-calendars', [HolidayCalendarController::class, 'index'])->name('holiday_calendars.index');
        Route::post('/holiday-calendars', [HolidayCalendarController::class, 'store'])->name('holiday_calendars.store');
        Route::put('/holiday-calendars/{calendar}', [HolidayCalendarController::class, 'update'])->name('holiday_calendars.update');
        Route::delete('/holiday-calendars/{calendar}', [HolidayCalendarController::class, 'destroy'])->name('holiday_calendars.destroy');

        // HOLIDAYS
        Route::get('/holidays', [HolidayController::class, 'index'])->name('holidays.index'); // filter by calendar_id
        Route::post('/holidays', [HolidayController::class, 'store'])->name('holidays.store');
        Route::delete('/holidays/{holiday}', [HolidayController::class, 'destroy'])->name('holidays.destroy');

        // WORK WEEK PROFILES
        Route::get('/work-week-profiles', [WorkWeekProfileController::class, 'index'])->name('work_week_profiles.index');
        Route::post('/work-week-profiles', [WorkWeekProfileController::class, 'store'])->name('work_week_profiles.store');
        Route::put('/work-week-profiles/{profile}', [WorkWeekProfileController::class, 'update'])->name('work_week_profiles.update');
        Route::delete('/work-week-profiles/{profile}', [WorkWeekProfileController::class, 'destroy'])->name('work_week_profiles.destroy');



        // Leave Types
        Route::get('/leave/types', [LeaveTypeController::class, 'index'])->name('leave_types.index');
        Route::post('/leave/types', [LeaveTypeController::class, 'store'])->name('leave_types.store');
        Route::put('/leave/types/{leaveType}', [LeaveTypeController::class, 'update'])->name('leave_types.update');
        Route::delete('/leave/types/{leaveType}', [LeaveTypeController::class, 'destroy'])->name('leave_types.destroy');

        // Leave Policies
        Route::get('/leave/policies', [LeavePolicyController::class, 'index'])->name('leave_policies.index');
        Route::post('/leave/policies', [LeavePolicyController::class, 'store'])->name('leave_policies.store');
        Route::put('/leave/policies/{leavePolicy}', [LeavePolicyController::class, 'update'])->name('leave_policies.update');
        Route::delete('/leave/policies/{leavePolicy}', [LeavePolicyController::class, 'destroy'])->name('leave_policies.destroy');

        // Employee Leave Policies (assign policy to employee)
        Route::get('/leave/employee-policies', [EmployeeLeavePolicyController::class, 'index'])->name('employee_leave_policies.index');
        Route::post('/leave/employee-policies', [EmployeeLeavePolicyController::class, 'store'])->name('employee_leave_policies.store');
        Route::put('/leave/employee-policies/{assignment}', [EmployeeLeavePolicyController::class, 'update'])->name('employee_leave_policies.update');
        Route::delete('/leave/employee-policies/{assignment}', [EmployeeLeavePolicyController::class, 'destroy'])->name('employee_leave_policies.destroy');

        // Leave Requests
        Route::get('/leave/requests', [LeaveRequestController::class, 'index'])->name('leave_requests.index');
        Route::post('/leave/requests', [LeaveRequestController::class, 'store'])->name('leave_requests.store'); // admin can create too
        Route::put('/leave/requests/{leaveRequest}/status', [LeaveRequestController::class, 'updateStatus'])->name('leave_requests.status');
        Route::delete('/leave/requests/{leaveRequest}', [LeaveRequestController::class, 'destroy'])->name('leave_requests.destroy');

        // Leave Balances
        Route::get('/leave/balances', [LeaveBalanceController::class, 'index'])->name('leave_balances.index');
        Route::post('/leave/balances/adjust', [LeaveBalanceController::class, 'adjust'])->name('leave_balances.adjust');


        // Payroll Setup - Pay Schedules
        Route::get('/payroll/pay-schedules', [PayScheduleController::class, 'index'])->name('pay_schedules.index');
        Route::post('/payroll/pay-schedules', [PayScheduleController::class, 'store'])->name('pay_schedules.store');
        Route::put('/payroll/pay-schedules/{paySchedule}', [PayScheduleController::class, 'update'])->name('pay_schedules.update');
        Route::delete('/payroll/pay-schedules/{paySchedule}', [PayScheduleController::class, 'destroy'])->name('pay_schedules.destroy');

        // Payroll Setup - Salary Components
        Route::get('/payroll/salary-components', [SalaryComponentController::class, 'index'])->name('salary_components.index');
        Route::post('/payroll/salary-components', [SalaryComponentController::class, 'store'])->name('salary_components.store');
        Route::put('/payroll/salary-components/{salaryComponent}', [SalaryComponentController::class, 'update'])->name('salary_components.update');
        Route::delete('/payroll/salary-components/{salaryComponent}', [SalaryComponentController::class, 'destroy'])->name('salary_components.destroy');

        // Payroll Setup - Salary Structures
        Route::get('/payroll/salary-structures', [SalaryStructureController::class, 'index'])->name('salary_structures.index');
        Route::post('/payroll/salary-structures', [SalaryStructureController::class, 'store'])->name('salary_structures.store');
        Route::put('/payroll/salary-structures/{salaryStructure}', [SalaryStructureController::class, 'update'])->name('salary_structures.update');
        Route::delete('/payroll/salary-structures/{salaryStructure}', [SalaryStructureController::class, 'destroy'])->name('salary_structures.destroy');

        // Structure Components (lines inside structure)
        Route::post('/payroll/salary-structures/{salaryStructure}/items', [SalaryStructureController::class, 'addItem'])->name('salary_structures.items.add');
        Route::delete('/payroll/salary-structures/items/{item}', [SalaryStructureController::class, 'removeItem'])->name('salary_structures.items.remove');

        // Employee Salary Assignment
        Route::get('/payroll/employee-salary', [EmployeeSalaryController::class, 'index'])->name('employee_salary.index');
        Route::post('/payroll/employee-salary', [EmployeeSalaryController::class, 'store'])->name('employee_salary.store');
        Route::put('/payroll/employee-salary/{assignment}', [EmployeeSalaryController::class, 'update'])->name('employee_salary.update');
        Route::delete('/payroll/employee-salary/{assignment}', [EmployeeSalaryController::class, 'destroy'])->name('employee_salary.destroy');


        // Payroll Processing
        Route::get('/payroll/run', [PayrollProcessingController::class, 'create'])->name('payroll.run.create');
        Route::post('/payroll/run', [PayrollProcessingController::class, 'store'])->name('payroll.run.store');

        Route::get('/payroll/runs', [PayrollRunController::class, 'index'])->name('payroll_runs.index');
        Route::get('/payroll/runs/{payrollRun}', [PayrollRunController::class, 'show'])->name('payroll_runs.show');
        Route::put('/payroll/runs/{payrollRun}/status', [PayrollRunController::class, 'updateStatus'])->name('payroll_runs.status');

        Route::get('/payroll/payslips', [PayslipController::class, 'index'])->name('payslips.index');
        Route::get('/payroll/payslips/{payrollRun?}/employee/{employee?}', [PayslipController::class, 'show'])->name('payslips.show');
        // later: Route::get('/payroll/payslips/{payrollRun}/employee/{employee}/download', ...)->name('payslips.download');

        Route::get('/payroll/loans', [LoanController::class, 'index'])->name('loans.index');
        Route::post('/payroll/loans', [LoanController::class, 'store'])->name('loans.store');
        Route::put('/payroll/loans/{loan}', [LoanController::class, 'update'])->name('loans.update');
        Route::delete('/payroll/loans/{loan}', [LoanController::class, 'destroy'])->name('loans.destroy');



        Route::get('/workflows', [WorkflowController::class, 'index'])->name('workflows.index');
        Route::get('/workflows/create', [WorkflowController::class, 'create'])->name('workflows.create');
        Route::post('/workflows', [WorkflowController::class, 'store'])->name('workflows.store');
        Route::get('/workflows/{workflow}/edit', [WorkflowController::class, 'edit'])->name('workflows.edit');
        Route::put('/workflows/{workflow}', [WorkflowController::class, 'update'])->name('workflows.update');
        Route::delete('/workflows/{workflow}', [WorkflowController::class, 'destroy'])->name('workflows.destroy');

        Route::get('/approval-requests', [ApprovalRequestController::class, 'index'])->name('approval_requests.index');
        Route::get('/approval-requests/{approvalRequest}', [ApprovalRequestController::class, 'show'])->name('approval_requests.show');
        Route::post('/approval-requests/{approvalRequest}/approve', [ApprovalRequestController::class, 'approve'])->name('approval_requests.approve');
        Route::post('/approval-requests/{approvalRequest}/reject', [ApprovalRequestController::class, 'reject'])->name('approval_requests.reject');
        Route::post('/approval-requests/{approvalRequest}/return', [ApprovalRequestController::class, 'returnBack'])->name('approval_requests.return');

        Route::get('/approval-history', [ApprovalHistoryController::class, 'index'])->name('approval_history.index');



        // ASSETS
        Route::get('/asset-categories', [AssetCategoryController::class, 'index'])->name('asset_categories.index');
        Route::post('/asset-categories', [AssetCategoryController::class, 'store'])->name('asset_categories.store');
        Route::put('/asset-categories/{assetCategory}', [AssetCategoryController::class, 'update'])->name('asset_categories.update');
        Route::delete('/asset-categories/{assetCategory}', [AssetCategoryController::class, 'destroy'])->name('asset_categories.destroy');

        Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
        Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
        Route::put('/assets/{asset}', [AssetController::class, 'update'])->name('assets.update');
        Route::delete('/assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');

        Route::get('/asset-assignments', [AssetAssignmentController::class, 'index'])->name('asset_assignments.index');
        Route::post('/asset-assignments', [AssetAssignmentController::class, 'store'])->name('asset_assignments.store');
        Route::put('/asset-assignments/{assetAssignment}/return', [AssetAssignmentController::class, 'returnAsset'])->name('asset_assignments.return');
        Route::delete('/asset-assignments/{assetAssignment}', [AssetAssignmentController::class, 'destroy'])->name('asset_assignments.destroy');

        // HR REQUEST TYPES
        Route::get('/hr-request-types', [RequestTypeController::class, 'index'])->name('request_types.index');
        Route::post('/hr-request-types', [RequestTypeController::class, 'store'])->name('request_types.store');
        Route::put('/hr-request-types/{hrRequestType}', [RequestTypeController::class, 'update'])->name('request_types.update');
        Route::delete('/hr-request-types/{hrRequestType}', [RequestTypeController::class, 'destroy'])->name('request_types.destroy');

        // HR REQUESTS
        Route::get('/hr-requests', [HRRequestController::class, 'index'])->name('requests.index');
        Route::post('/hr-requests', [HRRequestController::class, 'store'])->name('requests.store');
        Route::get('/hr-requests/{hrRequest}', [HRRequestController::class, 'show'])->name('requests.show');
        Route::put('/hr-requests/{hrRequest}/status', [HRRequestController::class, 'updateStatus'])->name('requests.status');
        Route::delete('/hr-requests/{hrRequest}', [HRRequestController::class, 'destroy'])->name('requests.destroy');


        // All templates
        Route::get('/templates', [TemplateController::class, 'index'])->name('templates.index');
        Route::post('/templates', [TemplateController::class, 'store'])->name('templates.store');
        Route::put('/templates/{template}', [TemplateController::class, 'update'])->name('templates.update');
        Route::delete('/templates/{template}', [TemplateController::class, 'destroy'])->name('templates.destroy');

        // Filtered views
        Route::get('/templates/offer-letters', [TemplateController::class, 'offerLetters'])->name('templates.offer_letters');
        Route::get('/templates/experience-letters', [TemplateController::class, 'experienceLetters'])->name('templates.experience_letters');
        Route::get('/templates/policies', [TemplateController::class, 'policies'])->name('templates.policies');



        // REPORTS
        Route::get('/reports/employees', [ReportController::class, 'employeeReports'])->name('reports.employee');
        Route::get('/reports/attendance', [ReportController::class, 'attendanceReports'])->name('reports.attendance');
        Route::get('/reports/leave', [ReportController::class, 'leaveReports'])->name('reports.leave');
        Route::get('/reports/payroll', [ReportController::class, 'payrollReports'])->name('reports.payroll');
        Route::get('/reports/audit-logs', [ReportController::class, 'auditLogs'])->name('reports.audit_logs');

        // SYSTEM SETTINGS
        Route::get('/settings/general', [SystemSettingController::class, 'general'])->name('settings.general');
        Route::post('/settings/general', [SystemSettingController::class, 'saveGeneral'])->name('settings.general.save');

        Route::get('/settings/notifications', [SystemSettingController::class, 'notifications'])->name('settings.notifications');
        Route::post('/settings/notifications', [SystemSettingController::class, 'saveNotifications'])->name('settings.notifications.save');

        Route::get('/settings/custom-fields', [SystemSettingController::class, 'customFields'])->name('settings.custom_fields');
        Route::post('/settings/custom-fields', [SystemSettingController::class, 'storeCustomField'])->name('settings.custom_fields.store');
        Route::delete('/settings/custom-fields/{customField}', [SystemSettingController::class, 'destroyCustomField'])->name('settings.custom_fields.destroy');

        Route::get('/settings/backups', [SystemSettingController::class, 'backups'])->name('settings.backups');
        Route::post('/settings/exports/employees', [SystemSettingController::class, 'exportEmployees'])->name('settings.exports.employees');
        Route::post('/settings/exports/attendance', [SystemSettingController::class, 'exportAttendance'])->name('settings.exports.attendance');
        Route::post('/settings/exports/payroll', [SystemSettingController::class, 'exportPayroll'])->name('settings.exports.payroll');
    });
});
