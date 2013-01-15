<?php


class Application
{
	protected $options = array();
	protected $arguments = array();
	protected $object = null;
	protected $method = null;
	protected $name;
	protected $description;
	protected $version;
	protected $author;

	public function __construct($name, $object, $method)
	{
		$this->object = $object;
		$this->method = $method;
		$this->name   = $name;
	}

	public function addArgument($name, $required, $description)
	{
		$arg = new stdClass();
		$arg->name        = $name;
		$arg->required    = $required;
		$arg->description = $description;
		$arg->type        = 'arg';

		$this->arguments[] = $arg;
	}

	public function addOption($name, $required, $description)
	{
		$opt = new stdClass();
		$opt->name        = $name;
		$opt->required    = $required;
		$opt->description = $description;
		$opt->type        = 'opt';

		$this->options[] = $opt;
	}

	public function setDescription($description)
	{
		$this->description = $description;
	}

	public function setVersion($version)
	{
		$this->version = $version;
	}

	public function setAuthor($author)
	{
		$this->author = $author;
	}

	public function run($argv)
	{

	}

	public function getUsage()
	{
		$usage = array($this->name);
		$description = array();

		$all = array_merge($this->options, $this->arguments);

		foreach ($all as $rule) {
			$spr = $rule->required ? ($rule->type == 'opt' ? '-%s' : '%s') : ($rule->type == 'opt' ? '[-%s]' : '[%s]');
			$usage[] = sprintf($spr, $rule->name);
			$description[] = sprintf("\t-%s\t%s (%s)", $rule->name, $rule->description, $rule->required ? 'required' : 'optional');
		}

		echo "\nUsage:\t".(implode(' ', $usage))."\n";
	}
}

$app = new Application('test', 'Class', 'run');
$app->addArgument('arg', true, 'hello');
$app->addOption('opt', true, 'option');
$app->addArgument('argsec', false, 'hello esc');
$app->addOption('optsec', false, 'option sec');

$app->getUsage();
/*$rules = call_user_func(function() {require __DIR__.'/options.php'; return $options;});

// TODO make description class
$usage = "Recursive calling of phpunit-skelgen v0.1.0-alpha by Sergey Kamardin.\n\nUsage:\tphpunit-skelgen-r [OPTIONS]\n\n";
$short = '';
$long  = array();

foreach ($rules as $rule) {
	$short.= $rule['short'].($rule['required'] ? ':' : '::');
	$usage.= "\t".'-'.$rule['short'].', --'.$rule['long']."\t\t".$rule['description']." ".($rule['required'] ? '(required)' : '(optional)')."\n";
	$long[] = $rule['long'].($rule['required'] ? ':' : '::');
}

$usage.= "\n";

$options = getopt($short, $long);
$fetched = array();
foreach ($rules as $key => $rule) {
	$fetched[$key] = isset($options[$rule['short']]) ? $options[$rule['short']] : isset($options[$rule['long']]) ? $options[$rule['long']] : null;
	if ($rule['required'] and is_null($fetched[$key])) {
		echo $usage;
		die;
	}
}

$options = array(
	'from' => array(
		'short' => 'f',
		'long' => 'from',
		'description' => 'Directory from where search php files',
		'required' => true,
	),
	'to' => array(
		'short' => 't',
		'long' => 'to',
		'description' => 'Directory where to write tests',
		'required' => true,
	),
	'bootstrap' => array(
		'short' => 'b',
		'long' => 'bootstrap',
		'description' => 'Bootstrap',
		'required' => false,
	),
);

*/