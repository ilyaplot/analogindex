<?php
require_once(dirname(__FILE__) . '/cackle_api.php');

class CackleSync {
    function CackleSync() {
        $cackle_api = new CackleAPI();
        $this->siteId = $cackle_api->cackle_get_param("site_id");
        $this->accountApiKey = $cackle_api->cackle_get_param("account_api");
        $this->siteApiKey = $cackle_api->cackle_get_param("site_api");
    }
    function has_next ($size_comments, $size_pagination = 100) {
        return $size_comments == $size_pagination;
    }
    function push_next_comments($mode,$comment_last_modified, $size_comments){
        $i = 1;
        while($this->has_next($size_comments)){
            if ($mode=="all_comments"){
                $response = $this->get_comments(0,$i) ;
            }
            else{
                $response = $this->get_comments($comment_last_modified,$i) ;
            }
            $size_comments = $this->push_comments($response); // get comment from array and insert it to wp db
			$i++;
        }
    }
    function init($mode = "") {
        $apix = new CackleAPI();
        $comment_last_modified = $apix->cackle_get_param("comment_last_modified");

        if ($mode == "all_comments") {
            $response = $this->get_comments(0);
        }
        else {
            $response = $this->get_comments($comment_last_modified);
        }

        if ($response==NULL){
            return false;
        }

        $size_comments = $this->push_comments($response); // get comment from array and insert it to wp db, and return size

        if ($this->has_next($size_comments)) {
            $this->push_next_comments($mode,$comment_last_modified, $size_comments);
        }

        return "success";
    }

