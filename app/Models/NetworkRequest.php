<?php namespace App\Models;

/**
 * NetworkRequest.php
 * 
 * Created: 17/12/2019
 * 
 * @author Mehdi Mehtarizadeh
 * 
 */

use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;

class NetworkRequest extends Model
{
    protected $table      = 'network_requests';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $db;
    protected $builder;


    public function __construct(ConnectionInterface &$db = null){
        if ($db != null) {
            $this->db =& $db;
        }
        else {
            $this->db = \Config\Database::connect();
        }
        $this->builder = $this->db->table($this->table);

    }

    public function getNetworkRequests(string $cols = null, array $conds = null, array $groupby = null, bool $isDistinct = false, int $limit = -1, int $offset = -1)
    {
		if ($cols) {
            $this->builder->select($cols);
        }
        if ($conds) {
            $this->builder->where($conds);
        }
        if ($groupby) {
            $this->builder->groupBy($groupby);
        }
        if ($isDistinct) {
            $this->builder->distinct();
        }
        if ($limit > 0) {
            if ($offset > 0) {
                $this->builder->limit($limit, $offset);
            }
            $this->builder->limit($limit);
        }

        return $this->builder->get()->getResultArray();
    }

    public function createNetworkRequest(array $data)
    {
        $this->builder->insert($data);
    }


}
