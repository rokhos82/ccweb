<?php

$menu = array(

	1 => 	array ( 'text' => 'Home', 'link' => '/' ),
	2 => 	array ( 'text' => 'Government and Administration', 'link' => '/gov-admin'),
	3 => 	array ( 'text' => 'Community', 'link' => '/community' ),
	4 => 	array ( 'text' => 'Announcements', 'link' => '/announcements' , 'parent' => 2  ),

	5 => 	array ( 'text' => 'Links', 'link' => '/links' ),
	6 => 	array ( 'text' => 'Contact', 'link' => '/contact' ),

	7 => 	array ( 'text' => 'Departments', 'link' => '/gov-admin/departments', 'parent' => 2 ),
	8 => 	array ( 'text' => 'Boards', 'link' => '/gov-admin/boards', 'parent' => 2 ),
	9 => 	array ( 'text' => 'General County Business', 'link' => '/gov-admin/business', 'parent' => 2 ),
  10 => 	array ( 'text' => 'General Business', 'link' => '/gov-admin/business/general', 'parent' => 9 ),
	11 => 	array ( 'text' => 'Grants', 'link' => '/gov-admin/grants', 'parent' => 2 ),
	

	12 => 	array ( 'text' => 'Circuit Court', 'link' => '/gov-admin/circuit-court', 'parent' => 7 ),
	13 => 	array ( 'text' => 'County Assessor', 'link' => '/gov-admin/county-assessor', 'parent' => 7 ),
	14 => 	array ( 'text' => 'County Attorney', 'link' => '/gov-admin/county-attorney', 'parent' => 7 ),
	15 => 	array ( 'text' => 'County Clerk', 'link' => '/gov-admin/county-clerk', 'parent' => 7 ),
	16 => 	array ( 'text' => 'County Commissioners', 'link' => '/gov-admin/county-commissioners', 'parent' => 7 ),
	17 => 	array ( 'text' => 'Clerk of District Court', 'link' => '/gov-admin/district-court-clerk', 'parent' => 7 ),
	18 => 	array ( 'text' => 'Emergency Management', 'link' => '/gov-admin/emergency-management', 'parent' => 7 ),
	19 => 	array ( 'text' => 'Extension', 'link' => '/gov-admin/extension', 'parent' => 7 ),
	//20 => 	array ( 'text' => 'GIS Department', 'link' => '/gov-admin/gis', 'parent' => 7 ),
	21 => 	array ( 'text' => 'Public Health', 'link' => '/gov-admin/public-health', 'parent' => 7 ),
	22 => 	array ( 'text' => 'Road &amp; Bridge', 'link' => '/gov-admin/road-and-bridge', 'parent' => 7 ),
	23 => 	array ( 'text' => 'Sheriff', 'link' => 'http://www.conversesheriff.info/', 'parent' => 7 ),
	24 => 	array ( 'text' => 'Treasurer', 'link' => '/gov-admin/treasurer', 'parent' => 7 ),

	25 => 	array ( 'text' => 'Board Info', 'link' => '/gov-admin/boards', 'parent' => 9 ),
	26 => 	array ( 'text' => 'Financial Info', 'link' => '/gov-admin/business/financial-info', 'parent' => 9 ),
	27 => 	array ( 'text' => 'Government Grants, Plus +', 'link' => '/gov-admin/business/government-grants', 'parent' => 9 ),
	28 => 	array ( 'text' => 'Notary Public', 'link' => '/gov-admin/business/notary-public', 'parent' => 9 ),
	29 => 	array ( 'text' => 'Public Hearings and Notices', 'link' => '/gov-admin/business/public-hearing', 'parent' => 2 ),

	30 => 	array ( 'text' => 'General Board Info', 'link' => '/gov-admin/boards/general', 'parent' => 8 ),
	31 => 	array ( 'text' => 'Airport Board', 'link' => '/gov-admin/boards/airport', 'parent' => 8 ),
	//32 => 	array ( 'text' => 'CANDO Board', 'link' => 'http://www.candowyoming.com/boardmembers.htm', 'parent' => 8 ),
	33 => 	array ( 'text' => 'Glenrock Solid Waste Disposal District Board', 'link' => '/gov-admin/boards/glenrock-solid-waste-disposal', 'parent' => 8 ),
	34 => 	array ( 'text' => 'Hospital Board', 'link' => '/gov-admin/boards/hospital', 'parent' => 8 ),
	35 => 	array ( 'text' => 'Library Board', 'link' => '/gov-admin/boards/library', 'parent' => 8 ),
	36 => 	array ( 'text' => 'Natural Resources Planning', 'link' => '/gov-admin/boards/natural-resources-planning', 'parent' => 8 ),
	37 => 	array ( 'text' => 'Parks and Recreation Board', 'link' => '/gov-admin/boards/parks-and-recreation', 'parent' => 8 ),
	38 => 	array ( 'text' => 'Planning and Zoning Commission', 'link' => '/gov-admin/boards/planning-and-zoning', 'parent' => 8 ),
	39 => 	array ( 'text' => 'Predator Management District Board of Directors', 'link' => '/gov-admin/boards/predator-management', 'parent' => 8 ),
	40 => 	array ( 'text' => 'Tourism Board', 'link' => '/gov-admin/boards/tourism', 'parent' => 8 ),
	41 => 	array ( 'text' => 'Weed &amp; Pest Board', 'link' => '/gov-admin/boards/weed-and-pest', 'parent' => 8 ),
	42 => 	array ( 'text' => 'Wyoming State Fair Advisory Board', 'link' => '/gov-admin/boards/wyoming-state-fair-advisory', 'parent' => 8 ),

  43 => 	array ( 'text' => 'Economic Development', 'link' => '/community/economic-development', 'parent' => 3 ),
	44 => 	array ( 'text' => 'Library', 'link' => 'http://www.conversecountylibrary.org/', 'parent' => 3 ),
	45 => 	array ( 'text' => 'History', 'link' => '/community/history', 'parent' => 3 ),
	46 => 	array ( 'text' => 'Recreation', 'link' => '/community/recreation', 'parent' => 3 ),
	47 => 	array ( 'text' => 'Fire Agencies', 'link' => '/community/fire', 'parent' => 3 ),
	48 =>   array ( 'text' => 'Elections', 'link' => '/gov-admin/county-clerk/elections', 'parent' => 2 ),
	49 => 	array ( 'text' => 'Wellness Committee ', 'link' => '/community/wellness', 'parent' => 3 ),
	50 => 	array ( 'text' => 'codeRed', 'link' => 'https://public.coderedweb.com/CNE/492E07D4AAF0', 'parent' => 3 ),
	51 => 	array ( 'text' => 'Converse County Cares', 'link' => '/community/cares', 'parent' => 3 ),

	52 => 	array ( 'text' => 'CANDO', 'link' => 'http://www.candowyoming.com', 'parent' => 43 ),
	53 => 	array ( 'text' => 'Community Builders', 'link' => 'http://www.consultcbi.com/', 'parent' => 43 ),
	54 => 	array ( 'text' => 'U.S. Census Bureau-Fast Facts', 'link' => 'http://fastfacts.census.gov/servlet/CWSFacts?geo_id=05000US56009&amp;_sse=on', 'parent' => 43 ),
	//55 => 	array ( 'text' => 'FYI: Converse County', 'link' => 'http://doe.state.wy.us/lmi/CountyFactSheets/Converse.pdf', 'parent' => 43 ),

  56 => 	array ( 'text' => 'Earth Day 2009', 'link' => '/community/cares/earth-day', 'parent' => 51 ),
 
	58 => 	array ( 'text' => 'Eating', 'link' => '/community/recreation/eating', 'parent' => 46 ),
	59 => 	array ( 'text' => 'Activities', 'link' => '/community/recreation/activities', 'parent' => 46 ),
	60 => 	array ( 'text' => 'Lodging', 'link' => '/community/recreation/lodging', 'parent' => 46 ),
	61 => 	array ( 'text' => 'Calendar', 'link' => '/community/recreation/calendar', 'parent' => 46 ),

	62 => 	array ( 'text' => 'Converse County Rural Fire', 'link' => '/community/fire/rural', 'parent' => 47 ),
	63 => 	array ( 'text' => 'Douglas Fire Department', 'link' => '/community/fire/douglas', 'parent' => 47 ),
	64 => 	array ( 'text' => 'Glenrock Fire Department', 'link' => '/community/fire/glenrock', 'parent' => 47 ),
  
  65 => 	array ( 'text' => 'Employee Benefit Information', 'link' => '/gov-admin/employee-benefit', 'parent' => 2 ),
  66 => 	array ( 'text' => 'Scholarships', 'link' => '/gov-admin/scholarships', 'parent' => 2 ),
  67 => 	array ( 'text' => 'Employment Opportunities', 'link' => '/gov-admin/employment-opportunities', 'parent' => 2 ),
  68 => 	array ( 'text' => 'Converse County Policy Manual', 'link' => '/gov-admin/policy-manual', 'parent' => 2 ),
  
  69 => 	array ( 'text' => 'WY Census 2010', 'link' => '/gov-admin/census-2010.pdf', 'parent' => 2 ),

	//70 => 	array ( 'text' => 'Notices', 'link' => '/gov-admin/business/notices', 'parent' => 9 ),
	
	71 => 	array ( 'text' => 'Other Links', 'link' => '/community/recreation/links', 'parent' => 46 ),
	72 => 	array ( 'text' => 'NEWEDC', 'link' => 'http://www.newedc.com/converse/conversecounty.html', 'parent' => 43 ),

	73 => 	array ( 'text' => 'One Cent Projects', 'link' => '/onecent' ),
	
	74 => 	array ( 'text' => 'General 1¢ Optional Sales Tax Information', 'link' => '/onecent/tax-info', 'parent' => 73 ),
	//75 => 	array ( 'text' => 'Joint Powers Board Minutes and Notices', 'link' => '/onecent/joint-powers-board-minutes-notices' , 'parent' => 73 ),
	75 => 	array ( 'text' => 'Converse County Library (Douglas)', 'link' => '/onecent/library', 'parent' => 73  ),
	76 => 	array ( 'text' => 'Glenrock Branch Library', 'link' => '/onecent/library-glenrock' , 'parent' => 73 ),
	77 => 	array ( 'text' => 'Eastern Wyo. College (Douglas Campus)', 'link' => '/onecent/ewc-douglas' , 'parent' => 73 ),
	
	78 => 	array ( 'text' => 'Special Districts', 'link' => '/gov-admin/special-districts' , 'parent' => 2 ),
	79 =>	array ( 'text' => 'Permits and Regulations', 'link' => '/gov-admin/permit-reg', 'parent' => 2 ),

	80 =>	array ( 'text' => 'Case Types', 'link' => '/gov-admin/district-court-clerk/case-types', 'parent' => 17),
	81 =>	array ( 'text' => 'Child Support', 'link' => '/gov-admin/district-court-clerk/child-support', 'parent' => 17),
	82 =>	array ( 'text' => 'Filing Fees', 'link' => '/gov-admin/district-court-clerk/filing-fees', 'parent' => 17),
	83 =>	array ( 'text' => 'Jury Duty', 'link' => '/gov-admin/district-court-clerk/jury-duty', 'parent' => 17),
	84 =>	array ( 'text' => 'Passports', 'link' => '/gov-admin/district-court-clerk/passports', 'parent' => 17),
	85 =>	array ( 'text' => 'Other Resources', 'link' => '/gov-admin/district-court-clerk/links', 'parent' => 17),
);
