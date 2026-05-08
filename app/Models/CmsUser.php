<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsUser extends Model
{
    protected $table = 'cms_users';

    protected $fillable = [
        'email',
        'nama',
        'password',
        'image',
    ];

    protected $hidden = [
        'password',
    ];
}