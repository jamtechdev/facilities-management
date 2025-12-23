<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\RouteHelper;

class BaseController extends Controller
{
    /**
     * Get the view prefix based on current route
     * 
     * @return string
     */
    protected function getViewPrefix(): string
    {
        $routeName = request()->route()->getName();
        
        // Check if route starts with superadmin
        if (str_starts_with($routeName, 'superadmin.')) {
            return 'superadmin';
        }
        
        // Default to admin
        return 'admin';
    }
    
    /**
     * Get view path with correct prefix
     * 
     * @param string $viewName View name without prefix (e.g., 'leads.index')
     * @return string Full view path (e.g., 'superadmin.leads.index' or 'admin.leads.index')
     */
    protected function getView(string $viewName): string
    {
        $prefix = $this->getViewPrefix();
        return $prefix . '.' . $viewName;
    }
}

