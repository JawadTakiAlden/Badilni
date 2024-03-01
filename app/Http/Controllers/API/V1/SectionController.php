<?php

namespace App\Http\Controllers\API\V1;

use App\HelperMethods\HelperMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Section\CreateSectionRequest;
use App\Http\Requests\API\V1\Section\UpdateSectionRequest;
use App\Http\Resources\API\V1\SectionResource;
use App\HttpResponse\HTTPResponse;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    use HTTPResponse;
    private HelperMethod $helpers;

    public function __construct()
    {
        $this->helpers = new HelperMethod();
    }
    private function getSectionByID($sectionID , array $with = []){
        return Section::with($with)->where('id' , $sectionID)->first();
    }

    public function getAll(){
        try {
            $sections = Section::all();
            return $this->success(SectionResource::collection($sections));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function getActive(){
        try {
            $sections = Section::where('is_active' , true)->get();
            return $this->success(SectionResource::collection($sections));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function showSection($sectionID){
        try {
            $section = $this->getSectionByID($sectionID);
            if (!$section){
                return $this->helpers->getNotFoundResourceRespone(__('messages.v1.section.section_not_found'));
            }
            return $this->success(SectionResource::make($section));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function createSection(CreateSectionRequest $request){
        try {
            $section = Section::create($request->only(['title' , 'is_active']));
            return $this->success(SectionResource::make($section) , __('messages.v1.sections.create_section'));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function editSection(UpdateSectionRequest $request , $sectionID){
        try {
            $section = $this->getSectionByID($sectionID);
            if (!$section){
                return $this->helpers->getNotFoundResourceRespone(__('messages.v1.section.section_not_found'));
            }
            $section->update($request->only(['title' , 'is_active']));
            return $this->success(SectionResource::make($section) , __('messages.v1.sections.update_section'));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function delete($sectionID){
        try {
            $section = $this->getSectionByID($sectionID);
            if (!$section){
                return $this->helpers->getNotFoundResourceRespone(__('messages.v1.section.section_not_found'));
            }
            $section->delete();
            return $this->success(SectionResource::make($section) , __('messages.v1.sections.delete_section'));
        }catch (\Throwable $th){
            return $this->helpers->getErrorResponse($th);
        }
    }
}
