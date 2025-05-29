<?php

namespace App\Exports;
use App\Model\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    
    public function collection()
    {
        return User::all();
       
    }
}
