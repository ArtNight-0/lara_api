<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Products;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
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

    public function show($id){
        #get by id
        $query = Products::find($id);

        #variabel
        $datas = $query;

        #pagination
        $pagination = [];

        return new ProductResource(true,'Get Data By Id Succsess', $datas,$pagination);

    }
    public function store(Request $request){
        #get by id
        $query = Products::create($request->all());

        #variabel
        $datas = $query;

        #pagination
        $pagination = [];

        return new ProductResource(true,'Get Data By Id Succsess', $datas,$pagination);
    }
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
