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
Route::get('/logout', 'AdminController@signout');
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
Route::get('/admin/timetable','TimetableController@index');
Route::get('/admin/timetable/class/{class_id}/','TimetableController@displayTimetable');
Route::get('/admin/timetable/class/{class_id}/register','TimetableController@registerTimetable');
Route::post('/admin/classes/{class_id}/timetable/registration','TimetableController@registrationTimetable');

Route::get('/admin/timetable/spot','SpotTimetableController@index');
Route::get('/admin/timetable/spot/class/{class_id}/register','SpotTimetableController@registerSpot');
Route::post('/admin/timetable/spot/class/{class_id}/registration','SpotTimetableController@registration');
Route::get('/admin/timetable/spot/class/{class_id}/register','SpotTimetableController@registerSpot');
Route::post('/admin/timetable/spot/delete','SpotTimetableController@delete');

Route::get('/admin/classes/{class_id}/timetable/subject/{sub_id}/teachers','TimetableController@getTeachers');
Route::get('/admin/check/{day}/{time}/{lecture_id}','TimetableController@teacherExistCheck');

//lecture
Route::get('/admin/lecture','LectureController@index');
Route::get('/admin/lecture/class/{class_id}','LectureController@registerLecture');
Route::get('/admin/lecture/class/{class_id}/set','LectureController@displayAllLecture');
Route::post('/admin/classes/{class_id}/lecture/registration','LectureController@registrationLecture');


/*
    API ROUTE
*/

//authnication student
Route::post('/auth','UserController@authnication');
Route::post('/auth/check','UserController@checkAuth');

Route::get('/signout','UserController@signout');

//timetable
Route::get('/timetable/{class_id}','UserController@getTimetable');