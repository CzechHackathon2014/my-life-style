<?php

namespace Test\Model;

use Nette,
	Tester,
	Tester\Assert,
	Aprila\Model\UserManager;

$container = require __DIR__ . '/../bootstrap.php';


class UsersTest extends Tester\TestCase
{
	private $container;

	/**
	 * @var \Nette\Database\Context
	 */
	private $db;


	function __construct(Nette\DI\Container $container)
	{
		$this->container = $container;
		$this->db = $this->container->getByType('Nette\Database\Context');
	}


	function setUp()
	{
		Tester\Environment::lock('db', __DIR__ . '/../temp');
		Nette\Database\Helpers::loadFromFile($this->db->getConnection(), __DIR__ . '/../initialization.sql');
	}


	function testAllActiveUsers()
	{
		$users = new UserManager($this->db);
		$allUsers = $users->findAll();

		Assert::same(count($allUsers), 7);
	}


	function testAddNewUser()
	{
		$users = new UserManager($this->db);

		$newUser = array(
			'username' => 'lapiduch',
			'password' => 'heslo',
			'email' => 'honza@test.aprila.cz',
			'role' => 'admin',
			'branchId' => 1
		);
		$person = $users->add($newUser);

		Assert::type('Nette\Database\Table\ActiveRow', $person);

		$allUsers = $users->findAll();
		Assert::same(count($allUsers), 8);

		$user = $users->getBy(array('email' => 'honza@test.aprila.cz'));
		Assert::same($user->email, $newUser['email']);

		// duplicate email
		Assert::exception(function () use ($users) {
			$newUser = array(
				'username' => 'lapiduch',
				'password' => 'heslo',
				'email' => 'honza@test.aprila.cz',
				'role' => 'admin',
				'branchId' => 1
			);
			$users->add($newUser);

		}, 'Aprila\DuplicateEntryException', '');

		// auth
		Assert::exception(function () use ($users) {
			$users->authenticate(array('lapiduch2', 'kreslo'));

		}, 'Nette\Security\AuthenticationException', 'The username is incorrect.');

		Assert::exception(function () use ($users) {
			$users->authenticate(array('lapiduch', 'kreslo2'));

		}, 'Nette\Security\AuthenticationException', 'The password is incorrect.');

		$identity = $users->authenticate(array('lapiduch', 'heslo'));

		Assert::type('Nette\Security\Identity', $identity);
		Assert::same($identity->getRoles(), array('admin'));
		$data = $identity->getData();
		Assert::same($data['username'], 'lapiduch');

		// deactivate
		Assert::count(2, $users->findAllDeactivated());
		$users->deactivateUser($data['id']);
		$allUsers = $users->findAll();
		Assert::same(count($allUsers), 7);

		Assert::exception(function () use ($users) {
			$users->authenticate(array('lapiduch', 'heslo'));

		}, 'Nette\Security\AuthenticationException', 'The username is incorrect.');

		Assert::count(3, $users->findAllDeactivated());
		// activate user
		$users->activateUser($data['id']);
		Assert::count(2, $users->findAllDeactivated());

	}


	function testChangePassword()
	{
		$users = new UserManager($this->db);

		$identity = $users->authenticate(array('architect', 'kreslo'));

		$userData = $identity->getData();

		Assert::type('Nette\Security\Identity', $identity);

		Assert::exception(function () use ($users, $userData) {
			$users->changeUserPassword('heslo', 'nove', $userData['id']);

		}, 'Aprila\InvalidArgumentException', 'The origin password is incorrect');

		$status = $users->changeUserPassword('kreslo', 'nove', $userData['id']);

		Assert::same(TRUE, $status);

		$identity = $users->authenticate(array('architect', 'nove'));
		Assert::type('Nette\Security\Identity', $identity);

	}


	function testFindMethods()
	{
		$userManager = new UserManager($this->db);

		$user = $userManager->getBy(array('email' => 'architect@test.aprila.cz'));
		Assert::truthy($user);
		Assert::same('architect@test.aprila.cz', $user->email);

		$user = $userManager->getBy(array('email' => 'architect@test.aprila.cz', 'role' => 'root'));
		Assert::truthy($user);
		Assert::same('architect@test.aprila.cz', $user->email);

		$users = $userManager->findBy(array('role' => 'admin', 'active' => '1'));
		Assert::truthy($users);
		Assert::count(2, $users);

		$users = $userManager->findFulltext('aprila');
		Assert::truthy($users);
		Assert::count(7, $users);

		$users = $userManager->findFulltext('er');
		Assert::truthy($users);
		Assert::count(2, $users);

		$users = $userManager->findFulltext('chuck norris');
		Assert::falsey($users);
	}

}


$test = new UsersTest($container);
$test->run();