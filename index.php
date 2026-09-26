<?php
session_start();

if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

$role = $_SESSION['role'];
$username = $_SESSION['username'];
$view_mode = isset($_GET['view']) ? $_GET['view'] : 'all';
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Filter parameters
$filter_type = isset($_GET['filter_type']) ? $_GET['filter_type'] : '';
$filter_disp = isset($_GET['filter_disp']) ? $_GET['filter_disp'] : '';

// Lead Edit Logic
if (isset($_POST['edit_lead'])) {
    $id = $_POST['lead_id'];
    $client_name = $_POST['client_name'];
    $company_name = $_POST['company_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $service = $_POST['service'];
    $timeline = $_POST['timeline'];
    $budget = $_POST['budget'];
    $callback_time = $_POST['callback_time'];
    $assigned_to = ($role == 'subadmin') ? $username : $_POST['assigned_to'];
    $lead_type = $_POST['lead_type'];
    $disposition = $_POST['disposition'];
    $notes = $_POST['notes'];

    $stmt = $conn->prepare("UPDATE leads SET client_name=?, company_name=?, email=?, phone=?, service=?, timeline=?, budget=?, callback_time=?, assigned_to=?, lead_type=?, disposition=?, notes=? WHERE id=?");
    $stmt->bind_param("ssssssssssssi", $client_name, $company_name, $email, $phone, $service, $timeline, $budget, $callback_time, $assigned_to, $lead_type, $disposition, $notes, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php?page=all_leads" . ($view_mode != 'all' ? "&view=$view_mode" : ""));
    exit();
}

// Lead Add karne ka logic
if (isset($_POST['add_lead'])) {
    $client_name = $_POST['client_name'];
    $company_name = $_POST['company_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $service = $_POST['service'];
    $timeline = $_POST['timeline'];
    $budget = $_POST['budget'];
    $callback_time = $_POST['callback_time'];
    $assigned_to = ($role == 'subadmin') ? $username : $_POST['assigned_to'];
    $lead_type = $_POST['lead_type'];
    $disposition = $_POST['disposition'];
    $notes = $_POST['notes'];
    $added_by = $username; 

    $stmt = $conn->prepare("INSERT INTO leads (client_name, company_name, email, phone, service, timeline, budget, callback_time, assigned_to, lead_type, disposition, notes, added_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssssss", $client_name, $company_name, $email, $phone, $service, $timeline, $budget, $callback_time, $assigned_to, $lead_type, $disposition, $notes, $added_by);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php?page=all_leads" . ($view_mode != 'all' ? "&view=$view_mode" : ""));
    exit();
}

// Leads Fetching Logic with Strict Role Restrictions
$query = "SELECT * FROM leads WHERE 1=1";

if ($role == 'subadmin') {
    $query .= " AND (assigned_to = '$username' OR added_by = '$username')";
} else {
    if ($view_mode == 'Nishant') {
        $query .= " AND (assigned_to = 'Nishant' OR added_by = 'Nishant')";
    } elseif ($view_mode == 'Ajay') {
        $query .= " AND (assigned_to = 'Ajay' OR added_by = 'Ajay')";
    } elseif ($view_mode == 'Varun') {
        $query .= " AND (assigned_to = 'Varun' OR added_by = 'Varun')";
    } elseif ($view_mode == 'Akanksha') {
        $query .= " AND (assigned_to = 'Akanksha' OR added_by = 'Akanksha')";
    }
}

if (!empty($filter_type)) {
    $query .= " AND lead_type = '$filter_type'";
}

if (!empty($filter_disp)) {
    $query .= " AND disposition = '$filter_disp'";
}

$query .= " ORDER BY callback_time ASC";

$result = $conn->query($query);
$leads_array = [];
while ($row = $result->fetch_assoc()) {
    $leads_array[] = $row;
}

$edit_lead_data = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $edit_res = $conn->query("SELECT * FROM leads WHERE id = $edit_id");
    if ($edit_res->num_rows > 0) {
        $edit_lead_data = $edit_res->fetch_assoc();
    }
}

$total_leads = count($leads_array);

// Metrics calculations
$hot_leads = 0;
$warm_leads = 0;
$cold_leads = 0;
$meetings_count = 0;
$tushar_count = 0;
$nishant_count = 0;
$ajay_count = 0;
$varun_count = 0;
$akanksha_count = 0;

$service_counts = [];
$disp_counts = [];

foreach ($leads_array as $l) {
    if ($l['lead_type'] == 'Hot') $hot_leads++;
    if ($l['lead_type'] == 'Warm') $warm_leads++;
    if ($l['lead_type'] == 'Cold') $cold_leads++;
    if ($l['disposition'] == 'Meeting Scheduled') $meetings_count++;
    
    if ($l['assigned_to'] == 'Tushar') $tushar_count++;
    if ($l['assigned_to'] == 'Nishant') $nishant_count++;
    if ($l['assigned_to'] == 'Ajay') $ajay_count++;
    if ($l['assigned_to'] == 'Varun') $varun_count++;
    if ($l['assigned_to'] == 'Akanksha') $akanksha_count++;

    $srv = $l['service'];
    $service_counts[$srv] = isset($service_counts[$srv]) ? $service_counts[$srv] + 1 : 1;

    $dsp = $l['disposition'];
    $disp_counts[$dsp] = isset($disp_counts[$dsp]) ? $disp_counts[$dsp] + 1 : 1;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syncro - Personalized Leads Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 font-sans text-slate-800 flex h-screen overflow-hidden">

    <!-- LEFT SIDEBAR -->
    <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col justify-between border-r border-slate-800 shrink-0 hidden md:flex">
        <div>
            <div class="p-6 border-b border-slate-800 flex items-center space-x-3">
                <img src="logo.png" alt="Logo" class="h-8 w-auto object-contain" onerror="this.style.display='none'">
                <div>
                    <h1 class="text-sm font-extrabold text-white tracking-wide">Syncro <span class="text-blue-400">Manager</span></h1>
                    <span class="text-[9px] text-slate-400 block font-medium">Syntheriq Technologies</span>
                </div>
            </div>

            <nav class="p-4 space-y-2">
                <a href="index.php?page=dashboard<?php echo $view_mode!='all'?'&view='.$view_mode:''; ?>" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-xs font-bold transition <?php echo ($page == 'dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800 text-slate-300'; ?>">
                    <span>📊</span> <span>Dashboard</span>
                </a>
                <a href="index.php?page=all_leads<?php echo $view_mode!='all'?'&view='.$view_mode:''; ?>" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-xs font-bold transition <?php echo ($page == 'all_leads') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800 text-slate-300'; ?>">
                    <span>📋</span> <span>All Leads</span>
                </a>
                <a href="index.php?page=add_lead<?php echo $view_mode!='all'?'&view='.$view_mode:''; ?>" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-xs font-bold transition <?php echo ($page == 'add_lead') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800 text-slate-300'; ?>">
                    <span>➕</span> <span>Add Leads</span>
                </a>
                <a href="index.php?page=calendar<?php echo $view_mode!='all'?'&view='.$view_mode:''; ?>" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-xs font-bold transition <?php echo ($page == 'calendar') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800 text-slate-300'; ?>">
                    <span>📅</span> <span>Calendar & Schedule</span>
                </a>
                <a href="index.php?page=analysis<?php echo $view_mode!='all'?'&view='.$view_mode:''; ?>" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-xs font-bold transition <?php echo ($page == 'analysis') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'hover:bg-slate-800 text-slate-300'; ?>">
                    <span>📈</span> <span>Lead Analysis</span>
                </a>
            </nav>
        </div>

        <div class="p-4 border-t border-slate-800 text-xs text-slate-400">
            <div class="bg-slate-800/80 p-3.5 rounded-2xl border border-slate-700/50 shadow-inner">
                <p class="text-[10px] uppercase tracking-wider text-slate-400 font-semibold">Logged in as</p>
                <p class="font-extrabold text-white capitalize mt-0.5 text-sm"><?php echo $username; ?> <span class="text-xs font-normal text-blue-400">(<?php echo $role; ?>)</span></p>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT WRAPPER -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        
        <!-- TOP NAVBAR -->
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center shadow-sm shrink-0 w-full">
            <h2 class="text-lg font-extrabold text-slate-900 capitalize tracking-tight">
                <?php 
                    if($page=='dashboard') echo 'Dashboard Overview';
                    elseif($page=='all_leads') echo 'All Leads Directory';
                    elseif($page=='add_lead') echo $edit_lead_data ? 'Edit Lead Details' : 'Add New Lead Form';
                    elseif($page=='calendar') echo 'Meetings & Callback Calendar';
                    elseif($page=='analysis') echo 'Lead Analytics & Reports';
                ?>
            </h2>

            <div class="flex items-center space-x-2 flex-wrap">
                <?php if ($role == 'admin'): ?>
                    <a href="index.php?page=<?php echo $page; ?>" class="px-3 py-1.5 rounded-xl text-xs font-bold transition shadow-sm <?php echo $view_mode=='all'?'bg-emerald-600 text-white shadow-emerald-600/20':'bg-slate-100 text-slate-700 hover:bg-slate-200'; ?>">👑 All</a>
                    <a href="index.php?page=<?php echo $page; ?>&view=Nishant" class="px-3 py-1.5 rounded-xl text-xs font-bold transition shadow-sm <?php echo $view_mode=='Nishant'?'bg-purple-600 text-white shadow-purple-600/20':'bg-slate-100 text-slate-700 hover:bg-slate-200'; ?>">🤝 Nishant</a>
                    <a href="index.php?page=<?php echo $page; ?>&view=Ajay" class="px-3 py-1.5 rounded-xl text-xs font-bold transition shadow-sm <?php echo $view_mode=='Ajay'?'bg-purple-600 text-white shadow-purple-600/20':'bg-slate-100 text-slate-700 hover:bg-slate-200'; ?>">🤝 Ajay</a>
                    <a href="index.php?page=<?php echo $page; ?>&view=Varun" class="px-3 py-1.5 rounded-xl text-xs font-bold transition shadow-sm <?php echo $view_mode=='Varun'?'bg-purple-600 text-white shadow-purple-600/20':'bg-slate-100 text-slate-700 hover:bg-slate-200'; ?>">🤝 Varun</a>
                    <a href="index.php?page=<?php echo $page; ?>&view=Akanksha" class="px-3 py-1.5 rounded-xl text-xs font-bold transition shadow-sm <?php echo $view_mode=='Akanksha'?'bg-purple-600 text-white shadow-purple-600/20':'bg-slate-100 text-slate-700 hover:bg-slate-200'; ?>">🤝 Akanksha</a>
                <?php endif; ?>

                <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white px-3.5 py-1.5 rounded-xl text-xs font-bold transition shadow-md shadow-red-600/20">Logout</a>
            </div>
        </header>

        <!-- DYNAMIC PAGE CONTENT CONTAINER -->
        <main class="flex-1 overflow-y-auto p-8 bg-slate-100/70 w-full">

            <?php if ($page == 'dashboard'): ?>
                <div class="space-y-6 w-full">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white p-8 rounded-3xl shadow-xl flex justify-between items-center">
                        <div>
                            <h2 class="text-3xl font-extrabold tracking-tight">Welcome back, <?php echo $username; ?>! 👋</h2>
                            <p class="text-blue-100 text-xs mt-1.5 font-medium">Here is your personal performance & callback overview.</p>
                        </div>
                        <div class="hidden md:flex space-x-3">
                            <a href="index.php?page=add_lead" class="bg-white text-blue-600 hover:bg-blue-50 px-5 py-2.5 rounded-2xl text-xs font-bold transition shadow-lg">➕ Add New Lead</a>
                            <a href="index.php?page=calendar" class="bg-blue-500/80 hover:bg-blue-500 text-white px-5 py-2.5 rounded-2xl text-xs font-bold transition shadow-lg">📅 View Calendar</a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center justify-between hover:shadow-md transition">
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Leads</p>
                                <h3 class="text-3xl font-extrabold text-slate-900 mt-1"><?php echo $total_leads; ?></h3>
                            </div>
                            <div class="bg-blue-50 text-blue-600 p-3.5 rounded-2xl text-2xl font-bold shadow-inner">📋</div>
                        </div>

                        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center justify-between hover:shadow-md transition">
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Hot Leads</p>
                                <h3 class="text-3xl font-extrabold text-red-600 mt-1"><?php echo $hot_leads; ?></h3>
                            </div>
                            <div class="bg-red-50 text-red-600 p-3.5 rounded-2xl text-2xl font-bold shadow-inner">🔥</div>
                        </div>

                        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center justify-between hover:shadow-md transition">
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Meetings Scheduled</p>
                                <h3 class="text-3xl font-extrabold text-emerald-600 mt-1"><?php echo $meetings_count; ?></h3>
                            </div>
                            <div class="bg-emerald-50 text-emerald-600 p-3.5 rounded-2xl text-2xl font-bold shadow-inner">🤝</div>
                        </div>

                        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center justify-between hover:shadow-md transition">
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Warm / Cold</p>
                                <h3 class="text-3xl font-extrabold text-amber-600 mt-1"><?php echo ($warm_leads + $cold_leads); ?></h3>
                            </div>
                            <div class="bg-amber-50 text-amber-600 p-3.5 rounded-2xl text-2xl font-bold shadow-inner">⚡</div>
                        </div>
                    </div>

                    <!-- ADMIN ONLY: Partner Breakdown Cards -->
                    <?php if ($role == 'admin'): ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-xs font-bold text-slate-600 uppercase">Tushar</span>
                                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-xl font-extrabold"><?php echo $tushar_count; ?></span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden"><div class="bg-blue-600 h-full rounded-full" style="width: <?php echo $total_leads>0?($tushar_count/$total_leads)*100:0; ?>%"></div></div>
                        </div>

                        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-xs font-bold text-slate-600 uppercase">Nishant</span>
                                <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-xl font-extrabold"><?php echo $nishant_count; ?></span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden"><div class="bg-purple-600 h-full rounded-full" style="width: <?php echo $total_leads>0?($nishant_count/$total_leads)*100:0; ?>%"></div></div>
                        </div>

                        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-xs font-bold text-slate-600 uppercase">Ajay</span>
                                <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-xl font-extrabold"><?php echo $ajay_count; ?></span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden"><div class="bg-emerald-600 h-full rounded-full" style="width: <?php echo $total_leads>0?($ajay_count/$total_leads)*100:0; ?>%"></div></div>
                        </div>

                        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-xs font-bold text-slate-600 uppercase">Varun</span>
                                <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-xl font-extrabold"><?php echo $varun_count; ?></span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden"><div class="bg-amber-600 h-full rounded-full" style="width: <?php echo $total_leads>0?($varun_count/$total_leads)*100:0; ?>%"></div></div>
                        </div>

                        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-xs font-bold text-slate-600 uppercase">Akanksha</span>
                                <span class="text-xs bg-rose-100 text-rose-700 px-2 py-0.5 rounded-xl font-extrabold"><?php echo $akanksha_count; ?></span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden"><div class="bg-rose-600 h-full rounded-full" style="width: <?php echo $total_leads>0?($akanksha_count/$total_leads)*100:0; ?>%"></div></div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm w-full">
                        <div class="flex justify-between items-center mb-4 pb-3 border-b border-slate-100">
                            <h3 class="text-base font-extrabold text-slate-900">⏰ Upcoming Callbacks & Recent Leads</h3>
                            <a href="index.php?page=all_leads" class="text-xs font-bold text-blue-600 hover:underline">View All Leads →</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wider font-extrabold">
                                        <th class="p-3.5 border-b border-slate-200">Client / Company</th>
                                        <th class="p-3.5 border-b border-slate-200">Phone</th>
                                        <th class="p-3.5 border-b border-slate-200">Service</th>
                                        <th class="p-3.5 border-b border-slate-200">Type & Disp</th>
                                        <th class="p-3.5 border-b border-slate-200">Assigned To</th>
                                        <th class="p-3.5 border-b border-slate-200">Callback Time</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-sm">
                                    <?php if(empty($leads_array)): ?>
                                        <tr><td colspan="6" class="text-center p-8 text-slate-400 font-medium">No leads found in pipeline.</td></tr>
                                    <?php else: ?>
                                        <?php $count=0; foreach($leads_array as $lead): if($count>=5) break; $count++; ?>
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <td class="p-3.5">
                                                <div class="font-bold text-slate-900"><?php echo htmlspecialchars($lead['client_name']); ?></div>
                                                <div class="text-xs text-slate-500 font-medium"><?php echo htmlspecialchars($lead['company_name']); ?></div>
                                            </td>
                                            <td class="p-3.5"><a href="https://wa.me/<?php echo $lead['phone']; ?>" target="_blank" class="text-blue-600 font-bold hover:underline"><?php echo htmlspecialchars($lead['phone']); ?></a></td>
                                            <td class="p-3.5 text-slate-600 text-xs font-semibold"><?php echo htmlspecialchars($lead['service']); ?></td>
                                            <td class="p-3.5">
                                                <span class="px-2.5 py-1 rounded-xl text-[10px] font-bold shadow-xs <?php echo $lead['lead_type']=='Hot'?'bg-red-100 text-red-700':($lead['lead_type']=='Warm'?'bg-amber-100 text-amber-700':'bg-blue-100 text-blue-700'); ?>"><?php echo $lead['lead_type']; ?></span>
                                                <span class="text-[10px] bg-slate-100 text-slate-700 px-2 py-1 rounded-xl font-semibold ml-1 border border-slate-200"><?php echo $lead['disposition']; ?></span>
                                            </td>
                                            <td class="p-3.5 text-xs font-bold text-slate-700"><?php echo htmlspecialchars($lead['assigned_to']); ?></td>
                                            <td class="p-3.5 font-bold text-red-600 text-xs"><?php echo date('d M Y, h:i A', strtotime($lead['callback_time'])); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            <?php elseif ($page == 'all_leads'): ?>
                <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm w-full space-y-6">
                    <div class="flex flex-col md:flex-row justify-between items-center pb-4 border-b border-slate-100 gap-4">
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-900">📋 Complete Leads Directory <?php echo $view_mode!='all' ? "($view_mode's View)" : ""; ?></h3>
                            <p class="text-xs text-slate-500 font-medium mt-0.5">Manage, filter, and track all client inquiries in real time.</p>
                        </div>
                        
                        <form method="GET" action="index.php" class="flex flex-wrap items-center gap-3">
                            <input type="hidden" name="page" value="all_leads">
                            <?php if($view_mode!='all'): ?><input type="hidden" name="view" value="<?php echo $view_mode; ?>"><?php endif; ?>
                            
                            <select name="filter_type" onchange="this.form.submit()" class="px-3.5 py-2 text-xs font-semibold bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                                <option value="">All Lead Types (Hot/Warm/Cold)</option>
                                <option value="Hot" <?php echo $filter_type=='Hot'?'selected':''; ?>>🔥 Hot Leads</option>
                                <option value="Warm" <?php echo $filter_type=='Warm'?'selected':''; ?>>⚡ Warm Leads</option>
                                <option value="Cold" <?php echo $filter_type=='Cold'?'selected':''; ?>>❄️ Cold Leads</option>
                            </select>

                            <select name="filter_disp" onchange="this.form.submit()" class="px-3.5 py-2 text-xs font-semibold bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                                <option value="">All Dispositions</option>
                                <option value="Interested" <?php echo $filter_disp=='Interested'?'selected':''; ?>>Interested</option>
                                <option value="Callback" <?php echo $filter_disp=='Callback'?'selected':''; ?>>Callback</option>
                                <option value="Not Interested" <?php echo $filter_disp=='Not Interested'?'selected':''; ?>>Not Interested</option>
                                <option value="Meeting Scheduled" <?php echo $filter_disp=='Meeting Scheduled'?'selected':''; ?>>Meeting Scheduled</option>
                                <option value="Call Not Picked Up" <?php echo $filter_disp=='Call Not Picked Up'?'selected':''; ?>>Call Not Picked Up</option>
                                <option value="Cut the Call" <?php echo $filter_disp=='Cut the Call'?'selected':''; ?>>Cut the Call</option>
                                <option value="DND" <?php echo $filter_disp=='DND'?'selected':''; ?>>DND</option>
                                <option value="Switched Off" <?php echo $filter_disp=='Switched Off'?'selected':''; ?>>Switched Off</option>
                                <option value="Wrong Number" <?php echo $filter_disp=='Wrong Number'?'selected':''; ?>>Wrong Number</option>
                            </select>

                            <?php if(!empty($filter_type) || !empty($filter_disp)): ?>
                                <a href="index.php?page=all_leads<?php echo $view_mode!='all'?'&view='.$view_mode:''; ?>" class="text-xs text-blue-600 underline font-extrabold">Clear Filters</a>
                            <?php endif; ?>
                        </form>
                    </div>

                    <div class="overflow-x-auto w-full">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wider font-extrabold">
                                    <th class="p-4 border-b border-slate-200">Client / Company</th>
                                    <th class="p-4 border-b border-slate-200">Phone / Email</th>
                                    <th class="p-4 border-b border-slate-200">Service</th>
                                    <th class="p-4 border-b border-slate-200">Timeline / Budget</th>
                                    <th class="p-4 border-b border-slate-200">Type & Disp</th>
                                    <th class="p-4 border-b border-slate-200">Callback Time</th>
                                    <th class="p-4 border-b border-slate-200">Assigned</th>
                                    <th class="p-4 border-b border-slate-200">Notes</th>
                                    <th class="p-4 border-b border-slate-200 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                <?php if (empty($leads_array)): ?>
                                    <tr><td colspan="9" class="text-center p-8 text-slate-400 font-medium">No leads found.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($leads_array as $lead): ?>
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <td class="p-4">
                                                <div class="font-bold text-slate-900"><?php echo htmlspecialchars($lead['client_name']); ?></div>
                                                <div class="text-xs text-slate-500 font-medium"><?php echo htmlspecialchars($lead['company_name']); ?></div>
                                            </td>
                                            <td class="p-4">
                                                <a href="https://wa.me/<?php echo $lead['phone']; ?>" target="_blank" class="text-blue-600 font-bold block hover:underline"><?php echo htmlspecialchars($lead['phone']); ?></a>
                                                <span class="text-xs text-slate-500 font-medium"><?php echo htmlspecialchars($lead['email']); ?></span>
                                            </td>
                                            <td class="p-4 text-slate-700 text-xs font-semibold"><?php echo htmlspecialchars($lead['service']); ?></td>
                                            <td class="p-4 text-xs font-medium">
                                                <div class="text-slate-600">🕒 <?php echo htmlspecialchars($lead['timeline']); ?></div>
                                                <div class="font-extrabold text-emerald-600 mt-0.5">💰 <?php echo htmlspecialchars($lead['budget']); ?></div>
                                            </td>
                                            <td class="p-4">
                                                <span class="px-2.5 py-1 rounded-xl text-xs font-extrabold shadow-xs <?php echo $lead['lead_type']=='Hot'?'bg-red-100 text-red-700':($lead['lead_type']=='Warm'?'bg-amber-100 text-amber-700':'bg-blue-100 text-blue-700'); ?>">
                                                    <?php echo $lead['lead_type']; ?>
                                                </span>
                                                <div class="mt-1.5"><span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200"><?php echo htmlspecialchars($lead['disposition']); ?></span></div>
                                            </td>
                                            <td class="p-4 font-extrabold text-red-600 text-xs"><?php echo date('d M Y, h:i A', strtotime($lead['callback_time'])); ?></td>
                                            <td class="p-4 text-xs font-extrabold text-slate-700"><?php echo htmlspecialchars($lead['assigned_to']); ?></td>
                                            <td class="p-4 text-slate-500 text-xs max-w-xs truncate font-medium"><?php echo htmlspecialchars($lead['notes']); ?></td>
                                            <td class="p-4 text-center">
                                                <a href="index.php?page=add_lead&edit_id=<?php echo $lead['id']; ?><?php echo $view_mode!='all'?'&view='.$view_mode:''; ?>" class="bg-slate-900 hover:bg-blue-600 text-white px-3.5 py-2 rounded-xl text-xs font-bold transition shadow-sm">Edit</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php elseif ($page == 'add_lead'): ?>
                <div class="bg-white p-10 rounded-3xl border border-slate-200 shadow-xl w-full">
                    <h3 class="text-xl font-extrabold text-slate-900 mb-6 pb-4 border-b border-slate-100 flex items-center gap-3">
                        <span class="bg-blue-50 text-blue-600 p-2.5 rounded-2xl">➕</span> <?php echo $edit_lead_data ? 'Edit Lead Details' : 'Add New Lead Form'; ?>
                    </h3>
                    <form action="index.php?page=all_leads<?php echo $view_mode!='all'?'&view='.$view_mode:''; ?>" method="POST" class="space-y-6">
                        
                        <?php if ($edit_lead_data): ?>
                            <input type="hidden" name="lead_id" value="<?php echo $edit_lead_data['id']; ?>">
                        <?php endif; ?>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Client Name</label>
                                <input type="text" name="client_name" required value="<?php echo $edit_lead_data ? htmlspecialchars($edit_lead_data['client_name']) : ''; ?>" class="w-full px-4 py-3.5 text-sm bg-slate-50 border border-slate-300 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white font-medium transition" placeholder="John Doe">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Company Name</label>
                                <input type="text" name="company_name" value="<?php echo $edit_lead_data ? htmlspecialchars($edit_lead_data['company_name']) : ''; ?>" class="w-full px-4 py-3.5 text-sm bg-slate-50 border border-slate-300 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white font-medium transition" placeholder="ABC Corp">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Email ID</label>
                                <input type="email" name="email" value="<?php echo $edit_lead_data ? htmlspecialchars($edit_lead_data['email']) : ''; ?>" class="w-full px-4 py-3.5 text-sm bg-slate-50 border border-slate-300 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white font-medium transition" placeholder="client@example.com">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Phone / WhatsApp</label>
                                <input type="text" name="phone" required value="<?php echo $edit_lead_data ? htmlspecialchars($edit_lead_data['phone']) : ''; ?>" class="w-full px-4 py-3.5 text-sm bg-slate-50 border border-slate-300 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white font-medium transition" placeholder="919876543210">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">IT Service</label>
                                <select name="service" class="w-full px-4 py-3.5 text-sm bg-slate-50 border border-slate-300 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white font-medium transition">
                                    <?php 
                                        $services = ["Custom Software Development", "Web Development", "Mobile App Development", "UI/UX Design", "SEO & Organic Growth", "Social Media Marketing (SMM)", "Performance Marketing / Ads", "Cloud Computing & Hosting", "Video Editing & Animation"];
                                        foreach($services as $srv) {
                                            $sel = ($edit_lead_data && $edit_lead_data['service'] == $srv) ? 'selected' : '';
                                            echo "<option value='$srv' $sel>$srv</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Assign Lead To</label>
                                <?php if ($role == 'admin'): ?>
                                    <select name="assigned_to" required class="w-full px-4 py-3.5 text-sm bg-slate-50 border border-slate-300 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white font-medium transition">
                                        <?php 
                                            $assigns = ["Tushar", "Nishant", "Ajay", "Varun", "Akanksha"];
                                            foreach($assigns as $ast) {
                                                $sel = ($edit_lead_data && $edit_lead_data['assigned_to'] == $ast) ? 'selected' : '';
                                                echo "<option value='$ast' $sel>$ast</option>";
                                            }
                                        ?>
                                    </select>
                                <?php else: ?>
                                    <input type="text" readonly value="<?php echo $username; ?>" class="w-full px-4 py-3.5 text-sm bg-slate-200 border border-slate-300 rounded-2xl font-bold text-slate-700 cursor-not-allowed">
                                    <input type="hidden" name="assigned_to" value="<?php echo $username; ?>">
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Timeline</label>
                                <input type="text" name="timeline" value="<?php echo $edit_lead_data ? htmlspecialchars($edit_lead_data['timeline']) : ''; ?>" class="w-full px-4 py-3.5 text-sm bg-slate-50 border border-slate-300 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white font-medium transition" placeholder="e.g. 1 Month">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Budget</label>
                                <input type="text" name="budget" value="<?php echo $edit_lead_data ? htmlspecialchars($edit_lead_data['budget']) : ''; ?>" class="w-full px-4 py-3.5 text-sm bg-slate-50 border border-slate-300 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white font-medium transition" placeholder="e.g. ₹50,000">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Lead Type</label>
                                <select name="lead_type" class="w-full px-4 py-3.5 text-sm bg-slate-50 border border-slate-300 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white font-medium transition">
                                    <option value="Warm" <?php echo ($edit_lead_data && $edit_lead_data['lead_type']=='Warm')?'selected':''; ?>>⚡ Warm</option>
                                    <option value="Hot" <?php echo ($edit_lead_data && $edit_lead_data['lead_type']=='Hot')?'selected':''; ?>>🔥 Hot</option>
                                    <option value="Cold" <?php echo ($edit_lead_data && $edit_lead_data['lead_type']=='Cold')?'selected':''; ?>>❄️ Cold</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Disposition Status</label>
                                <select name="disposition" class="w-full px-4 py-3.5 text-sm bg-slate-50 border border-slate-300 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white font-medium transition">
                                    <?php 
                                        $dispositions = ["Interested", "Callback", "Not Interested", "Meeting Scheduled", "Call Not Picked Up", "Cut the Call", "DND", "Switched Off", "Wrong Number"];
                                        foreach($dispositions as $dsp) {
                                            $sel = ($edit_lead_data && $edit_lead_data['disposition'] == $dsp) ? 'selected' : '';
                                            echo "<option value='$dsp' $sel>$dsp</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Callback Date & Time</label>
                                <input type="datetime-local" name="callback_time" required value="<?php echo $edit_lead_data ? date('Y-m-d\TH:i', strtotime($edit_lead_data['callback_time'])) : ''; ?>" class="w-full px-4 py-3.5 text-sm bg-slate-50 border border-slate-300 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white font-medium transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Initial Notes</label>
                            <input type="text" name="notes" value="<?php echo $edit_lead_data ? htmlspecialchars($edit_lead_data['notes']) : ''; ?>" class="w-full px-4 py-3.5 text-sm bg-slate-50 border border-slate-300 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white font-medium transition" placeholder="Requirement details...">
                        </div>

                        <button type="submit" name="<?php echo $edit_lead_data ? 'edit_lead' : 'add_lead'; ?>" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl text-sm font-extrabold transition shadow-xl shadow-blue-600/25">
                            <?php echo $edit_lead_data ? 'Update Lead Details' : 'Save & Assign Lead'; ?>
                        </button>
                    </form>
                </div>

            <?php elseif ($page == 'calendar'): ?>
                <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm w-full">
                    <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-100">
                        <h3 class="text-lg font-extrabold text-slate-900">📅 Monthly Meetings & Callback Calendar</h3>
                        <div class="flex items-center space-x-3">
                            <button onclick="changeMonth(-1)" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-xs font-bold transition shadow-xs">◀ Prev</button>
                            <span id="calendarMonthYear" class="text-sm font-extrabold text-slate-800 w-40 text-center"></span>
                            <button onclick="changeMonth(1)" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-xs font-bold transition shadow-xs">Next ▶</button>
                        </div>
                    </div>
                    <div class="grid grid-cols-7 gap-3 text-center font-extrabold text-xs text-slate-500 uppercase bg-slate-50 py-3 rounded-2xl mb-3">
                        <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                    </div>
                    <div id="calendarDaysGrid" class="grid grid-cols-7 gap-3"></div>
                </div>

            <?php elseif ($page == 'analysis'): ?>
                <div class="space-y-6 w-full">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pipeline</p>
                                <h3 class="text-3xl font-extrabold text-slate-900 mt-1"><?php echo $total_leads; ?></h3>
                            </div>
                            <div class="bg-blue-50 text-blue-600 p-3.5 rounded-2xl text-2xl font-bold">📊</div>
                        </div>

                        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Hot Leads Ratio</p>
                                <h3 class="text-3xl font-extrabold text-red-600 mt-1"><?php echo $hot_leads; ?></h3>
                            </div>
                            <div class="bg-red-50 text-red-600 p-3.5 rounded-2xl text-2xl font-bold">🔥</div>
                        </div>

                        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Meetings Booked</p>
                                <h3 class="text-3xl font-extrabold text-emerald-600 mt-1"><?php echo $meetings_count; ?></h3>
                            </div>
                            <div class="bg-emerald-50 text-emerald-600 p-3.5 rounded-2xl text-2xl font-bold">📅</div>
                        </div>

                        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Active Partners</p>
                                <h3 class="text-3xl font-extrabold text-purple-600 mt-1">5</h3>
                            </div>
                            <div class="bg-purple-50 text-purple-600 p-3.5 rounded-2xl text-2xl font-bold">🤝</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 w-full">
                        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                            <h3 class="text-base font-extrabold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center justify-between">
                                <span>📈 Service-Wise Lead Distribution</span>
                                <span class="text-xs bg-slate-100 text-slate-600 px-2.5 py-1 rounded-xl font-bold">Total: <?php echo count($service_counts); ?> Services</span>
                            </h3>
                            <div class="space-y-4 pt-2">
                                <?php if(empty($service_counts)): ?>
                                    <p class="text-slate-400 text-center py-6 text-sm font-medium">No service data available yet.</p>
                                <?php else: ?>
                                    <?php foreach($service_counts as $srv_name => $srv_cnt): 
                                        $pct = $total_leads > 0 ? ($srv_cnt / $total_leads) * 100 : 0;
                                    ?>
                                        <div>
                                            <div class="flex justify-between text-xs font-bold mb-1.5">
                                                <span class="text-slate-700"><?php echo htmlspecialchars($srv_name); ?></span>
                                                <span class="text-blue-600"><?php echo $srv_cnt; ?> Leads (<?php echo round($pct, 1); ?>%)</span>
                                            </div>
                                            <div class="w-full bg-slate-100 h-3.5 rounded-full overflow-hidden p-0.5">
                                                <div class="bg-blue-600 h-full rounded-full transition-all duration-500" style="width: <?php echo $pct; ?>%"></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                            <h3 class="text-base font-extrabold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center justify-between">
                                <span>🎯 Disposition Status Breakdown</span>
                                <span class="text-xs bg-slate-100 text-slate-600 px-2.5 py-1 rounded-xl font-bold">Statuses</span>
                            </h3>
                            <div class="space-y-4 pt-2">
                                <?php if(empty($disp_counts)): ?>
                                    <p class="text-slate-400 text-center py-6 text-sm font-medium">No disposition data available yet.</p>
                                <?php else: ?>
                                    <?php foreach($disp_counts as $dsp_name => $dsp_cnt): 
                                        $pct = $total_leads > 0 ? ($dsp_cnt / $total_leads) * 100 : 0;
                                    ?>
                                        <div>
                                            <div class="flex justify-between text-xs font-bold mb-1.5">
                                                <span class="text-slate-700"><?php echo htmlspecialchars($dsp_name); ?></span>
                                                <span class="text-purple-600"><?php echo $dsp_cnt; ?> Leads (<?php echo round($pct, 1); ?>%)</span>
                                            </div>
                                            <div class="w-full bg-slate-100 h-3.5 rounded-full overflow-hidden p-0.5">
                                                <div class="bg-purple-600 h-full rounded-full transition-all duration-500" style="width: <?php echo $pct; ?>%"></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </main>
    </div>

    <!-- Callback Pop-up Alert Modal -->
    <div id="callbackModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white p-8 rounded-3xl shadow-2xl max-w-sm w-full text-center border border-slate-100">
            <div class="text-red-500 text-5xl mb-3 animate-bounce">⏰</div>
            <h3 class="text-xl font-extrabold text-slate-900 mb-1">Callback Reminder!</h3>
            <p id="modalMessage" class="text-slate-600 mb-6 text-sm font-medium">It's time to call the client.</p>
            <button onclick="closeModal()" class="bg-red-600 hover:bg-red-700 text-white py-3 px-4 rounded-2xl text-sm font-extrabold w-full transition shadow-lg shadow-red-600/30">OK, Got It</button>
        </div>
    </div>

    <script>
        const leadsData = <?php echo json_encode($leads_array); ?>;

        function checkCallbacks() {
            const now = new Date();
            leadsData.forEach(lead => {
                const callbackTime = new Date(lead.callback_time);
                if (
                    now.getFullYear() === callbackTime.getFullYear() &&
                    now.getMonth() === callbackTime.getMonth() &&
                    now.getDate() === callbackTime.getDate() &&
                    now.getHours() === callbackTime.getHours() &&
                    now.getMinutes() === callbackTime.getMinutes()
                ) {
                    showModal(lead.client_name, lead.phone, lead.notes);
                }
            });
        }

        function showModal(name, phone, notes) {
            const modal = document.getElementById('callbackModal');
            document.getElementById('modalMessage').innerHTML = `Time to call <b>${name}</b> (${phone})!<br><span class="text-xs text-slate-400 mt-1 block">Note: ${notes}</span>`;
            modal.classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('callbackModal').classList.add('hidden');
        }

        setInterval(checkCallbacks, 30000);

        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();
        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

        function renderCalendar() {
            const monthYearEl = document.getElementById('calendarMonthYear');
            const gridEl = document.getElementById('calendarDaysGrid');
            if (!monthYearEl || !gridEl) return;

            monthYearEl.innerText = `${monthNames[currentMonth]} ${currentYear}`;
            gridEl.innerHTML = '';

            printFirstDays();
        }

        function printFirstDays() {
            const gridEl = document.getElementById('calendarDaysGrid');
            const firstDayIndex = new Date(currentYear, currentMonth, 1).getDay();
            const totalDays = new Date(currentYear, currentMonth + 1, 0).getDate();

            for (let i = 0; i < firstDayIndex; i++) {
                gridEl.appendChild(Object.assign(document.createElement('div'), { className: 'bg-slate-50/50 rounded-2xl h-32 border border-slate-200/50 opacity-40' }));
            }

            for (let day = 1; day <= totalDays; day++) {
                const dayBox = document.createElement('div');
                dayBox.className = 'bg-white rounded-2xl h-32 border border-slate-200 p-3 flex flex-col justify-between overflow-y-auto shadow-xs hover:border-blue-400 transition';
                
                const today = new Date();
                let dayHeaderClass = (day === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear()) ? 'bg-blue-600 text-white px-2 py-0.5 rounded-xl text-xs font-extrabold shadow-sm' : 'text-slate-800 font-extrabold text-xs';
                dayBox.innerHTML = `<div class="flex justify-between items-center"><span class="${dayHeaderClass}">${day}</span></div>`;

                const leadsContainer = document.createElement('div');
                leadsContainer.className = 'space-y-1 mt-1 overflow-y-auto flex-1';

                leadsData.forEach(lead => {
                    const leadDate = new Date(lead.callback_time);
                    if (leadDate.getDate() === day && leadDate.getMonth() === currentMonth && leadDate.getFullYear() === currentYear) {
                        const timeStr = leadDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                        const badge = document.createElement('div');
                        badge.className = 'bg-blue-50 border border-blue-200 text-blue-700 px-2 py-1 rounded-xl text-[10px] font-bold truncate shadow-2xs';
                        badge.innerHTML = `⏰ ${timeStr} - <b>${lead.client_name}</b>`;
                        leadsContainer.appendChild(badge);
                    }
                });

                dayBox.appendChild(leadsContainer);
                gridEl.appendChild(dayBox);
            }
        }

        function changeMonth(direction) {
            currentMonth += direction;
            if (currentMonth > 11) { currentMonth = 0; currentYear++; }
            else if (currentMonth < 0) { currentMonth = 11; currentYear--; }
            renderCalendar();
        }

        window.onload = function() { renderCalendar(); };
    </script>
</body>
</html>