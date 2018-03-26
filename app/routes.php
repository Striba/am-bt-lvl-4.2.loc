<?php

//Путь на главную страницу
Route::get('/', [
    'as'   => 'index',
    'uses' => 'SiteController@index'
]);

//Регистрация пользователя
Route::any('/register', [
    'as' => 'register',
    'uses' => 'SiteController@registration',
]);

//Авторизация пользователя
Route::any('/login', [
    'as' => 'login',
    'uses' => 'SiteController@login',
]);

//Создаем группу маршрутов в которые возможен доступ лишь после аутентификации:
Route::group(['before' => 'auth'], function()
{

//Путь в кабинет зарегистрированного пользователя 'id' == 'id_user':
    Route::get('/cabinet/{id_user}', [
        'as' => 'cabinet',
        'uses' => 'SiteController@cabinet'
    ]);


//Группа маршрутов для администратора:
    Route::group(['prefix' => 'admin'], function (){

        //Переход в кабинет администратора с определенным айди:
        Route::get('/{id}', [
            'as' => 'admin',
            'uses' => 'AdminController@index'
        ]);

        //Добавление задачи менеджеру с айди manager_id
        Route::get('/addtask/{manager_id}', [
            'as' => 'addtask',
            'uses' => 'AdminController@addtask'
        ]);

        //Загрузка файлов с заданиями для менеджеров
        Route::post('/upload/{manager_id}', [
            'as' => 'uptasks',
            'uses' => 'AdminController@uploadtasks'
        ]);

        //Добавить пользователя(передаем айди текущего админа для возврата назад)
        Route::get('/adduser/{id}', [
            'as' => 'adduser',
            'uses' => 'AdminController@adduserform'
        ]);

        //Сохранение добавленного пользователя с айди текущего админа для возврата
        Route::post('/save/{id}', [
            'as' => 'save',
            'uses' => 'AdminController@saveuser'

        ]);

        //Редактирования данных пльзователя
        Route::get('/edituser/{id}/{id_admin}', [
            'as' => 'edituser',
            'uses' => 'AdminController@edituserform'
        ]);

        //Обновление информации пользователя
        Route::post('/update/{id_admin}', [
            'as' => 'update',
            'uses' => 'AdminController@updateuser'
        ]);

        //Удаление пльзователя
        Route::get('/delete/{id}',[
            'as' => 'deleteuser',
            'uses' => 'AdminController@delete'
        ]);


    });

//Переход в кабинет менеджера с определенным айди:
    Route::get('/manager/{id}', [
        'as' => 'manager',
        'uses' => 'ManagerController@index'
    ]);


////Выход из кабинета(через анонимную функцию)
//    Route::get('/logout', function(){
//        Auth::logout();
//        return Redirect::to('/')->with('message', 'Сейчас вы будете перенаправленны на главную страницу');
//    });

//Выходи из кабинета(через соотв. метод контроллера.)
    Route::get('/logout', [
        'as' => 'logout',
        'uses' => 'SiteController@logout'
    ]);


});



