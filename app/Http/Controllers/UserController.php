<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CmsUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        return view('users');
    }

    public function datatable(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'email',
            2 => 'nama',
            3 => 'image',
            4 => 'created_at',
        ];

        $totalData = CmsUser::count();

        $query = CmsUser::query();

        if (!empty($request->search['value'])) {
            $search = $request->search['value'];

            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $totalFiltered = $query->count();

        $orderColumnIndex = $request->order[0]['column'] ?? 0;
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        $orderDir = $request->order[0]['dir'] ?? 'desc';

        $users = $query
            ->orderBy($orderColumn, $orderDir)
            ->offset($request->start)
            ->limit($request->length)
            ->get();

        $data = [];

        foreach ($users as $user) {
            $imageUrl = $user->image
                ? asset('uploads/profile/' . $user->image)
                : asset('uploads/profile/default.png');

            $data[] = [
                'id' => $user->id,
                'email' => $user->email,
                'nama' => $user->nama,
                'image' => '<img src="' . $imageUrl . '" width="200" height="200" style="object-fit:cover;border-radius:8px;">',
                'action' => '
                    <button class="btn btn-warning btn-sm btn-edit" data-id="' . $user->id . '">Edit</button>
                    <button class="btn btn-danger btn-sm btn-delete" data-id="' . $user->id . '">Delete</button>
                ',
            ];
        }

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data,
        ]);
    }

    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|unique:cms_users,email',
        'nama' => 'required|string|max:255',
        'password' => 'required|string|min:6',
        'image' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
    ], [
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email sudah digunakan.',

        'nama.required' => 'Nama wajib diisi.',
        'nama.string' => 'Nama harus berupa teks.',
        'nama.max' => 'Nama maksimal 255 karakter.',

        'password.required' => 'Password wajib diisi.',
        'password.string' => 'Password harus berupa teks.',
        'password.min' => 'Password minimal 6 karakter.',

        'image.file' => 'File profile tidak valid.',
        'image.mimes' => 'Image profile harus berformat JPG, JPEG, atau PNG.',
        'image.max' => 'Ukuran image profile maksimal 2MB.',
    ]);

    $validator->after(function ($validator) use ($request) {
        if ($request->hasFile('image')) {
            if (!$this->isRealImage($request->file('image'))) {
                $validator->errors()->add(
                    'image',
                    'File yang diupload bukan image asli. Jangan hanya mengganti extension file menjadi .jpg, .jpeg, atau .png.'
                );
            }
        }
    });

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    $imageName = null;

    if ($request->hasFile('image')) {
        $imageName = time() . '_' . rand(1000, 9999) . '.' . $request->image->extension();
        $request->image->move(public_path('uploads/profile'), $imageName);
    }

    CmsUser::create([
        'email' => $request->email,
        'nama' => $request->nama,
        'password' => Hash::make($request->password),
        'image' => $imageName,
    ]);

    return response()->json([
        'status' => true,
        'message' => 'User berhasil dibuat.'
    ]);
}

    public function edit($id)
    {
        $user = CmsUser::findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $user
        ]);
    }

    public function update(Request $request, $id)
{
    $user = CmsUser::findOrFail($id);

    $validator = Validator::make($request->all(), [
        'email' => [
            'required',
            'email',
            Rule::unique('cms_users', 'email')->ignore($user->id),
        ],
        'nama' => 'required|string|max:255',
        'password' => 'nullable|string|min:6',
        'image' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
    ], [
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email sudah digunakan.',

        'nama.required' => 'Nama wajib diisi.',
        'nama.string' => 'Nama harus berupa teks.',
        'nama.max' => 'Nama maksimal 255 karakter.',

        'password.string' => 'Password harus berupa teks.',
        'password.min' => 'Password minimal 6 karakter.',

        'image.file' => 'File profile tidak valid.',
        'image.mimes' => 'Image profile harus berformat JPG, JPEG, atau PNG.',
        'image.max' => 'Ukuran image profile maksimal 2MB.',
    ]);

    $validator->after(function ($validator) use ($request) {
        if ($request->hasFile('image')) {
            if (!$this->isRealImage($request->file('image'))) {
                $validator->errors()->add(
                    'image',
                    'File yang diupload bukan image asli. Jangan hanya mengganti extension file menjadi .jpg, .jpeg, atau .png.'
                );
            }
        }
    });

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    $imageName = $user->image;

    if ($request->hasFile('image')) {
        if ($user->image && File::exists(public_path('uploads/profile/' . $user->image))) {
            File::delete(public_path('uploads/profile/' . $user->image));
        }

        $imageName = time() . '_' . rand(1000, 9999) . '.' . $request->image->extension();
        $request->image->move(public_path('uploads/profile'), $imageName);
    }

    $user->email = $request->email;
    $user->nama = $request->nama;
    $user->image = $imageName;

    if (!empty($request->password)) {
        $user->password = Hash::make($request->password);
    }

    $user->save();

    return response()->json([
        'status' => true,
        'message' => 'User berhasil diupdate.'
    ]);
}

    public function destroy($id)
    {
        $user = CmsUser::findOrFail($id);

        if ($user->image && File::exists(public_path('uploads/profile/' . $user->image))) {
            File::delete(public_path('uploads/profile/' . $user->image));
        }

        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'User berhasil dihapus'
        ]);
    }

    public function isRealImage($file): bool{
        if (!$file){
            return true;
        }

        $allowedMimeTypes = [
            'image/jpeg',
            'image/png'
        ];

        $realMimeType = $file->getMimeType();
        if (!in_array($realMimeType, $allowedMimeTypes)){
            return false;
        }

        $imageInfo = @getimagesize($file->getPathname());

        if($imageInfo === false) {
            return false;
        }

        return true;
    }
}