<?php $__env->startSection('content'); ?>
<div class="module-head">
    <div>
        <h2 class="module-title">Detailed Reports</h2>
        <div class="module-note">Inventory, requisition, issuance, and acquisition analytics</div>
    </div>
</div>

<div class="stat-grid">
    <div class="report-stat"><div class="tiny-2">CAPEX Assets</div><div class="stat-value" style="font-size:28px"><?php echo e($totals['assets']); ?></div></div>
    <div class="report-stat"><div class="tiny-2">OPEX Consumables</div><div class="stat-value" style="font-size:28px"><?php echo e($totals['consumables']); ?></div></div>
    <div class="report-stat"><div class="tiny-2">Total Requisitions</div><div class="stat-value" style="font-size:28px"><?php echo e($totals['requisitions']); ?></div></div>
    <div class="report-stat"><div class="tiny-2">Issued Records</div><div class="stat-value" style="font-size:28px"><?php echo e($totals['issued']); ?></div></div>
</div>

<div class="report-grid">
    <div class="report-box">
        <div class="chart-head"><i class="bi bi-boxes"></i> Inventory by Type</div>
        <div class="chart-body"><div class="chart-wrap"><canvas id="inventorySummaryChart"></canvas></div></div>
    </div>
    <div class="report-box">
        <div class="chart-head"><i class="bi bi-pie-chart"></i> Requisition Status Mix</div>
        <div class="chart-body"><div class="chart-wrap"><canvas id="requestStatusChart"></canvas></div></div>
    </div>
</div>

<div class="report-grid">
    <div class="report-box">
        <div class="chart-head"><i class="bi bi-building"></i> Requests by Department</div>
        <div class="chart-body"><div class="chart-wrap"><canvas id="departmentRequestChart"></canvas></div></div>
    </div>
    <div class="report-box">
        <div class="chart-head"><i class="bi bi-currency-dollar"></i> Monthly Acquisition Cost</div>
        <div class="chart-body"><div class="chart-wrap"><canvas id="monthlyAcquisitionChart"></canvas></div></div>
    </div>
</div>

<div class="data-panel mb-3">
    <div class="module-head mb-2">
        <div>
            <h2 class="module-title">Low Stock Report</h2>
            <div class="module-note">Consumables below or equal to their stock threshold</div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr><th>Item Code</th><th>Name</th><th>Category</th><th>Quantity</th><th>Threshold</th></tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $lowStockItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td data-label="Item Code"><?php echo e($item->item_code); ?></td>
                    <td data-label="Name"><?php echo e($item->name); ?></td>
                    <td data-label="Category"><?php echo e($item->category->name ?? 'Office Supplies'); ?></td>
                    <td data-label="Quantity"><span class="status low"><?php echo e($item->quantity); ?></span></td>
                    <td data-label="Threshold"><?php echo e($item->low_stock_threshold); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="empty-state">No low stock incidents recorded.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="data-panel mb-3">
    <div class="module-head mb-2">
        <div>
            <h2 class="module-title">Predictive Analytics for OPEX</h2>
            <div class="module-note">Forecasted next-term demand based on the most recent approved quantities.</div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr><th>Item</th><th>Recent Approved Quantities</th><th>Forecast Next Term</th><th>Current Stock</th><th>Action Insight</th></tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $forecastItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $forecast): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td data-label="Item"><?php echo e($forecast['item_name']); ?></td>
                    <td data-label="Recent Approved Quantities"><?php echo e($forecast['basis']); ?></td>
                    <td data-label="Forecast Next Term"><span class="status pending"><?php echo e($forecast['forecast_next_term']); ?> <?php echo e($forecast['unit']); ?></span></td>
                    <td data-label="Current Stock"><?php echo e($forecast['current_stock']); ?> <?php echo e($forecast['unit']); ?></td>
                    <td data-label="Action Insight">
                        <?php if($forecast['current_stock'] < $forecast['forecast_next_term']): ?>
                            <span class="status low">Restock recommended</span>
                        <?php else: ?>
                            <span class="status approved">Stock is sufficient</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="empty-state">Not enough approved requisition history yet to generate a forecast.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<div class="data-panel mb-3">
    <div class="module-head mb-2">
        <div>
            <h2 class="module-title">Asset Location Report</h2>
            <div class="module-note">CAPEX asset assignment by room or area</div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr><th>Item Code</th><th>Asset Name</th><th>Category</th><th>Assigned Room</th><th>Status</th></tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $assetLocationReport; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td data-label="Item Code"><?php echo e($asset->item_code); ?></td>
                    <td data-label="Asset Name"><?php echo e($asset->name); ?></td>
                    <td data-label="Category"><?php echo e($asset->category->name ?? 'Uncategorized'); ?></td>
                    <td data-label="Assigned Room"><?php echo e($asset->room_assigned ?: 'Not assigned'); ?></td>
                    <td data-label="Status">
                        <?php if($asset->room_assigned): ?>
                            <span class="status approved">Trackable</span>
                        <?php else: ?>
                            <span class="status pending">Needs room assignment</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="empty-state">No CAPEX assets available for location reporting.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="data-panel mb-3">
    <div class="module-head mb-2">
        <div>
            <h2 class="module-title">Approval Tracking Report</h2>
            <div class="module-note">Recent requisitions with requestor, department, and approval status</div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr><th>Requisition No.</th><th>Requestor</th><th>Department</th><th>Requested At</th><th>Status</th></tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $approvalTracking; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td data-label="Requisition No."><?php echo e($record->requisition_no); ?></td>
                    <td data-label="Requestor"><?php echo e($record->user->name ?? 'Unknown'); ?></td>
                    <td data-label="Department"><?php echo e($record->department->name ?? 'Unassigned'); ?></td>
                    <td data-label="Requested At"><?php echo e(optional($record->requested_at)->format('M d, Y h:i A') ?: 'N/A'); ?></td>
                    <td data-label="Status"><span class="status pending"><?php echo e($record->statusLabel()); ?></span></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="empty-state">No requisition records available.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="report-grid">
    <div class="report-box p-3">
        <h5 class="mb-2">Issuance Status Snapshot</h5>
        <div class="chart-wrap"><canvas id="issuanceStatusChart"></canvas></div>
    </div>
    <div class="report-box p-3 d-flex flex-column justify-content-between">
        <div>
            <h5 class="mb-2">Acquisition Cost Summary</h5>
            <div class="tiny mb-3">Total recorded procurement value across all acquisitions.</div>
        </div>
        <div class="stat-value" style="font-size:36px">₱<?php echo e(number_format($totals['acquisition_cost'], 2)); ?></div>
        <div class="tiny-2">Based on quantity × unit cost entries.</div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
