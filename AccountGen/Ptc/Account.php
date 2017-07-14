<?php namespace AccountGen\Ptc;

class Account
{
    protected $domain;

    protected $username;

    protected $password;

    protected $email;

    protected $birthday;

    protected $country = 'GB';

    public function __construct($domain = null)
    {
        $this->domain = $domain;

        return $this;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        $this->email = $username.'@'.$this->domain;

        return $this;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    public function setBirthday($dob)
    {
        $this->birthday = $dob;

        return $this;
    }

    public function __toString()
    {
        return implode(';', [
            $this->username,
            $this->email,
            $this->password,
            $this->birthday,
            $this->country
        ]);
    }
}
