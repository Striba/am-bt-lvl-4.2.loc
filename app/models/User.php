<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');

    /**
     * Определяем заполняемые аттрибуты
     *
     * @var array
     */
	protected $fillable = array('name','email', 'password', 'role');

	public function signup(){
	    $this->password = Hash::make($this->password);

	    return $this->save();
    }

    //Метод выборки заданий по ключу manager_id для пользователей из таблицы Users с ключем $id
    public function tasks() {
	    return $this->hasMany('Task', 'manager_id');
    }


    /**
     * Get the remember token for the user
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }
    /**
     * Get the password for the user
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the e-mail address where password reminders are sent
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }

}