    function get_comments($comment_last_modified, $cackle_page = 0){
        $this->get_url = "http://cackle.me/api/3.0/comment/list.json?id=$this->siteId&accountApiKey=$this->accountApiKey&siteApiKey=$this->siteApiKey";
        $host = $this->get_url . "&modified=" . $comment_last_modified . "&page=" . $cackle_page . "&size=100";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $host);

        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip, deflate");
        //curl_setopt($ch,CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-type: application/x-www-form-urlencoded; charset=utf-8',
        )
        );
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;

    }

    function to_i($number_to_format){
        return number_format($number_to_format, 0, '', '');
    }


    function cackle_json_decodes($response){

        $obj = json_decode($response,true);

        return $obj;
    }

    function filter_cp1251($string1){
        $cackle_api = new CackleAPI();
        if ($cackle_api->cackle_get_param("cackle_encoding") == "1"){
            $string2 = iconv("utf-8", "CP1251",$string1);
            //print "###33";
        }
        return $string2;
    }
	function startsWith($haystack, $needle) {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
    function insert_comment($comment,$status){

        /*
         * Here you can convert $url to your post ID
         */
		if ($this->startsWith($comment['chan']['channel'], 'http')) {
            $url = 0;
        } else {
            $url = $comment['chan']['channel'];
        }

        if (!empty($comment['author'])){
            $author_name = ($comment['author']['name']) ? $comment['author']['name'] : "";
            $author_www = ($comment['author']['www']) ? $comment['author']['www']: "" ;
            $author_avatar = ($comment['author']['avatar']) ? $comment['author']['avatar']: "" ;
            $author_provider = ($comment['author']['provider']) ? $comment['author']['provider']: "" ;
            $author_anonym_name = "";
            $anonym_email = "";
        }
        else{
            $author_name = ($comment['anonym']['name']) ? $comment['anonym']['name']: "" ;
            $author_email= ($comment['anonym']['email']) ?  $comment['anonym']['email'] : "";
            $author_www = "";
            $author_avatar = "";
            $author_provider = "anonym";
            $author_anonym_name = $comment['anonym']['name'];
            $anonym_email = $comment['anonym']['email'];

        }
        $get_parent_local_id = null;
        $comment_id = $comment['id'];
        $comment_modified = $comment['modified'];
        $cackle_api = new CackleAPI();
        if ($cackle_api->cackle_get_param("last_comment")==0){
            $cackle_api->cackle_db_prepare();
        }
        $date =strftime("%Y-%m-%d %H:%M:%S", $comment['created']/1000);
        $ip = ($comment['ip']) ? $comment['ip'] : "";
        $message = $comment['message'];
        $user_agent = 'Cackle:' . $comment['id'];

        $conn = $cackle_api->conn();
        if ($cackle_api->cackle_get_param("cackle_encoding") == 1){

            $conn->exec('SET NAMES cp1251');
        }
		else{
		$conn->exec('SET NAMES utf8');
		}

        $sql = "insert into " . PREFIX ."_comments
        (channel,autor,email,avatar,date,ip,comment,approve,user_agent)
        values
        (:channel, :author_name, :author_email, :author_avatar, :date, :ip, :comment, :status, :user_agent ) ";

	    $q = $conn->prepare($sql);
	    $q->execute(
                array(
                    ':channel'=>($cackle_api->cackle_get_param("cackle_encoding") == 1) ? iconv("utf-8", "CP1251",$url) : $url,
                    ':author_name'=>($cackle_api->cackle_get_param("cackle_encoding") == 1) ? iconv("utf-8", "CP1251",@$author_name) : @$author_name,
                    ':author_email'=>($cackle_api->cackle_get_param("cackle_encoding") == 1) ? iconv("utf-8", "CP1251",@$author_email) : @$author_email ,
                    ':author_avatar'=>($cackle_api->cackle_get_param("cackle_encoding") == 1) ? iconv("utf-8", "CP1251",@$author_avatar) : @$author_avatar ,
                    ':date'=>$date,
                    ':ip'=>$ip,
                    ':comment'=>($cackle_api->cackle_get_param("cackle_encoding") == 1) ? iconv("utf-8", "CP1251",$message) : $message,
                    ':status'=>$status,
                    ':user_agent'=>$user_agent,


                ));
        $q=null;

        $cackle_api->cackle_set_param("last_comment",$comment_id);
        $get_last_modified = $cackle_api->cackle_get_param("comment_last_modified");
        $get_last_modified = (int)$get_last_modified;
        if ($comment['modified'] > $get_last_modified) {
            $cackle_api->cackle_set_param("comment_last_modified",(string)$comment['modified']);
        }

    }

    function comment_status_decoder($comment) {
        $status;
        if (strtolower($comment['status']) == "approved") {
            $status = 1;
        }
        elseif (strtolower($comment['status'] == "pending") || strtolower($comment['status']) == "rejected") {
            $status = 0;
        }
        elseif (strtolower($comment['status']) == "spam") {
            $status = 0;
        }
        elseif (strtolower($comment['status']) == "deleted") {
            $status = 0;
        }
        return $status;
    }

    function update_comment_status($comment_id, $status, $modified, $comment_content) {
        $apix = new CackleAPI();
        $cackle_api = new CackleAPI();
        $sql = "update ". PREFIX ."_comments set approve = ? , comment = ?  where user_agent = ?";
        $conn = $cackle_api->conn();
		if ($cackle_api->cackle_get_param("cackle_encoding") == 1){

            $conn->exec('SET NAMES cp1251');
        }
		else{
		    $conn->exec('SET NAMES utf8');
		}
        $q = $conn->prepare($sql);
        $comment_content = ($cackle_api->cackle_get_param("cackle_encoding") == 1) ? iconv("utf-8", "CP1251",$comment_content) : $comment_content;
        $q->execute(array($status,$comment_content,"Cackle:$comment_id"));
        $q = null;
        if ($modified > $apix->cackle_get_param('comment_last_modified', 0)) {
            $cackle_api->cackle_set_param("comment_last_modified",$modified);
        }

    }

    function push_comments ($response){
        $apix = new CackleAPI();
        $obj = $this->cackle_json_decodes($response,true);
        $obj = $obj['comments'];
        if ($obj) {
            $comments_size = count($obj);
            if ($comments_size != 0){
                foreach ($obj as $comment) {
                    if ($comment['id'] > $apix->cackle_get_param('last_comment')) {
                        $this->insert_comment($comment, $this->comment_status_decoder($comment));
                    } else {
                        // if ($comment['modified'] > $apix->cackle_get_param('cackle_comments_last_modified', 0)) {
                        $this->update_comment_status($comment['id'], $this->comment_status_decoder($comment), $comment['modified'], $comment['message'] );
                        // }
                    }
                }
            }
        }
        return $comments_size;

    }

}
?>