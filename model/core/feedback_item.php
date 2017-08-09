<?php

class GjmaaFeedbackItem {
    private $tableName;
    private $wpdb;

    public function __construct(){
        global $wpdb;
        $prefix = $wpdb->prefix;
        $this->tableName = $prefix . "gj_allegro_feedback";
        $this->wpdb = $wpdb;
    }

    public function install(){
        $wpdb = $this->wpdb;
        $tableName = $this->tableName;
        $db_version = "1.0";

        if ($wpdb->get_var("SHOW TABLES LIKE '" . $tableName . "'") != $tableName) {
            $query = "CREATE TABLE " . $tableName . " (
                feedback_id BIGINT NOT NULL,
                feedback_to_id BIGINT NOT NULL,
                feedback_to_login BIGINT NOT NULL,
                feedback_auction_id BIGINT NOT NULL,
                feedback_type TEXT NOT NULL,
                feedback_from_id VARCHAR(250) NOT NULL,
                feedback_from_login
                feedback_from_user_points INT,
                feedback_date timestamp NOT NULL,
                PRIMARY KEY (feedback_id))
                CHARACTER SET utf8 COLLATE utf8_bin";

            $wpdb->query($query);

            add_option("gjmaa_db_version", $db_version);
        }
    }

    public function uninstall(){
        return $this->wpdb->query('DROP TABLE '.$this->tableName);
    }

    public function getCommentsConst($cIndex = null){
        $comments = array(
            'POS' => __('Positive','gj_myauctions_allegro'),
            'NEG' => __('Negative','gj_myauctions_allegro'),
            'NEU' => __('Neutral','gj_myauctions_allegro')
        );

        return $cIndex ? $comments[$cIndex] : $comments;
    }

    /**
     * @param $api_allegro GjmaaAllegroWebApi
     * @param $user_id int
     * @return string
     */
    public function addCommentSection($api_allegro,$user_id,$count){
        $html = '<div id="allegro_comments">';
        $feedback = $api_allegro->getFeedback($user_id);
        foreach($feedback->feedbackList->item as $index => $comment){
            if($count <= $index)
                break;

            $html .=
                '<div class="comment_item '.($index % 2 == 0 ? "even" : "odd").'">'
                .'<p class="comment_details">'
                .'<span class="type ' . strtolower($comment->fType) . '">'.($comment->fType ? $this->getCommentsConst($comment->fType) : '').'</span>'
                .'<span class="date">'.date('Y/m/d H:i',$comment->fDate).'</span>'
                .'<span class="user">'
                .$comment->fUserLogin
                .'<span class="user-range-image '.(isset($comment->fUserRating) ? $api_allegro->switchRatingToImage($comment->fUserRating) : 'green_leaf_allegro').'"></span>'
                .'<span class="user-range">('.(isset($comment->fUserRating) ? $comment->fUserRating : 0).')</span>'
                .'</span>'
                .'</p>'
                .'<p class="comment">' . $comment->fDesc . '</p>'
                .'</div>';

        }
        $html .= '<div class="show_all_comments"><a href="http://'.$api_allegro->countryDetails[$api_allegro->getCountry()]['site'].'/show_user.php?uid='.$user_id.'" target="_blank">'.__('Show all comments','gj_myauctions_allegro').'</a></div>';
        $html .= '<p class="copyright">Powered by <a href="http://grojanteam.pl" title="Dodatki Joomla, Wtyczki WordPress - Grojan Team" target="_blank">GroJan Team</a></p>';
        $html .= '</div>';
        return $html;
    }
}