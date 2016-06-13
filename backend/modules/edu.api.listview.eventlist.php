<?php
if(!function_exists('edu_api_listview_eventlist'))
{
	function edu_api_listview_eventlist($request)
	{
		header("Content-type: application/json; charset=UTF-8");
		global $eduapi;

		$edutoken = edu_decrypt("edu_js_token_crypto", getallheaders()["Edu-Auth-Token"]);
		$_SESSION['eduadmin-phrases'] = $request['phrases'];

		$sorting = new XSorting();
		$s = new XSort('SubjectName', 'ASC');
		$sorting->AddItem($s);
		$subjects = $eduapi->GetEducationSubject($edutoken, $sorting->ToString(), '');

		$filterCourses = array();

		if(!empty($request['subject']))
		{
			foreach($subjects as $subject)
			{
				if($subject->SubjectName == $request['subject'])
				{
					if(!in_array($subject->ObjectID, $filterCourses))
					{
						$filterCourses[] = $subject->ObjectID;
					}
				}
			}
		}

		$filtering = new XFiltering();
		$f = new XFilter('ShowOnWeb','=','true');
		$filtering->AddItem($f);

		if(isset($request['category']))
		{
			$f = new XFilter('CategoryID', '=', $request['category']);
			$filtering->AddItem($f);
		}

		if(!empty($filterCourses))
		{
			$f = new XFilter('ObjectID', 'IN', join(',', $filterCourses));
			$filtering->AddItem($f);
		}

		if(isset($request['city']))
		{
			$f = new XFilter('LocationID', '=', $request['city']);
			$filtering->AddItem($f);
		}

		$edo = $eduapi->GetEducationObject($edutoken, '', $filtering->ToString());

		$filtering = new XFiltering();
		$f = new XFilter('ShowOnWeb','=','true');
		$filtering->AddItem($f);

		$f = new XFilter('PeriodStart','>',date("Y-m-d 00:00:00", strtotime("now +1 day")));
		$filtering->AddItem($f);
		$f = new XFilter('PeriodEnd', '<', date("Y-m-d 23:59:59", strtotime("now +6 months")));
		$filtering->AddItem($f);
		$f = new XFilter('StatusID','=','1');
		$filtering->AddItem($f);

		$f = new XFilter('LastApplicationDate','>=',date("Y-m-d 00:00:00"));
		$filtering->AddItem($f);

		if(!empty($filterCourses))
		{
			$f = new XFilter('ObjectID', 'IN', join(',', $filterCourses));
			$filtering->AddItem($f);
		}

		if(isset($request['city']))
		{
			$f = new XFilter('LocationID', '=', $request['city']);
			$filtering->AddItem($f);
		}

		if(isset($request['subject-search']))
		{
			$f = new XFilter('SubjectID', '=', $request['subject-search']);
			$filtering->AddItem($f);
		}

		if(isset($request['category']))
		{
			$f = new XFilter('CategoryID', '=', $request['category']);
			$filtering->AddItem($f);
		}

		$sorting = new XSorting();
		$s = new XSort('PeriodStart', 'ASC');
		$sorting->AddItem($s);

		$ede = $eduapi->GetEvent($edutoken, $sorting->ToString(), $filtering->ToString());

		foreach($ede as $object)
		{
			foreach($edo as $course)
			{
				$id = $course->ObjectID;
				if($id == $object->ObjectID)
				{
					$object->Days = $course->Days;
					$object->StartTime = $course->StartTime;
					$object->EndTime = $course->EndTime;
					$object->CategoryID = $course->CategoryID;
					$object->PublicName = $course->PublicName;
					break;
				}
			}
		}

		echo json_encode($ede);
	}
}

if(isset($_REQUEST['module']) && $_REQUEST['module'] == "listview_eventlist")
{
	echo edu_api_listview_eventlist($_REQUEST);
}
?>