<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Category;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class CategoryController extends BaseController {
    

     /**
     * Retrieves all Meal Categories.
     *
     * Returns meal categories
     */
    #[OpenApi\Operation(tags: ['Meals'])]
    public function index(){
        $categories = Category::select('id', 'Name')->get();

        $output = [
            'categories' => $categories
        ];

        return $this->sendResponse($output, 'Categories list retrieve succesfully.');
    }
}
