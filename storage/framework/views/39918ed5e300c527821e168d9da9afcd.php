<?php $__env->startSection('content'); ?>
<div class="module-head">
    <div>
        <h2 class="module-title">Charge Slip Form</h2>
        <div class="module-note">Requestor fills out this form first, then the request goes to Asset Management, College Dean, and Executive Director.</div>
    </div>
</div>

<div class="form-shell">
    <form method="POST" action="<?php echo e(route('requisitions.store')); ?>" id="chargeSlipForm">
        <?php echo csrf_field(); ?>
        <div class="surface p-3 mb-3" style="background:#fff">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
                <div>
                    <div style="font-size:22px;font-weight:800;color:#193b7a">NU CLARK</div>
                    <div class="tiny text-uppercase" style="letter-spacing:.08em">Charge Slip Form</div>
                </div>
                <div class="tiny" style="min-width:220px">
                    <label class="form-label">CSF No.</label>
                    <input type="text" name="csf_no" class="form-control" value="<?php echo e(old('csf_no')); ?>" placeholder="Optional reference number">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label">Date</label>
                    <input type="text" class="form-control" value="<?php echo e(now()->format('F d, Y')); ?>" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Branch</label>
                    <input type="text" name="branch" class="form-control" value="<?php echo e(old('branch', 'NU Clark')); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Department / Office</label>
                    <select name="department_id" class="form-select" required>
                        <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($department->id); ?>" <?php if(old('department_id', auth()->user()->department_id) == $department->id): echo 'selected'; endif; ?>><?php echo e($department->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Charge To (Per Budget Item)</label>
                    <input type="text" name="charge_to_budget_item" class="form-control" value="<?php echo e(old('charge_to_budget_item')); ?>" placeholder="Example: Office Supplies" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Purpose</label>
                    <input type="text" name="purpose" class="form-control" value="<?php echo e(old('purpose')); ?>" placeholder="Example: Daily operation / enrollment" required>
                </div>
            </div>

            <div class="table-responsive mb-3">
                <table class="data-table" id="itemsTable">
                    <thead>
                        <tr>
                            <th style="width:26%">Item</th>
                            <th>Unit</th>
                            <th>Available</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Amount</th>
                            <th>Remarks</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $oldItems = old('items', [['item_id' => $selectedItemId, 'quantity_requested' => 1, 'remarks' => '']]);
                        ?>
                        <?php $__currentLoopData = $oldItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <select name="items[<?php echo e($index); ?>][item_id]" class="form-select item-select" required>
                                    <option value="">Select item</option>
                                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($item->id); ?>" data-unit="<?php echo e($item->unit); ?>" data-stock="<?php echo e($item->quantity); ?>" data-cost="<?php echo e(number_format($item->latest_unit_cost ?? 0, 2, '.', '')); ?>" <?php if(($row['item_id'] ?? null) == $item->id): echo 'selected'; endif; ?>><?php echo e($item->name); ?> (<?php echo e($item->quantity); ?> <?php echo e($item->unit); ?>)</option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                            <td><input type="text" class="form-control unit-display" readonly></td>
                            <td><input type="text" class="form-control stock-display" readonly></td>
                            <td><input type="number" name="items[<?php echo e($index); ?>][quantity_requested]" class="form-control qty-input" min="1" value="<?php echo e($row['quantity_requested'] ?? 1); ?>" required></td>
                            <td><input type="text" class="form-control unit-cost-display" readonly></td>
                            <td><input type="text" class="form-control amount-display" readonly></td>
                            <td><input type="text" name="items[<?php echo e($index); ?>][remarks]" class="form-control" value="<?php echo e($row['remarks'] ?? ''); ?>" placeholder="Optional"></td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger remove-row">×</button></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between flex-wrap gap-2 align-items-center">
                <button type="button" class="btn-soft small-btn" id="addRowBtn"><i class="bi bi-plus-lg"></i> Add Item Row</button>
                <div class="report-stat" style="min-width:220px">
                    <div class="tiny-2">Estimated Total Amount</div>
                    <div class="fw-bold" id="grandTotal">0.00</div>
                </div>
            </div>
        </div>

        <div class="surface p-3" style="background:#fff">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Requested By</label>
                    <input type="text" name="requested_by_name" class="form-control" value="<?php echo e(old('requested_by_name', auth()->user()->name)); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Checked By (College Dean)</label>
                    <input type="text" name="checked_by_name" class="form-control" value="<?php echo e(old('checked_by_name')); ?>" placeholder="Optional label on form">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Approved By (Executive Director)</label>
                    <input type="text" name="approved_by_name" class="form-control" value="<?php echo e(old('approved_by_name')); ?>" placeholder="Optional label on form">
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button class="btn-primaryx">Submit Charge Slip</button>
            <a href="<?php echo e(route('requisitions.index')); ?>" class="btn btn-light small-btn" style="border:1px solid #c7cbd4">Cancel</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const tableBody = document.querySelector('#itemsTable tbody');
