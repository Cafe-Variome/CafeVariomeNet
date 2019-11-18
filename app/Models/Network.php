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

 class Network extends Model
 {
    protected $table      = 'networks';
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

    public function getNetworks(string $cols = null, array $conds = null, array $groupby = null, bool $isDistinct = false, int $limit = -1, int $offset = -1){
		
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

    public function createNetwork(array $data): int
    {
        try {
            $this->builder->insert($data);
            return $this->db->insertID();
        } catch (\Exception $ex) {
            error_log($ex->getMessage());
            return -1;
        }
    }

    public function addInstallationToNetwork(string $installation_key, int $network_key): bool
    {
        try {
            $this->builder = $this->db->table('installations_networks');
            $this->builder->insert(['installation_key' => $installation_key, 'network_key' => $network_key]);
            return true;
        } catch (\Exception $ex) {
            error_log($ex->getMessage());
            return false;
        }
        return false;
    }


 }
   