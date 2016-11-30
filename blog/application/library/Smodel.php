<?php

/**
 * Description of Smodel
 *
 * @author lidc
 * @date 2016-09-29 11:30
 */
class Smodel extends Medoo {

	protected $ssp_url = "http://zhi.9xu.com/interface/sdk";
    /**
     * 生成用户idSQL
     * @param type $id
     * @param type $pre
     */
    public function user_id_sql($id = null, $pre = "") {
        if (IS_ADMIN) {
            return "";
        } else if (IS_BUSINESS) {
            return " and " . $pre . 'user_id in (' . implode(',', $this->getUsersUnderBusiness()) . ') ';
        } else {
            return " and " . $pre . 'user_id=' . $id;
        }
    }

    /**
     * 獲取所有
     * @return [type] [description]
     */
    
   
    public function getAllUsers($where = "") {
		$condition =!empty($where) ? array('AND' => array('id' => $where)) : array();
    			$user = array();
    	$keysArr = array(
    			'type' => 'getUsers',
    			'where' => json_encode($condition),
    	);

    	$rs = json_decode(fn_get_contents($this->ssp_url, $keysArr, 'post'), true);
    	if ($rs['ret'] == 0 && isset($rs['data'])) {
    		$user = $rs['data'];
    	}
    	$userIds = array();
        if($user){
            foreach ($user as $k => $v){
                $userIds[$v['id']] = $v['username'];
            }
        }

        return $userIds;
    }
    /**
     * 获取所有apps信息
     */
    public function getAllApps($where = "") {
        $sql = sprintf('select id, app_name,platform,app_url,app_key, type from `%sapplication` where 1=1 %s', $this->prefix, $where);
        $result = $this->query($sql);
        $nresult = array();

        if ($result) {
            foreach ($result as $value) {
                $dic = new Model_Dic;
                $appTypeName = $dic->get("name", array('AND' => array('type' => 'APPTYPE', 'code' => $value['type'])));
//				$nresult[$value['id']] = array($value['app_url'], $value['app_name'], $appTypeName);
                $nresult[$value['id']]['app_name'] = $value['app_name'];
                $nresult[$value['id']]['app_url'] = $value['app_url'];
                $nresult[$value['id']]['type_name'] = $appTypeName !== false ? $appTypeName : "";
                $nresult[$value['id']]['app_key'] = $value['app_key'];
                $nresult[$value['id']]['platform'] = $value['platform'];
            }
        }
        return $nresult;
    }

    /**
     * 获取所有的广告位
     */
    public function getAllAdvertisings($where = "") {
        $sql = sprintf('SELECT * FROM `%sadvertising` where 1=1 %s', $this->prefix, $where);
        $result = $this->query($sql);
        $nresult = array();

        if ($result) {
            foreach ($result as $value) {
                $nresult[$value['id']] = $value;
            }
        }

        return $nresult;
    }

    /**
     * 获取所有的广告
     */
    public function getAllAdverts($where = "") {
        $sql = sprintf('SELECT * FROM `%sadvert` where 1=1 %s', $this->prefix, $where);
        $result = $this->query($sql);
        $nresult = array();

        if ($result) {
            foreach ($result as $value) {
                $nresult[$value['id']] = $value;
            }
        }

        return $nresult;
    }

    /**
     * 获取商务人员下的用户
     */
    public function getUsersUnderBusiness() {
        $sql = sprintf("select * from `%suser` where bid=%d", $this->prefix, getUserId());
        $result = $this->query($sql);

        $nresult = array();

        if ($result) {
            foreach ($result as $value) {
                $nresult[] = $value['id'];
            }
        }

        return $nresult;
    }

    /**
     * 查询用户
     */
	public function searchUsers($userName = "") {
	        $ids = array();
	        //远程获取ssp用户
	        $keysArr = array(
	            'type' => 'getUser',
	            'username' => $userName,
	        );
	        $rs = json_decode(fn_get_contents('http://zhi.9xu.com/interface/sdk', $keysArr, 'post'), true);
	        $list = array();
	        if ($rs['ret'] == 0 && $rs['data']) {
	            $list = $rs['data'];
	        }	      
	        $ids = isset($list['id']) ? $list['id'] : array();
	//        if ($list) {
	//            foreach ($list as $value) {
	//                $ids[] = $value['id'];
	//            }
	//        }
	  
	        return $ids;
	    }

    /**
     * 模糊查询用户
     */
    public function searchUsersLike($userName = "") {
        $ids = array();
        //远程获取ssp用户
        $keysArr = array(
            'type' => 'getUserLike',
            'username' => $userName,
        );
        $rs = json_decode(fn_get_contents('http://zhi.9xu.com/interface/sdk', $keysArr, 'post'), true);
        $list = array();
        if ($rs['ret'] == 0 && $rs['data']) {
            $list = $rs['data'];
        }
        $ids = $list ? $list : array();
        return $ids;
    }

    /**
     * 根据user_id查询用户
     */
    public function searchUsersId($user_id = -1) {
        $ids = array();
        //远程获取ssp用户
        $keysArr = array(
            'type' => 'getUserLike',
            'id' => $user_id,
        );
        $rs = json_decode(fn_get_contents('http://zhi.9xu.com/interface/sdk', $keysArr, 'post'), true);
        $list = array();
        if ($rs['ret'] == 0 && $rs['data']) {
            $list = $rs['data'];
        }
        $username = isset($list['username']) ? $list['username'] : '';
        return $username;
    }

    /**
     * 查询网站
     */
    public function searchWebs($webName = "") {
        $sql = "select id from `{$this->prefix}web` where web_url like '%$webName%'";
        $list = $this->query($sql);
        $ids = array();

        if ($list) {
            foreach ($list as $value) {
                $ids[] = $value['id'];
            }
        }

        return $ids;
    }

    /**
     * 查询网站
     */
    public function searchWebsByName($webName = "") {
        $sql = "select id from `{$this->prefix}web` where web_name like '%$webName%'";
        $list = $this->query($sql);
        $ids = array();

        if ($list) {
            foreach ($list as $value) {
                $ids[] = $value['id'];
            }
        }

        return $ids;
    }

}

?>

