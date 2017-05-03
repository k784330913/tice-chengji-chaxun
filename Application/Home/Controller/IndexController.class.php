<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
	//抓包获取的cookie值
	protected $JSESSIONID = 'JSESSIONID=FDF2A919E04F3F3AAEB3E7FDE7C3015A';
	//cookie所存放的文件名
	protected $cookie_file;
	//生成url
	public function url($id=0,$cate=0){
		echo U('Index/index',array('id' => $id, 'cate' => $cate));
	
		if(0){
			//设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
			$this->success('新增成功', '/User/index');
			} else {
			//错误页面的默认跳转页面是返回前一页，通常不需要设置
			$this->error('新增失败');
			}
	}
	public function _empty($a){
		$this->nocontroler($a);
	}
	protected function nocontroler($b){
		$this->show('404 not found','utf-8');
	}
	/**
	* 防止cookie过期的
	*
	*/
	public function curl(){
		$ch = curl_init();
		$curlopt = array(
							CURLOPT_URL => 'http://www.xytcypt.com/wx/studentData!getStudentData.action?openid=&studentid=2c95958d51c416050151c80620ab567b&areaCode=130000' ,
							CURLOPT_HEADER => 0 ,
							CURLOPT_RETURNTRANSFER => 1 ,
							CURLOPT_COOKIE => $this->JSESSIONID ,
							CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36 MicroMessenger/6.5.2.501 NetType/WIFI WindowsWechat QBCore/3.43.373.400 QQBrowser/9.0.2524.400' ,
						);
		curl_setopt_array($ch, $curlopt);
		// 抓取URL并把它传递给浏览器
		$data = curl_exec($ch);
		echo $data;
		// 关闭cURL资源，并且释放系统资源
		curl_close($ch);
	}
	/**
	*
	* 通过登录教务系统判断学号密码是否正确
	* 查询数据库中是否有数据，有就直接取出输出，
	* 若没有就先去教务系统里查询身份证号再调用this->ssid去查询studentid
	*
	*/
	public function index(){
		if (!IS_POST) {
			$this->assign('msg','');
			$this->display('index');
			exit();
		}
		$num = I('post.num');
		$pwd = I('post.pwd');
		if (!$this->jwxt($num,$pwd)) {
			$this->assign('msg','学号或密码错误！');
			$this->display('index');
			exit();
		}
		// $this->show('right!');
		// exit();
		$tice = D('tice');
		$where = array('xueid' => $num );
		$res = $tice->where($where)->select();
		if (empty($res)) {
			$idcard = $this->getidcard();
			$this->ssid($idcard);
		}else{
			//print_r($res[0]);
			$this->showout($res[0]);
		}
	}
	/**
	* 去教务系统验证学号和密码是否正确
	* 并把这次登录的cookie保存在protected $cookie_file里边
	*
	*/
	protected function jwxt($num = 0,$pwd = 0){
		//开始登陆
		$this->cookie_file = tempnam('./Application/Runtime/Temp/','hei');//保存cookie
		//setcookie('tmp',$cookie_file);
		$ch = curl_init();                //通过curl来登陆
		$curlopt = array(
							CURLOPT_URL => 'http://202.206.1.176/loginAction.do' ,
							CURLOPT_HEADER => 0 ,
							CURLOPT_POST => 1 ,
							CURLOPT_RETURNTRANSFER => 1 ,
							CURLOPT_COOKIEJAR => $this->cookie_file ,
							CURLOPT_POSTFIELDS => "zjh=$num&mm=$pwd" ,
						);
		curl_setopt_array($ch, $curlopt);
		$data = curl_exec($ch);
		$nodata="/\/img\/icon\/alert.gif/";
		curl_close($ch);
		//echo $this->cookie_file;
		if(preg_match($nodata, $data)) {
			return false;
		}else{
			return true;
		}
	}
	/**
	* 通过刚才获得的$this->cookie_file去get有身份证的那个页面，并用正则匹配出
	* 身份证号并返回
	*/
	protected function getidcard(){
		$ch = curl_init();                //通过curl来登陆
		$curlopt = array(
							CURLOPT_URL => 'http://202.206.1.176/xjInfoAction.do?oper=xjxx' ,
							CURLOPT_HEADER => 0 ,
							CURLOPT_RETURNTRANSFER => 1 ,
							CURLOPT_COOKIEFILE => $this->cookie_file ,
						);
		curl_setopt_array($ch, $curlopt);
		$data = curl_exec($ch);
		$patt = '/:&nbsp;\s+<\/td>\s+<td align="left" width="275">\s+([0-9X]{18})\s+<\/td>/';
		if(!preg_match_all($patt, $data, $matches)){
			$this->assign('msg','你登录的可能是假教务系统！');
			$this->display('index');
			exit();
		}
		//print_r($matches);
		return $matches[1][0];
	}
	/**
	* 模拟登录查询studentid
	* 可以用学号也可以用身份证号，当前是去教务系统中找出身份证号传过来查找studentid
	*
	*/
	protected function ssid($idcard = 0){
		$ch = curl_init();
		$curlopt = array(
							CURLOPT_URL => 'http://www.xytcypt.com/wx/userInfo!findStudentInfo.action' ,
							CURLOPT_HEADER => 0 ,
							CURLOPT_RETURNTRANSFER => 1 ,
							CURLOPT_COOKIE => $this->JSESSIONID ,
							CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36 MicroMessenger/6.5.2.501 NetType/WIFI WindowsWechat QBCore/3.43.373.400 QQBrowser/9.0.2524.400' ,
							CURLOPT_POSTFIELDS => "openid=&userType=01&areaCode=130000&no=$idcard" ,
						);
		curl_setopt_array($ch, $curlopt);
		// 抓取URL并把它传递给浏览器
		$data = curl_exec($ch);
		if (empty($data)) {
			$this->assign('msg','系统出错了，请等待管理员解决。');
			$this->display('index');
			exit();
		}
		$patt = '/dentid" value="([a-z0-9]{32})?" id="userInfo_stud/';
		preg_match_all($patt, $data, $res);
		$studentid = $res[1][0];
		if (empty($studentid)) {
			$this->assign('msg','抱歉，没有找到你的信息！');
			$this->display('index');
			exit();
		}
		// 关闭cURL资源，并且释放系统资源
		curl_close($ch);
		$openid = $this->getopenid();
		$this->mess($studentid,$openid);
	}
	/**
	* 通过studentid获取详细成绩信息并写入数据库
	*
	*/
	protected function mess($studentid = 0,$openid = 0){
		$ch = curl_init();
		$curlopt = array(
							CURLOPT_URL => "http://www.xytcypt.com/wx/studentData!getStudentData.action?openid=&studentid=$studentid&areaCode=130000" ,
							CURLOPT_HEADER => 0 ,
							CURLOPT_RETURNTRANSFER => 1 ,
							CURLOPT_COOKIE => $this->JSESSIONID ,
							CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36 MicroMessenger/6.5.2.501 NetType/WIFI WindowsWechat QBCore/3.43.373.400 QQBrowser/9.0.2524.400' ,
						);
		curl_setopt_array($ch, $curlopt);
		// 抓取URL并把它传递给浏览器
		$data = curl_exec($ch);
		// 关闭cURL资源，并且释放系统资源
		curl_close($ch);
		$str = $data;
		$patt = '/<span class="font_blue">(.+)<\/span>/';
		preg_match_all($patt, $str, $res);
		$message = array();
		$message['name'] = $res[1][0];
		$message['xueid'] = $res[1][1];
		$message['age'] = $res[1][2];
		if (empty($message['name'])){
			$this->assign('msg','抱歉，没有找到你的成绩！');
			$this->display('index');
			exit();
		}
		$patt = '/<span class="col-xs-12">\s+(.+?)\s/';
		preg_match_all($patt, $str, $res);
		$message['grade'] = $res[1][0];
		$patt = '/;<big class="font_yel">(.+?)<\/big>/';
		preg_match_all($patt, $str, $res);
		$message['total'] = $res[1][0];
		$message['totalstatus'] = $res[1][1];
		$patt = '/"><\/i>\s+<b>(.+?)<\/b>\s+<span>(.+?)<\/span>/';
		preg_match_all($patt, $str, $res);
		$message['height'] = $res[1][0];
		$message['heightstatus'] = $res[2][0];
		$message['weight'] = $res[1][1];
		$message['weightstatus'] = $res[2][1];
		$message['bmi'] = $res[1][2];
		$message['bmistatus'] = $res[2][2];
		$message['lung'] = $res[1][3];
		$message['lungstatus'] = $res[2][3];
		$message['fifty'] = $res[1][4];
		$message['fiftystatus'] = $res[2][4];
		$message['jump'] = $res[1][5];
		$message['jumpstatus'] = $res[2][5];
		$message['bond'] = $res[1][6];
		$message['bondstatus'] = $res[2][6];
		$message['run'] = $res[1][7];
		$message['runstatus'] = $res[2][7];
		$message['extre'] = $res[1][8];
		$message['extrestatus'] = $res[2][8];
		$message['studentid'] = $studentid;
		$message['openid'] = $openid;
		$message['add_time'] = time();
		//print_r($message);
		$tice = D('tice');
		if (!$tice->create($message)){
			exit($tice->getError());
		}
		$tice->add();
		$this->showout($message);
	}
	/**
	* 输出查询出结果的模板的方法
	* @param array $data
	*/
	protected function showout($data){
		$this->assign('data',$data);
		$this->display('showout');
	}
	/**
	* 获取微信公众号的openid的方法
	*
	*/
	protected function getopenid(){
		return ' ';
	}
}