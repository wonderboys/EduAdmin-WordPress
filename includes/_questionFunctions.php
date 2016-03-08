<?php
// QuestionTypeID 5
function renderNoteQuestion ($question)
{
	echo "<h3>" . $question[0]->QuestionText . "</h3>";
	echo "<div class=\"inputHolder\">";
	echo "<textarea name=\"question_" . $question[0]->AnswerID . "_note\" resizable=\"resizable\" class=\"questionNoteField\" rows=\"3\"></textarea>";
	echo "</div>";
}

// QuestionTypeID 2
function renderCheckBoxQuestion($question)
{
	echo "<h3>" . $question[0]->QuestionText . "</h3>";
	foreach($question as $q)
	{
		echo "<label>";
		echo "<div class=\"inputHolder\">";
		echo "<input type=\"checkbox\" class=\"questionCheck\" name=\"question_" . $q->QuestionID . "_check\"" . ($q->DefaultAlternative == 1 ? " checked=\"checked\"" : "") . " value=\"" . $q->AnswerID . "\" /> ";
		echo $q->AnswerText;
		echo "</div>";
		echo "</label>";
	}
}

// QuestionTypeID 8
function renderDateQuestion($question)
{
	echo "<label>";
	echo "<div class=\"inputLabel noHide\">";
	echo $question[0]->QuestionText;
	echo "</div>";
	echo "<div class=\"inputHolder\">";
	echo "<input type=\"date\" class=\"questionDate\" name=\"question_" . $question[0]->AnswerID . "_date\" />";
	if($question[0]->Time == 1)
	{
		echo "<input type=\"time\" class=\"questionTime\" name=\"question_" . $question[0]->AnswerID . "_time\" />";
	}
	echo "</div>";
	echo "</label>";
}

// QuestionTypeID 11
function renderDropListQuestion($question)
{
	echo "<label>";
	echo "<div class=\"inputLabel noHide\">";
	echo $question[0]->QuestionText;
	echo "</div>";
	echo "<div class=\"inputHolder\">";
	echo "<select class=\"questionDropdown\" name=\"question_" . $question[0]->QuestionID . "_dropdown\">";
	foreach($question as $q)
	{
		echo "<option value=\"" . $q->AnswerID . "\"" . ($q->DefaultAlternative == 1 ? " selected=\"selected\"" : "") . ">" . $q->AnswerText . "</option>";
	}
	echo "</select>";
	echo "</div>";
	echo "</label>";
}

function renderNumberQuestion($question)
{
	echo "<label>";
	echo "<div class=\"inputLabel noHide\">";
	echo $question[0]->QuestionText;
	echo "</div>";
	echo "<div class=\"inputHolder\">";
	echo "<input type=\"number\" class=\"questionText\" min=\"0\" name=\"question_" . $question[0]->AnswerID . "_number\" placeholder=\"" . edu__("Quantity") . "\" />";
	echo "</div>";
	echo "</label>";
}

function renderInfoText($question)
{
	if(trim($question[0]->AnswerText) != "")
	{
		echo "<h3>" . $question[0]->QuestionText . "</h3>";
		echo "<div class=\"questionInfoText\">";
		echo $question[0]->AnswerText;
		echo "</div>";
	}
	// Hittade inget sätt att fylla i info-text-fält för ett tillfälle.
}

function renderRadioQuestion($question, $display)
{
	echo "<h3>" . $question[0]->QuestionText . "</h3>";
	if($display == 'vertical')
	{
		foreach($question as $q)
		{
			echo "<label class=\"questionRadioVertical\">";
			echo "<div class=\"inputHolder\">";
			echo "<input type=\"radio\" class=\"questionRadio\" name=\"question_" . $question[0]->QuestionID . "_radio\" value=\"" . $q->AnswerID . "\" /> ";
			echo $q->AnswerText;
			echo "</div>";
			echo "</label>";
		}
	}
	else if($display == 'horizontal')
	{
		foreach($question as $q)
		{
			echo "<label class=\"questionRadioHorizontal\">";
			echo "<div class=\"inputHolder\">";
			echo "<input type=\"radio\" class=\"questionRadio\" name=\"question_" . $question[0]->QuestionID . "_radio\" value=\"" . $q->AnswerID . "\" /> ";
			echo $q->AnswerText;
			echo "</div>";
			echo "</label>";
		}
	}
	else
	{
		// Not supposed to happen.. But ok.
	}
}

// QuestionTypeID 1
function renderTextQuestion($question)
{
	echo "<label>";
	echo "<div class=\"inputLabel noHide\">";
	echo $question[0]->QuestionText;
	echo "</div>";
	echo "<div class=\"inputHolder\">";
	echo "<input type=\"text\" class=\"questionText\" name=\"question_" . $question[0]->AnswerID . "_text\" />";
	echo "</div>";
	echo "</label>";
}
?>