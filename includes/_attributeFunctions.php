<?php
function renderAttribute($attribute, $multiple = false, $suffix = "")
{
	switch($attribute->AttributeTypeID)
	{
		case 1: // Checkbox
			renderCheckField($attribute, $multiple, $suffix);
			break;
		case 2: // Textfält
			renderTextField($attribute, $multiple, $suffix);
			break;
		case 3: // Nummerfält
			renderNumberField($attribute, $multiple, $suffix);
			break;
		case 4: // Flervärdesfält
			//renderTextField($attribute, $multiple, $suffix);
			break;
		case 5: // Dropdownlista
			renderSelectField($attribute, $multiple, $suffix);
			break;
		case 6: // Anteckningsfält
			renderTextAreaField($attribute, $multiple, $suffix);
			break;
		case 7: // Datumfält
			//renderDateField($attribute, $multiple, $suffix);
			break;
		case 8: // HTML
			//renderTextAreaField($attribute, $multiple, $suffix);
			break;
		case 9: // Checkboxlista
			//renderCheckboxListField($attribute, $multiple, $suffix);
			break;
		case 10: // Pinkod
			break;
		default:
			renderDebugAttributeInfo($attribute);
			break;
	}
}

function renderCheckField($attribute, $multiple, $suffix)
{
	echo "<label><div class=\"inputLabel\">";
	echo $attribute->AttributeDescription;
	echo "</div><div class=\"inputHolder\">";
	echo "<input type=\"checkbox\" name=\"edu-attr_" . $attribute->AttributeID . ($suffix != "" ? "-" . $suffix : "") . ($multiple ? "[]" : "") . "\" />";
	echo "</div></label>";
}

function renderTextField($attribute, $multiple, $suffix)
{
	echo "<label><div class=\"inputLabel\">";
	echo $attribute->AttributeDescription;
	echo "</div><div class=\"inputHolder\">";
	echo "<input type=\"text\" name=\"edu-attr_" . $attribute->AttributeID . ($suffix != "" ? "-" . $suffix : "") . ($multiple ? "[]" : "") . "\" value=\"" . $attribute->AttributeValue . "\" />";
	echo "</div></label>";
}

function renderNumberField($attribute, $multiple, $suffix)
{
	echo "<label><div class=\"inputLabel\">";
	echo $attribute->AttributeDescription;
	echo "</div><div class=\"inputHolder\">";
	echo "<input type=\"number\" name=\"edu-attr_" . $attribute->AttributeID . ($suffix != "" ? "-" . $suffix : "") . ($multiple ? "[]" : "") . "\" value=\"" . $attribute->AttributeValue . "\" />";
	echo "</div></label>";
}

function renderDateField($attribute, $multiple, $suffix)
{
	echo "<label><div class=\"inputLabel\">";
	echo $attribute->AttributeDescription;
	echo "</div><div class=\"inputHolder\">";
	echo "<input type=\"date\" name=\"edu-attr_" . $attribute->AttributeID . ($suffix != "" ? "-" . $suffix : "") . ($multiple ? "[]" : "") . "\" />";
	echo "</div></label>";
}

function renderTextAreaField($attribute, $multiple, $suffix)
{
	echo "<label><div class=\"inputLabel\">";
	echo $attribute->AttributeDescription;
	echo "</div><div class=\"inputHolder\">";
	echo "<textarea name=\"edu-attr_" . $attribute->AttributeID . ($suffix != "" ? "-" . $suffix : "") . ($multiple ? "[]" : "") . "\" rows=\"3\" resizable=\"resizable\">" . $attribute->AttributeValue . "</textarea>";
	echo "</div></label>";
}

function renderSelectField($attribute, $multiple, $suffix)
{
	echo "<label><div class=\"inputLabel\">";
	echo $attribute->AttributeDescription;
	echo "</div><div class=\"inputHolder\">";
	echo "<select name=\"edu-attr_" . $attribute->AttributeID . ($suffix != "" ? "-" . $suffix : "") . ($multiple ? "[]" : "") . "\">\n";
	foreach($attribute->AttributeAlternative as $val)
	{
		echo "\t<option value=\"" . $val->AttributeAlternativeID . "\">" . $val->AttributeAlternativeDescription . "</option>\n";
	}
	echo "</select>";
	echo "</div></label>";
}

function renderCheckboxListField($attribute, $multiple, $suffix)
{
	echo "<div class=\"inputLabel\">";
	echo $attribute->AttributeDescription;
	echo "</div><div class=\"inputHolder\">";
	foreach($attribute->AttributeAlternative as $val)
	{
		echo "\t<label><input type=\"checkbox\" name=\"edu-attr_" . $attribute->AttributeID . ($suffix != "" ? "-" . $suffix : "") . ($multiple ? "[]" : "") . "\" value=\"" . $val->AttributeAlternativeID . "\">" . $val->AttributeAlternativeDescription . "</label>\n";
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