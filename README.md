## 整理的比较简单的小框架
smartcms主要包含:路由、配置、ORM、日志、MVC、模板引擎等功能
## 主要特色有:
- 自定义路由列表优先，并兼容正则路由，二者可同时使用
- 配置文件根据服务器配置的环境变量自由切换，配置好后，同一套代码可以无损发布到生产环境和开发环境
- 比较精简，去掉了其他框架的一些不常用功能。增加一些常用的类文件，包括:
  1. 文件上传
  2. 分页
  3. 图片缩略图、水印
  4. Http请求
## 使用指南
  ### 为指定运行环境，需要配置服务器变量ENV,如下:
  1. Nginx用户,在php配置的location中，增加『fastcgi_param ENV 'DEV';』配置，其中，ENV的值DEV，可以自由指定，也可以不配置。以下说明中，所有的DEV都替换为自行设置的字符串。
  2. IIS和Apache用户，自行配置如上服务器变量。
  ### 上传文件到服务器，并配置网站根目录到public目录。如下配置url重写:
  
  ```
  location / {
	  root /web/xxx.com/public;
	  index index.html index.htm index.php;
	  try_files $uri $uri/ /index.php?$query_string;
  }
  ```
  
  3. 修改config目录下的配置文件，依赖于不同的服务器变量，配置不同的配置文件。以ENV = 'DEV'为例:
  - global.php为全局配置文件，其中global.php是未配置服务器变量的环境使用，global_dev.php为服务器环境变量为DEV时使用。以此类推，自行配置的其他服务器变量对应的全局配置文件为:"global_" + {服务器变量小写}".php"
  - config目录下全部文件都可以以此配置不同环境。其中CLI为后缀的，是php命令行脚本配置。
  - 修改database.php中的db及redis连接配置。
  - 修改global.php中的路由设置。默认配置是按照『分组』/『控制器』/『处理方法』的格式正则路由。如果有特殊自定义路由，请在config/route.php中指定请求的url与"分组/控制器/处理方法"的对应关系。系统会默认优先使用router中的路由。
  
  4. 编写控制器逻辑,在app/C/Index/Index.php中实现如下代码:
 
  ```
    <?php
  	//控制器命名空间，以App开头，按照路径到当前文件所在目录
  	namespace App\C\Index;
  	//使用模型文件，app/M/User.php;
  	use \App\M\User;
  	use \Request;
  	use \Response;
  	
  	class Index extends \App\C\BaseController{
	  	
	  	public function Index(Request $req, Response $resp){
		  	
		  	$page = max(1, intval($this->request->get('page')));
		  	//也可以使用下面的方式接收参数
		  	//$page = max(1, intval($req->get('page')));

		  	$user = new User();
		  	
		  	$userList = $user->getUserList($page, $pagesize);
		  	
		  	return $this->success($userList);
		}
  	}
  ```
  
  * 控制器中封装了request对象，可以从函数中传入，也可以使用$this->request对象，其中request对象包含以下方法:
    - get('参数名', '默认值')：接收$_GET参数
    - post('参数名', '默认值')：接收$_POST参数
    - server('参数名', '默认值')：接收$_SERVER参数
    - cookie('参数名', '默认值')：接收$_COOKIE参数
    - file('参数名', '索引值')：接收上传的第n个文件信息
    - session()：返回一个session对象，可以使用set/get/getId/setId等方法获取和设置$_SESSION值以及session id
    - isAjax()：返回bool值，根据http头的X-Requested-With来判定是否是ajax请求
    - isPost()：返回bool值，判定是否是post请求
    - isGet()：返回bool值，判定是否是get请求
    - getGroup()：返回当前请求的分组
    - getController()：返回当前请求的控制器
    - getAction()：返回当前请求的处理方法
    - startSession()：手动启动session
    - 静态方法:getInstance()：获取当前request对象
    
  5. 实现User模型app/M/User.php,代码如下
  ```
    <?php
    namespace App\M;
    	
    class User extends Model{
	    	
	    //指定使用member表，依赖于配置，数据库配置中可以统一指定表前缀。实际使用的表名为:前缀 + $table
	    public static $table = 'member';
	    	
	    public function getUserList($page, $pagesize){
		    	
		    return $this->page($page)->pagesize($pagesize)->getRows();
	    }
    }
  ```
    
  * 模型中封装了以下方法:
    - where('id = ? AND status = ?', [100, 1])：获取指定id位100，状态为1的条件，参数2可以省略
    - andWhere('id = ? AND status = ?', [100, 1])：同where
    - orWhere('id = ? AND status = ?', [100, 1])：指定or条件，参数2可以省略，where/andWhere/orWhere可以连续使用，如:
      + $this->where('id = ?', [100]);
  	  + $this->andWhere('status = ?', [1]);
  	  + $this->orWhere('id = 101');
  	  + 或者$this->where('id = ?', [100])->where('status = ?', [1])->orWhere('id = 101');
  	  + 最终生成的条件为:(id = 100 AND status = 1) OR (id = 101)
    - insert($data)：写入数据，$data可以为单条或者多条数据
    - update($data)：搭配where使用，更新指定的记录
    - delete()：搭配where使用，删除指定的记录
    - getRow()：搭配where使用，获取一行记录
    - getRows()：搭配where使用，获取多行记录
    - getCount()：统计记录条数，相当于SELECT COUNT(1) AS num FROM xxx
    - getValue()：获取字段的一个值
    - fields(['id', 'name'])：查询指定的字段
    - orderBy(['id DESC', 'createTime ASC'])：排序
    - groupBy(['gender', 'age'])：分组
    - having('count(1) > 1')：having过滤
    - getSqls()：打印查询的sql
    - getError()：返回错误信息
      
  6. 在浏览器中，使用http://127.0.0.1 访问你的网站
    
    
    
  

