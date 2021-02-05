<?php
require 'config.php';
$location = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$backup = false;
if (isset($_GET['backup'])) {
  $backup = true;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Youtube Safekeeping</title>

  <!-- Bootstrap Core Css -->
  <link href="css/bootstrap.css" rel="stylesheet" />

  <!-- Font Awesome Css -->
  <link href="css/font-awesome.min.css" rel="stylesheet" />

  <!-- Bootstrap Select Css -->
  <link href="css/bootstrap-select.css" rel="stylesheet" />

  <!-- Custom Css -->
  <link href="css/app_style.css" rel="stylesheet" />

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

  <link href="youtube/youtube_api.css" rel="stylesheet" />


</head>

<body>

  <div class="all-content-wrapper">
    <!-- Top Bar -->
    <?php require_once('./include/header.php'); ?>
    <!-- #END# Top Bar -->



    <!-- Modal -->
    <section class="container">
      <div class="form-group custom-input-space has-feedback">
        <div class="page-heading">
          <h3 class="post-title">YouTube Safekeeping</h3>
        </div>
        <div class="page-body clearfix">
          <div class="row">
            <div class="col-md-12">
              <div>
              </div>
              <div class="panel panel-default">
                <div class="panel-heading">Videos:<div id="backup">
                    <a href="/playlist" style=\"margin-right:5em\">Back to playlists </a>
                    <?php
                    if (!$backup) {
                      echo "<a href=\"$location&backup\">Backup</a>";
                    } ?>
                  </div>
                </div>
                <div class="panel-body">
                  <div id="my_video_list">
                    <?php
                    $playlistID = $_GET["id"];
                    $client->setScopes('https://www.googleapis.com/auth/youtube.readonly');
                    if (isset($_SESSION['googletoken'])) {
                      $client->setAccessToken($_SESSION['googletoken']['access_token']);
                      $tokenSessionKey = 'token-' . $client->prepareScopes();

                      // Check to ensure that the access token was successfully acquired.
                      if ($client->getAccessToken()) {
                        $htmlBody = '';
                        try {
                          $queryParams = [
                            'playlistId' => $playlistID,
                            'maxResults' => 50,
                          ];
                          $listResponse = $youtube->playlistItems->listPlaylistItems('snippet', $queryParams);
                          if (empty($listResponse)) {
                            $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>');
                          } else {
                            foreach ($listResponse as $videos => $value) {
                              $video = $listResponse[$videos];
                              $my_videos[] = array('v_name' => $video['snippet']['title'], 'v_id' => $video['snippet']['resourceId']['videoId'], 'v_url' => $video['snippet']['thumbnails']['medium']['url'], 'p_id' => $video['snippet']['playlistId']);
                            }
                            while (isset($listResponse['nextPageToken'])) {
                              $queryParams = [
                                'playlistId' => $playlistID,
                                'maxResults' => 50,
                                'pageToken' => $listResponse['nextPageToken']

                              ];
                              $listResponse = $youtube->playlistItems->listPlaylistItems('snippet', $queryParams);

                              foreach ($listResponse['items'] as $video) {
                                $my_videos[] = array('v_name' => $video['snippet']['title'], 'v_id' => $video['snippet']['resourceId']['videoId'], 'v_url' => $video['snippet']['thumbnails']['medium']['url'], 'p_id' => $video['snippet']['playlistId']);
                              }
                            }
                          }

                          function savevideos($my_videos, $connect)
                          {
                            if (isset($my_videos)) {
                              if (is_array($my_videos)) {
                                foreach ($my_videos as $row => $value) {
                                  $item_id = mysqli_real_escape_string($connect, $value['v_id']);
                                  $item_name = mysqli_real_escape_string($connect, $value['v_name']);
                                  $item_playlist = mysqli_real_escape_string($connect, $value['p_id']);
                                  $sql = "INSERT INTO contents(id, name, playlist) VALUES ('" . $item_id . "', '" . $item_name . "', '" . $item_playlist . "') ON DUPLICATE KEY UPDATE playlist='" . $item_playlist . "'";
                                  if (!mysqli_query($connect, $sql)) {
                                    echo "query error";
                                    die("error " . mysqli_error($connect));
                                  } else {
                                    $_SESSION['backup'] = true;
                                  }
                                }
                                mysqli_close($connect);
                              }
                            }
                          }
                          if (isset($_GET['sort'])) {
                            asort($my_videos);
                          }
                          if ($backup) {

                            try {

                              $connect = mysqli_connect("localhost", "sqluser", "sqlpass", "sqldatabase");
                            } catch (mysqli_sql_exception $e) {
                              $htmlBody .= sprintf(
                                '<p>An client error occurred: <code>%s</code></p>',
                                htmlspecialchars($e->getMessage())
                              );
                              echo "<center>
          DATABASE ERROR <i class=\"fa fa-database\" aria-hidden=\"true\"></i>
          </center>";
                              exit();
                            }
                            savevideos($my_videos, $connect);
                          }
                          foreach ($my_videos as $videos => $value) {
                            $video = $my_videos[$videos];
                            $videoId = $video['v_id'];
                            $videoTitle = $video['v_name'];
                            if (isset($video)) {
                              echo '<a href="https://www.youtube.com/watch?v=' . $video['v_id'] . '" style="background: url(\'' . $video['v_url'] . ' \')">
            <div>' . $video['v_name'] . '</div>
        </a>';
                            }
                            if (is_null($videoTitle)) {
                              echo "Nothing found";
                            } else {
                              //var_dump(json_encode($listResponse));
                            }
                          }
                        } catch (Google_Service_Exception $e) {
                          $htmlBody .= sprintf(
                            '<p>A service error occurred: <code>%s</code></p>',
                            htmlspecialchars($e->getMessage())
                          );
                        } catch (Google_Exception $e) {
                          $htmlBody .= sprintf(
                            '<p>An client error occurred: <code>%s</code></p>',
                            htmlspecialchars($e->getMessage())
                          );
                        }
                      }
                    }

                    ?>
                  </div>
                </div>
              </div>
            </div>

            <div id="my_player">
              <div></div>
            </div>

          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- Jquery Core Js -->
  <script src="js/jquery.min.js"></script>

  <!-- Bootstrap Core Js -->
  <script src="js/bootstrap.min.js"></script>

  <!-- Bootstrap Select Js -->
  <script src="js/bootstrap-select.js"></script>

  <?= $htmlBody ?>
  <script>
    $(document).ready(function(e) {

      $('#my_video_list a').on('click', function(e) {

        e.preventDefault();

        var video_url = $(this).attr('href');

        var video_id = video_url.substring(video_url.search('=') + 1, video_url.length);
        console.log(video_id);
        $('#my_player DIV').html('<iframe width="1080" height="720" src="https://www.youtube.com/embed/' + video_id + '" frameborder="0" allowfullscreen></iframe>');
        $('#my_player').fadeIn(150);

      });


      $('#my_player').on('click', function(e) {
        $('#my_player').fadeOut(150);
        $('#my_player DIV').empty();
      });


    });
  </script>
</body>

</html>