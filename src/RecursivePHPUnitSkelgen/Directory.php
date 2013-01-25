<?php
namespace RecursivePHPUnitSkelgen;

use RecursivePHPUnitSkelgen\Exception\ApplicationException;
use Closure;

class Directory extends FSEntry
{
	public function __construct($entry)
	{
		parent::__construct($entry);

		if (!is_dir($this->path)) {
			throw new ApplicationException("Its not a directory - $entry");
		}
	}

	public function getListing()
	{
		$handle = opendir($this->path);

		$files = array();

		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != "..") {
				$file = $this->path.'/'.$entry;
				if (is_file($file)) {
					switch (File::extension($file)) {
						case 'php' :
							$files[] = new FilePHP($file);
							break;
						default:
							$files[] = new File($file);
							break;
					}
				} else if (is_dir($file)) {
					$files[] = new Directory($file);
				}
			}
		}

		closedir($handle);

		return $files;
	}

	public function getFiles(Closure $filter = null)
	{
		$recursion = function($files, Closure $recursion) {
			$justFiles = array();

			foreach ($files as $entry) {
				if ($entry instanceof Directory) {
					$justFiles = array_merge(
						$justFiles,
						call_user_func_array($recursion, array($entry->getListing(), $recursion))
					);
				} else {
					/* @var $file File */
					$justFiles[$entry->getName()] = $entry;
				}
			}

			return $justFiles;
		};

		$files = call_user_func_array($recursion, array($this->getListing(), $recursion));

		if (!is_null($filter)) {
			$files = array_filter($files, $filter);
		}

		return $files;
	}

	/**
	 * @param Directory $directory
	 * @return mixed|null
	 * @throws Exception\ApplicationException
	 */
	public function diff(Directory $directory)
	{
		if (!strstr($directory->getPath(), $this->getPath())) {
			throw new ApplicationException("Given path does not contain given base : '{$directory->getPath()}' not in '{$this->getPath()}'");
		}

		$offset = str_replace($this->getPath(), '', $directory->getPath());

		return empty($offset) ? null : $offset;
	}

	/**
	 * @param $directory
	 * @return Directory
	 */
	public function appendPath($directory)
	{
		return new Directory($this->mkdirRecursive($directory));
	}

	/**
	 * @param $path
	 * @return string
	 * @throws Exception\ApplicationException
	 */
	public function mkdirRecursive($path)
	{
		$pathList = explode('/', $path);

		$curPath = $this->getPath();

		foreach ($pathList as $path) {
			if (!empty($path)) {
				$curPath.= '/'.$path;
				if (!is_dir($curPath)) {
					$this->makeDir($curPath);
				}
			}
		}

		return $curPath;
	}

	/**
	 * @param $path
	 * @throws Exception\ApplicationException
	 */
	public function makeDir($path)
	{
		if (!@mkdir($path)) {
			throw new ApplicationException("Can not create directory '$path'");
		}
	}

	public function __toString()
	{
		return $this->getPath();
	}
}
