<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use OpenAdmin\Admin\Layout\Content;

class PostController extends Controller
{
    public function index(Content $content)
    {
        // Mostrar un mensaje de error
        $content->withError('Error Title', 'This is an error message.');

        // Mostrar un mensaje de advertencia
        $content->withWarning('Warning Title', 'This is a warning message.');

        // Mostrar un mensaje de información
        $content->withInfo('Info Title', 'This is an information message.');

        // Mostrar un mensaje de éxito
        $content->withSuccess('Success Title', 'This is a success message.');

        // También puedes usar las funciones admin_success, admin_warning, admin_error, admin_info directamente
        admin_success('Success Title', 'This is a success message.');
        admin_warning('Warning Title', 'This is a warning message.');
        admin_error('Error Title', 'This is an error message.');
        admin_info('Info Title', 'This is an information message.');

        // Finalmente, devuelve el contenido
        return $content;
    }
}
