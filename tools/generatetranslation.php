<?php
// Generator for en_US language file
function find_strings($tokens, $filename)
{
	global $messages, $languagePack;
	
	$filenameInserted = false;
	// Now search for __() calls
	foreach ($tokens as $id => $token)
	{
		// __() declaration
		if (is_array($token) && $token[1] === '__' && $tokens[$id + 1] === '(')
		{
			if ($tokens[$id + 2][0] === T_CONSTANT_ENCAPSED_STRING && ($tokens[$id + 3] === ')' || $tokens[$id + 3] === ','))
			{
				$thetoken = $tokens[$id + 2][1];
				eval('$string = '.$thetoken.';');
				if (!isset($messages[$string]))
				{
					if (!$filenameInserted)
					{
						echo "\n// $filename\n";
						$filenameInserted = true;
					}
					
					$translation = "";
					if(isset($languagePack[$string]))
						$translation = $languagePack[$string];

					echo var_export($string, true), ' => ', var_export($translation, true), "\n";
				}
				// Hash lookups are fast, so why not abuse this structure?
				$messages[$string] = true;
			}
			elseif ($tokens[$id - 1][0] !== T_FUNCTION)
			{
				$line = isset($tokens[$id + 2][2]) ? $tokens[$id + 2][2] : $token[2];
				die("The __() call in $filename at line $line is not constant value\n");
			}
		}
	}
}

$messages = array();

if(isset($argv[1]))
{
	require "../lib/lang/${argv[1]}_lang.php";
}
else
	$languagePack = array();
	
require 'lib/recursivetokenizer.php';
echo "<?php\n\$languagePack = array(\n";

recurse('find_strings');

$textWritten = false;
foreach($languagePack as $original => $translated)
{
	if(!isset($messages[$original]))
	{
		if(!$textWritten)
			echo "\n// Strings no longer used\n";
		$textWritten = true;
		echo var_export($original, true), ' => ', var_export($translated, true), "\n";
	}
}

echo ");\n";
