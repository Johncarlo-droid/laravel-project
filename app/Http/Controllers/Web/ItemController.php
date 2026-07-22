<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    public const FLOOR_OPTIONS = ['4th Floor', '5th Floor', '6th Floor', '7th Floor', '8th Floor'];

    public const ASSET_TYPE_OPTIONS = [
        'Electronics' => ['Desktop Computer', 'Laptop', 'Monitor', 'Printer', 'Scanner', 'Projector', 'Television', 'Router / Switch', 'UPS', 'Server', 'Speaker / Sound System', 'Other Electronics'],
        'Furniture' => ['Chair', 'Table', 'Cabinet', 'Whiteboard', 'Shelf', 'Podium', 'Other Furniture'],
        'Office Supplies' => ['Consumable Supply', 'Stationery', 'Paper Product', 'Other Supply'],
    ];

    public function index(Request $request)
    {
        $type = strtoupper((string) $request->get('type', 'CAPEX'));
        if (!in_array($type, ['CAPEX', 'OPEX'], true)) {
            $type = 'CAPEX';
        }
        $user = auth()->user();
        if ($user?->isRequestor()) {
            $type = 'OPEX';
        }

        $search = trim((string) $request->get('search'));
        $stockFilter = $request->get('stock_filter');

        $items = Item::with('category')
            ->where('item_type', $type)
            ->when($user?->isRequestor(), function ($query) use ($type) {
                if ($type === 'OPEX') {
                    $query->where('is_active', true)
                        ->where('availability_status', '!=', 'Out of Stock')
                        ->where('quantity', '>', 0);
                }
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('item_code', 'like', "%{$search}%")
                        ->orWhere('qr_value', 'like', "%{$search}%")
                        ->orWhere('room_assigned', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%")
                        ->orWhere('specifications', 'like', "%{$search}%");
                });
            })
            ->when($stockFilter === 'low', fn ($q) => $q->where('availability_status', 'Limited Stock'))
            ->when($stockFilter === 'available', fn ($q) => $q->where('availability_status', 'Available'))
            ->when($stockFilter === 'out', fn ($q) => $q->where('availability_status', 'Out of Stock'))
            ->when($stockFilter === 'active', fn ($q) => $q->where('is_active', true))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('items.index', compact('items', 'type', 'search', 'stockFilter'));
    }

    public function create(Request $request)
    {
        abort_unless(auth()->user()->canManageInventory(), 403);
        $categories = ItemCategory::orderBy('name')->get();
        $type = strtoupper((string) $request->get('type', 'CAPEX'));
        if (!in_array($type, ['CAPEX', 'OPEX'], true)) {
            $type = 'CAPEX';
        }
        $suggestedCode = $type === 'OPEX' ? $this->generateItemCode($type) : null;
        $floors = self::FLOOR_OPTIONS;
        $assetTypeOptions = self::ASSET_TYPE_OPTIONS;
        return view('items.create', compact('categories', 'type', 'suggestedCode', 'floors', 'assetTypeOptions'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->canManageInventory(), 403);
        $data = $request->validate([
            'category_id' => ['nullable','exists:item_categories,id'],
            'new_category' => ['nullable', 'string', 'max:100'],
            'item_code' => ['nullable','string','max:100','unique:items,item_code'],
            'name' => ['required','string','max:200'],
            'item_type' => ['required','in:CAPEX,OPEX'],
            'description' => ['nullable','string'],
            'specifications' => ['nullable','string'],
            'acquisition_date' => ['nullable','date'],
            'assigned_department' => ['nullable','string','max:150'],
            'asset_type_name' => ['nullable','string','max:150'],
            'acquisition_date' => ['nullable','date'],
            'assigned_department' => ['nullable','string','max:150'],
            'asset_type_name' => ['nullable','string','max:150'],
            'quantity' => ['nullable','integer','min:0'],
            'unit' => ['nullable','string','max:50'],
            'unit_price' => ['nullable','numeric','min:0'],
            'brand' => ['nullable','string','max:100'],
            'low_stock_threshold' => ['nullable','integer','min:0'],
            'availability_status' => ['nullable', 'in:Available,Limited Stock,Out of Stock'],
            'room_assigned' => ['nullable','string','max:100'],
            'floor' => ['required_if:item_type,CAPEX','nullable','string','in:'.implode(',', self::FLOOR_OPTIONS)],
            'image_file' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:4096'],
            'is_active' => ['nullable','boolean'],
        ]);

        if (!empty($data['new_category'])) {
            $category = ItemCategory::firstOrCreate(['name' => trim($data['new_category'])]);
            $data['category_id'] = $category->id;
        }
        unset($data['new_category']);

        if ($request->hasFile('image_file')) {
            $data['image_path'] = $this->storeUploadedImage($request->file('image_file'));
        }
        unset($data['image_file']);

        $data['is_active'] = $request->boolean('is_active', true);
        if ($data['item_type'] === 'CAPEX') {
            // Server-generated, floor-based, collision-checked — never trust a typed/posted code for CAPEX.
            $data['item_code'] = $this->generateItemCode('CAPEX', $data['floor'] ?? null);
        } elseif (empty($data['item_code'])) {
            $data['item_code'] = $this->generateItemCode($data['item_type']);
        }

        if ($data['item_type'] === 'CAPEX') {
            $data['quantity'] = 1;
            $data['name'] = $data['name'] ?: $data['item_code'];
            $data['unit'] = ($data['unit'] ?? '') ?: 'unit';
            $data['unit_price'] = $data['unit_price'] ?? 0;
            $data['low_stock_threshold'] = 0;
            $data['availability_status'] = 'Available';
        } else {
            $data['quantity'] = $data['quantity'] ?? 0;
            $data['unit'] = ($data['unit'] ?? '') ?: 'pcs';
            $data['unit_price'] = $data['unit_price'] ?? 0;
            $data['low_stock_threshold'] = $data['low_stock_threshold'] ?? 0;
            $data['availability_status'] = $data['availability_status'] ?? 'Available';
        }
        $data['qr_value'] = $data['item_type'] === 'CAPEX' ? $data['item_code'] : null;
        if ($data['availability_status'] === 'Out of Stock') {
            $data['quantity'] = 0;
        }
        if ($data['item_type'] === 'OPEX') {
            $data['room_assigned'] = null;
        }

        Item::create($data);
        return redirect()->route('items.index', ['type' => $data['item_type']])->with('success', 'Item created successfully.');
    }

    public function edit(Item $item)
    {
        abort_unless(auth()->user()->canManageInventory(), 403);
        $categories = ItemCategory::orderBy('name')->get();
        $type = $item->item_type;
        $floors = self::FLOOR_OPTIONS;
        $assetTypeOptions = self::ASSET_TYPE_OPTIONS;
        return view('items.edit', compact('item','categories', 'type', 'floors', 'assetTypeOptions'));
    }

    public function update(Request $request, Item $item)
    {
        abort_unless(auth()->user()->canManageInventory(), 403);
        $data = $request->validate([
            'category_id' => ['nullable','exists:item_categories,id'],
            'new_category' => ['nullable', 'string', 'max:100'],
            'item_code' => ['nullable','string','max:100','unique:items,item_code,'.$item->id],
            'name' => ['required','string','max:200'],
            'item_type' => ['required','in:CAPEX,OPEX'],
            'description' => ['nullable','string'],
            'specifications' => ['nullable','string'],
            'acquisition_date' => ['nullable','date'],
            'assigned_department' => ['nullable','string','max:150'],
            'asset_type_name' => ['nullable','string','max:150'],
            'quantity' => ['nullable','integer','min:0'],
            'unit' => ['nullable','string','max:50'],
            'unit_price' => ['nullable','numeric','min:0'],
            'brand' => ['nullable','string','max:100'],
            'low_stock_threshold' => ['nullable','integer','min:0'],
            'availability_status' => ['nullable', 'in:Available,Limited Stock,Out of Stock'],
            'room_assigned' => ['nullable','string','max:100'],
            'floor' => ['required_if:item_type,CAPEX','nullable','string','in:'.implode(',', self::FLOOR_OPTIONS)],
            'image_file' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:4096'],
            'remove_image' => ['nullable','boolean'],
            'is_active' => ['nullable','boolean'],
        ]);
        if (!empty($data['new_category'])) {
            $category = ItemCategory::firstOrCreate(['name' => trim($data['new_category'])]);
            $data['category_id'] = $category->id;
        }
        unset($data['new_category']);

        if ($request->boolean('remove_image')) {
            $this->deleteUploadedImage($item->image_path);
            $data['image_path'] = null;
        }

        if ($request->hasFile('image_file')) {
            $this->deleteUploadedImage($item->image_path);
            $data['image_path'] = $this->storeUploadedImage($request->file('image_file'));
        }
        unset($data['image_file'], $data['remove_image']);

        $data['is_active'] = $request->boolean('is_active', false);
        if (empty($data['item_code'])) {
            $data['item_code'] = $this->generateItemCode($data['item_type']);
        }

        if ($data['item_type'] === 'CAPEX') {
            $data['quantity'] = 1;
            $data['name'] = $data['name'] ?: $data['item_code'];
            $data['unit'] = ($data['unit'] ?? '') ?: 'unit';
            $data['unit_price'] = $data['unit_price'] ?? 0;
            $data['low_stock_threshold'] = 0;
            $data['availability_status'] = 'Available';
        } else {
            $data['quantity'] = $data['quantity'] ?? 0;
            $data['unit'] = ($data['unit'] ?? '') ?: 'pcs';
            $data['unit_price'] = $data['unit_price'] ?? 0;
            $data['low_stock_threshold'] = $data['low_stock_threshold'] ?? 0;
            $data['availability_status'] = $data['availability_status'] ?? 'Available';
        }
        $data['qr_value'] = $data['item_type'] === 'CAPEX' ? $data['item_code'] : null;
        if ($data['availability_status'] === 'Out of Stock') {
            $data['quantity'] = 0;
        }
        if ($data['item_type'] === 'OPEX') {
            $data['room_assigned'] = null;
        }
        $item->update($data);
        return redirect()->route('items.index', ['type' => $data['item_type']])->with('success', 'Item updated successfully.');
    }

    public function show(Item $item)
    {
        if (auth()->user()?->isRequestor() && $item->item_type === 'OPEX' && $item->isOutOfStock()) {
            abort(404);
        }
        $item->load('category');
        return view('items.show', compact('item'));
    }

    public function destroy(Item $item)
    {
        abort_unless(auth()->user()->canManageInventory(), 403);
        $type = $item->item_type;
        $this->deleteUploadedImage($item->image_path);
        $item->delete();
        return redirect()->route('items.index', ['type' => $type])->with('success', 'Item deleted successfully.');
    }


    private function generateItemCode(string $type, ?string $floor = null): string
    {
        if (strtoupper($type) === 'CAPEX') {
            $floorDigit = preg_replace('/\D/', '', $floor ?: self::FLOOR_OPTIONS[0]);
            $floorDigit = $floorDigit !== '' ? $floorDigit : '4';
            do {
                $candidate = $floorDigit . '-' . str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            } while (Item::where('item_code', $candidate)->exists());
            return $candidate;
        }

        $prefix = 'OPEX-AMO-';
        $latest = Item::where('item_code', 'like', $prefix.'%')->pluck('item_code');
        $max = 0;
        foreach ($latest as $code) {
            $suffix = str_replace($prefix, '', $code);
            $number = (int) preg_replace('/\D/', '', $suffix);
            if ($number > $max) {
                $max = $number;
            }
        }
        $next = $max + 1;
        return $prefix.str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }

    private function storeUploadedImage($file): string
    {
        $directory = public_path('uploads/items');
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $filename = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
        $file->move($directory, $filename);

        return 'uploads/items/'.$filename;
    }

    private function deleteUploadedImage(?string $imagePath): void
    {
        if (!$imagePath || str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://') || str_starts_with($imagePath, 'data:image/')) {
            return;
        }

        $fullPath = public_path($imagePath);
        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }
}
