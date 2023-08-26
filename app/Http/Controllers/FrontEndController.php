<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class FrontEndController extends Controller
{
    public function index(){
        $products = Product::limit(3)->get();
        $categories = Category::all();
        return view('customer.index', ["products" => $products, "categories" => $categories]);
    }
}
