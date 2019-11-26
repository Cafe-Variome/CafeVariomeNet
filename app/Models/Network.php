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

    private function initiateResponse(int $status, array $data = null)
    {
        $this->response = new NetworkAPIResponse($status, $data);
    }

    private function setResponseMessage(string $message)
    {
        $this->response->setMessage($message);
    }

    public function getResponse(): NetworkAPIResponse
    {
        return $this->response;
    }

    public function getResponseArray(): array
    {
        return $this->response->toArray();
    }

    public function getResponseJSON(): string
    {
        return $this->response->toJSON();
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

    public function updateNetworks(array $data, array $conds = null) {
        if ($conds) {
            $this->builder->where($conds);
        }
        $this->builder->update($data);
    }

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
        
        try {
            $results = $this->builder->get()->getResultArray();
            $this->initiateResponse(1, $results);
        } catch (\Exception $ex) {
            $this->initiateResponse(0);
            $this->setResponseMessage($ex->getMessage());
        }
    }

    public function createNetwork(array $data, bool $uniquename = false): int
    {
        try {
            if ($uniquename) {
                $networks = $this->getNetworks('network_name', ['network_name' => $data['network_name']]);
                if (count($networks) > 0){
                    //network name is not unique
                    $this->initiateResponse(0);
                    $this->setResponseMessage('Network name is not unique.');
                    return -1;
                }
            }
            $this->builder->insert($data);
            $network_key =  (int)$this->db->insertID();
            $this->initiateResponse(1, ['network_key' => $network_key]);
            return $network_key;
        } catch (\Exception $ex) {
            error_log($ex->getMessage());
            $this->initiateResponse(0);
            $this->setResponseMessage($ex->getMessage());
            return -1;
        }
    }

    public function addInstallationToNetwork(string $installation_key, int $network_key): bool
    {
        try {
            $this->builder = $this->db->table('installations_networks');
            $this->builder->insert(['installation_key' => $installation_key, 'network_key' => $network_key]);
            $this->initiateResponse(1);
            return true;
        } catch (\Exception $ex) {
            error_log($ex->getMessage());
            $this->initiateResponse(0);
            $this->setResponseMessage($ex->getMessage());
            return false;
        }
        return false;
    }

    public function getNetworkThreshold(int $network_key): int
    {
        try {
            $networks = $this->getNetworks('network_threshold', ['network_key' => $network_key]);
            if (count($networks) == 1) {
                $this->initiateResponse(1, $networks[0]);
                return $networks[0]['network_threshold'];
            }
        } catch (\Exception $ex) {
            error_log($ex->getMessage());
            $this->initiateResponse(0);
            $this->setResponseMessage($ex->getMessage());
        }
        return -1;
    }

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

    public function leaveNetwork(string $installation_key, int $network_key): bool
    {
        $this->builder = $this->db->table('installations_networks');

        try {
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

            $this->initiateResponse(1);
            return true;
        } catch (\Exception $ex) {
            error_log($ex->getMessage());
            $this->initiateResponse(0);
            $this->setResponseMessage($ex->getMessage());
            return false;
        }
        return false;
    }

 }
   