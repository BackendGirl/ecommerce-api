<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    
    public function index(Request $request)
    {
        // Create cache key based on filters
        $cacheKey = 'all_products';

        if ($request->has('quantity')) {
            $cacheKey .= '_quantity_' . $request->quantity;
        }

        if ($request->has('price_min')) {
            $cacheKey .= '_min_' . $request->price_min;
        }

        if ($request->has('price_max')) {
            $cacheKey .= '_max_' . $request->price_max;
        }

        // Retrieve from cache or query the database to optimize loading performance
        $products = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($request) {
            $query = Product::query();

            // Filter by quantity
            if ($request->has('quantity')) {
                $query->where('quantity', $request->quantity);
            }

            // Filter by price range
            if ($request->has('price_min')) {
                $query->where('price', '>=', $request->price_min);
            }

            if ($request->has('price_max')) {
                $query->where('price', '<=', $request->price_max);
            }

            return $query->get();
        });

        return response()->json([
            'success' => true,
            'products' => $products,
        ]);
    }

    public function store(Request $request){
        //validating request
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'description'=>'required',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
        ]);
        if($validator->fails()){
            return response()->json([
                'message'=>$validator->errors(),
                'status'=>400,
                'success'=>false
            ],400);
        }else{

            
            // Save Product
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'quantity' => $request->quantity,
            ]);

            return response()->json([
                'message' => 'Product created successfully',
                'data' => $product,
                'status' => 201,
                'success' => true,
            ], 201);
        }
    }

    public function update($id,Request $request)
    {
        //validating request
        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'description' => 'string',
            'price' => 'numeric',
            'quantity' => 'integer',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'status' => 400,
                'success' => false
            ], 400);
        }
        
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
                'status' => 404,
                'success' => false
            ], 404);
        }
    
        //if request has updated value of the field then store it in variable
        if ($request->filled('name')) {
            $product->name = $request->name;
        }
        if ($request->filled('description')) {
            $product->description = $request->description;
        }
        if ($request->filled('price')) {
            $product->price = $request->price;
        }
        if ($request->filled('quantity')) {
            $product->quantity = $request->quantity;
        }
    
        //finally restoring changes
        $product->save();
    
        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $product,
            'status' => 200,
            'success' => true,
        ], 200);
    }

    public function delete($id)
    {
        //finding and ensuring the product avialability
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
                'status' => 404,
                'success' => false
            ], 404);
        }
    
        $product->delete();
    
        return response()->json([
            'message' => 'Product deleted successfully',
            'status' => 200,
            'success' => true,
        ], 200);
    }
    
}
