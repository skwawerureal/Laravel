<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'subject',
        'html_content',
        'text_content',
        'variables',
        'status'
    ];

    protected $casts = [
        'variables' => 'array',
    ];

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    public function getVariableListAttribute()
    {
        return $this->variables ? array_keys($this->variables) : [];
    }

    public function renderContent(array $data = [])
    {
        $htmlContent = $this->html_content;
        $textContent = $this->text_content;

        foreach ($data as $key => $value) {
            $htmlContent = str_replace('{{' . $key . '}}', $value, $htmlContent);
            if ($textContent) {
                $textContent = str_replace('{{' . $key . '}}', $value, $textContent);
            }
        }

        return [
            'html' => $htmlContent,
            'text' => $textContent
        ];
    }
}
