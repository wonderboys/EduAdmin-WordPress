<?php
function renderAttribute($attribute, $multiple = false)
{
	return;
	switch($attribute->AttributeTypeID)
	{
		case 1: // Checkbox
			renderCheckField($attribute, $multiple);
			break;
		case 2: // Textfält
			renderTextField($attribute, $multiple);
			break;
		case 3: // Nummerfält
			renderNumberField($attribute, $multiple);
			break;
		case 4: // Flervärdesfält
			//renderTextField($attribute, $multiple);
			break;
		case 5: // Dropdownlista
			renderSelectField($attribute, $multiple);
			break;
		case 6: // Anteckningsfält
			renderTextAreaField($attribute, $multiple);
			break;
		case 7: // Datumfält
			//renderDateField($attribute, $multiple);
			break;
		case 8: // HTML
			//renderTextAreaField($attribute, $multiple);
			break;
		case 9: // Checkboxlista
			//renderCheckboxListField($attribute, $multiple);
			break;
		case 10: // Pinkod
			break;
		default:
			renderDebugAttributeInfo($attribute);
			break;
	}
}

function renderCheckField($attribute, $multiple)
{
	echo "<label><div class=\"inputLabel\">";
	echo $attribute->AttributeDescription;
	echo "</div><div class=\"inputHolder\">";
	echo "<input type=\"checkbox\" name=\"edu-attr_" . $attribute->AttributeID . ($multiple ? "[]" : "") . "\" />";
	echo "</div></label>";
}

function renderTextField($attribute, $multiple)
{
	echo "<label><div class=\"inputLabel\">";
	echo $attribute->AttributeDescription;
	echo "</div><div class=\"inputHolder\">";
	echo "<input type=\"text\" name=\"edu-attr_" . $attribute->AttributeID . ($multiple ? "[]" : "") . "\" value=\"" . $attribute->AttributeValue . "\" />";
	echo "</div></label>";
}

function renderNumberField($attribute, $multiple)
{
	echo "<label><div class=\"inputLabel\">";
	echo $attribute->AttributeDescription;
	echo "</div><div class=\"inputHolder\">";
	echo "<input type=\"number\" name=\"edu-attr_" . $attribute->AttributeID . ($multiple ? "[]" : "") . "\" value=\"" . $attribute->AttributeValue . "\" />";
	echo "</div></label>";
}

function renderDateField($attribute, $multiple)
{
	echo "<label><div class=\"inputLabel\">";
	echo $attribute->AttributeDescription;
	echo "</div><div class=\"inputHolder\">";
	echo "<input type=\"date\" name=\"edu-attr_" . $attribute->AttributeID . ($multiple ? "[]" : "") . "\" />";
	echo "</div></label>";
}

function renderTextAreaField($attribute, $multiple)
{
	echo "<label><div class=\"inputLabel\">";
	echo $attribute->AttributeDescription;
	echo "</div><div class=\"inputHolder\">";
	echo "<textarea name=\"edu-attr_" . $attribute->AttributeID . ($multiple ? "[]" : "") . "\" rows=\"3\" resizable=\"resizable\">" . $attribute->AttributeValue . "</textarea>";
	echo "</div></label>";
}

function renderSelectField($attribute, $multiple)
{
	echo "<label><div class=\"inputLabel\">";
	echo $attribute->AttributeDescription;
	echo "</div><div class=\"inputHolder\">";
	echo "<select name=\"edu-attr_" . $attribute->AttributeID . ($multiple ? "[]" : "") . "\">\n";
	foreach($attribute->AttributeAlternative as $val)
	{
		echo "\t<option value=\"" . $val->AttributeAlternativeID . "\">" . $val->AttributeAlternativeDescription . "</option>\n";
	}
	echo "</select>";
	echo "</div></label>";
}

function renderCheckboxListField($attribute, $multiple)
{
	echo "<div class=\"inputLabel\">";
	echo $attribute->AttributeDescription;
	echo "</div><div class=\"inputHolder\">";
	foreach($attribute->AttributeAlternative as $val)
	{
		echo "\t<label><input type=\"checkbox\" name=\"edu-attr_" . $attribute->AttributeID . ($multiple ? "[]" : "") . "\" value=\"" . $val->AttributeAlternativeID . "\">" . $val->AttributeAlternativeDescription . "</label>\n";
	}
	echo "</div>";
}

function renderDebugAttributeInfo($attribute)
{
	echo "<label><div class=\"inputLabel\">";
	echo $attribute->AttributeDescription;
	echo "</div><div class=\"inputHolder\">";
	echo "<pre>" . print_r($attribute, true) . "</pre>";
	echo "</div></label>";
}