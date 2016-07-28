<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\Frame;

use Cradle\Event\PipeTrait;
use Cradle\Resolver\ResolverTrait;

/**
 *
 * @vendor   Cradle
 * @package  Frame
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
trait FrameTrait
{	
	use PipeTrait, ResolverTrait;

	/**
	 * @var array $packages A safe place to store package junk
	 */
	protected $packages = array();
	
	/**
	 * Setups dispatcher and global package
	 */
	public function __construct()
	{
		$this->packages['global'] = $this->resolve(Package::class);
	}
	
	/**
	 * Returns a package space
	 *
	 * @param *string $vendor The vendor/package name
	 *
	 * @return FrameService
	 */
	public function package($vendor)
	{
		if(!isset($this->packages[$vendor])) {
			throw FrameException::forPackageNotFound($vendor);
		}
		
		return $this->packages[$vendor];
	}
	
	/**
	 * Registers and initializes a plugin
	 *
	 * @param *string $vendor The vendor/package name
	 *
	 * @return FrameService
	 */
	public function register($vendor)
	{
		//create a space
		$this->packages[$vendor] = $this->resolve(Package::class);
		
		//luckily we know where we are in vendor folder :)
		//is there a better recommended way?
		$root = __DIR__ . '/../../../../';
		
		if(strpos($vendor, '/') === 0) {
			$root .= '../';
		}
		
		//we should check for events
		$file = $root . $vendor . '/.cradle';

		if(file_exists($file)) {
			//so you can access cradle 
			//within the included file
			$cradle = $this;
			include_once($file);
		}
		
		return $this;
	}
}