<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsContent extends Model
{
    protected $fillable = [
        'key',
        'label',
        'type',
        'value',
        'group',
        'description',
        'order',
    ];

    // Helper method to get content by key
    public static function getContent($key, $default = '')
    {
        $content = self::where('key', $key)->first();
        return $content ? $content->value : $default;
    }

    // Helper method to get all contents by group
    public static function getByGroup($group)
    {
        return self::where('group', $group)->orderBy('order')->get();
    }
}
