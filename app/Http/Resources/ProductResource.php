<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{

    public $status;
    public $message;
    public $pagination;

    public function __construct($status,$message,$resource,$pagination){
        parent::__construct ($resource);
        $this->status = $status;
        $this->message = $message;
        $this->pagination = $pagination;
    }

    public function toArray($request)
    {
        return[
            'success' => $this->status,
            'message' => $this->message,
            'data' => $this->resource,
            'pagination' => $this->pagination,
        ];
    }
}
