<?php
namespace RecursivePHPUnitSkelgen;
use RecursivePHPUnitSkelgen\Exception\ApplicationException;

class File extends FSEntry
{
	/**
	 * @var Directory
	 */
	protected $directory;

	protected $extension;

	public static function extension($file)
	{
		return substr(basename($file), strrpos(basename($file), '.') + 1);
	}

	public function __construct($entry)
	{
		parent::__construct($entry);

		$this->extension = self::extension($this->name);

		if (!is_file($this->path)) {
			throw new ApplicationException("It is not a file '$entry''");
		}

		$this->directory = new Directory(dirname($this->path));
	}

	public function getExtension()
	{
		return $this->extension;
	}

	/**
	 * @return Directory
	 */
	public function getDirectory()
	{
		return $this->directory;
	}

	public function __toString()
	{
		return $this->getPath();
	}
}
