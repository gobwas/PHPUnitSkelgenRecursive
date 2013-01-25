<?php
namespace RecursivePHPUnitSkelgen;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;

class SkelgenRecursiveApplication extends Application
{
	/**
	 * Gets the name of the command based on input.
	 *
	 * @param InputInterface $input The input interface
	 *
	 * @return string The command name
	 */
	protected function getCommandName(InputInterface $input)
	{
		// This should return the name of your command.
		return 'make';
	}

	/**
	 * Gets the default commands that should always be available.
	 *
	 * @return array An array of default Command instances
	 */
	protected function getDefaultCommands()
	{
		// Keep the core default commands to have the HelpCommand
		// which is used when using the --help option
		$defaultCommands = parent::getDefaultCommands();

		$defaultCommands[] = new SkelgenRecursive();

		return $defaultCommands;
	}
}
