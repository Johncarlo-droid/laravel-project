<?php $__env->startSection('content'); ?>
<div class="module-head">
    <div>
        <h2 class="module-title">QR Scan Flow</h2>
        <div class="module-note">Scan, preview, and verify the matching CAPEX asset record</div>
    </div>
</div>

<div class="qr-grid mb-3">
    <div class="scanner-shell">
        <div class="module-head mb-3">
            <div>
                <h2 class="module-title">Live Scanner</h2>
                <div class="module-note">Use a mobile camera or paste a QR value manually. CAPEX labels store the item code for stable matching.</div>
            </div>
        </div>
        <div class="scanner-box mb-3">
            <div id="reader" style="width:100%;min-height:300px"></div>
        </div>
        <form method="GET" action="<?php echo e(route('qr.index')); ?>" class="d-flex gap-2 flex-wrap">
            <input type="text" class="form-control" name="code" id="manualQrCode" placeholder="Enter or scan QR / item code" value="<?php echo e($normalizedCode ?? request('code')); ?>">
            <button class="btn-primaryx" type="submit"><i class="bi bi-search"></i> Find Asset</button>
        </form>
    </div>

    <div class="scanner-result">
        <div class="module-head mb-3">
            <div>
                <h2 class="module-title">Scan Result</h2>
                <div class="module-note">Matched system record based on QR value</div>
            </div>
        </div>
        <?php if($selectedItem): ?>
            <div class="mb-2 d-flex gap-2 flex-wrap">
                <span class="code-badge"><?php echo e($selectedItem->item_type); ?></span>
                <?php if($verificationStatus === 'matched'): ?>
                    <span class="status approved">Verified</span>
                <?php elseif($verificationStatus === 'mismatch'): ?>
                    <span class="status low">Location Mismatch</span>
                <?php elseif($verificationStatus === 'no-room'): ?>
                    <span class="status pending">No Room Assigned</span>
                <?php endif; ?>
            </div>
            <h4 class="mb-1"><?php echo e($selectedItem->name); ?></h4>
            <div class="tiny mb-2"><?php echo e($selectedItem->item_code); ?> · <?php echo e($selectedItem->category->name ?? 'Uncategorized'); ?></div>
            <p class="tiny mb-3"><?php echo e($selectedItem->description ?: 'No description available.'); ?></p>
            <div class="row g-2 mb-3">
                <div class="col-6"><div class="report-stat"><div class="tiny-2">Available Qty</div><div class="fw-bold"><?php echo e($selectedItem->quantity); ?></div></div></div>
                <div class="col-6"><div class="report-stat"><div class="tiny-2">Asset Tag ID</div><div class="fw-bold"><?php echo e($selectedItem->asset_tag_id); ?></div></div></div>
                <div class="col-6"><div class="report-stat"><div class="tiny-2">Date Acquired</div><div class="fw-bold"><?php echo e($selectedItem->date_acquired_label); ?></div></div></div>
                <div class="col-6"><div class="report-stat"><div class="tiny-2">Department</div><div class="fw-bold"><?php echo e($selectedItem->department_label); ?></div></div></div>
                <div class="col-6"><div class="report-stat"><div class="tiny-2">Asset Type</div><div class="fw-bold"><?php echo e($selectedItem->asset_type_label); ?></div></div></div>
                <div class="col-12"><div class="report-stat"><div class="tiny-2">QR Value</div><div class="fw-bold"><?php echo e($selectedItem->qr_value ?: $selectedItem->item_code); ?></div></div></div>
                <div class="col-12"><div class="report-stat"><div class="tiny-2">Assigned Room</div><div class="fw-bold"><?php echo e($selectedItem->room_assigned ?: 'Not assigned'); ?></div></div></div>
            </div>

            <div class="surface p-3 mb-3" style="border:1px solid #e5e7eb">
                <div class="module-head mb-2">
                    <div>
                        <h2 class="module-title" style="font-size:16px">Asset Location Verification</h2>
                        <div class="module-note">Check whether the scanned asset is currently in its assigned room.</div>
                    </div>
                </div>
                <form method="GET" action="<?php echo e(route('qr.index')); ?>" class="d-flex gap-2 flex-wrap align-items-end">
                    <input type="hidden" name="code" value="<?php echo e($normalizedCode ?? request('code')); ?>">
                    <div style="flex:1;min-width:220px">
                        <label class="form-label">Current Room / Location</label>
                        <input type="text" class="form-control" name="verify_room" value="<?php echo e($verifiedRoom); ?>" placeholder="e.g. Room 719">
                    </div>
                    <button class="btn-primaryx" type="submit"><i class="bi bi-check2-circle"></i> Verify Location</button>
                </form>
                <?php if($verificationMessage): ?>
                    <div class="mt-3 alert <?php echo e($verificationStatus === 'matched' ? 'alert-success' : ($verificationStatus === 'mismatch' ? 'alert-danger' : 'alert-warning')); ?> mb-0">
                        <?php echo e($verificationMessage); ?>

                        <?php if($verificationStatus === 'mismatch'): ?>
                            <div class="tiny mt-1">Assigned room: <strong><?php echo e($selectedItem->room_assigned); ?></strong> · Provided room: <strong><?php echo e($verifiedRoom); ?></strong></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <a target="_blank" href="<?php echo e(route('qr.show', $selectedItem)); ?>" class="btn-soft"><i class="bi bi-qr-code"></i> View / Print QR</a>
                <a href="<?php echo e(route('items.show', $selectedItem)); ?>" class="btn-primaryx"><i class="bi bi-box-seam"></i> View Asset</a>
                <a href="<?php echo e(route('requisitions.create', ['item_id' => $selectedItem->id])); ?>" class="btn-approve"><i class="bi bi-file-earmark-plus"></i> Create Request</a>
            </div>
        <?php elseif(request('code')): ?>
            <div class="empty-state">No asset matched the scanned code <strong><?php echo e($normalizedCode ?? request('code')); ?></strong>.</div>
        <?php else: ?>
            <div class="empty-state">Start the scanner or enter a QR code to preview the linked asset record.</div>
        <?php endif; ?>
    </div>
