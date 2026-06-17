<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    * { box-sizing: border-box; }
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 10px;
        color: #1e293b;
        margin: 0;
        padding: 24px 28px;
    }
    .header {
        border-bottom: 3px solid #4f46e5;
        padding-bottom: 14px;
        margin-bottom: 18px;
    }
    .brand {
        font-size: 22px;
        font-weight: bold;
        color: #312e81;
        margin: 0 0 4px 0;
    }
    .subtitle {
        font-size: 11px;
        color: #64748b;
        margin: 0;
    }
    .meta {
        width: 100%;
        margin-bottom: 16px;
    }
    .meta td {
        padding: 3px 0;
        vertical-align: top;
    }
    .meta-label {
        color: #64748b;
        width: 120px;
        font-weight: bold;
    }
    .summary {
        width: 100%;
        margin-bottom: 20px;
        border-collapse: separate;
        border-spacing: 8px 0;
    }
    .summary td {
        width: 33.33%;
        padding: 12px 14px;
        border-radius: 8px;
        text-align: center;
    }
    .summary-label {
        font-size: 9px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: bold;
        margin-bottom: 4px;
    }
    .summary-value {
        font-size: 16px;
        font-weight: bold;
    }
    .card-income  { background: #ecfdf5; color: #047857; }
    .card-expense { background: #fef2f2; color: #b91c1c; }
    .card-balance { background: #eef2ff; color: #4338ca; }
    .section-title {
        font-size: 12px;
        font-weight: bold;
        color: #334155;
        margin: 0 0 8px 0;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    table.data {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 16px;
    }
    table.data th {
        background: #4f46e5;
        color: #ffffff;
        text-align: left;
        padding: 8px 10px;
        font-size: 9px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    table.data th.amount { text-align: right; }
    table.data td {
        padding: 7px 10px;
        border-bottom: 1px solid #e2e8f0;
    }
    table.data tr:nth-child(even) td { background: #f8fafc; }
    table.data td.amount { text-align: right; font-weight: bold; }
    .badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 8px;
        font-weight: bold;
        text-transform: uppercase;
    }
    .badge-income  { background: #d1fae5; color: #065f46; }
    .badge-expense { background: #fee2e2; color: #991b1b; }
    .footer {
        position: fixed;
        bottom: 16px;
        left: 28px;
        right: 28px;
        border-top: 1px solid #e2e8f0;
        padding-top: 8px;
        font-size: 8px;
        color: #94a3b8;
    }
    .footer-left { float: left; }
    .footer-right { float: right; }
</style>
</head>
<body>
    <div class="header">
        <p class="brand">FinPulse Financial Report</p>
        <p class="subtitle">Professional transaction summary &amp; analysis</p>
    </div>

    <table class="meta">
        <tr>
            <td class="meta-label">Prepared for</td>
            <td><?php echo e($user->name); ?> &lt;<?php echo e($user->email); ?>&gt;</td>
        </tr>
        <tr>
            <td class="meta-label">Report period</td>
            <td>
                <?php if(!empty($filters['year'])): ?>
                    Calendar year <?php echo e($filters['year']); ?>

                <?php else: ?>
                    All transactions on record
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td class="meta-label">Generated</td>
            <td><?php echo e($generatedAt->format('F j, Y \a\t g:i A')); ?></td>
        </tr>
        <tr>
            <td class="meta-label">Total records</td>
            <td><?php echo e($transactions->count()); ?> transactions</td>
        </tr>
    </table>

    <table class="summary">
        <tr>
            <td class="card-income">
                <div class="summary-label">Total Income</div>
                <div class="summary-value">$<?php echo e(number_format($summary['income'], 2)); ?></div>
            </td>
            <td class="card-expense">
                <div class="summary-label">Total Expenses</div>
                <div class="summary-value">$<?php echo e(number_format($summary['expense'], 2)); ?></div>
            </td>
            <td class="card-balance">
                <div class="summary-label">Net Balance</div>
                <div class="summary-value">$<?php echo e(number_format($summary['balance'], 2)); ?></div>
            </td>
        </tr>
    </table>

    <p class="section-title">Transaction Detail</p>
    <table class="data">
        <thead>
            <tr>
                <th style="width:12%">Date</th>
                <th style="width:28%">Description</th>
                <th style="width:18%">Category</th>
                <th style="width:12%">Type</th>
                <th class="amount" style="width:15%">Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($t->date->format('M d, Y')); ?></td>
                <td><?php echo e($t->description ?? '—'); ?></td>
                <td><?php echo e($t->category->name); ?></td>
                <td><span class="badge badge-<?php echo e($t->type); ?>"><?php echo e(ucfirst($t->type)); ?></span></td>
                <td class="amount">$<?php echo e(number_format($t->amount, 2)); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="5" style="text-align:center; padding: 20px; color:#64748b;">
                    No transactions found for the selected period.
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <span class="footer-left">FinPulse &mdash; Confidential financial report</span>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_text(680, 555, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 8, [0.58, 0.64, 0.69]);
        }
    </script>
</body>
</html>
<?php /**PATH E:\Projects\Financial Tracker\resources\views/exports/transactions-pdf.blade.php ENDPATH**/ ?>