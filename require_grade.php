<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset="gbk">
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
	<title>�ɼ���ѯ���</title>
	<link rel="stylesheet" href="//cdn.bootcss.com/weui/0.4.0/style/weui.min.css"/>
</head>

<?php 
    session_start();
    header("Content-type: text/html; charset=gbk");  //��ѧУ������һ����gbk���룬phpҲ���õ�gbk���뷽ʽ
    
    //function: ����post���ݲ���½
    function login_post($url,$cookie,$post){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);   //���Զ�������ݣ�Ҫecho����
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  //��Ҫ��ץȡ��ת������
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie); 
        //curl_setopt($ch, CURLOPT_REFERER, 'http://gdjwgl.bjut.edu.cn/default2.aspx');  //��Ҫ��302��ת��Ҫreferer��������Request Headers�ҵ� 
        curl_setopt($ch, CURLOPT_POSTFIELDS,$post);   //post�ύ����
        $result=curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    //��ȡVIEWSTATE
    $_SESSION['xh']=$_POST['account'];
    $xh=$_POST['account'];
    $pw=$_POST['password'];
    $current_year=$_POST['current_year'];
    $current_term=$_POST['current_term'];
    //$code= $_POST['verify_code'];
    $cookie = dirname(__FILE__) . '/cookie/'.$_SESSION['id'].'.txt';
    $url="http://gdjwgl.bjut.edu.cn/default_vsso.aspx";  //�����ַ
    $con1=login_post($url,$cookie,'');               //��½
    preg_match_all('/<input type="hidden" name="__VIEWSTATE" value="([^<>]+)" \/>/', $con1, $view); //��ȡ__VIEWSTATE�ֶβ��浽$view������
    //Ϊ��½׼����POST����
    
    $post=array(
        //'__VIEWSTATE'=>$view[1][0],
        'TextBox1'=>$xh,
        'TextBox2'=>$pw,
        //'txtSecretCode'=>$code,
        'RadioButtonList1_2'=>'%D1%A7%C9%FA',  //��ѧ������gbk����
        'Button1'=>'',
        //'lbLanguage'=>'',
        //'hidPdrs'=>'',
        //'hidsc'=>''
        );
    $con2=login_post($url,$cookie,http_build_query($post)); //���������ӳ��ַ���, ��½����ϵͳ
    
    //����½��Ϣ��������
    if(!preg_match("/xs_main/", $con2)){
		//echo $con2;
        echo '<h2>&nbsp;<i class="weui_icon_warn"></i>&nbsp;�����˺� or ����������󣬻�����ѡ������Ч��ѧ��/ѧ�ڣ���<a href="/login_grade.php">����</a>��������</h2>';
        exit();
    }

    //Login done.
    require_score($cookie, $current_year, $current_term);    //��ȡ��Ȩƽ���ֺͳɼ���ϸ
    
