<?php
// crude webhook to push info to discord

include('config/discord_vars.php');

function pushToDiscord($action, $pid) {
  global $webhook_url, $board_url;

  $rPost = Query("
  SELECT
    u.name, u.displayname, t.title t_title, f.minpower, f.minpostsread, f.title f_title
  FROM
    {posts} p
    LEFT JOIN {users} u ON u.id = p.user
    LEFT JOIN {threads} t ON t.id=p.thread
    LEFT JOIN {forums} f ON f.id=t.forum
  WHERE p.id={0}", $pid);

  if(!NumRows($rPost))
    return;
  $post = Fetch($rPost);
  if($post['minpower'] > 0)
    return;
  if($post['minpostsread'] > 0)
    return;

  $purl = $board_url . actionLink("post", $pid, "");
  $name = ($post['displayname'] ? $post['displayname'] : $post['name']);

  if($action == "thread") {
    $format = "**New %s:** %s in %s by %s\n%s";
    $formatted_message = sprintf($format, $action, $post['t_title'], $post['f_title'], $name, $purl);
  }
  else if($action == "reply") {
    $format = "**New %s** in %s by %s\n%s";
    $formatted_message = sprintf($format, $action, $post['t_title'], $name, $purl);
  }

  $data = array(
    "content" => $formatted_message
  );

  $options = array(
    'http' => array(
      'method'  => 'POST',
      'content' => json_encode( $data ),
      'header'=>  "Content-Type: application/json\r\n" .
                  "Accept: application/json\r\n"
      )
  );
  
  $context  = stream_context_create( $options );
  $result = file_get_contents( $webhook_url, false, $context );
  $response = json_decode( $result );
}

?>