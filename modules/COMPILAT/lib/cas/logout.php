<?php

// import phpCAS lib
include_once('CAS.php');

if(get_conf('using_CAS')==TRUE)
	{
	phpCAS::setDebug();
	// initialize phpCAS
	if(get_conf('version_CAS')==1)
		{
		phpCAS::client(CAS_VERSION_1_0,get_conf('host_CAS'),get_conf('port_CAS'),get_conf('uri_CAS'));
		}
	else
		{
		phpCAS::client(CAS_VERSION_2_0,get_conf('host_CAS'),get_conf('port_CAS'),get_conf('uri_CAS'));
		}
	// no SSL validation for the CAS server
	phpCAS::setNoCasServerValidation();
	// force CAS authentication
	phpCAS::logout();
	}
?>
