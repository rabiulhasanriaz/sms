<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DateFormater extends Model
{
    use HasFactory;
    protected $fillable = ['dateFormat'];
    protected $table = 'date_formaters';
    protected $primaryKey = 'id';
    public $timestamps = true;
}