const baseOptions = { responsive: true, maintainAspectRatio: false, plugins: { legend: { labels: { boxWidth: 12 } } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } };
new Chart(document.getElementById('inventorySummaryChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($inventoryByType->pluck('item_type')); ?>,
        datasets: [{ label: 'Items', data: <?php echo json_encode($inventoryByType->pluck('total_items')); ?>, backgroundColor: ['#8b80ff','#f0a2a0'], borderRadius: 8 }]
    },
    options: baseOptions
});
new Chart(document.getElementById('requestStatusChart'), {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($requestStatus->pluck('status')); ?>,
        datasets: [{ data: <?php echo json_encode($requestStatus->pluck('total')); ?>, backgroundColor: ['#f3c969','#27c96f','#57d2eb','#ff4d4f','#8b80ff'] }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
});
new Chart(document.getElementById('departmentRequestChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($departmentRequests->pluck('name')); ?>,
        datasets: [{ label: 'Requests', data: <?php echo json_encode($departmentRequests->pluck('total_requests')); ?>, backgroundColor: '#74d0e8', borderRadius: 8 }]
    },
    options: baseOptions
});
new Chart(document.getElementById('monthlyAcquisitionChart'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode($monthlyAcquisitions->pluck('month_num')->map(fn($m) => date('M', mktime(0,0,0,(int)$m,1)))); ?>,
        datasets: [{ label: 'Cost', data: <?php echo json_encode($monthlyAcquisitions->pluck('total_amount')); ?>, borderColor: '#8b80ff', backgroundColor: 'rgba(139,128,255,.18)', fill: true, tension: .35 }]
    },
    options: baseOptions
});
new Chart(document.getElementById('issuanceStatusChart'), {
    type: 'polarArea',
    data: {
        labels: <?php echo json_encode($issuanceStatus->pluck('status')); ?>,
        datasets: [{ data: <?php echo json_encode($issuanceStatus->pluck('total')); ?>, backgroundColor: ['#57d2eb','#27c96f','#f3c969','#ff4d4f'] }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'Reports'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/claude/work/capex-opex/resources/views/reports/index.blade.php ENDPATH**/ ?>