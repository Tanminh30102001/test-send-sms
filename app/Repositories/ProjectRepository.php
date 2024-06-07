<?php
namespace App\Repositories;

use App\Models\Project;

class ProjectRepository extends BaseRepository
{
    public function __construct(Project $model)
    {
        parent::__construct($model);
    }

    // Các phương thức đặc thù cho Project có thể thêm ở đây
}
