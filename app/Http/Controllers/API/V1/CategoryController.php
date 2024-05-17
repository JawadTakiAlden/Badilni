<?php

namespace App\Http\Controllers\API\V1;

use App\HelperMethods\HelperMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Category\CreateCategoryRequest;
use App\Http\Requests\API\V1\Category\CreateSubCategoryRequest;
use App\Http\Requests\API\V1\Category\UpdateCategoryRequest;
use App\Http\Resources\API\V1\CategoryResource;
use App\HttpResponse\HTTPResponse;
use Illuminate\Support\Facades\File;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use HTTPResponse;
    private HelperMethod $helpers;

    public function __construct()
    {
        $this->helpers = new HelperMethod();
    }
    private function getCategorByID($categoryID , array $with = []){
        return Category::with($with)->where('id',$categoryID)->first();
    }

    public function getAllSubCategoryOfCategory ($categoryId) {
        try {
            $categories = Category::where('parent_id' , $categoryId)->where('is_active' , true)->orderBy('sort')->get();
            return $this->success(CategoryResource::collection($categories));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function getActiveSubCategoryOfCategory ($categoryId) {
        try {
            $categories = Category::where('parent_id' , $categoryId)->where('is_active' , true)->orderBy('sort')->get();
            return $this->success(CategoryResource::collection($categories));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function getAllCategories(){
        try {
            $categories = Category::where('parent_id' , null)->orderBy('sort')->get();
            return $this->success(CategoryResource::collection($categories));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function getActiveCategories(){
        try {
            $categories = Category::where('parent_id' , null)->where('is_active' , true)->orderBy('sort')->get();
            return $this->success(CategoryResource::collection($categories));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function createCategory(CreateCategoryRequest $request){
        try {
            $category = Category::create($request->only(['title' , 'description', 'is_active' , 'sort' , 'image']));
            return $this->success(CategoryResource::make($category) , __('messages.v1.category.create_category'));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function createSubCategory(CreateSubCategoryRequest $request){
        try {
            $category = Category::create($request->only(['title' , 'parent_id' , 'description' , 'is_active' , 'sort' , 'image']));
            return $this->success(CategoryResource::make($category) , __('messages.v1.category.create_category'));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function updateCategory(UpdateCategoryRequest $request , $categoryID){
        try {
            $category = $this->getCategorByID($categoryID);
            if (!$category){
                return $this->helpers->getNotFoundResourceRespone(__('messages.v1.category.category_not_found'));
            }
            $currntImagePath = $category->image;
            $category->update($request->only(['title' , 'parent_id' , 'description' , 'is_active' , 'sort' , 'image']));
            if ($request->image){
                File::delete(public_path($currntImagePath));
            }
            return $this->success(CategoryResource::make($category) , __('messages.v1.category.update_category'));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function deleteCategory($categoryID){
        try {
            $category = $this->getCategorByID($categoryID);
            if (!$category){
                return $this->helpers->getNotFoundResourceRespone(__('messages.v1.category.category_not_found'));
            }
            $currntImagePath = $category->image;
            $category->delete();
            File::delete(public_path($currntImagePath));
            return $this->success(CategoryResource::make($category) , __('messages.v1.category.delete_category'));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }
}
