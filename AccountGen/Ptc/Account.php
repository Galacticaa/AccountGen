<?php namespace AccountGen\Ptc;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $domain;

    protected $primaryKey = 'username';

    public $incrementing = false;

    public function __construct($domain = null)
    {
        parent::__construct();

        $this->domain = $domain;

        $this->attributes['country'] = 'GB';

        return $this;
    }

    public function setUsernameAttribute($username)
    {
        $this->attributes['username'] = $username;
        $this->attributes['email'] = $username.'@'.$this->domain;
    }

    public function formatForKinan()
    {
        return implode(';', [
            $this->attributes['username'],
            $this->attributes['email'],
            $this->attributes['password'],
            $this->attributes['birthday'],
            $this->attributes['country']
        ]);
    }

    public function formatForRocketMap()
    {
        return implode(';', [
            'ptc',
            $this->attributes['username'],
            $this->attributes['password'],
        ]);
    }
}