function require_score($cookie, $current_year, $current_term){
    // ��֪��Ϊʲô�����ύ������ϢҲ�ܲ�ѯ
    // preg_match_all('/<span id="xhxm">([^<>]+)/', $con2, $xm);   //����������ݴ浽$xm������
    // print_r($xm);
    // $xm[1][0]=substr($xm[1][0],0,-4);  //�ַ�����ȡ���������
    $url2="http://gdjwgl.bjut.edu.cn/xscjcx.aspx?xh=".$_SESSION['xh'];
    $viewstate=login_post($url2,$cookie,'');
    preg_match_all('/<input type="hidden" name="__VIEWSTATE" value="([^<>]+)" \/>/', $viewstate, $vs);
    $state=$vs[1][0];  //$state���һ��post��__VIEWSTATE
    //��ѯĳһѧ�ڵĳɼ�
    $post=array(
     '__EVENTTARGET'=>'',
     '__EVENTARGUMENT'=>'',
     '__VIEWSTATE'=>$state,
     'hidLanguage'=>'',
       'ddlXN'=>$current_year,  //��ǰѧ��
       'ddlXQ'=>$current_term,  //��ǰѧ��
       'ddl_kcxz'=>'',
       'btn_xq'=>'%D1%A7%C6%DA%B3%C9%BC%A8'  //��ѧ�ڳɼ�����gbk���룬���������
       );
    $content=login_post($url2,$cookie,http_build_query($post)); //��ȡԭʼ����
    $content=get_td_array($content);    //tableתarray
    //��ѯ�ܳɼ�
    $post_allgrade=array(
     '__EVENTTARGET'=>'',
     '__EVENTARGUMENT'=>'',
     '__VIEWSTATE'=>$state,
     'hidLanguage'=>'',
       'ddlXN'=>$current_year,  //��ǰѧ��
       'ddlXQ'=>$current_term,  //��ǰѧ��
       'ddl_kcxz'=>'',
       'btn_zg'=>'%BF%CE%B3%CC%D7%EE%B8%DF%B3%C9%BC%A8'  //��֪����ɶ
       );
    $content_allgrade=login_post($url2,$cookie,http_build_query($post_allgrade)); //��ȡԭʼ����
    $content_allgrade=get_td_array($content_allgrade);    //tableתarray
    //�����ܵļ�Ȩ�������ܵ�GPA
    $i = 5;         //��array[5]��ʼ����Ч��Ϣ
    $all_score = 0; //�ܵļ�Ȩ*����
    $all_value = 0; //�ܵ�ѧ��Ȩֵ
    $all_GPA = 0;   //�ܵ�GPA
    $all_number_of_lesson = 0;  //�ܵĿγ���
    //�����ܺ͵Ķ�����ѧ��/GPA
    while(isset($content_allgrade[$i][4])){
        //������ڶ����ú��������ֿ��Լ�0�ֿγ�
        if ($content_allgrade[$i][5] === "�ڶ�����" || $content_allgrade[$i][5] == "�������ֿ�" || $content_allgrade[$i][4] < 5){
            $i++;
        }
        else{
            $all_score += ($content_allgrade[$i][3] * $content_allgrade[$i][4]);  //  �ۼ��ܷ�
            $all_value += $content_allgrade[$i][3];    //  �ۼ�ѧ��(Ȩֵ)
            
            if ($content_allgrade[$i][4] >= 85 && $content_allgrade[$i][4] <= 100){
                $all_GPA += 4.0;
            }
            else if ($content_allgrade[$i][4] >= 70 && $content_allgrade[$i][4] < 85){
                $all_GPA += 3.0;
            }
            else if ($content_allgrade[$i][4] >= 60 && $content_allgrade[$i][4] < 70){
                $all_GPA += 2.0;
            }
            $i++;
            $all_number_of_lesson++;
        }
    }
    //����ѧ�ڼ�Ȩƽ���ֺ�GPA�ļ���
    $i = 5;                       //array��5��ʼ�ǿγ̣������ˣ����ܸ�
    //���޿γ�
    $total_score = 0;
    $total_value = 0;
    $total_GPA = 0;
    $number_of_lesson = 0;        //�����ܿγ���
    //��רҵ�͸��ޣ�content[$i][9] == 2
    $total_score_fuxiu = 0;
    $total_value_fuxiu = 0;
    $total_GPA_fuxiu = 0;
    $number_of_lesson_fuxiu = 0;  //��רҵ/���޿γ���
    //�������ѧ�ڵ���Ϣ
    while(isset($content[$i][8])){
        if ($content[$i][5] === "�ڶ�����" || $content[$i][5] === "�������ֿ�" || $content[$i][8] < 5){
            $i++;
        }
        else{
            //������/��רҵ
            if ($content[$i][9] == 2){
                $total_score_fuxiu += ($content[$i][8] * $content[$i][6]);  //  �ۼ��ܷ�
                $total_value_fuxiu += $content[$i][6];    //  �ۼ�ѧ��(Ȩֵ)
                $total_GPA_fuxiu += $content[$i][7];
                $i++;
                $number_of_lesson_fuxiu++;
            }  
            //��ͨ�γ�
            if ($content[$i][9] == 0){
                $total_score += ($content[$i][8] * $content[$i][6]);  //  �ۼ��ܷ�
                $total_value += $content[$i][6];    //  �ۼ�ѧ��(Ȩֵ)
                $total_GPA += $content[$i][7];
                $i++;
                $number_of_lesson++;
            }
        }
    }
    $average_score = $total_score / $total_value;
    $average_score_fuxiu = $total_score_fuxiu / $total_value_fuxiu;
    echo'
    <div class="weui_cells_title">ƽ����</div>
    <div class="weui_cells">
    <div class="weui_cell">
    <div class="weui_cell_bd weui_cell_primary" id="average_score">
    <p>';
    printf("���ϴ�ѧ�����ܵļ�Ȩƽ����Ϊ: %.2lf ��",$all_score / $all_value);
    echo'</p>
    </div>
    </div>
    <div class="weui_cell">
    <div class="weui_cell_bd weui_cell_primary" id="average_score">
    <p>';
    printf("���ϴ�ѧ�����ܵ�ƽ��GPAΪ: %.2lf ",$all_GPA / $all_number_of_lesson);
    echo'</p>
    </div>
    </div>
    <div class="weui_cell">
    <div class="weui_cell_bd weui_cell_primary" id="average_score">
    <p>';
    printf("����ѧ�ڵļ�Ȩƽ����Ϊ: %.2lf ��",$average_score);
    echo'</p>
    </div>
    </div>
    <div class="weui_cell">
    <div class="weui_cell_bd weui_cell_primary" id="average_GPA">
    <p>';
    printf("����ѧ�ڵ�ƽ������Ϊ: %.2lf",$total_GPA / $number_of_lesson);
    echo'
    </p>
    </div>
    </div>';
    //����/��רҵ�γ���Ϣ���
    if ($total_score_fuxiu > 0) {
        echo'
        <div class="weui_cell">
        <div class="weui_cell_bd weui_cell_primary" id="average_score">
        <p>';
        printf("����/��רҵ�γ̵ļ�Ȩƽ����Ϊ: %.2lf ��",$average_score_fuxiu);
        echo'</p>
        </div>
        </div>
        <div class="weui_cell">
        <div class="weui_cell_bd weui_cell_primary" id="average_score">
        <p>';
        printf("����/��רҵ�γ̵�ƽ������Ϊ %.2lf ��",$total_GPA_fuxiu / $number_of_lesson_fuxiu);
        echo'</p>
        </div>
        </div>';
    }
    echo'
    </div> 
    <!-- <script src="weui/dist/example/zepto.min.js"></script> -->
    <!-- <script src="weui/dist/example/toast.js"></script> -->
    <script src="/js/require_score.js"></script>   
    </body>
    </html>';
    //����γ���ϸ,���޿γ�
    echo '<div class="weui_cells_title">�γ���ϸ</div>';
    echo '<div class="weui_cells">';
    $i = 5;
    while(isset($content[$i][7])){   
        if ($content[$i][9] == 0){
            echo '<div class="weui_cell">';
            echo '<div class="weui_cell_bd weui_cell_primary">';
            echo $content[$i][3]."  ����: ".$content[$i][8]."   �γ�ѧ��: ".$content[$i][6];
            echo '</div>';
            echo '</div>';    
        }  
        $i++;
    }   
    echo '</div>';
    //�������/��רҵ�γ���Ϣ
    if ($total_score_fuxiu > 0 || $total_score_secondmajor > 0) {
        echo '<div class="weui_cells_title">����/��רҵ�γ�</div>';
        echo '<div class="weui_cells">';
        $i = 5;
        while(isset($content[$i][7])){
            if ($content[$i][9] == 2){
                echo '<div class="weui_cell">';
                echo '<div class="weui_cell_bd weui_cell_primary">';
                echo $content[$i][3]."  ����: ".$content[$i][8]."   �γ�ѧ��: ".$content[$i][6];
                echo '</div>';
                echo '</div>';    
            }     
            $i++;
        }   
        echo '</div>';       
    }
    echo '<a class="weui_btn weui_btn_default" href="javascript:;" onClick="location.href=document.referrer">����</a>';
}
    //tableתarray
    function get_td_array($table) {
        $table = preg_replace("'<table[^>]*?>'si","",$table);
        $table = preg_replace("'<tr[^>]*?>'si","",$table);
        $table = preg_replace("'<td[^>]*?>'si","",$table);
        $table = str_replace("</tr>","{tr}",$table);
        $table = str_replace("</td>","{td}",$table);
            //ȥ�� HTML ���
        $table = preg_replace("'<[/!]*?[^<>]*?>'si","",$table);
            //ȥ���հ��ַ�
        $table = preg_replace("'([rn])[s]+'","",$table);
        $table = preg_replace('/&nbsp;/',"",$table);
        $table = str_replace(" ","",$table);
        $table = str_replace(" ","",$table);
        $table = explode('{tr}', $table);
        array_pop($table);
        foreach ($table as $key=>$tr) {
            $td = explode('{td}', $tr);
            array_pop($td);
            $td_array[] = $td;
        }
        return $td_array;
    }
?>