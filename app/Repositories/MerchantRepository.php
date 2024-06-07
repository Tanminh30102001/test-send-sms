<?php
namespace App\Repositories;

use App\Models\Merchant;

class MerchantRepository extends BaseRepository
{
    public function __construct(Merchant $model)
    {
        parent::__construct($model);
    }

    // Các phương thức đặc thù cho Merchant có thể thêm ở đây
}
