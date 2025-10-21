<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Render view with AJAX support and layout control
     */
   protected function renderView($view, $data = [], $layoutConfig = [])
{
    $defaultConfig = [
        'showNavbar' => true,
        'showSidebar' => true,
        'showFooter' => true,
    ];

    $config = array_merge($defaultConfig, $layoutConfig);
    $viewData = array_merge($data, $config);

    $isAjax = request()->header('X-Requested-With') === 'XMLHttpRequest';

    if ($isAjax) {

        $viewInstance = view($view, $viewData);
        $sections = $viewInstance->renderSections();

        return $sections['content'] ?? $viewInstance->render();

    }


    return view($view, $viewData);
}
}
