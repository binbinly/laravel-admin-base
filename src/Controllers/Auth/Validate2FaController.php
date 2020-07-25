<?php

namespace AdminBase\Controllers\Auth;


use AdminBase\Controllers\HttpController;

class Validate2FaController extends HttpController
{
    /**
     * 验证通过逻辑
     * @return mixed
     */
    public function index()
    {
        return admin_url('/');
    }
}
