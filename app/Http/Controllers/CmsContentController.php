<?php

namespace App\Http\Controllers;

use App\Models\CmsContent;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CmsContentController extends Controller
{
    public function index()
    {
        $contents = CmsContent::orderBy('group')->orderBy('order')->paginate(20);
        $groups = CmsContent::select('group')->distinct()->pluck('group');
        return view('admin.cms.index', compact('contents', 'groups'));
    }

    public function create()
    {
        return view('admin.cms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:cms_contents,key',
            'label' => 'required|string|max:255',
            'type' => 'required|in:text,textarea,image,url,number',
            'value' => 'nullable|string',
            'group' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload if type is image
        if ($request->type === 'image' && $request->hasFile('image')) {
            $validated['value'] = $request->file('image')->store('cms', 'public');
        }

        $content = CmsContent::create($validated);

        // Log activity
        ActivityLog::create([
            'log_name' => 'cms',
            'description' => 'Created CMS content: ' . $content->label,
            'subject_type' => CmsContent::class,
            'subject_id' => $content->id,
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'event' => 'created',
            'properties' => json_encode(['content_key' => $content->key, 'group' => $content->group]),
        ]);

        alert()->success('Success', 'Content created successfully!');
        return redirect()->route('admin.cms.index');
    }

    public function edit(CmsContent $cms)
    {
        return view('admin.cms.edit', compact('cms'));
    }

    public function update(Request $request, CmsContent $cms)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:cms_contents,key,' . $cms->id,
            'label' => 'required|string|max:255',
            'type' => 'required|in:text,textarea,image,url,number',
            'value' => 'nullable|string',
            'group' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload if type is image
        if ($request->type === 'image' && $request->hasFile('image')) {
            // Delete old image
            if ($cms->value && $cms->type === 'image') {
                Storage::disk('public')->delete($cms->value);
            }
            $validated['value'] = $request->file('image')->store('cms', 'public');
        }

        $cms->update($validated);

        // Log activity
        ActivityLog::create([
            'log_name' => 'cms',
            'description' => 'Updated CMS content: ' . $cms->label,
            'subject_type' => CmsContent::class,
            'subject_id' => $cms->id,
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'event' => 'updated',
            'properties' => json_encode(['content_key' => $cms->key, 'group' => $cms->group]),
        ]);

        alert()->success('Success', 'Content updated successfully!');
        return redirect()->route('admin.cms.index');
    }

    public function destroy(CmsContent $cms)
    {
        $contentLabel = $cms->label;
        $contentKey = $cms->key;

        // Delete image if exists
        if ($cms->type === 'image' && $cms->value) {
            Storage::disk('public')->delete($cms->value);
        }

        $cms->delete();

        // Log activity
        ActivityLog::create([
            'log_name' => 'cms',
            'description' => 'Deleted CMS content: ' . $contentLabel,
            'subject_type' => CmsContent::class,
            'subject_id' => null,
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'event' => 'deleted',
            'properties' => json_encode(['content_key' => $contentKey]),
        ]);

        alert()->success('Deleted', 'Content deleted successfully!');
        return redirect()->route('admin.cms.index');
    }
}
