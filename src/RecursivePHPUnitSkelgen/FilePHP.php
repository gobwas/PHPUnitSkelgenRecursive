<?php
namespace RecursivePHPUnitSkelgen;
use RecursivePHPUnitSkelgen\Exception\ApplicationException;

class FilePHP extends File
{
	protected $class;
	protected $namespace;
	protected $parsed = false;

	public function __construct($entry, Directory $dir = null)
	{
		parent::__construct($entry, $dir);

		if ($this->extension != 'php') {
			throw new ApplicationException("File '{$this->getName()}' must have a php extension");
		}

		$this->parse();
	}

	protected function parse()
	{
		$class     = null;
		$namespace = array();

		$tokens = token_get_all(file_get_contents($this->getPath()));

		foreach ($tokens as $key => $token) {
			if ($token[0] == T_CLASS) {
				if (isset($tokens[$key + 2])) {
					$class = $tokens[$key + 2];
					$class = is_array($class) ? $class[1] : $class;
				}
			} else if ($token[0] == T_NAMESPACE) {
				for ($x = $key + 1; $x < count($tokens); $x++) {
					if ($tokens[$x][0] === T_STRING) {
						$namespace[] = $tokens[$x][1];
					} else if ($tokens[$x] === '{' || $tokens[$x] === ';') {
						break;
					}
				}
			}
		}

		$this->class     = $class;
		$this->namespace = empty($namespace) ? null : implode('\\', $namespace);
		$this->parsed    = true;
	}

	/**
	 * @return string|null
	 */
	public function getNamespace()
	{
		return $this->namespace;
	}

	/**
	 * @return string|null
	 */
	public function getClass()
	{
		return $this->class;
	}

	public function getClassPath()
	{
		$path = array();

		if (!is_null($this->getNamespace())) {
			$path[] = $this->getNamespace();
		}

		if (!is_null($this->getClass())) {
			$path[] = $this->getClass();
		}

		return empty($path) ? null : implode('\\', $path);
	}
}
