<?php
class DriverServiceTest extends BaseTest{
    public function testIsDriver(){
        $r = DriverService::isDriver(34954395894305);
        $this->assertNotNull($r);
        $this->assertFalse($r);
    }
    public function testRegister(){
        $r = DriverService::register(2341, 234);
        $this->assertNotNull($r);
    }

    public function testStatus(){
        $r = DriverStatus::model()->get(97);
    }
    public function testUpdateRedisAccount(){
        $account = array(1,2,3);
        $r = DriverService::updateRedisAccount(1, $account);
        $r = DriverService::status(1);
        $this->assertTrue($r->account === $account);
    }
}
