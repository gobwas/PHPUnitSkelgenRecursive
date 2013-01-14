<?php
namespace RecursivePHPUnitSkelgen;

class FSEntry
{
	protected $name;

	protected $path;

	public function __construct($entry)
	{
		$this->path = realpath($entry);
		$this->name = basename($entry);
	}

	public function getPath()
	{
		return $this->path;
	}

	public function getName()
	{
		return $this->name;
	}
}
