<?php

namespace App\Http\Controllers;

use DataTables;
use Illuminate\Http\Request;

use App\Http\Requests\CategoryRequest;
use App\Solid\Interfaces\CategoryRepositoryInterface;

class CategoryController extends Controller
{
    protected $categoryRepository;
    
    public function __construct( CategoryRepositoryInterface $categoryRepository) {
        $this->categoryRepository = $categoryRepository;
    }

    public function get_category(Request $request){
        $category = $this->categoryRepository->getAllCategory();

        return DataTables::of($category)
        ->addColumn('action', function($category){
            $result = "";
            if(is_null($category->deleted_at)){
                $result .= "<button class='btn btn-sm btn-secondary btnEditCat' data-id='$category->id' data-cat-name='$category->category_name'><i class='fas fa-edit'></i></button>";
                $result .= "<button class='btn btn-sm btn-danger btnChangeStat ml-1' data-id='$category->id' data-status='0'><i class='fas fa-trash'></i></button>";
            }
            else{
                $result .= "<button class='btn btn-sm btn-success btnChangeStat ml-1' data-id='$category->id' data-status='1'><i class='fas fa-undo'></i></button>";
            }
            return $result;
        })
        ->addColumn('status', function($category){
            $result = "";
            if(is_null($category->deleted_at)){
                $result .= "<span class='badge badge-success'>Active</span>";
            }
            else{
                $result .= "<span class='badge badge-danger'>Inactive</span>";
            }
            return $result;
        })
        ->rawColumns(['action', 'status'])
        ->make(true);
    }

    public function save_category(CategoryRequest $request){
        date_default_timezone_set('Asia/Manila');
        $data = $request->validated();

        if(isset($request->id)){ // Update
            $data['updated_by'] = $_SESSION['rapidx_username'];
            return $this->categoryRepository->update($request->id, $data);
        }
        else{ // Create
            $data['created_by'] = $_SESSION['rapidx_username'];
            $data['created_at'] = NOW();
            return $this->categoryRepository->insert($data);
        }
    }

    public function update_status(Request $request){
        date_default_timezone_set('Asia/Manila');
        $data = array(
            'deleted_at' => null
        );
        if($request->status == 0){
            $data['deleted_at'] = NOW();
        }

        return $this->categoryRepository->update($request->id, $data);

    }
}