const addBtn = document.getElementById('addRowBtn');
const grandTotal = document.getElementById('grandTotal');
const itemOptions = `<?php echo collect($items)->map(fn($item) => '<option value="'.$item->id.'" data-unit="'.$item->unit.'" data-stock="'.$item->quantity.'" data-cost="'.number_format($item->latest_unit_cost ?? 0, 2, '.', '').'">'.e($item->name).' ('.$item->quantity.' '.$item->unit.')</option>')->implode(''); ?>`;

function formatCurrency(value) {
    const number = Number(value || 0);
    return number.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function updateGrandTotal() {
    const total = [...tableBody.querySelectorAll('.amount-display')]
        .reduce((sum, input) => sum + Number((input.dataset.rawAmount || 0)), 0);
    grandTotal.textContent = formatCurrency(total);
}

function updateAmount(row) {
    const qty = Number(row.querySelector('.qty-input')?.value || 0);
    const cost = Number(row.querySelector('.item-select').selectedOptions[0]?.dataset.cost || 0);
    const amount = qty * cost;
    const amountInput = row.querySelector('.amount-display');
    amountInput.value = formatCurrency(amount);
    amountInput.dataset.rawAmount = amount;
    updateGrandTotal();
}

function refreshRow(row) {
    const selected = row.querySelector('.item-select').selectedOptions[0];
    row.querySelector('.unit-display').value = selected?.dataset.unit || '';
    row.querySelector('.stock-display').value = selected?.dataset.stock || '';
    row.querySelector('.unit-cost-display').value = formatCurrency(selected?.dataset.cost || 0);
    updateAmount(row);
}

function bindRow(row) {
    row.querySelector('.item-select').addEventListener('change', () => refreshRow(row));
    row.querySelector('.qty-input').addEventListener('input', () => updateAmount(row));
    row.querySelector('.remove-row').addEventListener('click', () => {
        if (tableBody.querySelectorAll('tr').length > 1) {
            row.remove();
            reindexRows();
            updateGrandTotal();
        }
    });
    refreshRow(row);
}

function reindexRows() {
    [...tableBody.querySelectorAll('tr')].forEach((row, index) => {
        row.querySelectorAll('select, input').forEach((input) => {
            if (input.name) {
                input.name = input.name.replace(/items\[\d+\]/, `items[${index}]`);
            }
        });
    });
}

addBtn.addEventListener('click', () => {
    const index = tableBody.querySelectorAll('tr').length;
    const row = document.createElement('tr');
    row.innerHTML = `
        <td><select name="items[${index}][item_id]" class="form-select item-select" required><option value="">Select item</option>${itemOptions}</select></td>
        <td><input type="text" class="form-control unit-display" readonly></td>
        <td><input type="text" class="form-control stock-display" readonly></td>
        <td><input type="number" name="items[${index}][quantity_requested]" class="form-control qty-input" min="1" value="1" required></td>
        <td><input type="text" class="form-control unit-cost-display" readonly></td>
        <td><input type="text" class="form-control amount-display" readonly></td>
        <td><input type="text" name="items[${index}][remarks]" class="form-control" placeholder="Optional"></td>
        <td><button type="button" class="btn btn-sm btn-outline-danger remove-row">×</button></td>`;
    tableBody.appendChild(row);
    bindRow(row);
});

document.querySelectorAll('#itemsTable tbody tr').forEach(bindRow);
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'Charge Slip Request'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/claude/webfix/capex-opex/resources/views/requisitions/create.blade.php ENDPATH**/ ?>