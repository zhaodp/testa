<?php

use Elastica\Client;
use Elastica\Search;
use Elastica\Query;
use Elastica\Query\Bool;
use Elastica\Query\Term;
use Elastica\Document;
use Elastica\Exception\ResponseException;

Class ElasticsearchSynchronizer
{
    static public function addDocument($index, $type, $id, $doc)
    {
        $client = new Client(array('host' => 'search.n.edaijia.cn', 'port' => 80));
        $type = $client->getIndex($index)->getType($type);
        try {
            $response = $type->addDocument(new Document($id, $doc));
            if ($response->isOk()) {
                EdjLog::info("add document $id succeeded");
                return true;
            } else {
                EdjLog::info("add document $id failed");
                return false;
            }
        } catch (Exception $e) {
            EdjLog::error("add document $id failed with exception ".$e->getMessage());
            return false;
        }
    }

    static public function updateDocument($index, $type, $id, $doc)
    {
        $client = new Client(array('host' => 'search.n.edaijia.cn', 'port' => 80));
        try {
            $response = $client->getIndex($index)
                ->getType($type)
                ->updateDocument(new Document($id, $doc));

            if ($response->isOk()) {
                EdjLog::info("update document $id succeeded");
                return true;
            } else {
                EdjLog::info("update document $id failed");
                return false;
            }
        } catch (Exception $e) {
            EdjLog::error("update document $id failed with exception ".$e->getMessage());
            return false;
        }
    }

    static public function deleteDocument($index, $type, $id)
    {
        $client = new Client(array('host' => 'search.n.edaijia.cn', 'port' => 80));
        try {
            $response = $client->getIndex($index)->getType($type)->deleteById($id);
            if ($response->isOk()) {
                EdjLog::info("delete document $id succeeded");
                return true;
            } else {
                EdjLog::info("delete document $id failed");
                return false;
            }
        } catch (Exception $e) {
            EdjLog::error("delete document $id failed with exception ".$e->getMessage());
            return false;
        }
    }
}

