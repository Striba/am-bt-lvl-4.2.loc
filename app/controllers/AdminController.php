<?php

class AdminController extends BaseController
{

    /**
     * Метод формирования страницы admin
     *
     * @param $id_user айди пользовалея
     * @return mixed
     */
    public function index($id_user)
    {
        //Получаем данные всех пользователей из таблицы users
        $users = User::get();

        //Получаем данные текущего пользователя
        $userData = DB::table('users')->where('id', $id_user)->first();

        return View::make('admin', [
            'title' => "Администратор ".$userData->name,
            'users' => $users,
            'userData' => $userData
        ]);
    }

    /**
     * Метод формирующий представление добавления заданий-файлов менеджеру
     *
     * @param int $manager_id айди менеджера
     * @return \Illuminate\Contracts\View\View
     */
    public function addtask($manager_id)
    {
        //Получаем имя выбранного менеджера по его айди:
        $managerName = User::find($manager_id)->name;

        return View::make('cabinet.addtask', [
            'title' => 'Добавить задание',
            'managerName' => $managerName,
            'manager_id' => $manager_id
        ]);
    }

    /**
     * Метод загрузки задания на сервер
     *
     * @param $manager_id
     * @return string
     */
    public function uploadtasks($manager_id)
    {

        //Проверка поступил ли файл:
        if (Input::hasFile('file'))
        {
            //Получаем название файла:
            $filename = Input::file('file')->getClientOriginalName();

            //Определяем путь по которому производится сохранение файла на диске:
            $destinationPath = 'tasks/';

            //Перемещение загруженного файла
            Input::file('file')->move($destinationPath, $filename);

            //создаем объект модели Task
            $task = new Task();

            //Передаем значения записываемые в таблицу tasks:
            $task->filename = $filename;
            $task->manager_id = $manager_id;

            //Сохраняем новые данные в таблицу tsks:
            $task->save();

            return "success";
        } else {
            return "wrong";
        }
    }

    /**
     * Метод создания вида добавления пользователя
     *
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function addUserForm ($id)
    {
        $title = "Добавить пользователя";

        return View::make('cabinet.adduser', [
            'title' => $title,
            'id' => $id,
            'errors' => isset($errors) ? $errors : null,
            'success' => isset($success) ? $success : null
        ]);

    }

    /**
     * Сохрянить данные нового пользователя
     *
     * @param int $id - айди текущего админа
     * @return mixed
     */
    public function saveUser ($id)
    {
        //Заносим введенные данные в переменную
        $insert = Input::all();

        //Проверка на существование глобальной переменной $_POST['email']:
        if (Input::has('email'))
        {
            //Присваиваем полученнst значения из формы в переменные:
            $email = $insert['email'];
            $password = $insert['password'];
            $name = $insert['name'];
            $role = $insert['role'];

            //Дополнительная валидация - проверка на корректность:
            $validators = Validator::make(
            //Указываем поля которые будем валидировать:
                array(
                    'email' => $email,
                    'password' => $password,
                    'name' => $name,
                    'role' => $role
                ),
                //Указываем правила валидации:
                array(
                    'email' => 'required|unique:users,email|email|min:3|max:55',
                    'password' => 'required|min:6',
                    'name' => 'required'
                ),
                //Указываем сообщения в случае ошибок:
                array(
                    'required' => 'Вы не ввели поле :attribute ',
                    'unique' => 'Такой :attribute уже исспльзуется',
                    'min' => 'Поле :attribute должно содержать минимум :min символов',
                    'max' => 'Поле :attribute не должно превышать :max сиволов'
                )

            );

            //Проверяем поступили ли ошибки:
            if($validators->fails())
            {
                //Сообшения ошибок:
                $errorMessage = $validators->messages();
                $errors = "";
                foreach ($errorMessage->all() as $messages)
                {
                    $errors .= $messages . "\n" . nl2br("\n");
                }

                return View::make('cabinet.adduser', [
                    'id' => $id,
                    'success' => isset($success) ? $success : null,
                    'success' => isset($success) ? $success : null,
                    'title' => 'Добавить пользователя']);

                //return Redirect::intended('manager', ['errorss' => isset($errors) ? $errors : null]);
            }    else {

                //создаем новую сущность модели User
                $user = new User();

                //Регистрация пользователя
                $user->fill(Input::all());
                if($user->signup()){
                    $success = 'Пользователь '.$name.' успешно добавлен';
                }

                return View::make('cabinet.adduser', array(
                    'id' => $id,
                    'title' => 'Добавить пользователя',
                    'success' => isset($success) ? $success : null,
                    'errors' => isset($errors) ? $errors : null
                ));

            }

        }

    }

    /**
     * Метод формирвания формы редактирования
     *
     * @param  $id_admin айди текущего админа
     * @param $id - редактируемого пользователя
     * @return mixed
     */
    public function editUserForm($id, $id_admin)
    {
        $data = User::where('id', $id)->first();

        return View::make('cabinet.edit', [
            'id_admin' => $id_admin,
            'data' => $data,
            'title' => "Редактировать данные"
        ]);
    }


    /**
     * Метод актуализации введенной инфомации по заданному юзеру
     *
     * @param $id_admin айди админа для возвращения на страницу
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateUser($id_admin)
    {
        //Заносим все обновленные данные в переменную:
        $update = Input::all();
        $data = array(
            'email' => $update['email'],
            'name' => $update['name'],
            'role' => $update['role']
        );

        //создаем новый объект модели User
        $user = new User();

        //Обновляем данные выбранного пользователя:
        $user->where('id', $update['id'])->update($data);

        return Redirect::route('admin', [$id_admin]);

    }

    /**
     * Метод удаления выбранного пользователя
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        //Выбираем пользователя по поступившему айди и удаляенм:
        User::where('id',$id)->delete();
        return Redirect::back();

    }


}