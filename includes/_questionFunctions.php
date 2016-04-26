<?php
// Render ALL the types
function renderQuestion($question)
{
	switch($question[0]->QuestionTypeID)
	{
		case 1: // Text-fråga
			renderTextQuestion($question);
			break;
		case 2: // Checkbox-fråga
			renderCheckBoxQuestion($question);
			break;
		case 3: // Radio - Vertikal
			renderRadioQuestion($question, 'vertical');
			break;
		case 4: // Nummerfråga
			renderNumberQuestion($question);
			break;
		case 5: // Anteckningar
			renderNoteQuestion($question);
			break;
		case 6: // Infotext - hel rad
			renderInfoText($question);
			break;
		case 8: // Datum-fråga
			renderDateQuestion($question);
			break;
		case 9: // Infotext - halv rad
			renderInfoText($question);
			break;
		case 10: // Radio - horisontell
			renderRadioQuestion($question, 'horizontal');
			break;
		case 11: // Droplist-fråga
			renderDropListQuestion($question);
			break;
		default:
			echo "<xmp>" . print_r($question, true) . "</xmp>";
		break;
	}
}

// QuestionTypeID 5
function renderNoteQuestion ($question)
{
	echo "<h3>" . $question[0]->QuestionText . ($question[0]->Price > 0 ? " <i>(" . convertToMoney($question[0]->Price) . ")</i>" : "") . "</h3>";
	echo "<div class=\"inputHolder\">";
	echo "<textarea name=\"question_" . $question[0]->AnswerID . "_note\" data-price=\"" . $question[0]->Price . "\" resizable=\"resizable\" class=\"questionNoteField\" rows=\"3\"></textarea>";
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
		echo "<input type=\"checkbox\" class=\"questionCheck\" data-price=\"" . $q->Price . "\" name=\"question_" . $q->QuestionID . "_check\"" . ($q->DefaultAlternative == 1 ? " checked=\"checked\"" : "") . " value=\"" . $q->AnswerID . "\" /> ";
		echo $q->AnswerText;
		if($q->Price > 0) {
			echo " <i>(" . convertToMoney($q->Price) . ")</i>";
		}
		echo "</div>";
		echo "</label>";
	}
}

// QuestionTypeID 8
function renderDateQuestion($question)
{
	echo "<label>";
	echo "<div class=\"inputLabel noHide\">";
	echo $question[0]->QuestionText . ($question[0]->Price > 0 ? " <i>(" . convertToMoney($question[0]->Price) . ")</i>" : "");
	echo "</div>";
	echo "<div class=\"inputHolder\">";
	echo "<input type=\"date\" class=\"questionDate\" data-price=\"" . $question[0]->Price . "\" name=\"question_" . $question[0]->AnswerID . "_date\" />";
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
		echo "<option value=\"" . $q->AnswerID . "\"" . ($q->DefaultAlternative == 1 ? " selected=\"selected\"" : "") . " data-price=\"" . $q->Price . "\">";
		echo $q->AnswerText;
		if($q->Price > 0) {
			echo " (" . convertToMoney($q->Price) . ")";
		}
		echo "</option>";
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
	echo "<input type=\"number\" class=\"questionText\" data-price=\"" . $question[0]->Price . "\" min=\"0\" name=\"question_" . $question[0]->AnswerID . "_number\" placeholder=\"" . edu__("Quantity") . "\" />";
	if($question[0]->Price > 0) {
		echo " <i>(" . sprintf(edu__('%1$s / pcs'), convertToMoney($question[0]->Price)) . ")</i>";
	}
	echo "</div>";
	echo "</label>";
}

function renderInfoText($question)
{
	if(trim($question[0]->AnswerText) != "")
	{
		echo "<h3>" . $question[0]->QuestionText . ($question[0]->Price > 0 ? " <i>(" . convertToMoney($question[0]->Price) . ")</i>" : "") . "</h3>";
		echo "<div class=\"questionInfoText\" data-price=\"" . $question[0]->Price . "\">";
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
			echo "<input type=\"radio\" class=\"questionRadio\" data-price=\"" . $q->Price . "\" name=\"question_" . $question[0]->QuestionID . "_radio\" value=\"" . $q->AnswerID . "\" /> ";
			echo $q->AnswerText;
			if($q->Price > 0) {
				echo " <i>(" . convertToMoney($q->Price) . ")</i>";
			}
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
			echo "<input type=\"radio\" class=\"questionRadio\" data-price=\"" . $q->Price . "\" name=\"question_" . $question[0]->QuestionID . "_radio\" value=\"" . $q->AnswerID . "\" /> ";
			echo $q->AnswerText;
			if($q->Price > 0) {
				echo " <i>(" . convertToMoney($q->Price) . ")</i>";
			}
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
	echo $question[0]->QuestionText . ($question[0]->Price > 0 ? " <i>(" . convertToMoney($question[0]->Price) . ")</i>" : "");
	echo "</div>";
	echo "<div class=\"inputHolder\">";
	echo "<input type=\"text\" data-price=\"" . $question[0]->Price . "\" class=\"questionText\" name=\"question_" . $question[0]->AnswerID . "_text\" />";
	echo "</div>";
	echo "</label>";
}
?>