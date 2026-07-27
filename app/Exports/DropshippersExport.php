<?php

namespace App\Exports;

use App\Models\Dropshipper;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class DropshippersExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
{
    protected $headings;

    function __construct()
    {
        $this->headings = [
            'name',
            'email',
            'password',
            'user_name',
            'domain_name',
            'phone',
            'address',
            'is_approved',
            'total_deposit',
            'total_credit',
            'total_withdraw',
            'app_key',
            'app_secret',
            'dropshipper_id',
            'package_id',
        ];
    }

    public function collection()
    {
        return Dropshipper::withSum('withdraw', 'amount')->latest()->get();
    }

    public function map($dropshipper): array
    {
        return [
            $this->ensureUtf8($dropshipper->name),
            $this->ensureUtf8($dropshipper->email),
            $dropshipper->password,
            $this->ensureUtf8($dropshipper->user_name),
            $this->ensureUtf8($dropshipper->domain_name),
            $dropshipper->phone,
            $this->ensureUtf8($dropshipper->address),
            $dropshipper->is_approved ? 1 : 0,
            $dropshipper->total_deposit ?? 0,
            $dropshipper->total_credit ?? 0,
            $dropshipper->withdraw_sum_amount ?? 0,
            $dropshipper->app_key ?? '',
            $dropshipper->app_secret ?? '',
            $dropshipper->dropshipper_id ?? '',
            $dropshipper->package_id ?? '',
        ];
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
            'enclosure' => '"',
            'line_ending' => "\n",
            'use_bom' => true,
            'include_separator_line' => false,
            'excel_compatibility' => true,
        ];
    }

    private function ensureUtf8($string)
    {
        if (is_null($string) || $string === '') {
            return $string;
        }

        if (mb_check_encoding($string, 'UTF-8')) {
            return mb_convert_encoding($string, 'UTF-8', 'UTF-8');
        }

        $encodings = ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'ASCII'];

        foreach ($encodings as $encoding) {
            if (mb_check_encoding($string, $encoding)) {
                $converted = mb_convert_encoding($string, 'UTF-8', $encoding);
                if (mb_check_encoding($converted, 'UTF-8')) {
                    return $converted;
                }
            }
        }

        $encoded = mb_convert_encoding($string, 'UTF-8', 'auto');
        return $encoded ?: $string;
    }
}
