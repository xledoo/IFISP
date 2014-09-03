
	/*
	*	@GitHub:IFISP
	*	@RemoteURL:git@github.com:xledoo/IFISP.git
	*	@Author:alipiapia
	*	@Date:03/09/2014
	*	@Title:Summary For Progress
	*/
	
	
	======	用户模块	======
	
		
		-RegisterController(注册)
			（完成）
			
		-LoginController(登录)
			（完成）
			
		-ProfileController(用户资料)
			--基本资料	（页面完成/基本信息可修改）
			--密码安全	（页面完成/密码可修改）
			--我的头像	（页面完成/头像可修改）
			--我的积分	（积分显示）
			
		-CreditController(信用认证)
			--等级/认证中心
				（页面完成）
				
				
	
	
	Pro:		
			a:	密码修改时会很慢，全局变量$_G会为空，原因可能是Nginx服务器的问题;
			b:	平台旧版本注册成功用户表(uc_members)未导入;
			c:	跳转到前台首页Home/Index/index时因为JS插件问题需要刷新页面，否则卡住无法进行登录注册操作;