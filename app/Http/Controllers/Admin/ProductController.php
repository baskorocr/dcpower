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
        $query = Product::with(['project', 'creator', 'standardPacking', 'repairDistributor']);
        
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
        
        // Filter by variant
        if ($request->filled('variant')) {
            $query->whereHas('standardPacking', function($q) use ($request) {
                $q->where('variant', $request->variant);
            });
        }
        
        // Get variants based on role and selected project
        $variants = collect([]);
        if ($request->filled('project_id')) {
            $project = Project::find($request->project_id);
            if ($project && $project->use_variants) {
                $variants = collect($project->variants);
            }
        } elseif (!auth()->user()->hasRole('admin')) {
            // For PM and Distributor, get variants from their projects
            foreach ($projects as $project) {
                if ($project->use_variants) {
                    $variants = $variants->merge($project->variants);
                }
            }
            $variants = $variants->unique();
        } else {
            // For admin, get all variants from all projects
            $allProjects = Project::where('use_variants', 1)->get();
            foreach ($allProjects as $project) {
                if ($project->variants) {
                    $variants = $variants->merge($project->variants);
                }
            }
            $variants = $variants->unique();
        }
        
        $perPage = $request->get('per_page', 20);
        $products = $query->latest()->paginate($perPage)->withQueryString();
        
        return view('admin.products.index', compact('products', 'projects', 'variants'));
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

    public function checkSerial($serial)
    {
        $user = auth()->user();
        $projectId = null;

        // Get user's project
        if (!$user->hasRole('admin')) {
            $projectUser = \DB::table('project_users')
                ->where('user_id', $user->id)
                ->first();
            $projectId = $projectUser ? $projectUser->project_id : null;
        }

        // Check if serial exists
        $query = Product::where('serial_number', $serial);
        
        if ($projectId) {
            $query->where('project_id', $projectId);
        }
        
        $exists = $query->exists();

        return response()->json([
            'exists' => $exists,
            'serial' => $serial
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

        // Validate standard packing quantity
        if ($project->standard_packing_quantity) {
            if (count($serialNumbers) != $project->standard_packing_quantity) {
                return response()->json([
                    'success' => false, 
                    'message' => "Standard packing requires exactly {$project->standard_packing_quantity} items. You scanned " . count($serialNumbers) . " items."
                ], 422);
            }
        }

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
            try {
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
                    'variant' => $variant,
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
            } catch (\Illuminate\Database\QueryException $e) {
                // Skip if duplicate entry error
                if ($e->getCode() == 23000) {
                    continue;
                }
                throw $e;
            }
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
        // Check distributor access
        if (auth()->user()->hasRole('distributor')) {
            $distributor = \App\Models\Distributor::where('user_id', auth()->id())->first();
            if (!$distributor || $distributor->project_id !== $product->project_id) {
                abort(403, 'Unauthorized access.');
            }
            
            $hasAccess = \DB::table('stock_movements')
                ->where('product_id', $product->id)
                ->where('distributor_id', $distributor->id)
                ->exists();
            if (!$hasAccess) {
                abort(403, 'Unauthorized access.');
            }
        }

        $product->load(['traceLogs.user', 'warrantyClaims', 'standardPacking']);
        return view('admin.products.show', compact('product'));
    }

    public function destroy(Product $product)
    {
        \App\Models\ProductAuditLog::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'action' => 'deleted',
            'old_values' => $product->only(['serial_number', 'status', 'variant']),
            'ip_address' => request()->ip(),
        ]);

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

    public function switchForm()
    {
        return view('admin.products.switch');
    }

    public function switchSerial(Request $request)
    {
        $request->validate([
            'old_serial' => 'required|string',
            'new_serial' => 'required|string',
        ]);

        $oldProduct = Product::where('serial_number', $request->old_serial)->first();
        
        if (!$oldProduct) {
            return back()->with('error', 'Old serial number not found!')->withInput();
        }

        $newExists = Product::where('serial_number', $request->new_serial)->exists();
        
        if ($newExists) {
            return back()->with('error', 'New serial number already exists!')->withInput();
        }

        // Audit log
        \App\Models\ProductAuditLog::create([
            'product_id' => $oldProduct->id,
            'user_id' => auth()->id(),
            'action' => 'serial_switched',
            'old_values' => ['serial_number' => $request->old_serial],
            'new_values' => ['serial_number' => $request->new_serial],
            'ip_address' => request()->ip(),
        ]);

        // Update serial number
        $oldProduct->serial_number = $request->new_serial;
        $oldProduct->save();

        // Log the switch
        $oldProduct->traceLogs()->create([
            'user_id' => auth()->id(),
            'scanned_by' => auth()->id(),
            'event_type' => 'serial_switched',
            'action' => 'serial_switched',
            'location' => 'Factory',
            'notes' => "Serial number switched from {$request->old_serial} to {$request->new_serial}",
            'scanned_at' => now(),
        ]);

        return redirect()->route('products.switch')->with('success', "Serial number switched successfully from {$request->old_serial} to {$request->new_serial}");
    }

    public function updateRepairStatus(Request $request, Product $product)
    {
        $request->validate([
            'can_repair' => 'required|boolean',
            'repair_distributor_id' => 'required_if:can_repair,1|nullable|exists:distributors,id',
        ]);

        $product->update([
            'can_repair' => $request->can_repair,
            'repair_distributor_id' => $request->can_repair ? $request->repair_distributor_id : null,
            'repair_sent_at' => $request->can_repair ? now() : null,
            'status' => $request->can_repair ? 'in_distributor' : 'warranty_expired',
        ]);

        // Log the repair status change
        $product->traceLogs()->create([
            'user_id' => auth()->id(),
            'scanned_by' => auth()->id(),
            'event_type' => 'repair_status_updated',
            'action' => 'repair_status_updated',
            'location' => 'Admin',
            'notes' => $request->can_repair 
                ? "Product sent for repair to distributor: " . $product->repairDistributor->name 
                : "Product marked as cannot be repaired",
            'scanned_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Repair status updated successfully!'
        ]);
    }
}
