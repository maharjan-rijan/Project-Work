<?php

use App\Models\Category;
use App\Models\Page;
function getCategories(){
  return  Category::orderBy('name','ASC')
  ->with('sub_category')
  ->orderBy('id','DESC')
  ->where('status',1)
  ->where('showHome','Yes')
  ->get();
}
function staticPages(){
    $pages = Page::orderby('name','ASC')->get();
    return $pages;
}
?>
