<?php
function edu__($key)
{
	$phrases = get_option('eduadmin-phrases');
	if(!$phrases)
	{
		$file = file_get_contents(( dirname( __FILE__ ) ) . '/defaultPhrases.json');
		$phrases = json_decode($file, true);
		update_option('eduadmin-phrases', json_encode($phrases));
	}
	else
	{
		$phrases = json_decode($phrases, true);
	}

	if(!array_key_exists($key, $phrases))
	{
		$phrases[$key] = $key;
		update_option('eduadmin-phrases', json_encode($phrases));
	}

	return $phrases[$key];
}

function edu_e($key)
{
	echo edu__($key);
}

function edu_n($single, $plural, $number)
{
	return $number === 1 ? edu__($single) : edu__($plural);
}
?>