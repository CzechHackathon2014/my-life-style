<?php

namespace Test\Model;

use Nette,
	Tester,
	Tester\Assert,
	App\Model\ReferenceManager;

$container = require __DIR__ . '/../bootstrap.php';


class ReferenceTest extends Tester\TestCase
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


	function testAllReferences()
	{
		$referenceManager = new ReferenceManager($this->db);
		$allReferences = $referenceManager->findAll();

		Assert::same(count($allReferences), 10);
	}


	function testAddNewReference()
	{
		$referenceManager = new ReferenceManager($this->db);

		$new = array(
			'onlyLogo' => 0,
			'name' => 'Honza Cerny',
			'company' => 'Kreativni Laborator',
			'content' => 'Lorem ipsum dolor sit amet',
			'stars' => 3
		);

		$reference = $referenceManager->addReference($new['onlyLogo'], $new['name'], $new['company'], $new['content'], $new['stars']);

		Assert::truthy($reference);
		Assert::type('Nette\Database\Table\ActiveRow', $reference);
		Assert::same($reference->company, 'Kreativni Laborator');

		$allReferences = $referenceManager->findAll();
		Assert::same(count($allReferences), 11);
	}

}


$test = new ReferenceTest($container);
$test->run();