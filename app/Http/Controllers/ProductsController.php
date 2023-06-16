<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Products;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
 * @OA\Get(
 * path="/api/product",
 * tags={"Product"},
 * summary="",
 * description="Get All Data",
 * operationId="product_index",
 * @OA\Parameter(
 *    name="per_page",
 *    description="per_page value is number , ex: ?per_page=10",
 *    in="query",
 *    @OA\Schema(
 *       type="number",
 *    )
 * ),
 * @OA\Parameter(
 *    name="page",
 *    description="page value is number , ex: ?page=2",
 *    in="query",
 *    @OA\Schema(
 *       type="number",
 *    )
 * ),
 * @OA\Parameter(
 *    name="sort",
 *    description="sort value is string , ex: ?sort=id:asc",
 *    required=false
 *    in="query",
 *    @OA\Schema(
 *       type="string",
 *    )
 * ),
 * @OA\Parameter(
 *    name="where",
 *    description="where value is string , ex: ?where{'name':'jhon','dob':'1990-12-31'}",
 *    required=false
 *    in="query",
 *    @OA\Schema(
 *       type="string",
 *    )
 * ),
 * @OA\Parameter(
 *    name="count",
 *    description="count value is boolean , ex: ?count=true",
 *    required=false
 *    in="query",
 *    @OA\Schema(
 *       type="boolean",
 *    )
 * ),
 * @OA\Response(
 *    response="default",
 *    description="OK",
 *    @OA\MediaType(
 *       mediaType="application/json",
 *       example={
 *          "status"=true,
 *          "message"="Get Dtaa Successfull",
 *          "data"={},
 *      }
 *    )
 * ),
 * 
 * )
 */
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        #parameter
        $where = $request->has('where') ? $request->get('where') : '{}';
        $sort = $request->has('sort') ? $request->get('sort') : 'id:asc';
        $per_page = $request->has('per_page') ? $request->get('per_page') : 2;
        $page = $request->has('page') ? $request->get('page') : 1;
        $count = $request->has('count') ? $request->get('count') : false;
        $search = $request->has('search') ? $request->get('search') : '';

        #prepare parameter
        $sort = explode(':',$sort);
        $where = str_replace("'","\"",$where);
        $where = json_decode($where,true);

        #query get
        $query = Products::where([['id','>','0']]);

        #query where
        if($where){
            foreach($where as$key =>$value){
                $query = $query->where([[$key,'=', $value]]);
            }
        }

        #query search
        if($search){
            $query = $query->where([['name','like','%' . $search . '%']]);
            $query = $query->orWhere([['description','like','%' . $search . '%']]);
            $query = $query->orWhere([['price','like','%' . $search . '%']]);
        }
        #variabel data
        $datas = [];
        
        #pagination
        $pagination = [];
        $pagination['page'] = (int)$page;
        $pagination['per_page'] = (int)$per_page;
        $pagination['total_data'] = $query->count('id');
        $pagination['total_page'] = ceil($pagination['total_data'] / $pagination['per_page']);

        if($count == true){
            $query = $query->count('id');
            $datas['count'] = $query;

        }else{
            $query = $query
            ->orderBy($sort[0],$sort[1])
            ->limit($per_page)
            ->offset(($page - 1 )*$per_page)
            ->get()
            ->toArray();

            foreach ($query as $qry) {
                $temp = $qry;
                
                $created_at_indo = \Carbon\Carbon::parse($temp['created_at']);
                $created_at_indo->locale('id')->settings(['formatFunction' => 'translatedFormat']);

                $temp['created_date_indo'] = $created_at_indo->format('l, d F Y H:i:s ');
                array_push($datas,$temp);
            };
        }
        // $query = Products::get()->toArray();

        // $datas= [];

        return new ProductResource(true, 'Get Data Successfully',$datas,$pagination);
    }

