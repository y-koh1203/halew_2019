<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


//User
Route::get('/user/{id}', 'UserController@getProfile');
Route::post('/user/register', 'UserController@registerProfile');

//Admin

//admin signin
Route::get('/admin', 'AdminController@signin');
//admin signin authnication
Route::post('/admin/auth', 'AdminController@authnication');

//admin home
Route::get('/admin/home', 'AdminController@displayHomePage');

//admin signup
Route::get('/admin/signup', 'AdminController@signup');
//signup registration
Route::post('/admin/signup/registration', 'AdminController@registration');

//student ragister page
Route::get('/admin/student/register','AdminController@displayStudentRegistrationPage');
//student registration
Route::get('/admin/student/registration','AdminController@studentRegistration');
//Route::get('/admin/class/{id}/students','AdminController@getBelongStudentInClass');

//classes
Route::get('/admin/classes','ClassController@index');
Route::get('/admin/classes/{class_id}','ClassController@getBelongStudentInClass');

//timetable
Route::get('/admin/classes/{class_id}/timetable','TimetableController@displayTimetable');
Route::get('/admin/classes/{class_id}/timetable/register','TimetableController@registerTimetable');
Route::post('/admin/classes/{class_id}/timetable/registration','TimetableController@registrationTimetable');
Route::get('/admin/classes/{class_id}/timetable/subject/{sub_id}/teachers','TimetableController@getTeachers');