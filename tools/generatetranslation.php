<?php
// Generator for en_US language file
function find_strings($tokens, $filename) {
	global $messages;
	
	$filenameInserted = false;
	// Now search for __() calls
	foreach ($tokens as $id => $token)
	{
		// __() declaration
		if (is_array($token) && $token[1] === '__' && $tokens[$id + 1] === '(')
		{
			if ($tokens[$id + 2][0] === T_CONSTANT_ENCAPSED_STRING && ($tokens[$id + 3] === ')' || $tokens[$id + 3] === ','))
			{
				if (!isset($messages[$tokens[$id + 2][1]]))
				{
					if (!$filenameInserted) {
						echo "\n// $filename\n";
						$filenameInserted = true;
					}
					echo $tokens[$id + 2][1], " => '',\n";
				}
				// Hash lookups are fast, so why not abuse this structure?
				$messages[$tokens[$id + 2][1]] = true;
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

require 'lib/recursivetokenizer.php';
echo "<?php\n\$languagePack = array(\n";

recurse('find_strings');

echo ");\n";
