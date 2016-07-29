<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Frame;

use Cradle\Resolver\ResolverTrait;
use Cradle\Event\PipeTrait;

/**
 * Service level handler for micro framework 
 * calls. Handles process using event pipes.
 *
 * @vendor   Cradle
 * @package  Frame
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class FrameService
{	
	use PipeTrait,
		PackageTrait,
		ResolverTrait;
}