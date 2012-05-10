<?php
// Generator for en_US language file
if (php_sapi_name() !== 'cli') {
	die("This script is only intended for CLI usage.\n");
}


$directory = isset($argv[1]) ? $argv[1] : '..';

$messages = array();

foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {
	if ($file->isFile()) {
		$filename = $file->getPathName();
		if (strpos($file, '.php') !== FALSE) {
			$filenameInserted = false;
			$file = token_get_all(file_get_contents($filename));
			$tokens = array();
			// Process the file to remove comments and whitespace
			foreach ($file as $id => $token) {
				if (is_string($token) || $token[0] !== T_WHITESPACE && $token[0] !== T_COMMENT) {
					$tokens[] = $token;
				}
			}
			// Now search for __() calls
			foreach ($tokens as $id => $token) {
				// __() declaration
				if (is_array($token) && $token[1] === '__' && $tokens[$id + 1] === '(') {
					if ($tokens[$id + 2][0] === T_CONSTANT_ENCAPSED_STRING && ($tokens[$id + 3] === ')' || $tokens[$id + 3] === ',')) {
						// eval() removes all useless PHP baggage
						$message = eval("return {$tokens[$id + 2][1]};");
						if (!isset($messages[$message])) {
							if (!$filenameInserted) {
								echo "# $filename\n";
								$filenameInserted = true;
							}
							echo $message, "\n\n";
						}
						// Hash lookups are fast, so why not abuse this structure?
						$messages[$message] = true;
					}
					elseif ($tokens[$id - 1][0] !== T_FUNCTION) {
						$line = isset($tokens[$id + 2][2]) ? $tokens[$id + 2][2] : $token[2];
						die("The __() call in $filename at line $line is not constant value\n");
					}
				}
			}
		}
	}
}