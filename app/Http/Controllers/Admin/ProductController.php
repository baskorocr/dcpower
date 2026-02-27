<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Project;
use App\Models\StandardPacking;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['project', 'creator', 'standardPacking']);
        
        // Get user's projects
        if (auth()->user()->hasRole('distributor')) {
            // Distributor only sees products in their stock
            $distributor = \App\Models\Distributor::where('user_id', auth()->id())->first();
            if ($distributor) {
                $productIds = \DB::table('stock_movements')
                    ->where('distributor_id', $distributor->id)
                    ->pluck('product_id');
                $query->whereIn('id', $productIds);
                $projects = Project::where('id', $distributor->project_id)->get();
            } else {
                $query->whereRaw('1 = 0'); // No products
                $projects = collect([]);
            }
        } elseif (!auth()->user()->hasRole('admin')) {
            $projectIds = auth()->user()->projects->pluck('id');
            $query->whereIn('project_id', $projectIds);
            $projects = auth()->user()->projects;
        } else {
            $projects = Project::all();
        }
        
        // Search
        if ($request->filled('search')) {
            $query->where('serial_number', 'like', '%' . $request->search . '%');
        }
        
        // Filter by status
        if ($request->filled('status')) {
            if ($request->status == 'retail') {
                $query->where('retail_stock', '>', 0);
            } else {
                $query->where('status', $request->status);
            }
        }
        
        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        
        $products = $query->latest()->paginate(20)->withQueryString();
        
        return view('admin.products.index', compact('products', 'projects'));
    }

    public function create()
    {
        // Get current user's project
        $projectUser = \DB::table('project_users')
            ->where('user_id', auth()->id())
            ->first();
        
        $project = null;
        if ($projectUser) {
            $project = Project::find($projectUser->project_id);
        } elseif (auth()->user()->hasRole('admin')) {
            $project = Project::first();
        }

        return view('admin.products.create', compact('project'));
    }

    public function verifyProjectQR($qrCode)
    {
        $project = Project::where('qr_code', $qrCode)->first();
        
        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        }

        // Check if user is assigned to this project
        $isAssigned = $project->users()->where('user_id', auth()->id())->exists();
        
        if (!$isAssigned && !auth()->user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'You are not assigned to this project'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
                'code' => $project->code,
                'standard_packing_quantity' => $project->standard_packing_quantity
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'serial_numbers' => 'required|array',
            'serial_numbers.*' => 'required|string',
            'variant' => 'nullable|string',
        ]);

        // Get project_id from project_users for current user
        $projectUser = \DB::table('project_users')
            ->where('user_id', auth()->id())
            ->first();
        
        if (!$projectUser && !auth()->user()->hasRole('admin')) {
            return response()->json(['success' => false, 'message' => 'You are not assigned to any project.'], 403);
        }

        // If admin and not assigned, get first project
        $projectId = $projectUser ? $projectUser->project_id : \App\Models\Project::first()->id;
        $project = Project::find($projectId);

        $serialNumbers = $request->serial_numbers;
        $variant = $request->variant;
        $createdProducts = [];
        $standardPacking = null;

        // Check if project uses standard packing
        if ($project->standard_packing_quantity && count($serialNumbers) == $project->standard_packing_quantity) {
            // Generate packing code based on format
            $packingCode = $this->generatePackingCode($project, $variant);
            
            // Create standard packing
            $standardPacking = StandardPacking::create([
                'project_id' => $projectId,
                'variant' => $variant,
                'packing_code' => $packingCode,
                'quantity' => $project->standard_packing_quantity,
                'created_by' => auth()->id(),
                'packed_at' => now(),
            ]);
        }

        foreach ($serialNumbers as $serialNumber) {
            // Check if serial number already exists in this project
            $exists = Product::where('serial_number', $serialNumber)
                ->where('project_id', $projectId)
                ->exists();
            
            if ($exists) {
                continue; // Skip duplicate
            }

            $product = Product::create([
                'project_id' => $projectId,
                'standard_packing_id' => $standardPacking ? $standardPacking->id : null,
                'serial_number' => $serialNumber,
                'created_by' => auth()->id(),
                'manufactured_at' => now(),
                'status' => 'manufactured',
            ]);

            $product->traceLogs()->create([
                'user_id' => auth()->id(),
                'scanned_by' => auth()->id(),
                'event_type' => 'manufactured',
                'action' => 'manufactured',
                'location' => 'Factory',
                'notes' => $standardPacking ? 'Product manufactured - Packing: ' . $standardPacking->packing_code : 'Product manufactured',
                'scanned_at' => now(),
            ]);

            $createdProducts[] = $product;
        }

        $response = [
            'success' => true,
            'message' => count($createdProducts) . ' products created successfully!',
            'products' => $createdProducts
        ];

        if ($standardPacking) {
            $response['standard_packing'] = [
                'id' => $standardPacking->id,
                'code' => $standardPacking->packing_code,
                'quantity' => $standardPacking->quantity
            ];
        }

        return response()->json($response);
    }

    private function generatePackingCode($project, $variant = null)
    {
        $format = $project->packing_format ?? 'PACK-{RANDOM}';
        
        // Get next batch number for this project and variant
        $lastPacking = StandardPacking::where('project_id', $project->id)
            ->when($variant, fn($q) => $q->where('variant', $variant))
            ->latest('id')
            ->first();
        
        $batchNumber = $lastPacking ? (intval(substr($lastPacking->packing_code, -5)) + 1) : 1;
        
        // Replace placeholders
        $code = str_replace('{PROJECT_NAME}', strtoupper($project->name), $format);
        $code = str_replace('{PROJECT_CODE}', $project->project_code ?? strtoupper(substr($project->name, 0, 3)), $code);
        $code = str_replace('{VARIANT}', $variant ?? '', $code);
        $code = str_replace('{YYYY}', date('Y'), $code);
        $code = str_replace('{MM}', date('m'), $code);
        $code = str_replace('{DD}', date('d'), $code);
        $code = str_replace('{YYYYMMDD}', date('Ymd'), $code);
        $code = str_replace('{BATCH:5}', str_pad($batchNumber, 5, '0', STR_PAD_LEFT), $code);
        $code = str_replace('{RANDOM}', strtoupper(\Str::random(10)), $code);
        
        return $code;
    }

    public function show(Product $product)
    {
        $product->load(['traceLogs.user', 'warrantyClaims', 'standardPacking']);
        return view('admin.products.show', compact('product'));
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id'
        ]);

        Product::whereIn('id', $request->product_ids)->delete();
        
        return redirect()->route('products.index')->with('success', count($request->product_ids) . ' product(s) deleted successfully');
    }

    public function print(Request $request)
    {
        $ids = explode(',', $request->ids);
        $products = Product::with('project')->whereIn('id', $ids)->get();
        
        return view('admin.products.print', compact('products'));
    }
}
