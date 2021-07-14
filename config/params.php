<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
	'NavisionUsername'=> env('NAV_USER'),
    'NavisionPassword'=> env('NAV_PASSWORD'),


    'server'=> env('NAV_SERVER'),
    'WebServicePort'=> env('NAV_PORT'),
    'ServerInstance'=> env('NAV_INSTANCE'),
    'CompanyName'=> env('NAV_COMPANY'),
    'DBCompanyName' => env('NAV_DB_COMPANY'),
    'ldPrefix'=>'francis',//ACTIVE DIRECTORY prefix
    'adServer' => 'DC2SVR.AASCIENCES.AC.KE', //Active directory domain co
	
	'codeUnits' => [
	],
	'ServiceName' => [
		'VendorList' => 'VendorList', // 27
		'SupplierCard' => 'SupplierCard', //26
		'Chart_of_Accounts' => 'Chart_of_Accounts', //16
		'GL_Account' => 'GL_Account',//17
		'ChartOfAccount' => 'ChartOfAccount', //65012
		'Imprest_Profits' => 'Imprest_Profits'
	]
];
