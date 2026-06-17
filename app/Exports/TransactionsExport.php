<?php

namespace App\Exports;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithCustomStartCell, WithEvents, WithTitle
{
    protected array $filters;

    protected array $summary;

    protected User $user;

    public function __construct(array $filters = [], array $summary = [], ?User $user = null)
    {
        $this->filters = $filters;
        $this->summary = $summary;
        $this->user = $user ?? auth()->user();
    }

    public function title(): string
    {
        return 'Financial Report';
    }

    public function startCell(): string
    {
        return 'A10';
    }

    public function collection(): Collection
    {
        return $this->buildQuery()->orderBy('date', 'desc')->get();
    }

    public function headings(): array
    {
        return ['Date', 'Description', 'Category', 'Type', 'Amount (USD)'];
    }

    public function map($row): array
    {
        return [
            $row->date->format('M d, Y'),
            $row->description ?? '—',
            $row->category->name,
            ucfirst($row->type),
            (float) $row->amount,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $year = $this->filters['year'] ?? now()->year;
                $lastRow = $sheet->getHighestRow();

                $sheet->setCellValue('A1', 'FinPulse — Financial Report');
                $sheet->mergeCells('A1:E1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => '312E81']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);

                $sheet->setCellValue('A2', 'Prepared for: ' . $this->user->name . ' (' . $this->user->email . ')');
                $sheet->mergeCells('A2:E2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => '64748B']],
                ]);

                $sheet->setCellValue('A3', 'Report period: Calendar year ' . $year);
                $sheet->setCellValue('A4', 'Generated: ' . now()->format('F j, Y \a\t g:i A'));
                $sheet->mergeCells('A3:E3');
                $sheet->mergeCells('A4:E4');
                $sheet->getStyle('A3:A4')->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => '64748B']],
                ]);

                $sheet->setCellValue('A6', 'Total Income');
                $sheet->setCellValue('C6', 'Total Expenses');
                $sheet->setCellValue('E6', 'Net Balance');
                $sheet->setCellValue('A7', $this->summary['income']);
                $sheet->setCellValue('C7', $this->summary['expense']);
                $sheet->setCellValue('E7', $this->summary['balance']);

                $sheet->getStyle('A6,E6,C6')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '475569']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
                ]);
                $sheet->getStyle('A7,C7,E7')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                ]);
                $sheet->getStyle('A7')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                $sheet->getStyle('C7')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                $sheet->getStyle('E7')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

                $sheet->getStyle('A10:E10')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                if ($lastRow > 10) {
                    $sheet->getStyle('A11:E' . $lastRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']],
                        ],
                    ]);
                    $sheet->getStyle('E11:E' . $lastRow)
                        ->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                }

                foreach (range('A', 'E') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                $sheet->setAutoFilter('A10:E' . $lastRow);
                $sheet->freezePane('A11');
            },
        ];
    }

    protected function buildQuery()
    {
        $query = Transaction::with('category')
            ->where('user_id', $this->user->id);

        if (! empty($this->filters['year'])) {
            $query->whereYear('date', $this->filters['year']);
        }
        if (! empty($this->filters['type'])) {
            $query->where('type', $this->filters['type']);
        }
        if (! empty($this->filters['date_from'])) {
            $query->whereDate('date', '>=', $this->filters['date_from']);
        }
        if (! empty($this->filters['date_to'])) {
            $query->whereDate('date', '<=', $this->filters['date_to']);
        }

        return $query;
    }
}
