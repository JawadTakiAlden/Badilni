<?php

namespace App\Http\Controllers\API\V1;

use App\HelperMethods\HelperMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Item\CreateItemRequest;
use App\Http\Resources\API\V1\ItemResource;
use App\HttpResponse\HTTPResponse;
use App\Models\Item;
use App\Models\ItemImage;
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

    public function addItem(CreateItemRequest $request){
        try {
            DB::beginTransaction();
            $data = array_merge(
                $request->only(['title' , 'area_id' , 'sub_category_id' , 'is_active' , 'price' , 'description']),
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
            return $this->success(ItemResource::make($item) , __('messages.v1.items.item_added_successfully'));
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
            return $this->success(ItemResource::make($item) , __('messages.v1.items.item_deleted_successfully'));
        }catch (\Throwable $th){
            DB::rollBack();
            return $this->helpers->getErrorResponse($th);
        }
    }
}
