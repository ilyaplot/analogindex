<?php

//Define timer for sync
define('CACKLE_TIMER', 500);

require_once(dirname(__FILE__) . '/cackle_sync.php');


class Cackle{

    function Cackle($init=true,$channel){
        $this->channel = $channel;
        global $db;
        if ($init){
            $this->cackle_auth();
            $sync = new CackleSync();
            if ($this->time_is_over(CACKLE_TIMER)){
                $sync->init();
            }
            $this->cackle_display_comments();

        }
    }
    
    function time_is_over($cron_time){
        $cackle_api = new CackleAPI();
        $get_last_time = $cackle_api->cackle_get_param("last_time");
        $now=time();
        if ($get_last_time==""){
            $set_time = $cackle_api->cackle_set_param("last_time",$now);
            return time();
        }
        else{
            if($get_last_time + $cron_time > $now){
                return false;
            }
            if($get_last_time + $cron_time < $now){
                $set_time = $cackle_api->cackle_set_param("last_time",$now);
                return $cron_time;
            }
        }
    }

    function cackle_auth() {
        $cackle_api = new CackleAPI();
        $siteApiKey = $cackle_api->cackle_get_param("site_api");
        $timestamp = time();
        if (!empty($_SESSION['dle_user_id'])) {
            $user_id = $_SESSION['dle_user_id'];
            $user_info = $cackle_api->db_connect("select * from ".PREFIX."_users where user_id = $user_id");
            $user_info = $user_info[0];
            $user = array(
                'id' => $user_id,
                'name' => $user_info["name"],
                'email' => $user_info["email"],
                'avatar' => AVATAR_PATH . $user_info["foto"]
            );
            $user_data = base64_encode(json_encode($user));
        } else {
            $user = '{}';
            $user_data = base64_encode($user);
        }
        $sign = md5($user_data . $siteApiKey . $timestamp);
        return "$user_data $sign $timestamp";
    }


     function cackle_comments( $comment) {
        
        ?><li id="cackle-comment-<?php echo $comment['id']; ?>" itemscope itemtype="http://schema.org/UserComments">
              <div id="cackle-comment-header-<?php echo $comment['id']; ?>" class="cackle-comment-header">
                  <cite id="cackle-cite-<?php echo $comment['id']; ?>" itemprop="creator" itemscope itemtype="http://schema.org/Person">
                  <?php if($comment['autor']) : ?>
                      <a id="cackle-author-user-<?php echo $comment['id']; ?>" href="#" target="_blank" rel="nofollow" >
                          <span itemprop="name"><?php echo $comment['autor']; ?></span>
                      </a>
                  <?php else : ?>
                      <span id="cackle-author-user-<?php echo $comment['id']; ?>" itemprop="name"><?php echo $comment['name']; ?></span>
                  <?php endif; ?>
                  </cite>
              </div>
              <div id="cackle-comment-body-<?php echo $comment['id']; ?>" class="cackle-comment-body">
                  <div id="cackle-comment-message-<?php echo $comment['id']; ?>" class="cackle-comment-message" itemprop="commentText">
                  <?php echo $comment['comment']; ?>
                  </div>
              </div>
          </li><?php } 
    
     
     function cackle_display_comments(){
         global $cackle_api;
         $cackle_api = new CackleAPI();?>
        <div id="mc-container">

                <ul id="cackle-comments">
                <?php $this->list_comments(); ?>
                </ul>

        </div>
        <script type="text/javascript">
        <?php $channel = $this->channel; ?>

        cackle_widget = window.cackle_widget || [];
        cackle_widget.push({widget: 'Comment', id: '<?php echo $cackle_api->cackle_get_param("site_id"); //from cackle's admin panel?>', lang: 'en', channel: '<?php echo($channel)?>' });
        document.getElementById('mc-container').innerHTML = '';
        (function() {
            var mc = document.createElement("script");
            mc.type = "text/javascript";
            mc.async = true;
            mc.src = ("https:" == document.location.protocol ? "https" : "http") + "://cackle.me/widget.js";
            var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(mc, s.nextSibling);
        })();

        </script>
<?php }
    function get_local_comments(){
        //getting all comments for special post_id from database.
        $cackle_api = new CackleAPI();
        $channel = $this->channel;
        $get_all_comments = $cackle_api->db_connect("select * from ".PREFIX."_comments where md5(channel) = '".md5($channel)."' and approve = 1;");
        return $get_all_comments;
    }
    function list_comments(){
        $obj = $this->get_local_comments();
        if ($obj){
            foreach ($obj as $comment) {
                $this->cackle_comments($comment);
            }
        }
    }
}

?>
