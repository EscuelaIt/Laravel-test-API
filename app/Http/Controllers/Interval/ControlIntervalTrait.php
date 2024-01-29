<?php

namespace App\Http\Controllers\Interval;

use App\Models\Project;
use App\Models\Category;

trait ControlIntervalTrait {
  protected $intervalValidationRules = [
    'project_id' => 'required|integer|exists:projects,id'
  ];

  protected $intervalCategoryValidationRules = [
    'category_id' => 'required|integer|exists:categories,id',
    'attached' => 'nullable|boolean',
  ];


  protected function isProjectIdValid($user, $projectId) {
    $project = Project::find($projectId);
    if(! $project) {
      return false;
    }
    return $user->can('update', $project);
  }

  protected function isCategoryIdValid($user, $category) {
    if(! $category) {
      return false;
    }
    $category = Category::find($category);
    if(! $category) {
      return false;
    }
    return $user->can('update', $category);
  }
}