/**
 * @OA\Get(
 * path="/api/product/{id}",
 * tags={"Product"},
 * summary="",
 * description="Get data by id",
 * operationId="product_show",
 * @OA\Parameter(
 *    name="id",
 *    description="id",
 *    required=true,
 *    in="path",
 *    @OA\Schema(
 *       type="number",
 *    )
 * ),
 * @OA\Response(
 *    response="default",
 *    description="OK",
 *    @OA\MediaType(
 *       mediaType="application/json",
 *       example={
 *          "status"=true,
 *          "message"="Get Data Successfull",
 *          "data"={},
 *           }
 *         )
 *      )
 *    )
 * 
 */
    public function show($id){
        #get by id
        $query = Products::find($id);

        #variabel
        $datas = $query;

        #pagination
        $pagination = [];

        return new ProductResource(true,'Get Data By Id Succsess', $datas,$pagination);

    }

/**
     * @OA\Post(
     *      path="/api/product",
     *      tags={"product"},
     *      summary="",
     *      description="insert data",
     *      operationId="product_store",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                 @OA\Property(
     *                      property="name",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="image",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="description",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="type",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="price",
     *                      type="string"
     *                   )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="default",
     *          description="OK",
     *         @OA\MediaType(
     *              mediaType="application/json",
     *              example={
     *                  "success"=true,
     *                  "massage"="Insert Data Successfull",
     *                  "data"={}
     *              } 
     *            ) 
     *         )
     * )        
     */
    public function store(Request $request){
        #get by id
        $query = Products::create($request->all());

        #variabel
        $datas = $query;

        #pagination
        $pagination = [];

        return new ProductResource(true,'Get Data By Id Succsess', $datas,$pagination);
    }

    /**
     *  @OA\Put(
     *      path="/api/product/{id}",
     *      tags={"Product"},
     *      summary="",
     *      description="Update Data",
     *      operationId="product_update",
     *      security={{ "bearirAuth":{} }},
     *      @OA\Parameter(
     *          name="id",
     *          description="id",
     *          required=true,
     *          in="path",
     *      @OA\Schema(
     *          type="number"
     *      )
     *  ),
     *  @OA\RequestBody(
     *      @OA\MediaType(
     *      mediaType="application/json",
     *      @OA\Schema(
     *          @OA\Property(
     *              property="name",
     *              type="string"
     *          ),
     *          @OA\Property(
     *              property="image",
     *              type="string"
     *          ),
     *          @OA\Property(
     *              property="description",
     *              type="string"
     *          ),
     *          @OA\Property(
     *              property="type",
     *              type="string"
     *          ),
     *          @OA\Property(
     *              property="price",
     *              type="string"
     *          )
     *      )
     *  )
     *),
     *      @OA\Response(
     *          response="default",
     *          description="OK",
     *          @OA\MediaType(
     *          mediaType="application/json",
     *          example={
     *              "success"=true,
     *              "message"="Update Data Successfull",
     *              "data"={},
     *        }
     *      )       
     *      )
     *      ) 
     *       )
     */
    public function update(Request $request, $id){
        #query insert
        $query = Products::findOrFail($id);
        $query = $query->update($request->all());

        #query after update
        $query = Products::findOrFail($id);

        #variabel data
        $datas = $query;

        #pagination
        $pagination = [];

        return new ProductResource(true,'Get Data By Id Succsess', $datas,$pagination);
    }
    /**
     * @OA\Delete(
     *      path="/api/product/{id}",
     *      tags={"Product"},
     *      summary="",
     *      description="Delete data",
     *      operationId="product_destroy",
     *      security={{ "bearerAuth":{} }},
     *      @OA\Parameter(
     *          name="id",
     *          description="id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="number"
     *         )
     *      ),
     *      @OA\Response(
     *          response="default",
     *          description="OKE",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              example={
     *              "success"=true,
     *              "message"="Delete Data successfull",
     *              "data"={}
     *          }
     *      )   
     *    )
     *  )  

    */
    public function destroy($id){
        #query insert
        $query = Products::findOrFail($id);
        $query = $query->delete();

 #variabel data
        $datas = $query;

        #pagination
        $pagination = [];

        return new ProductResource(true,'Get Data By Id Succsess', $datas,$pagination);
    }

}
