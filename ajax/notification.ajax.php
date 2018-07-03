<?php
/**
 * Verifica se existem novas notificações ou mensagens no moodle.
 */
define('AJAX_SCRIPT', true);

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once($CFG->dirroot . '/message/lib.php');

$PAGE->set_url('/theme/ufsm2/ajax/notification.ajax.php');

//parametros
$user = optional_param('usuario', '', PARAM_RAW);
$op = optional_param('op', '', PARAM_RAW);
$msg = optional_param('msg', '', PARAM_RAW);
$urlAtual = optional_param('url', '', PARAM_RAW);

if (!confirm_sesskey()) {
    throw new moodle_exception('invalidsesskey', 'error');
}
if (!isloggedin()) {

    throw new moodle_exception('notlogged', 'chat');
}

switch ($op) {

    case 'msg-read':
        read_all_messages(0);
        break;

    case 'notif-read':
        read_all_messages(1);
        break;
    case 'msg-last':
        echo json_encode(get_last_messages());
        break;

    case 'msg-exist':
        echo json_encode(get_exist_new_messages(0));
        break;

    case 'notif-last':
        echo json_encode(get_last_notifications($urlAtual));
        break;

    case 'notif-exist':
        echo json_encode(get_exist_new_messages(1));
        break;

    case 'msg-new':
        $msg = $msg > 15 ?: $msg;
        echo json_encode(get_last_messages($msg));
        break;

    case 'notif-new':
        $msg = $msg > 15 ?: $msg;
        echo json_encode(get_last_notifications($urlAtual, $msg));
        break;
    case  'exists':
        $x = [
            'msgExist' => get_exist_new_messages(0),
            'notifExist' => get_exist_new_messages(1)
        ];
        echo json_encode($x);
        break;
    default:
        $x = [
            'msgLast' => get_last_messages(),
            'msgExist' => get_exist_new_messages(0),
            'notifLast' => get_last_notifications($urlAtual),
            'notifExist' => get_exist_new_messages(1)
        ];
        echo json_encode($x);
        return null;
}
/**
 * @param $notification
 * @return int|void
 */
function get_exist_new_messages($notification)
{
    global $USER, $DB, $PAGE, $CFG;
    $messagecount = $DB->count_records('message', array('useridto' => $USER->id));
    if ($messagecount < 1) {
        return;
    }
    $messagesql = "SELECT m.id, c.blocked
                     FROM {message} m
                     JOIN {message_working} mw      ON m.id=mw.unreadmessageid
                     JOIN {message_processors} p    ON mw.processorid=p.id
                     LEFT JOIN {message_contacts} c ON c.contactid = m.useridfrom
                                                    AND c.userid = m.useridto
                    WHERE m.useridto = :userid
                      AND m.notification=$notification ";
    $waitingmessages = $DB->get_records_sql($messagesql, array('userid' => $USER->id, 'lastpopuptime' => $USER->message_lastpopup));
    $validmessages = 0;
    foreach ($waitingmessages as $messageinfo) {
        if ($messageinfo->blocked) {
            // Message is from a user who has since been blocked so just mark it read.
            // Get the full message to mark as read.
            $messageobject = $DB->get_record('message', array('id' => $messageinfo->id));
            message_mark_message_read($messageobject, time());
        } else {
            $validmessages++;
        }
    }
    return $validmessages;
}


function read_all_messages($notification)
{
    global $USER, $DB, $PAGE, $CFG;
    $messagecount = $DB->count_records('message', array('useridto' => $USER->id));
    if ($messagecount < 1) {
        return;
    }
    $messagesql = "SELECT m.id, c.blocked
                     FROM {message} m
                     JOIN {message_working} mw      ON m.id=mw.unreadmessageid
                     JOIN {message_processors} p    ON mw.processorid=p.id
                     LEFT JOIN {message_contacts} c ON c.contactid = m.useridfrom
                                                    AND c.userid = m.useridto
                    WHERE m.useridto = :userid
                      AND m.notification=$notification ";
    $waitingmessages = $DB->get_records_sql($messagesql, array('userid' => $USER->id, 'lastpopuptime' => $USER->message_lastpopup));
    $validmessages = 0;
    foreach ($waitingmessages as $messageinfo) {
        $messageobject = $DB->get_record('message', array('id' => $messageinfo->id));
        message_mark_message_read($messageobject, time());
    }
}

/**
 *
 * @param int $max
 * @return array
 */
function get_last_messages($max = 8)
{
    global $USER, $CFG;
    $messages = [];
    foreach (message_get_recent_conversations2($USER, 0, $max) as $m) {
        $total = message_count_unread_messages($USER, $m);

        $msg = new stdClass();
        $msg->id = $m->id;
        $msg->nome = ucwords(strtolower($m->firstname . " " . $m->lastname)) . ($total <= 0 ? '' : " ($total)");
        $msg->dtEnvio = $m->timecreated; //new DateTime('@' . $m->timecreated);
        $msg->msg = strlen($m->smallmessage) > 60 ? substr(strip_tags($m->smallmessage), 0, 57) . " ..." : $m->smallmessage;
        $msg->msg = preg_replace('/<[^<]+?>/', ' ', $msg->msg);
        $msg->msg = fix_utf8($msg->msg);
        $msg->foto = $CFG->wwwroot . '/user/pix.php/' . $m->id . '/f2.jpg';
        $msg->url = $CFG->wwwroot . "/message/index.php?user1={$USER->id}&user2={$m->id}&history=1";
        $messages[] = $msg;

    }
    return array_reverse($messages);
}

