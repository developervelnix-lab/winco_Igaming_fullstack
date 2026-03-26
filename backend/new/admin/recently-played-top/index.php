<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
session_cache_limiter("private_no_expire");

define("ACCESS_SECURITY","true");
include '../../security/config.php';
include '../../security/constants.php';
include '../access_validate.php';

session_start();
$accessObj = new AccessValidate();
if($accessObj->validate()!="true"){
    header('location:../logout-account');
}

$searched = "";
if (isset($_POST['submit'])){
  $searched = $_POST['searchinp'];
}

$content = 15;
$page_num = isset($_GET['page_num']) ? $_GET['page_num'] : 1;
$offset = ($page_num - 1) * $content;

// Get current server date/time for debugging
$server_date = date('Y-m-d H:i:s');
$today_date = date('Y-m-d');
$yesterday_date = date('Y-m-d', strtotime('-1 day'));

// Get TODAY'S game statistics - total bet amounts by game
$game_stats_sql = "SELECT mp.tbl_project_name as game, 
                   SUM(mp.tbl_match_cost) as total_bet_amount,
                   COUNT(*) as bet_count 
                   FROM tblmatchplayed mp
                   WHERE DATE(mp.created_at) = '$today_date' 
                   GROUP BY mp.tbl_project_name 
                   ORDER BY total_bet_amount DESC 
                   LIMIT 5";
$game_stats_result = mysqli_query($conn, $game_stats_sql);
$game_stats = [];
while ($row = mysqli_fetch_assoc($game_stats_result)) {
    $game_stats[] = $row;
}

// Get TODAY'S top players for each game
$top_players_by_game_sql = "SELECT mp.tbl_project_name as game, 
                           mp.tbl_user_id as user_id,
                           SUM(mp.tbl_match_cost) as total_bet_amount
                           FROM tblmatchplayed mp
                           WHERE DATE(mp.created_at) = '$today_date'
                           GROUP BY mp.tbl_project_name, mp.tbl_user_id
                           ORDER BY mp.tbl_project_name, total_bet_amount DESC";
$top_players_result = mysqli_query($conn, $top_players_by_game_sql);
$top_players_by_game = [];
while ($row = mysqli_fetch_assoc($top_players_result)) {
    if (!isset($top_players_by_game[$row['game']])) {
        $top_players_by_game[$row['game']] = [];
    }
    if (count($top_players_by_game[$row['game']]) < 3) { // Get top 3 players per game
        $top_players_by_game[$row['game']][] = $row;
    }
}

// Today's Top Bets - Above ₹5000 (using explicit date)
$todayTopBetsSql = "SELECT * FROM tblmatchplayed 
                    WHERE DATE(created_at) = '$today_date' 
                    AND tbl_match_cost > 5000
                    ORDER BY tbl_match_cost DESC LIMIT 5";
$todayTopBetsResult = mysqli_query($conn, $todayTopBetsSql);
$todayTopBets = [];
while ($row = mysqli_fetch_assoc($todayTopBetsResult)) {
    $todayTopBets[] = $row;
}

// Yesterday's Top Bets - Above ₹5000 (using explicit date)
$yesterdayTopBetsSql = "SELECT * FROM tblmatchplayed 
                        WHERE DATE(created_at) = '$yesterday_date'
                        AND tbl_match_cost > 5000
                        ORDER BY tbl_match_cost DESC LIMIT 5";
$yesterdayTopBetsResult = mysqli_query($conn, $yesterdayTopBetsSql);
$yesterdayTopBets = [];
while ($row = mysqli_fetch_assoc($yesterdayTopBetsResult)) {
    $yesterdayTopBets[] = $row;
}

// Get a sample of yesterday's bets for debugging (without the 5000 filter)
$yesterdaySampleSql = "SELECT * FROM tblmatchplayed 
                      WHERE DATE(created_at) = '$yesterday_date'
                      ORDER BY tbl_match_cost DESC LIMIT 3";
$yesterdaySampleResult = mysqli_query($conn, $yesterdaySampleSql);
$yesterdaySample = [];
while ($row = mysqli_fetch_assoc($yesterdaySampleResult)) {
    $yesterdaySample[] = $row;
}

