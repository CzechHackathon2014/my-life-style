<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace App\AdminModule\Forms;

interface ISignFormFactory
{

	/**
	 * @return SignForm
	 */
	function create();

}