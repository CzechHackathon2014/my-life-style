<?php

namespace Test\Model;

use Nette,
	Tester,
	Tester\Assert;

$container = require __DIR__ . '/../bootstrap.php';


class DaysTest extends Tester\TestCase
{
	private $container;

	/**
	 * @var \App\Model\DayManager
	 */
	private $dayManager;


	function __construct(Nette\DI\Container $container)
	{
		$this->container = $container;
		$this->dayManager = $this->container->getByType('App\Model\DayManager');
	}


	function setUp()
	{
		Tester\Environment::lock('db', __DIR__ . '/../temp');
		Nette\Database\Helpers::loadFromFile($this->db->getConnection(), __DIR__ . '/../initialization.sql');
	}


	function testAllDaysForUser()
	{
		Assert::true(TRUE);
	}


}


$test = new DaysTest($container);
$test->run();