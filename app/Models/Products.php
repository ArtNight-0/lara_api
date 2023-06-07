<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Nette\Utils\ArrayList;

class Products extends Model
{
    use HasFactory;


    protected $fillable =[
        'name',
        'image',
        'description',
        'type',
        'price',
    ];

}
