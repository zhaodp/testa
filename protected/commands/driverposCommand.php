<?php

class driverposCommand extends LoggerExtCommand {

	/**
	 * 测试环境是否正常
	 */
	public function actionTest()
    {
        echo "Fuck";
	}


	public function parse($line)
    {
        $pos = strpos($line, " ");
        $method = trim(substr($line, 0, $pos));
        $params = trim(substr($line, $pos));
        //echo "$line|$pos|$method|$params\n";
        if (strlen($method) == 0 || strlen($params) == 0) {
            return false;
        } else {
            //echo "method=$method\n";
            //echo "params=$params\n";

            $params = json_decode($params, true);
            if ($params) {
                return array(
                             'method' => $method,
                             'params' => $params,
                             );
            } else {
                return false;
            }

        }
            
	}

	public function run_task($task)
    {
		$method=$task['method'];
		$params=$task['params'];
        if ($method == 'driver_position_track') {
            //EdjLog::info("------");
            $method = 'driver_position_track_ope';
            $queue_process=new QueueProcess();
            call_user_func_array(array(
                                       $queue_process,
                                       $method
                                       ), array(
                                                $params
                                                ));

        } else if ($method == 'driver_batch_position_track' || $method == 'driverUploadTrackJob') {
            //EdjLog::info("------");
            $method = 'driver_batch_position_track_ope';
            $queue_process=new QueueProcess();
            call_user_func_array(array(
                                       $queue_process,
                                       $method
                                       ), array(
                                                $params
                                                ));



        } else {
            EdjLog::info("not save");
        }


    }

    // format: 2014/03/19 10
	public function pick($line, $hour)
    {
        $tm = substr($line, 0, 13);
        if ($tm == $hour) {
            $pos = strpos($line, "METHOD:");
            if ($pos) {
                $tmp = substr($line, $pos+7);
                $pos = strpos($tmp, "PARAMS:");
                if ($pos) {
                    //echo "1>$pos $tmp\n";
                    $method = trim(substr($tmp, 0, $pos));
                    $params = trim(substr($tmp, $pos+7));
                    //echo "2>$method $params\n";
                
                    return sprintf("%s %s", $method, $params);
                } else {
                    return false;
                }
                
            } else {
                return false;
            }

        } else {
            return false;
        }
    }



	public function actionSaveHour($hour, $begin_line)
    {
        $line_num = 1;
        $fp = fopen("php://stdin", "r");
        while($input = fgets($fp)) {
            // print the line numper
            //EdjLog::info("linenum=$line_num");
            if ($line_num++ < $begin_line) {
                //EdjLog::info("ignore");
                continue;
            }

            try {
                $pick = $this->pick($input, $hour);
                if ($pick) {
                    //echo "$pick\n";
                    $task = $this->parse($pick);
                    //var_dump($task);
                    if ($task) {
                        EdjLog::info("$hour linenum=".($line_num-1));
                        $this->run_task($task);
                    }
                }

            } catch (Exception $e) {
                $errmsg = $e->getMessage();
                EdjLog::warning("execption save:$input  err:$errmsg");
            }

        }


	}




	public function actionSave()
    {
        $line_num = 1;
        $fp = fopen("php://stdin", "r");
        while($input = fgets($fp)) {
            // print the line numper
            EdjLog::info("analyse $line_num");
            $line_num++;
            try {
                $task = $this->parse($input);
                //var_dump($task);
                if ($task) {
                    $this->run_task($task);
                }

            } catch (Exception $e) {
                $errmsg = $e->getMessage();
                EdjLog::warning("execption save:$input  err:$errmsg");
            }

        }

	}

}