</div>

<div class="qr-card">
    <div class="module-head mb-3">
        <div>
            <h2 class="module-title">CAPEX QR Directory</h2>
            <div class="module-note">Quick-access QR labels for existing assets</div>
        </div>
    </div>
    <div class="qr-tiles">
        <?php $__currentLoopData = $capexItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="qr-tile text-start">
            <div class="d-flex justify-content-between align-items-start gap-2">
                <div>
                    <div style="font-weight:700;font-size:12px"><?php echo e($item->name); ?></div>
                    <div class="tiny-2"><?php echo e($item->item_code); ?></div>
                    <div class="tiny mt-1">Room: <?php echo e($item->room_assigned ?: 'Not assigned'); ?></div>
                </div>
                <span class="status available">Active</span>
            </div>
            <div class="mt-3 d-flex gap-2 flex-wrap">
                <a target="_blank" href="<?php echo e(route('qr.show', $item)); ?>" class="btn-soft small-btn"><i class="bi bi-box-arrow-up-right"></i> View / Print QR</a>
                <a href="<?php echo e(route('qr.index', ['code' => $item->qr_value ?: $item->item_code])); ?>" class="btn-primaryx small-btn"><i class="bi bi-search"></i> Preview</a>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
function onScanSuccess(decodedText) {
    const input = document.getElementById('manualQrCode');
    input.value = decodedText;
    const form = input.closest('form');
    if (form) {
        form.submit();
    }
}
if (window.innerWidth > 767 && document.getElementById('reader')) {
    const qrScanner = new Html5QrcodeScanner('reader', { fps: 10, qrbox: 220 }, false);
    qrScanner.render(onScanSuccess, function(){});
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'QR Scanner'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/claude/work2/capex-opex/resources/views/qr/index.blade.php ENDPATH**/ ?>