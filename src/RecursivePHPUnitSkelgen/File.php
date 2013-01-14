<?php
namespace RecursivePHPUnitSkelgen;
use RecursivePHPUnitSkelgen\Exception\ApplicationException;

class File extends FSEntry
{
	/**
	 * @var null|Directory
	 */
	protected $dir = null;

	public function __construct($entry, Directory $dir = null)
	{
		parent::__construct($entry);
		if (!is_file($this->path)) {
			throw new ApplicationException("Its not a file - $entry");
		}
		$this->dir = $dir;
	}

	public function getExtension()
	{
		return substr($this->name, strrpos($this->name, '.') + 1);
	}

	public function getDir()
	{
		return $this->dir;
	}
}
