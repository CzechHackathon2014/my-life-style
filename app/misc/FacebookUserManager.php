<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

class FacebookUserManager extends Nette\Object
{
	/**
	 * @var \Nette\Database\Context
	 */
	protected $database;

	/**
	 * User roles
	 *
	 * @var array
	 */
	protected $roles = array(
		"user" => "user",
	);


	/**
	 * @param \Nette\Database\Context $database
	 */
	public function __construct(\Nette\Database\Context $database)
	{
		$this->database = $database;
	}


	public function findByFacebookId($userFacebookId = '')
	{
		$user = $this->database->table('users')->where('facebook_id', $userFacebookId)->fetch();

		if (!$user){
			return FALSE;
		}

		$userEntity = \Nette\Utils\ArrayHash::from([
			'id' => $user->id,
			'name' => $user->name,
			'username' => $user->username,
			'facebook_id' => $user->facebook_id,
			'email' => $user->email,
			'role' => $user->role,
			'avatar' => $user->avatar,
		]);

		return $userEntity;
	}


	public function registerFromFacebook($userFacebookId, $me)
	{
		$userFacebookId;

		if (!isset($me->email)){
			$email = $userFacebookId.'@user.from.facebook';
		} else {
			$email = $me->email;
		}

		if (!isset($me->name)){
			$name = "Mr. Noname";
		} else {
			$name = $me->name;
		}


		$newUser = array(
			'facebook_id' => $userFacebookId,
			'username' => $email,
			'email' => $email,
			'name' => $name,
			'active' => '1',
			'role' => 'user',
			'avatar' => 'https://graph.facebook.com/'.$userFacebookId.'/picture',
		);

		// try if email is not exists, then assign facebook id to user?
		try {
			$user = $this->database->table('users')->insert($newUser);

		} catch (PDOException $e){
			if ($e->getCode() == '23000'){
				// TODO : zamyslet se zdali je to bezpecne, pripadne se zeptat uzivatele zdali to chce
				$actualUser = $this->database->table('users')->where('email', $me->email)->fetch();

				$actualUser->update(['facebook_id'=> $userFacebookId]);
				if ($actualUser->avatar == ''){
					$actualUser->update(['avatar'=> $newUser['avatar']]);
				}
				if ($actualUser->name == ''){
					$actualUser->update(['name' => $newUser['name']]);
				}

				$user = $actualUser;

			} else {
				throw new $e;
			}

		}

		$userEntity = \Nette\Utils\ArrayHash::from([
			'id' => $user->id,
			'name' => $user->name,
			'username' => $user->username,
			'facebook_id' => $user->facebook_id,
			'email' => $user->email,
			'role' => $user->role,
			'avatar' => $user->avatar,
		]);

		return $userEntity;
	}

}
