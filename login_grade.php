<?php 
    session_start();
    $id=session_id();
    $_SESSION['id']=$id;

    require_verify_code();  //��ȡ��֤��

    function require_verify_code(){
        $cookie = dirname(__FILE__) . '/cookie/'.$_SESSION['id'].'.txt';    //cookie·��  
        $verify_code_url = "http://gdjwgl.bjut.edu.cn/CheckCode.aspx";      //��֤���ַ
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $verify_code_url);
        curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie);                     //����cookie
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $img = curl_exec($curl);                                            //ִ��curl
        curl_close($curl);

        $fp = fopen("verifyCode.jpg","w");                                  //�ļ���
        fwrite($fp,$img);                                                   //д���ļ� 
        fclose($fp);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="gbk">
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
	<title>Ұ����������</title>
	<link rel="stylesheet" href="/weui/dist/style/weui.min.css"/>
    <script src="//cdn.bootcss.com/jquery/3.0.0/jquery.min.js"></script>
	<script src="/js/login_score.js"></script>  
</head>
<body>
    <!-- ʹ�õ���WeUI -->
	<form action="http://yourip/require_grade.php" method="post">
		<div class="weui_cells_title">��¼��Ϣ</div>
		<div class="weui_cells weui_cells_form">
			<div class="weui_cell">
				<div class="weui_cell_hd">
					<label class="weui_label">ѧ��</label>
				</div>
				<div class="weui_cell_bd weui_cell_primary">
					<input class="weui_input" name="account" type="text" placeholder="������ѧ��">
				</div>
			</div>

			<div class="weui_cell">
				<div class="weui_cell_hd">
					<label class="weui_label">����</label>
				</div>
				<div class="weui_cell_bd weui_cell_primary">
					<input class="weui_input" name="password" type="password" placeholder="�������������(gdjwgl.bjut.edu.cn)">
				</div>
			</div>

            <div class="weui_cell weui_cell_select weui_select_after">
                <div class="weui_cell_hd">
                    ѧ��
                </div>
                <div class="weui_cell_bd weui_cell_primary">
                    <select class="weui_select" name="current_year">
                        <option value="2015-2016">2015-2016</option>
                        <option value="2014-2015">2014-2015</option>
                        <option value="2013-2014">2013-2014</option>
                    </select>
                </div>
            </div>

            <div class="weui_cell weui_cell_select weui_select_after">
                <div class="weui_cell_hd">
                    ѧ��
                </div>
                <div class="weui_cell_bd weui_cell_primary">
                    <select class="weui_select" name="current_term">
                        <option value="1">1</option>
                        <option selected="" value="2">2</option>
                        <option value="3">3</option>
                    </select>
                </div>
            </div>

			<div class="weui_cell weui_vcode">
				<div class="weui_cell_hd"><label class="weui_label">��֤��</label></div>
				<div class="weui_cell_bd weui_cell_primary">
					<input class="weui_input" name="verify_code" type="text" placeholder="��������֤��"/>
				</div>
				<div class="weui_cell_ft">
					<img id="verify_code" src="verifyCode.jpg" onclick="update_verify_code()" />
				</div>
			</div>

		</div>

        <!-- loading toast -->
            <div id="loadingToast" class="weui_loading_toast" style="display:none;">
                <div class="weui_mask_transparent"></div>
                <div class="weui_toast">
                    <div class="weui_loading">
                        <div class="weui_loading_leaf weui_loading_leaf_0"></div>
                        <div class="weui_loading_leaf weui_loading_leaf_1"></div>
                        <div class="weui_loading_leaf weui_loading_leaf_2"></div>
                        <div class="weui_loading_leaf weui_loading_leaf_3"></div>
                        <div class="weui_loading_leaf weui_loading_leaf_4"></div>
                        <div class="weui_loading_leaf weui_loading_leaf_5"></div>
                        <div class="weui_loading_leaf weui_loading_leaf_6"></div>
                        <div class="weui_loading_leaf weui_loading_leaf_7"></div>
                        <div class="weui_loading_leaf weui_loading_leaf_8"></div>
                        <div class="weui_loading_leaf weui_loading_leaf_9"></div>
                        <div class="weui_loading_leaf weui_loading_leaf_10"></div>
                        <div class="weui_loading_leaf weui_loading_leaf_11"></div>
                    </div>
                    <p class="weui_toast_content">���ݼ�����</p>
                </div>
            </div>

        <script>
            //Loading��ת�ջ�
            $(function() {
                $('#showLoadingToast').click(function() {
                    $('#loadingToast').fadeIn().delay(5000).fadeOut();
                });
            })
        </script>

		<input class="weui_btn weui_btn_primary" type="submit" value="��ѯ" id="showLoadingToast"/>
	</form>		

	<article class="weui_article">
		&nbsp;<h1><i class="weui_icon_success_circle"></i>&nbsp;�˺ź����벻�ᱻ�����������ʹ�á�<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;�ܼ�Ȩƽ���ֺ���ƽ��GPA������ֻ��û����רҵ/���޵�ͬѧ��Ч<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;���ݽ����ο������Խ���ϵͳΪ׼��<br/><br/>
		<section>
Powered by Dept.7<br/>Contact: wyf0615@emails.bjut.edu.cn
        </section>
    </article>

    <script src="weui/dist/example/zepto.min.js"></script>
    <!-- CNZZ��ͳ��DIV���Է������� -->
    </body>
</html>