// Alternative approach: Get bets from the last 24-48 hours
$alt_yesterday_sql = "SELECT * FROM tblmatchplayed 
                     WHERE created_at >= NOW() - INTERVAL 2 DAY 
                     AND created_at < NOW() - INTERVAL 1 DAY
                     AND tbl_match_cost > 5000
                     ORDER BY tbl_match_cost DESC LIMIT 5";
$alt_yesterday_result = mysqli_query($conn, $alt_yesterday_sql);
$alt_yesterday_bets = [];
while ($row = mysqli_fetch_assoc($alt_yesterday_result)) {
    $alt_yesterday_bets[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php include "../header_contents.php" ?>
  <title><?php echo $APP_NAME; ?>: Betting Dashboard</title>
  <link href='../style.css' rel='stylesheet'>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary-color: #4361ee;
      --secondary-color: #3f37c9;
      --success-color: #4cc9f0;
      --info-color: #4895ef;
      --warning-color: #f72585;
      --danger-color: #e63946;
      --light-color: #f8f9fa;
      --dark-color: #212529;
    }
    
    body {
      background-color: #f5f7fb;
      font-family: 'Poppins', sans-serif;
    }
    
    .dashboard-container {
      padding: 20px;
    }
    
    .card {
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
      border: none;
      margin-bottom: 24px;
      transition: transform 0.2s;
    }
    
    .card:hover {
      transform: translateY(-5px);
    }
    
    .card-header {
      border-radius: 10px 10px 0 0 !important;
      font-weight: 600;
      padding: 15px 20px;
    }
    
    .stats-card {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      padding: 20px;
      border-radius: 10px;
      height: 100%;
    }
    
    .stats-card h3 {
      font-size: 1.2rem;
      margin-bottom: 15px;
    }
    
    .stats-card .value {
      font-size: 1.8rem;
      font-weight: 700;
    }
    
    .stats-card .icon {
      font-size: 2.5rem;
      opacity: 0.8;
    }
    
    .table {
      margin-bottom: 0;
    }
    
    .table th {
      font-weight: 600;
      color: #495057;
    }
    
    .table td, .table th {
      padding: 12px 15px;
      vertical-align: middle;
    }
    
    .badge-profit {
      background-color: rgba(25, 135, 84, 0.1);
      color: #198754;
      font-weight: 500;
      padding: 5px 10px;
      border-radius: 6px;
    }
    
    .badge-loss {
      background-color: rgba(220, 53, 69, 0.1);
      color: #dc3545;
      font-weight: 500;
      padding: 5px 10px;
      border-radius: 6px;
    }
    
    .btn-refresh {
      background-color: var(--primary-color);
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 6px;
      transition: all 0.3s;
    }
    
    .btn-refresh:hover {
      background-color: var(--secondary-color);
      transform: scale(1.05);
    }
    
    .btn-refresh i {
      margin-right: 5px;
    }
    
    .search-container {
      background-color: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
      margin-bottom: 24px;
    }
    
    .chart-container {
      height: 250px;
      position: relative;
    }
    
    .pagination {
      margin-top: 20px;
    }
    
    .pagination .page-link {
      border-radius: 5px;
      margin: 0 3px;
      color: var(--primary-color);
    }
    
    .pagination .page-item.active .page-link {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }
    
    .game-stat-item {
      display: flex;
      flex-direction: column;
      padding: 15px;
      border-bottom: 1px solid rgba(0,0,0,0.05);
      background-color: #f8f9fa;
      margin-bottom: 10px;
      border-radius: 8px;
    }
    
    .game-stat-item:last-child {
      border-bottom: none;
    }
    
    .game-header {
      display: flex;
      justify-content: space-between;
      margin-bottom: 10px;
    }
    
    .game-name {
      font-weight: 600;
      font-size: 1.1rem;
      color: var(--primary-color);
    }
    
    .game-amount {
      font-weight: 700;
      color: var(--primary-color);
    }
    
    .player-list {
      margin-top: 8px;
    }
    
    .player-item {
      display: flex;
      justify-content: space-between;
      padding: 5px 0;
      border-top: 1px dashed rgba(0,0,0,0.1);
    }
    
    .player-id {
      font-weight: 500;
    }
    
    .player-amount {
      color: #555;
    }
    
    .spinner-border-sm {
      width: 1rem;
      height: 1rem;
      border-width: 0.2em;
      display: none;
    }
    
    .loading .spinner-border-sm {
      display: inline-block;
    }
    
    .loading .refresh-text {
      display: none;
    }
    
    .timestamp {
      font-size: 0.8rem;
      color: #6c757d;
      margin-top: 5px;
      text-align: right;
    }
    
    .nav-tabs .nav-link {
      border: none;
      color: #495057;
      font-weight: 500;
      padding: 10px 15px;
    }
    
    .nav-tabs .nav-link.active {
      color: var(--primary-color);
      border-bottom: 2px solid var(--primary-color);
      background: transparent;
    }
    
    @media (max-width: 768px) {
      .dashboard-container {
        padding: 10px;
      }
      
      .card {
        margin-bottom: 15px;
      }
      
      .stats-card {
        margin-bottom: 15px;
      }
    }
  </style>
</head>
<body>
  <div class="dashboard-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="mb-0"><i class="fas fa-chart-line me-2"></i>Betting Dashboard</h2>
      <button id="refreshButton" class="btn btn-refresh" onclick="refreshPage()">
        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
        <span class="refresh-text"><i class="fas fa-sync-alt"></i> Refresh Data</span>
      </button>
    </div>
    
    <!-- Search & Export -->
    <div class="search-container">
      <form method="POST" class="row g-3">
        <div class="col-md-8">
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" name="searchinp" placeholder="Search by ID, Game or User ID" class="form-control" value="<?php echo $searched; ?>" />
          </div>
        </div>
        <div class="col-md-4">
          <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button class="btn btn-primary" name="submit" type="submit">
              <i class="fas fa-search me-1"></i> Search
            </button>
            <button class="btn btn-outline-secondary" type="button" onclick="exportPDF('recent-played', 'table')">
              <i class="fas fa-file-pdf me-1"></i> Export PDF
            </button>
          </div>
        </div>
      </form>
    </div>
    
    <!-- Game Statistics -->
    <div class="row">
      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-header bg-primary text-white">
            <i class="fas fa-trophy me-2"></i> Today's Top Games by Bet Amount
          </div>
          <div class="card-body p-3">
            <?php if (count($game_stats) > 0): ?>
              <?php foreach ($game_stats as $game): ?>
                <div class="game-stat-item">
                  <div class="game-header">
                    <div class="game-name"><?php echo $game['game']; ?></div>
                    <div class="game-amount">₹<?php echo number_format($game['total_bet_amount'], 2); ?></div>
                  </div>
                  
                  <div class="player-list">
                    <div class="fw-bold mb-1 text-muted"><small>Top Players Today:</small></div>
                    <?php if (isset($top_players_by_game[$game['game']])): ?>
                      <?php foreach ($top_players_by_game[$game['game']] as $player): ?>
                        <div class="player-item">
                          <div class="player-id"><?php echo $player['user_id']; ?></div>
                          <div class="player-amount">₹<?php echo number_format($player['total_bet_amount'], 2); ?></div>
                        </div>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <div class="text-muted">No player data available</div>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <p class="text-center text-muted">No game statistics available for today</p>
            <?php endif; ?>
            <div class="timestamp">Last updated: <?php echo date('d M Y, H:i:s'); ?></div>
          </div>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-header bg-success text-white">
            <i class="fas fa-calendar-day me-2"></i> Today's Top Bets (>₹5000)
          </div>
          <div class="card-body p-0">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>User ID</th>
                  <th>Game</th>
                  <th>Amount</th>
                </tr>
              </thead>
              <tbody>
                <?php if (count($todayTopBets) > 0): ?>
                  <?php foreach ($todayTopBets as $bet): ?>
                    <tr>
                      <td><?php echo $bet['tbl_user_id']; ?></td>
                      <td><?php echo $bet['tbl_project_name']; ?></td>
                      <td>₹<?php echo number_format($bet['tbl_match_cost'], 2); ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="3" class="text-center">No bets over ₹5000 today</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
            <div class="p-2">
              <div class="timestamp">Last updated: <?php echo date('d M Y, H:i:s'); ?></div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-header bg-warning text-white">
            <i class="fas fa-calendar-week me-2"></i> Yesterday's Top Bets (>₹5000)
          </div>
          <div class="card-body p-0">
            <ul class="nav nav-tabs" id="yesterdayTabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="date-tab" data-bs-toggle="tab" data-bs-target="#date-content" type="button" role="tab">By Date</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="hours-tab" data-bs-toggle="tab" data-bs-target="#hours-content" type="button" role="tab">By Hours</button>
              </li>
            </ul>
            
            <div class="tab-content" id="yesterdayTabContent">
              <!-- Yesterday by Date (midnight to midnight) -->
              <div class="tab-pane fade show active" id="date-content" role="tabpanel">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>User ID</th>
                      <th>Game</th>
                      <th>Amount</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (count($yesterdayTopBets) > 0): ?>
                      <?php foreach ($yesterdayTopBets as $bet): ?>
                        <tr>
                          <td><?php echo $bet['tbl_user_id']; ?></td>
                          <td><?php echo $bet['tbl_project_name']; ?></td>
                          <td>₹<?php echo number_format($bet['tbl_match_cost'], 2); ?></td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <?php if (count($yesterdaySample) > 0): ?>
                        <tr><td colspan="3" class="text-center">No bets over ₹5000 yesterday, but found <?php echo count($yesterdaySample); ?> bets with lower amounts</td></tr>
                      <?php else: ?>
                        <tr><td colspan="3" class="text-center">No bets found for yesterday (<?php echo $yesterday_date; ?>)</td></tr>
                      <?php endif; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
              
              <!-- Yesterday by Hours (24-48 hours ago) -->
              <div class="tab-pane fade" id="hours-content" role="tabpanel">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>User ID</th>
                      <th>Game</th>
                      <th>Amount</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (count($alt_yesterday_bets) > 0): ?>
                      <?php foreach ($alt_yesterday_bets as $bet): ?>
                        <tr>
                          <td><?php echo $bet['tbl_user_id']; ?></td>
                          <td><?php echo $bet['tbl_project_name']; ?></td>
                          <td>₹<?php echo number_format($bet['tbl_match_cost'], 2); ?></td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr><td colspan="3" class="text-center">No bets over ₹5000 in the 24-48 hour period</td></tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
            
            <div class="p-2">
              <div class="timestamp">Server time: <?php echo $server_date; ?><br>Yesterday: <?php echo $yesterday_date; ?></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Top Bettors Leaderboard -->
    <div class="card mt-4">
      <div class="card-header bg-dark text-white">
        <i class="fas fa-crown me-2"></i> Top Bettors - Latest High Bets
      </div>
      <div class="card-body p-0">
        <table class="table table-hover table-striped mb-0">
          <thead class="table-light">
            <tr>
              <th>No</th>
              <th>User ID</th>
              <th>Game</th>
              <th>Bet Amount</th>
              <th>Profit</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php
            // Top bettors including all accounts
            $top_bet_sql = "SELECT * FROM tblmatchplayed 
                           ORDER BY id DESC, tbl_match_cost DESC LIMIT 100";
            $top_bet_result = mysqli_query($conn, $top_bet_sql);
            $user_game_pairs = [];
            $i = 1;

            while ($top = mysqli_fetch_assoc($top_bet_result)) {
              $key = $top['tbl_user_id'] . '-' . $top['tbl_project_name'];
              if (isset($user_game_pairs[$key])) continue;
              $user_game_pairs[$key] = true;

              $status_class = ($top['tbl_match_status'] == 'profit') ? 'badge-profit' : 'badge-loss';
              $status_text = ucfirst($top['tbl_match_status']);

              echo "<tr>
                      <td>{$i}</td>
                      <td>{$top['tbl_user_id']}</td>
                      <td>{$top['tbl_project_name']}</td>
                      <td>₹" . number_format($top['tbl_match_cost'], 2) . "</td>
                      <td>₹" . number_format($top['tbl_match_profit'], 2) . "</td>
                      <td><span class='{$status_class}'>{$status_text}</span></td>
                    </tr>";
              $i++;
              if ($i > 10) break;
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
    
    <!-- Recently Played Table -->
    <div class="card mt-4">
      <div class="card-header bg-primary text-white">
        <i class="fas fa-history me-2"></i> Recent Records
      </div>
      <div class="card-body p-0">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>No</th>
              <th>User ID</th>
              <th>Game</th>
              <th>Bet Amount</th>
              <th>Profit</th>
              <th>Status</th>
              <th>Time</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $indexVal = 1;
            if($searched != ""){
              $play_records_sql = "SELECT * FROM tblmatchplayed 
                                  WHERE tbl_period_id LIKE '%$searched%' 
                                  OR tbl_project_name LIKE '%$searched%' 
                                  OR tbl_user_id LIKE '%$searched%'
                                  ORDER BY id DESC LIMIT 100";
            } else {
              $play_records_sql = "SELECT * FROM tblmatchplayed 
                                  ORDER BY id DESC LIMIT {$offset},{$content}";
            }
            $play_records_result = mysqli_query($conn, $play_records_sql) or die('Query Failed');

            if (mysqli_num_rows($play_records_result) > 0) {
              while ($row = mysqli_fetch_assoc($play_records_result)) {
                $match_status = $row['tbl_match_status'];
                $status_class = ($match_status == 'profit') ? 'badge-profit' : 'badge-loss';
                $status_text = ucfirst($match_status);
                
                // Format the date
                $date = new DateTime($row['created_at']);
                $formatted_date = $date->format('d M, H:i');
            ?>
              <tr>
                <td><?php echo $indexVal++; ?></td>
                <td><?php echo $row['tbl_user_id']; ?></td>
                <td><?php echo $row['tbl_project_name']; ?></td>
                <td>₹<?php echo number_format($row['tbl_match_cost'], 2); ?></td>
                <td>₹<?php echo number_format($row['tbl_match_profit'], 2); ?></td>
                <td><span class="<?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                <!--<td><?php echo $formatted_date; ?></td>-->
                
                <td><?php echo $row['tbl_time_stamp'] ?></td>

                
                
              </tr>
            <?php }} else { ?>
              <tr><td colspan="7" class="text-center">No data found!</td></tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Pagination -->
    <?php
    // Count query for all accounts
    $count_sql = "SELECT COUNT(*) as total FROM tblmatchplayed";
    $count_result = mysqli_query($conn, $count_sql);
    $total_records = mysqli_fetch_assoc($count_result)['total'];
    $total_pages = ceil($total_records / $content);
    ?>
    <nav>
      <ul class="pagination justify-content-end">
        <li class="page-item <?php if ($page_num <= 1) echo 'disabled'; ?>">
          <a class="page-link" href="?page_num=<?php echo $page_num - 1; ?>">
            <i class="fas fa-chevron-left"></i> Previous
          </a>
        </li>
        
        <?php
        $start_page = max(1, $page_num - 2);
        $end_page = min($total_pages, $page_num + 2);
        
        for ($i = $start_page; $i <= $end_page; $i++) {
          echo '<li class="page-item ' . ($page_num == $i ? 'active' : '') . '">
                  <a class="page-link" href="?page_num=' . $i . '">' . $i . '</a>
                </li>';
        }
        ?>
        
        <li class="page-item <?php if ($page_num >= $total_pages) echo 'disabled'; ?>">
          <a class="page-link" href="?page_num=<?php echo $page_num + 1; ?>">
            Next <i class="fas fa-chevron-right"></i>
          </a>
        </li>
      </ul>
    </nav>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Function to refresh the page with proper cache-busting
    function refreshPage() {
      const refreshBtn = document.getElementById('refreshButton');
      refreshBtn.classList.add('loading');
      
      // Add a timestamp to the URL to prevent caching
      const timestamp = new Date().getTime();
      const currentUrl = window.location.href.split('?')[0]; // Remove any existing query params
      
      // Redirect after a short delay to show loading animation
      setTimeout(() => {
        window.location.href = currentUrl + '?_=' + timestamp;
      }, 500);
    }
    
    // Function to export table as PDF
    function exportPDF(elementId, type) {
      alert('PDF export functionality will be implemented here');
      // This would typically use a library like jsPDF or call a server-side PDF generation endpoint
    }
    
    // Initialize any tooltips and tabs
    document.addEventListener('DOMContentLoaded', function() {
      const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
      });
      
      // Initialize tabs
      const tabEl = document.querySelector('button[data-bs-toggle="tab"]')
      tabEl && tabEl.addEventListener('shown.bs.tab', function (event) {
        // Update active tab content
      });
    });
  </script>
</body>
</html>
