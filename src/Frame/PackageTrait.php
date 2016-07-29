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

/**
 * If you want to utilize composer packages
 * as plugins/extensions/addons you can adopt
 * this trait
 *
 * @vendor   Cradle
 * @package  Frame
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
trait PackageTrait
{	
	use ResolverTrait;

	/**
	 * @var array $packages A safe place to store package junk
	 */
	protected $packages = array();

	/**
	 * @var string $bootstrapFile A file to call on when a package is registered
	 */
	protected $bootstrapFile = '.cradle';
	
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
	 * @return PackageTrait
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
	 * @return PackageTrait
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
		$file = $root . $vendor . '/' . $this->bootstrapFile;

		if(file_exists($file)) {
			//so you can access cradle 
			//within the included file
			$cradle = $this;
			include_once($file);
		}
		
		return $this;
	}
	
	/**
	 * Returns a package space
	 *
	 * @param *string $file A file to call on when a package is registered
	 *
	 * @return PackageTrait
	 */
	public function setBootstrapFile($file)
	{
		$this->bootstrapFile = $file;
		
		return $this;
	}
}