<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body    { font-family: sans-serif; font-size: 12px; color: #1f2937; }
    h1      { font-size: 20px; margin-bottom: 4px; }
    .sub    { color: #6b7280; font-size: 11px; margin-bottom: 24px; }
    .cards  { display: flex; gap: 16px; margin-bottom: 24px; }
    .card   { flex: 1; padding: 12px 16px; border-radius: 8px; }
    .green  { background: #D1FAE5; color: #065F46; }
    .red    { background: #FEE2E2; color: #991B1B; }
    .blue   { background: #DBEAFE; color: #1E40AF; }
    table   { width: 100%; border-collapse: collapse; }
    th      { background: #F8FAFC; text-align: left; padding: 8px 12px; border-bottom: 2px solid #E2E8F0; font-size: 11px; text-transform: uppercase; }
    td      { padding: 8px 12px; border-bottom: 1px solid #F1F5F9; }
    .income  { background: #D1FAE5; color: #065F46; padding: 2px 8px; border-radius: 999px; font-size: 10px; }
    .expense { background: #FEE2E2; color: #991B1B; padding: 2px 8px; border-radius: 999px; font-size: 10px; }
</style>
</head>
<body>
    <h1>Financial Report</h1>
    <p class="sub">Generated on {{ now()->format('F j, Y') }}</p>

    <div class="cards">
        <div class="card green"><strong>Total Income</strong><br>${{ number_format($summary['income'], 2) }}</div>
        <div class="card red"><strong>Total Expense</strong><br>${{ number_format($summary['expense'], 2) }}</div>
        <div class="card blue"><strong>Balance</strong><br>${{ number_format($summary['balance'], 2) }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Category</th>
                <th>Type</th>
                <th style="text-align:right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $t)
            <tr>
                <td>{{ $t->date->format('Y-m-d') }}</td>
                <td>{{ $t->description ?? '—' }}</td>
                <td>{{ $t->category->name }}</td>
                <td><span class="{{ $t->type }}">{{ $t->type }}</span></td>
                <td style="text-align:right">${{ number_format($t->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
