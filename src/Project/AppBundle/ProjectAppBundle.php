<?php

namespace Project\AppBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ProjectAppBundle extends Bundle
{
	public function getParent()
	{
		return 'FOSUserBundle';
	}
}
