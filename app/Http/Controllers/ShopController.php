<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brand;
use App\Models\Product;
class ShopController extends Controller
{
    public function index(Request $request, $categorySlug = null, $subCategorySlug = null){
        $categorySelected = '';
        $subCategorySelected = '';
        $products = Product::where('status',1);
        $brandsArray = [];
        if (!empty($request->get('brand'))) {
            $brandsArray = explode(',',$request->get('brand'));
            $products = $products->where('brand_id',$brandsArray);
        }

       $categories = Category::orderBy('name','ASC')->with('sub_category')->where('status',1)->get();
       $brands = Brand::orderBy('name','ASC')->where('status',1)->get();
       //Apply Filters
       if(!empty($categorySlug)){
        $category = Category::where('slug',$categorySlug)->first();
        $products = $products->where('category_id',$category->id);
        $categorySelected = $category->id;
       }
       if(!empty($subCategorySlug)){
        $subCategory = SubCategory::where('slug',$subCategorySlug)->first();
        $products = $products->where('sub_category_id',$subCategory->id);
        $subCategorySelected = $subCategory->id;
       }
       if($request->get('price_max') != '' && $request->get('price_min') != ''){
        if ($request->get('price_max') == 1000) {
            $products = $products->whereBetween('price',[intval($request->get('price_min')),10000000]);
        } else
        {
            $products = $products->whereBetween('price',[intval($request->get('price_min')),intval($request->get('price_max'))]);
        }
       }
       if ($request->get('sort') != '') {
        if ($request->get('sort') == 'latest') {
            $products = Product::orderBy('id','DESC');
        } else if ($request->get('sort') == 'price_asc') {
            $products = Product::orderBy('price','ASC');
        } else {
            $products = Product::orderBy('price','DESC');
        }
       } else {
        $products = Product::orderBy('id','DESC');
       }
       $products = Product::paginate(6);

      // $products = Product::orderBy('title','DESC')->where('status',1)->get();

       $data['categories'] = $categories;
       $data['brands'] = $brands;
       $data['products'] = $products;
       $data['categorySelected'] = $categorySelected;
       $data['subCategorySelected'] = $subCategorySelected;
       $data['priceMax'] = (intval($request->get('price_max')) == 0) ? 1000 : $request->get('price_max');
       $data['priceMin'] = intval($request->get('price_min'));
       $data['sort'] = $request->get('sort');
       $data['brandsArray'] = $brandsArray;
        return view('front.shop', $data);
    }
    public function product($slug) {
        $product = Product::where('slug',$slug)->with('product_images')->first();
       if($product == null) {
        abort(404);
       }

       $relatedProducts = [];
       //fetch Related Product
       if($product->related_products != ' '){
           $productArray = explode(',',$product->related_products);

           $relatedProducts = Product::whereIn('id',$productArray)->get();
       }
       $data['product'] = $product;
        $data['relatedProducts'] = $relatedProducts;
       return view('front.product',$data);
      }
}
