<?php
class ManagerController extends BaseController
{
    /**
     * Метод формирования страницы manager
     *
     * @param int $id_user айди пользователя
     * @return mixed
     */
    public function index($id_user)
    {
        //Получаем данные текущего пользователя
        $userData = User::where('id', $id_user)->firstOrfail();

        return View::make('manager', [
            'title' => "Менеджер ".$userData->name,
            'userData' => $userData,
        ]);
    }

}
