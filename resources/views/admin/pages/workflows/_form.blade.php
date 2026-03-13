@csrf
@if(isset($workflow))
    @method('PUT')
@endif

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">Module</label>
        <select name="module" class="form-select" required>
            <option value="">Select module</option>
            @foreach($modules as $key => $label)
                <option value="{{ $key }}"
                    {{ old('module', isset($workflow) ? $workflow->module : '') == $key ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-5 mb-3">
        <label class="form-label">Workflow Name</label>
        <input type="text" name="name" class="form-control"
            value="{{ old('name', isset($workflow) ? $workflow->name : '') }}" required>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label d-block">Status</label>
        <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                {{ old('is_active', isset($workflow) ? $workflow->is_active : true) ? 'checked' : '' }}>
            <label class="form-check-label">Active</label>
        </div>
    </div>
</div>

<hr>
<h5>Workflow Steps</h5>

@php
    $oldSteps = old(
        'steps',
        isset($workflow)
            ? $workflow->steps->sortBy('step_order')->values()->toArray()
            : [
                [
                    'step_order' => 1,
                    'approver_type' => 'manager',
                    'approver_role_id' => '',
                    'approver_user_id' => '',
                    'min_approvals' => 1
                ]
            ]
    );
@endphp

<div id="steps-wrapper">
    @foreach($oldSteps as $i => $step)
        <div class="card mb-3 step-row">
            <div class="card-body row">
                <div class="col-md-2 mb-2">
                    <label class="form-label">Step Order</label>
                    <input type="number" name="steps[{{ $i }}][step_order]" class="form-control"
                        value="{{ $step['step_order'] ?? 1 }}" min="1" required>
                </div>

                <div class="col-md-3 mb-2">
                    <label class="form-label">Approver Type</label>
                    <select name="steps[{{ $i }}][approver_type]" class="form-select" required>
                        <option value="manager" {{ ($step['approver_type'] ?? '') === 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="role" {{ ($step['approver_type'] ?? '') === 'role' ? 'selected' : '' }}>Role</option>
                        <option value="user" {{ ($step['approver_type'] ?? '') === 'user' ? 'selected' : '' }}>Specific User</option>
                    </select>
                </div>

                <div class="col-md-3 mb-2">
                    <label class="form-label">Approver Role</label>
                    <select name="steps[{{ $i }}][approver_role_id]" class="form-select">
                        <option value="">Select role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}"
                                {{ (string)($step['approver_role_id'] ?? '') === (string)$role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-2">
                    <label class="form-label">Approver User</label>
                    <select name="steps[{{ $i }}][approver_user_id]" class="form-select">
                        <option value="">Select user</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}"
                                {{ (string)($step['approver_user_id'] ?? '') === (string)$user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-1 mb-2">
                    <label class="form-label">Min</label>
                    <input type="number" name="steps[{{ $i }}][min_approvals]" class="form-control"
                        value="{{ $step['min_approvals'] ?? 1 }}" min="1" required>
                </div>
            </div>
        </div>
    @endforeach
</div>

<button type="button" class="btn btn-outline-primary btn-sm mb-4" id="add-step-btn">Add Step</button>

<hr>
<h5>Conditions (Optional)</h5>

@php
    $existingRules = old(
        'rules',
        isset($workflow) && $workflow->conditions->first()
            ? $workflow->conditions->first()->rules
            : [['field' => '', 'operator' => '', 'value' => '']]
    );
@endphp

<div id="rules-wrapper">
    @foreach($existingRules as $ri => $rule)
        <div class="row mb-2 rule-row">
            <div class="col-md-4">
                <input type="text" name="rules[{{ $ri }}][field]" class="form-control"
                    placeholder="Field e.g. total_days"
                    value="{{ $rule['field'] ?? '' }}">
            </div>
            <div class="col-md-3">
                <select name="rules[{{ $ri }}][operator]" class="form-select">
                    <option value="">Operator</option>
                    <option value="=" {{ ($rule['operator'] ?? '') === '=' ? 'selected' : '' }}>=</option>
                    <option value=">" {{ ($rule['operator'] ?? '') === '>' ? 'selected' : '' }}>&gt;</option>
                    <option value="<" {{ ($rule['operator'] ?? '') === '<' ? 'selected' : '' }}>&lt;</option>
                    <option value=">=" {{ ($rule['operator'] ?? '') === '>=' ? 'selected' : '' }}>&gt;=</option>
                    <option value="<=" {{ ($rule['operator'] ?? '') === '<=' ? 'selected' : '' }}>&lt;=</option>
                    <option value="in" {{ ($rule['operator'] ?? '') === 'in' ? 'selected' : '' }}>in</option>
                </select>
            </div>
            <div class="col-md-5">
                <input type="text" name="rules[{{ $ri }}][value]" class="form-control"
                    placeholder="Value"
                    value="{{ $rule['value'] ?? '' }}">
            </div>
        </div>
    @endforeach
</div>

<button type="button" class="btn btn-outline-secondary btn-sm mb-4" id="add-rule-btn">Add Condition</button>

<div>
    <button type="submit" class="btn btn-primary">Save Workflow</button>
    <a href="{{ route('admin.workflows.index') }}" class="btn btn-outline-dark">Cancel</a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let stepIndex = {{ count($oldSteps) }};
    let ruleIndex = {{ count($existingRules) }};

    document.getElementById('add-step-btn').addEventListener('click', function () {
        const html = `
            <div class="card mb-3 step-row">
                <div class="card-body row">
                    <div class="col-md-2 mb-2">
                        <label class="form-label">Step Order</label>
                        <input type="number" name="steps[${stepIndex}][step_order]" class="form-control" value="${stepIndex + 1}" min="1" required>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Approver Type</label>
                        <select name="steps[${stepIndex}][approver_type]" class="form-select" required>
                            <option value="manager">Manager</option>
                            <option value="role">Role</option>
                            <option value="user">Specific User</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Approver Role</label>
                        <select name="steps[${stepIndex}][approver_role_id]" class="form-select">
                            <option value="">Select role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Approver User</label>
                        <select name="steps[${stepIndex}][approver_user_id]" class="form-select">
                            <option value="">Select user</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1 mb-2">
                        <label class="form-label">Min</label>
                        <input type="number" name="steps[${stepIndex}][min_approvals]" class="form-control" value="1" min="1" required>
                    </div>
                </div>
            </div>
        `;
        document.getElementById('steps-wrapper').insertAdjacentHTML('beforeend', html);
        stepIndex++;
    });

    document.getElementById('add-rule-btn').addEventListener('click', function () {
        const html = `
            <div class="row mb-2 rule-row">
                <div class="col-md-4">
                    <input type="text" name="rules[${ruleIndex}][field]" class="form-control" placeholder="Field e.g. total_days">
                </div>
                <div class="col-md-3">
                    <select name="rules[${ruleIndex}][operator]" class="form-select">
                        <option value="">Operator</option>
                        <option value="=">=</option>
                        <option value=">">&gt;</option>
                        <option value="<">&lt;</option>
                        <option value=">=">&gt;=</option>
                        <option value="<=">&lt;=</option>
                        <option value="in">in</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <input type="text" name="rules[${ruleIndex}][value]" class="form-control" placeholder="Value">
                </div>
            </div>
        `;
        document.getElementById('rules-wrapper').insertAdjacentHTML('beforeend', html);
        ruleIndex++;
    });
});
</script>
