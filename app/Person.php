<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $table = 'people';

    protected $fillable = [
        "firstName",
        "lastName",
        "documentNumber",
        "city",        
        "country",
        "street", 
        "number", 
        "single" 
    ];

    protected $hidden = [
        "created_at", 
        "updated_at"
    ];
}
