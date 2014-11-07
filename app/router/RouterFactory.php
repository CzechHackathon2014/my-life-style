<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace App;

use Nette,
	Nette\Application\Routers\RouteList,
	Nette\Application\Routers\Route;


/**
 * Router factory.
 */
class RouterFactory
{

	/**
	 * @return \Nette\Application\IRouter
	 */
	public function createRouter()
	{
		$router = new RouteList();
		// Admin
		$router[] = $adminRouter = new RouteList('Admin');
		$adminRouter[] = new Route('aprila/<presenter>/<action>', 'Dashboard:default');

		// Front
		$router[] = $frontRouter = new RouteList('Front');
		$frontRouter[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');

		return $router;
	}

}
