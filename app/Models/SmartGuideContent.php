<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmartGuideContent extends Model
{
    protected $table = 'smart_guides_content';
    protected $primaryKey = 'id';
    protected $fillable = [
        'guide_id',
        'title',
        'category',
        'level',
        'roleOverview',
        'clientAcquisition',
        'pricingPackages',
        'toolsTemplates',
        'serviceStandards',
        'quickWins',
        'aiAutomationTips',
    ];

    protected $casts = [
        'clientAcquisition' => 'array',
        'pricingPackages' => 'array',
        'toolsTemplates' => 'array',
        'serviceStandards' => 'array',
        'quickWins' => 'array',
        'aiAutomationTips' => 'array',
    ];
}
