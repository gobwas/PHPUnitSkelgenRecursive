<?php
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