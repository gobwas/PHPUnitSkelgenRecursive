<?php
namespace RecursivePHPUnitSkelgen;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use RecursivePHPUnitSkelgen\Exception\ApplicationException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\DialogHelper;


class SkelgenRecursive extends Command
{
	const NAME        = 'RecursivePHPUnitSkelgen';
	const VERSION     = '0.2.1-dev';

	const POSTFIX   = 'Test';
	const GENERATOR = 'phpunit-skelgen';

	protected $input     = null;
	protected $output    = null;
	protected $bootstrap = null;

	protected $cache = array();

	protected function configure()
	{
		$this
			->setName('make')
			->setDescription('Generate tests skeletons using phpunit-skelgen recursively.')
			->addArgument(
				'from',
				InputArgument::REQUIRED,
				'Source classes destination.'
			)
			->addArgument(
				'to',
				InputArgument::REQUIRED,
				'Test classes destination.'
			)
			->addArgument(
				'bootstrap',
				InputArgument::OPTIONAL,
				'Bootstrap file for tests.'
			)
			->addOption(
				'force',
				'f',
				InputOption::VALUE_NONE,
				'Do not ask of any action (descend directories, create tests).'
			);
			/*->addOption(
				'xml',
				'x',
				InputOption::VALUE_NONE,
				'If set, will generate a phpunit.xml file in source directory (not implemented yet).'
			);*/
	}

	/**
	 *
	 */
	public function execute(InputInterface $input, OutputInterface $output)
	{
		$start = microtime(true);

		$output->writeln(implode(' ', array(self::NAME, self::VERSION)));
		$output->writeln('');

		$output->getFormatter()->setStyle('red', new OutputFormatterStyle('red'));

		$from = new Directory($input->getArgument('from'));
		$to   = new Directory($input->getArgument('to'));

		if ($bootstrap = $input->getArgument('bootstrap')) {
			$bootstrap = new File($bootstrap);
			$bootstrap = '--bootstrap '.$bootstrap->getPath();
		} else {
			$bootstrap = '';
		}

		$files = $from->getFiles(
			function (File $file) {
				if ($file instanceof FilePHP) {
					$class = $file->getClass();
					return !is_null($class);
				}

				return false;
			}
		);

		/* @var $dialog DialogHelper */
		$dialog = $this->getHelperSet()->get('dialog');


		/* @var $file FilePHP */
		foreach ($files as $file) {
			$fileDirectory = (string) $file->getDirectory();

			if (!$input->getOption('force') and !array_key_exists($fileDirectory, $this->cache)) {
				$this->cache[$fileDirectory] = $dialog->askConfirmation($output, "Descend into directory '{$fileDirectory}'? (y/n) ");
			}

			if ($input->getOption('force') or $this->cache[$fileDirectory]) {
				try {
					$target = $to->appendPath($from->diff($file->getDirectory()));
				} catch (ApplicationException $e) {
					$output->writeln("<error>{$e->getMessage()}</error>");
					continue;
				}
			} else {
				continue;
			}


			$unitFile      = $file->getPath();
			$unitClassPath = $file->getClassPath();
			$testClass     = $file->getClass().self::POSTFIX;
			$testFile      = $target->getPath().DIRECTORY_SEPARATOR.$testClass.'.php';

			$command = sprintf("phpunit-skelgen %s --test -- \"%s\" %s %s %s", $bootstrap, $unitClassPath, $unitFile, $testClass, $testFile);

			$question = is_file($testFile) ? "<red>File '$testFile' already exists. Do u want to overwrite it? (y/n) </red>" : "Create test '$testClass' in '$testFile'? (y/n) ";

			if ($input->getOption('force') or $dialog->askConfirmation($output, $question)) {
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

		$time   = sprintf('%0.2f', microtime(true) - $start);
		$memory = sprintf('%0.2f', memory_get_peak_usage(true)/1024/1024);

		$output->writeln("\nTime: $time Seconds, Memory: {$memory} Mb.\n");
	}
}
