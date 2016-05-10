		<div class="questionPanel">
		<?php
		$ft = new XFiltering();
		$f = new XFilter("ShowOnWeb", "=", 'true');
		$ft->AddItem($f);

		$categories = $api->GetCategory($token, '', $ft->ToString());
		$flatList = Array();

		foreach($categories as $i => $v)
		{
			$flatList[$v->CategoryID] = $v;
		}

		$lineage = array();
		$lineage[] = $cat->CategoryID;
		$cat = $flatList[$cat->ParentID];

		while(true)
		{
			$lineage[] = $cat->CategoryID;

			if($cat->ParentID == 0)
				break;

			$cat = $flatList[$cat->ParentID];
		}

		$ft = new XFiltering();
		$f = new XFilter("ShowExternal", "=", 'true');
		$ft->AddItem($f);
		$f = new XFilter("CategoryID", "in", join(",", $lineage));
		$ft->AddItem($f);
		$st = new XSorting();
		$s = new XSort('SortIndex', 'ASC');
		$st->AddItem($s);
		$objCatQuestion = $api->GetObjectCategoryQuestion($token, $st->ToString(), $ft->ToString());

		$ft = new XFiltering();
		$f = new XFilter("ShowExternal", "=", 'true');
		$ft->AddItem($f);
		$st = new XSorting();
		$s = new XSort('SortIndex', 'ASC');
		$st->AddItem($s);
		$objCatQuestion2 = $api->GetObjectCategoryQuestion($token, $st->ToString(), $ft->ToString());

		$groupedQuestions = array();

		foreach($objCatQuestion as $q)
		{
			$groupedQuestions[$q->QuestionID][] = $q;
		}

		foreach($objCatQuestion2 as $q)
		{
			$groupedQuestions[$q->QuestionID][] = $q;
		}

		if(count($groupedQuestions) > 0)
		{
			$lastQuestionId = -1;
			foreach($groupedQuestions as $question)
			{
				if($lastQuestionId != $question[0]->QuestionID)
				{
					renderQuestion($question);
				}

				$lastQuestionId = $question[0]->QuestionID;
			}
		}

		?>
		</div>