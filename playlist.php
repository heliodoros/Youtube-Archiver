<?php
$thisPage = 'playlist';
include 'config.php';

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

  <style>

  </style>
</head>

<body>
  <div class="all-content-wrapper">
    <!-- Top Bar -->
    <?php require_once('./include/header.php'); ?>
    <!-- #END# Top Bar -->

    <section class="container">
      <div class="form-group custom-input-space has-feedback">
        <div class="page-heading">
          <h3 class="post-title">YouTube Safekeeping</h3>
        </div>
        <div class="page-body clearfix">
          <div class="row">
            <div class="col-md-12">
              <div class="panel panel-default">
                <div class="panel-heading">Playlists:</div>
                <div class="panel-body">
                  <div id="my_video_list">
                    <?php
                    $client->setScopes('https://www.googleapis.com/auth/youtube.readonly');
                    if (isset($_SESSION['googletoken'])) {
                      $client->setAccessToken($_SESSION['googletoken']['access_token']);
                      $tokenSessionKey = 'token-' . $client->prepareScopes();
                      // Check to ensure that the access token was successfully acquired.
                      if ($client->getAccessToken()) {
                        $htmlBody = '';
                        try {
                          $queryParams = [
                            'maxResults' => 10,
                            'mine' => true
                          ];
                          $listResponse = $youtube->playlists->listPlaylists('snippet', $queryParams);
                          // If $listResponse is empty, the specified video was not found.
                          if (empty($listResponse)) {
                            $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>');
                          } else {
                            foreach ($listResponse as $playlists => $value) {
                              $playlist = $listResponse[$playlists];
                              $my_playlists[] = array('p_id' => $playlist['id'], 'p_name' => $playlist['snippet']['title'], 'p_url' => $playlist['snippet']['thumbnails']['medium']['url']);
                            }
                            while (isset($listResponse['nextPageToken'])) {
                              $queryParams = [
                                'maxResults' => 10,
                                'mine' => true,
                                'pageToken' => $listResponse['nextPageToken']
                              ];
                              $listResponse = $youtube->playlists->listPlaylists('snippet', $queryParams);
                              foreach ($listResponse['items'] as $playlist) {
                                $my_playlists[] = array('p_id' => $playlist['id'], 'p_name' => $playlist['snippet']['title'], 'p_url' => $playlist['snippet']['thumbnails']['medium']['url']);
                              }
                            }
                          }

                          foreach ($my_playlists as $playlists => $value) {
                            $playlist = $my_playlists[$playlists];
                            $playlistId = $playlist['p_id'];
                            $playlistTitle = $playlist['p_name'];
                            if (isset($playlist)) {
                              echo '<a href="/video?id=' . $playlist['p_id'] . '" style="background: url(\'' . $playlist['p_url'] . ' \')">
            <div>' . $playlist['p_name'] . '</div>
        </a>';
                            }
                            if (is_null($playlistTitle)) {
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
                    } else {
                      var_dump($_SESSION);
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


</body>

</html>