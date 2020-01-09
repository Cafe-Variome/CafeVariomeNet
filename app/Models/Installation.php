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
    
    public $response;

    public function __construct(ConnectionInterface &$db = null){
        if ($db != null) {
            $this->db =& $db;
        }
        else {
            $this->db = \Config\Database::connect();
        }
        $this->builder = $this->db->table($this->table);
    }

    public function createInstallation(string $name, string $base_url):string
    {
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

    function getInstallations(string $cols = null, array $conds = null, array $groupby = null, bool $isDistinct = false, int $limit = -1, int $offset = -1)
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

        $query = $this->builder->get()->getResultArray();
        return $query; 
    }

    public function getInstallationsByNetworkKey(int $network_key)
    {
        $this->builder->select('*');
        $this->builder->join('installations_networks', 'installations_networks.installation_key = '.$this->table.'.installation_key');
        $this->builder->where('installations_networks.network_key', $network_key);
        
        return $this->builder->get()->getResultArray();
    }
}
