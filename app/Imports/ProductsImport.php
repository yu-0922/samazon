<?php

namespace App\Imports;

use App\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $product = new Product();
        $product->name = $row['name'];
        $product->description = $row['description'];
        $product->price = $row['price'];
        $product->category_id = $row['category_id'];
        $product->recommend_flag = $row['recommend_flag'];
        $product->carriage_flag = $row['carriage_flag'];
        $product->save();

        return $product;
    }
}
