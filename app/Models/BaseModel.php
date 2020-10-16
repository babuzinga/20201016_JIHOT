<?php namespace App\Models;

use CodeIgniter\Model;

class BaseModel extends Model
{
  /**
   * @param array $data
   * @return array
   */
  protected function beforeUpdate(array $data)
  {
    $data['data']['updated_at'] = date('Y-m-d H:i:s');
    return $data;
  }

  /**
   * @param array $data
   * @return array
   */
  protected function generateUUID(array $data)
  {
    $data['data']['uuid'] = gen_uuid();
    return $data;
  }
}