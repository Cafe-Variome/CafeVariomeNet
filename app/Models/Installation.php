<?php namespace App\Models;

/**
 * Network.php
 * 
 * Created: 14/10/2019
 * 
 * @author Mehdi Mehtarizade
 * @author Gregory Warren
 * 
 */

use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;
use App\Libraries\Net\KeyGen;

class Installation extends Model
{
    protected $table      = 'installations';
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
    }

    public function createInstallation(string $name, string $base_url):string
    {
        $this->builder = $this->db->table($this->table);

        //generate installation key
        $keyGen = new KeyGen();
        $installation_key = $keyGen->generateInstallationKey();
        $data = [
                'installation_key' => $installation_key,
                'name' => $name,
                'base_url' => $base_url,
                'status' => 1
        ];

        $this->builder->insert($data);

        return $installation_key;
    }

    function getInstallations(string $cols = null, array $conds = null, array $groupby = null, bool $isDistinct = false, int $limit = -1, int $offset = -1){
		$this->builder = $this->db->table($this->table);
		
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

        $query = $this->builder->get()->getResultArray();
        return $query; 
    }
}
