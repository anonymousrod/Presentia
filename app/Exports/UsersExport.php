<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class UsersExport implements FromQuery, WithHeadings, WithMapping, WithCustomCsvSettings
{
    use Exportable;

    protected Builder $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';',
            'use_bom' => true,
        ];
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nom',
            'Prénom',
            'Email',
            'Téléphone',
            'Statut',
            'Date de naissance',
            'Inscrit le',
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->first_name,
            $user->email,
            $user->phone,
            $user->status ? $user->status->value : '',
            $user->birth_date ? $user->birth_date->format('d/m/Y') : '',
            $user->created_at ? $user->created_at->format('d/m/Y H:i') : '',
        ];
    }
}
