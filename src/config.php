<?php


const DB_HOST = 'localhost' ;
const DB_USER = 'ftsroot' ;
const DB_PASS = 'fts.grass.bowl.saw' ;
const DB_SCHEMA = 'premierlocation' ;


// am I going to make a connection here?
// Yes, i rather think I am

$mydb = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_SCHEMA) ;

