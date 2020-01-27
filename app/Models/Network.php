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
    protected $primaryKey = 'network_key';

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

        return $this->builder->get()->getResultArray();
    }

    public function updateNetworks(array $data, array $conds = null):bool {
        if ($conds) {
            $this->builder->where($conds);
        }
        return $this->builder->update($data);
    }

    /**
     * @deprecated
     */
    public function getNetwork(int $network_key): array
    {
        try {
            $network = $this->getNetworks(null, ['network_key' => $network_key]);

            if (count($network) == 1) {
                $this->initiateResponse(1, $network[0]);
                return $network[0];
            }
            else {
                $this->initiateResponse(1);
            }
        } catch (\Exception $ex) {
            $this->initiateResponse(0);
            $this->setResponseMessage($ex->getMessage());
        }

        return [];
    }

    public function getNetworksByInstallationKey(string $installationKey)
    {
        $this->builder->select('*');
        $this->builder->join('installations_networks', 'installations_networks.network_key = '.$this->table.'.network_key');
        $this->builder->where('installations_networks.installation_key', $installationKey);
        
        return $this->builder->get()->getResultArray();
    }

    public function createNetwork(array $data): int
    {
        $this->builder->insert($data);
        $network_key =  (int)$this->db->insertID();
        return $network_key;
    }

    public function addInstallationToNetwork(string $installation_key, int $network_key)
    {
        $this->builder = $this->db->table('installations_networks');
        $this->builder->insert(['installation_key' => $installation_key, 'network_key' => $network_key]);
    }

    /**
     * @deprecated
     */
    public function setNetworkThreshold(int $network_key, int $network_threshold): bool
    {
        $data = ['network_threshold' =>  $network_threshold];
        try {
            $this->updateNetworks($data, ['network_key' => $network_key]);
            $this->initiateResponse(1);
            return true;
        } catch (\Exception $ex) {
            error_log($ex->getMessage());
            $this->initiateResponse(0);
            $this->setResponseMessage($ex->getMessage());
        }
        return false;
    }

    public function leaveNetwork(string $installation_key, int $network_key)
    {
        $this->builder = $this->db->table('installations_networks'); //set the table to installation_networks
        $this->builder->where(['network_key' => $network_key]);

        $installationCount = $this->builder->countAllResults();

        $this->builder->where(['installation_key' => $installation_key, 'network_key' => $network_key]);
        $this->builder->delete();

        if ($installationCount == 1) {
            //Delete the network entity as well.
            $this->builder = $this->db->table($this->table);
            $this->builder->where(['network_key' => $network_key]);
            $this->builder->delete();
        }
    }

    public function getAvailableNetworks(string $installation_key): array
    {
        $this->builder->select($this->table .'.network_key');
        $this->builder->join('installations_networks', 'installations_networks.network_key = '.$this->table.'.network_key');
        $this->builder->where('installations_networks.installation_key', $installation_key);

        $networksInstallationIn = $this->builder->get()->getResultArray();
        $networkKeys = [];

        foreach ($networksInstallationIn as $networkKey) {
           array_push($networkKeys, $networkKey['network_key']);
        }

        $this->builder->select('*');
        if (count($networkKeys) != 0) {
            $this->builder->whereNotIn('network_key', $networkKeys);
        }
        
        return $this->builder->get()->getResultArray();
    }

 }
   