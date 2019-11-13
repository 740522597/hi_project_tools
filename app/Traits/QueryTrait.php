<?php
/**
 * Created by PhpStorm.
 * User: Haiyang
 * Date: 2019-11-14
 * Time: 00:23
 */

namespace App\Traits;

use App\ArchivedPlan;
use App\ArchivedTask;
use App\Models\HPPlan;
use App\Models\HPProject;
use App\Models\HPTask;
use App\TaskComment;
use Illuminate\Database\Eloquent\Model;

trait QueryTrait
{
    public function myQuery($query)
    {
        /** @var Model $model */
        return $query->where('created_by', auth()->user()->id);
    }
}