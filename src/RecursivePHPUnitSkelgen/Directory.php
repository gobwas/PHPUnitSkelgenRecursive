<?php
namespace RecursivePHPUnitSkelgen;
use RecursivePHPUnitSkelgen\Exception\ApplicationException;
use Symfony\Component\Console\Output\OutputInterface;
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
					$files[] = new File($file, $this);
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
			$filterApplier = function(File $file) use ($filter) {
				return call_user_func_array($filter, array($file));
			};
			return array_filter($files, $filterApplier);
		}

		return $files;
	}

	public static function recursiveMake(Directory $base, $path, OutputInterface $output)
	{
		if (strstr($path, $base->getPath())) {
			$pathList = explode('/', str_replace($base->getPath(), '', $path));
			$curPath = $base->getPath().'/';
			foreach ($pathList as $path) {
				if (!empty($path)) {
					$curPath.= $path.'/';
					if (!is_dir($curPath)) {
						if (@mkdir($curPath)) {
							$output->writeln("<comment>Successfully created directory $curPath</comment>");
						} else {
							$output->writeln("<error>Can not create directory $curPath</error>");
						}
					}
				}
			}
		} else {
			$output->writeln("<error>Given path does not contain given base : $path ({$base->getPath()})</error>");
		}
	}
}
