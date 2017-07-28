<?php namespace AccountGen;

use Illuminate\Database\Eloquent\Model;

class Instance extends Model
{
    protected $primaryKey = 'name';

    public $incrementing = false;

    public $timestamps = false;

    public function accounts()
    {
        return $this->hasMany('AccountGen\Account', 'instance', 'name');
    }
}
