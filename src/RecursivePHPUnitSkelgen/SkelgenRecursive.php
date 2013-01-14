<?php
namespace RecursivePHPUnitSkelgen;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use stdClass;
use ShellStyle;


class SkelgenRecursive extends Command
{
	const POSTFIX   = 'Test';
	const GENERATOR = 'phpunit-skelgen';

	protected $input     = null;
	protected $output    = null;
	protected $bootstrap = null;

	protected function configure()
	{
		$this
			->setName('make')
			->setDescription('Generate tests using phpunit-skelgen recursively.')
			->addArgument(
				'from',
				InputArgument::REQUIRED,
				'Source classes destination.'
			)
			->addArgument(
				'to',
				InputArgument::REQUIRED,
				'Test classes destination'
			)
			->addArgument(
				'bootstrap',
				InputArgument::OPTIONAL,
				'Bootstrap file for tests'
			)
			->addOption(
				'xml',
				'x',
				InputOption::VALUE_NONE,
				'If set, will generate a phpunit.xml file in source directory (not implemented yet)'
			)
		;
	}

	/**
	 *
	 */
	public function execute(InputInterface $input, OutputInterface $output)
	{
		$from      = new Directory($input->getArgument('from'));
		$to        = new Directory($input->getArgument('to'));

		if ($bootstrap = $input->getArgument('bootstrap')) {
			$bootstrap = new File($bootstrap);
			$bootstrap = '--bootstrap '.$bootstrap->getPath();
		} else {
			$bootstrap = '';
		}

		$me = $this;
		$files = $from->getFiles(function(File $file) use ($me) {
			return ($file->getExtension() == 'php' and !is_null($me->getClass($file)));
		});

		/* @var $file File */
		foreach ($files as $file) {
			$offset = str_replace($from->getPath(), '', $file->getDir()->getPath());

			if (!empty($offset)) {
				$target = $to->getPath().$offset;
				if (!is_dir($target)) {
					Directory::recursiveMake($to, $target, $output);
				}
			} else {
				$target = $to->getPath();
			}

			$classObj  = $this->getClass($file);

			$unitFile  = $file->getPath();
			$unitClass = $classObj->namespace.'\\'.$classObj->class;
			$testFile  = $target.'/'.$classObj->class.self::POSTFIX.'.php';
			$testClass = $classObj->class.self::POSTFIX;

			$command = sprintf("phpunit-skelgen %s --test -- \"%s\" %s %s %s", $bootstrap, $unitClass, $unitFile, $testClass, $testFile);

			$process = new Process($command);
			$process->run();

			if ($process->isSuccessful()) {
				$output->writeln("<info>Successfully created test: $testFile</info>");
			} else {
				$output->writeln("<error>Can not create test: $testFile</error>");
				$output->writeln("<error>\t{$process->getErrorOutput()}</error>");
			}
		}
	}

	/**
	 * @param File $file
	 * @return stdClass
	 */
	public function getClass(File $file)
	{
		$path      = $file->getPath();
		$class     = '';
		$namespace = array();

		if (is_file($path)) {
			$tokens = token_get_all(file_get_contents($path));
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
		}

		$answer = new stdClass();
		$answer->class     = $class;
		$answer->namespace = implode('\\', $namespace);

		return $answer;
	}
}
