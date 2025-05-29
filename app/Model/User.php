<?php

namespace App\Model;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Authenticatable
{

    use SoftDeletes;

//    protected $primaryKey = 'id';
    protected $table = 'users';
    protected $fillable = [
        'id',
    	'create_by',
    	'company_name',
    	'email',
    	'cellphone',
    	'password',
    	'status',
        'login_status',
        'last_login_time',
        'last_active_time',
    	'role',
        'position',
        'permission',
        'employee_limit',
    ];

    protected $dates = [
        'last_login_time',
        'last_active_time',
    ];


    /*my details*/
    public function userDetail()
    {
        return $this->hasOne(UserDetail::class);
    }

    /*all user who created by me*/
    public function myUsers()
    {
        return $this->hasMany(User::class, 'create_by', 'id');
    }

    /*my sms rates*/
    public function smsRates()
    {
        return $this->hasMany(AccSmsRate::class);
    }

    /*my all sender id*/
    public function senderIds()
    {
        return $this->hasMany(SenderIdUser::class);
    }

    /**/
    public function templates()
    {
        return $this->hasMany(SmsTemplate::class);
    }
    public function allFlexiload()
    {
        return $this->hasMany(LoadCampaign::class);
    }
    public function flexibooks()
    {
        return $this->hasMany(LoadFlexibook::class);
    }

    public function parentInfo()
    {
        return $this->belongsTo(User::class, 'create_by', 'id');
    }

}
