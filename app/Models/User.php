<?php
namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Models\Province;
use App\Models\District;
use App\Models\Regency;
use App\Models\Village;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 
        'last_name', 
        'birth_place', 
        'birth_date', 
        'username', 
        'email', 
        'password', 
        'provider', 
        'provider_id', 
        'phone', 
        'address_street', 
        'province_id', 
        'regency_id', 
        'district_id', 
        'village_id', 
        'reg_number',
        'email_verified_at',
        'age', 
        'gender', 
        'type', 
        'status', 
        'jenjang_pkb', 
        'job', 
        'photo'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'provider_name', 'provider_id'
    ];

    public function instructor()
    {
        return $this->hasOne('App\Models\Instructor', 'user_id', 'id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
    
    /**
     * @param string|array $roles
     */
    public function authorizeRoles($roles)
    {
        if (is_array($roles)) {
            return $this->hasAnyRole($roles) ||
                abort(401, 'This action is unauthorized.');
        }
        return $this->hasRole($roles) ||
            abort(401, 'This action is unauthorized.');
    }
    /**
     * Check multiple roles
     * @param array $roles
     */
    public function hasAnyRole($roles)
    {
        return null !== $this->roles()->whereIn('name', $roles)->first();
    }
    /**
     * Check one role
     * @param string $role
     */
    public function hasRole($role)
    {
        return null !== $this->roles()->where('name', $role)->first();
    }
    
    public function getRole()
    {
        return $this->roles()->where('user_id', $this->id )->first()['name'];
    }

    public function getProvince()
    {
        if($this->hasRole('adminprovinsi')){
            return ucfirst(strtolower(Province::where('id', $this->province_id )->first()['name'])) ;
        }else if($this->hasRole('admindaerah')){
            return ucfirst(strtolower(Regency::where('id', $this->regency_id )->first()['name'])) ;
        }else if($this->hasRole('superadmin') or $this->hasRole('admin')){
            return 'Pusat';
        }
    }

    public function RoleUser()
    {
        return $this->hasMany('App\Models\RoleUser', 'user_id', 'id');
    }

    public function getFullAddress()
    {
        $province = Province::where('id', $this->province_id)->first()['name'];
        return $province != '' ?
        $this->address_street.", ".
        Village::where(['id' => $this->village_id])->first()['name'].", ".
        Regency::where('id', $this->regency_id)->first()['name'].", ".
        District::where('id', $this->district_id)->first()['name'].", ".
        $province : 'Belum Diatur';
    }
}