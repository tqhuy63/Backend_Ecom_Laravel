<?php

use App\Http\Controllers\admin\ProductConTroller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\ProductImageController;
use App\Http\Controllers\admin\ProductSubCategoryController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\TempImagesController;


Route::get('/', function () {
    return view('welcome');
});





Route::group(['prefix' => 'admin'], function () {
    Route::group(['middleware' => ['admin.guest']], function () {
        Route::get('/login', [AdminLoginController::class, 'index'])->name('admin.login');
        Route::post('/authenticate', [AdminLoginController::class, 'authenticate'])->name('admin.authenticate');
    });
    Route::group(['middleware' => ['admin.auth']], function () {
        Route::get('/dashboard', [HomeController::class, 'index'])->name('admin.dashboard');
        Route::get('/logout', [HomeController::class, 'logout'])->name('admin.logout');

        // category Routes
        Route::get('/categories', [CategoryController::class,'index'])->name('categories.index');
        Route::get('/categories/create', [CategoryController::class,'create'])->name('categories.create');
        Route::get('/categories/{category}/edit', [CategoryController::class,'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class,'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class,'destroy'])->name('categories.delete');
        Route::post('/categories', [CategoryController::class,'store'])->name('categories.store');

        // Sub Category Routes
        Route::get('/sub-categories', [SubCategoryController::class,'index'])->name('sub-categories.index');
        Route::get('/sub-categories/create', [SubCategoryController::class, 'create'])->name('sub-categories.create');
        Route::post('/sub-categories', [SubCategoryController::class, 'store'])->name('sub-categories.store');
        Route::get('/sub-categories/{subCategory}/edit', [SubCategoryController::class,'edit'])->name('sub-categories.edit');
        Route::put('/sub-categories/{subCategory}', [SubCategoryController::class,'update'])->name('sub-categories.update');
        Route::delete('/sub-categories/{subCategory}', [SubCategoryController::class,'destroy'])->name('sub-categories.delete');

        // Brand Routes
        Route::get('/brands/create', [BrandController::class, 'create'])->name('brands.create');
        Route::post('/brands', [BrandController::class, 'store'])->name('brands.store');
        Route::get('/brands', [BrandController::class,'index'])->name('brands.index');
        Route::get('/brands/{brand}/edit', [BrandController::class,'edit'])->name('brands.edit');
        Route::delete('/brands/{brand}', [BrandController::class,'destroy'])->name('brands.delete');
        Route::put('/brands/{brand}', [BrandController::class,'update'])->name('brands.update');

        //Product Routes
        Route::get('/products', [ProductConTroller::class,'index'])->name('products.index');
        Route::get('/products/create', [ProductConTroller::class, 'create'])->name('products.create');
        Route::post('/products', [ProductConTroller::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductConTroller::class,'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductConTroller::class,'update'])->name('products.update');



        Route::get('/product-subcategories', [ProductSubCategoryController::class, 'index'])->name('product-subcategories.index');


        Route::post('/product-images/update', [ProductImageController::class, 'update'])->name('product-images.update');
        Route::delete('/product-images', [ProductImageController::class, 'destroy'])->name('product-images.destroy');


        
        // temp-images.create
        Route::post('/upload-temp-image', [TempImagesController::class,'create'])->name('temp-images.create');

        Route::get('/getSlug', function(Request $request) {
            $slug = '';
            if (!empty($request->title)) {
                $slug = Str::slug($request->title);
            }
            return response()->json([
                'status' => true,
                'slug' => $slug,
            ]);

        })->name('getSlug');
    });
});