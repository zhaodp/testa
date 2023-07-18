<?php
class elasticsearchCommand extends LoggerExtCommand 
{
    public function actionElasticsearchTest()
    {
        $params = array();
        $params['hosts'] = array (
            '127.0.0.1:9200',
        );
        $client = new Elasticsearch\Client($params);
        //var_dump($client);

        $search_params = array();
        $search_params['index'] = 'jdbc';
        $search_params['type'] = 'jdbc';
        $json = '{  "query": {
                  "bool": {
                            "must": [
                                        { "match": { "address": "中钢" } }
                                              ]
                                                  }   
                    }
                }';

        $search_params['body'] = $json;
        $results = $client->search($search_params);
        if (isset($results, $results['hits'])) {
            foreach($results['hits']['hits'] as $doc) {
                //var_dump($doc);
                echo($doc['_source']['address']."\t".$doc['_source']['city_id'].PHP_EOL);     
            }
        }
        //var_dump($results);
    }

    public function actionElastica($address)
    {
        //str_replace('\\', '/', 'Elastica\\Query\\Term')
        $address_pool = array('address' => $address, 'city_id' => 1);

        //$elasticsearch_cluster = '127.0.0.1';
        //$elasticsearch_port = 9200;
        $elasticaClient = new \Elastica\Client(array(
            //'host' => $elasticsearch_cluster,
            'host' => 'search.edaijia.cn',
            //'port' => $elasticsearch_port
            'port' => 9200
        ));

        $search = new \Elastica\Search($elasticaClient);        
        $search ->addIndex('address_pool')->addType('address_pool');
            
        $query = new \Elastica\Query\Bool();

        $address_query = new \Elastica\Query\QueryString();
        $address_query->setDefaultField('address');
        $address_query->setQuery($address_pool['address']);
        $query->addMust($address_query);

        $city_id_query = new \Elastica\Query\Term();
        $city_id_query->setTerm('city_id', $address_pool['city_id']);
        $query->addMust($city_id_query);

        $query = \Elastica\Query::create($query);
        $query->setSource(["city_id","address", "lng", "lat"]);
        $query->setSize(50);

        $search->setQuery($query);
        /*
        $query = new \Elastica\Query\Match();
        //$field = 'address';
        $query->setFieldQuery('address', $address_pool['address']);
        //$query->setFieldQuery('city_id', $address_pool['city_id']);
        $query = \Elastica\Query::create($query);
        //$query->setSize(5);
        $query->setSource(["city_id","address"]);
        $search->setQuery($query);
        //$query->setFieldOperator($field, $operator);
         */

        
        /*
        $textQuery = new \Elastica\Query\Term();
        $textQuery->setTerm('address', $address_pool['address']);
        $query1 = \Elastica\Query::create($textQuery);
        $query1->setSize(5);
        $query1->setSource(["city_id","address"]);
        $search->setQuery($query1);
        */

        /*
        $query = array(
            "query" => array(
                //"bool" => array(
                    //"must" => array(
                        //"match" => array(
                            //array("address" => $address_pool['address']),
                            //array("city_id" => $address_pool['city_id'])
                        //)
                    //)
                //)
            ),
            "size" => 3,
            "_source" => array("city_id","address")
        );
        //$query['query'] = new stdclass();
        $query['query']['match'] =  new stdClass();
        $query['query']['match']->address = $address_pool['address'];
        $query_string = json_encode($query);
        //var_dump($query_string);
        //die;
        //$query = new \Elastica\Query\Builder($query_string);

        $search->setQuery($query_string);
         */

        $results = array();
        $totalResults = 0;
        try {
            $resultSet = $search->search(); 
            $results = $resultSet->getResults();
            $totalResults = $resultSet->getTotalHits();
        } catch (Exception $e) {
            EdjLog::error("query elasticsearch failed");
        }

        //var_dump($results);
        if ($totalResults <= 0) {
            //echo json_encode($results);
            echo('[]');
            return;
        }

        $poi_result = array();
        foreach ($results as $result) {
            $poi_result[] = array(
                'city_id' => $result->city_id,
                'address' => $result->address,
                'lng' => $result->lng,
                'lat' => $result->lat
            );
        }

        echo json_encode($poi_result);
    }
}