/**
 * @param int $max
 * @return array
 */
function get_last_notifications($urlAtual, $max = 8)
{
    global $USER, $CFG;
    $notif = [];
    foreach (message_get_recent_notifications_all($USER, 0, $max) as $m) {
        if ($urlAtual == $m->contexturl) {
            message_mark_messages_read($USER->id, isset($m->id) ? $m->id : '1');
        }
        $msg = new stdClass();
        $msg->id = isset($m->id) ? $m->id : '1';
        $msg->foto = $CFG->wwwroot . '/user/pix.php/' . (isset($m->id) ? $m->id : '1') . '/f2.jpg';
        $msg->msg = strip_tags($m->smallmessage);
        $msg->url = $m->contexturl;
        $msg->url = ($m->contexturl == null or $m->id == NULL) ? $CFG->wwwroot . "/message/index.php?user1={$USER->id}&user2=-10" : $m->contexturl;
        $msg->dtEnvio = $m->timecreated;
        $notif[] = $msg;
    }
    return array_reverse($notif);
}


/**
 * Get the users recent conversations meaning all the people they've recently
 * sent or received a message from plus the most recent message sent to or received from each other user
 *
 * @param object $user the current user
 * @param int $limitfrom can be used for paging
 * @param int $limitto can be used for paging
 * @return array
 */
function message_get_recent_conversations2($user, $limitfrom = 0, $limitto = 100)
{
    global $DB;
    $sql = "SELECT otheruser.id, otheruser.picture, otheruser.firstname, otheruser.lastname, 
                    message.id as mid, message.notification, message.smallmessage, message.timecreated
              FROM {message} message
              JOIN (
                        SELECT MAX(id) AS messageid,
                               matchedmessage.useridto,
                               matchedmessage.useridfrom
                         FROM {message} matchedmessage
                   INNER JOIN (
                               SELECT MAX(recentmessages.timecreated) timecreated,
                                      recentmessages.useridfrom,
                                      recentmessages.useridto
                                 FROM {message} recentmessages
                                WHERE (
                                      (recentmessages.useridfrom = :userid1 AND recentmessages.timeuserfromdeleted = 0) OR
                                      (recentmessages.useridto = :userid2   AND recentmessages.timeusertodeleted = 0)
                                      )
                             GROUP BY recentmessages.useridfrom, recentmessages.useridto
                              ) recent ON matchedmessage.useridto     = recent.useridto
                           AND matchedmessage.useridfrom   = recent.useridfrom
                           AND matchedmessage.timecreated  = recent.timecreated
                           WHERE (
                                 (matchedmessage.useridfrom = :userid6 AND matchedmessage.timeuserfromdeleted = 0) OR
                                 (matchedmessage.useridto = :userid7   AND matchedmessage.timeusertodeleted = 0)
                                 )
                      GROUP BY matchedmessage.useridto, matchedmessage.useridfrom
                   ) messagesubset ON messagesubset.messageid = message.id
              JOIN {user} otheruser ON (message.useridfrom = :userid4 AND message.useridto = otheruser.id)
                OR (message.useridto   = :userid5 AND message.useridfrom   = otheruser.id)
         LEFT JOIN {message_contacts} contact ON contact.userid  = :userid3 AND contact.contactid = otheruser.id
             WHERE otheruser.deleted = 0 AND message.notification = 0 and
                    message.useridfrom != :userid8
          ORDER BY message.timecreated DESC";
    $params = array(
        'userid1' => $user->id,
        'userid2' => $user->id,
        'userid3' => $user->id,
        'userid4' => $user->id,
        'userid5' => $user->id,
        'userid6' => $user->id,
        'userid7' => $user->id,
        'userid8' => $user->id
    );
    return $DB->get_records_sql($sql, $params, $limitfrom, $limitto);
}

/**
 * Get the users recent event notifications
 *
 * @param object $user the current user
 * @param int $limitfrom can be used for paging
 * @param int $limitto can be used for paging
 * @return array
 */
function message_get_recent_notifications_all($user, $limitfrom = 0, $limitto = 10)
{
    global $DB;
    $sql0 = "SELECT u.id, u.picture, u.firstname, u.lastname,
                      mr.id AS message_read_id, mr.smallmessage,  mr.timecreated as timecreated,
                       mr.contexturl, mr.contexturlname
              FROM {message} mr
                   LEFT JOIN {user} u ON u.id=mr.useridfrom
             WHERE mr.notification = 1 AND mr.useridto = {$user->id} AND mr.useridfrom != {$user->id} AND (u.deleted = '0' OR mr.useridfrom=-10 )
             ORDER BY mr.timecreated DESC";
    return $DB->get_records_sql($sql0, [], $limitfrom, $limitto);
}