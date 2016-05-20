<?php
function edu_LoadPhrases()
{
	$phrases = get_transient('eduadmin-phrases');
	if(!$phrases)
	{
		$phrases = get_option('eduadmin-phrases');
		$file = file_get_contents(( dirname( __FILE__ ) ) . '/defaultPhrases.json');
		$originalPhrases = json_decode($file, true);

		if(!$phrases)
		{
			$phrases = $originalPhrases;
			update_option('eduadmin-phrases', json_encode($phrases));
		}
		else
		{
			$phrases = json_decode($phrases, true);
			foreach($originalPhrases as $ph => $val)
			{
				if(!isset($phrases[$ph]))
				{
					$phrases[$ph] = $val;
				}
			}
		}

		set_transient('eduadmin-phrases', $phrases, DAY_IN_SECONDS);
	}

	return $phrases;
}

function edu__($key)
{
	$phrases = edu_LoadPhrases();

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