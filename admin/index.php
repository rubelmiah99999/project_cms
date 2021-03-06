<?php
include "includes/admin_header.php";
include "../includes/db.php";
?>
    <div id="wrapper">

        <!-- Navigation -->
<?php
include "includes/admin_navigation.php";
$app_key = getenv('APP_KEY');
?>
        <div id="page-wrapper">

            <div class="container-fluid">

<?php
//permission_warning ();
?>
                <!-- Page Heading -->
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
<?php
  echo "Welcome to Admin page <small>".$_SESSION['username']."</small>";
?>
                        </h1>
                        <ol class="breadcrumb">
                            <li>
                                <i class="fa fa-dashboard"></i>
                                  <a href="index.php">Dashboard</a>
                            </li>
                            <li class="active">
                                <i class="fa fa-file"></i> Blank Page
                            </li>
                        </ol>
                    </div>
                </div>
                <!-- /.row -->

                <!-- Admin widget -->
                                
                <div class="row">

                    <!-- posts -->
                    <div class="col-lg-3 col-md-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-file-text fa-5x"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
<?php

if (is_admin ($_SESSION['username'])) {
  $posts_counts = recordCount ('posts');
} else {
  $user = $_SESSION['username'];
  $posts_counts = recordCountOfUser ('posts', 'post_author', $user);
}

echo "<div class='huge'>{$posts_counts}</div>";
?>
                                        <div>Posts</div>
                                    </div>
                                </div>
                            </div>
                            <a href="posts.php">
                                <div class="panel-footer">
                                    <span class="pull-left">View Details</span>
                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                    <div class="clearfix"></div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- comments -->
                    <div class="col-lg-3 col-md-6">
                        <div class="panel panel-green">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-comments fa-5x"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
<?php

if (is_admin ($_SESSION['username'])) {
  $comments_counts = recordCount ('comments');
} else {
  $comments_counts = recordCountOfUser ('comments', 'comment_author', $user);
}

echo "<div class='huge'>{$comments_counts}</div>";
?>
                                      <div>Comments</div>
                                    </div>
                                </div>
                            </div>
                            <a href="comments.php">
                                <div class="panel-footer">
                                    <span class="pull-left">View Details</span>
                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                    <div class="clearfix"></div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Users -->
                    <div class="col-lg-3 col-md-6">
                        <div class="panel panel-yellow">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-user fa-5x"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
<?php
$users_counts = recordCount ('users');
echo "<div class='huge'>{$users_counts}</div>";
?>
                                        <div> Users</div>
                                    </div>
                                </div>
                            </div>
                            <a href="users.php">
                                <div class="panel-footer">
                                    <span class="pull-left">View Details</span>
                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                    <div class="clearfix"></div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="col-lg-3 col-md-6">
                        <div class="panel panel-red">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-list fa-5x"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
<?php
$categories_counts = recordCount ('categories');
echo "<div class='huge'>{$categories_counts}</div>";
?>
                                         <div>Categories</div>
                                    </div>
                                </div>
                            </div>
                            <a href="categories.php">
                                <div class="panel-footer">
                                    <span class="pull-left">View Details</span>
                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                    <div class="clearfix"></div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <!-- End Admin widget -->

                <!-- Chart -->
                <div class="row">
<?php
// Status count for posts table 
$published_posts = checkStatus ('posts', 'post_status', 'Published');
$draft_posts     = checkStatus ('posts', 'post_status', 'Draft');

// Status count for comments table 
$approved_comments   = checkStatus ('comments', 'comment_status', 'Approved');
$unapproved_comments = checkStatus ('comments', 'comment_status', 'Unapproved');

// Status count for Users table 
$approved_users   = checkStatus ('users', 'user_status', 'Approved');
$unapproved_users = checkStatus ('users', 'user_status', 'Unapproved');
?>
                <script type="text/javascript">
                      google.charts.load('current', {'packages':['bar']});
                      google.charts.setOnLoadCallback(drawChart);
                
                      function drawChart() {
                        var data = google.visualization.arrayToDataTable([
                          ['Data', 'Count'],
<?php

$element_text  = [ 'Active Posts', 'Draft Posts', 'Approved Comments', 'Unapproved Comments', 'Approved Users', 'Unapproved Users' ];
$element_count = [ $published_posts, $draft_posts, $approved_comments, $unapproved_comments, $approved_users, $unapproved_users ];

for ($i = 0; $i < 6; $i++) {
  echo "['{$element_text[$i]}', {$element_count[$i]}],";
} 

?>
                        ]);
                
                        var options = {
                          chart: {
                            title: 'Blog Status',
                            subtitle: 'Posts, Comments, Users Status',
                          }
                        };
                
                        var chart = new google.charts.Bar(document.getElementById('columnchart_material'));
                
                        chart.draw(data, google.charts.Bar.convertOptions(options));
                      }
                    </script>
                
                    <div class="col-lg-12">
                      <div id="columnchart_material" style="width: 'auto'; height: 500px;"></div>
                    </div>

                </div>
                <!-- End Chart -->

            </div>
            <!-- /.container-fluid -->

        </div>
<?php
include "includes/admin_footer.php";
?>

<!-- toastr -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<!-- pusher -->
<script src="https://js.pusher.com/4.3/pusher.min.js"></script>
<script>

$(document).ready(function () {
  var key = '<?php echo $app_key; ?>';
  var pusher = new Pusher(key, {
    cluster: 'ap1',
    encrypted: true
  });

  var channel = pusher.subscribe('notifications');
  channel.bind('new_user', function(data) {
    //alert(JSON.stringify(data));
     var message = data.message;
     toastr.success(`${message} just registerd`);
     console.log(message);
  });
});
  //Pusher.logToConsole = true;
</script>
