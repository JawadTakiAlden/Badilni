<?php

namespace App\Http\Controllers\API\V1;

use App\HelperMethods\HelperMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Item\CreateItemRequest;
use App\Http\Requests\API\V1\Item\UpdateItemRequest;
use App\Http\Resources\API\V1\ItemResource;
use App\HttpResponse\HTTPResponse;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\Section;
use App\Types\ImageFlag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    use HTTPResponse;
    private HelperMethod $helpers;

    public function __construct()
    {
        $this->helpers = new HelperMethod();
    }

    private function getItemByID($itemID , array $with = []){
        return Item::with($with)->where('id' , $itemID)->first();
    }

    public function getAll(){
        try {
            $items = Item::all();
            return $this->success(ItemResource::collection($items));
        }catch (\Throwable $th){
            DB::rollBack();
            return $this->helpers->getErrorResponse($th);
        }
    }
    public function getActive(){
        try {
            $items = Item::where('is_active' , true)->get();
            return $this->success(ItemResource::collection($items));
        }catch (\Throwable $th){
            DB::rollBack();
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function myItems(){
        try {
            $items = Item::where('user_id' , auth()->user()->id)->get();
            return $this->success(ItemResource::collection($items));
        }catch (\Throwable $th){
            DB::rollBack();
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function showItem($itemID){
        try {
            $item = $this->getItemByID($itemID);
            if (!$item){
                return  $this->helpers->getNotFoundResourceRespone(__('messages.v1.items.item_not_found'));
            }
            return $this->success(ItemResource::make($item));
        }catch (\Throwable $th){
            DB::rollBack();
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function getHome(){
        try {
            $section_id = \request('section_id');
            $section = Section::where('id', $section_id)->first();
            if (!$section){
                return $this->helpers->getNotFoundResourceRespone(__('messages.v1.sections.section_not_found'));
            }
            $sectionTitle = json_decode($section->title)->en;
            if ($sectionTitle === 'newest'){
                $items = Item::where('is_active' , true)
                    ->where('user_id' , '!=' , auth()->user()->id)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
                return $this->success(ItemResource::collection($items));
            }
            else if ($sectionTitle === 'most viewed'){
                $items = Item::where('is_active' , true)
                    ->where('user_id' , '!=' , auth()->user()->id)
                    ->orderBy('views', 'desc')
                    ->paginate(10);
                return $this->success(ItemResource::collection($items));
            }else {
                return $this->success(ItemResource::collection($section->items));
            }
        }catch (\Throwable $th){
            DB::rollBack();
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function search(){
        try {
            $items = Item::where('is_active' , true)
                ->where('user_id' , '!=' , auth()->user()->id)
                ->filter(\request(['country_id','city_id' ,'area_id','search_text','status' ]))
                ->orderBy('created_at', 'desc')
                ->get();
            return $this->success(ItemResource::collection($items));
        }catch (\Throwable $th){
            DB::rollBack();
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function addItem(CreateItemRequest $request){
        try {
            DB::beginTransaction();
            $data = array_merge(
                $request->only(['title' , 'area_id' , 'status', 'category_id' , 'is_active' , 'price' , 'description']),
                ['user_id' => $request->user()->id]
            );
            $item = Item::create($data);
            if ($request->images){
                foreach ($request->images as $image){
                    ItemImage::create([
                       'image' =>  $image['imageFile'],
                       'is_default' => $image['is_default'],
                       'item_id' => $item->id
                    ]);
                }
            }
            DB::commit();
            return $this->success(null , __('messages.v1.items.item_added_successfully'));
        }catch (\Throwable $th){
            DB::rollBack();
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function editItem( UpdateItemRequest $request , $itemID){
        try {
            DB::beginTransaction();
            $item = $this->getItemByID($itemID);
            if (!$item){
                return  $this->helpers->getNotFoundResourceRespone(__('messages.v1.items.item_not_found'));
            }
            $item->update($request->only(['title' , 'description' , 'area_id' ,'status' , 'category_id' , 'is_active' , 'price']));
            if ($request->images){
                foreach ($request->images as $image){
                    if (intval($image['flag']) === ImageFlag::DELETE){
                        ItemImage::where('id' , $image['id'])->delete();
                    }else if (intval($image['flag']) === ImageFlag::ADD){
                        ItemImage::create([
                           'item_id' => $item->id,
                           'is_default' => $image['is_default'],
                           'is_active' => $image['is_active'] ?? true,
                           'image' => $image['imageFile']
                        ]);
                    }else if (intval($image['flag']) === ImageFlag::UPDATE_IS_DEFAULT){
                        ItemImage::where('id' , $image['id'])->update([
                            'is_default' => $image['is_default'],
                        ]);
                    }else{
                        return $this->error(__('messages.v1.items.flag_wrong') , 422);
                    }
                }
            }
            DB::commit();
            return $this->success(null , __('messages.v1.items.item_deleted_successfully'));
        }catch (\Throwable $th){
            DB::rollBack();
            return $this->helpers->getErrorResponse($th);
        }
    }

    public function deleteItem($itemID){
        try {
            $item = $this->getItemByID($itemID);
            if (!$item){
                return  $this->helpers->getNotFoundResourceRespone(__('messages.v1.items.item_not_found'));
            }
            $item->delete();
            return $this->success(null , __('messages.v1.items.item_deleted_successfully'));
        }catch (\Throwable $th){
            DB::rollBack();
            return $this->helpers->getErrorResponse($th);
        }
    }
}
