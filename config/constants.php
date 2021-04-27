<?php

namespace config;

class Constants
{
	public static $ID_ROLE_USER  		= 2;
	public static $ID_ROLE_PETANI		= 4;
	
	public static $DEFAULT_STATUS		= -1;
	
    public static $COURSE_ROLE			= 1;
	public static $COURSE_ROLE_PETANI	= 2;
	
	public static  $BANNED_STATUS 		= -2;
	public static  $PENDING_STATUS 		= -1;
	public static  $DELETE_STATUS 		= 0;
	public static  $ACTIVE_STATUS 		= 1;
	public static  $BEST_STATUS 		= 2;
	public static  $INACTIVE_STATUS		= 3;

	public static  $TRANSACTION_FINISHED	= 700;
	public static  $DISTANCE_THRESHOLD		= 1.2875536481; 

	public static $apiSDM_trig_key 	= "token";
	public static $apiSDM_key 		= "c5267b114e81119ca108d2ff64e64309";
	
	public static $apiEVIS_trig_key = "token_evisum";
	public static $apiEVIS_key 		= "351fcbb4364c0c84ed7905ee38710a77";
	
	public static $simsdm_url_login = "https://sdmapi.bkkbn.go.id/index.php/LoginController/authPegawaiLite";
	public static $evism_url_plkb	= "http://evisum.carakageoinformatika.com/mobile/index.php/ApiController/pesertaPLKB";
	public static $evism_url_pkb	= "http://evisum.carakageoinformatika.com/mobile/index.php/ApiController/pesertaPKB";
	public static $evism_url_event	= "http://evisum.carakageoinformatika.com/mobile/index.php/ApiController/kegiatanMekop";
	
	public static $username 		= "username";
	public static $password 		= "pwd";
	public static $tokenhit 		= "tokenhit";
	
	public static $EVIS_START 	= "start";
	public static $EVIS_END 	= "end";

	//API BIMA SYSTEM
	public static $BIMA_BASEURL 		= 'http://localhost:8080/';
	public static $BIMA_API_BASEURL 	= 'http://localhost:8080/api/';
	public static $BIMA_TOKEN 			= 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiOTMyMTlhNjE2NDYwMWJmYmY2OGJjOWY0OTExZjIwYTQyOTg3MjZiMjRmYzFkZjQ0MTcyNGY1MzU4ZTU3MWUwZTFmY2VjZmFiZjkwYWVlNjIiLCJpYXQiOjE2MDY5ODcxOTgsIm5iZiI6MTYwNjk4NzE5OCwiZXhwIjoxNjM4NTIzMTk4LCJzdWIiOiI3Iiwic2NvcGVzIjpbXX0.BhHE3W8BDvOT73Q-KAS6MKQdYj9wOL2m2yLkTvGJ8N6DJ67h15c5qEr4pAAbx4BHib44suwUPt-yuvIUsYSAVsjPMD461uZ3EF-fKtsWnl_IwNJRKxIFP1y2p0fmLiyBlbT_VKoeBBxDhrFR6a4aD2xXhW5CfqqFeVN6-1BBgsLotpr5ntkYlPJjvmA51R2bcDp2yG42sivh7C5VIZI4pgvBlreCWu1GAs_qYPX7D_bASld7wvbLuBeF9KW7xtQmQkPZBMsGbUGU8FjSXQiAznlCFLZGmwp04SqOtv1ux2CndKkJDyFdxP_hu7x6S_82x7APerOho9NgWy8u9OUof-rmpXhmEF2CrbnESHt-l9BcYmV3NUlmE19kesXbkKL2l81JDWECbA_RggFC5Xmj-DvqlFFX1HHhKSYjvb63IQ8rsjrU7xUAarnNWvOglqyiYdr-PwfvHy2_-gViasulO1h4E0BpEQ4Z-WY8huaon3ACjMOXVS38Gbm3PEUuHv89DX_sYx7OQ_ttxDEpl7SvMrt91ToXrU3twnt1AEtqgkSzSbR-GAgkflmuqXqjavAmZdQ_QKzHQHc3MkGbkPR6ZH_7IZkkWYWaiWZQUqiU9g1A7pWgIo0pNJGksAKVhKord0jMDCjs16AI5k8Uq_5zDkk2O-e8JXDTmCkT2bgM7XA';

	//API BIMA SYSTEM
	public static $LEARNING_BASEURL 		= 'https://elearning.bkkbn.go.id/';
	public static $LEARNING_API_BASEURL 	= 'https://elearning.bkkbn.go.id/';
	public static $LEARNING_TOKEN 			= '#';
}
?>